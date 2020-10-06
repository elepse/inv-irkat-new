function loadModalEdit(active, typeLoad) {
    if (active) {
        $('#itemResult').hide();
        $('#EditItemLoad').show();
    } else if (typeLoad) {
        $('#EditItemLoad').hide();
    } else {
        $('#itemResult').show('fade');
        $('#EditItemLoad').hide();
    }
}

function editItem(id) {
    $("#EditSave").data('id', id);
    $("#editModal").modal('show');
    loadModalEdit(true, false);
    $.ajax({
        url: "/edit",
        data: {
            id: id
        },
        dataType: 'json',
        type: "GET"
    }).done(function (response) {
        if (response !== undefined) {
            if (response.status === true) {
                $('#idEditItem').val(response.item.id);
                $('#EditName').val(response.item.name);
                $('#EditName1C').val(response.item.name_1c);
                $('#EditSer').val(response.item.serial_number);
                $('#EditInv').val(response.item.inv_number);
                $('#EditCount').val(response.item.count);
                $('#EditPrice').val(response.item.price);
                $('#EditCom').val(response.item.last_comment);
                $('#EditYear').val(response.item.create_time.substring(0, 4));
                $("#EditLoc").val(response.item.last_location);
                $("#EditOwner").val(response.item.last_owner);
                $("#EditStatus").val(response.item.status);
                $('#EditInvStatus').val(response.item.inv_status)
            } else if (response.status === false || response.status === 'error') {
                alert(response.error);
            } else {
                alert('Ошибка запроса. Обратитесь к администратору.');
            }
        } else {
            alert('Ошибка запроса. Обратитесь к администратору.');
        }
    }).always(function () {
        loadModalEdit(false, false);
    });
}

var EditName = $('#EditName'),
    EditName1C = $('#EditName1C'),
    EditInv = $('#EditInv'),
    EditSer = $('#EditSer'),
    EditStatus = $('#EditStatus'),
    EditSave = $("#EditSave"),
    EditCount = $('#EditCount'),
    EditPrice = $('#EditPrice'),
    EditCom = $('#EditCom');
    EditId = $('#idEditItem');
    EditInvStatus = $('#EditInvStatus');
EditSave.on('click', function () {
    loadModalEdit(true, true);
    $.ajax({
        url: "/edit/save",
        data: {
            '_token': window.csrf,
            EditId: EditId.val(),
            EditName: EditName.val(),
            EditName1C: EditName1C.val(),
            EditInv: EditInv.val(),
            EditSer: EditSer.val(),
            EditStatus: EditStatus.val(),
            EditCount: EditCount.val(),
            EditPrice: EditPrice.val(),
            EditCom: EditCom.val(),
            EditInvStatus: EditInvStatus.val()
        },
        dataType: 'json',
        type: "POST"
    }).done(function (response) {
        if (response !== undefined) {
            if (response.status === true) {
                loadModalEdit(false, true);
                $("#successNotification").show();
                sessionStorage.clear();
                $('#select_all').prop('checked', false);
                setTimeout(function () {
                    $("#successNotification").hide();
                    $("#editModal").modal('hide');
                    $('#search_button').trigger('click');
                }, 500);
            } else if (response.status === false || response.status === 'error') {
                alert(response.error);
            } else {
                alert('Ошибка запроса. Обратитесь к администратору.');
            }
        } else {
            alert('Ошибка запроса. Обратитесь к администратору.');
        }
    });
});

function callMoveModal() {
    $('#moveModal').modal('show');
    parseForMove(parseCashItemsModal(), parseGroupCashModal())
}

function parseForMove(items, groups) {
    $('#elementsForMove').empty();
    var colItems = items.length,
        colGroups = groups.length;
    if (colItems > 0 || colGroups > 0) {
        $('#paramForMove').show();
        $('#noneElementsMoveModal').hide();
        items.forEach(function (i, n) {
            var itemForMove = $('<tr class="itemForMove" data-cashId="' + i.id + '"></tr>');
            i.full_name = i.last_name + " " + i.first_name + " " + i.father_name;
            itemForMove.append($('<td></td>').append(i.name))
                .append($('<td></td>').append(i.name_1c))
                .append($('<td></td>').append(i.inv_number))
                .append($('<td></td>').append(i.serial_number))
                .append($('<td></td>').append(i.loc_name))
                .append($('<td></td>').append(i.status))
                .append($('<td></td>').append(i.full_name))
                .append($('<td><a href="javascript: void(0)" onclick="deleteCashItem(' + i.id + ', true)"><i class="fa fa-times" id="deleteMove" aria-hidden="true" title="Отменить выбор"></i></a></td>'));
            $('#elementsForMove').append(itemForMove);
            $('#newGroupSave').prop('disabled', false);
        });

        groups.forEach(function (i, n) {
            var groupForMove = $('<tr class="DocCashGroup" data-cashGroupId="' + i.idGroup + '"></tr>');
            groupForMove.append($('<td></td>').append(i.name_group).append(' <i class="fa fa-link" aria-hidden="true"></i>'))
                .append($('<td></td>').append(i.name1c))
                .append($('<td></td>').append(i.inventory_number_group))
                .append($('<td></td>').append('-'))
                .append($('<td></td>').append(i.loc_name))
                .append($('<td></td>').append('-'))
                .append($('<td></td>').append(i.full_name))
                .append($('<td><a href="javascript: void(0)" onclick="deleteCashGroup(' + i.idGroup + ');"><i class="fa fa-times" id="deleteMove" aria-hidden="true" title="удалить элемент"></i></a></td>'));
            $('#elementsForMove').append(groupForMove);
        });
    } else {
        $('#noneElementsMoveModal').show('blind');
        $('#paramForMove').hide('blind');
    }
}

