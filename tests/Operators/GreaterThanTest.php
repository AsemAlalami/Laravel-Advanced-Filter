<?php

namespace AsemAlalami\LaravelAdvancedFilter\Test\Operators;

use AsemAlalami\LaravelAdvancedFilter\Test\Models\Order;
use AsemAlalami\LaravelAdvancedFilter\Test\TestCase;
use Illuminate\Http\Request;

class GreaterThanTest extends TestCase
{
    /** @test */
    public function it_can_filter_numeric_fields()
    {
        $subtotal = 15.5;
        $queryFilters = 'filters=[{"field":"subtotal","operator":">","value":"' . $subtotal . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(2, $orders);

        $this->assertEquals(['LAF_0002', 'LAF_0003'], $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_filter_date_fields()
    {
        $orderDate = '2020-10-1';
        $queryFilters = 'filters=[{"field":"order_date","operator":">","value":"' . $orderDate . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(2, $orders);

        $this->assertEquals(['LAF_0001', 'LAF_0003'], $orders->pluck('reference')->toArray());
    }


    /** @test */
    public function it_can_filter_datetime_fields()
    {
        // sqlite does not support datetime datatype, but you can use "strftime", I will not support that
        if (env('DB_CONNECTION') == 'sqlite') {
            return $this->assertTrue(true);
        }

        $shipDate = '2020-09-30 5:25:04';
        $queryFilters = 'filters=[{"field":"ship_date","operator":">","value":"' . $shipDate . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(1, $orders);

        $this->assertEquals(['LAF_0002'], $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_filter_custom_fields()
    {
        $lineSubtotal = 8.6;
        $queryFilters = 'filters=[{"field":"line_subtotal","operator":">","value":"' . $lineSubtotal . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(3, $orders);

        $this->assertEquals(['LAF_0001', 'LAF_0002', 'LAF_0003'], $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_filter_count_fields()
    {
        $linesCount = 1;
        $queryFilters = 'filters=[{"field":"lines_count","operator":">","value":"' . $linesCount . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(2, $orders);

        $this->assertEquals(['LAF_0002', 'LAF_0003'], $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_filter_relation_fields()
    {
        $linePrice = 10;
        $queryFilters = 'filters=[{"field":"line_price","operator":">","value":"' . $linePrice . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(2, $orders);

        $this->assertEquals(['LAF_0001', 'LAF_0003'], $orders->pluck('reference')->toArray());
    }
}
