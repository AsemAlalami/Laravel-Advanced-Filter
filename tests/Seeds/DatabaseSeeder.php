<?php

namespace AsemAlalami\LaravelAdvancedFilter\Test\Seeds;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ProductSeeder::class);
        $this->call(StoreSeeder::class);
        $this->call(OrderSeeder::class);
    }
}
