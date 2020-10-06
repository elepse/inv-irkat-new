function addDocuments() {
    uploadCashItemsModal(parseCashItemsModal(), parseGroupCashModal());
    $('#DocSave').prop('disabled', true);
    $('.defOptionTypeDoc').prop('selected', true);
    $('.DocumentInfo').hide();
    $('#movementItems').hide();
    $('#alertMatchs').hide();
    arrayNewItems = [];
    arrayChanges = [];
    CashItems = [];
    checkOwner();
    $('#SaveNewDocument').hide();
    $('#paramsNewDocument').show();
    documentFileLabel.text('Выберите файл');
}

function parseCashItemsModal() {
    var CashItemsModal = sessionStorage.getItem('cached_array');
    if (!CashItemsModal) CashItemsModal = [];
    else CashItemsModal = JSON.parse(CashItemsModal);
    return (CashItemsModal);
}

function parseGroupCashModal() {
    var CashGroupModal = sessionStorage.getItem('cachedGroup_array');
    if (!CashGroupModal) CashGroupModal = [];
    else CashGroupModal = JSON.parse(CashGroupModal);
    return (CashGroupModal);
}

var CashItems = [];

function uploadCashItemsModal(CashItemsModal, CashGroupModal) {
    $("#addDocModal").modal('show');
    var LengthArrayCashItems = CashItemsModal.length,
        LengthArrayCashGroups = CashGroupModal.length;
    if (LengthArrayCashItems <= 0 && LengthArrayCashGroups <= 0) {
        $('#NonItems').text('Вы не выбрали элементы');
        $('#deleteChangesDocument').hide();
        $('#DocSave').prop('disabled', true);
    } else {
        $('#NonItems').text('');
        $('#deleteChangesDocument').show();
        CashItemsModal.forEach(function (i, n) {
            var CashItem = $('<tr class="DocCashItem" data-cashId="' + i.id + '"></tr>');
            i.full_name = i.last_name + " " + i.first_name + " " + i.father_name;
            CashItem.append($('<td></td>').append(i.name))
                .append($('<td></td>').append(i.name_1c))
                .append($('<td></td>').append(i.inv_number))
                .append($('<td></td>').append(i.serial_number))
                .append($('<td></td>').append(i.loc_name))
                .append($('<td></td>').append(i.status))
                .append($('<td></td>').append(i.full_name))
                .append($('<td><a href="javascript: void(0)" onclick="deleteCashItem(' + i.id + ',false)"><i class="fa fa-times" aria-hidden="true" title="Отменить выбор"></i></a></td>'));
            $('#SelectItemsModal').append(CashItem);
            CashItems.push(i);
        });
        CashGroupModal.forEach(function (i, n) {
            var CashGroup = $('<tr class="DocCashGroup" data-cashGroupId="' + i.idGroup + '"></tr>');
            CashGroup.append($('<td></td>').append(i.name_group).append('<i class="fa fa-link" aria-hidden="true"></i>'))
                .append($('<td></td>').append(i.name1c))
                .append($('<td></td>').append(i.inventory_number_group))
                .append($('<td></td>').append('-'))
                .append($('<td></td>').append(i.loc_name))
                .append($('<td></td>').append('-'))
                .append($('<td></td>').append(i.full_name))
                .append($('<td><a href="javascript: void(0)" onclick="deleteCashGroup(' + i.idGroup + ')"><i class="fa fa-times" aria-hidden="true" title="удалить элемент"></i></a></td>'));
            $('#SelectItemsModal').append(CashGroup);
            CashItems.push(i);
        })
    }
}

$('#addDocModal').on('hidden.bs.modal', function () {
    $("#SelectItemsModal").empty();
    $('#NonItems').text('');
    $('#deleteChangesDocument').hide();
    $('#addItemsDocument').hide();
    CashItems = [];
});

function deleteCashGroup(id) {
    var ArrayCashGroups = parseGroupCashModal(),
        ArrayCashItems = parseCashItemsModal(),
        deleteGroup = ArrayCashGroups.findIndex(function (i) {
            return i.idGroup = id;
        });
    if (deleteGroup === 0 && ArrayCashGroups.length == 1 && ArrayCashItems.length == 0) {
        $('#NonItems').text('Вы не выбрали элементы');
        $('#deleteChangesDocument').hide();
        $('#DocSave').prop('disabled', true);
    }
    if (deleteGroup >= 0) ArrayCashGroups.splice(deleteGroup, 1);
    if (deleteGroup >= 0) arrayChanges.splice(deleteGroup, 1);
    sessionStorage.setItem('cachedGroup_array', JSON.stringify(ArrayCashGroups));
    $('tr[data-cashGroupId=' + id + ']').hide('fade');
    setTimeout(function () {
        $('tr[data-cashGroupId=' + id + ']').remove();
    }, 400);
    $('#select_all').prop('checked', false);
    var deleteCashIgroup = CashItems.findIndex(function (i) {
        return i.idGroup == id;
    });
    if (deleteCashIgroup >= 0) CashItems.splice(deleteCashIgroup, 1);

    checkOwner();
}

