@extends('layouts.WelcomeLayout')
@section('search')
    <div class="col col-lg-12">
        <form class="row">
            <div class="col-lg-4">
                <dl>
                    <dt>Номер документа</dt>
                    <dd><input class="form-control documentSearch" type="text" id="numberDocumentSearch"></dd>
                </dl>
            </div>
            <div class="col col-lg-4">
                <dl>
                    <dt>Тип документа</dt>
                    <dd>
                        <select class="form-control documentSearch" id="typeDocumentSearch">
                            <option value="">Не выбрано</option>
                            <option value="1">Добавление</option>
                            <option value="2">Перемещение</option>
                            <option value="3">Списание</option>
                        </select>
                    </dd>
                </dl>
            </div>
            <div class="col col-lg-4">
                <dl>
                    <dt>Статус документа</dt>
                    <dd>
                        <select class="form-control documentSearch" id="statusDocumentSearch">
                            <option value="">Не выбрано</option>
                            <option value="1">Без документа</option>
                            <option value="2">Ожидающие</option>
                            <option value="3">Принятые</option>
                            <option value="4">Отклонённые</option>
                        </select>
                    </dd>
                </dl>
            </div>
            <div class="col-lg-6">
                <dl>
                    <dt>С кого</dt>
                    <dd><select class="form-control documentSearch owners" id="fromOwnerDocumentSearch">
                            <option value="">Не выбрано</option>
                            @foreach($owners as $owner)
                                <option value='{{$owner->owner_id}}'>{{$owner->last_name}} {{$owner->first_name}} {{$owner->father_name}}</option>
                            @endforeach
                        </select>
                    </dd>
                </dl>
            </div>
            <div class="col-lg-6">
                <dl>
                    <dt>На кого</dt>
                    <dd>
                        <select class="form-control documentSearch owners" id="toOwnerDocumentSearch">
                            <option value="">Не выбрано</option>
                            @foreach($owners as $owner)
                                <option value='{{$owner->owner_id}}'>{{$owner->last_name}} {{$owner->first_name}} {{$owner->father_name}}</option>
                            @endforeach
                        </select>
                    </dd>
                </dl>
            </div>
        </form>
    </div>
@endsection
@section('content')
    <div class="col col-lg-12">
        <div class="table-responsive">
            <table class="table table-hover table-striped table-condensed text-center">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Статус</th>
                    <th scope="col">Тип документа</th>
                    <th scope="col">С кого</th>
                    <th scope="col"></th>
                    <th scope="col">На кого</th>
                    <th scope="col">Создан</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody class="table" id="documentsList">
                </tbody>
            </table>
        </div>
        <hr>
        <div id="documents_pages" style="text-align: center" class="btn-group">

        </div>
        <div class="col col-lg-12 text-center" id="loadDocumentSpinner">
            <i class="fa fa-spin fa-refresh" style="font-size: 100px" aria-hidden="true"></i>
        </div>
    </div>
    <!-- модальное окно  подробнее о документе -->
    <div class="modal fade" tabindex="-1" id="moreDetailed" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" id="moveModalBody" role="dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Подробнее</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col col-lg-12 text-center" id="tableElementsDocuments">
                        <div class="col col-lg-12 row">
                            <div class="col-lg-6 text-left"><h4 id="documentNumberMoreModal"></h4></div>
                            <div class="col-lg-6 text-right" id="documentStatusMoreModal"></div>
                        </div>
                        <table class="table table-bordered table-condensed table-hover" style="text-align: left;">
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
                            <tbody id="elementsDocument" style="background-color: #bde0ff">
                            </tbody>
                        </table>
                        <div class="col col-lg-12 row">
                            <div class="col col-lg-6 text-right">
                                <h4 class="text-muted" id="oldOwnerMoreDetailed"></h4>
                            </div>
                            <div class="col col-lg-1 text-center">
                                <i class="fa fa-arrow-right" style="font-size: 25px" aria-hidden="true"></i>
                            </div>
                            <div class="col col-lg-5 left text-left">
                                <h4 class="text-success" id="newOwnerMoreDetailed"></h4>
                            </div>
                        </div>
                        <hr>
                        <div class="col col-lg-12" id="buttonsMoreModal">
                            <div class="col col-lg-6 offset-3 text-center">
                                <button style="margin-left: 25px" class="btn btn-success" onclick="acceptDocument();"
                                        id="acceptDocumentMoreModal">Подтвердить
                                </button>
                                <button class="btn btn-danger" id="rejectDocumentMoreModal" onclick="rejectDocument();">
                                    Отклонить
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 text-center" style="display: none;" id="successMoreModal">
                        <h4 class="alert-success" style=" text-align: center; margin: 25%;">Успешно!!! <i
                                    class="fa fa-check-circle-o" aria-hidden="true"></i></h4>
                    </div>
                    <div class="col col-lg-12 text-center" id="loadingItemsDocument">
                        <i class="fa fa-refresh fa-spin" style="font-size: 80px;"
                           aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Окно сохранения файла -->
    <div class="modal fade" tabindex="-1" role="dialog" id="saveFileDocumentModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Прикрепление файла</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col col-lg-6 offset-3" id="attachNewFile">
                        <form id="formForAttachDocument">
                            <input type="file" name="document" id="documentFileInput">
                        </form>
                    </div>

                    <div class="col col-lg-12 text-center" id="loadAttachFileModal" style="display: none;">
                        <i class="fa fa-refresh fa-spin" style="font-size: 80px;"
                           aria-hidden="true"></i>
                    </div>
                    <div class="col-lg-12 text-center text-center" style="display: none;" id="successAttachFileModal">
                        <h4 class="alert-success" style=" margin: 25% 0 25% 0;">Успешно!!! <i
                                    class="fa fa-check-circle-o" aria-hidden="true"></i></h4>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col col-lg-12 text-center">
                        <button type="button" class="btn btn-primary" onclick="saveDocumentFile();"
                                id="attachDocumentFile">Прикрепить
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{asset('js/documents.js')}}"></script>
@endsection