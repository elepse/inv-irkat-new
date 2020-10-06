<?php

namespace App\Http\Controllers;

use App\DocumentItems;
use App\HistoryChanges;
use App\ItemGroups;
use Illuminate\Http\Request;
use App\Document;
use App\Owner;
use App\Location;
use App\Item;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\DocumentFile;
use TCPDF;
use function Psy\debug;

define('DOC_STATUS_NONE_DOCUMENT', 1);
define('DOC_STATUS_WAIT', 2);
define('DOC_STATUS_ACCEPT', 3);
define('DOC_STATUS_REJECT', 4);

define('CHANGE_TYPE_CHANGE_LOCATION', 1);
define('CHANGE_TYPE_CHANGE_OWNER', 2);
define('CHANGE_TYPE_OVERALL_CHANGE', 3);
define('CHANGE_TYPE_ADD_IN_GROUP', 4);
define('CHANGE_TYPE_REMOVE_FROM_GROUP', 5);

class DocumentsController extends Controller
{

    public function main()
    {
        $owners = Owner::all('owner_id', 'first_name', 'last_name', 'father_name');
        $locations = Location::all('loc_id', 'loc_name');
        return view('Documents', ['owners' => $owners, 'locations' => $locations]);
    }

    public function save(Request $request)
    {
        $status = true;
        $error = null;
        $arrayItems = json_decode($request->get('arrayItems', null));
        $typeDocument = $request->get('typeDocument', null);
        $newOwnerItems = $request->get('newOwnerItems', null);
        $oldOwnerItems = $request->get('oldOwner', null);
        $documentFile = $request->file('documentFile', null);
        $lastId = null;
        try {
            DB::transaction(function () use ($arrayItems, $typeDocument, $newOwnerItems, $oldOwnerItems, $lastId, $documentFile) {
                if ($typeDocument == 1 || $typeDocument == 2 || $typeDocument == 3) {
                    $newDocument = (new Document())->fill([
                        'type' => $typeDocument,
                        'from_employee' => $oldOwnerItems,
                        'to_employee' => $newOwnerItems,
                        'create_time' => date("Y-m-d H:i:s"),

                    ]);
                    $newDocument->save();
                    $lastId = $newDocument->id;
                    // если перенос или списание
                    if ($typeDocument == 2 || $typeDocument == 3) {
                        foreach ($arrayItems as $arrayItem) {
                            //проверка группа это или отдельный элемент
                            if (array_key_exists('id', $arrayItem)) {
                                (new DocumentItems())->fill([
                                    'document_id' => $lastId,
                                    'item_id' => $arrayItem->id
                                ])->save();
                            } else {
                                (new DocumentItems())->fill([
                                    'document_id' => $lastId,
                                    'group_id' => $arrayItem->idGroup
                                ])->save();
                            }
                        }
                    } else {
                        foreach ($arrayItems as $arrayItem) {
                            $newItem = (new Item())->fill([
                                'name' => $arrayItem->name,
                                'name_1c' => $arrayItem->name1c,
                                'inv_number' => str_replace(' ', '', $arrayItem->inv_number),
                                'serial_number' => $arrayItem->ser_number,
                                'price' => $arrayItem->price,
                                'count' => $arrayItem->col,
                                'create_time' => date("Y-m-d H:i:s"),
                                'last_owner' => $arrayItem->owner,
                                'last_location' => $arrayItem->location,
                                'status' => 1,
                                'year' => $arrayItem->year
                            ]);
                            $newItem->save();
                            (new DocumentItems())->fill([
                                'document_id' => $lastId,
                                'item_id' => $newItem->id
                            ])->save();
                        }
                    }
                    if (!is_null($documentFile)) {
                        $type = $documentFile->getMimeType();
                        if (($type === "image/jpeg") || ($type === "image/png") || ($type === "image/jpg") || ($type === "application/pdf") && $lastId != null) {
                            $path = Storage::putFile('public/documents', $documentFile);
                            $urlDocument = Storage::url($path);
                            try {
                                (new DocumentFile())->fill([
                                    'document_id' => $lastId,
                                    'file_link' => $urlDocument,
                                    'create_time' => date("Y-m-d H:i:s")
                                ])->save();
                                Document::query()->find("$lastId")->fill(['status' => DOC_STATUS_WAIT])->update();
                            } catch (\Exception $e) {
                                $status = false;
                                $error = 'ошибка при прикреплении файла, попробуйте сделать это на вкладке История документов или обратитесь к администратору';
                                Storage::delete($path);
                            } catch (\PDOException $e) {
                                $status = false;
                                $error = 'ошибка при прикреплении файла, попробуйте сделать это на вкладке История документов или обратитесь к администратору';
                                Storage::delete($path);
                            }
                        }
                    }
                } else {
                    $status = false;
                }
            });
        } catch (\Exception $e) {
            $status = false;
        } catch (\PDOException $e) {
            $status = false;
        }
        return ['status' => $status, 'array' => $arrayItems, 'lastid' => $lastId];
    }

