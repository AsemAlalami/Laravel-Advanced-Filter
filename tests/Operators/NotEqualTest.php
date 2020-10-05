<?php

namespace AsemAlalami\LaravelAdvancedFilter\Test\Operators;

use AsemAlalami\LaravelAdvancedFilter\Test\Models\Order;
use AsemAlalami\LaravelAdvancedFilter\Test\TestCase;
use Illuminate\Http\Request;

class NotEqualTest extends TestCase
{
    /** @test */
    public function it_can_filter_string_fields()
    {
        $reference = 'LAF_0005';
        $queryFilters = 'filters=[{"field":"order_number","operator":"!=","value":"' . $reference . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(4, $orders);

        $this->assertNotContainsEquals('LAF_0005', $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_filter_numeric_fields()
    {
        $subtotal = 25;
        $queryFilters = 'filters=[{"field":"subtotal","operator":"!=","value":"' . $subtotal . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(4, $orders);

        $this->assertNotContains('LAF_0003', $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_filter_date_fields()
    {
        $orderDate = '2020-10-2';
        $queryFilters = 'filters=[{"field":"order_date","operator":"!=","value":"' . $orderDate . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(3, $orders);

        $this->assertNotContains(['LAF_0001', 'LAF_0003'], $orders->pluck('reference')->toArray());
    }


    /** @test */
    public function it_can_filter_datetime_fields()
    {
        $shipDate = '2020-10-3 10:30:00';
        $queryFilters = 'filters=[{"field":"ship_date","operator":"!=","value":"' . $shipDate . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(1, $orders);

        $this->assertNotContains('LAF_0002', $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_filter_custom_fields()
    {
        $lineSubtotal = 8.6;
        $queryFilters = 'filters=[{"field":"line_subtotal","operator":"!=","value":"' . $lineSubtotal . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(4, $orders);

        $this->assertNotContains('LAF_0005', $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_filter_count_fields()
    {
        $linesCount = 2;
        $queryFilters = 'filters=[{"field":"lines_count","operator":"!=","value":"' . $linesCount . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(3, $orders);

        $this->assertNotContains(['LAF_0002', 'LAF_0003'], $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_filter_relation_fields()
    {
        $storeName = 'Sociis Corporation';
        $queryFilters = 'filters=[{"field":"store_name","operator":"!=","value":"' . $storeName . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(4, $orders);

        $this->assertNotContains('LAF_0005', $orders->pluck('reference')->toArray());
    }
}
