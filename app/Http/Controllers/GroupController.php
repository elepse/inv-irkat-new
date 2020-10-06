<?php

namespace App\Http\Controllers;

use App\HistoryChanges;
use App\Item;
use App\ItemGroups;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;


const CHANGE_TYPE_CHANGE_LOCATION = 1;
const CHANGE_TYPE_CHANGE_OWNER = 2;
const CHANGE_TYPE_OVERALL_CHANGE = 3;
const CHANGE_TYPE_ADD_IN_GROUP = 4;
const CHANGE_TYPE_REMOVE_FROM_GROUP = 5;
const GROUP_DELETED = 1;

class GroupController extends Controller
{
    public function showGroupItems(Request $request)
    {
        $idGroup = $request->get('idGroup', 'null');
        $status = true;
        $error = null;
        $query = (new ItemGroups)->newQuery()
            ->with('groupItems')
            ->join('owners', 'owners.owner_id', '=', 'item_groups.owner_group')
            ->join('locations', 'locations.loc_id', '=', 'item_groups.location_group');
        $query->where('group_id', '=', $idGroup);
        $query = $query->get();
        if ($query->get(0)->deleted === 1) {
            $status = false;
            $error = 'Группа удалена!';
        }
        return (['status' => $status, 'group' => $query, 'error' => $error]);
    }

    public function untieItem(Request $request)
    {
        $id = $request->get('id', null);
        $groupId = $request->get('groupId', null);
        $status = true;
        $changeLog = [
            'group' => $groupId,
        ];
        if ($groupId != null && $id != null) {
            try {
                DB::transaction(function () use ($id, $groupId, $changeLog) {
                    (new Item())->find($id)->update([
                        'group_id' => null
                    ]);
                    (new HistoryChanges())->fill([
                        'id_item' => $id,
                        'change_log' => json_encode($changeLog),
                        'create_time' => date("Y-m-d H:i:s"),
                        'creator' => Auth::user()->id,
                        'type_Change' => CHANGE_TYPE_REMOVE_FROM_GROUP,
                    ])->save();
                });
            } catch (\PDOException $e) {
                $status = false;
            } catch (\Exception $e) {
                $status = false;
            }
        } else $status = false;
        return ['status' => $status];
    }

    public function create(Request $request)
    {
        $status = true;
        $name = $request->get('name', null);
        $name1c = $request->get('name1c', null);
        $owner = $request->get('owner', null);
        $location = $request->get('location', null);
        $inv_number = $request->get('inv_number', null);
        $arrayItems = $request->get('arrayItems', null);
        $changeLog = null;
        $error = null;

        if (!is_null($name) && !is_null($name1c) && !is_null($owner) && !is_null($location) && !is_null($inv_number) && !is_null($arrayItems)) {
            try {

                DB::transaction(function () use ($inv_number, $name1c, $changeLog, $name, $owner, $location, $arrayItems) {
                    $query = (new ItemGroups())->fill([
                        'inventory_number' => $inv_number,
                        'group_create_time' => date("Y-m-d H:i:s"),
                        'name1c_group' => $name1c,
                        'name_group' => $name,
                        'owner_group' => $owner,
                        'location_group' => $location
                    ]);

                    $query->save();
                    $idNewGroup = $query->group_id;
                    foreach ($arrayItems as $item) {
                        $id = $item['id'];
                        (new Item())->find($id)->update(['group_id' => $idNewGroup]);
                        $changeLog = [
                            'group' => $idNewGroup
                        ];
                        (new HistoryChanges())->fill([
                            'id_item' => $id,
                            'change_log' => json_encode($changeLog),
                            'create_time' => date("Y-m-d H:i:s"),
                            'creator' => Auth::user()->id,
                            'type_change' => CHANGE_TYPE_ADD_IN_GROUP
                        ])->save();
                    }
                });
            }catch (\Exception $e) {
                $status = false;
            }catch (\PDOException $e){
                $status = false;
            }
        } else {
            $status = false;
        }
        if (!$status) $error = 'Что-то пошло не так. Обратитесь к системному администратору';;
        return ['status' => $status, 'error' => $error];
    }

    public function reTieItem(Request $request)
    {
        $id = $request->get('id', null);
        $groupId = $request->get('groupId', null);
        $status = true;
        $changeLog = [
            'group' => $groupId
        ];
        if (!is_null($groupId) && !is_null($id)) {
            try {
                DB::transaction(function () use ($id, $groupId, $changeLog) {
                    (new Item())->find($id)->update([
                        'group_id' => $groupId
                    ]);
                    (new HistoryChanges())->fill([
                        'id_item' => $id,
                        'change_log' => json_encode($changeLog),
                        'create_time' => date("Y-m-d H:i:s"),
                        'creator' => Auth::user()->id,
                        'type_Change' => CHANGE_TYPE_ADD_IN_GROUP,
                    ])->save();
                });
            } catch (\PDOException $e) {
                $status = false;
            } catch (\Exception $e) {
                $status = false;
            }
        } else $status = false;

        return ['status' => $status];
    }

    public function disband(Request $request)
    {
        $status = true;
        $groupId = $request->get('groupId', null);
        $changeLog = [
            'group' => (int)$groupId
        ];
        try {
            DB::transaction(function () use ($groupId, $changeLog) {
        $items = (new Item())->where('group_id', '=', "$groupId")->get();
                (new Item())->where('group_id', '=', "$groupId")->update(array('group_id' => null));
                (new ItemGroups())->newQuery()->where('group_id', '=', "$groupId")->update(['deleted' => GROUP_DELETED]);
                foreach ($items as $item) {
                    (new HistoryChanges())->fill([
                        'id_item' => $item->id,
                        'change_log' => json_encode($changeLog),
                        'create_time' => date("Y-m-d H:i:s"),
                        'creator' => Auth::user()->id,
                        'type_change' => CHANGE_TYPE_REMOVE_FROM_GROUP,
                    ])->save();
                }
            });
        } catch (\PDOException $e) {
            $status = false;
        } catch (\Exception $e) {
            $status = false;
        }
        return (['status' => $status]);
    }
}