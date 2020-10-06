//констатнты типов документов
var DOC_TYPE_ADD = 1,
    DOC_TYPE_MOVE = 2,
    DOC_TYPE_OFFS = 3;
// константы статуса документа
var DOC_STATUS_NONE_DOCUMENT = 1,
    DOC_STATUS_WAIT = 2,
    DOC_STATUS_ACCEPT = 3,
    DOC_STATUS_REJECT = 4;

$(function () {
    getDocuments(1);
    $(document).on('click', '#documents_pages .page', function () {
        var page = $(this).data('page');
        getDocuments(page);
    });
});

$('.documentSearch').on('change', function f() {
    getDocuments(1);
});

function getDocuments(page) {
    var pages = $('#documents_pages');
    pages.hide();
    $('#loadDocumentSpinner').show();
    var number = $('#numberDocumentSearch').val(),
        type = $('#typeDocumentSearch').val(),
        status = $('#statusDocumentSearch').val(),
        fromOwner = $('#fromOwnerDocumentSearch').val(),
        toOwner = $('#toOwnerDocumentSearch').val();
    $('#documentsList').empty();
    $.ajax({
        url: "/documents/getDocuments",
        data: {
            number: number,
            type: type,
            status: status,
            fromOwner: fromOwner,
            toOwner: toOwner,
            page: page
        },
        dataType: 'json',
        type: "GET"
    }).done(function (response) {
        if (response !== undefined) {
            if (response.status === true) {
                var icon, text;
                response.documents.data.forEach(function (i) {
                    var document = $('<tr></tr>');
                    //получаем фио владельца
                    var fromOwner = response.owners.find(function (j) {
                        return j.owner_id === i.from_employee;
                    });
                    var toOwner = response.owners.find(function (j) {
                        return j.owner_id === i.to_employee;
                    });
                    if (fromOwner) {
                        i.fullnameFromOwner = fromOwner.last_name + ' ' + fromOwner.first_name + ' ' + fromOwner.father_name;
                    }
                    if (toOwner) {
                        i.fullnameToOwner = toOwner.last_name + ' ' + toOwner.first_name + ' ' + toOwner.father_name;
                    }
                    switch (i.status) {
                        case DOC_STATUS_NONE_DOCUMENT:
                            document.css('background-color', '#ffcfd3');
                            text = 'Нет документа ';
                            icon = $('<i class="fa fa-file" style="color: #de000b" aria-hidden="true"></i>');
                            break;
                        case DOC_STATUS_WAIT:
                            document.css('background-color', '#cbc0ff');
                            text = 'Ожидает ';
                            icon = $('<i class="fa fa-clock-o" style="color: #1b00ff" aria-hidden="true"></i>');
                            break;
                        case DOC_STATUS_ACCEPT:
                            document.css('background-color', '#d6e9c6');
                            text = 'Принят ';
                            icon = $('<i class="fa fa-check" style="color: #1e8d19;" aria-hidden="true"></i>');
                            break;
                        case DOC_STATUS_REJECT:
                            document.css('background-color', '#c2c2c2');
                            document.css('opacity', '0.9');
                            text = 'Отклонён ';
                            icon = $('<i class="fa fa-undo" aria-hidden="true"></i>');
                            break;
                    }
                    switch (i.type) {
                        case DOC_TYPE_ADD :
                            i.type = 'Добавление';
                            i.typeNumber = DOC_TYPE_ADD;
                            break;
                        case DOC_TYPE_MOVE:
                            i.type = 'Перемещение';
                            i.typeNumber = DOC_TYPE_MOVE;
                            break;
                        case DOC_TYPE_OFFS:
                            i.type = 'Списание';
                            i.typeNumber = DOC_TYPE_OFFS;
                            break;
                    }
                    document.append($('<td style="vertical-align: middle"></td>').append(i.id))
                        .append($('<td style="vertical-align: middle"></td>').append(text).append(icon))
                        .append($('<td style="vertical-align: middle"></td>').append(i.type))
                        .append($('<td style="vertical-align: middle"></td>').append(i.fullnameFromOwner))
                        .append($('<td style="vertical-align: middle"></td>').append($('<i class="fa fa-arrow-right" aria-hidden="true"></i>')))
                        .append($('<td style="vertical-align: middle"></td>').append(i.fullnameToOwner))
                        .append($('<td style="vertical-align: middle"></td>').append(i.create_time))
                        .append($('<td style="vertical-align: middle"></td>')
                            .append($('<i class="fa fa-print" title="Печать" onclick="printDoc(' + i.typeNumber + ', ' + i.id + ')" style="font-size: 22px; cursor: pointer" aria-hidden="true"></i>'))
                            .append($('<i class="fa fa-info moreDetailedIco" title="Подробнее" onclick="moreDetailedModal(' + i.id + ',' + i.status + ')" style="font-size: 22px; color: #3542df; margin: 15px; cursor: pointer" aria-hidden="true"></i>'))
                            .append($('<i style="font-size: 22px; color: #2a910a;cursor: pointer" class="fa fa-paperclip saveFileIco" onclick="callSaveFileDocumentModal(' + i.id + ');" aria-hidden="true"></i>')));
                    $('#documentsList').append(document);
                    var printDocumentIco = $('.printDocumentIco:last'),
                        moreDetailedIco = $('.moreDetailedIco:last'),
                        saveFileIco = $('.saveFileIco:last');
                    switch (i.status) {
                        case DOC_STATUS_NONE_DOCUMENT:
                            break;
                        case DOC_STATUS_WAIT:
                            break;
                        case DOC_STATUS_ACCEPT:
                            saveFileIco.hide();
                            printDocumentIco.hide();
                            break;
                        case DOC_STATUS_REJECT:
                            saveFileIco.hide();
                            printDocumentIco.hide();
                            break;
                    }
                });
                pages.empty();
                for (var page = 1; page <= response.documents.last_page; page++) {
                    var pageDom = $('<button class="page page-link" style="margin-right: 2px"></button>');
                    pageDom.append(page);
                    if (page === response.documents.current_page) {
                        pageDom.addClass("active")
                    }
                    pageDom.data('page', page);
                    pages.append(pageDom);
                }
                pages.show();
                $('#loadDocumentSpinner').hide();
            } else if (response.status === false || response.status === 'error') {
                alert(response.error);
            } else {
                alert('Ошибка запроса. Обратитесь к администратору.');
            }
        } else {
            alert('Ошибка запроса. Обратитесь к администратору.');
        }
    });
}

