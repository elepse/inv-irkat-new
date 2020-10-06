<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Owner
 *
 * @property int $owner_id
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Owner whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Owner whereOwnerId($value)
 * @mixin \Eloquent
 * @property string $last_name
 * @property string $first_name
 * @property string|null $father_name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Owner whereFatherName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Owner whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Owner whereLastName($value)
 */
class Owner extends Model
{
    protected $table = 'owners';
    public $timestamps = false;
    protected $guarded = [''];
}
