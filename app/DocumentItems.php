<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\DocumentItems
 *
 * @property int $document_id
 * @property int|null $item_id
 * @property int|null $group_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DocumentItems whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DocumentItems whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DocumentItems whereItemId($value)
 * @mixin \Eloquent
 */
class DocumentItems extends Model
{
    protected $table = 'document_items';
    public $timestamps = false;
    protected $guarded = [''];

    public function item() {
        return $this->belongsTo(Item::class, 'item_id','id');
    }

    public function group() {
        return $this->belongsTo(ItemGroups::class, 'group_id','group_id')->with('groupItems');
    }

}
