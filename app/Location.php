<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Location
 *
 * @property int $loc_id
 * @property string $loc_name
 * @property string $loc_create_time
 * @property string|null $loc_edit_time
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereLocCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereLocEditTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereLocId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Location whereLocName($value)
 * @mixin \Eloquent
 */
class Location extends Model
{
    protected $table = 'locations';
    public $timestamps = false;
    protected $guarded = [''];
}
