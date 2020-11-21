<?php


namespace AsemAlalami\LaravelAdvancedFilter\Test\Models;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as MongoModel;

if (env('DB_CONNECTION') == 'mongodb') {
    abstract class BaseModel extends MongoModel
    {
        protected $connection = 'mongodb';
    }
} else {
    abstract class BaseModel extends Model
    {

    }
}
