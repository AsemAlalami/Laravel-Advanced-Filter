<?php

namespace AsemAlalami\LaravelAdvancedFilter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use InvalidArgumentException;

abstract class Filter
{
    use HasFilter;

    /**
     * @param Builder|Model|string $source
     * @param Request|array $request
     */
    public static function for($source, $request)
    {
        if (is_string($source)) {
            $source = new $source();

            if (!$source instanceof Model) {
                throw new InvalidArgumentException('The source must be Model or Builder');
            }
        }

    }

    public function setupCallback(callable $callback)
    {
        $callback($this);

        return $this;
    }
}
