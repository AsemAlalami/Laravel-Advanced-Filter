<?php

namespace AsemAlalami\LaravelAdvancedFilter\Test;

use AsemAlalami\LaravelAdvancedFilter\QueryFormats\QueryFormat;
use Illuminate\Http\Request;

class QueryFormatsTest extends TestCase
{
    private $normalFilter = [
        'field' => 'email',
        'operator' => 'equal',
        'value' => 'abc',
    ];

    private $arrayValueFilter = [
        'field' => 'email',
        'operator' => 'in',
        'value' => ['abc1', 'abc2'],
    ];


    private $betweenFilter = [
        'field' => 'email',
        'operator' => 'between',
        'value' => ['from' => 10, 'to' => 20],
    ];

    /** @test */
    public function array_query_format()
    {
        $this->app['config']->set('advanced_filter.query_format', 'array');

        $queryFilters = 'filters[email][value]=abc&filters[email][operator]=equal';
        $request = Request::create("test?{$queryFilters}");

        $filters = QueryFormat::factory($request)->getFilters();
//        dd($filters);

        $this->assertEquals([$this->normalFilter], $filters);
    }

    /** @test */
    public function json_query_format()
    {
        $this->app['config']->set('advanced_filter.query_format', 'json');

        $queryFilters = 'filters=[{"field":"email","operator":"equal","value":"abc"}]';
        $request = Request::create("test?{$queryFilters}");

        $filters = QueryFormat::factory($request)->getFilters();

        $this->assertEquals([$this->normalFilter], $filters);
    }

    /** @test */
    public function separator_query_format()
    {
        $this->app['config']->set('advanced_filter.query_format', 'separator:^');

        $queryFilters = 'filters^email^value=abc&filters^email^operator=equal';
        $request = Request::create("test?{$queryFilters}");

        $filters = QueryFormat::factory($request)->getFilters();

        $this->assertEquals([$this->normalFilter], $filters);
    }

    /** @test */
    public function array_query_format_for_array_value()
    {
        $this->app['config']->set('advanced_filter.query_format', 'array');

        $queryFilters = 'filters[email][value][0]=abc1&filters[email][value][1]=abc2&filters[email][operator]=in';
        $request = Request::create("test?{$queryFilters}");

        $filters = QueryFormat::factory($request)->getFilters();

        $this->assertEquals([$this->arrayValueFilter], $filters);
    }

    /** @test */
    public function json_query_format_for_array_value()
    {
        $this->app['config']->set('advanced_filter.query_format', 'json');

        $queryFilters = 'filters=[{"field":"email","operator":"in","value":["abc1","abc2"]}]';
        $request = Request::create("test?{$queryFilters}");

        $filters = QueryFormat::factory($request)->getFilters();

        $this->assertEquals([$this->arrayValueFilter], $filters);
    }

    /** @test */
    public function separator_query_format_for_array_value()
    {
        $this->app['config']->set('advanced_filter.query_format', 'separator:^');

        $queryFilters = 'filters^email^value^0=abc1&filters^email^value^1=abc2&filters^email^operator=in';
        $request = Request::create("test?{$queryFilters}");

        $filters = QueryFormat::factory($request)->getFilters();

        $this->assertEquals([$this->arrayValueFilter], $filters);
    }

    /** @test */
    public function array_query_format_for_between()
    {
        $this->app['config']->set('advanced_filter.query_format', 'array');

        $queryFilters = 'filters[email][value][from]=10&filters[email][value][to]=20&filters[email][operator]=between';
        $request = Request::create("test?{$queryFilters}");

        $filters = QueryFormat::factory($request)->getFilters();

        $this->assertEquals([$this->betweenFilter], $filters);
    }

    /** @test */
    public function json_query_format_for_between()
    {
        $this->app['config']->set('advanced_filter.query_format', 'json');

        $queryFilters = 'filters=[{"field":"email","operator":"between","value":{"from":10,"to":20}}]';
        $request = Request::create("test?{$queryFilters}");

        $filters = QueryFormat::factory($request)->getFilters();

        $this->assertEquals([$this->betweenFilter], $filters);
    }

    /** @test */
    public function separator_query_format_for_between()
    {
        $this->app['config']->set('advanced_filter.query_format', 'separator:^');

        $queryFilters = 'filters^email^value^from=10&filters^email^value^to=20&filters^email^operator=between';
        $request = Request::create("test?{$queryFilters}");

        $filters = QueryFormat::factory($request)->getFilters();

        $this->assertEquals([$this->betweenFilter], $filters);
    }
}
