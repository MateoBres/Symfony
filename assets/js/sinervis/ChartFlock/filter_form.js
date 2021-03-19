
$(document).ready(()=>{
    let optionalSelectDetail = "chartflock_optional_search_by_entity_filter";
    let baseName = "chartflock_search_by_entity_filter";
    if ($('#chartflock_group_by_entity_chart_filter_searchBy_0').length) {
        baseName = "chartflock_search_by_entity_bis_chart_filter";
    } else if ($('#chartflock_optional_search_by_entity_filter_searchBy_0').length) {
        baseName = optionalSelectDetail;
    }
    
    if ($('#' + baseName + '_searchBy_0').length) {
        addListener(baseName + '_searchBy_0', baseName);
        addListener(baseName + '_searchBy_1', baseName);
        addListener(baseName + '_searchBy_2', baseName);
        addListener(baseName + '_searchBy_3', baseName);
        addListener(baseName + '_searchBy_4', baseName);
        if (baseName == optionalSelectDetail)
            addListener(baseName + '_searchBy_5', baseName);
        else 
            moveSelectDetail(baseName);
    }

});

function moveSelectDetail(baseName) {
    let label = $("label[for='" + baseName + "_detail']");
    let select = $('#' + baseName + '_detail');
    let cell = select.parent().parent().parent();
    let newCell = cell.siblings().next();
    select.detach();
    label.detach();
    label.appendTo(newCell);
    select.appendTo(newCell);
    label.remove();
    select.remove();
}

function addListener(id, baseName) {
    var searchBy = $('#' + id);
    searchBy.change(function() {
        var $form = $(this).closest('form');
        var data = {};
        data[searchBy.attr('name')] = searchBy.val();
        $.ajax({
            url : $form.attr('action'),
            type: $form.attr('method'),
            data : data,
            success: function(html) {
                // Replace current position field ...
                $('#' + baseName + '_detail').replaceWith(
                    $(html).find('#' + baseName + '_detail')
                );
                $("label[for='" + baseName + "_detail']").replaceWith(
                    $(html).find("label[for='" + baseName + "_detail']")
                );
            }
        });
    });
}