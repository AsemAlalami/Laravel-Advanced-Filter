<?php

namespace AsemAlalami\LaravelAdvancedFilter\Test\Operators;

use AsemAlalami\LaravelAdvancedFilter\Exceptions\UnsupportedOperatorException;
use AsemAlalami\LaravelAdvancedFilter\Test\Models\Order;
use AsemAlalami\LaravelAdvancedFilter\Test\TestCase;
use Illuminate\Http\Request;

class ContainsTest extends TestCase
{
    /** @test */
    public function it_can_filter_string_fields()
    {
        $reference = '005';
        $queryFilters = 'filters=[{"field":"order_number","operator":"*","value":"' . $reference . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(1, $orders);

        $this->assertEquals(['LAF_0005'], $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_filter_numeric_fields()
    {
        $subtotal = 5;
        $queryFilters = 'filters=[{"field":"subtotal","operator":"*","value":"' . $subtotal . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(3, $orders);

        $this->assertEquals(['LAF_0001', 'LAF_0003', 'LAF_0004'], $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_filter_custom_fields()
    {
        $storeReference = 'Consulting';
        $queryFilters = 'filters=[{"field":"store_reference","operator":"*","value":"' . $storeReference . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(3, $orders);

        $this->assertEquals(['LAF_0001', 'LAF_0002', 'LAF_0004'], $orders->pluck('reference')->toArray());
    }

    /** @test */
    public function it_can_not_filter_count_fields()
    {
        $linesCount = 2;
        $queryFilters = 'filters=[{"field":"lines_count","operator":"*","value":"' . $linesCount . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $this->expectException(UnsupportedOperatorException::class);

        Order::filter($request)->get();
    }

    /** @test */
    public function it_can_filter_relation_fields()
    {
        $productName = 'Nam';
        $queryFilters = 'filters=[{"field":"product_name","operator":"*","value":"' . $productName . '"}]';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(3, $orders);

        $this->assertEquals(['LAF_0001', 'LAF_0003', 'LAF_0005'], $orders->pluck('reference')->toArray());
    }
}
