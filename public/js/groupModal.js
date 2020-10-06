var groupId,
    groupParams = [],
    groupChange;

function showGroupItem(idGroup) {
    groupChange = 0;
    $('#selectGroupSuccessful').hide();
    $('.groupModalItems').hide();
    $('untieGroupItems').hide();
    $('.loadGroupItems').show();
    $('#groupItemsInfo').empty();
    $('.untieGroupItems').find('.tbodyUntie').empty();
    groupParams = [];
    $('#showGroupItems').modal('show');
    $.ajax({
        url: "group/show",
        data: {
            idGroup: idGroup
        },
        dataType: 'json',
        type: "GET"
    }).done(function (response) {
        if (response !== undefined) {
            if (response.status === true) {
                groupId = response.group[0].group_id;
                var checkSelectGroup = parseGroupCashModal().findIndex(function (i) {
                    return i.idGroup == response.group[0].group_id;
                });
                if (!checkSelectGroup) {
                    $('#selectGroupSuccessful').show();
                    $('#selectGroup').hide();
                    $('#unselectGroup').show();
                } else {
                    $('#selectGroupSuccessful').hide();
                    $('#selectGroup').show();
                    $('#unselectGroup').hide();
                }
                groupParams = response.group[0];
                $('.groupModalItems').show('fade');
                $('.loadGroupItems').hide();
                response.group[0].group_items.forEach(function (i, n) {
                    var item = $('<tr class="validGroupElement"  data-idtie="' + i.id + '"></tr>');
                    if (i.status == 1) {
                        i.status = "Работает";
                    } else if (i.status == 2) {
                        i.status = "Не работает";
                    } else if (i.status == 3) {
                        i.status = "Утеряно";
                    } else if (i.status == 4) {
                        i.status = "Списано"
                    } else {
                        i.status = "ERROR!!!";
                    }
                    $('#groupName').text(response.group[0].name_group);
                    $('#groupInvNum').text(response.group[0].inventory_number);
                    i.full_name = i.last_name + " " + i.first_name + " " + i.father_name;
                    response.group[0].full_name = response.group[0].last_name + ' ' + response.group[0].first_name + ' ' + response.group[0].father_name;
                    $('#groupLocation').text(response.group[0].loc_name);
                    $('#groupOwner').text(response.group[0].full_name);
                    CashItems.push(i);
                    item.append($('<td id="nameGroupInModal"></td>').append(i.name))
                        .append($('<td></td>').append(i.name_1c))
                        .append($('<td></td>').append(i.inv_number))
                        .append($('<td></td>').append(i.serial_number))
                        .append($('<td id="locationGroupInModal"></td>').append(i.loc_name))
                        .append($('<td></td>').append(i.status))
                        .append($('<td></td>').append(i.full_name))
                        .append($('<td class="untieItemIcon"></td>').append($('<i class="fa fa-chain-broken" title="Открепить элемент от группы" style="color: red;cursor: pointer;" onclick="untieItem(' + i.id + ')" aria-hidden="true"></i>')))
                        .append($('<td></td>').append('<i class="fa fa-sign-out oru" style="color: #007ed3; cursor: pointer;" onclick="showMoveModalInGroup(' + i.id + ')" aria-hidden="true"></i>'));
                    item.css('background-color', '#bde0ff');
                    $('#groupItemsInfo').append(item);
                });
            } else if (response.status === false || response.status === 'error') {
                alert(response.error);
            } else {
                alert('Ошибка запроса. Обратитесь к администратору.');
            }
        } else {
            alert('Ошибка запроса. Обратитесь к администратору.');
        }
    }).always(function () {

    });
}

