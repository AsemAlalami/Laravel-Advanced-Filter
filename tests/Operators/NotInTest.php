<?php

namespace AsemAlalami\LaravelAdvancedFilter\Test\Operators;

use AsemAlalami\LaravelAdvancedFilter\Exceptions\UnsupportedOperatorException;
use AsemAlalami\LaravelAdvancedFilter\Test\Models\Order;
use AsemAlalami\LaravelAdvancedFilter\Test\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotInTest extends TestCase
{
    /** @test */
    public function it_can_filter_string_fields()
    {
        $reference = '["LAF_0005","LAF_0001"]';
        $queryFilters = 'filters=[{"field":"order_number","operator":"!|","value":' . $reference . '}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(3, $orders);

        $this->assertEquals(['LAF_0002', 'LAF_0003', 'LAF_0004'], $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_filter_numeric_fields()
    {
        $subtotal = "[25,5.7]";
        $queryFilters = 'filters=[{"field":"subtotal","operator":"!|","value":' . $subtotal . '}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(3, $orders);

        $this->assertEquals(['LAF_0001', 'LAF_0002', 'LAF_0005'], $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_filter_date_fields()
    {
        $orderDate = '["2020-10-02","2020-09-25"]';
        $queryFilters = 'filters=[{"field":"order_date","operator":"!|","value":' . $orderDate . '}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(2, $orders);

        $this->assertEquals(['LAF_0002', 'LAF_0005'], $orders->pluck('reference')->toArray());
    }


    /** @test */
    public function it_can_filter_datetime_fields()
    {
        $shipDate = '["2020-10-03 10:30:00", "2020-09-30 05:25:04"]';
        $queryFilters = 'filters=[{"field":"ship_date","operator":"!|","value":' . $shipDate . '}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();
        // 0 because of NULL-safe
        /** @link https://dev.mysql.com/doc/refman/8.0/en/comparison-operators.html#operator_equal-to */
        $this->assertCount(0, $orders);
    }

    /** @test */
    public function it_can_filter_custom_fields()
    {
        $lineSubtotal = "[8.6,5.7]";
        $queryFilters = 'filters=[{"field":"line_subtotal","operator":"!|","value":' . $lineSubtotal . '}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(3, $orders);

        $this->assertEquals(['LAF_0001', 'LAF_0002', 'LAF_0003'], $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_filter_count_fields()
    {
        $linesCount = "[1,2]";
        $queryFilters = 'filters=[{"field":"lines_count","operator":"!|","value":' . $linesCount . '}]';
        $request = Request::create("test?{$queryFilters}");

        $this->expectException(UnsupportedOperatorException::class);

        Order::filter($request)->get();
    }

    /** @test */
    public function it_can_filter_relation_fields()
    {
        $productSku = '["2971-KW","KU5-8ZD"]';
        $queryFilters = 'filters=[{"field":"product_sku","operator":"!|","value":' . $productSku . '}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(3, $orders);

        $this->assertEquals(['LAF_0002', 'LAF_0003', 'LAF_0005'], $orders->pluck('reference')->toArray());
    }
}
