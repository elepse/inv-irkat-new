<?php

namespace App;

use const App\Http\Controllers\CHANGE_TYPE_CHANGE_OWNER;
use Illuminate\Database\Eloquent\Model;

/**
 * App\ItemGroups
 *
 * @property int $group_id
 * @property string $inventory_number
 * @property string $group_create_time
 * @property string|null $group_edit_time
 * @property string $name1c_group
 * @property string $name_group
 * @property int $owner_group
 * @property int $location_group
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Item[] $groupItems
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ItemGroups whereGroupCreateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ItemGroups whereGroupEditTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ItemGroups whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ItemGroups whereInventoryNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ItemGroups whereLocationGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ItemGroups whereName1cGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ItemGroups whereNameGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ItemGroups whereOwnerGroup($value)
 * @mixin \Eloquent
 * @property int $deleted
 * @property-read int|null $group_items_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ItemGroups newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ItemGroups newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ItemGroups query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ItemGroups whereDeleted($value)
 */
class ItemGroups extends Model
{
    protected $table = 'item_groups';
    public $timestamps = false;
    protected $guarded = [''];
    protected $primaryKey = 'group_id';

    public function groupItems()
    {
        return $this->HasMany(Item::class, 'group_id', 'group_id')
            ->join('owners', 'owners.owner_id', '=', 'items.last_owner')
            ->join('locations', 'locations.loc_id', '=', 'items.last_location');
    }

    protected static function boot()
    {
        parent::boot();
        static::updated(function ($itemGroup) {
            $items = Item::query()->where('group_id', '=', "$itemGroup->group_id")->get();
            $actuallyGroup = (new ItemGroups)->find($itemGroup->group_id)->get(['owner_group']);
            if ($itemGroup->isDirty('owner_group')) {
                foreach ($items as $item) {
                    Item::query()->find("$item->id")->fill([
                        'last_owner' => $itemGroup->owner_group,
                    ])->update();
                    $changeLog = [
                        'oldOwner' => $actuallyGroup->get(0)->owner_group,
                        'newOwner' => $itemGroup->owner_group,
                        'inGroup' => $itemGroup->group_id
                    ];
                    (new HistoryChanges())->fill([
                        'id_item' => $item->id,
                        'change_log' => json_encode($changeLog),
                        'create_time' => date("Y-m-d H:i:s"),
                        'creator' => 'admin',
                        'type_Change' => CHANGE_TYPE_CHANGE_OWNER,
                    ])->save();
                }
            }
        });
    }
}
