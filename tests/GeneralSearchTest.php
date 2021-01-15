<?php


namespace AsemAlalami\LaravelAdvancedFilter\Test;


use AsemAlalami\LaravelAdvancedFilter\Test\Models\Order;
use Illuminate\Http\Request;

class GeneralSearchTest extends TestCase
{
    /** @test */
    public function general_search()
    {
        $queryFilters = 'query=64759';
        $request = Request::create("test?{$queryFilters}");

        $orders = Order::filter($request)->get();

        $this->assertCount(2, $orders);

        $this->assertEquals(['LAF_0002', 'LAF_0003'], $orders->pluck('reference')->toArray());
    }
}
