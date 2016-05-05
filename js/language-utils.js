/**
 * Utility for applying labels to all spans that have the i18n_label attribute.
 * The labels are extracted from property file bundles.
 * Uses the functilnality of jQuery.i18n.properties, so it has to be included first.
 *
 * @author Vilizar Tsonev
 */
var languageUtils = languageUtils || {};


languageUtils.applyLabelsToHTML = function(callbackFunction) {
    jQuery.i18n.properties({
        name: 'labels',
        path:'bundle/',
        language:'en',
        mode:'map',
        callback: function() {
            if(typeof(callbackFunction) !== "undefined" ) {
                callbackFunction();
            }
            var elementsToLabelize = $('span[i18n_label]');
            $.each(elementsToLabelize, function(index, element) {
                var key = $(element).attr('i18n_label');
                $(element).html($.i18n.map[key]);
            });
        }
    });
}


