/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2019 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 *
 */

$(document).ready(function () {
    //disable quantity block in cart page
    $('#cart .js-cart .js-cart-line-product-quantity').each(function () {
        var id_product = $(this).data('product-id');
        if (typeof kb_cart_validate != 'undefined') {
            var kb_cart_details = $.parseJSON(kb_cart_prod);
            if ($.inArray(id_product, kb_cart_details) == -1)
            {
                $(this).attr('disabled', true);
            }
        }
    });

    //hide reorder button from the order history page
    if ($('#history #content-wrapper table').length && prestashop.page.page_name == 'history') {
        if (typeof history_reorder != 'undefined') {
            $('.order-actions a').each(function () {
                if ($(this).data('link-action') != "view-order-details") {
                    var sPageURL = $(this).attr('href');
                    var sURLVariables = sPageURL.split('&');
                    for (var i = 0; i < sURLVariables.length; i++)
                    {
                        var sParameterName = sURLVariables[i].split('=');
                        if (sParameterName[0] == 'id_order') {
                            var kb_order_details = $.parseJSON(kb_history_orders);
                            if ($.inArray(sParameterName[1], kb_order_details) != -1)
                            {
                                $(this).remove();
                            }
                        }
                    }

                }
            });
        }
    }

    //hide reorder link from the order detail of the history page
    if ($('#order-detail #content-wrapper #order-infos').length && prestashop.page.page_name == 'order-detail') {
        if (typeof history_reorder != 'undefined') {
            $('.box a').each(function () {
                var sPageURL = $(this).attr('href');
                if (typeof sPageURL != 'undefined') {
                    var sURLVariables = sPageURL.split('&');
                    for (var i = 0; i < sURLVariables.length; i++)
                    {
                        var sParameterName = sURLVariables[i].split('=');
                        if (sParameterName[0] == 'id_order') {
                            var kb_order_details = $.parseJSON(kb_history_orders);
                            if ($.inArray(sParameterName[1], kb_order_details) != -1)
                            {
                                $(this).remove();
                            }
                        }
                    }

                }
            });
        }
    }


    if ($('input[name="kb_booking_product"]').length) {
        $('.product-customization').hide();
    }

    var kb_dateformat = 'yyyy-mm-dd';
    var kb_minview = 2;
    if ($('input[name="kb_product_type"]').length) {
        if ($('input[name="kb_product_type"]').val() == 'hourly_rental') {
            kb_minview = 0;
            kb_dateformat = 'yyyy-mm-dd hh:ii';
        }
    }
    if (typeof (current_date) == 'undefined') {
        var current_date = new Date();
    }

    $('input[name="kb_check_out"]').datetimepicker({
        daysOfWeekDisabled: $('input[name="kb_disable_days"]').val(),
        startDate: current_date,
        minView: kb_minview,
        format: kb_dateformat,
        autoclose: 1,
        weekStart: 1,
      onRenderDay: function (date) {
            var yr = date.getUTCFullYear();
            var month = date.getUTCMonth() + 1;
            var day = date.getUTCDate();
            if (month < 10) {
                month = '0' + month;
            }
            if (date.getUTCDate() < 10) {
                day = '0' + date.getUTCDate();
            }

            var newDate = yr + '-' + month + '-' + day;
            if (typeof kb_render_dates != 'undefined') {
                var date_valid = false;
                var render_dates = $.parseJSON(kb_render_dates);
                if (render_dates != '') {
                    for (var i in render_dates) {
                        if (newDate >= render_dates[i]['from_date'] && newDate <= render_dates[i]['to_date']) {
                            date_valid = true;
                        }
                    }

                    if (!date_valid) {
                        return ['disabled'];
                    }
                }
            }
        },
    }).on('changeDate', function (ev) {
        $('.kb-error-message').remove();
        if (ev != '') {
            if (ev.date != '') {
                checkCheckInOutDates(ev, 'checkout');
            }
        }
    });

    $('input[name="kb_check_in"]').datetimepicker({
        daysOfWeekDisabled: $('input[name="kb_disable_days"]').val(),
        startDate: current_date,
        minView: kb_minview,
        format: kb_dateformat,
        autoclose: 1,
        weekStart: 1,
        onRenderDay: function (date) {
            var yr = date.getUTCFullYear();
            var month = date.getUTCMonth() + 1;
            var day = date.getUTCDate();
            if (month < 10) {
                month = '0' + month;
            }
            if (date.getUTCDate() < 10) {
                day = '0' + date.getUTCDate();
            }

            var newDate = yr + '-' + month + '-' + day;
            if (typeof kb_render_dates != 'undefined') {
                var date_valid = false;
                var render_dates = $.parseJSON(kb_render_dates);
                if (render_dates != '') {
                    for (var i in render_dates) {
                        if (newDate >= render_dates[i]['from_date'] && newDate <= render_dates[i]['to_date']) {
                            date_valid = true;
                        }
                    }

                    if (!date_valid) {
                        return ['disabled'];
                    }
                }
            }
        },
    }).on('changeDate', function (ev) {
        $('.kb-error-message').remove();
        if (ev != '') {
            if (ev.date != '') {
                if ($('input[name="kb_period_type"]').val() == 'date_time') {
                    displayKbTimeSlot(ev);
                } else {
                    checkCheckInOutDates(ev, 'checkin');
                }
            }
        }
    });
    if (typeof kb_is_booking_product != 'undefined') {
        $('#product .product-actions').find('.product-add-to-cart').hide();
        $('#product .product-prices').remove();
        $('.kb-booking-product-block').show().insertBefore($('#product .product-actions').firstChild)
    }

    renderHotelFacilities('.kb_slider');

    $('#kb-submit-booking-product-cart').click(function () {
        $('.kb-error-message').remove();
        var kb_checkin_date = '';
        var kb_checkout_date = '';
        var kb_checkout_selected = {};
        var kb_checkin_selected = {};
        var product_type = $('input[name="kb_product_type"]').val();
        kb_checkin_date = new Date($('input[name="kb_check_in"]').val());
        kb_checkin_selected = {
            'date': kb_checkin_date.getDate(),
            'day': kb_checkin_date.getDay(),
            'year': kb_checkin_date.getFullYear(),
            'hours': kb_checkin_date.getHours(),
            'minutes': kb_checkin_date.getMinutes(),
            'months': kb_checkin_date.getMonth(),
            'seconds': kb_checkin_date.getSeconds(),
        };

        if ($('input[name="kb_check_in"]').val() == '') {
            $('.booking-product-checkin-block').append('<div class="col-xs-12 alert alert-danger kb-error-message">' + kb_checkin_checkout_empty + '</div>');
            return;
        }
        if ($('input[name="kb_check_out"]').length) {
            if ($('input[name="kb_check_out"]').val() == '') {
                $('.booking-product-checkin-block').append('<div class="col-xs-12 alert alert-danger kb-error-message">' + kb_checkin_checkout_empty + '</div>');
                return;
            } else {
                kb_checkout_date = new Date($('input[name="kb_check_out"]').val());
                if (kb_checkin_date != '' && kb_checkout_date != '') {
                    if (parseInt(Date.parse(kb_checkout_date)) < parseInt(Date.parse(kb_checkin_date))) {
                        $('.kb-hotel-room-popup-date-block').append('<div class="col-xs-12 alert alert-danger kb-error-message">' + kb_checkin_checkout_valid + '</div>');
                        $('.booking-product-checkin-block').append('<div class="col-xs-12 alert alert-danger kb-error-message">' + kb_checkin_checkout_valid + '</div>');
                        return;
                    }
                }

            }

            kb_checkout_selected = {
                'date': kb_checkout_date.getDate(),
                'day': kb_checkout_date.getDay(),
                'year': kb_checkout_date.getFullYear(),
                'hours': kb_checkout_date.getHours(),
                'minutes': kb_checkout_date.getMinutes(),
                'months': kb_checkout_date.getMonth(),
                'seconds': kb_checkout_date.getSeconds(),
            };

        }

        var time_slot = {};
        if ($('select[name="kb_checkin_time_slot"]').length) {
            time_slot = {
                time: $('select[name="kb_checkin_time_slot"]').val(),
                price: $('select[name="kb_checkin_time_slot"] option:selected').data('price'),
            };
        } else {


        }
        $.ajax({
            type: 'post',
            dataType: 'json',
            cache: true,
            beforeSend: function () {
//                        $('body').addClass("loading");
            },
            url: kb_cart_url,
            data: {
                ajax: true,
                addCart: true,
                kb_checkin_selected: kb_checkin_selected,
                kb_checkout_selected: kb_checkout_selected,
                id_booking_product: $('input[name="kb_booking_product"]').val(),
                id_product: $('input[name="kb_id_product"]').val(),
                product_type: $('input[name="kb_product_type"]').val(),
                period_type: $('input[name="kb_period_type"]').val(),
                service_type: $('input[name="kb_service_type"]').val(),
                price: $('input[name="kb_product_price"]').val(),
                qty: $('.kb_product_qty').val(),
                time_slot: time_slot
            },
            success: function (rec) {
                if (rec['success']) {
                    if (rec['id_customization'] != '') {
                        $('input[name="id_customization"]').val(rec['id_customization']);
                    }
                    $('#blockcart-modal').remove();
                    $('#quantity_wanted').val($('.kb_product_qty').val());
                    $('.add-to-cart').removeAttr('disabled');
                    $('.add-to-cart').click();
                    $('.product-customization').hide();
                } else if (rec['error'] != '') {
                    $('.booking-product-checkin-block').append('<div class="col-xs-12 alert alert-danger kb-error-message">' + rec['error'] + '</div>');
                }
            },
            complete: function () {
            }
        });
    });



    $('.kb_product_qty').on('change', function () {
        var kb_qty = $(this).val();
        $('.kb-error-message').remove();
        if (kb_qty > 0) {
            if ($('input[name="kb_period_type"]').val() == 'date_time') {
                var check_in_date = $('input[name="kb_check_in"]').val().trim();
                var time_slot = {};
                if ($('select[name="kb_checkin_time_slot"]').length) {
                    time_slot = {
                        time: $('select[name="kb_checkin_time_slot"]').val(),
                        price: $('select[name="kb_checkin_time_slot"] option:selected').data('price'),
                    };
                } else {


                }
                if (check_in_date != '') {
                    var kb_checkdate = new Date(check_in_date);
                    var selected_date = {};
                    selected_date['date'] = kb_checkdate.getDate();
                    selected_date['day'] = kb_checkdate.getDay();
                    selected_date['year'] = kb_checkdate.getFullYear();
                    selected_date['hours'] = kb_checkdate.getHours();
                    selected_date['minutes'] = kb_checkdate.getMinutes();
                    selected_date['months'] = kb_checkdate.getMonth();
                    selected_date['seconds'] = kb_checkdate.getSeconds();
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        cache: true,
                        beforeSend: function () {
//                        $('body').addClass("loading");
                        },
                        url: kb_cart_url,
                        data: {
                            ajax: true,
                            validateQuantity: true,
//                            date: ev.date,
                            kb_checkin_selected: selected_date,
                            id_booking_product: $('input[name="kb_booking_product"]').val(),
                            id_product: $('input[name="kb_id_product"]').val(),
                            product_type: $('input[name="kb_product_type"]').val(),
                            period_type: $('input[name="kb_period_type"]').val(),
                            time_slot: time_slot,
                            qty: kb_qty
                        },
                        success: function (rec) {
                            if (rec['success']) {
                            } else if (rec['error'] != '') {
                                $('.booking-product-checkin-block').append('<div class="col-xs-12 alert alert-danger kb-error-message">' + rec['error'] + '</div>');
                            }
                        },
                        complete: function () {
//                        $('body').removeClass("loading");
                        }
                    });
                }
            } else {
                var kb_checkin_date = new Date($('input[name="kb_check_in"]').val());
                var kb_checkout_date = new Date($('input[name="kb_check_out"]').val());
                if ($('input[name="kb_check_in"]').val() != '' && $('input[name="kb_check_out"]').val() != '') {
                    if (parseInt(Date.parse(kb_checkout_date)) < parseInt(Date.parse(kb_checkin_date))) {
                        $('.booking-product-checkin-block').append('<div class="col-xs-12 alert alert-danger kb-error-message">' + kb_checkin_checkout_valid + '</div>');
                        return;
                    }
                    if ($('input[name="kb_product_type"]').val() == 'hotel_booking') {
                        kb_qty = $('.kb_hotel_room_qty').val();
                    }
                    var kb_checkin_selected = {
                        'date': kb_checkin_date.getDate(),
                        'day': kb_checkin_date.getDay(),
                        'year': kb_checkin_date.getFullYear(),
                        'hours': kb_checkin_date.getHours(),
                        'minutes': kb_checkin_date.getMinutes(),
                        'months': kb_checkin_date.getMonth(),
                        'seconds': kb_checkin_date.getSeconds(),
                    };
                    var kb_checkout_selected = {
                        'date': kb_checkout_date.getDate(),
                        'day': kb_checkout_date.getDay(),
                        'year': kb_checkout_date.getFullYear(),
                        'hours': kb_checkout_date.getHours(),
                        'minutes': kb_checkout_date.getMinutes(),
                        'months': kb_checkout_date.getMonth(),
                        'seconds': kb_checkout_date.getSeconds(),
                    };
                    var time_slot = {};
                    if ($('select[name="kb_checkin_time_slot"]').length) {
                        time_slot = {
                            time: $('select[name="kb_checkin_time_slot"]').val(),
                            price: $('select[name="kb_checkin_time_slot"] option:selected').data('price'),
                        };
                    } else {
                    }
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        cache: true,
                        beforeSend: function () {
//                        $('body').addClass("loading");
                        },
                        url: kb_cart_url,
                        data: {
                            ajax: true,
                            validateQuantity: true,
                            kb_checkin_selected: kb_checkin_selected,
                            kb_checkout_selected: kb_checkout_selected,
                            id_booking_product: $('input[name="kb_booking_product"]').val(),
                            id_product: $('input[name="kb_id_product"]').val(),
                            product_type: $('input[name="kb_product_type"]').val(),
                            period_type: $('input[name="kb_period_type"]').val(),
                            qty: kb_qty,
                            id_hotel_room: $('input[name="kb_room_selected_hotel"]').val(),
                            time_slot: time_slot
                        },
                        success: function (rec) {
                            if (rec['success']) {
                                $('input[name="kb_product_price"]').val(rec['price']);
                                $('.kb-booking-product-price').html(rec['display_price']);
                                $('input[name="kb_product_date_valid"]').val('1');
                            } else if (rec['error'] != '') {
                                $('.booking-product-checkin-block').append('<div class="col-xs-12 alert alert-danger kb-error-message">' + rec['error'] + '</div>');
                            }
                        },
                        complete: function () {
//                        $('body').removeClass("loading");
                        }
                    });
                }
            }
        }
    });


    $('select#kb-product-room-category-search').change(function () {
        searchRoomByCategory();
    });

});

