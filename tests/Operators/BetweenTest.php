<?php

namespace AsemAlalami\LaravelAdvancedFilter\Test\Operators;

use AsemAlalami\LaravelAdvancedFilter\Exceptions\UnsupportedOperatorException;
use AsemAlalami\LaravelAdvancedFilter\Test\Models\Order;
use AsemAlalami\LaravelAdvancedFilter\Test\TestCase;
use Illuminate\Http\Request;

class BetweenTest extends TestCase
{
    /** @test */
    public function it_can_filter_numeric_fields()
    {
        $subtotal = '{"from":15, "to":25}';
        $queryFilters = 'filters=[{"field":"subtotal","operator":"><","value":' . $subtotal . '}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(3, $orders);

        $this->assertEquals(['LAF_0001', 'LAF_0002', 'LAF_0003'], $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_filter_date_fields()
    {
        $orderDate = '{"from":"2020-09-26", "to":"2020-10-01"}';
        $queryFilters = 'filters=[{"field":"order_date","operator":"><","value":' . $orderDate . '}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(2, $orders);

        $this->assertEquals(['LAF_0002', 'LAF_0005'], $orders->pluck('reference')->toArray());
    }


    /** @test */
    public function it_can_filter_datetime_fields()
    {
        $shipDate = '{"from":"2020-09-30 05:00:00", "to":"2020-10-02 05:00:00"}';
        $queryFilters = 'filters=[{"field":"ship_date","operator":"><","value":' . $shipDate . '}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(1, $orders);

        $this->assertEquals(['LAF_0004'], $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_filter_custom_fields()
    {
        $lineSubtotal = '{"from":5.7, "to":8.6}';
        $queryFilters = 'filters=[{"field":"line_subtotal","operator":"><","value":' . $lineSubtotal . '}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(2, $orders);

        $this->assertEquals(['LAF_0004', 'LAF_0005'], $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_filter_count_fields()
    {
        $linesCount = '{"from":1, "to":2}';
        $queryFilters = 'filters=[{"field":"lines_count","operator":"><","value":' . $linesCount . '}]';
        $request = Request::create("test?{$queryFilters}");

        $this->expectException(UnsupportedOperatorException::class);

        Order::filter($request)->get();
    }

    /** @test */
    public function it_can_filter_relation_fields()
    {
        $productSku = '{"from": 5, "to":10}';
        $queryFilters = 'filters=[{"field":"line_price","operator":"><","value":' . $productSku . '}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(3, $orders);

        $this->assertEquals(['LAF_0002', 'LAF_0003', 'LAF_0004'], $orders->pluck('reference')->toArray());
    }
}
