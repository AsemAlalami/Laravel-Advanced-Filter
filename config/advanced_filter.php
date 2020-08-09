<?php

return [
    'operators' => [
        'Equal' => ['=', 'equal'],
        'NotEqual' => ['!=', 'notEqual'],
        'GreaterThan' => ['>', 'greater'],
        'GreaterThanOrEqual' => ['>=', 'greaterOrEqual'],
        'LessThan' => ['<', 'less'],
        'LessThanOrEqual' => ['<=', 'lessOrEqual'],
    ],

    'custom_operators' => [

    ],

    'default_operator' => 'Equal',

    'prefix_operator_function' => 'filterWhere',

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

    'custom_query_format' => null,

    'param_filter_name' => 'filters', // or as prefix in "separate" query format

    'param_conjunction_name' => 'conjunction',

    'field_params' => [
        'field' => 'field', // used only in "json" query format
        'operator' => 'operator',
        'value' => 'value',
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
    | Yes must set it TRUE if your columns type is not "datetime or date"
    */
    'cast_db_date' => false,

    'prefix_scope_function' => 'where'
];
