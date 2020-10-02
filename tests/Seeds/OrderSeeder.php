<?php


namespace AsemAlalami\LaravelAdvancedFilter\Test\Seeds;


use AsemAlalami\LaravelAdvancedFilter\Test\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {
        Order::create([
            'store_id' => 1,
            'reference' => 'LAF_0001',
            'order_date' => '2020-10-2',
            'subtotal' => 15.5,
            'shipping_cost' => 1
        ])->orderLines()->create(['product_id' => 1, 'price' => 15, 'quantity' => 1]);


        Order::create([
            'store_id' => 1,
            'reference' => 'LAF_0002',
            'order_date' => '2020-10-1',
            'ship_date' => '2020-10-3 10:30:00',
            'subtotal' => 20,
            'shipping_cost' => 0
        ])->orderLines()->createMany([
            ['product_id' => 2, 'price' => 5, 'quantity' => 2],
            ['product_id' => 3, 'price' => 10, 'quantity' => 1]
        ]);


        Order::create([
            'store_id' => 2,
            'reference' => 'LAF_0003',
            'order_date' => '2020-10-2',
            'subtotal' => 25,
            'shipping_cost' => 1
        ])->orderLines()->createMany([
            ['product_id' => 1, 'price' => 15, 'quantity' => 1],
            ['product_id' => 3, 'price' => 10, 'quantity' => 1]
        ]);


        Order::create([
            'store_id' => 3,
            'reference' => 'LAF_0004',
            'order_date' => '2020-09-25',
            'ship_date' => '2020-09-30 5:25:04',
            'subtotal' => 5.7,
            'shipping_cost' => 1.5
        ])->orderLines()->create(['product_id' => 5, 'price' => 5.7, 'quantity' => 1]);


        Order::create([
            'store_id' => 5,
            'reference' => 'LAF_0005',
            'order_date' => '2020-09-26',
            'subtotal' => 8.6,
            'shipping_cost' => 3
        ])->orderLines()->create(['product_id' => 6, 'price' => 4.3, 'quantity' => 2]);
    }
}
