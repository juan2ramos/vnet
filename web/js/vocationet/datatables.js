(function () {
    // begin first table
    var tbl = $('.datetable').dataTable({
        "sDom": "<'row'<'col-sm-6'l><'col-sm-6'f>r>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
        "sPaginationType": "bootstrap",
        "oLanguage": datatable_trans,
        "aoColumnDefs": [{
            'bSortable': false,
            'aTargets': [0]
        }]
    });

    /*
     jQuery('#sample_1 .group-checkable').change(function () {
        var set = jQuery(this).attr("data-set");
        var checked = jQuery(this).is(":checked");
        jQuery(set).each(function () {
            if (checked) {
                $(this).attr("checked", true);
            } else {
                $(this).attr("checked", false);
            }
        });
        jQuery.uniform.update(set);
    });
    //*/

    jQuery('.dataTables_wrapper .dataTables_filter input').addClass("form-control"); // modify table search input
    jQuery('.dataTables_wrapper .dataTables_length select').addClass("form-control"); // modify table per page dropdown
})();