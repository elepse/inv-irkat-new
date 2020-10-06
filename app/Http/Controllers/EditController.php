<?php


namespace App\Http\Controllers;

use App\HistoryChanges;
use App\Item;
use App\ItemGroups;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;

define('CHANGE_TYPE_CHANGE_LOCATION', 1);
define('CHANGE_TYPE_CHANGE_OWNER', 2);
define('CHANGE_TYPE_OVERALL_CHANGE', 3);
define('CHANGE_TYPE_ADD_IN_GROUP', 4);
define('CHANGE_TYPE_REMOVE_FROM_GROUP', 5);

class EditController extends Controller
{
    public function edit(Request $request)
    {
        $id = $request->get('id', null);
        $query = (new Item())->newQuery()->where('id', '=', $id);
        return ['status' => true, 'item' => $query->first()];
    }

    public function save(Request $r)
    {
        $status = true;
        $EditId = $r->get('EditId', null);
        $EditName = $r->get('EditName', null);
        $EditName1C = $r->get('EditName1C', null);
        $EditInv = $r->get('EditInv', null);
        $EditSer = $r->get('EditSer', null);
        $EditCount = $r->get('EditCount', null);
        $EditPrice = $r->get('EditPrice', null);
        $EditStatus = $r->get('EditStatus', null);
        $EditCom = $r->get('EditCom', null);
        $EditInvStatus = $r->get('EditInvStatus', null);
        $oldParams = Item::query()->where('id', '=', "$EditId")->get();
        $oldParams = $oldParams->get(0);
        $array_change = [
            'oldInv_number' => $oldParams->inv_number,
            'oldName' => $oldParams->name,
            'oldName_1C' => $oldParams->name_1c,
            'oldSer_number' => $oldParams->serial_number,
            'oldStatus' => $oldParams->status,
            'oldPrice' => $oldParams->price,
            'oldCount' => $oldParams->count,
            'oldInv_status' => (int) $oldParams->inv_status,
            'oldComment' => $oldParams->last_comment,
            'newInv_number' => $EditInv,
            'newName' => $EditName,
            'newName_1C' => $EditName1C,
            'newSer_number' => $EditSer,
            'newStatus' => (int)$EditStatus,
            'newPrice' => $EditPrice,
            'newCount' => (int)$EditCount,
            'newComment' => $EditCom,
            'newInv_status' => (int)$EditInvStatus,
        ];

        try {
            DB::transaction(function () use ($EditId, $EditInvStatus, $EditName, $EditName1C, $EditInv, $EditSer, $EditPrice, $EditCount, $EditStatus, $EditCom, $array_change) {
                $query = (new Item())->find($EditId)->fill([
                    'name' => $EditName,
                    'name_1c' => $EditName1C,
                    'inv_number' => $EditInv,
                    'serial_number' => $EditSer,
                    'price' => $EditPrice,
                    'count' => $EditCount,
                    'edit_time' => date("Y-m-d H:i:s"),
                    'status' => $EditStatus,
                    'last_comment' => $EditCom,
                    'inv_status' => $EditInvStatus
                ])->update();

                (new HistoryChanges())->fill([
                    'change_log' => json_encode($array_change),
                    'type_Change' => CHANGE_TYPE_OVERALL_CHANGE,
                    'id_item' => $EditId,
                    'create_time' => date("Y-m-d H:i:s"),
                    'creator' => Auth::user()->id
                ])->save();
            });
        } catch (\Exception $e) {
            $status = false;
        } catch (\PDOException $e) {
            $status = true;
        }
        return ['status' => $status];
    }

// перенос в другое расположение
    public function saveNewLocation(Request $request)
    {
        $location = $request->get('location', null);
        $groups = $request->get('groups', null);
        $items = $request->get('items', null);
        $comment = $request->get('comment', null);
        $status = true;
        if ($location != null && (($groups != null) || ($items != null)) && $comment != null) {
            try {
                DB::transaction(function () use ($items, $comment, $location, $groups) {
                    if ($items != null) {
                        foreach ($items as $item) {
                            $oldLocation = (new Item())->find($item['id'])->get(['last_location']);
                            $query = (new Item())->find($item['id'])->update(['last_location' => $location]);
                            $changeLog = [
                                'newLocation' => (int)$location,
                                'oldLocation' => $oldLocation->get(0)->last_location,
                                'commentForMove' => $comment
                            ];
                            (new HistoryChanges())->fill([
                                'id_item' => $item['id'],
                                'change_log' => json_encode($changeLog),
                                'create_time' => date("Y-m-d H:i:s"),
                                'creator' => Auth::user()->id,
                                'type_change' => CHANGE_TYPE_CHANGE_LOCATION
                            ])->save();
                        }
                    }
                    if ($groups != null) {
                        foreach ($groups as $group) {
                            $query = (new ItemGroups())->find($group['idGroup'])->update(['location_group' => $location]);
                            $oldLocation = (new ItemGroups())->find($group['idGroup'])->get(['location_group']);
                            $changeLog = [
                                'newLocation' => $location,
                                'oldLocation' => $oldLocation->get(0)->location_group,
                                'commentForMove' => $comment
                            ];
                            (new HistoryChanges())->fill([
                                'change_log' => json_encode($changeLog),
                                'create_time' => date("Y-m-d H:i:s"),
                                'creator' => Auth::user()->id,
                                'type_change' => CHANGE_TYPE_CHANGE_LOCATION,
                                'id_group' => $group['idGroup']
                            ]);
                        }
                    }
                });
            } catch (\PDOException $e) {
                $status = false;
            } catch (\Exception $e) {
                $status = false;
            }
        } else $status = false;
        return (['status' => $status]);
    }
}