    public function saveReasons(Request $request) {
        $reasons = $request->get('reasonsArray', null);
        $idDoc = $request->get('idDoc', null);
        $status = Document::query()->find($idDoc)->update(['reasons' => json_encode($reasons)]);
        return (['status' => $status]);
    }

    public function printDoc(Request $request)
    {
        $idDoc = $request->get('idDoc');
        $items = [];
        $document = Document::query()->where('id', '=', "$idDoc")->get();
        $reasons = json_decode($document->get(0)->reasons);

        foreach ($reasons as $reason) {
            $item = Item::query()->where('id', '=', "$reason->id")->get();
            $item['conclusion'] = $reason->conclusion;
            $item['condition'] = $reason->condition;
            array_push($items, $item);
        }
       $view = \View::make('pdf.cancelDocument',['items' => $items]);
       $html_content = $view->render();

        $pdf = new TCPDF('P', 'mm', 'A4', true, "UTF-8", false);

        $pdf->SetCreator("Журнал ИАТ");
        $pdf->SetTitle('Документ');
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->SetMargins(15, 10, 10);
        $pdf->SetDisplayMode('fullpage');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->SetFont('times', '', 12);

        $pdf->AddPage("P", "A4");
        $pdf->writeHTML($html_content, true, false, true, false, '');
        $pdf->Output('sample.pfd');
    }

    public function getDocuments(Request $request)
    {
        $number = $request->get('number', null);
        $type = $request->get('type', null);
        $status = $request->get('status', null);
        $fromOwner = $request->get('fromOwner', null);
        $toOwner = $request->get('toOwner', null);

        $query = (new Document())::query();

        if ($number != null) {
            $query = $query->where('id', 'like', "%$number%");
        }
        if ($type != null) {
            $query = $query->where('type', '=', "$type");
        }
        if ($status != null) {
            $query = $query->where('status', '=', "$status");
        }
        if ($fromOwner != null) {
            $query = $query->where('from_employee', '=', "$fromOwner");
        }
        if ($toOwner != null) {
            $query = $query->where('to_employee', '=', "$toOwner");
        }
        $query = $query->orderBy('status');
        $query = $query->orderByDesc('create_time');

        $owners = (new Owner())::all();
        $pag = $query->paginate(15);
        return (['status' => true, 'documents' => $pag, 'owners' => $owners]);
    }

    public function showMore(Request $request)
    {
        $id = $request->get('id', null);
        $path = null;

        $items = (new DocumentItems())->newQuery();
        $groups = (new DocumentItems())->newQuery();

        $owners = Document::query()->where('id', '=', "$id")->select('from_employee', 'to_employee')->first();

        $fromOwner = Owner::query()->where('owner_id', '=', $owners->from_employee)->first();
        if ($fromOwner != null) {
            $fromOwner = $fromOwner->last_name . ' ' . $fromOwner->first_name . ' ' . $fromOwner->father_name;
        }
        $toOwner = Owner::query()->where('owner_id', '=', $owners->to_employee)->first();
        if ($toOwner != null) {
            $toOwner = $toOwner->last_name . ' ' . $toOwner->first_name . ' ' . $toOwner->father_name;
        }
        $owners = [
            'fromOwner' => $fromOwner,
            'toOwner' => $toOwner,
        ];

        $items = $items->join('items', 'items.id', '=', 'document_items.item_id')
            ->join('owners', 'owners.owner_id', '=', 'items.last_owner')
            ->join('locations', 'locations.loc_id', '=', 'items.last_location')
            ->where('document_id', '=', "$id");

        $groups = $groups->join('item_groups', 'item_groups.group_id', '=', 'document_items.group_id')
            ->join('owners', 'owners.owner_id', '=', 'item_groups.owner_group')
            ->join('locations', 'locations.loc_id', '=', 'item_groups.location_group')
            ->where('document_id', '=', "$id");

        $document = DocumentFile::query()->where('document_id', '=', "$id")->select('file_link')->first();
        if ($document !== null) {
            $path = $document->file_link;
            $path = asset("$path");
        }
        return (['status' => true, 'items' => $items->get(), 'groups' => $groups->get(), 'owners' => $owners, 'documentLink' => $path]);
    }

