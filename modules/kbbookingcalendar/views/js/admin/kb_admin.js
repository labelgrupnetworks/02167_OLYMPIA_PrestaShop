/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 *
 */
var block_number = 1;
$(document).ready(function () {

    if (typeof currentFormTab != 'undefined') {
        $('.tab-row.active').removeClass('active');
        $('#kb_booking_product_form').append($('#kbproduct-images'));
        $('#fieldset_dates_3 .form-wrapper').append($('.kb-date-time-block'));
        $('.kb-date-time-block').show();
        $('#kb_add_product_form_tab_' + currentFormTab).show();
        $('#kb_add_product_form_tab_' + currentFormTab).parent().addClass('active');
        hideShowPCFields(currentFormTab);

        $('#fieldset_dates_3').find('.form-wrapper').append($('.kb-date-time-warning-alert'));
    }
    $('select[name="date_selection"]').on('change', function () {
        if ($(this).val() == 'date_range') {
            $('input[name="start_date"]').closest('.form-group').show();
            $('input[name="end_date"]').closest('.form-group').show();
            $('input[name="particular_date"]').closest('.form-group').hide();
        } else if ($(this).val() == 'particular_date') {
            $('input[name="start_date"]').closest('.form-group').hide();
            $('input[name="end_date"]').closest('.form-group').hide();
            $('input[name="particular_date"]').closest('.form-group').show();
        }
    }).change();
    if (typeof currentText != 'undefined') {
        $('.kb_date_from, .kb_end_date, .kb_start_date,#start_date,#end_date,#particular_date').datepicker({
            beforeShow: function (input, inst) {
                setTimeout(function () {
                    inst.dpDiv.css({
                        'z-index': 1031
                    });
                }, 0);
            },
            prevText: '',
            nextText: '',
            minDate: new Date(),
            dateFormat: 'yy-mm-dd',
            // Define a custom regional settings in order to use PrestaShop translation tools
            currentText: currentText,
            closeText: closeText,
        });
        $('.kb_time_from,.kb_time_to').timepicker({
            showDate: false,
            timeFormat: 'hh:mm tt',
            currentText: currentText,
            closeText: closeText,
            timeOnlyTitle: timeonlytext,
        });
    }



    $('button[name="addkbDateTimeField"]').click(function () {
        if ($('.kb-date-time-block .kb-datetime-row').length) {
            block_number = $('.kb-date-time-block .kb-datetime-row').length + 1;
        }
        $.ajax({
            type: 'POST',
            url: admin_form_url,
            dataType: 'json',
            data: {
                'addDateTimeRow': true,
                'counter': block_number,
                'id_booking_product': $('input[name="id_booking_product"]').val()
            },
            beforeSend: function () {

            },
            success: function (html) {
                $('.kb-date-time-block .form-group .col-lg-9').find('button[name="addkbDateTimeField"]').closest('div').before(html);
                if ($('select[name="period_type"]').val() == 'date') {
                    $('.kb_booking_time_range').find('th.kb_time_from_th').hide();
                    $('.kb_booking_time_range').find('th.kb_time_to_th').hide();
                    $('.kb_booking_time_range').find('td.kb_time_from_td').hide();
                    $('.kb_booking_time_range').find('td.kb_time_to_td').hide();
                    $('.kb_booking_time_range').removeClass('col-lg-7').addClass('col-lg-3');
                    $('button[name="addkbTimeField"]').hide();
                }
                $('.kb_date_from,.kb_end_date').datepicker({
                    beforeShow: function (input, inst) {
                        setTimeout(function () {
                            inst.dpDiv.css({
                                'z-index': 1031
                            });
                        }, 0);
                    },
                    prevText: '',
                    nextText: '',
                    dateFormat: 'yy-mm-dd',
                    currentText: currentText,
                    closeText: closeText,
                });
                $('.kb_time_from,.kb_time_to').timepicker({
                    showDate: false,
                    timeFormat: 'hh:mm tt',
                    currentText: currentText,
                    closeText: closeText,
                    timeOnlyTitle: timeonlytext,
                });
            },
            error: function () {

            }
        });

        event.preventDefault();
        return false;
    });

    $('input[name="image_upload"]').on('change', function () {
        $("input[name='image_upload']").closest('.form-group').find('.input-group').removeClass('error_field');
        var imgPath = $(this)[0].value;
        $('.error_message').remove();
        var image_holder = $("#kbslmarker");
        if (($("input[name='image_upload']").prop("files").length)) {
            var validate_image = velovalidation.checkImage($(this), 2097152, 'kb');
            if (validate_image != true) {
                $('input[name="filename"]').val('');
                showErrorMessage(validate_image);
                $("input[name='image_upload']").closest('.form-group').find('.input-group').addClass('error_field');
                $('input[name="image_upload"]').closest('.form-group').after('<span class="error_message">' + validate_image + '</span>');
            } else {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#kbslmarker').attr('src', e.target.result);
                }
                image_holder.show();
                reader.readAsDataURL($(this)[0].files[0]);
            }
        }
    });


    if (typeof select_placeholder != 'undefined') {
        if ($('.kb_price_rule_id_product,.kb_booking_room_category_select').length) {
            $('.kb_price_rule_id_product,.kb_booking_room_category_select').select2({
                placeholder: select_placeholder,
                formatNoMatches: no_match_err,
                allowClear: true
            });
        }
    }



    $('select[name="image_type"]').on('change', function () {
        if ($(this).val() == 'upload') {
            $('input[name="image_upload"]').parents('.form-group').show()
            $('input[name="font_awesome_icon"]').closest('.form-group').hide();
        } else if ($(this).val() == 'font') {
            $('input[name="image_upload"]').parents('.form-group').hide()
            $('input[name="font_awesome_icon"]').closest('.form-group').show();
        } else {
            $('input[name="image_upload"]').parents('.form-group').hide()
            $('input[name="font_awesome_icon"]').closest('.form-group').hide();
        }
    }).change();
    
        $('select[name="discount_type"]').on('change', function () {
        if ($(this).val() == 'fixed') {
            $('input[name="fix_amount"]').parents('.form-group').show()
            $('input[name="percent_amount"]').parents('.form-group').hide();
        } else {
            $('input[name="fix_amount"]').parents('.form-group').hide()
            $('input[name="percent_amount"]').parents('.form-group').show();
        }
    }).change();

    if ($('input[name="kb_product_type"]').val() == 'hotel_booking') {
        $('.product_disable_days').closest('.form-group').hide();
    }


    $('select[name="period_type"]').on('change', function () {
        if ($(this).val() == 'date') {
            $('.kb_booking_time_range').find('th.kb_time_from_th').hide();
            $('.kb_booking_time_range').find('th.kb_time_to_th').hide();
            $('.kb_booking_time_range').find('td.kb_time_from_td').hide();
            $('.kb_booking_time_range').find('td.kb_time_to_td').hide();
            $('.kb_booking_time_range').removeClass('col-lg-7').addClass('col-lg-3');
            $('button[name="addkbTimeField"]').hide();
            $('.kb-date-time-block').show();
            $('.kb_booking_time_range tbody').each(function () {
                $(this).find('tr').not('tr:first').remove();
            });
            if ($('input[name="kb_product_type"]').val() == 'daily_rental') {
                $('input[name="min_days"]').closest('.form-group').show();
                $('input[name="max_days"]').closest('.form-group').show();
            }
        } else if ($(this).val() == 'date_time') {
            $('.kb_booking_time_range').find('th.kb_time_from_th').show();
            $('.kb_booking_time_range').find('th.kb_time_to_th').show();
            $('.kb_booking_time_range').find('td.kb_time_from_td').show();
            $('.kb_booking_time_range').find('td.kb_time_to_td').show();
            $('.kb_booking_time_range').removeClass('col-lg-3').addClass('col-lg-7');
            $('.kb-date-time-block').show();
            $('button[name="addkbTimeField"]').show();
            $('.kb_booking_time_range').show();
            if ($('input[name="kb_product_type"]').val() == 'daily_rental') {
                $('input[name="min_days"]').closest('.form-group').hide();
                $('input[name="max_days"]').closest('.form-group').hide();
            }
        }
    }).change();

    $('button[name="submitAddKbFacilitiesProduct"]').click(function () {
        if (typeof kb_available_facilities_data != 'undefined') {
            var available_facilites = $.parseJSON(kb_available_facilities_data);
            var selected_array = [];
            var $kbmapfacilities = $('input[name="kb-added-facilities-data"]');
            $('input[name="selected_add_facilities[]"]:checked').each(function (i) {
                selected_array.push($(this).val());
            });
            if (selected_array.length) {
                $.ajax({
                    type: "POST",
                    url: admin_form_url,
                    data: 'addProductFacilities=true&id_booking_product=' + $('input[name="id_booking_product"]').val() + '&ajax=true&selected_facilities=' + selected_array,
                    beforeSend: function () {
                    },
                    success: function (response) {
                        response = $.parseJSON(response);
                        if (response['success']) {
                            $('.kb-no-record-list').remove();
                            $('input[name="selected_add_facilities[]"]:checked').each(function (i) {
                                var selected_val = $(this).val();
                                var kb_data = '';
                                $.each(available_facilites, function (idx, item) {
                                    if (selected_val == item.id_facilities) {
                                        kb_data += '<tr id="kb-already-product-facilites_' + item.id_facilities + '"><td>' + item.id_facilities + '</td><td>' + item.name + '</td><td><a class="btn btn-default" href="javascript:removeProductFacilities(' + item.id_facilities + ');"><i class="icon-trash"></i> ' + remove_string + '</a></td></tr>';

                                        $kbmapfacilities.val($kbmapfacilities.val() + item.id_facilities + '-');
                                    }
                                });
                                if ($('table#table-product-facilities-list').find('tr#kb-already-product-facilites_' + selected_val).length <= 0) {
                                    $('#table-product-facilities-list').find('tbody#kb-append-facilities-data').append(kb_data);
                                }
                            });
                            $('input[name="selected_add_facilities[]"]').prop('checked', false);

                            showSuccessMessage(kb_facility_map_success);
                            $('#kbaddFacilitiesBlockModel').modal('hide');
                            if ($kbmapfacilities.val() != '') {
                                var mapped_facilities = $kbmapfacilities.val().split('-');
                                $.each(mapped_facilities, function (idx, item) {
                                    if (item != '') {
                                        $('.kb-productfacilities-dialogue-form tbody').find('tr#kb-added-product-facilites_' + item).hide();

                                    }
                                });
                            }
                        }
                    }
                });
            }
        }
        return false;
    });


    $('select#product_add_room_category').change(function () {
        var id_category = $(this).val();
        $('select#product_add_room_type').closest('.form-group').hide();
        if (id_category != '') {
            $.ajax({
                type: "POST",
                url: admin_check_room_type_url,
                data: {
                    'fetchRoomType': true,
                    'id_category': id_category,
                    'ajax': true
                },
                beforeSend: function () {
                    $('select#product_add_room_type').attr('disabled', true);
//                    $('#c_loader').show();
                },
                success: function (response) {
                    response = $.parseJSON(response);
                    $('select#product_add_room_type').find('option:not(:first)').remove();
                    $('select#product_add_room_type').closest('.form-group').show();
                    if (response != '') {
                        for (var i in response) {
                            var kb_selected = '';
                            if (typeof kb_room_type_field_value != 'undefined') {
                                if (kb_room_type_field_value != '') {
                                    if (kb_room_type_field_value == response[i]['id_room_type']) {
                                        kb_selected = 'selected';
                                    }
                                }
                            }
                            $('select#product_add_room_type').append($('<option value=' + response[i]['id_room_type'] + ' ' + kb_selected + ' >' + response[i]['room_name'] + '</option>'));
                        }
                    } else {
                    }
                    $('select#product_add_room_type').removeAttr('disabled');
                }
            });
        }
    }).change();

});

