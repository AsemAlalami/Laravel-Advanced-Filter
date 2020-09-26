<?php

namespace AsemAlalami\LaravelAdvancedFilter\Test\Models;

use AsemAlalami\LaravelAdvancedFilter\HasFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 *
 * @property int $id
 * @property int $store_id
 * @property string $reference
 * @property Carbon $order_date
 * @property float $subtotal
 * @property float $shipping_cost
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Order extends Model
{
    use HasFilter;

    protected $fillable = ['store_id', 'reference', 'order_date', 'subtotal', 'shipping_cost'];
    protected $casts = ['order_date' => 'datetime'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function orderLines()
    {
        return $this->hasMany(OrderLine::class);
    }

    public function setupFilter()
    {
        $this->addFields(['reference' => 'order_number', 'order_date', 'created_at'])->setDatatype('datetime');
        $this->addFields(['subtotal', 'shipping_cost' => 'shipping'])->setDatatype('numeric');

        $this->addFields(['store_id', 'store.name' => 'store_name']);

        $this->addCountField('orderLines', 'lines_count');
        $this->addCustomField('line_subtotal', '(`price` * `quantity`)', 'orderLines');

        $this->addField('orderLines.product_id', 'product_id');
        $this->addField('orderLines.product.name', 'product_name');
    }
}
