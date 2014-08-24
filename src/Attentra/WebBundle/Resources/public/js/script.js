$(function () {

    var lang = $('html').attr('lang') || 'fr';

    if ( typeof $.fn.chosen !== 'undefined' ) {

        $('.chosen').chosen({
            search_contains         : true,
            disable_search_threshold: 10,
            width                   : "100%",
            allow_single_deselect   : true,
            no_results_text         : lang === "fr" ? "Aucun résultat trouvé" : "",
            placeholder_text        : lang === "fr" ? "Afficher les options" : ""
        });
    }

    if ( typeof $.fn.datepicker !== 'undefined' ) {
        $.fn.datepicker.dates['fr'] = {
            days       : ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"],
            daysShort  : ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"],
            daysMin    : ["D", "L", "Ma", "Me", "J", "V", "S", "D"],
            months     : ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
            monthsShort: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jui", "Jul", "Aou", "Sep", "Oct", "Nov", "Déc"],
            today      : "Aujourd'hui",
            clear      : "Effacer"
        };

        $('input.datepicker, .datepicker-container > input').datepicker({
            language : lang,
            weekStart: 1,
            format   : "yyyy-mm-dd"
        });
    }

    if ( typeof $.fn.redactor !== 'undefined' ) {

        var errorCallback = function (json) {
            if ( json.error ) {
                console.error && console.error(json);
                alert(json.error);
            }
        };

        $(".redactor").redactor({
            lang                    : lang,
            buttonSource            : false,
            tabSpaces               : 4,
            minHeight               : 200,
            toolbarFixed            : true,
            toolbarFixedBox         : true,
            toolbarFixedTopOffset   : 0,
            formattingTags          : ['p', 'blockquote', 'pre', 'h3', 'h4', 'h5'],
            buttons                 : ['html', '|', 'formatting', '|', 'bold', 'italic', 'underline', 'deleted', '|', 'unorderedlist', 'orderedlist', 'outdent', 'indent', '|', /*'image', 'video', 'file',*/ 'table', 'link', '|', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'horizontalrule'],
            imageUploadErrorCallback: errorCallback,
            fileUploadErrorCallback : errorCallback
        }).after('<p class="text-muted">' + (lang === "fr" ? "Vous pouvez copier/coller des images dans le sujet en utilisant le navigateur Firefox" : "You can copy and paste images with Firefox.") + '</p>');
    }

    if ( typeof $.fn.autosize !== 'undefined' ) {
        $('textarea.autosize').autosize();
    }

    var $layoutModal = $('#layout-modal');
    $('.show-hidden-value').on('click', function () {
        $layoutModal.find('.modal-body').text($(this).attr('data-hidden-value'));
        $layoutModal.modal('show');
    });
});
