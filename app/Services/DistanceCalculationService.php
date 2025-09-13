<?php

namespace App\Services;

use App\Models\Backend\Hub;

class DistanceCalculationService
{
    /**
     * Earth's radius in kilometers
     */
    const EARTH_RADIUS_KM = 6371;

    /**
     * Calculate distance between two coordinates using Haversine formula
     *
     * @param float $lat1 Latitude of first point
     * @param float $lon1 Longitude of first point
     * @param float $lat2 Latitude of second point
     * @param float $lon2 Longitude of second point
     * @return float Distance in kilometers
     */
    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        // Validate coordinates
        if (!$this->isValidCoordinate($lat1, $lon1) || !$this->isValidCoordinate($lat2, $lon2)) {
            return 0;
        }

        // Convert degrees to radians
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        // Calculate differences
        $dLat = $lat2Rad - $lat1Rad;
        $dLon = $lon2Rad - $lon1Rad;

        // Haversine formula
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round(self::EARTH_RADIUS_KM * $c, 2);
    }

    /**
     * Calculate distance between two hubs
     *
     * @param int $fromHubId Source hub ID
     * @param int $toHubId Destination hub ID
     * @return float Distance in kilometers
     */
    public function calculateHubToHubDistance($fromHubId, $toHubId)
    {
        $fromHub = Hub::find($fromHubId);
        $toHub = Hub::find($toHubId);

        if (!$fromHub || !$toHub) {
            return 0;
        }

        if (empty($fromHub->hub_lat) || empty($fromHub->hub_long) ||
            empty($toHub->hub_lat) || empty($toHub->hub_long)) {
            return 0;
        }

        return $this->calculateDistance(
            (float) $fromHub->hub_lat,
            (float) $fromHub->hub_long,
            (float) $toHub->hub_lat,
            (float) $toHub->hub_long
        );
    }

    /**
     * Calculate distance from pickup to customer location
     *
     * @param string $pickupLat Pickup latitude
     * @param string $pickupLong Pickup longitude
     * @param string $customerLat Customer latitude
     * @param string $customerLong Customer longitude
     * @return float Distance in kilometers
     */
    public function calculatePickupToCustomerDistance($pickupLat, $pickupLong, $customerLat, $customerLong)
    {
        if (empty($pickupLat) || empty($pickupLong) || empty($customerLat) || empty($customerLong)) {
            return 0;
        }

        return $this->calculateDistance(
            (float) $pickupLat,
            (float) $pickupLong,
            (float) $customerLat,
            (float) $customerLong
        );
    }

    /**
     * Validate if coordinates are valid
     *
     * @param float $lat Latitude
     * @param float $lon Longitude
     * @return bool
     */
    private function isValidCoordinate($lat, $lon)
    {
        return is_numeric($lat) && is_numeric($lon) &&
               $lat >= -90 && $lat <= 90 &&
               $lon >= -180 && $lon <= 180;
    }

    /**
     * Get distance category based on distance
     *
     * @param float $distanceKm Distance in kilometers
     * @return string Category name
     */
    public function getDistanceCategory($distanceKm)
    {
        if ($distanceKm <= 5) {
            return 'local';
        } elseif ($distanceKm <= 20) {
            return 'nearby';
        } elseif ($distanceKm <= 50) {
            return 'regional';
        } else {
            return 'long_distance';
        }
    }

    /**
     * Calculate estimated delivery time based on distance
     *
     * @param float $distanceKm Distance in kilometers
     * @param string $deliveryType Delivery type (same_day, next_day, etc.)
     * @return int Estimated hours
     */
    public function calculateEstimatedDeliveryTime($distanceKm, $deliveryType = 'standard')
    {
        $baseHours = 2; // Base delivery time
        $kmPerHour = 30; // Average speed in km/h for delivery

        $travelTime = $distanceKm / $kmPerHour;
        $totalTime = $baseHours + $travelTime;

        // Adjust based on delivery type
        switch ($deliveryType) {
            case 'same_day':
                return max(1, round($totalTime));
            case 'next_day':
                return max(24, round($totalTime));
            default:
                return max(4, round($totalTime));
        }
    }
}
