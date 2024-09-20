var format_mosyle_yes_no = function(colNumber, row){
    var col = $('td:eq('+colNumber+')', row),
        colvar = col.text();
    colvar = colvar == '0' ? '<span class="label label-success">'+i18n.t('No')+'</span>' :
    colvar = (colvar == '1' ? '<span class="label label-danger">'+i18n.t('Yes')+'</span>' : colvar)
    col.html(colvar)
}

var format_mosyle_yes_no_rev = function(colNumber, row){
    var col = $('td:eq('+colNumber+')', row),
        colvar = col.text();
    colvar = colvar == '0' ? '<span class="label label-danger">'+i18n.t('No')+'</span>' :
    colvar = (colvar == '1' ? '<span class="label label-success">'+i18n.t('Yes')+'</span>' : colvar)
    col.html(colvar)
}

var format_mosyle_direct_device_link = function(colNumber, row){
    var col = $('td:eq('+colNumber+')', row),
        colvar = col.text();
    // Make sure we have the direct link before attempting to create the button for it
    if (colvar && colvar != ""){
        col.html('<a data-i18n-"mosyle.view_in_mosyle" class="btn btn-default btn-xs" href="'+colvar+'" target="_blank" title="'+i18n.t('mosyle.view_in_mosyle')+'">'+i18n.t('mosyle.view_in_mosyle')+'</a>')  
    }
}