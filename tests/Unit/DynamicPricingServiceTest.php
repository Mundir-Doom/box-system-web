<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\DynamicPricingService;
use App\Services\DistanceCalculationService;

class DynamicPricingServiceTest extends TestCase
{
    protected $pricingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pricingService = new DynamicPricingService(new DistanceCalculationService());
    }

    /** @test */
    public function it_calculates_distance_based_pricing()
    {
        $request = [
            'pricing_method' => 'distance',
            'pickup_lat' => '23.8103',
            'pickup_long' => '90.4125',
            'customer_lat' => '23.7500',
            'customer_long' => '90.4000',
            'weight' => 2
        ];

        $result = $this->pricingService->calculateDeliveryCharge($request);

        $this->assertArrayHasKey('delivery_charge', $result);
        $this->assertArrayHasKey('distance_km', $result);
        $this->assertArrayHasKey('pricing_method', $result);
        $this->assertEquals('distance', $result['pricing_method']);
        $this->assertGreaterThan(0, $result['delivery_charge']);
    }

    /** @test */
    public function it_validates_pricing_request()
    {
        $request = [
            'pricing_method' => 'distance',
            'pickup_lat' => '23.8103',
            'pickup_long' => '90.4125',
            // Missing customer coordinates
        ];

        $validation = $this->pricingService->validatePricingRequest($request);

        $this->assertFalse($validation['valid']);
        $this->assertNotEmpty($validation['errors']);
    }

    /** @test */
    public function it_returns_pricing_breakdown()
    {
        $request = [
            'pricing_method' => 'distance',
            'pickup_lat' => '23.8103',
            'pickup_long' => '90.4125',
            'customer_lat' => '23.7500',
            'customer_long' => '90.4000',
            'weight' => 1
        ];

        $breakdown = $this->pricingService->getPricingBreakdown($request);

        $this->assertArrayHasKey('base_charge', $breakdown);
        $this->assertArrayHasKey('total_charge', $breakdown);
        $this->assertArrayHasKey('distance_km', $breakdown);
        $this->assertArrayHasKey('pricing_method', $breakdown);
    }

    /** @test */
    public function it_handles_fixed_pricing_method()
    {
        $request = [
            'pricing_method' => 'fixed',
            'merchant_id' => 1,
            'category_id' => 1,
            'weight' => 1,
            'delivery_type_id' => 1
        ];

        $result = $this->pricingService->calculateDeliveryCharge($request);

        $this->assertEquals('fixed', $result['pricing_method']);
        $this->assertEquals(0, $result['distance_km']);
    }
}