    public function accept(Request $request)
    {
        $id = $request->get('id', null);
        $status = true;
        $documentStatus = Document::query()->where('id', '=', "$id")->get(['status']);
        if ($id != null && $documentStatus->get(0)->status == 2) {
            try {
                DB::transaction(function () use ($id, $status) {
                    $owners = Document::query()->where('id', '=', "$id")->get(['to_employee', 'from_employee']);
                    $items = DocumentItems::query()->where('document_id', '=', "$id")->get(['item_id', 'group_id']);
                    foreach ($items as $item) {
                        $changeLog = [
                            'newOwner' => $owners->get(0)->to_employee,
                            'oldOwner' => $owners->get(0)->from_employee,
                            'inGroup' => null
                        ];
                        if ($item->item_id != null) {
                            $request = Item::query()->find("$item->item_id")->fill(['last_owner' => $owners->get(0)->to_employee])->update();
                            (new HistoryChanges)->fill([
                                'id_item' => $item->item_id,
                                'change_log' => json_encode($changeLog),
                                'create_time' => date("Y-m-d H:i:s"),
                                'creator' => Auth::user()->id,
                                'type_change' => CHANGE_TYPE_CHANGE_OWNER
                            ])->save();
                        } else {
                            $request = ItemGroups::query()->find("$item->group_id")->fill(['owner_group' => $owners->get(0)->to_employee])->update();
                            $changeLog = [
                                'newOwner' => $owners->get(0)->to_employee,
                                'oldOwner' => $owners->get(0)->from_employee,
                            ];
                            (new HistoryChanges)->fill([
                                'change_log' => json_encode($changeLog),
                                'create_time' => date("Y-m-d H:i:s"),
                                'creator' => Auth::user()->id,
                                'type_change' => CHANGE_TYPE_CHANGE_OWNER,
                                'id_group' => $item->group_id
                            ])->save();
                        }
                        if (!$request) {
                            $status = false;
                        }
                    }
                    Document::query()->find($id)->fill(['status' => DOC_STATUS_ACCEPT])->update();
                });
            } catch (\PDOException $e) {
                $status = false;
            } catch (\Exception $e) {
                $status = false;
            }
        } else {
            $status = false;
        }
        return (['status' => $status]);
    }

    public function reject(Request $request)
    {
        $id = $request->get('id', null);
        $status = true;
        $statusDocument = Document::query()->where('id', '=', "$id")->get(['status']);

        if ($id != null && ($statusDocument->get(0)->status == DOC_STATUS_WAIT || $statusDocument->get(0)->status == DOC_STATUS_NONE_DOCUMENT)) {
            $query = Document::query()->find("$id")->fill(['status' => DOC_STATUS_REJECT])->update();
            if (!$query) $status = false;
        } else $status = false;

        return (['status' => $status]);
    }

    public function saveDocumentFile(Request $request)
    {
        $status = true;
        $file = $request->file('file');
        $id = $request->get('id', null);
        $error = '';
        $statusDocument = Document::query()->where('id', '=', "$id")->get(['status']);

        if ($statusDocument->get(0)->status == DOC_STATUS_REJECT || $statusDocument->get(0)->status == DOC_STATUS_ACCEPT) {
            $status = false;
            $error = 'Обратитесь к системному администратору';
        } else {
            $type = $file->getMimeType();
            if (($type === "image/jpeg") || ($type === "image/png") || ($type === "image/jpg") || ($type === "application/pdf") && $id != null) {
                $path = Storage::putFile('public/documents', $file);
                $urlDocument = Storage::url($path);
                try {
                    DB::transaction(function () use ($id, $urlDocument) {
                        (new DocumentFile())->fill([
                            'document_id' => $id,
                            'file_link' => $urlDocument,
                            'create_time' => date("Y-m-d H:i:s")
                        ])->save();
                        Document::query()->find("$id")->fill(['status' => DOC_STATUS_WAIT])->update();
                    });
                } catch (\Exception $e) {
                    $status = false;
                    Storage::delete($path);
                } catch (\PDOException $e) {
                    $status = false;
                    Storage::delete($path);
                }
            } else {
                $status = false;
            }
        }
        return (['status' => $status, 'error' => $error]);
    }

    public function create(Request $request) {
        $id = $request->get('id', null);
        $type = Document::query()->where('id', '=', "$id")->get();

        if ((int) $type->get(0)->type === 3) {
            $items = DocumentItems::query()->where('document_id', '=', "$id")->with('item')->with('group')->get();
            return (['status' => true, 'items' => $items,'docId' => $type->get(0)->id]);
        }
        return (['status' => false, 'error' => 'Обратитесь к системному администатору']);
    }
}