$('#SaveChangeLocation').on('click', function () {
    var commentForMove = $('#commentForMove'),
        moveLocation = $('#newLocationForMoveItem'),
        groupsForMove = parseGroupCashModal(),
        itemsForMove = parseCashItemsModal();
    if (commentForMove.val() === "") {
        $('#errorMoveComment').text('Вы не указали комментарий!').show('blind');
    } else {
        if (moveLocation.val() != 'none') {
            $('#errorMoveComment').hide('fade');
            $('#bodyForMoveModal').hide();
            $('#loadForMoveModal').show();
            $.ajax({
                url: "/edit/saveNewLocation",
                data: {
                    '_token': window.csrf,
                    'location': parseInt(moveLocation.val()),
                    'groups': groupsForMove,
                    'items': itemsForMove,
                    'comment': commentForMove.val()
                },
                dataType: 'json',
                type: "POST"
            }).done(function (response) {
                if (response !== undefined) {
                    if (response.status === true) {
                        $('#search_button').click();
                        sessionStorage.clear();
                        $('#formForMove')[0].reset();
                        $('#moveModal').modal('hide');
                    } else if (response.status === false || response.status === 'error') {
                        alert(response.error);
                    } else {
                        alert('Ошибка запроса. Обратитесь к администратору.');
                    }
                } else {
                    alert('Ошибка запроса. Обратитесь к администратору.');
                }
            }).always(function () {
                $('#bodyForMoveModal').show();
                $('#loadForMoveModal').hide();
            });
        } else {
            $('#errorMoveComment').text('Вы не выбрали расположение').show('blind');
        }
    }
});

function showMoveModalInGroup(id) {
    var name = $('tr[data-idtie= ' + id + ']').find('#nameGroupInModal').text(),
        nameTittle = $('#nameMoveItemInGroup');
    $('#idItemMoveInGroup').val('id');
    nameTittle.text(name).data('idMovingItem', id);
    $('#errorMoveModalInGroup').hide();
    $('#bodyModalMoveInGroup').show();
    $('#loadingModalMoveInGroup').hide();
    $('#successMoveInGroup').hide();
    $('#moveModalForElementInGroup').modal('show');
}

function saveNewLocationItemInGroup() {
    var id = $('#nameMoveItemInGroup').data('idMovingItem'),
        location = $('#locationForMoveElementInGroup'),
        comment = $('#commentForMoveElementInGroup'),
        error = $('#errorMoveModalInGroup'),
        loading = $('#loadingModalMoveInGroup'),
        body = $('#bodyModalMoveInGroup'),
        itemsForMove = [];
    if (comment.val() !== "" && location.val() !== "") {
        error.hide();
        id = {'id': id};
        itemsForMove.push(id);
        loading.show();
        body.hide();
        $.ajax({
            url: "/edit/saveNewLocation",
            data: {
                '_token': window.csrf,
                'location': location.val(),
                'items': itemsForMove,
                'comment': comment.val()
            },
            dataType: 'json',
            type: "POST"
        }).done(function (response) {
            if (response !== undefined) {
                if (response.status === true) {
                    loading.hide();
                    $('tr[data-idtie= ' + id.id + ']').find('#locationGroupInModal').text(location.find('option:selected').text());
                    $('#successMoveInGroup').show();
                    setTimeout(function () {
                        $('#moveModalForElementInGroup').modal('hide');
                    }, 500);
                } else if (response.status === false || response.status === 'error') {
                    alert(response.error);
                } else {
                    alert('Ошибка запроса. Обратитесь к администратору.');
                }
            } else {
                alert('Ошибка запроса. Обратитесь к администратору.');
            }
        });
    } else if (comment.val() !== "") {
        error.empty().text('Оставьте комментарий!').show('fade')
    } else error.empty().text('Выберите новое расположение').show('fade')
}

function updateLocations(id,name) {
    $('.locations').append($('<option value="'+ id +'"></option>').append(name));
}

function updateOwners(id, name) {
    $('.owners').append($('<option value="'+ id +'"></option>').append(name));
}