$(document).ajaxComplete(function (event, xhr, settings) {
    if (typeof (settings.url) != "undefined") {
        if ($('select[name="kb_checkin_time_slot"]').length) {
            $('select[name="kb_checkin_time_slot"]').on('change', function () {
                var kb_price = $(this).find(':selected').data('price');
                var kb_display_price = $(this).find(':selected').data('display-price');
                $('input[name="kb_product_price"]').val(kb_price);
                $('.kb-booking-product-price').html(kb_display_price);
                $('input[name="kb_product_date_valid"]').val('1');
            }).change();
        }
        if ($('#cart .js-cart .js-cart-line-product-quantity').length) {
            $('#cart .js-cart .js-cart-line-product-quantity').each(function () {
                var id_product = $(this).data('product-id');
                if (typeof kb_cart_validate != 'undefined') {
                    var kb_cart_details = $.parseJSON(kb_cart_prod);
                    if ($.inArray(id_product, kb_cart_details) == -1)
                    {
                        $(this).attr('disabled', true);
                    }
                }
            });
        }
    }
});

//function to display time slots
function displayKbTimeSlot(ev)
{
    var kb_checkdate = new Date(ev.date);
    var selected_date = {};
    selected_date['date'] = kb_checkdate.getDate();
    selected_date['day'] = kb_checkdate.getDay();
    selected_date['year'] = kb_checkdate.getFullYear();
    selected_date['hours'] = kb_checkdate.getHours();
    selected_date['minutes'] = kb_checkdate.getMinutes();
    selected_date['months'] = kb_checkdate.getMonth();
    selected_date['seconds'] = kb_checkdate.getSeconds();
    $.ajax({
        type: 'post',
        dataType: 'json',
        cache: true,
        beforeSend: function () {
//                        $('body').addClass("loading");
        },
        url: kb_cart_url,
        data: {
            ajax: true,
            displayTimeSlots: true,
            date: ev.date,
            kb_checkin_selected: selected_date,
            id_booking_product: $('input[name="kb_booking_product"]').val(),
            id_product: $('input[name="kb_id_product"]').val(),
            product_type: $('input[name="kb_product_type"]').val(),
            period_type: $('input[name="kb_period_type"]').val(),
            qty: $('.kb_product_qty').val()
        },
        success: function (rec) {
            $('.kb-timeslot-block').remove();
            if (rec['success']) {
                $('.booking-product-checkin-block').append(rec['content']);
            } else if (rec['error'] != '') {
                $('input[name="kb_check_in"]').closest('.form-group').after('<div class="col-xs-12 alert alert-danger kb-error-message">' + rec['error'] + '</div>');
            }
        },
        complete: function () {
//                        $('body').removeClass("loading");
        }
    });
}

