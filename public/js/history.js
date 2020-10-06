var historyModal = $('#historyModal'),
    historyLoad = $('#historyLoad'),
    historyItemsContainer = $('#historyItemsContainer');

function callHistoryModal(id, type) {
    historyModal.modal('show');
    historyLoad.show();
    historyItemsContainer.hide();
    $.ajax({
        url: "history",
        data: {
            id: id,
            type: type
        },
        dataType: 'json',
        type: "GET"
    }).done(function (response) {
        if (response !== undefined) {
            if (response.status === true) {
                $('#historyTimeLine').empty();

                response.data.forEach(function (i) {
                    if (i.user !== null) {
                        i.creator = i.user.name;
                    }else {
                        i.creator = 'Неизвестно';
                    }
                    var historyItem = $('<li class="timeline-item historyItem" data-idhistoryitem="' + i.id + '"></li>'),
                        historyIcon,
                        infoChange = $('<div class="timeline-body"></div>'),
                        header,
                        subscript;
                    switch (i.type_change) {
                        case 1:
                            let comment = JSON.parse(i.change_log);
                            historyIcon = $('<div class="timeline-badge info"><i class="fa fa-map-marker" aria-hidden="true"></i></div>');
                            infoChange.append($('<h6></h6>').append('Перенесён в: ' + i.loc_name))
                                .append($('<h6></h6>').append('Комментарий: ' + comment.commentForMove));
                            header = "Перемещение";
                            break;
                        case 2:
                            historyIcon = $('<div class="timeline-badge success"><i class="fa fa-user" aria-hidden="true"></i></div>');
                            i.full_name = i.last_name + " " + i.first_name + " " + i.father_name;
                            infoChange.append($('<h6></h6>').append('Новый владелец: ' + i.full_name));
                            header = "Смена владельца";
                            break;
                        case 3:
                            let changeLog = JSON.parse(i.change_log);
                            switch (changeLog['oldStatus']) {
                                case 1:
                                    changeLog['oldStatus'] = 'Работает';
                                    break;
                                case 2:
                                    changeLog['oldStatus'] = 'Не работает';
                                    break;
                                case 3:
                                    changeLog['oldStatus'] = 'Утеряно';
                                    break;
                                case 4:
                                    changeLog['oldStatus'] = 'Списано';
                                    break;
                            }
                            switch (changeLog['newStatus']) {
                                case 1:
                                    changeLog['newStatus'] = 'Работает';
                                    break;
                                case 2:
                                    changeLog['newStatus'] = 'Не работает';
                                    break;
                                case 3:
                                    changeLog['newStatus'] = 'Утеряно';
                                    break;
                                case 4:
                                    changeLog['newStatus'] = 'Списано';
                                    break;
                            }

                            switch (changeLog['oldInv_status']) {
                                case 1:
                                    changeLog['oldInv_status'] = 'Найден';
                                    break;
                                case 2:
                                    changeLog['oldInv_status'] = 'Не найден';
                                    break;
                                case 3:
                                    changeLog['oldInv_status'] = 'Найден частично';
                                    break;
                            }
                            switch (changeLog['newInv_status']) {
                                case 1:
                                    changeLog['newInv_status'] = 'Найден';
                                    break;
                                case 2:
                                    changeLog['newInv_status'] = 'Не найден';
                                    break;
                                case 3:
                                    changeLog['newInv_status'] = 'Найден частично';
                                    break;
                            }

                            historyIcon = $('<div class="timeline-badge warning"><i class="fa fa-pencil" aria-hidden="true"></i></div>');
                            if (changeLog['oldName'] !== changeLog['newName']) infoChange.append($('<h6></h6>').append('Наименование: ' + changeLog['oldName'] + ' => ' + changeLog['newName']));
                            if (changeLog['oldCount'] !== changeLog['newCount']) infoChange.append($('<h6></h6>').append('Количество: ' + changeLog['oldCount'] + ' => ' + changeLog['newCount']));
                            if (changeLog['oldPrice'] !== changeLog['newPrice']) infoChange.append($('<h6></h6>').append('Цена: ' + changeLog['oldPrice'] + ' => ' + changeLog['newPrice']));
                            if (changeLog['oldStatus'] !== changeLog['newStatus']) infoChange.append($('<h6></h6>').append('Статус: ' + changeLog['oldStatus'] + ' => ' + changeLog['newStatus']));
                            if (changeLog['oldName_1C'] !== changeLog['newName_1C']) infoChange.append($('<h6></h6>').append('Наименование 1С: ' + changeLog['oldName_1C'] + ' => ' + changeLog['newName_1C']));
                            if (changeLog['oldInv_number'] !== changeLog['newInv_number']) infoChange.append($('<h6></h6>').append('Инв. номер: ' + changeLog['oldInv_number'] + ' => ' + changeLog['newInv_number']));
                            if (changeLog['oldSer_number'] !== changeLog['newSer_number']) infoChange.append($('<h6></h6>').append('Сер. номер: ' + changeLog['oldSer_number'] + ' => ' + changeLog['newSer_number']));
                            if (changeLog['oldInv_status'] !== changeLog['newInv_status']) infoChange.append($('<h6></h6>').append('Статус инвентаризации: ' + changeLog['oldInv_status'] + ' => ' + changeLog['newInv_status']));
                            if (changeLog['oldComment'] !== changeLog['newComment']) infoChange.append($('<h6></h6>').append('Комментарий: ' + changeLog['oldComment'] + ' => ' + changeLog['newComment']));
                            header = "Основные изменения";
                            break;
                        case 4:
                            historyIcon = $('<div class="timeline-badge primary"><i class="fa fa-link" aria-hidden="true"></i></div>');
                            if (i.deleted === 1){
                                subscript = "(Удалена)"
                            }else{
                                subscript = ""
                            }
                            infoChange.append($('<h6></h6>').append('Приклеплён к группе: ' + i.name_group).append($('<span style="color: red"> </span>').append(subscript)))
                                .append($('<h6></h6>').append('Инв номер: ' + i.inventory_number));
                            header = "Добавление в группу";
                            break;
                        case 5:
                            historyIcon = $('<div class="timeline-badge danger"><i class="fa fa-chain-broken" aria-hidden="true"></i></div>');
                            if (i.deleted === 1){
                                subscript = "(Удалена)"
                            }else{
                                subscript = ""
                            }
                            infoChange.append($('<h6></h6>').append('Приклеплён к группе: ' + i.name_group).append($('<span style="color: red"> </span>').append(subscript)))
                                .append($('<h6></h6>').append('Инв номер: ' + i.inventory_number));
                            header = "Откреплён от группы";
                            break;
                    }
                    let createTime = new Date(i.create_time);
                    historyItem.append(historyIcon)
                        .append($('<div class="timeline-panel"></div>').append($('<div class="timeline-heading"></div>').append($('<h4></h4>').append(header)).append($('<p></p>').append($('<small class="text-muted"></small>').append(createTime.toLocaleString("ru"))))).append(infoChange).append($('<hr>')).append($('<p></p>').append(i.creator)));
                    $('#historyTimeLine').append(historyItem);
                });
                historyLoad.hide();
                historyItemsContainer.show();
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