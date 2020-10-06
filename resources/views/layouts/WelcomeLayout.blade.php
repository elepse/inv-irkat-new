@php
    /** @var \Illuminate\Pagination\Paginator $items */
    /** @var App\Owner[] $owners */
    /** @var App\Location[] $locations */
@endphp
        <!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Inv-Irkat</title>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"
            integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"
            integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm"
            crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
    <!-- Fonts -->
    <style>@import "http://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css";</style>
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.7.5/js/bootstrap-select.min.js"></script>
    <!-- Styles -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"
          integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
    @yield('css')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="{{route('main')}}">Инвентаризация ИАТ <?php echo date('Y')?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="{{route('main')}}">Главная</a>
            </li>
            <li class="nav-item dropdown active">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                   aria-expanded="false">
                    Документы <i class="fa fa-file-text" aria-hidden="true"></i>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="javascript: void(0)" onclick="openDocumentsHistory()">История
                        Документов <i class="fa fa-history" aria-hidden="true"></i></a>
                    <a class="dropdown-item" href="javascript: void(0)" onclick="addDocuments()">Добавить документ <i
                                class="fa fa-plus-circle" aria-hidden="true"></i></a>
                </div>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="javascript: void(0)" onclick="callCreateGroupModal()">Создание группы <i
                            class="fa fa-plus-circle" aria-hidden="true"></i></a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="javascript: void(0)" onclick="callMoveModal()">Перемещение <i
                            class="fa fa-sign-out" aria-hidden="true"></i></a>
            </li>
        </ul>
        @if(Auth::check())
        <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
                <a class="nav-link">{{Auth::user()->username}} <i class="fa fa-user" aria-hidden="true"></i></a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{route('logOut')}}">Выход <i class="fa fa-sign-out" aria-hidden="true"></i></a>
            </li>
        </ul>
            @endif
    </div>
</nav>
<br>
<div class="container">
    @yield('search')