function untieItem(id) {
    $('tr[data-idtie=' + id + ']').find('.untieItemIcon').empty().html("<i class='fa fa-circle-o-notch fa-spin'></i>");
    $.ajax({
        url: "group/untieItem",
        data: {
            '_token': window.csrf,
            id: id,
            groupId: groupId
        },
        dataType: 'json',
        type: "POST"
    }).done(function (response) {
        if (response !== undefined) {
            if (response.status === true) {
                var untieItem = $('#groupItemsInfo').find('tr[data-idtie=' + id + ']').clone(true, true).attr('class', 'untieGroupElement').hide();
                $('#groupItemsInfo').find('tr[data-idtie=' + id + ']').hide('fade');
                setTimeout(function () {
                    $('#groupItemsInfo').find('tr[data-idtie=' + id + ']').remove();
                }, 500);
                untieItem.find('.untieItemIcon').empty().append('<i title="Отменить открепление" onclick="reTieItem($(this).parents(\'.untieGroupElement\').data(\'idtie\'))" aria-hidden="true" class="fa fa-undo" style="color: blue;cursor: pointer;"></i>');
                $('.untieGroupItems').find('.tbodyUntie').append(untieItem.show('fade'));
                $('.untieGroupItems').show('fade');
                $('untieGroupItems').show();
                groupChange++;
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

function reTieItem(id) {
    $('tr[data-idtie=' + id + ']').find('.untieItemIcon').empty().html("<i class='fa fa-circle-o-notch fa-spin'></i>");
    $.ajax({
        url: "group/reTieItem",
        data: {
            '_token': window.csrf,
            id: id,
            groupId: groupId
        },
        dataType: 'json',
        type: "POST"
    }).done(function (response) {
        if (response !== undefined) {
            if (response.status === true) {
                var reTieItem = $('.untieGroupElement[data-idtie=' + id + ']').clone(true, true).attr('class', 'validGroupElement').hide();
                $('.tbodyUntie').find('tr[data-idtie=' + id + ']').hide('fade');
                reTieItem.find('.untieItemIcon').empty().append('<i class="fa fa-chain-broken" title="Открепить элемент от группы" style="color: red;cursor: pointer;" onclick="untieItem(' + id + ')" aria-hidden="true"></i>');
                setTimeout(function () {
                    $('.tbodyUntie').find('.untieGroupElement[data-idtie=' + id + ']').remove();
                }, 400);
                $('.tieGroupItems').find('#groupItemsInfo').append(reTieItem.show('fade'));
                groupChange--;
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


function selectGroup(id, isRemove) {
    var fullName = groupParams.last_name + ' ' + groupParams.first_name + ' ' + groupParams.father_name,
        paramSelectGroup = {
            'idGroup': id,
            'name_group': groupParams.name_group,
            'name1c': groupParams.name1c_group,
            'inventory_number_group': groupParams.inventory_number,
            'owner_group': groupParams.owner_group,
            'full_name': fullName,
            'loc_name': groupParams.loc_name
        };

    if (isRemove === undefined) isRemove = false;
    var array = sessionStorage.getItem('cachedGroup_array');
    if (!array) array = [];
    else array = JSON.parse(array);
    var index = array.findIndex(function (i) {
        return i.idGroup == id;
    });
    if (isRemove) {
        if (index >= 0) array.splice(index, 1);
    } else if (index < 0) {
        array.push(paramSelectGroup);
    }
    sessionStorage.setItem('cachedGroup_array', JSON.stringify(array));
    $('#selectGroupSuccessful').show('blind');
    $('#selectGroup').hide();
    $('#unselectGroup').show();
    updateTableOfSelectItems();
}

$('#unselectGroup').on('click', function () {
    var array = parseGroupCashModal(),
        indexUnselectItem = array.findIndex(function (i) {
            return i.idGroup == groupId;
        });
    if (indexUnselectItem >= 0) {
        array.splice(indexUnselectItem, 1);
    }
    sessionStorage.setItem('cachedGroup_array', JSON.stringify(array));
    $('#selectGroupSuccessful').hide('blind');
    $('#selectGroup').show();
    $('#unselectGroup').hide();
    updateTableOfSelectItems();
});

$('#disbandGroup').on('click', function () {
    var answer = confirm('Вы уверены, что хотите удалить группу?');
    if (answer) {
        $.ajax({
            url: "group/disband",
            data: {
                '_token': window.csrf,
                groupId: groupId
            },
            dataType: 'json',
            type: "POST"
        }).done(function (response) {
            if (response !== undefined) {
                if (response.status === true) {
                    var arrayCashGroups = parseGroupCashModal(),
                        idDeletedGroup = arrayCashGroups.findIndex(function (i) {
                            return i.idGroup == groupId;
                        });
                    if (!idDeletedGroup) {
                        arrayCashGroups.splice(idDeletedGroup, 1);
                        sessionStorage.setItem('cachedGroup_array', JSON.stringify(arrayCashGroups));
                    }
                    $('#search_button').trigger('click');
                    $('#showGroupItems').modal('hide');
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
});

$('#showGroupItems').on('hidden.bs.modal', function () {
    if (groupChange > 0) {
        $('#search_button').trigger('click');
    }
});
$('#selectGroup').on('click', function () {
    selectGroup(groupId, false);
    $('#selectGroupSuccessful').show('blind');
});