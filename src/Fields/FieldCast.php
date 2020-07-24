<?php


namespace AsemAlalami\LaravelAdvancedFilter\Fields;


use Carbon\CarbonInterface;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

trait FieldCast
{
    /**
     * The built-in, primitive data types supported by Filter.
     *
     * @var array
     */
    public static $primitiveDatatypes = [
        'boolean',
        'date',
        'datetime',
        'numeric',
        'string',
    ];

    protected function getFieldCastType(string $field, $casts)
    {
        if (array_key_exists($field, $casts)) {

            if ($this->isDatetime($casts[$field])) {
                return 'datetime';
            }

            if ($this->isDate($casts[$field])) {
                return 'date';
            }

            if ($this->isNumeric($casts[$field])) {
                return 'numeric';
            }

            if ($this->isBoolean($casts[$field])) {
                return 'boolean';
            }

        }

        return 'string';
    }

    protected function isDatetime(string $cast)
    {
        return Str::startsWith($cast, 'datetime');
    }

    protected function isDate(string $cast)
    {
        return Str::startsWith($cast, 'date');
    }

    protected function isNumeric(string $cast)
    {
        return Str::startsWith($cast, [
            'int',
            'integer',
            'real',
            'float',
            'double',
            'decimal',
        ]);
    }

    protected function isBoolean(string $cast)
    {
        return Str::startsWith($cast, ['bool', 'boolean']);
    }

    public function getCastedValue($value)
    {
        if (is_null($value)) {
            return $value;
        }

        switch ($this->getDatatype()) {
            case 'numeric':
                return (float)$value;
            case 'string':
                return (string)$value;
            case 'boolean':
                return (bool)$value;
            case 'date':
                return $this->asDateTime($value)->startOfDay();
            case 'datetime':
                return $this->asDateTime($value);
            case 'timestamp':
                return $this->asDateTime($value)->getTimestamp();
        }

        return $value;
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @param mixed $value
     * @return \Illuminate\Support\Carbon
     */
    protected function asDateTime($value)
    {
        // If this value is already a Carbon instance, we shall just return it as is.
        // This prevents us having to re-instantiate a Carbon instance when we know
        // it already is one, which wouldn't be fulfilled by the DateTime check.
        if ($value instanceof CarbonInterface) {
            return Date::instance($value);
        }

        // If the value is already a DateTime instance, we will just skip the rest of
        // these checks since they will be a waste of time, and hinder performance
        // when checking the field. We will just return the DateTime right away.
        if ($value instanceof DateTimeInterface) {
            return Date::parse(
                $value->format('Y-m-d H:i:s.u'), $value->getTimezone()
            );
        }

        // If this value is an integer, we will assume it is a UNIX timestamp's value
        // and format a Carbon object from this timestamp. This allows flexibility
        // when defining your date fields as they might be UNIX timestamps here.
        if (is_numeric($value)) {
            return Date::createFromTimestamp($value);
        }

        // If the value is in simply year, month, day format, we will instantiate the
        // Carbon instances from that format. Again, this provides for simple date
        // fields on the database, while still supporting Carbonized conversion.
        if ($this->isStandardDateFormat($value)) {
            return Date::instance(Carbon::createFromFormat('Y-m-d', $value)->startOfDay());
        }

        $format = $this->getDateFormat();

        // Finally, we will just assume this date is in the format used by default on
        // the database connection and use that format to create the Carbon object
        // that is returned back out to the developers after we convert it here.
        if (Date::hasFormat($value, $format)) {
            return Date::createFromFormat($format, $value);
        }

        return Date::parse($value);
    }

    /**
     * Determine if the given value is a standard date format.
     *
     * @param string $value
     * @return bool
     */
    protected function isStandardDateFormat($value)
    {
        return preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $value);
    }

    /**
     * Get the format for database stored dates.
     *
     * @return string
     */
    public function getDateFormat()
    {
        return $this->getModel()->getDateFormat() ?: $this->getModel()->getConnection()->getQueryGrammar()->getDateFormat();
    }
}
