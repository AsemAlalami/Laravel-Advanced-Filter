<?php


namespace AsemAlalami\LaravelAdvancedFilter\Test\Seeds;


use AsemAlalami\LaravelAdvancedFilter\Test\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::create(['name' => 'Nam porttitor scelerisque neque. Nullam', 'sku' => '2971-KW']);
        Product::create(['name' => 'tellus id nunc interdum feugiat.', 'sku' => 'M0E-8H3']);
        Product::create(['name' => 'magna tellus faucibus leo, in', 'sku' => '64759-40420']);
        Product::create(['name' => 'eu neque pellentesque massa lobortis', 'sku' => 'L5X-7J3']);
        Product::create(['name' => 'arcu vel quam dignissim pharetra.', 'sku' => 'KU5-8ZD']);
        Product::create(['name' => 'magna. Nam ligula elit, pretium', 'sku' => 'LT5W-9CV']);
        Product::create(['name' => 'aliquam eros turpis non enim.', 'sku' => '943262']);
        Product::create(['name' => 'pharetra nibh. Aliquam ornare, libero', 'sku' => '50336']);
        Product::create(['name' => 'purus, accumsan interdum libero dui', 'sku' => '7889-LY']);
        Product::create(['name' => 'sit amet ultricies sem magna', 'sku' => 'K6V-4G6']);
    }
}
