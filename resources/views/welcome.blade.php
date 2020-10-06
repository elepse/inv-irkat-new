@extends('layouts.WelcomeLayout')
@section('css')
    <link href="{{ asset('css/timeLine.css') }}" rel="stylesheet">
@endsection
@section('search')
    <form action="#" method="post" class="row" id="searchParams">
        <div class="col-lg-4 col-sm-12">
            <dl>
                <dt>Наименование</dt>
                <dd><input type="text" class="form-control" name="search_name" id="search_name"></dd>
                <br>
                <dt>Состояние</dt>
                <dd><select class="form-control" name="search_status" id="search_status">
                        <option value>Все</option>
                        <option value="1">Работает</option>
                        <option value="2">Не работает</option>
                        <option value="3">Утеряно</option>
                        <option value="4">Списано</option>
                    </select>
                </dd>
                <br>
                <dt>Год проверки</dt>
                <dd>
                    <select class="form-control" name="search_year" id="search_year_check">
                        <option value=>Все</option>
                    </select>
                </dd>
            </dl>
        </div>
        <div class="col-lg-4 col-sm-12">
            <dl>
                <dt>Наименование 1С</dt>
                <dd><input class="form-control" type="text" name="search_Name1C" id="search_Name1c"></dd>
                <br>
                <dt>Инвертарный номер</dt>
                <dd><input type="text" class="form-control" name="search_inv_num" id="search_inv_num"></dd>
                <br>
                <dt>Серийный\заводской номер</dt>
                <dd><input type="text" class="form-control" name="search_ser_num" id="search_ser_num"></dd>
                <br>
            </dl>
        </div>
        <div class="col-lg-4 col-sm-12">
            <dt>Владелец</dt>
            <dd><select class="form-control owners" name="search_owner" id="search_owner">
                    <option value>Не выбран</option>
                    @foreach($owners as $owner)
                        <option value='{{$owner->owner_id}}'>{{$owner->last_name}} {{$owner->first_name}} {{$owner->father_name}}</option>
                    @endforeach
                </select>
            </dd>
            <br>
            <dt>Расположение</dt>
            <dd><select class="form-control locations" name="search_loc" id="search_loc">
                    <option value>Не выбран</option>
                    @foreach($locations as $loc)
                        <option value="{{$loc->loc_id}}">{{$loc->loc_name}}</option>
                    @endforeach
                </select>
            </dd>
            <br>
            <dt>Изменения</dt>
            <dd>
                <select class="form-control" name="search_change" id="search_change">
                    <option value>Не сортировать</option>
                </select>
            </dd>
        </div>
        <div class="offset-lg-2 col-lg-4 col-xs-12">
            <dt>Комментарий</dt>
            <dd><input type="text" class="form-control" name="search_com" id="search_com"></dd>
        </div>
        <div class="col-lg-4 col-xs-12">
            <dt>Статус инвентаризация</dt>
            <dd>
                <select class="form-control" name="inv_status" id="inv_status">
                    <option value="">Не выбран</option>
                    <option style="background-color: #d6e9c6;" value="1">Найден</option>
                    <option value="2" style="background-color: #ffcfd3">Не найден</option>
                    <option value="3" style="background-color: #ffe0a4">Найден частично</option>
                </select>
            </dd>
        </div>
    </form>