function printDoc(type, id) {
    if (type === 3) {
        $.ajax({
            url: "/documents/create",
            data: {
                id: id
            },
            dataType: 'json',
            type: "GET"
        }).done(function (response) {
            if (response !== undefined) {
                if (response.status === true) {
                    $('#idDocumentReasons').val(response.docId);
                    let itemsDocument = $('#itemsDocument');
                    itemsDocument.empty();
                    response.items.forEach(function (item) {
                        let itemContainer = $('<div class="card border-primary mb-10"></div>');
                        if (item.item !== null) {
                            itemContainer.append($('<div class="card-header"></div>').append(item.item.name + ' Инв. номер: ').append($('<span style="font-weight: bold;"></span>').append('(' + item.item.inv_number + ')')))
                                .append($('<div class="card-body text-dark reason" data-itemid="' + item.item.id + '"></div>').append($('<h6>Техническое состояние на момент составления заключения:</h6>')).append($('<textarea class="condition form-control"></textarea>'))
                                    .append('<h6>Заключение:</h6>').append($('<textarea class="conclusion form-control"></textarea>')));
                            itemsDocument.append(itemContainer);
                            itemsDocument.append($('<br>'));
                        } else {
                            let groupContainer = $('<div class="card border-success mb-10" style="border: 1px solid black; border-radius: 10px;"></div>');
                            groupContainer.append($('<div class="card-header"></div>').append(item.group.name_group));
                            item.group.group_items.forEach(function (i) {
                                itemContainer = $('<div  style="margin: 10px;" class="card border-primary mb-10"></div>');
                                itemContainer.append($('<div class="card-header"></div>').append(i.name + ' Инв. номер: ').append($('<span style="font-weight: bold;"></span>').append('(' + i.inv_number + ')')))
                                    .append($('<div class="card-body reason text-dark"></div>').append($('<h6>Техническое состояние на момент составления заключения:</h6>')).append($('<textarea data-itemid="' + i.id + '" class="condition form-control"></textarea>'))
                                        .append('<h6>Заключение:</h6>').append($('<textarea class="conclusion form-control"></textarea>')));
                                groupContainer.append(itemContainer);
                            });
                            itemsDocument.append(groupContainer);
                            itemsDocument.append($('<br>'));
                        }
                    });
                    $('#printDocModal').modal('show');
                } else if (response.status === false || response.status === 'error') {
                    alert(response.error);
                } else {
                    alert('Ошибка запроса. Обратитесь к администратору.');
                }
            } else {
                alert('Ошибка запроса. Обратитесь к администратору.');
            }
        });
    }
}

