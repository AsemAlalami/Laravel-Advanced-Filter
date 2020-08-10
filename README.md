## Laravel Advanced Filter
This package allows you to filter/sort on laravel models

You can choose filter fields and customize its data-types, aliases and excepted operators, 
and you can customize your request format, and add a new operator or overwrite an existed operators


### Installation  
You can install the package via composer:
```  
composer required AsemAlalami/Laravel-Advanced-Filter
```  

The package will automatically register its service provider.

You can optionally publish the config file with:
```
php artisan vendor:publish --provider="AsemAlalami\LaravelAdvancedFilter\AdvancedFilterServiceProvider" --tag="config"
```

These default config file that will be published:
 [Config File](https://github.com/AsemAlalami/Laravel-Advanced-Filter/blob/master/config/advanced_filter.php)

### Usage
- use `HasFilter` trait in the model
- add your fields in the implementation of abstract function `setupFilter`
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

    public function orderLineItems()
    {
        return $this->hasMany(OrderLineItem::class);
    }

    public function setupFilter()
    {
        $this->addField('void'); // will cast to 'boolean' from the model casts
        $this->addField('total')->setDatatype('numeric');
        $this->addFields(['source', 'subsource', 'order_date']);
        // field from relation
        $this->addFields(['channel.created_at' => 'channel_create'])->setDatatype('date');
        // field from relation count
        $this->addCountField('orderLineItems');
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
```

### Query Format
Query format is shape of the request, the package support 3 formats, and you can define a new format
- `json` (default): the request will send filters as json 
    ```json
    filters=[{"field":"email","operator":"equal","value":"abc"}]
    ```
- `array`: the request will send filters as array
    ```
    filters[email][value]=abc&filters[email][operator]=equal
    ```
- `separate`: the request will send as well as in `array`, but separated by a separator, 
    
    the format set with separator symbol `separate:^`
    ```
    filters^email^value=abc&filters^email^operator=equal
    ```
> set the default query format in the config file `query_format` attribute

##### Define a new query format:
- create a new class and extends it from `QueryFormat`: `class MyFormat extends QueryFormat`
- implement abstract function `format` that returns `FilterRequest`
- add class to the config file in `custom_query_format` attribute: `'custom_query_format' => MyFormat::class,`


### Fields
Normal Field options:
- field name is the column name
- alias is the key that you want to send it in the request
- data-type set from model casts by default, if you want to set custom data-type use `setDatatype`
- operators, field will accept all operators unless you use `setExceptedOperators` to exclude some operators
- a relational field, only set field name by `.` separator `channel.name`, `channel.type.name`
- customize query, you can make a scope for the field to customize filter behavier, scope name must be combined 3 sections :
    - scope
    - `prefix_scope_function`value of the key in config file (`where` is the default)
    - attribute name(or function name)
    ```php
    public function scopeWhereEmail(Builder $builder, Field $field, string $operator, $value, $conjunction = 'and')
    ```
    > you can customize spacific attribute in relational field by define the scope in the relation model
    
You can add fields to model by using 4 functions:
- `addField`(string $field, string $alias = null): default alias same field name
    ```php
    $this->addField('total')->setDatatype('numeric');
    ```
- `addFields`($fields): accept an array of field and aliases:
    ```php
    $this->addFields(['created_at' => 'create_date', 'order_date'])->setDatatype('date');
    ```
- `addCountField`(string $relation, string $alias = null, callable $callback = null): add a field from count of relation,
    use can customize the count query and alias(by default is camel case on relation plus `_count`)
    ```php 
    $this->addCountField('orderLineItems', 'lines_count', function (Builder $builder) {
        $builder->where('quantity', '>', 1);
    });
    ```
- `addCustomField`(string $alias, string $sqlRaw): add a field from raw sql query
    ```php
    $this->addCustomField('my_total', '(`shipping_cost` + `subtotal`)');
    ```

### Conjunction
Currently the package support one conjunction between all fields
`and` | `or`, default conjunction attribute in the config file `default_conjunction`

### Operators
The package has a many of operators, and you can define a new operators,
also the package support customize the operators aliases to send in the request
- Equal
- NotEqual
- GreaterThan
- GreaterThanOrEqual
- LessThan
- LessThanOrEqual


##### Define a new Operator:
- create a new class and extends it from `Operator`: `class MyOperator extends Operator`
- implement abstract function `apply` and `getSqlOperator` (used as a default sql operator for count and custom field)
- add the class in the config file in `custom_operators` attribute: `'custom_operators' => [MyOperator::class => ['my-op', '*']],`

### Config
