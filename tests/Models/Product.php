<?php

namespace AsemAlalami\LaravelAdvancedFilter\Test\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 *
 * @property int $id
 * @property string $name
 * @property string $sku
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Product extends Model
{
    protected $fillable = ['name', 'sku'];

    public function orderLines()
    {
        return $this->hasMany(OrderLine::class);
    }
}