function saveReasons() {
    let reasonsArray = [];
    let idDoc = $('#idDocumentReasons');
    let ready = true;
    $('#alertPrint').hide();
    $('#itemsDocument').find('textarea').each(function () {
        if ($(this).val() === null || $(this).val() === '') {
            ready = false;
        }
    });
    if (ready) {
        $('#itemsDocument').hide();
        $('#printLoad').show();
        $('#itemsDocument .reason').each(function () {
            let id = $(this).data('itemid');
            let conclusion = $(this).find('.conclusion').val();
            let condition = $(this).find('.condition').val();
            reasonsArray.push({
                'id': id,
                'condition': condition,
                'conclusion': conclusion,
            });
        });
        $.ajax({
            url: "/documents/saveReasons",
            data: {
                '_token': window.csrf,
                reasonsArray: reasonsArray,
                idDoc: idDoc.val(),
            },
            dataType: 'json',
            type: "POST"
        }).done(function (response) {
            $('#itemsDocument').show();
            $('#printLoad').hide();
            if (response !== undefined) {
                if (response.status === true) {
                    $('#printDocModal').modal('hide');
                    window.open('/documents/print' + '?idDoc=' + idDoc.val(), '_blank');
                } else if (response.status === false || response.status === 'error') {
                    alert(response.error);
                } else {
                    alert('Ошибка запроса. Обратитесь к администратору.');
                }
            } else {
                alert('Ошибка запроса. Обратитесь к администратору.');
            }
        });
    }else {
        $('#alertPrint').show('fade');
    }
}


function moreDetailedModal(id, status) {
    $('#moreDetailed').modal('show');
    $('#elementsDocument').empty();
    $('#loadingItemsDocument').show();
    $('#tableElementsDocuments').hide();
    var statusString,
        acceptDocumentBtn = $('#acceptDocumentMoreModal'),
        buttonsMoreModal = $('#buttonsMoreModal');
    switch (status) {
        case DOC_STATUS_NONE_DOCUMENT:
            statusString = $('<h4>Статус документа: Нет документа <i class="fa fa-file" style="color: #de000b" aria-hidden="true"></i></h4>');
            acceptDocumentBtn.prop('disabled', true).show();
            break;
        case DOC_STATUS_WAIT:
            statusString = $('<h4>Статус документа: Ожидает <i class="fa fa-clock-o" style="color: #1b00ff" aria-hidden="true"></i></h4>');
            acceptDocumentBtn.prop('disabled', false).show();
            buttonsMoreModal.show();
            break;
        case DOC_STATUS_ACCEPT:
            statusString = $('<h4>Статус документа: Принят <i class="fa fa-check" style="color: #1e8d19;" aria-hidden="true"></i></h4>');
            acceptDocumentBtn.prop('disabled', false);
            buttonsMoreModal.hide();
            break;
        case DOC_STATUS_REJECT:
            statusString = $('<h4> Статус документа: Отклонён <i class="fa fa-undo" aria-hidden="true"></i></h4>');
            acceptDocumentBtn.prop('disabled', false);
            buttonsMoreModal.hide();
            break;
    }
    $.ajax({
        url: "/documents/showMore",
        data: {
            id: id
        },
        dataType: 'json',
        type: "GET"
    }).done(function (response) {
        if (response !== undefined) {
            if (response.status === true) {
                var documentStatusMore = $('#documentStatusMoreModal');
                $('#acceptDocumentMoreModal').attr('data-iddocument', id).data('iddocument', id);
                $('#oldOwnerMoreDetailed').empty().text(response.owners.fromOwner);
                $('#newOwnerMoreDetailed').empty().text(response.owners.toOwner);
                $('#loadingItemsDocument').hide();
                $('#tableElementsDocuments').show('fade');
                $('#documentNumberMoreModal').empty().append('Номер документа: ').append(id);
                documentStatusMore.empty().append(statusString);
                response.items.forEach(function (i) {
                    var item = $('<tr></tr>');
                    i.full_name = i.last_name + " " + i.first_name + " " + i.father_name;
                    if (i.status === 1) {
                        i.status = "Работает";
                    } else if (i.status === 2) {
                        i.status = "Не работает";
                    } else if (i.status === 3) {
                        i.status = "Утеряно";
                    } else if (i.status === 4) {
                        i.status = "Списано"
                    } else {
                        i.status = "ERROR!!!";
                    }
                    item.append($('<td></td>').append(i.name))
                        .append($('<td></td>').append(i.name_1c))
                        .append($('<td></td>').append(i.inv_number))
                        .append($('<td></td>').append(i.serial_number))
                        .append($('<td></td>').append(i.loc_name))
                        .append($('<td></td>').append(i.status))
                        .append($('<td></td>').append(i.full_name))
                        .append($('<td></td>'));
                    $('#elementsDocument').append(item);
                });
                response.groups.forEach(function (i) {
                    var group = $('<tr></tr>');
                    i.full_name = i.last_name + " " + i.first_name + " " + i.father_name;
                    if (i.status === 1) {
                        i.status = "Работает";
                    } else if (i.status === 2) {
                        i.status = "Не работает";
                    } else if (i.status === 3) {
                        i.status = "Утеряно";
                    } else if (i.status === 4) {
                        i.status = "Списано"
                    } else {
                        i.status = "ERROR!!!";
                    }
                    group.append($('<td></td>').append(i.name_group))
                        .append($('<td></td>').append(i.name1c_group))
                        .append($('<td></td>').append(i.inventory_number))
                        .append($('<td></td>').append('-'))
                        .append($('<td></td>').append(i.loc_name))
                        .append($('<td></td>').append('-'))
                        .append($('<td></td>').append(i.full_name))
                        .append($('<td></td>'));
                    $('#elementsDocument').append(group);
                });
                if (response.documentLink !== null && status !== DOC_STATUS_NONE_DOCUMENT) {
                    documentStatusMore.append($('<a target="_blank" class="text-center" href="' + response.documentLink + '">Просмотреть документ</a>'))
                }
            } else if (response.status === false || response.status === 'error') {
                alert(response.error);
            } else {
                alert('Ошибка запроса. Обратитесь к администратору.');
            }
        } else {
            alert('Ошибка запроса. Обратитесь к администратору.');
        }
    });
}

