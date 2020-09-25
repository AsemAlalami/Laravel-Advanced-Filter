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
- data-type set from model `casts` by default, if you want to set custom data-type use `setDatatype`
- operators, field will accept all operators unless you use `setExceptedOperators` to exclude some operators
- a relational field, only set field name by `.` separator `channel.name`, `channel.type.name`
    > you can define field by `.` separator, but consider it as a non relational field by assign `false` for `inRelation` parameter
    ```php 
    $this->addField('channels.name', 'channel_name', false);
    ```
- customize a query, you can make a scope for the field to customize a filter behavior, scope name must be combined 3 sections :
    - scope
    - `prefix_scope_function`value of the key in config file (`where` is the default)
    - attribute name(or relation name) for example `email`
    ```php
    public function scopeWhereEmail(Builder $builder, Field $field, string $operator, $value, $conjunction = 'and')
    ```
    > you can customize specific attribute in relational field by define the scope in the relation model
                                                                                                                                                                                                                                                                                                                                                                                                               
    > you can use `applyOperator` function to use default behavior `$builder->applyOperator($operator, $field, $value, $conjunction);`
    
You can add fields to model by using 4 functions:
- `addField`(string $field, string $alias = null, ?bool $inRelation = null): default alias same field name
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
- `addCustomField`(string $alias, string $sqlRaw, $relation = null): add a field from raw sql query
    ```php
    $this->addCustomField('my_total', '(`shipping_cost` + `subtotal`)');
    $this->addCustomField('line_subtotal', '(`price` + `quantity`)', 'orderLineItems');
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

```php
/*
|--------------------------------------------------------------------------
| Operators
|--------------------------------------------------------------------------
|
| These build-in package operators and its aliases
| You can customize default aliases, remove any operator
|
*/

'operators' => [
    'Equal' => ['=', 'equal'],
    'NotEqual' => ['!=', 'notEqual'],
    'GreaterThan' => ['>', 'greater'],
    'GreaterThanOrEqual' => ['>=', 'greaterOrEqual'],
    'LessThan' => ['<', 'less'],
    'LessThanOrEqual' => ['<=', 'lessOrEqual'],
],

/*
|--------------------------------------------------------------------------
| Custom Operators
|--------------------------------------------------------------------------
|
| Add your operators here
|
| example: operator class and its aliases
|   MyOperator::class => ['my-op', '*']
|
*/

'custom_operators' => [

],

/*
|--------------------------------------------------------------------------
| Default Operator
|--------------------------------------------------------------------------
|
| Default operator if the field sent in the request without operator
|
*/

'default_operator' => 'Equal',

/*
|--------------------------------------------------------------------------
| Prefix Operator Function
|--------------------------------------------------------------------------
|
| This option used when binging operators to Builder
| The operators bound to Builder by using macros
|
| example:
|   if you want to filter by GreaterThan operator
|   $orders->filterWhereGreaterThan('total', 100)
|
*/

'prefix_operator_function' => 'filterWhere',

/*
|--------------------------------------------------------------------------
| Default Conjunction
|--------------------------------------------------------------------------
|
| This option used when request sent without conjunction
|
| values: and | or
|
*/

'default_conjunction' => 'and',

/*
|--------------------------------------------------------------------------
| Default Query Format
|--------------------------------------------------------------------------
|
| This option controls the default query format.
| This format is used when sending request.
|
| Supported:
|   -  "separate:^"     : filters^email^value=abc&filters^email^operator=equal
|   -  "array"          : filters[email][value]=abc&filters[email][operator]=equal
|   -  "json" (Default) : filters=[{"field":"email","operator":"equal","value":"abc"}]
|
*/

'query_format' => 'json',

/*
|--------------------------------------------------------------------------
| Custom Query Format
|--------------------------------------------------------------------------
|
| Add you custom query format here
| this value will set as a default query format of the request
|
| example: MyQueryFormat::class
|
*/

'custom_query_format' => null,

/*
|--------------------------------------------------------------------------
| Request Parameters Names
|--------------------------------------------------------------------------
|
| This options to customize your request parameters names
|
*/

// name of the parameter that contains the fields
'param_filter_name' => 'filters', // or as prefix in "separate" query format
// name of the parameter that set the conjunction
'param_conjunction_name' => 'conjunction',
// names of the parameters that define the field
'field_params' => [
    'field' => 'field', // filed name, only used in "json" query format
    'operator' => 'operator', // field operator
    'value' => 'value', // field value
],

/*
|--------------------------------------------------------------------------
| Cast date in the database
|--------------------------------------------------------------------------
|
| This options will use "whereDate" function to compare date fields
| the default will compare by start/end of day (as between)
| this feature for big-data if you have index on the column
|
| And should set it to TRUE if your columns type (in the database) is not "datetime or date"
|
*/
'cast_db_date' => false,

/*
|--------------------------------------------------------------------------
| Prefix Scope Function
|--------------------------------------------------------------------------
|
| This options used when you want to customize field filter behavior by defining scope for it
|
| example:
|   you need to customize behavior for "email" field, the scope function name with be:
|   scopeWhereEmail(Builder $builder, Field $field, string $operator, $value, $conjunction = 'and')
|
*/

'prefix_scope_function' => 'where'
```
