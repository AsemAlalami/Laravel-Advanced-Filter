<?php

return [

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
        'Equals' => ['=', 'equals'],
        'NotEquals' => ['!=', 'notEquals'],
        'GreaterThan' => ['>', 'greater'],
        'GreaterThanOrEqual' => ['>=', 'greaterOrEqual'],
        'LessThan' => ['<', 'less'],
        'LessThanOrEqual' => ['<=', 'lessOrEqual'],
        'In' => ['|', 'in'],
        'NotIn' => ['!|', 'notIn'],
        'Contains' => ['*', 'contains'],
        'NotContains' => ['!*', 'notContains'],
        'StartsWith' => ['^', 'startsWith'],
        'NotStartsWith' => ['!^', 'notStartsWith'],
        'EndsWith' => ['$', 'endsWith'],
        'NotEndsWith' => ['!$', 'notEndsWith'],
        'Between' => ['><', 'between'],
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

    'default_operator' => 'Equals',

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
    |   -  "separator:^"    : filters^email^value=abc&filters^email^operator=equal
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
    | Add your custom query format here
    | this format will set as a default query format of the request
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

    'prefix_scope_function' => 'where',

    /*
    |--------------------------------------------------------------------------
    | Empty Value As NULL
    |--------------------------------------------------------------------------
    |
    | This option used when you want to considered empty value as NULL
    |
    */

    'empty_as_null' => false,
];