//function to submit add to cart of rooms
function submitRoomHotelCart() {
    $('.kb-error-message').remove();
    if ($('input[name="kb_product_date_valid"]').val() == '1') {
        var kb_checkin_date = '';
        var kb_checkout_date = '';
        var kb_checkout_selected = {};
        var kb_checkin_selected = {};
        var product_type = $('input[name="kb_product_type"]').val();
        kb_checkin_date = new Date($('input[name="kb_room_check_in"]').val());
        kb_checkin_selected = {
            'date': kb_checkin_date.getDate(),
            'day': kb_checkin_date.getDay(),
            'year': kb_checkin_date.getFullYear(),
            'hours': kb_checkin_date.getHours(),
            'minutes': kb_checkin_date.getMinutes(),
            'months': kb_checkin_date.getMonth(),
            'seconds': kb_checkin_date.getSeconds(),
        };

        kb_checkout_date = new Date($('input[name="kb_room_check_out"]').val());
        kb_checkout_selected = {
            'date': kb_checkout_date.getDate(),
            'day': kb_checkout_date.getDay(),
            'year': kb_checkout_date.getFullYear(),
            'hours': kb_checkout_date.getHours(),
            'minutes': kb_checkout_date.getMinutes(),
            'months': kb_checkout_date.getMonth(),
            'seconds': kb_checkout_date.getSeconds(),
        };
        $('.kb-error-message').remove();
        $.ajax({
            type: 'post',
            dataType: 'json',
            cache: true,
            beforeSend: function () {
//                        $('body').addClass("loading");
            },
            url: kb_cart_url,
            data: {
                ajax: true,
                addCart: true,
                kb_checkin_selected: kb_checkin_selected,
                kb_checkout_selected: kb_checkout_selected,
                id_booking_product: $('input[name="kb_booking_product"]').val(),
                id_product: $('input[name="kb_id_product"]').val(),
                product_type: $('input[name="kb_product_type"]').val(),
                period_type: $('input[name="kb_period_type"]').val(),
                id_hotel_room: $('input[name="kb_room_selected_hotel"]').val(),
                price: $('input[name="kb_product_price"]').val(),
                qty: $('.kb_hotel_room_qty').val()
            },
            success: function (rec) {
                if (rec['success']) {
                    if (rec['id_customization'] != '') {
                        $('input[name="id_customization"]').val(rec['id_customization']);
                    }
                    $('#quantity_wanted').val($('.kb_hotel_room_qty').val());
                    $('.add-to-cart').removeAttr('disabled');
                    $('.add-to-cart').click();
                    $('#kbrenderHotelRoomBlockModel').modal('toggle');
                    $('.product-customization').hide();
                } else if (rec['error'] != '') {
                    $('.kb-hotel-room-popup-date-block').append('<div class="col-xs-12 alert alert-danger kb-error-message">' + rec['error'] + '</div>');
                }
            },
            complete: function () {
//                        $('body').removeClass("loading");
            }
        });
    } else {
        $('.kb-hotel-room-popup-date-block').append('<div class="col-xs-12 alert alert-danger kb-error-message">' + kb_checkin_checkout_empty + '</div>');
    }

}