</div>
@yield('content')
<!-- Модальноеы окно для перемещения -->
<div class="modal fade" tabindex="-1" id="moveModal" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" id="moveModalBody" role="dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Перемещение <i class="fa fa-sign-out" aria-hidden="true"></i></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col col-lg-12" id="bodyForMoveModal">
                    <div class="col col-lg-12 text-center">
                        <h4>Элементы для перемещения</h4>
                        <table class="table table-bordered table-condensed table-hover"
                               style="text-align: left;">
                            <thead>
                            <tr>
                                <th>Наименование</th>
                                <th>Наименование 1С</th>
                                <th>Инвентарный</th>
                                <th>Серийный</th>
                                <th>Располож.</th>
                                <th>Статус</th>
                                <th>На кого</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody id="elementsForMove" style="background-color: #bde0ff">
                            </tbody>
                        </table>
                        <h5 id="noneElementsMoveModal" style="display: none;">Вы не выбрали элементы</h5>
                    </div>
                    <div id="paramForMove" class="col col-lg-12">
                        <form id="formForMove">
                            <hr>
                            <div class="col col-lg-6 offset-lg-3 text-center">
                                <dl>
                                    <dt>Новое рассположение</dt>
                                    <dd>
                                        <div class="input-group mb-3">
                                            <select id="newLocationForMoveItem" class="form-control locations"
                                                    aria-describedby="button-addon2">
                                                <option value="">Не выбрано!</option>
                                                @foreach($locations as $loc)
                                                    <option value="{{$loc->loc_id}}">{{$loc->loc_name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary"
                                                        onclick="callAddLocationModal()" type="button"
                                                        id="button-addon2">
                                                    Добавить
                                                </button>
                                            </div>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                            <div class="col col-lg-10 offset-1 text-center">
                                <dl>
                                    <dt>Обязательный комментарий</dt>
                                    <dd>
                                        <textarea class="form-control" id="commentForMove"></textarea>
                                    </dd>
                                </dl>
                            </div>
                            <div class="alert alert-danger text-center" id="errorMoveComment" style="display: none;"
                                 role="alert">
                                Вы не указали комментарий!
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col col-lg-12 text-center" id="loadForMoveModal" style="display: none;">
                    <i class="fa fa-spin fa-refresh" style="font-size: 80px; margin: 25%;" aria-hidden="true"></i>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="SaveChangeLocation">Сохранить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<!-- Модальное окно для создание группы -->
<div class="modal fade" tabindex="-1" id="createGroup" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="dialog" id="createGroupBody">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Создание группы</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="SaveNewGroup" style="display: none; text-align: center">
                    <i class="fa fa-refresh fa-spin fa-3x fa-fw" style="font-size: 80px; margin: 25%;"></i>
                </div>
                <div class="col col-lg-12 row" id="paramNewGroup">
                    <form class="col col-lg-12 row" id="formCreateNewGroup">
                        <div class="col col-lg-6">
                            <dt>Наименование</dt>
                            <dd><input class="form-control" placeholder="Не заполнено." type="text" id="newGroupName">
                            </dd>
                            <dl>
                                <dt>Владелец</dt>
                                <dd>
                                    <select class="form-control owners" id="newGroupOwner">
                                        <option value="none">Не выбран</option>
                                        @foreach($owners as $owner)
                                            <option value="{{$owner->owner_id}}">{{$owner->last_name}} {{$owner->first_name}} {{$owner->father_name}}</option>
                                        @endforeach
                                    </select>
                                </dd>
                            </dl>
                        </div>
                        <div class="col col-lg-6">
                            <dl>
                                <dt>Наименование 1C</dt>
                                <dd><input class="form-control" type="text" placeholder="Не заполнено."
                                           id="newGroupName1c"></dd>
                                <dt>Расположение</dt>
                                <dd>
                                    <select class="form-control locations" id="newGroupLocation">
                                        <option value="none">Не выбрано</option>
                                        @foreach($locations as $location)
                                            <option value="{{$location->loc_id}}">{{$location->loc_name}}</option>
                                        @endforeach
                                    </select>
                                </dd>
                            </dl>
                        </div>
                        <div class="col offset-lg-1 col-lg-10">
                            <dl>
                                <dt>Инвентарный номер</dt>
                                <dd><input class="form-control" type="text" placeholder="Не заполнено."
                                           id="newGroupInvNumber"></dd>
                            </dl>
                        </div>
                    </form>
                    <div class="col col-lg-12 text-center">
                        <h4>Элементы которые войдут в новую группу</h4>
                        <table class="table table-bordered table-condensed table-hover" id="tableItemsForNewGroup"
                               style="text-align: left;">
                            <thead>
                            <tr>
                                <th>Наименование</th>
                                <th>Наименование 1С</th>
                                <th>Инвентарный</th>
                                <th>Серийный</th>
                                <th>Располож.</th>
                                <th>Статус</th>
                                <th>На кого</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody id="selectItemsForNewGroup" style="background-color: #bde0ff">
                            </tbody>
                        </table>
                        <h4 id="noneSelectItemsForNewGroup" style="display: none; text-align: center;">Вы не выбрали
                            элементы</h4>
                        <div class="alert alert-danger text-center col col-lg-6 offset-lg-3" id="alertCoincidence"
                             role="alert" style="display: none;">
                            Наименования принадлежат разным владельцам!
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="newGroupSave">Сохранить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<!-- Модальное окно для просмотра элементов группы -->
<div class="modal fade" id="showGroupItems" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" id="groupItemsBody" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Просмотр элементов группы</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col col-md-12 text-center loadGroupItems">
                    <i class="fa fa-refresh fa-spin fa-3x fa-fw" aria-hidden="true"
                       style="font-size: 80px; margin: 25%;"></i>
                </div>
                <div class="col col-md-12 groupModalItems row">
                    <div class="col col-md-8 text-left">
                        <h3>Группа: <span style="text-decoration: underline" id="groupName"></span></h3>
                        <h3>Инвентарный номер: <span style="text-decoration: underline" id="groupInvNum"></span></h3>
                        <h3>Расположение: <span style="text-decoration: underline" id="groupLocation"></span></h3>
                        <h3>Владелец: <span style="text-decoration: underline" id="groupOwner"></span></h3>
                    </div>
                    <div class="col col-md-4 text-right" style="padding-top: 20px">
                        <button type="button" class="btn btn-danger" id="disbandGroup">Расформировать группу
                        </button>
                    </div>
                </div>
                <br>
                <div class="col col-md-12 groupModalItems">
                    <h3 class="text-center">Элементы группы</h3>
                    <table class="table table-bordered table-condensed table-hover groupItems tieGroupItems">
                        <thead>
                        <tr>
                            <th>Наименование</th>
                            <th>Наименование 1С</th>
                            <th>Инвентарный</th>
                            <th>Серийный</th>
                            <th>Располож.</th>
                            <th>Статус</th>
                            <th>На кого</th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody class="table" id="groupItemsInfo">
                        </tbody>
                    </table>
                    <br>
                    <h3 style="display: none" class="untieGroupItems text-center">Откреплённые элементы</h3>
                    <table class="table table-bordered table-condensed table-hover untieGroupItems groupItems"
                           style="display: none">
                        <thead>
                        <tr>
                            <th>Наименование</th>
                            <th>Наименование 1С</th>
                            <th>Инвентарный</th>
                            <th>Серийный</th>
                            <th>Располож.</th>
                            <th>Статус</th>
                            <th>На кого</th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody class="tbodyUntie">

                        </tbody>
                    </table>
                </div>
                <div class="col col-md-12 text-center groupModalItems">
                    <button type="button" class="btn btn-primary" id="selectGroup">Выбрать группу</button>
                    <button type="button" class="btn btn-danger" id="unselectGroup" style="display: none;">Отменить
                        выбор
                    </button>
                    <h4 style="color: green; display: none;" id="selectGroupSuccessful">Выбрано!</h4>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<!-- Модальное окно для добавления документа-->
<div class="modal fade" id="addDocModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" id="ModalAddDoc">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Добавление документа</h4>
                <button type="button" class="close CloseAddDocModal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="SaveNewDocument" style="display: none; text-align: center">
                    <i class="fa fa-refresh fa-spin fa-3x fa-fw" style="font-size: 80px; margin: 25%;"></i>
                </div>
                <div class="container" id="paramsNewDocument">
                    <div class="row col container" id="marginDocument">
                        <div class="col col-md-6 offset-md-3">
                            <dt>Тип документа</dt>
                            <dd><select class="form-control documentProperty" id="SelectTypeDocument">
                                    <option class="defOptionTypeDoc" value="none">Выберите типа документа</option>
                                    <option value="1">Добавление</option>
                                    <option value="2">Перемещение</option>
                                    <option value="3">Cписание</option>
                                </select>
                            </dd>
                        </div>
                        <div class="col col-md-12 row" id="SelectItemsModalPage">
                            <!-- Добавление нового наименования -->
                            <div class="col col-md-12 text-center" id="addItemsDocument" style="display: none">
                                <br>
                                <button type="button" class="btn btn-danger" id="clearNewItem"
                                        onclick="clearNewItem();">
                                    Очистить!
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
{{--                                <div>--}}
{{--                                    <br>--}}
{{--                                    <input id="inGroup" style="width: 18px; height: 18px;" name="inGroup" type="checkbox">--}}
{{--                                    <label style="font-size: 20px" for="inGroup">Добавить в группу</label>--}}
{{--                                </div>--}}
                                <div class="col col-md-12" id="groupAddItems">
                                    <div class="col col-md-12 borderAddItem addedItem" data-addedid="1">
                                        <form class="formNewItem">
                                            <div class="col col-md-12 cutParamNewItem"
                                                 style="padding: 15px 0 0 0; display: none">
                                                <div class="input-group-sm input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Имя:</span>
                                                    </div>
                                                    <input class="form-control cutNameNewItem col-md-6"
                                                           aria-describedby="inputGroup-sizin"
                                                           type="text" disabled="">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Инвентарный:</span>
                                                    </div>
                                                    <input class="form-control cutInvNumberNewItem col-md-3"
                                                           aria-describedby="inputGroup-sizin" disabled=""
                                                           type="text">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Цена:</span>
                                                    </div>
                                                    <input class="form-control col-md-2 cutPriceNewItem"
                                                           aria-describedby="inputGroup-sizin" type="number"
                                                           disabled min="0" step="100">
                                                    &nbsp;
                                                    <i class="fa fa-pencil editNewItem" aria-hidden="true"
                                                       onclick="editNewItem($(this).parents('.borderAddItem').data('addedid'))"
                                                       style="color: green; cursor: pointer; font-size: 18px; margin-top: 5px"></i>
                                                    &nbsp;
                                                    &nbsp;
                                                    <i class="fa fa-times deleteNewItem"
                                                       onclick="deleteNewItem($(this).parents('.borderAddItem').data('addedid'))"
                                                       aria-hidden="true"
                                                       style="color: red; cursor: pointer;margin-top: 5px; font-size: 18px;"></i>
                                                </div>
                                            </div>
                                            <div class="col col-md-12 allParamNewItem">
                                                <div class="input-group-sm input-group mb-3"
                                                     style="padding: 15px 0 0 0">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Кол. :</span>
                                                    </div>
                                                    <input class="form-control col-md-1 colNewItem"
                                                           aria-describedby="inputGroup-sizin"
                                                           type="number" value="1" min="1" step="1">
                                                    &nbsp
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Наименование:</span>
                                                    </div>
                                                    <input class="form-control nameNewItem"
                                                           aria-describedby="inputGroup-sizin"
                                                           type="text">
                                                    &nbsp
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Наименование 1c:</span>
                                                    </div>
                                                    <input class="form-control name1cNewItem"
                                                           aria-describedby="inputGroup-sizin"
                                                           type="text">
                                                </div>
                                                <div class="input-group-sm input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Ивентарный номер:</span>
                                                    </div>
                                                    <input class="form-control invNumberNewItem"
                                                           aria-describedby="inputGroup-sizin"
                                                           type="text">
                                                    &nbsp
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Серийный номер:</span>
                                                    </div>
                                                    <input class="form-control serNumberNewItem"
                                                           aria-describedby="inputGroup-sizin"
                                                           type="text">
                                                    &nbsp
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Год:</span>
                                                    </div>
                                                    <select class="form-control col-md-2 yearNewItem"
                                                           aria-describedby="inputGroup-sizin"
                                                            type="text">
                                                        @for($i = 0; $i <= 30; $i++)
                                                            <option value="{{$year = date('Y') - $i}}">{{$year = date('Y') - $i}}</option>
                                                            @endfor
                                                    </select>
                                                </div>
                                                <div class="input-group-sm input-group mb-3"
                                                     style="padding: 0 0 10px 0;">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Расположение:</span>
                                                    </div>
                                                    <select class="form-control col-md-2 locationNewItem locations">
                                                        <option value="">Не выбрано</option>
                                                        @foreach($locations as $loc)
                                                            <option value="{{$loc->loc_id}}">{{$loc->loc_name}}</option>
                                                        @endforeach
                                                    </select>
                                                    &nbsp
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Владелец:</span>
                                                    </div>
                                                    <select class="form-control ownerNewItem">
                                                        <option value="">Не выбрано</option>
                                                        @foreach($owners as $owner)
                                                            <option value='{{$owner->owner_id}}'>{{$owner->last_name}} {{$owner->first_name}} {{$owner->father_name}}</option>
                                                        @endforeach
                                                    </select>
                                                    &nbsp
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Цена:</span>
                                                    </div>
                                                    <input class="form-control col-md-2 priceNewItem"
                                                           aria-describedby="inputGroup-sizin" type="number" min="0"
                                                           step="100">
                                                </div>
                                                <div class="col col-md-12 text-right" style="padding-right: 0">
                                                    <button type="button" class="btn btn-primary fastenNewItem"
                                                            onclick="fastenNewItem($(this).parents('.borderAddItem').data('addedid'))"
                                                            style="margin-bottom: 10px;">
                                                        Закрепить <i class="fa fa-thumb-tack"
                                                                     aria-hidden="true"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger deleteNewItem"
                                                            onclick="deleteNewItem($(this).parents('.borderAddItem').data('addedid'))"
                                                            style=" margin: 0 4px 10px 0;">
                                                        Удалить <i class="fa fa-times deleteNewItem"
                                                                   aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                                <br>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- Изменение владельца -->
                                </div>
                                <br>

                                <button type="button" class="btn btn-success" id="addNewItemDocument">
                                    Добавить наименование <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                </button>
                                <br>
                            </div>
                            <table class="table table-bordered table-condensed DocumentInfo">
                                <thead>
                                <tr>
                                    <th>Наименование</th>
                                    <th>Наименование 1С</th>
                                    <th>Инвентарный</th>
                                    <th>Серийный</th>
                                    <th>Располож.</th>
                                    <th>Статус</th>
                                    <th>На кого</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody class="table" id="SelectItemsModal">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <h4 id="NonItems" class="text-center DocumentInfo"></h4>
                    <div class="col col-md-12 text-center" style="display: none;" id="deleteChangesDocument">
                    </div>
                    <div class="col-lg-12" id="alertMatchs" style="display: none;">
                        <div class="alert text-center col-lg-6 offset-lg-3 alert-danger" role="alert" id="matchs"
                             style="display: none;">
                            Наименования принадлежат разным владельцам!
                        </div>
                    </div>
                    <div class="col col-md-6 offset-md-3 text-center" id="movementItems"
                         style="display:none; min-width:50% ">
                        <dl>
                            <dt>Новый владелец</dt>
                            <dd>
                                <select class="form-control form owners" id="newOwnerItems">
                                    <option class="defOptionTypeDoc" value="">Не выбрано</option>
                                    @foreach($owners as $owner)

                                        <option value='{{$owner->owner_id}}'>{{$owner->last_name}} {{$owner->first_name}} {{$owner->father_name}}</option>

                                    @endforeach
                                </select>
                            </dd>
                        </dl>
                    </div>
                    <hr>
                    <div class="col col-md-12 row">
                        <div class="col col-md-5 text-left">
                            <h5>Прикрепление документа</h5>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="documentFile" lang="ru">
                                <label class="custom-file-label" id="documentFileLabel" for="documentFile">Выберите
                                    файл</label>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <hr>
                <div style="display: flex;">
                    <div class="text-left">
                        <button type="button" class="btn btn-info" href="javascript: void(0)"
                                onclick="callAddNewOwnerModal()">Добавить владельца
                        </button>
                        <button type="button" class="btn btn-info" href="javascript: void(0)"
                                onclick="callAddLocationModal()">Добавить расположение
                        </button>
                    </div>
                    <div class="text-right ml-auto">
                        <button type="button" class="btn btn-primary" id="DocSave">Сохранить</button>
                        <button type="button" class="btn btn-secondary CloseAddDocModal" data-dismiss="modal">Закрыть
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Модальное окно для редактироваия -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelTitleId">Редактирование</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalHide">
                <div id="EditItemLoad" style="display: none; text-align: center">
                    <i class="fa fa-refresh fa-spin fa-3x fa-fw" style="font-size: 80px; margin: 25%;"></i>
                </div>
                <h1 class="alert-success" id="successNotification"
                    style="display: none; text-align: center; margin: 25%;">Успешно! <span><i
                                class="fa fa-check-circle-o" aria-hidden="true"></i></span></h1>
                <form action="Edit_Item" method="get">
                    <div class="container" id="itemResult">
                        <input class="sr-only" id="idEditItem">
                        <dt>Наименование</dt>
                        <dd><input class="form-control EditItem" name="EditName1C" id="EditName"></dd>
                        <dt>Наименование 1С</dt>
                        <dd><input class="form-control EditItem" name="EditName1C" id="EditName1C"></dd>
                        <div class="container row">
                            <div class="col col-md-6">
                                <dl>
                                    <dt>Инвентарный номер</dt>
                                    <dd><input class="form-control EditItem" type="text" name="EditInv" id="EditInv">
                                    </dd>
                                    <dt>Расположение</dt>
                                    <dd><select class="form-control EditItem" disabled type="text" name="EditLoc"
                                                id="EditLoc">
                                            @foreach($locations as $loc)
                                                <option value="{{$loc->loc_id}}">{{$loc->loc_name}}</option>
                                            @endforeach
                                        </select>
                                    </dd>
                                    <dt>Год</dt>
                                    <dd><input class="form-control EditItem" type="number" min="1990" step="1"
                                               name="EditYear" id="EditYear" disabled>
                                    </dd>
                                    <dt>Количество</dt>
                                    <dd><input class="form-control EditItem" type="number" min="1" name="EditCount"
                                               id="EditCount"></dd>
                                </dl>
                            </div>
                            <div class="col col-md-6">
                                <dl>
                                    <dt>Серийный номер</dt>
                                    <dd><input class="form-control EditItem" type="text" name="EditSer" id="EditSer">
                                    </dd>
                                    <dt>Владелец</dt>
                                    <dd><select class="form-control EditItem" type="text" name="EditOwner"
                                                id="EditOwner"
                                                disabled>
                                            @foreach($owners as $owner)

                                                <option value='{{$owner->owner_id}}'>{{$owner->last_name}} {{$owner->first_name}} {{$owner->father_name}}</option>

                                            @endforeach
                                        </select></dd>
                                    <dt>Статус</dt>
                                    <dd><select class="form-control EditItem" type="text" name="EditStatus"
                                                id="EditStatus">
                                            <option value="1">Работает</option>
                                            <option value="2">Не работает</option>
                                            <option value="3">Утеряно</option>
                                            <option value="4">Списано</option>
                                        </select></dd>
                                    <dt>Цена</dt>
                                    <dd><input class="form-control EditItem" type="number" name="EditPrice"
                                               id="EditPrice">
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-lg-6 col-xs-12 offset-lg-3">
                                <dl>
                                    <dt>Статус инвентаризации</dt>
                                    <dd>
                                        <select class="form-control EditItem" id="EditInvStatus">
                                            <option value="1" style="background-color: #d6e9c6;">Найден</option>
                                            <option value="2" style="background-color: #ffcfd3">Не найден</option>
                                            <option value="3" style="background-color: #ffe0a4">Найден частично</option>
                                        </select>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <dt>Коментарий</dt>
                        <dd><textarea class="form-control EditItem" name="EditCom" maxlength="255"
                                      id="EditCom"></textarea></dd>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="EditSave">Сохранить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<!-- Окно для добавления нового владельца -->
<div class="modal fade" style="margin-top: 50px;" tabindex="-1" role="dialog" id="addNewOwnerModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавление владельца</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col col-lg-12" id="paramNewAddOwner">
                    <form id="formAddNewOwner">
                        <dl>
                            <dt>Имя<sup style="color: red">*</sup></dt>
                            <dd><input type="text" class="form-control" id="firstNameNewOwner"></dd>
                            <dt>Фамилия<sup style="color: red">*</sup></dt>
                            <dd><input type="text" class="form-control" id="lastNameNewOwner"></dd>
                            <dt>Отчество</dt>
                            <dd><input type="text" class="form-control" id="fatherNameNewOwner"></dd>
                        </dl>
                    </form>
                </div>
                <div class="col col-lg-12 text-center">
                    <button class="btn btn-primary" onclick="addNewOwner()">Добавить</button>
                </div>
                <div class="col col-lg-12 text-center" style="display: none;" id="loadingAddNewOwnerModal">
                    <i class="fa fa-refresh fa-spin" style="font-size: 40px;"
                       aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Обрезанная модалка для перемещения в группе -->
<div class="modal fade" tabindex="-1" role="dialog" id="moveModalForElementInGroup" style="margin-top: 50px;">
    <div class="modal-dialog" role="dialog">
        <div class="modal-content" style="box-shadow: 0 0 10px rgba(0,0,0,0.5);">
            <div class="modal-header">
                <h5 class="modal-title">Перемещение <i class="fa fa-sign-out" aria-hidden="true"></i></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col col-lg-12" id="bodyModalMoveInGroup">
                    <h5>Наименование: <span style="text-decoration: underline;" id="nameMoveItemInGroup"></span></h5>
                    <div class="col-lg-12">
                        <label>Расположение</label>
                        <div class="input-group mb-3">
                            <select id="locationForMoveElementInGroup" class="form-control locations"
                                    aria-describedby="button-addon2">
                                <option value="">Не выбрано!</option>
                                @foreach($locations as $loc)
                                    <option value="{{$loc->loc_id}}">{{$loc->loc_name}}</option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" onclick="callAddLocationModal();"
                                        type="button" id="button-addon2">
                                    Добавить
                                </button>
                            </div>
                        </div>
                        <label>Комментарий</label>
                        <textarea class="form-control" id="commentForMoveElementInGroup"></textarea>
                    </div>
                    <br>
                    <div class="alert alert-danger text-center" role="alert" style="display: none;"
                         id="errorMoveModalInGroup"></div>
                    <hr>
                    <div class="col col-lg-12 text-center">
                        <button class="btn btn-primary" onclick="saveNewLocationItemInGroup()">Сохранить</button>
                    </div>
                </div>
                <div class="alert alert-success text-center" id="successMoveInGroup">
                    Успешно!
                </div>
                <div class="col col-lg-12 text-center" style="display: none;" id="loadingModalMoveInGroup">
                    <i class="fa fa-refresh fa-spin" style="font-size: 40px;"
                       aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Модальное окно для добавление нового расположения -->
<div class="modal fade" style="margin-top: 50px;" tabindex="-1" role="dialog" id="addNewLocationModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавление расположения</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col col-lg-12" id="paramAddNewLocation">
                    <form id="formAddNewLocation">
                        <dl>
                            <dt>Название расположения <sup style="color: red;">*</sup></dt>
                            <dd><input type="text" class="form-control" id="nameAddNewLocation"></dd>
                        </dl>
                    </form>
                </div>
                <div class="col col-lg-12 text-center">
                    <button class="btn btn-primary" onclick="addNewLocation()">Добавить</button>
                </div>
                <div class="col col-lg-12 text-center" style="display: none;" id="loadingAddNewLocationModal">
                    <i class="fa fa-refresh fa-spin" style="font-size: 40px;"
                       aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- печать документа -->
<div class="modal fade lg" style="margin-top: 50px;" tabindex="-1" role="dialog" id="printDocModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Печать документа</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input class="readonly" id="idDocumentReasons" style="display: none;">
                <div class="col col-lg-12 col-xs-12 col-md-12" id="itemsDocument">

                </div>
                <div class="col col-lg-12 text-center" id="printLoad" style="display: none;">
                        <i class="fa fa-refresh fa-spin fa-3x fa-fw" style="font-size: 80px; margin: 25%;"></i>
                </div>
            </div>
            <div class="alert alert-danger col col-lg-10 offset-lg-1 text-center" id="alertPrint" style="display: none;">
                Заполните все поля формы.
            </div>
            <div class="modal-footer">
                <button class="btn btn-info" onclick="saveReasons();">Сформировать документ</button>
            </div>
        </div>
    </div>
</div>
<script>
    window.csrf = '{{csrf_token()}}';
    urlDocs = '{{route('DocMain')}}';
    urlMain = "{{route('main')}}";
</script>
<script src="{{asset('js/button.js')}}"></script>
<script>
    var urlSearch = "{{route('search')}}",
        urlDocSave = "{{route('DocSave')}}";
</script>
<script src="{{asset('js/search.js')}}"></script>
<script src="{{asset('js/groupModal.js')}}"></script>
<script src="{{asset('js/AddDocument.js')}}"></script>
<script src="{{asset('js/EditItem.js')}}"></script>
<script src="{{asset('js/createGroup.js')}}"></script>
</body>
</html>