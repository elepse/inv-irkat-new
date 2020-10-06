var searchPage = 1,
    sortTo,
    sortBy;
$(function () {
    var owner = $('#search_owner'),
        location = $('#search_loc'),
        name = $('#search_name'),
        ser = $('#search_ser_num'),
        Name1C = $('#search_Name1c'),
        status = $('#search_status'),
        invStatus = $('#inv_status'),
        inv = $('#search_inv_num'),
        icon,
        com = $('#search_com'),
        pages = $('#search_pages'),
        loading = $('#search_loading'),
        search = $('#search_button'),
        result = $('#result'),
        clean = $('#clean_search');
    search.on('click', function () {
        searchItems();
    });

    function load(status) {
        if (status) {
            loading.show();
            result.hide();
            pages.hide();
            search.hide();
            return;
        }

        loading.hide();
        result.show('Fade');
        pages.show();
        search.show();
    }

    Name1C.change(function () {
        searchPage = 1;
        searchItems()
    });
    status.change(function () {
        searchPage = 1;
        searchItems()
    });

    owner.change(function () {
        searchPage = 1;
        searchItems()
    });

    com.change(function () {
        searchPage = 1;
        searchItems()
    });

    ser.change(function () {
        searchPage = 1;
        searchItems()
    });

    name.change(function () {
        searchPage = 1;
        searchItems()
    });

    inv.change(function () {
        searchPage = 1;
        searchItems()
    });
    invStatus.change(function () {
        searchPage = 1;
        searchItems()
    });

    location.change(function () {
        searchPage = 1;
        searchItems()
    });

    clean.click(function () {
        $('#searchParams')[0].reset();
        searchItems();
    });

    function searchItems(sortTo, sortBy) {
        items = [];
        load(true);
        $.ajax({
            url: urlSearch,
            data: {
                location: location.val(),
                owner: owner.val(),
                inv: inv.val(),
                name: name.val(),
                ser: ser.val(),
                com: com.val(),
                Name1C: Name1C.val(),
                status: status.val(),
                invStatus: invStatus.val(),
                page: searchPage,
                sortTo: sortTo,
                sortBy: sortBy,
            },
            dataType: 'json',
            type: "GET"
        }).done(function (r) {
            if (!r.status) alert('error!');
            result.empty();
            r.items.data.forEach(function (i, n = 1) {
                var item = $('<tr></tr>');
                if (i.inv_status == 1) {
                    item.css('background-color', '#d4e7c4');
                } else if (i.inv_status == 2){
                    item.css('background-color', '#ffcfd3');
                } else {
                    item.css('background-color', '#ffe0a4')
                }

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
                var checkbox = $('<input type="checkbox" class="checkboxItemsMain" data-id="' + i.id + '">');
                i.full_name = i.last_name + " " + i.first_name + " " + i.father_name;
                iconChain = $('<i class="fa fa-link" style="font-size: 16px; cursor: pointer;" onclick="showGroupItem($(this).data(\'idgroup\'))" data-idgroup="' + i.group_id + '" aria-hidden="true"></i>');

                if (!!i.group_id) {
                    icon = iconChain;
                } else {
                    icon = checkbox;
                }
                n++;
                if (i.count > 1) i.name = '(' + i.count + ')' + i.name;
                item.append($('<td id="select"></td>').append(icon))
                    .append($('<td></td>').append(n))
                    .append($('<td></td>').append($('<a href="javascript: void(0)" onclick="editItem(' + i.id + ')"></a>').append(i.name)))
                    .append($('<td></td>').append(i.name_1c))
                    .append($('<td></td>').append(i.inv_number))
                    .append($('<td></td>').append(i.serial_number))
                    .append($('<td></td>').append(i.year))
                    .append($('<td></td>').append(i.loc_name))
                    .append($('<td></td>').append(i.status))
                    .append($('<td></td>').append(i.full_name))
                    .append($('<td></td>').append(i.price))
                    .append($('<td></td>').append(i.last_comment))
                    .append($('<td><i class="fa fa-history" style="color: #007bff;" aria-hidden="true" onclick="callHistoryModal(' + i.id + ',1)" title="Просмотреть историю"></td>'));
                result.append(item);
                items.push(i);
                checkbox[0].checked = searchCache(i.id);
            });

            $('input[class="checkboxItemsMain"]').click(function () {
                if (!$(this).is(':checked')) {
                    $('#select_all').prop('checked', false);
                }
            });

            function searchCache(id) {
                var check_array = sessionStorage.getItem('cached_array');
                if (!check_array) return false;
                check_array = JSON.parse(check_array);
                if (check_array) {
                    return !!check_array.find(function (i) {
                        return i.id == id;
                    });
                }
                return false;
            }

            pages.empty();
            for (var page = 1; page <= r.items.last_page; page++) {
                var pageDom = $('<button class="btn btn-outline-primary page" style="margin: 2px;"></button>');
                pageDom.append(page);
                if (page === r.items.current_page) {
                    pageDom.addClass("active")
                }
                pageDom.data('page', page);
                pages.append(pageDom);
            }
        })
            .always(function () {
                load(false);
            });
        updateTableOfSelectItems();
    }

    $(document).on('click', '#search_pages .page', function () {
        searchPage = $(this).data('page');
        searchItems();
    });
    searchItems();

    $('.sort').on('click', function () {
        let data = $(this).data('sort');
        if (data !== sortBy) {
            sortBy = data;
            sortTo = -1;
        } else {
            sortTo = sortTo * -1;
        }

        $('.sort').children('.fa').remove();

        if (sortTo === -1) {
            $(this).append('<i class="fa fa-arrow-down" aria-hidden="true"></i>')
        } else {
            $(this).append('<i class="fa fa-arrow-up" aria-hidden="true"></i>')
        }
        searchItems(sortTo, sortBy);
    })

});