//validate room/s checkin checkout date
function checkRoomCheckInOutDates(ev, date_field, id_hotel_room) {
    var kb_checkin_date = '';
    var kb_checkout_date = '';
    $('.kb-error-message').remove();
    $('input[name="kb_product_date_valid"]').val('0');
    if (date_field == 'checkin' && $('input[name="kb_room_check_out"]').val() != '') {
        kb_checkin_date = new Date(ev.date);
        kb_checkout_date = new Date($('input[name="kb_room_check_out"]').val());
    } else if (date_field == 'checkout' && $('input[name="kb_room_check_in"]').val() != '') {
        kb_checkout_date = new Date(ev.date);
        kb_checkin_date = new Date($('input[name="kb_room_check_in"]').val());
    }



    if (kb_checkin_date != '' && kb_checkout_date != '') {
        if (parseInt(Date.parse(kb_checkout_date)) < parseInt(Date.parse(kb_checkin_date))) {
            $('.kb-hotel-room-popup-date-block').append('<div class="col-xs-12 alert alert-danger kb-error-message">' + kb_checkin_checkout_valid + '</div>');
            return;
        }
        var kb_checkin_selected = {
            'date': kb_checkin_date.getDate(),
            'day': kb_checkin_date.getDay(),
            'year': kb_checkin_date.getFullYear(),
            'hours': kb_checkin_date.getHours(),
            'minutes': kb_checkin_date.getMinutes(),
            'months': kb_checkin_date.getMonth(),
            'seconds': 0,
        };
        var kb_checkout_selected = {
            'date': kb_checkout_date.getDate(),
            'day': kb_checkout_date.getDay(),
            'year': kb_checkout_date.getFullYear(),
            'hours': kb_checkout_date.getHours(),
            'minutes': kb_checkout_date.getMinutes(),
            'months': kb_checkout_date.getMonth(),
            'seconds': 0,
        };
        $.ajax({
            type: 'post',
            dataType: 'json',
            cache: true,
            beforeSend: function () {
            },
            url: kb_cart_url,
            data: {
                ajax: true,
                validateCheckInDate: true,
                id_hotel_room: id_hotel_room,
//            date: ev.date,
                kb_checkin_selected: kb_checkin_selected,
                kb_checkout_selected: kb_checkout_selected,
                id_booking_product: $('input[name="kb_booking_product"]').val(),
                id_product: $('input[name="kb_id_product"]').val(),
                product_type: $('input[name="kb_product_type"]').val(),
                period_type: $('input[name="kb_period_type"]').val(),
                qty: $('.kb_hotel_room_qty').val()
            },
            success: function (rec) {
                if (rec['success']) {
                    $('input[name="kb_product_price"]').val(rec['price']);
                    $('.kb-booking-product-price').html(rec['display_price']);
                    $('input[name="kb_product_date_valid"]').val('1');
                } else if (rec['error'] != '') {
                    $('.kb-hotel-room-popup-date-block').append('<div class="col-xs-12 alert alert-danger kb-error-message">' + rec['error'] + '</div>');
                }
            },
            complete: function () {
//                        $('body').removeClass("loading");
            }
        });
    } else {
        $('.kb-hotel-room-popup-date-block').append('<div class="col-xs-12 alert alert-danger kb-error-message">' + kb_checkin_checkout_empty + '</div>');
        return;
    }
}

