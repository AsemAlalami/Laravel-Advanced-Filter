<?php

namespace AsemAlalami\LaravelAdvancedFilter\Test\Operators;

use AsemAlalami\LaravelAdvancedFilter\Test\Models\Order;
use AsemAlalami\LaravelAdvancedFilter\Test\TestCase;
use Illuminate\Http\Request;

class EqualTest extends TestCase
{
    /** @test */
    public function it_can_filter_string_fields()
    {
        $reference = 'LAF_0005';
        $queryFilters = 'filters=[{"field":"order_number","operator":"equal","value":"' . $reference . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $order = Order::filter($request)->first();

        $this->assertNotNull($order);

        $this->assertEquals($reference, $order->reference);
    }

    /** @test */
    public function it_can_filter_numeric_fields()
    {
        $subtotal = 25;
        $queryFilters = 'filters=[{"field":"subtotal","operator":"equal","value":"' . $subtotal . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $order = Order::filter($request)->first();

        $this->assertNotNull($order);

        $this->assertEquals('LAF_0003', $order->reference);
    }

    /** @test */
    public function it_can_filter_date_fields()
    {
        $orderDate = '2020-10-2';
        $queryFilters = 'filters=[{"field":"order_date","operator":"equal","value":"' . $orderDate . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(2, $orders);

        $this->assertEquals(['LAF_0001', 'LAF_0003'], $orders->pluck('reference')->toArray());
    }


    /** @test */
    public function it_can_filter_datetime_fields()
    {
        $shipDate = '2020-10-3 10:30:00';
        $queryFilters = 'filters=[{"field":"ship_date","operator":"equal","value":"' . $shipDate . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(1, $orders);

        $this->assertEquals(['LAF_0002'], $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_filter_custom_fields()
    {
        $lineSubtotal = 8.6;
        $queryFilters = 'filters=[{"field":"line_subtotal","operator":"equal","value":"' . $lineSubtotal . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(1, $orders);

        $this->assertEquals(['LAF_0005'], $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_filter_count_fields()
    {
        $linesCount = 2;
        $queryFilters = 'filters=[{"field":"lines_count","operator":"equal","value":"' . $linesCount . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(2, $orders);

        $this->assertEquals(['LAF_0002', 'LAF_0003'], $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_filter_relation_fields()
    {
        $storeName = 'Sociis Corporation';
        $queryFilters = 'filters=[{"field":"store_name","operator":"equal","value":"' . $storeName . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(1, $orders);

        $this->assertEquals(['LAF_0005'], $orders->pluck('reference')->toArray());
    }
}
