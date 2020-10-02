<?php


namespace AsemAlalami\LaravelAdvancedFilter\Test\Seeds;


use AsemAlalami\LaravelAdvancedFilter\Test\Models\Order;
use AsemAlalami\LaravelAdvancedFilter\Test\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run()
    {
        Store::create(['name' => 'Placerat Consulting']);
        Store::create(['name' => 'Hendrerit A Arcu Ltd']);
        Store::create(['name' => 'Proin Eget Odio Consulting']);
        Store::create(['name' => 'Orci In Consequat Associates']);
        Store::create(['name' => 'Sociis Corporation']);
    }
}
