<?php

namespace AsemAlalami\LaravelAdvancedFilter\Test\Models;

use AsemAlalami\LaravelAdvancedFilter\HasFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class User extends Model
{
    use HasFilter;

    protected $fillable = ['first_name', 'last_name', 'email'];

    public function setupFilter()
    {
        $this->addFields(['first_name', 'last_name', 'email', 'created_at']);
        $this->addCustomField('full_name', "CONCAT(`first_name`, ' ', `last_name`)");
    }
}
