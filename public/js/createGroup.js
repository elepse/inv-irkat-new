function callCreateGroupModal() {
    $('#formCreateNewGroup')[0].reset();
    $("#createGroup").modal('show');
    $('#SaveNewGroup').hide();
    $('#paramNewGroup').show();
    $('#newGroupSave').prop('disabled', true);
    $('#noneSelectItemsForNewGroup').hide();
    parseItemForNewGroup(parseCashItemsModal());
    arrayItemForNewGroup = [];
    checkOwnerForGroup();
}

function parseItemForNewGroup(items) {
    $('#selectItemsForNewGroup').empty();
    var colItems = items.length;
    if (colItems > 0) {
        items.forEach(function (i, n) {
            var itemForNewGroup = $('<tr class="itemForNewGroup" data-cashId="' + i.id + '"></tr>');
            i.full_name = i.last_name + " " + i.first_name + " " + i.father_name;
            itemForNewGroup.append($('<td></td>').append(i.name))
                .append($('<td></td>').append(i.name_1c))
                .append($('<td></td>').append(i.inv_number))
                .append($('<td></td>').append(i.serial_number))
                .append($('<td></td>').append(i.loc_name))
                .append($('<td></td>').append(i.status))
                .append($('<td></td>').append(i.full_name))
                .append($('<td><a href="javascript: void(0)" onclick="deleteCashItem(' + i.id + ', true)"><i class="fa fa-times" aria-hidden="true" title="Отменить выбор"></i></a></td>'));
            $('#selectItemsForNewGroup').append(itemForNewGroup);
            $('#newGroupSave').prop('disabled', false);
        });
    } else {
        $('#noneSelectItemsForNewGroup').show();
    }
}

function checkOwnerForGroup() {
    var items = parseCashItemsModal(),
        coincidence = null,
        firstOwner = null;
    if (items.length > 0) {
        firstOwner = items[0].last_owner;
        coincidence = items.findIndex(function (i) {
            return i.last_owner != firstOwner;
        });
        if (coincidence >= 0) {
            $('#alertCoincidence').text('Наименования принадлежат разным владельцам!').show();
            $('#newGroupSave').prop('disabled',true);
            return true;
        } else {
            $('#alertCoincidence').hide('fade');
            $('#newGroupSave').prop('disabled',false);
            return false;
        }
    } else return false;
}

$('#newGroupSave').on('click', function () {
    var name = $('#newGroupName').val(),
        name1c = $('#newGroupName1c').val(),
        owner = $('#newGroupOwner').val(),
        location = $('#newGroupLocation').val(),
        inv_number = $('#newGroupInvNumber').val(),
        arrayItemForNewGroup = [];

    if (name !== "" && name1c !== "" && owner !== "none" && location !== "none" && inv_number !== "") {
        if (!checkOwnerForGroup()) {
            arrayItemForNewGroup = parseCashItemsModal();
            $('#SaveNewGroup').show();
            $('#paramNewGroup').hide();
            $('#alertCoincidence').hide();
            $.ajax({
                url: "/group/create",
                data: {
                    '_token': window.csrf,
                    'arrayItems': arrayItemForNewGroup,
                    'name': name,
                    'name1c': name1c,
                    'owner': owner,
                    'location': location,
                    'inv_number': inv_number
                },
                dataType: 'json',
                type: "POST"
            }).done(function (response) {
                if (response !== undefined) {
                    if (response.status === true) {
                        $("#createGroup").modal('hide');
                        sessionStorage.clear();
                        $('#select_all').prop('checked', false);
                        $('#search_button').trigger('click');
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
    } else {
        $('#alertCoincidence').text('Вы заполнили не все поля формы').show('fade');
    }
});