@endsection
@section('content')
    <div class="col-md-12 col-lg-12 col-xs-12">
        <p style="text-align: center; display: none;">
            <button id="search_button" class="btn btn-info">Найти!</button>
        </p>
        <p style="text-align: center">
            <button class="btn btn-info" id="clean_search">Очистить</button>
        </p>
        <hr>
        <div style="display: none;" class="table-responsive" id="selectTables">
            <h4 class="text-center">Выбранные элементы</h4>
            <table id="selectItemsTable" class="table-sm table-bordered table-condensed table-hover" style="font-size: 15px; text-align: center; width: 100%;">
                <thead style="font-size: 15px; font-weight: bold;">
                <tr>
                    <td class="text-center" colspan="12">Предметы</td>
                </tr>
                <tr>
                    <th></th>
                    <th>Наименование</th>
                    <th>Наименование 1С</th>
                    <th>Инвентарный</th>
                    <th>Серийный</th>
                    <th>Год</th>
                    <th>Расположение</th>
                    <th>Статус</th>
                    <th>На кого</th>
                    <th>Цена</th>
                    <th>Комментарий</th>
                    <th></th>
                </tr>
                </thead>
                <tbody id="selectItems" class="selectItems">
                </tbody>
            </table>
            <table id="selectGroupsTable" class="table-sm table-bordered table-condensed table-hover" style="font-size: 15px; text-align: center; width: 100%;">
                <thead style="font-size: 15px;">
                <tr>
                    <td class="text-center" colspan="6" style="font-weight: bold;">Группы</td>
                </tr>
                    <tr>
                        <th></th>
                        <th>Наименование</th>
                        <th>Наименование 1С</th>
                        <th>Инвентарный номер</th>
                        <th>Расположение</th>
                        <th>На кого</th>
                    </tr>
                </thead>
                <tbody id="selectGroups">
                </tbody>
            </table>
        </div>
        <br>
        <div class="text-center">
            <button data-status="0" onclick="selectTableBtn($(this).data('status'))" style="font-size: 18px;" class="btn btn-success text-center" id="btnSelected">
                Показать выбранные | <i style="font-style: normal" id="countSelected">0</i>
            </button>
        </div>
        <hr>
        <div class="table-responsive" id="tableSide">
            <form action="#" method="post">
                <table class="table table-hover custom-border" id="list_table">
                    <thead style="font-size: 15px;">
                    <tr>
                        <th><input type="checkbox" id="select_all"></th>
                        <th>#</th>
                        <th class="sort" data-sort="name">Наименование</th>
                        <th class="sort" data-sort="name_1c">Наименование 1С</th>
                        <th class="sort" data-sort="inv_number">Инвентарный</th>
                        <th class="sort" data-sort="serial_number">Серийный</th>
                        <th class="sort" data-sort="year">Год</th>
                        <th class="sort" data-sort="loc_name">Располож.</th>
                        <th class="sort" data-sort="status">Статус</th>
                        <th class="sort" data-sort="last_owner">На кого</th>
                        <th class="sort" data-sort="price">Цена</th>
                        <th class="sort" data-sort="last_comment">Комментарий</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="result" class="result table" style="font-size: 15px; text-align: center">
                    </tbody>
                </table>
            </form>
        </div>
        <div id="search_result"></div>
        <div id="search_loading" style="display: none; text-align: center">
            <i class="fa fa-refresh fa-spin fa-3x fa-fw" style="margin:40px 0 40px 0; font-size: 60px; "></i>
        </div>
        <div id="search_pages" style="display: flex; flex-wrap: wrap;" class="">

        </div>
    </div>
    <!-- Модальное окно истории -->
    <div class="modal fade" tabindex="-1" id="historyModal" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">История <i style="color: #007bff;"class="fa fa-history" aria-hidden="true"></i></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="modal-body">
                        <div class="col col-lg-12 text-center"  id="historyLoad">
                            <i class="fa fa-refresh fa-spin fa-3x fa-fw" aria-hidden="true"
                               style="font-size: 80px; margin: 25%;"></i>
                        </div>
                        <div class="container" id="historyItemsContainer">
                        <div class="row">
                            <div class="col-md-12">
                                <div style="display:inline-block;width:100%;overflow-y:auto;">
                                    <ul class="timeline timeline-horizontal" id="historyTimeLine" style="margin-top: 30px;">
                                        <li class="timeline-item historyItem">
                                            <div class="timeline-badge primary"><i
                                                        class="glyphicon glyphicon-check"></i>
                                            </div>
                                            <div class="timeline-panel">
                                                <div class="timeline-heading">
                                                    <h4 class="timeline-title">Mussum ipsum cacilds 1</h4>
                                                    <p>
                                                        <small class="text-muted"><i
                                                                    class="glyphicon glyphicon-time"></i>
                                                            11 hours ago via Twitter
                                                        </small>
                                                    </p>
                                                </div>
                                                <div class="timeline-body">
                                                    <p>Mussum ipsum cacilds, vidis litro abertis. Consetis faiz
                                                        elementum
                                                        girarzis, nisi eros gostis.</p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="timeline-item">
                                            <div class="timeline-badge success"><i
                                                        class="glyphicon glyphicon-check"></i>
                                            </div>
                                            <div class="timeline-panel">
                                                <div class="timeline-heading">
                                                    <h4 class="timeline-title">Mussum ipsum cacilds 2</h4>
                                                    <p>
                                                        <small class="text-muted"><i
                                                                    class="glyphicon glyphicon-time"></i>
                                                            11 hours ago via Twitter
                                                        </small>
                                                    </p>
                                                </div>
                                                <div class="timeline-body">
                                                    <p>Mussum ipsum cacilds, vidis faiz elementum girarzis, nisi eros
                                                        gostis.</p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{asset('js/history.js')}}"></script>
@endsection