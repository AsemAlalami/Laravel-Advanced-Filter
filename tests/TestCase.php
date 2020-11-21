<?php

namespace AsemAlalami\LaravelAdvancedFilter\Test;

use AsemAlalami\LaravelAdvancedFilter\AdvancedFilterServiceProvider;
use AsemAlalami\LaravelAdvancedFilter\Test\Seeds\DatabaseSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\Config;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (!RefreshDatabaseState::$migrated || env('DB_CONNECTION') == 'sqlite') {
            $this->artisan('migrate:fresh');

            $this->setupDatabase($this->app['db']->connection()->getSchemaBuilder());

            $this->seed(DatabaseSeeder::class);

            RefreshDatabaseState::$migrated = true;
        }
    }

    protected function getPackageProviders($app)
    {
        return [
            AdvancedFilterServiceProvider::class,
//            MongodbServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // setup mongodb config
        if (env('DB_CONNECTION') == 'mongodb') {
            Config::set('database.connections.mongodb', [
                'driver' => 'mongodb',
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', 27017),
                'database' => env('DB_DATABASE', 'test'),
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
                'options' => [
                    'database' => 'admin' // sets the authentication database required by mongo 3
                ]
            ]);
        }
    }

    private function setupDatabase(Builder $schema)
    {
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
            $table->dateTime('ship_date')->nullable();
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
