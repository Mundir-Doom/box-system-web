<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\DistanceCalculationService;

class DistanceCalculationServiceTest extends TestCase
{
    protected $distanceService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->distanceService = new DistanceCalculationService();
    }

    /** @test */
    public function it_calculates_distance_between_two_coordinates()
    {
        // Test distance between Dhaka (23.8103, 90.4125) and Chittagong (22.3569, 91.7832)
        $distance = $this->distanceService->calculateDistance(23.8103, 90.4125, 22.3569, 91.7832);
        
        // Expected distance is approximately 214 km (actual calculation result)
        $this->assertGreaterThan(210, $distance);
        $this->assertLessThan(220, $distance);
    }

    /** @test */
    public function it_returns_zero_for_invalid_coordinates()
    {
        $distance = $this->distanceService->calculateDistance(999, 999, 23.8103, 90.4125);
        $this->assertEquals(0, $distance);
    }

    /** @test */
    public function it_calculates_same_location_distance_as_zero()
    {
        $distance = $this->distanceService->calculateDistance(23.8103, 90.4125, 23.8103, 90.4125);
        $this->assertEquals(0, $distance);
    }

    /** @test */
    public function it_returns_correct_distance_category()
    {
        $this->assertEquals('local', $this->distanceService->getDistanceCategory(3));
        $this->assertEquals('nearby', $this->distanceService->getDistanceCategory(15));
        $this->assertEquals('regional', $this->distanceService->getDistanceCategory(35));
        $this->assertEquals('long_distance', $this->distanceService->getDistanceCategory(100));
    }

    /** @test */
    public function it_calculates_estimated_delivery_time()
    {
        $time = $this->distanceService->calculateEstimatedDeliveryTime(30, 'same_day');
        $this->assertGreaterThan(0, $time);
        $this->assertLessThan(24, $time);
    }
}
