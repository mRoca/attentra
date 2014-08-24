$(function() {
    $.extend($.tablesorter.themes.bootstrap, {
        table      : 'table table-bordered',
        header     : 'bootstrap-header', // give the header a gradient background
        footerRow  : '',
        footerCells: '',
        icons      : '', // add "icon-white" to make them white; this icon class is added to the <i> in the header
        sortNone   : 'bootstrap-icon-unsorted',
        sortAsc    : 'icon-chevron-up glyphicon glyphicon-chevron-up',     // includes classes for Bootstrap v2 & v3
        sortDesc   : 'icon-chevron-down glyphicon glyphicon-chevron-down', // includes classes for Bootstrap v2 & v3
        active     : '', // applied when column is sorted
        hover      : '', // use custom css here - bootstrap class may not override it
        filterRow  : '', // filter row class
        even       : '', // odd row zebra striping
        odd        : ''  // even row zebra striping
    });

	$("table.tablesorter").each(function(){

		var widgets = ["uitheme", "zebra", "resizable"];

		//Affichage des zones de recherche à partir de 8 enregistremenents
		if($(this).find("> tbody > tr").length > 8)
			widgets.push("filter");

		$(this).tablesorter({
			theme : "bootstrap",
			widthFixed: false,
			headerTemplate : '{content} {icon}',
			widgets : widgets,
			widgetOptions : {
				zebra : ["even", "odd"],
				filter_reset : ".reset"
			}
		});

		//Affichage de la pagination à partir de 100 enrezgistrements
		if($(this).find("> tfoot .pager").length){
			$(this).tablesorterPager({
				container: $(".pager"),
				cssGoto  : ".pagenum",
				removeRows: false,
				output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'
			});
		}

	});
});