function deleteCashItem(id, type) {
    var ArrayCashItems = parseCashItemsModal(),
        ArrayCashGroups = parseGroupCashModal(),
        deleteItem = ArrayCashItems.findIndex(function (i) {
            return i.id == id;
        });

    if (!type) {
        var deletedSelectItem = arrayChanges.findIndex(function (i) {
            return i.id == id;
        });
        var deleteCashItem = CashItems.findIndex(function (i) {
            return i.id == id;
        });

        if ((deleteItem === 0 && ArrayCashItems.length === 1) || deleteItem === 0 && ArrayCashGroups.length === 1) {
            $('#NonItems').text('Вы не выбрали элементы');
            $('#deleteChangesDocument').hide();
            $('#DocSave').prop('disabled', true);
        }
        if (deleteItem >= 0) ArrayCashItems.splice(deleteItem, 1);
        if (deleteCashItem >= 0) CashItems.splice(deleteCashItem, 1);
        if (deletedSelectItem >= 0) arrayChanges.splice(deletedSelectItem, 1);
        sessionStorage.setItem('cached_array', JSON.stringify(ArrayCashItems));
        checkOwner();
    } else {
        if (deleteItem === 0 && ArrayCashItems.length == 1) {
            $('#noneSelectItemsForNewGroup').show('fade');
            $('#newGroupSave').prop('disabled', true);
        }
        if (deleteItem >= 0) ArrayCashItems.splice(deleteItem, 1);
        sessionStorage.setItem('cached_array', JSON.stringify(ArrayCashItems));
        checkOwnerForGroup();
    }
    $('tr[data-cashId=' + id + ']').hide('fade');
    setTimeout(function () {
        $('tr[data-cashId=' + id + ']').remove();
    }, 400);
    $('input[data-id=' + id + ']').prop('checked', false);
    $('#select_all').prop('checked', false);
}

$('#SelectTypeDocument').change(function () {
    var doc_type = $('#SelectTypeDocument').val();
    if (doc_type == 1) {
        $('.DocumentInfo').hide('blind');
        $('#addItemsDocument').show('blind');
        $('#buttonDeleteChangeItem').hide('fade');
        $('#movementItems').hide('blind');
        $('#alertMatchs').hide('blind');
        $('#DocSave').prop('disabled', false);
    } else if (doc_type == 2) {
        CashItems = [];
        $('#addItemsDocument').hide('blind');
        $("#SelectItemsModal").empty();
        uploadCashItemsModal(parseCashItemsModal(), parseGroupCashModal());
        $('#buttonDeleteChangeItem').show('fade');
        $('.DocumentInfo').show('blind');
        $('#movementItems').show('fade');
        $('#alertMatchs').show('blind');
    } else if (doc_type == 3) {
        CashItems = [];
        $('#addItemsDocument').hide('blind');
        $("#SelectItemsModal").empty();
        uploadCashItemsModal(parseCashItemsModal(), parseGroupCashModal());
        $('.DocumentInfo').show('blind');
        $('#buttonDeleteChangeItem').show('fade');
        $('#movementItems').hide('fade');
        $('#alertMatchs').show('blind');
    } else {
        $('#movementItems').hide('blind');
        $('#addItemsDocument').hide('blind');
        $('.DocumentInfo').hide('blind');
        $('#DocSave').prop('disabled', true);
        $('#buttonDeleteChangeItem').hide('fade');
    }
});

var numNewItem = 2,
    colNewItem = 1,
    oldOwner;

$('#addNewItemDocument').on('click', function () {
    var newItem = $('.borderAddItem:last').clone(true, true);
    newItem.data("addedid", numNewItem);
    newItem.attr('data-addedid', numNewItem);
    newItem.insertAfter('.borderAddItem:last');
    $('div[data-addedid=' + numNewItem + ']').find('.formNewItem')[0].reset();
    $('div[data-addedid=' + numNewItem + ']').hide();
    $('div[data-addedid=' + numNewItem + ']').show('blind');
    $('div[data-addedid=' + numNewItem + ']').find('.allParamNewItem').show();
    $('div[data-addedid=' + numNewItem + ']').find('.cutParamNewItem').hide();
    numNewItem++;
    colNewItem++;
});

