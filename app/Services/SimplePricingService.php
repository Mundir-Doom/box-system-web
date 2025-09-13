<?php

namespace App\Services;

use App\Models\Backend\HubTransferCharge;
use Illuminate\Support\Facades\Validator;

class SimplePricingService
{
    public function validatePricingRequest(array $data)
    {
        $rules = [
            'from_hub_id' => 'required|exists:hubs,id',
            'to_hub_id' => 'required|exists:hubs,id|different:from_hub_id',
            'weight' => 'nullable|numeric|min:0',
        ];

        $validator = Validator::make($data, $rules);

        return [
            'valid' => !$validator->fails(),
            'errors' => $validator->errors()->all()
        ];
    }

    public function calculateDeliveryCharge(array $requestData)
    {
        $fromHubId = $requestData['from_hub_id'];
        $toHubId = $requestData['to_hub_id'];
        $weight = $requestData['weight'] ?? 0;

        // Get hub-to-hub pricing
        $pricing = HubTransferCharge::where([
            'from_hub_id' => $fromHubId,
            'to_hub_id' => $toHubId
        ])->first();

        if (!$pricing) {
            return [
                'delivery_charge' => 0.00,
                'weight_charge' => 0.00,
                'total_charge' => 0.00,
                'weight' => $weight,
                'pricing_method' => 'hub_to_hub',
                'message' => 'No pricing found for this hub combination'
            ];
        }

        // Calculate base charge
        $baseCharge = $pricing->base_charge;

        // Calculate weight charge (optional)
        $weightCharge = 0;
        if ($weight > 0 && $pricing->per_km_rate > 0) {
            $weightCharge = $weight * $pricing->per_km_rate; // Using per_km_rate field for per_kg_rate
        }

        // Calculate total
        $totalCharge = $baseCharge + $weightCharge;

        // Apply min/max limits
        if ($pricing->min_charge > 0) {
            $totalCharge = max($totalCharge, $pricing->min_charge);
        }
        if ($pricing->max_charge > 0) {
            $totalCharge = min($totalCharge, $pricing->max_charge);
        }

        return [
            'delivery_charge' => round($baseCharge, 2),
            'weight_charge' => round($weightCharge, 2),
            'total_charge' => round($totalCharge, 2),
            'weight' => $weight,
            'pricing_method' => 'hub_to_hub',
            'from_hub' => $pricing->fromHub->name ?? 'Unknown',
            'to_hub' => $pricing->toHub->name ?? 'Unknown',
            'base_rate' => $pricing->base_charge,
            'per_kg_rate' => $pricing->per_km_rate, // Using per_km_rate field for per_kg_rate
            'min_charge' => $pricing->min_charge,
            'max_charge' => $pricing->max_charge
        ];
    }

    public function getPricingBreakdown(array $requestData)
    {
        $result = $this->calculateDeliveryCharge($requestData);
        
        return [
            'from_hub' => $result['from_hub'],
            'to_hub' => $result['to_hub'],
            'weight' => $result['weight'],
            'base_rate' => $result['base_rate'],
            'per_kg_rate' => $result['per_kg_rate'],
            'weight_charge_calculation' => $result['weight'] > 0 ? 
                sprintf("%.2f kg × %.2f LYD/kg = %.2f LYD", 
                    $result['weight'], 
                    $result['per_kg_rate'], 
                    $result['weight_charge']) : 
                "No weight charge (weight not specified)",
            'total_calculation' => sprintf(
                "%.2f LYD (base) + %.2f LYD (weight) = %.2f LYD (total)",
                $result['delivery_charge'],
                $result['weight_charge'],
                $result['total_charge']
            )
        ];
    }
}