//validate the checkin checkout date
function checkCheckInOutDates(ev, date_field)
{
    $('.kb-error-message').remove();
    var kb_checkin_date = '';
    var kb_checkout_date = '';
    if (date_field == 'checkin' && $('input[name="kb_check_out"]').val() != '') {
        kb_checkin_date = new Date(ev.date);
        kb_checkout_date = new Date($('input[name="kb_check_out"]').val());
    } else if (date_field == 'checkout' && $('input[name="kb_check_in"]').val() != '') {
        kb_checkout_date = new Date(ev.date);
        kb_checkin_date = new Date($('input[name="kb_check_in"]').val());
    }

    if (kb_checkin_date != '' && kb_checkout_date != '') {
        if (parseInt(Date.parse(kb_checkout_date)) < parseInt(Date.parse(kb_checkin_date))) {
            $('.booking-product-checkin-block').append('<div class="col-xs-12 alert alert-danger kb-error-message">' + kb_checkin_checkout_valid + '</div>');
            return;
        }
        var kb_checkin_selected = {
            'date': kb_checkin_date.getDate(),
            'day': kb_checkin_date.getDay(),
            'year': kb_checkin_date.getFullYear(),
            'hours': kb_checkin_date.getHours(),
            'minutes': kb_checkin_date.getMinutes(),
            'months': kb_checkin_date.getMonth(),
            'seconds': 0,
        };
        var kb_checkout_selected = {
            'date': kb_checkout_date.getDate(),
            'day': kb_checkout_date.getDay(),
            'year': kb_checkout_date.getFullYear(),
            'hours': kb_checkout_date.getHours(),
            'minutes': kb_checkout_date.getMinutes(),
            'months': kb_checkout_date.getMonth(),
            'seconds': 0,
        };
        var time_slot = {};
        if ($('select[name="kb_checkin_time_slot"]').length) {
            time_slot = {
                time: $('select[name="kb_checkin_time_slot"]').val(),
                price: $('select[name="kb_checkin_time_slot"] option:selected').data('price'),
            };
        } else {


        }
        $.ajax({
            type: 'post',
            dataType: 'json',
            cache: true,
            beforeSend: function () {
//                        $('body').addClass("loading");
            },
            url: kb_cart_url,
            data: {
                ajax: true,
                validateCheckInDate: true,
//            date: ev.date,
                kb_checkin_selected: kb_checkin_selected,
                kb_checkout_selected: kb_checkout_selected,
                id_booking_product: $('input[name="kb_booking_product"]').val(),
                id_product: $('input[name="kb_id_product"]').val(),
                product_type: $('input[name="kb_product_type"]').val(),
                period_type: $('input[name="kb_period_type"]').val(),
                qty: $('.kb_product_qty').val(),
                time_slot: time_slot
            },
            success: function (rec) {
                if (rec['success']) {
                    $('input[name="kb_product_price"]').val(rec['price']);
                    $('.kb-booking-product-price').html(rec['display_price']);
                    $('input[name="kb_product_date_valid"]').val('1');
                } else if (rec['error'] != '') {
                    $('.booking-product-checkin-block').append('<div class="col-xs-12 alert alert-danger kb-error-message">' + rec['error'] + '</div>');
                }
            },
            complete: function () {
            }
        });
    } else {
        $('.booking-product-checkin-block').append('<div class="col-xs-12 alert alert-danger kb-error-message">' + kb_checkin_checkout_empty + '</div>');
        return;
    }

}