function fastenNewItem(idNewItem) {
    var nameNewItem = $('div[data-addedid=' + idNewItem + ']').find('.nameNewItem'),
        colNewItem = $('div[data-addedid=' + idNewItem + ']').find('.colNewItem'),
        nameNewItem1c = $('div[data-addedid=' + idNewItem + ']').find('.name1cNewItem'),
        invNumberNewItem = $('div[data-addedid=' + idNewItem + ']').find('.invNumberNewItem'),
        serNumberNewItem = $('div[data-addedid=' + idNewItem + ']').find('.serNumberNewItem'),
        locationNewItem = $('div[data-addedid=' + idNewItem + ']').find('.locationNewItem'),
        ownerNewItem = $('div[data-addedid=' + idNewItem + ']').find('.ownerNewItem'),
        priceNewItem = $('div[data-addedid=' + idNewItem + ']').find('.priceNewItem'),
        cutNameNewItem = $('div[data-addedid=' + idNewItem + ']').find('.cutNameNewItem'),
        cutInvNumberNewItem = $('div[data-addedid=' + idNewItem + ']').find('.cutInvNumberNewItem'),
        cutPriceNewItem = $('div[data-addedid=' + idNewItem + ']').find('.cutPriceNewItem'),
        yearNewItem = $('div[data-addedid=' + idNewItem + ']').find('.yearNewItem'),
        newItem = {
            id: idNewItem,
            col: colNewItem.val(),
            name: nameNewItem.val(),
            name1c: nameNewItem1c.val(),
            inv_number: invNumberNewItem.val(),
            ser_number: serNumberNewItem.val(),
            location: locationNewItem.val(),
            owner: ownerNewItem.val(),
            price: priceNewItem.val(),
            year: yearNewItem.val(),
        };

    if (nameNewItem.val() == "" || invNumberNewItem.val() == "" || ownerNewItem.val() == "" || priceNewItem.val() == "") {
        alert('Заполните все поля');
    } else {
        $('div[data-addedid=' + idNewItem + ']').find('.allParamNewItem').hide('blind');
        cutNameNewItem.val(nameNewItem.val());
        cutInvNumberNewItem.val(invNumberNewItem.val());
        cutPriceNewItem.val(priceNewItem.val());
        $('div[data-addedid=' + idNewItem + ']').find('.cutParamNewItem').show('blind');
        arrayNewItems.push(newItem);
    }
}

function deleteNewItem(idNewItem) {
    if (colNewItem > 1 && idNewItem != 1) {
        $('div[data-addedid=' + idNewItem + ']').hide('blind');
        setTimeout(function () {
            $('div[data-addedid=' + idNewItem + ']').remove();
        }, 400);
        colNewItem--;
        var indexDeleteNewItem = arrayNewItems.find(function (i) {
            return i.id == idNewItem;
        });
        if (indexDeleteNewItem !== undefined) arrayNewItems.splice(indexDeleteNewItem, 1);
    }
}

function editNewItem(idNewItem) {
    $('div[data-addedid=' + idNewItem + ']').find('.allParamNewItem').show('blind');
    $('div[data-addedid=' + idNewItem + ']').find('.cutParamNewItem').hide('blind');
    var indexDeleteNewItem = arrayNewItems.find(function (i) {
        return i.id == idNewItem;
    });
    arrayNewItems.splice(indexDeleteNewItem, 1);
}

function clearNewItem() {
    numNewItem = 2;
    colNewItem = 1;
    $('div[data-addedid=' + 1 + ']').find('.formNewItem')[0].reset();
    $('div[data-addedid=' + 1 + ']').find('.allParamNewItem').show('blind');
    $('div[data-addedid=' + 1 + ']').find('.cutParamNewItem').hide('blind');
    $('.borderAddItem:not(:first)').hide('blind');
    setTimeout(function () {
        $('.borderAddItem:not(:first)').remove();
    }, 500);
    arrayNewItems = [];
}

let documentFile = $('#documentFile'),
    documentFileLabel = $('#documentFileLabel');
documentFile.on('change', function () {
    let nameFile = 'Выберите файл';
    if (documentFile.val() !== "") {
        nameFile = documentFile.val().split("\\");
        nameFile = nameFile[nameFile.length - 1];
    }
    documentFileLabel.text(nameFile);
});