function removeProductFacilities(id_facilities) {
    var kb_booking_id = $('input[name="id_booking_product"]').val();
    if (kb_booking_id != '') {
        if ($('input[name="kb-added-facilities-data"]').val() != '') {
            $.ajax({
                type: "POST",
                url: admin_form_url,
                data: 'removeProductFacilities=true&id_booking_product=' + kb_booking_id + '&ajax=true&id_facilities=' + id_facilities,
                beforeSend: function () {
//                    $('#c_loader').show();
                },
                success: function (response) {
                    response = $.parseJSON(response);
                    if (response['success']) {
                        $('#table-product-facilities-list tbody#kb-append-facilities-data').find('tr#kb-already-product-facilites_' + id_facilities).remove();

                        var $kbmapfacilities = $('input[name="kb-added-facilities-data"]');
                        if ($kbmapfacilities.val() != '') {
                            $kbmapfacilities.val($kbmapfacilities.val().replace(id_facilities + '-', ''));
                        }
                        showSuccessMessage(kb_facility_remove_success);
                        if ($('table#table-product-facilities-list').find('tbody#kb-append-facilities-data tr').length <= 0) {
                            $('table#table-product-facilities-list').find('tbody#kb-append-facilities-data').append('<tr class="kb-no-record-list"><td class="list-empty" colspan="7"><div class="list-empty-msg"><i class="icon-warning-sign list-empty-icon"></i>' + no_record_found + '</div></td></tr>');
                        }
                    } else {
                        showErrorMessage(kb_facility_remove_error);
                    }
                }
            });
        } else {
            if ($('table#table-product-facilities-list').find('tbody#kb-append-facilities-data tr').length <= 0) {
                $('table#table-product-facilities-list').find('tbody#kb-append-facilities-data').append('<tr class="kb-no-record-list"><td class="list-empty" colspan="7"><div class="list-empty-msg"><i class="icon-warning-sign list-empty-icon"></i>' + no_record_found + '</div></td></tr>');
            }
        }
    }
}