function acceptDocument() {
    var id;
    id = $('#acceptDocumentMoreModal').data('iddocument');
    $('#loadingItemsDocument').show();
    $('#tableElementsDocuments').hide();
    $.ajax({
        url: "/documents/accept",
        data: {
            '_token': window.csrf,
            id: id
        },
        dataType: 'json',
        type: "POST"
    }).done(function (response) {
        if (response !== undefined) {
            if (response.status === true) {
                $('#loadingItemsDocument').hide();
                $('#successMoreModal').show();
                setTimeout(function () {
                    $('#successMoreModal').hide();
                    $('#moreDetailed').modal('hide');
                }, 500);
                getDocuments(1);
            } else if (response.status === false || response.status === 'error') {
                alert(response.error);
            } else {
                alert('Ошибка запроса. Обратитесь к администратору.');
            }
        } else {
            alert('Ошибка запроса. Обратитесь к администратору.');
        }
    });
}

function rejectDocument() {
    var id;
    id = $('#acceptDocumentMoreModal').data('iddocument');
    $('#loadingItemsDocument').show();
    $('#tableElementsDocuments').hide();
    $.ajax({
        url: "/documents/reject",
        data: {
            '_token': window.csrf,
            id: id
        },
        dataType: 'json',
        type: "POST"
    }).done(function (response) {
        if (response !== undefined) {
            if (response.status === true) {
                $('#loadingItemsDocument').hide();
                $('#successMoreModal').show();
                setTimeout(function () {
                    $('#successMoreModal').hide();
                    $('#moreDetailed').modal('hide');
                }, 500);
                getDocuments(1)
            } else if (response.status === false || response.status === 'error') {
                alert(response.error);
            } else {
                alert('Ошибка запроса. Обратитесь к администратору.');
            }
        } else {
            alert('Ошибка запроса. Обратитесь к администратору.');
        }
    });
}

function callSaveFileDocumentModal(id) {
    $('#formForAttachDocument')[0].reset();
    $('#saveFileDocumentModal').modal('show');
    $('#attachNewFile').show();
    $('#loadAttachFileModal').hide();
    $('#attachDocumentFile').attr('data-iddocument', id).data('data-iddocument', id);
}

function saveDocumentFile() {
    $('#loadAttachFileModal').show();
    $('#attachNewFile').hide();
    var idDocument = $('#attachDocumentFile').data('iddocument'),
        fl = $('#documentFileInput');
    if (fl.val() != null) {
        var fd = new FormData;
        fd.append('file', fl.prop('files')[0]);
        fd.append('id', idDocument);
        $.ajax({
            url: "/documents/saveDocumentFile",
            headers: {
                'X-CSRF-TOKEN': window.csrf
            },
            data: fd,
            dataType: 'json',
            type: "POST",
            processData: false,
            contentType: false
        }).done(function (response) {
            if (response !== undefined) {
                if (response.status === true) {
                    $('#loadAttachFileModal').hide();
                    $('#successAttachFileModal').show();
                    setTimeout(function () {
                        $('#successAttachFileModal').hide();
                        $('#saveFileDocumentModal').modal('hide');
                    }, 500);
                    getDocuments(1);
                } else if (response.status === false || response.status === 'error') {
                    alert(response.error);
                } else {
                    alert('Ошибка запроса. Обратитесь к администратору.');
                }
            } else {
                alert('Ошибка запроса. Обратитесь к администратору.');
            }
        });
    }
}