//add slider to the facilities displaying on hotel product
function renderHotelFacilities(elm)
{
    if (typeof kb_slider_item_count != 'undefined') {
        var kb_slider_item = 3;
        if (kb_slider_item_count >= 3) {
            $(elm).slick({
                centerMode: true,
                centerPadding: '60px',
                slidesToShow: 3,
                infinite: true,
                autoplay: true,
                autoplaySpeed: 2000,
                focusOnSelect: true,
                responsive: [
                    {
                        breakpoint: 768,
                        settings: {
                            arrows: false,
                            centerMode: true,
                            centerPadding: '40px',
                            slidesToShow: 3
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            arrows: false,
                            centerMode: true,
                            centerPadding: '40px',
                            slidesToShow: 1
                        }
                    }
                ]
            });
        }
    }
}

//function to display room popup
function renderHotelRoomPopup(id_booking_product, id_hotel_room)
{
    $.ajax({
        type: 'POST',
        url: kb_hotel_url,
        dataType: "html",
        data: {
            "renderHotelRoom": true,
            "ajax": true,
            "id_booking_product": id_booking_product,
            "id_hotel_room": id_hotel_room,
            "id_product": $('input[name="id_product"]').val()
        },
        beforeSend: function () {

        },
        success: function (rec) {
            $('#kbrenderHotelRoomBlockModel .modal-body').html(rec);
            $('#kbrenderHotelRoomBlockModel').modal();
            var kb_maxView = '0';
            var kb_dateformat = 'yyyy-mm-dd hh:ii';
            if ($('input[name="kb_period_type"]').val() == 'date') {
                kb_maxView = '1';
                kb_dateformat = 'yyyy-mm-dd';
            } else if ($('input[name="kb_period_type"]').val() == 'date_time') {
                kb_maxView = '0';
            }
            if (typeof (current_date) == 'undefined') {
                var current_date = new Date();
            }
            $('input[name="kb_room_check_in"]').datetimepicker({
                startDate: current_date,
                minView: 2,
                format: kb_dateformat,
                autoclose: 1,
                weekStart: 1,
                onRenderDay: function (date) {
                    var yr = date.getUTCFullYear();
                    var month = date.getUTCMonth() + 1;
                    var day = date.getUTCDate();
                    if (month < 10) {
                        month = '0' + month;
                    }
                    if (date.getUTCDate() < 10) {
                        day = '0' + date.getUTCDate();
                    }

                    var newDate = yr + '-' + month + '-' + day;
                    if (typeof kb_render_dates != 'undefined') {
                        var date_valid = false;
                        var render_dates = $.parseJSON(kb_render_dates);
                        if (render_dates != '') {
                            for (var i in render_dates) {
                                if (newDate >= render_dates[i]['from_date'] && newDate <= render_dates[i]['to_date']) {
                                    date_valid = true;
                                }
                            }

                            if (!date_valid) {
                                return ['disabled'];
                            }
                        }
                    }
                },

            }).on('changeDate', function (ev) {
                $('.kb-error-message').remove();
                if (ev != '') {
                    if (ev.date != '') {
                        checkRoomCheckInOutDates(ev, 'checkin', id_hotel_room);
                    }
                }
            });

            $('input[name="kb_room_check_out"]').datetimepicker({
                startDate: current_date,
                minView: 2,
                format: kb_dateformat,
                autoclose: 1,
                weekStart: 1,
                onRenderDay: function (date) {
                    var yr = date.getUTCFullYear();
                    var month = date.getUTCMonth() + 1;
                    var day = date.getUTCDate();
                    if (month < 10) {
                        month = '0' + month;
                    }
                    if (date.getUTCDate() < 10) {
                        day = '0' + date.getUTCDate();
                    }

                    var newDate = yr + '-' + month + '-' + day;
                    if (typeof kb_render_dates != 'undefined') {
                        var date_valid = false;
                        var render_dates = $.parseJSON(kb_render_dates);
                        if (render_dates != '') {
                            for (var i in render_dates) {
                                if (newDate >= render_dates[i]['from_date'] && newDate <= render_dates[i]['to_date']) {
                                    date_valid = true;
                                }
                            }

                            if (!date_valid) {
                                return ['disabled'];
                            }
                        }
                    }
                },
            }).on('changeDate', function (ev) {
                $('.kb-error-message').remove();
                if (ev != '') {
                    if (ev.date != '') {
                        checkRoomCheckInOutDates(ev, 'checkout', id_hotel_room);
                    }
                }
            });

            $('.kb_hotel_room_qty').on('change', function () {
                var kb_qty = $(this).val();
                $('.kb-error-message').remove();
                if (kb_qty > 0) {
                    if ($('input[name="kb_period_type"]').val() == 'date') {
                        var kb_checkin_date = new Date($('input[name="kb_room_check_in"]').val());
                        var kb_checkout_date = new Date($('input[name="kb_room_check_out"]').val());
                        if (kb_checkin_date != '' && kb_checkout_date != '') {
                            if (parseInt(Date.parse(kb_checkout_date)) < parseInt(Date.parse(kb_checkin_date))) {
                                $('.booking-product-checkin-block').append('<div class="col-xs-12 alert alert-danger kb-error-message">' + kb_checkin_checkout_valid + '</div>');
                                return;
                            }
                            var kb_checkin_selected = {
                                'date': kb_checkin_date.getDate(),
                                'day': kb_checkin_date.getDay(),
                                'year': kb_checkin_date.getFullYear(),
                                'hours': kb_checkin_date.getHours(),
                                'minutes': kb_checkin_date.getMinutes(),
                                'months': kb_checkin_date.getMonth(),
                                'seconds': kb_checkin_date.getSeconds(),
                            };
                            var kb_checkout_selected = {
                                'date': kb_checkout_date.getDate(),
                                'day': kb_checkout_date.getDay(),
                                'year': kb_checkout_date.getFullYear(),
                                'hours': kb_checkout_date.getHours(),
                                'minutes': kb_checkout_date.getMinutes(),
                                'months': kb_checkout_date.getMonth(),
                                'seconds': kb_checkout_date.getSeconds(),
                            };
                            $.ajax({
                                type: 'post',
                                dataType: 'json',
                                cache: true,
                                beforeSend: function () {
                                },
                                url: kb_cart_url,
                                data: {
                                    ajax: true,
                                    validateQuantity: true,
                                    kb_checkin_selected: kb_checkin_selected,
                                    kb_checkout_selected: kb_checkout_selected,
                                    id_booking_product: $('input[name="kb_booking_product"]').val(),
                                    id_product: $('input[name="kb_id_product"]').val(),
                                    product_type: $('input[name="kb_product_type"]').val(),
                                    period_type: $('input[name="kb_period_type"]').val(),
                                    qty: kb_qty,
                                    id_hotel_room: $('input[name="kb_room_selected_hotel"]').val(),
                                },
                                success: function (rec) {

                                    if (rec['success']) {
                                        $('input[name="kb_product_price"]').val(rec['price']);
                                        $('.kb-booking-product-price').html(rec['display_price']);
                                        $('input[name="kb_product_date_valid"]').val('1');
                                    } else if (rec['error'] != '') {
                                        $('.kb-hotel-room-popup-date-block').find('.kb-error-message').remove();
                                        $('.kb-hotel-room-popup-date-block').append('<div class="col-xs-12 alert alert-danger kb-error-message">' + rec['error'] + '</div>');
                                    }
                                },
                                complete: function () {
                                }
                            });
                        }
                    }
                }
            });

            $('.kbroom_img_slider').slick({
                dots: true,
//                centerMode: true,
                infinite: true,
                slidesToShow: 1,
                speed: 500,
                fade: true,
                focusOnSelect: true,
            });

        },
        error: function () {

        },
        complete: function () {
            renderHotelFacilities('.kb_room_slider');
        }
    });

    event.preventDefault();
    return false;
}


//function to search room by room category
function searchRoomByCategory()
{
    $('.kb-error-message').remove();
    $.ajax({
        type: 'POST',
        url: kb_hotel_url,
        dataType: "html",
        data: {
            "searchHotelRoom": true,
            "ajax": true,
            "id_booking_product": $('input[name="kb_booking_product"]').val(),
            "room_category": $('select[name="room_category"]').val(),
            "id_product": $('input[name="id_product"]').val()
        },
        beforeSend: function () {

        },
        success: function (rec) {
            if (rec != '') {
                $('.kb-hotel-room-block').html(rec);
            } else {
                $('select[name="room_category"]').after('<p class="kb-error-message" style="color:red;">' + kb_no_room_available + '</p>');
                $('.kb-hotel-room-block').html('<h3 class="h3">' + room_available + '</h3><div class="alert alert-danger">' + kb_no_room_available + '</div>');
            }
        },
        error: function () {

        },
        complete: function () {
        }
    });

    event.preventDefault();
    return false;
}


$(document).ajaxComplete(function (event, xhr, settings) {
    if ($('input[name="kb_booking_product"]').length) {
        $('.product-customization').hide();
    }
});