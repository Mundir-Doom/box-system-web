<?php

namespace App\Services;

use App\Models\Backend\HubTransferCharge;
use App\Models\Backend\DeliveryCharge;
use App\Models\Backend\MerchantDeliveryCharge;

class DynamicPricingService
{
    protected $distanceService;

    public function __construct(DistanceCalculationService $distanceService)
    {
        $this->distanceService = $distanceService;
    }

    /**
     * Calculate delivery charge based on pricing method
     *
     * @param array $request Request data
     * @return array Pricing details
     */
    public function calculateDeliveryCharge($request)
    {
        $pricingMethod = $request['pricing_method'] ?? 'fixed';

        if ($pricingMethod === 'distance') {
            return $this->calculateDistanceBasedCharge($request);
        }

        return $this->calculateFixedCharge($request);
    }

    /**
     * Calculate distance-based delivery charge
     *
     * @param array $request Request data
     * @return array Pricing details
     */
    private function calculateDistanceBasedCharge($request)
    {
        // Calculate pickup to customer distance
        $pickupToCustomerDistance = $this->distanceService->calculatePickupToCustomerDistance(
            $request['pickup_lat'] ?? null,
            $request['pickup_long'] ?? null,
            $request['customer_lat'] ?? null,
            $request['customer_long'] ?? null
        );

        // Calculate hub transfer charge if applicable
        $transferCharge = 0;
        if (!empty($request['transfer_hub_id']) && !empty($request['hub_id'])) {
            $transferCharge = $this->calculateHubTransferCharge(
                $request['hub_id'],
                $request['transfer_hub_id'],
                $request['weight'] ?? 1
            );
        }

        // Get base rate from configuration
        $baseRate = config('rxcourier.distance_pricing.base_rate_per_km', 5.00);
        
        // Apply weight multiplier
        $weightMultiplier = $this->getWeightMultiplier($request['weight'] ?? 1);
        
        // Calculate distance charge
        $distanceCharge = $pickupToCustomerDistance * $baseRate * $weightMultiplier;
        
        // Apply minimum charge
        $minCharge = config('rxcourier.distance_pricing.min_charge', 20.00);
        $distanceCharge = max($distanceCharge, $minCharge);

        return [
            'delivery_charge' => round($distanceCharge, 2),
            'transfer_charge' => round($transferCharge, 2),
            'total_charge' => round($distanceCharge + $transferCharge, 2),
            'distance_km' => $pickupToCustomerDistance,
            'pricing_method' => 'distance',
            'base_rate_per_km' => $baseRate,
            'weight_multiplier' => $weightMultiplier
        ];
    }

    /**
     * Calculate fixed delivery charge (existing logic)
     *
     * @param array $request Request data
     * @return array Pricing details
     */
    private function calculateFixedCharge($request)
    {
        $merchantId = $request['merchant_id'] ?? null;
        $categoryId = $request['category_id'] ?? null;
        $weight = $request['weight'] ?? 1;
        $deliveryTypeId = $request['delivery_type_id'] ?? 1;

        // Try merchant-specific charges first
        $charges = null;
        if ($merchantId && $categoryId && $weight != '0' && $deliveryTypeId) {
            $charges = MerchantDeliveryCharge::where([
                'merchant_id' => $merchantId,
                'category_id' => $categoryId,
                'weight' => $weight
            ])->first();
        }

        // Fallback to general delivery charges
        if (!$charges) {
            $charges = MerchantDeliveryCharge::where([
                'merchant_id' => $merchantId,
                'category_id' => $categoryId,
                'weight' => $weight
            ])->first();
            
            if (!$charges) {
                $charges = DeliveryCharge::where(['category_id' => $categoryId])->first();
            }
        }

        $chargeAmount = 0;
        if ($charges) {
            switch ($deliveryTypeId) {
                case '1':
                    $chargeAmount = $charges->same_day;
                    break;
                case '2':
                    $chargeAmount = $charges->next_day;
                    break;
                case '3':
                    $chargeAmount = $charges->sub_city;
                    break;
                case '4':
                    $chargeAmount = $charges->outside_city;
                    break;
            }
        }

        return [
            'delivery_charge' => round($chargeAmount, 2),
            'transfer_charge' => 0,
            'total_charge' => round($chargeAmount, 2),
            'distance_km' => 0,
            'pricing_method' => 'fixed'
        ];
    }

    /**
     * Calculate hub transfer charge
     *
     * @param int $fromHubId Source hub ID
     * @param int $toHubId Destination hub ID
     * @param int $weight Package weight
     * @return float Transfer charge
     */
    private function calculateHubTransferCharge($fromHubId, $toHubId, $weight = 1)
    {
        $transferCharge = HubTransferCharge::where([
            'from_hub_id' => $fromHubId,
            'to_hub_id' => $toHubId,
            'status' => 1 // Active
        ])->first();

        if (!$transferCharge) {
            return 0;
        }

        $distance = $this->distanceService->calculateHubToHubDistance($fromHubId, $toHubId);
        
        return $transferCharge->calculateCharge($distance, $weight);
    }

    /**
     * Get weight multiplier for pricing
     *
     * @param int $weight Package weight in kg
     * @return float Weight multiplier
     */
    private function getWeightMultiplier($weight)
    {
        $weightMultipliers = config('rxcourier.distance_pricing.weight_multipliers', [
            1 => 1.0,
            5 => 1.2,
            10 => 1.5,
            'default' => 2.0
        ]);

        if ($weight <= 1) {
            return $weightMultipliers[1] ?? 1.0;
        } elseif ($weight <= 5) {
            return $weightMultipliers[5] ?? 1.2;
        } elseif ($weight <= 10) {
            return $weightMultipliers[10] ?? 1.5;
        }

        return $weightMultipliers['default'] ?? 2.0;
    }

    /**
     * Get pricing breakdown for display
     *
     * @param array $request Request data
     * @return array Detailed pricing breakdown
     */
    public function getPricingBreakdown($request)
    {
        $pricing = $this->calculateDeliveryCharge($request);
        
        $breakdown = [
            'base_charge' => $pricing['delivery_charge'],
            'transfer_charge' => $pricing['transfer_charge'],
            'total_charge' => $pricing['total_charge'],
            'distance_km' => $pricing['distance_km'],
            'pricing_method' => $pricing['pricing_method']
        ];

        if ($pricing['pricing_method'] === 'distance') {
            $breakdown['base_rate_per_km'] = $pricing['base_rate_per_km'];
            $breakdown['weight_multiplier'] = $pricing['weight_multiplier'];
            $breakdown['distance_charge'] = $pricing['distance_km'] * $pricing['base_rate_per_km'] * $pricing['weight_multiplier'];
        }

        return $breakdown;
    }

    /**
     * Validate pricing request
     *
     * @param array $request Request data
     * @return array Validation result
     */
    public function validatePricingRequest($request)
    {
        $errors = [];

        if (($request['pricing_method'] ?? 'fixed') === 'distance') {
            if (empty($request['pickup_lat']) || empty($request['pickup_long'])) {
                $errors[] = 'Pickup coordinates are required for distance-based pricing';
            }
            
            if (empty($request['customer_lat']) || empty($request['customer_long'])) {
                $errors[] = 'Customer coordinates are required for distance-based pricing';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