function addProductKbFacilities()
{
    var kb_booking_id = $('input[name="id_booking_product"]').val();
    if (kb_booking_id != '') {
        $('.kb-productfacilities-dialogue-form tbody').find('tr').show();
        $('input[name="selected_add_facilities[]"]').prop('checked', false);
        var kbmapfacilities = $('input[name="kb-added-facilities-data"]');
        if (kbmapfacilities.val() != '') {
            var mapped_facilities = kbmapfacilities.val().split('-');
            $.each(mapped_facilities, function (idx, item) {
                if (item != '') {
                    $('.kb-productfacilities-dialogue-form tbody').find('tr#kb-added-product-facilites_' + item).hide();

                }
            });
        }

        $('.kb-productfacilities-dialogue-form tr#kb-no-available-facilities').hide();
        $('#kbaddFacilitiesBlockModel').modal({
            show: 'true',
        });
        $('#kbaddFacilitiesBlockModel').on('shown.bs.modal', function () {
            if ($('.kb-productfacilities-dialogue-form tbody').find('tr:visible').length <= 0) {
                $('.kb-productfacilities-dialogue-form tr#kb-no-available-facilities').show();
            }
        });
        return false;

    } else {

    }
}

function removeTimeRow(elm) {
//    if ($('.kb-datetime-row').length > 1) {
    if ($(elm).closest('.kb_booking_time_range').find('table tbody tr').length > 1) {
        $(elm).closest('tr').remove();
    } else {
        if ($('.kb-datetime-row').length > 1) {
            $(elm).closest('.kb-datetime-row').remove();
        }
    }
    var counter = 1;
    $('[class^="kb-datetime-row"]').each(function () {
        $(this).find('input.kb_date_from').attr('name', 'kb_date_from[' + counter + ']');
        $(this).find('input.kb_end_date').attr('name', 'kb_date_to[' + counter + ']');
        var time_from_name = $(this).find('input.kb_time_from').attr('name').match(/\d+/g);
        var time_to_name = $(this).find('input.kb_time_to').attr('name').match(/\d+/g);
        var time_price_name = $(this).find('input.kb_time_price').attr('name').match(/\d+/g);
        $(this).find('input.kb_time_from').attr('name', 'kb_time_from[' + counter + '][' + time_from_name.pop() + ']');
        $(this).find('input.kb_time_to').attr('name', 'kb_time_to[' + counter + '][' + time_to_name.pop() + ']');
        $(this).find('input.kb_time_price').attr('name', 'kb_time_price[' + counter + '][' + time_price_name.pop() + ']');
        $(this).attr('id', 'kb-datetime-block-row_' + counter);
        counter = counter + 1;
    });
//    }
}