function checkOwner() {
    var groups = parseGroupCashModal(),
        items = parseCashItemsModal(),
        colGroups = groups.length,
        colItems = items.length,
        firstOwner = null,
        checkCoincidence = 0;

    if (colItems > 0) {
        firstOwner = items[0].last_owner;
    } else if (colGroups > 0) {
        firstOwner = groups[0].owner_group;
    } else {
        $('#movementItems').hide();
    }
    if (colItems > 0) {
        items.forEach(function (i, n) {
            if (i.last_owner !== firstOwner) {
                checkCoincidence = true;
            } else if (!checkCoincidence) {
                checkCoincidence = false;
            }
        });
    }
    if (colGroups > 0 && !checkCoincidence) {
        groups.forEach(function (i, n) {
            if (i.owner_group !== firstOwner) {
                checkCoincidence = true;
            } else if (!checkCoincidence) {
                checkCoincidence = false;
            }
        });
    }
    if (checkCoincidence) {
        $('#matchs').show('fade');
        $('#DocSave').prop('disabled', true);
    } else {
        $('#matchs').hide('fade');
        $('#DocSave').prop('disabled', false);
    }
    oldOwner = firstOwner;
    return checkCoincidence;
}

$('#DocSave').on('click', function () {
    var typeDocument = parseInt($('#SelectTypeDocument').val());
    $('#NewParamItems').hide();
    var arraySaveItems,
        newOwnerItems,
        ownerCoincidence = checkOwner();
    if (typeDocument === 1) {
        arraySaveItems = arrayNewItems;
        newOwnerItems = $('#newOwnerItems').val();
        oldOwner = null;
    } else if (typeDocument === 3) {
        arraySaveItems = CashItems;
        newOwnerItems = 4;
    } else if (typeDocument === 2) {
        arraySaveItems = CashItems;
        newOwnerItems = $('#newOwnerItems').val();
    } else {
        alert('Неверный тип документа. Обратитесь к администратору.')
    }
    if (((typeDocument === 2 || typeDocument === 3) && !ownerCoincidence) || typeDocument === 1) {
        let formData = new FormData;
        arraySaveItems = JSON.stringify(arraySaveItems);
        formData.append('documentFile', documentFile.prop('files')[0]);
        formData.append('arrayItems', arraySaveItems);
        formData.append('typeDocument', typeDocument);
        formData.append('newOwnerItems', newOwnerItems);
        typeDocument !== 1 ? formData.append('oldOwner', oldOwner) :
            $('#SaveNewDocument').show();
        $('#paramsNewDocument').hide();
        if (typeDocument === 1) {
            clearNewItem();
        }
        $.ajax({
            url: urlDocSave,
            headers: {
                'X-CSRF-TOKEN': window.csrf
            },
            data: formData,
            dataType: 'json',
            type: "POST",
            processData: false,
            contentType: false
        }).done(function (response) {
            if (response !== undefined) {
                if (response.status === true) {
                    $("#addDocModal").modal('hide');
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
});

function callAddNewOwnerModal() {
    $('#addNewOwnerModal').modal('show');
    $('#formAddNewOwner')[0].reset();
}

function addNewOwner() {
    var firstName = $('#firstNameNewOwner').val(),
        lastName = $('#lastNameNewOwner').val(),
        fatherName = $('#fatherNameNewOwner').val();
    if (firstName !== '' && lastName !== '') {
        $('#paramNewAddOwner').hide();
        $('#loadingAddNewOwnerModal').show();
        $.ajax({
            url: "/addOwner",
            data: {
                '_token': window.csrf,
                'firstName': firstName,
                'lastName': lastName,
                'fatherName': fatherName
            },
            dataType: 'json',
            type: "POST"
        }).done(function (response) {
            if (response !== undefined) {
                if (response.status === true) {
                    var fullname = firstName + ' ' + lastName + ' ' + fatherName;
                    updateOwners(response.idOwner, fullname);
                    $('#paramNewAddOwner').show();
                    $('#loadingAddNewOwnerModal').hide();
                    $('#addNewOwnerModal').modal('hide');
                } else if (response.status === false || response.status === 'error') {
                    alert(response.error);
                } else {
                    alert('Ошибка запроса. Обратитесь к администратору.');
                }
            } else {
                alert('Ошибка запроса. Обратитесь к администратору.');
            }
        });
    } else alert('Вы не заполнили обязательные поля')
}

function callAddLocationModal() {
    $('#addNewLocationModal').modal('show');
    $('#formAddNewLocation')[0].reset();
}

function addNewLocation() {
    var nameNewLocation = $('#nameAddNewLocation').val();
    if (nameNewLocation !== '') {
        $('#loadingAddNewLocationModal').show();
        $('#paramAddNewLocation').hide();
        $.ajax({
            url: "/addLocation",
            data: {
                '_token': window.csrf,
                'nameLocation': nameNewLocation
            },
            dataType: 'json',
            type: "POST"
        }).done(function (response) {
            if (response !== undefined) {
                if (response.status === true) {
                    updateLocations(response.idLocation, nameNewLocation);
                    $('#paramAddNewLocation').show();
                    $('#loadingAddNewLocationModal').hide();
                    $('#addNewLocationModal').modal('hide');
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