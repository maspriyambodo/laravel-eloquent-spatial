<?php

namespace MatanYadaev\EloquentSpatial\Tests\Builders;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Tests\TestCase;
use MatanYadaev\EloquentSpatial\Tests\TestModels\TestPlace;

class SpatialBuilderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_calculates_distance_between_column_and_column()
    {
        TestPlace::factory()->create();

        $testPlaceWithDistance = TestPlace::query()
            ->withDistance('point', 'point')
            ->first();
        // @TODO add different column

        $this->assertEquals(0, $testPlaceWithDistance->distance);
    }

    /** @test */
    public function it_calculates_distance_between_column_and_geometry()
    {
        TestPlace::factory()->create([
            'point' => new Point(23.1, 55.5),
        ]);

        $testPlaceWithDistance = TestPlace::query()
            ->withDistance('point', new Point(23.1, 55.6))
            ->first();

        $this->assertEquals(0.1, $testPlaceWithDistance->distance);
    }

    /** @test */
    public function it_calculates_distance_with_defined_name()
    {
        TestPlace::factory()->create([
            'point' => new Point(23.1, 55.5),
        ]);

        $testPlaceWithDistance = TestPlace::query()
            ->withDistance('point', new Point(23.1, 55.6), 'distance_in_meters')
            ->first();

        $this->assertEquals(0.1, $testPlaceWithDistance->distance_in_meters);
    }

    /** @test */
    public function it_filters_by_distance()
    {
        TestPlace::factory()->create([
            'point' => new Point(23.1, 55.5),
        ]);

        $testPlaceWithinDistance = TestPlace::query()
            ->whereDistance('point', new Point(23.1, 55.6), '<', 1)
            ->first();

        $this->assertNotNull($testPlaceWithinDistance);
    }

    /** @test */
    public function it_orders_by_distance()
    {
        $point = new Point(23.1, 55.51);
        $testPlace1 = TestPlace::factory()->create([
            'point' => new Point(23.1, 55.5),
        ]);
        $testPlace2 = TestPlace::factory()->create([
            'point' => new Point(0, 0),
        ]);

        $closestTestPlace = TestPlace::query()
            ->orderByDistance('point', $point)
            ->first();

        $farthestTestPlace = TestPlace::query()
            ->orderByDistance('point', $point, 'desc')
            ->first();

        $this->assertEquals($testPlace1->id, $closestTestPlace->id);
        $this->assertEquals($testPlace2->id, $farthestTestPlace->id);
    }

    /** @test */
    public function it_calculates_distance_sphere_column_and_column()
    {
        TestPlace::factory()->create();

        $testPlaceWithDistance = TestPlace::query()
            ->withDistanceSphere('point', 'point')
            ->first();

        $this->assertEquals(0, $testPlaceWithDistance->distance);
    }

    /** @test */
    public function it_calculates_distance_sphere_column_and_geometry()
    {
        TestPlace::factory()->create([
            'point' => new Point(23.1, 55.5),
        ]);

        $testPlaceWithDistance = TestPlace::query()
            ->withDistanceSphere('point', new Point(23.1, 55.51))
            ->first();

        $this->assertEquals(1022.7925914593363, $testPlaceWithDistance->distance);
    }

    /** @test */
    public function it_calculates_distance_sphere_with_defined_name()
    {
        TestPlace::factory()->create([
            'point' => new Point(23.1, 55.5),
        ]);

        $testPlaceWithDistance = TestPlace::query()
            ->withDistanceSphere('point', new Point(23.1, 55.51), 'distance_in_meters')
            ->first();

        $this->assertEquals(1022.7925914593363, $testPlaceWithDistance->distance_in_meters);
    }

    /** @test */
    public function it_filters_distance_sphere()
    {
        TestPlace::factory()->create([
            'point' => new Point(23.1, 55.5),
        ]);

        $testPlaceWithinDistanceSphere = TestPlace::query()
            ->whereDistanceSphere('point', new Point(23.1, 55.51), '<', 2000)
            ->first();

        $this->assertNotNull($testPlaceWithinDistanceSphere);
    }

    /** @test */
    public function it_orders_by_distance_sphere()
    {
        $point = new Point(23.1, 55.51);
        $testPlace1 = TestPlace::factory()->create([
            'point' => new Point(23.1, 55.5),
        ]);
        $testPlace2 = TestPlace::factory()->create([
            'point' => new Point(0, 0),
        ]);

        $closestTestPlace = TestPlace::query()
            ->orderByDistanceSphere('point', $point)
            ->first();

        $farthestTestPlace = TestPlace::query()
            ->orderByDistanceSphere('point', $point, 'desc')
            ->first();

        $this->assertEquals($testPlace1->id, $closestTestPlace->id);
        $this->assertEquals($testPlace2->id, $farthestTestPlace->id);
    }
}
