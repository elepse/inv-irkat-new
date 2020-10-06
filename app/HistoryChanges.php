<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\HistoryChanges
 *
 * @property int $id
 * @property int|null $id_item
 * @property mixed $change_log
 * @property string $create_time
 * @property string $creator
 * @property int $type_change
 * @property int|null $id_group
 * @property-read \App\HistoryChanges $selfRel
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryChanges whereChangeLog($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryChanges whereCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryChanges whereCreator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryChanges whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryChanges whereIdGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryChanges whereIdItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HistoryChanges whereTypeChange($value)
 * @mixin \Eloquent
 */
class HistoryChanges extends Model
{
    protected $table = 'history_changes';
    public $timestamps = false;
    protected $guarded = [''];

    public function selfRel()
    {
        return $this->hasOne(self::class, 'id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'creator','id');
    }

}
