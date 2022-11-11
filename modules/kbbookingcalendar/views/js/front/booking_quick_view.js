/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2015 knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */


$(document).ajaxComplete(function (event, xhr, options) {
    //changes done by tarun for the quick view issue reported by gopi
    if (typeof options.data != 'undefined') {
        setTimeout(function(){ 
            if (options.data.indexOf('quickview') != -1) {
                if ($('.product-add-to-cart').length && $('#kb_booking_product_redirect_link_div').length) {
                    $('.product-add-to-cart').append($('#kb_booking_product_redirect_link_div'));
                    $('#kb_booking_product_redirect_link_div').show();
                    if ($('.add-to-cart').length) {
                        $('.add-to-cart').attr("disabled", true);
                    }
                }
            }
        }, 300);
    }
    //changes over
});