$('#select_all').click(function () {
    var checked = $(this).is(':checked');
    var checkboxes = $('#result input:checkbox');
    checkboxes.prop('checked', checked);
    checkboxes.each(function (i, item) {
        $(item).trigger('change');
    })


});
$(document).on('change', '.result [type=checkbox]', function () {
    var id = $(this).data('id');
    checkboxChange(id, !this.checked);
});

var items = [];

function checkboxChange(id, isRemove) {
    if (isRemove === undefined) isRemove = false;
    var array = sessionStorage.getItem('cached_array');
    if (!array) array = [];
    else array = JSON.parse(array);
    var index = array.findIndex(function (i) {
        return i.id == id;
    });
    if (isRemove) {
        if (index >= 0) array.splice(index, 1);
    } else if (index < 0) {
        var row = items.find(function (i) {
            return i.id == id;
        });
        array.push(row);
    }
    sessionStorage.setItem('cached_array', JSON.stringify(array));
    updateTableOfSelectItems();

}


var selectCount = 0;

function updateTableOfSelectItems() {
    let items = parseCashItemsModal(),
        groups = parseGroupCashModal(),
        itemsTable = $('#selectItemsTable'),
        groupsTable = $('#selectGroupsTable');
    selectCount = groups.length + items.length;
    itemsTable.children('#selectItems').empty();
    groupsTable.children('#selectGroups').empty();
    $('#countSelected').text(selectCount);
    if (items.length !== 0) {
        itemsTable.show('blind');
        items.forEach(function (item) {
            let tr = $("<tr class='table-success'></tr>");
            tr.append($('<td></td>').append('<i class="fa fa-trash-o" style="color: #e93335; cursor: pointer;" onclick="checkboxChange(' + item.id + ', true)" data-idselectitem="' + item.id + '" aria-hidden="true"></i>'))
                .append($('<td></td>').append($('<a href="javascript: void(0)" onclick="editItem(' + item.id + ')"></a>').append(item.name)))
                .append($('<td></td>').append(item.name_1c))
                .append($('<td></td>').append(item.inv_number))
                .append($('<td></td>').append(item.serial_number))
                .append($('<td></td>').append(item.create_time.substring(0, 4)))
                .append($('<td></td>').append(item.loc_name))
                .append($('<td></td>').append(item.status))
                .append($('<td></td>').append(item.full_name))
                .append($('<td></td>').append(item.price))
                .append($('<td></td>').append(item.last_comment))
                .append($('<td><i class="fa fa-history" style="color: #007bff;" aria-hidden="true" onclick="callHistoryModal(' + item.id + ',1)" title="Просмотреть историю"></td>'));
            itemsTable.append(tr);
        });
    } else {
        itemsTable.hide('blind');
    }
    if (groups.length !== 0) {
        groupsTable.show('blind');
        groups.forEach(function (group) {
            let tr = $("<tr class='table-success'></tr>");
            tr.append($('<td></td>').append($('<i class="fa fa-link" style="font-size: 16px; cursor: pointer;" onclick="showGroupItem(' + group.idGroup + ')" aria-hidden="true"></i>')))
                .append($('<td></td>').append(group.name_group))
                .append($('<td></td>').append(group.name1c))
                .append($('<td></td>').append(group.inventory_number_group))
                .append($('<td></td>').append(group.loc_name))
                .append($('<td></td>').append(group.full_name));
            groupsTable.append(tr);
        })
    } else {
        groupsTable.hide('blind');
    }
    //TODO: ИСППРАВИТЬ
    if (selectCount == 0) {
        selectTableBtn(1);
    }
}

var selectTables = $('#selectTables');

function selectTableBtn(status) {
    if (selectCount > 0) {
        if (status === 0) {                    // 1 - активная; 0 - не активная
            $('#btnSelected').data('status', 1);
            selectTables.show('blind');
        } else {
            $('#btnSelected').data('status', 0);
            selectTables.hide('blind');
        }
    }
}