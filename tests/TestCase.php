<?php

namespace AsemAlalami\LaravelAdvancedFilter\Test;

use AsemAlalami\LaravelAdvancedFilter\AdvancedFilterServiceProvider;
use AsemAlalami\LaravelAdvancedFilter\Test\Seeds\DatabaseSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setupDatabase($this->app['db']->connection()->getSchemaBuilder());

        $this->seed(DatabaseSeeder::class);
    }

    protected function getPackageProviders($app)
    {
        return [
            AdvancedFilterServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }

    private function setupDatabase(Builder $schema)
    {
        $schema->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->timestamps();
        });

        $schema->create('stores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        $schema->create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('sku');
            $table->timestamps();
        });

        $schema->create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('store_id');
            $table->string('reference');
            $table->dateTime('order_date');
            $table->decimal('subtotal');
            $table->decimal('shipping_cost');
            $table->timestamps();
        });

        $schema->create('order_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('product_id');
            $table->float('price');
            $table->integer('quantity');
            $table->timestamps();
        });
    }
}
