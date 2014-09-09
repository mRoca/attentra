$(function () {

    var lang = $('html').attr('lang') || 'fr';

    if ( typeof $.fn.chosen !== 'undefined' ) {

        $('.chosen').chosen({
            search_contains: true,
            disable_search_threshold: 10,
            width: "100%",
            allow_single_deselect: true,
            no_results_text: lang === "fr" ? "Aucun résultat trouvé" : "",
            placeholder_text: lang === "fr" ? "Afficher les options" : ""
        });
    }

    if ( typeof $.fn.datepicker !== 'undefined' ) {
        $.fn.datepicker.dates['fr'] = {
            days: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"],
            daysShort: ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"],
            daysMin: ["D", "L", "Ma", "Me", "J", "V", "S", "D"],
            months: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
            monthsShort: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jui", "Jul", "Aou", "Sep", "Oct", "Nov", "Déc"],
            today: "Aujourd'hui",
            clear: "Effacer"
        };

        $('input.datepicker, .datepicker-container > input').datepicker({
            language: lang,
            weekStart: 1,
            format: "yyyy-mm-dd"
        });
    }

    if ( typeof $.fn.autosize !== 'undefined' ) {
        $('textarea.autosize').autosize();
    }

    var $layoutModal = $('#layout-modal');
    $('.show-hidden-value').on('click', function () {
        $layoutModal.find('.modal-body').text($(this).attr('data-hidden-value'));
        $layoutModal.modal('show');
    });


    $('table.tablesorter-knppaginator thead tr th').addClass('tablesorter-header').on('click', function (e) {
        if ( !$(e.target).is('a') && $(this).find('a').length ) {
            $(this).find('a').get(0).click();
            return false;
        }
        return true;
    });
});
