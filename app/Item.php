<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


const CHANGE_TYPE_OVERALL_CHANGE = 3;

/**
 * App\Item
 *
 * @property int $id
 * @property string $name
 * @property string $name_1c
 * @property string $inv_number
 * @property string $serial_number
 * @property float $price
 * @property int $count
 * @property string $create_time
 * @property string $edit_time
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LocationLink[] $locationLinks
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item whereEditTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item whereInvNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item whereName1c($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item whereSerialNumber($value)
 * @mixin \Eloquent
 * @property int|null $last_owner
 * @property int|null $last_location
 * @property int $status
 * @property int|null $group_id
 * @property string|null $create_author
 * @property string|null $edit_author
 * @property string|null $last_comment
 * @property-read \App\HistoryChanges $historyChanges
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item whereCreateAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item whereEditAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item whereLastComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item whereLastLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item whereLastOwner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item whereStatus($value)
 */
class Item extends Model
{
    protected $table = 'items';
    public $timestamps = false;
    protected $guarded = [''];

}
