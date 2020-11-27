# Laravel Advanced Filter
This package allows you to filter on laravel models

You can choose fields to filtering and customize its data-types, aliases and excepted operators, 
you can add/customize your request format, and you add new operators or overwrite the existed operators


## Installation  
You can install the package via composer:
```  
composer require asemalalami/laravel-advanced-filter
```  

The package will automatically register its service provider.

You can optionally publish the config file with:
```
php artisan vendor:publish --provider="AsemAlalami\LaravelAdvancedFilter\AdvancedFilterServiceProvider" --tag="config"
```

These default config file that will be published:
 [Config File](https://github.com/AsemAlalami/Laravel-Advanced-Filter/blob/master/config/advanced_filter.php)

## Usage
- use `HasFilter` trait in the model
- add fields in the implementation of the abstract function `setupFilter`
- call the `filter` scope in your controller
```php
class Order extends Model
{
    use HasFilter;

    protected $casts = [
        'void' => 'boolean',
    ];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function orderLines()
    {
        return $this->hasMany(OrderLine::class);
    }

    public function setupFilter()
    {
        $this->addField('void'); // will cast to 'boolean' from the model casts
        $this->addField('total')->setDatatype('numeric');
        $this->addFields(['source', 'subsource', 'order_date']);
        // field from relation
        $this->addFields(['channel.created_at' => 'channel_create'])->setDatatype('date');
        // field from relation count
        $this->addCountField('orderLines');
        // custom field (raw sql)
        $this->addCustomField('my_total', '(shipping_cost + subtotal)');
    }

    // customize field filter by custom scope
    public function scopeWhereSource(Builder $builder, Field $field, string $operator, $value, $conjunction = 'and')
    {
        if ($operator == 'Equal') {
            return $builder->where(function (Builder $builder) use ($value) {
                $builder->where('source', $value)
                    ->orWhere('subsource', $value);
            });
        }
        
        // default behavior
        return $builder->applyOperator($operator, $field, $value, $conjunction);
    }
}

...

class OrderController extends Controller
{
    public function index()
    {
        return Order::filter()->paginate(); // you can pass your custom request
    }
}
```

## Query Format
Query format is the shape that you want to send your query(filters) in the request.
the package support 3 formats, and you can create a new format.
- `json` (default): the filters will send as json in the request
    ```json
    filters=[{"field":"email","operator":"equal","value":"abc"}]
    ```
- `array`: the filters will send as array in the request
    ```
    filters[email][value]=abc&filters[email][operator]=equal
    ```
- `separator`: the filters will send as well as in the `array` format, but separated by a separator(`^` default) 
    
    the format sets with a separator symbol `separator:^`
    ```
    filters^email^value=abc&filters^email^operator=equal
    ```
> set the default query format in the config file `query_format` attribute

#### Create a new query format:
- create a new class and extends it from `QueryFormat`: `class MyFormat extends QueryFormat`
- implement the abstract function `format` that returns `FilterRequest` object
- add the class to the config file in `custom_query_format` attribute: `'custom_query_format' => MyFormat::class,`

## Fields
Normal Field options:
- field name is the column name
- alias is the key that you want to send in the request
- data-type: by default it set from model `casts`, if you want to set custom data-type, use `setDatatype`
- operators: the field will accept all operators unless you use `setExceptedOperators` to exclude some operators
- a relational field: only set the field name by `.` separator `channel.name`, `channel.type.name`
    > you can define field name by `.` separator, but you want to consider it as a non relational field 
    > by pass `false` for `inRelation` parameter (used in NoSQL DB or join between tables)
    ```php 
    $this->addField('channels.name', 'channel_name', false);
    ```
- customize a field query, you can make a scope for the field to customize the filter behavior. 
    scope name must be combined 3 sections :
    - scope
    - the value of `prefix_scope_function` key in config file (`where` is the default)
    - field name(or relation name) for example `email`
    ```php
    public function scopeWhereEmail(Builder $builder, Field $field, string $operator, $value, $conjunction = 'and')
    ```
    > you can customize a relational field by define the scope in the relation model OR define scope by relation name
                                                                                                                                                                                                                                                                                                                                                                                                               
    ```php
    OrderLine.php
  
    public function scopeWherePrice(Builder $builder, Field $field, string $operator, $value, $conjunction = 'and')
    
    OR
  
    Order.php
  
    public function scopeWhereOrderLines(Builder $builder, Field $field, string $operator, $value, $conjunction = 'and')
    ```
    > you can use `applyOperator` function to use the default behavior `$builder->applyOperator($operator, $field, $value, $conjunction);`
    
You can add fields to a model by using 4 functions:
- `addField`(string $field, string $alias = null, ?bool $inRelation = null): by default alias value same as field name value
    ```php
    $this->addField('total')->setDatatype('numeric');
    ```
- `addFields`($fields): accept an array of field and aliases:
    ```php
    $this->addFields(['created_at' => 'create_date', 'order_date'])->setDatatype('date');
    ```
- `addCountField`(string $relation, string $alias = null, callable $callback = null): add a field from count of relation,
    use can customize the count query and alias(by default is concat relation name(snake case) and `_count`)
    ```php 
    $this->addCountField('orderLines');
  
    $this->addCountField('orderLines', 'lines_count', function (Builder $builder) {
        $builder->where('quantity', '>', 1);
    });
    ```
   > 
- `addCustomField`(string $alias, string $sqlRaw, $relation = null): add a field from raw sql query
    ```php
    $this->addCustomField('my_total', '(`shipping_cost` + `subtotal`)');
    $this->addCustomField('line_subtotal', '(`price` + `quantity`)', 'orderLines'); // inside "orderLines" relation
    ```

## Conjunction
Currently, the package support one conjunction between all fields
`and` | `or`, default conjunction attribute in the config file `default_conjunction`

## Operators
The package has many operators, you can create new operators, 
and you can customize the operators aliases that you want to send in the request
- Equals (`=`, `equals`)
- NotEquals (`!=`, `notEquals`)
- GreaterThan (`>` , `greater`)
- GreaterThanOrEqual (`>=`, `greaterOrEqual`)
- LessThan (`<`, `less`)
- LessThanOrEqual (`<=`, `lessOrEqual`)
- In (`|`, `in`)
- NotIn (`!|`, `notIn`)
- Contains (`*`, `contains`)
- NotContains (`!*`, `notContains`)
- StartsWith (`^`, `startsWith`)
- NotStartsWith (`!^`, `notStartsWith`)
- EndsWith (`$`, `endsWith`)
- NotEndsWith (`!$`, `notEndsWith`)
- Between (`><`, `between`)

#### Create a new Operator:
- create a new class and extends it from `Operator`: `class MyOperator extends Operator`
- implement the abstract function `apply` and `getSqlOperator` (used as a default sql operator for count and custom field)
- add the class in the config file in `custom_operators` attribute: `'custom_operators' => [MyOperator::class => ['my-op', '*']],`

## Data Types:
- boolean
- date
- datetime
- numeric
- string

## Config
[Config File](https://github.com/AsemAlalami/Laravel-Advanced-Filter/blob/master/config/advanced_filter.php)