function addTimeSlotRow(elm)
{
    var block_number = 1;
    var current_step = $(elm);
    if (current_step.closest('.kb_booking_time_range').find('input[name^=kb_time_from]').length) {
        block_number = $(elm).closest('.kb_booking_time_range').find('input[name^=kb_time_from]').length + 1;
    }

    var block_date_row_id = $(elm).closest('.kb-datetime-row').attr('id');
    var block_date_row = block_date_row_id.split('_');
    $.ajax({
        type: 'POST',
        url: admin_form_url,
        dataType: 'json',
        data: 'addTimeRow=true&counter=' + block_number + '&datetime_block=' + block_date_row[1] + '&id_booking_product=' + $('input[name="id_booking_product"]').val(),
        beforeSend: function () {

        },
        success: function (html) {
            current_step.closest('.kb_booking_time_range').find('table tbody').append(html);
            $('.kb_time_from,.kb_time_to').timepicker({
                showDate: false,
                timeFormat: 'hh:mm tt',
                currentText: currentText,
                closeText: closeText,
                timeOnlyTitle: timeonlytext,
            });
        },
        error: function () {

        }
    });

    event.preventDefault();
    return false;
}

function displayKbProductTab(tab) {
    $('.tab-row.active').removeClass('active');
    $('#kb_add_product_form_tab_' + tab).parent().addClass('active');
    $('#currentFormTab').val(tab);
    hideShowPCFields(tab);
    return false;
}

