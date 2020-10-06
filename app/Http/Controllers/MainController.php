<?php

namespace App\Http\Controllers;

use App\HistoryChanges;
use App\Item;
use App\Location;
use App\Owner;
use Illuminate\Http\Request;
use Adldap\AdldapInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

const HISTORY_ITEM = 1;
const HISTORY_GROUP = 2;

class MainController extends Controller
{

    protected $ldap;

    public function __construct(AdldapInterface $ldap)
    {
        $this->ldap = $ldap;
    }

    public function getOwners() {
        $owners = $owners = Owner::all('owner_id', 'first_name', 'last_name', 'father_name');
        $owners = $owners->sortBy('last_name')->values()->all();
        return $owners;
    }

    public function getLocations() {
        $locations = Location::all('loc_id', 'loc_name');
        $locations = $locations->sortBy('loc_name')->values()->all();
        return $locations;
    }

    // Главная страница + данные о владельцах и расположениях
    /**
     * @return array
     */
    public function index()
    {
        $owners = $this->getOwners();
        $locations = $this->getLocations();
        return view('welcome', ['owners' => $owners, 'locations' => $locations]);
    }
    //Поиск для Items
    /**
     * @param Request $request
     * @return array
     */
    public function search(Request $request)
    {
        $owner = $request->get('owner', null);
        $location = $request->get('location', null);
        $inv = $request->get('inv', null);
        $name = $request->get('name', null);
        $ser = $request->get('ser', null);
        $com = $request->get('com', null);
        $status = $request->get('status', null);
        $name1C = $request->get('Name1C', null);
        $invStatus = $request->get('invStatus', null);
        $sortTo = $request->get('sortTo', null);
        $sortBy = $request->get('sortBy', null);

        $query = (new Item)->newQuery();
        $query->leftjoin('locations', 'locations.loc_id', '=', 'items.last_location')
            ->join('owners', 'owners.owner_id', '=', 'items.last_owner')
            ->leftJoin('item_groups', 'item_groups.group_id', '=', 'items.group_id');

        if ($owner) {
            $query->where('last_owner', '=', "$owner");
        }

        if ($location) {
            $query->whereLast_location($location);
        }

        if ($status) {
            $query = $query->whereStatus($status);
        }

        if ($inv) {
            $query = $query->where(function($q) use ($inv){
                $q->where('inv_number', 'like', "%$inv%")
                    ->orWhere('inventory_number', 'like', "%$inv%");
            });
        }

        if ($name) {
            $query = $query->where(function($q) use ($name) {
                $q->where('name', 'like', "%$name%")
                    ->orWhere('name_group', 'like', "%$name%");
            });
        }

        if ($ser) {
            $query = $query->where('serial_number', 'like', "%$ser%");
        }

        if ($com) {
            $query = $query->where('last_comment', 'like', "%$com%");
        }

        if ($name1C) {
            $query = $query->where('name_1c', 'like', "%$name1C%");
        }

        if ($invStatus) {
            $query = $query->where('inv_status','=', "$invStatus");
        }

        if ($sortTo > 0) {
            $sortTo = 'asc';
        } else {
            $sortTo = 'desc';
        }

        if ($sortBy) {
            $query = $query->orderBy($sortBy, $sortTo);
        }

        $pag = $query->paginate(20);
        return ['status' => true, 'items' => $pag];
    }

    //Для создание нового владельца

    /**
     * @param Request $request
     * @return array
     */
    public function addOwner(Request $request)
    {
        $firstName = $request->get('firstName', null);
        $lastName = $request->get('lastName', null);
        $fatherName = $request->get('fatherName', null);
        if ($firstName != null && $lastName != null) {
            $status = (new Owner())->fill([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'father_name' => $fatherName
            ]);
            $statusQuery = $status->save();
            $idNewOwner = $status->id;
            return (['status' => $statusQuery, 'idOwner' => $idNewOwner]);
        } else {
            $error = 'Обратитесь к системному администратору';
            return (['status' => false, 'error' => $error]);
        }
    }

    public function addLocation(Request $request)
    {
        $nameLocation = $request->get('nameLocation', null);
        if ($nameLocation != null) {
            $status = (new Location())->fill([
                'loc_name' => $nameLocation,
                'loc_create_time' => date("Y-m-d H:i:s"),
            ]);
            $statusQuery = $status->save();
            $idNewLocation = $status->id;

            return (['status' => $statusQuery, 'idLocation' => $idNewLocation]);
        } else {
            $error = 'Обратитесь к системному администратору';
            return (['status' => false, 'error' => $error]);
        }
    }

    public function history(Request $request)
    {
        $id = $request->get('id', null);
        $type = $request->get('type', null);
        $status = true;
        $data = null;
        $error = null;
        if (!is_null($id) && !is_null($type)) {
            if ($type == HISTORY_ITEM) {
                $data = HistoryChanges::query()
                    ->leftjoin('owners', 'owners.owner_id', '=', 'history_changes.change_log->newOwner')
                    ->leftjoin('locations', 'locations.loc_id', '=', 'history_changes.change_log->newLocation')
                    ->leftjoin('item_groups', 'item_groups.group_id', '=', 'history_changes.change_log->group')
                    ->whereIdItem($id)
                    ->with('user');
            } elseif ($type == HISTORY_GROUP) {
                $data = HistoryChanges::query()
                    ->leftjoin('owners', 'owners.owner_id', '=', 'history_changes.change_log->newOwner')
                    ->leftjoin('locations', 'locations.loc_id', '=', 'history_changes.change_log->newLocation')
                    ->whereIdGroup($id)
                    ->with('user');
            } else {
                $status = false;
                $error = 'Не верный тип запрашиваемого элемента. Обратитесь к системному администратору.';
            }
        } else {
            $status = false;
            $error = 'Запрос с пустыми значениями. Обратитесь к системному администратору';
        }
        return (['status' => $status, 'data' => $data->orderBy('create_time', 'ask')->get(), 'error' => $error]);
    }
}