function hideShowPCFields(tab)
{
    if (tab == 'general') {
        $('#fieldset_general').show();
        $('#fieldset_price_1').hide();
        $('#fieldset_location_2').hide();
        $('#fieldset_dates_3').hide();
        $('#kbproduct-images').hide();
        $('#fieldset_hotelbookingfacilities').hide();
        $('#fieldset_hotelbookingroom').hide();
    } else if (tab == 'price') {
        $('#fieldset_general').hide();
        $('#fieldset_location_2').hide();
        $('#fieldset_dates_3').hide();
        $('#fieldset_price_1').show();
        $('#kbproduct-images').hide();
        $('#fieldset_hotelbookingfacilities').hide();
        $('#fieldset_hotelbookingroom').hide();
    } else if (tab == 'days') {
        $('#fieldset_general').hide();
        $('#fieldset_price_1').hide();
        $('#fieldset_location_2').hide();
        $('#fieldset_dates_3').show();
        $('#kbproduct-images').hide();
        $('#fieldset_hotelbookingfacilities').hide();
        $('#fieldset_hotelbookingroom').hide();
    } else if (tab == 'location') {
        $('#fieldset_general').hide();
        $('#fieldset_price_1').hide();
        $('#fieldset_location_2').show();
        $('#fieldset_dates_3').hide();
        $('#kbproduct-images').hide();
        $('#fieldset_hotelbookingfacilities').hide();
        $('#fieldset_hotelbookingroom').hide();
    } else if (tab == 'image') {
        $('#fieldset_general').hide();
        $('#fieldset_price_1').hide();
        $('#fieldset_location_2').hide();
        $('#fieldset_dates_3').hide();
        $('#fieldset_hotelbookingfacilities').hide();
        $('#fieldset_hotelbookingroom').hide();
        $('#kbproduct-images').show();
    } else if (tab == 'rooms') {
        $('#fieldset_general').hide();
        $('#fieldset_price_1').hide();
        $('#fieldset_location_2').hide();
        $('#fieldset_dates_3').hide();
        $('#kbproduct-images').hide();
        $('#fieldset_hotelbookingfacilities').hide();
        $('#fieldset_hotelbookingroom').show();
    } else if (tab == 'facilities') {
        $('#fieldset_general').hide();
        $('#fieldset_price_1').hide();
        $('#fieldset_location_2').hide();
        $('#fieldset_dates_3').hide();
        $('#kbproduct-images').hide();
        $('#fieldset_hotelbookingroom').hide();
        $('#fieldset_hotelbookingfacilities').show();
    } else {
        $('#fieldset_general').show();
        $('#fieldset_price_1').hide();
        $('#fieldset_location_2').hide();
        $('#fieldset_dates_3').hide();
        $('#kbproduct-images').hide();
        $('#fieldset_hotelbookingfacilities').hide();
        $('#fieldset_hotelbookingroom').hide();
    }
}