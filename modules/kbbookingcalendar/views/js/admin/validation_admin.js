/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */


$(document).ready(function () {
    validateKbProductForm
    $('button[name="generalsettingsubmitkbbookingcalendar"]').click(function () {
//        changes done by tarun
//        var error = false;
//        $(".error_message").remove();
//        $('input[name="kbbooking[api_key]"]').removeClass('error_field');
//        var gmap_empty = velovalidation.checkMandatory($('input[name="kbbooking[api_key]"]'));
//        if (gmap_empty != true) {
//            error = true;
//            $('input[name="kbbooking[api_key]"]').addClass('error_field');
//            $('input[name="kbbooking[api_key]"]').after('<p class="error_message">' + gmap_empty + '</p>');
//        }
//
//        if (error) {
//            $('html, body').animate({
//                scrollTop: $(".error_message").offset().top - 200
//            }, 1000);
//            return false;
//        }
//        changes over
        $("button[name='generalsettingsubmitkbbookingcalendar']").attr('disabled', 'disabled');
        $('#general_configuration_form').submit();
    });

    $('button[name="submitAddkb_booking_room_type"]').click(function () {
        var error = false;
        var is_error = 0;
        $(".error_message").remove();
        $('input[name^="room_name_"]').removeClass('error_field');
        $('input[name="max_allowed_child"]').removeClass('error_field');
        $('input[name="max_allowed_adult"]').removeClass('error_field');
        $('div.kb_booking_room_category_select').removeClass('error_field');

        $('input[name^="room_name_"]').each(function () {
            var name_error = velovalidation.checkMandatory($(this));
            if (name_error != true) {
                error = true;
                if (is_error < 1) {
                    $(this).parents('.col-lg-5').last().append('<span class="error_message">' + name_error + ' ' + check_for_all + '</span>');
                    is_error++;
                }
                $(this).addClass('error_field');
            }
        });

        var max_allowed_child_mand = velovalidation.checkMandatory($('input[name="max_allowed_child"]'));
        if (max_allowed_child_mand != true) {
            error = true;
            $('input[name="max_allowed_child"]').addClass('error_field');
            $('input[name="max_allowed_child"]').after('<p class="error_message">' + max_allowed_child_mand + '</p>');
        } else {
            var max_allowed_child_valid = velovalidation.isNumeric($('input[name="max_allowed_child"]'));
            if (max_allowed_child_valid != true) {
                error = true;
                $('input[name="max_allowed_child"]').addClass('error_field');
                $('input[name="max_allowed_child"]').after('<p class="error_message">' + max_allowed_child_valid + '</p>');
            }
        }
        var max_allowed_adult_mand = velovalidation.checkMandatory($('input[name="max_allowed_adult"]'));
        if (max_allowed_adult_mand != true) {
            error = true;
            $('input[name="max_allowed_adult"]').addClass('error_field');
            $('input[name="max_allowed_adult"]').after('<p class="error_message">' + max_allowed_adult_mand + '</p>');
        } else {
            var max_allowed_adult_valid = velovalidation.isNumeric($('input[name="max_allowed_child"]'));
            if (max_allowed_adult_valid != true) {
                error = true;
                $('input[name="max_allowed_adult"]').addClass('error_field');
                $('input[name="max_allowed_adult"]').after('<p class="error_message">' + max_allowed_adult_valid + '</p>');
            }
        }

        if ($('select[name="booking_category[]"]').val() == null) {
            error = true;
            $('div.kb_booking_room_category_select').addClass('error_field');
            $('div.kb_booking_room_category_select').after('<p class="error_message">' + kb_category_empty + '</p>');
        }

        if (error) {
            $('html, body').animate({
                scrollTop: $(".error_message").offset().top - 200
            }, 1000);
            return false;
        }
        $("button[name='submitAddkb_booking_room_type']").attr('disabled', 'disabled');
        $('#kb_booking_room_type_form').submit();
    });

    $('button[name="submitAddkb_booking_category"]').click(function () {
        var error = false;
        var is_error = 0;
        $(".error_message").remove();
        $('input[name^="name_"]').removeClass('error_field');
        $('textarea[name="description"]').removeClass('error_field');

        $('input[name^="name_"]').each(function () {
            var name_error = velovalidation.checkMandatory($(this));
            if (name_error != true) {
                error = true;
                if (is_error < 1) {
                    $(this).parents('.col-lg-5').last().append('<span class="error_message">' + name_error + ' ' + check_for_all + '</span>');
                    is_error++;
                }
                $(this).addClass('error_field');
            }
        });

        var description_invalid = velovalidation.checkHtmlTags($('textarea[name="description"]'));
        if (description_invalid != true) {
            error = true;
            $('textarea[name="description"]').addClass('error_field');
            $('textarea[name="description"]').after('<p class="error_message">' + description_invalid + '</p>');
        }
        if (error) {
            $('html, body').animate({
                scrollTop: $(".error_message").offset().top - 200
            }, 1000);
            return false;
        }
        $("button[name='submitAddkb_booking_category']").attr('disabled', 'disabled');
        $('#kb_booking_category_form').submit();
    });

    $('button[name="submitAddkb_booking_price_rule"]').click(function () {
        var error = false;
        var is_error = 0;
        $(".error_message").remove();
        $('input[name^="name_"]').removeClass('error_field');
        $('input[name="start_date"]').closest('.input-group').removeClass('error_field');
        $('input[name="end_date"]').closest('.input-group').removeClass('error_field');
        $('input[name="particular_date"]').closest('.input-group').removeClass('error_field');
        $('input[name="reduction"]').removeClass('error_field');

        $('input[name^="name_"]').each(function () {
            var name_error = velovalidation.checkMandatory($(this));
            if (name_error != true) {
                error = true;
                if (is_error < 1) {
                    $(this).parents('.col-lg-5').last().append('<span class="error_message">' + name_error + ' ' + check_for_all + '</span>');
                    is_error++;
                }
                $(this).addClass('error_field');
            }
        });

        if ($('select[name="date_selection"]').val() == "date_range") {
            var start_date_empty = velovalidation.checkMandatory($('input[name="start_date"]'));
            if (start_date_empty != true) {
                error = true;
                $('input[name="start_date"]').closest('.input-group').addClass('error_field');
                $('input[name="start_date"]').closest('.input-group').after('<p class="error_message">' + start_date_empty + '</p>');
            }
            var end_date_empty = velovalidation.checkMandatory($('input[name="end_date"]'));
            if (end_date_empty != true) {
                error = true;
                $('input[name="end_date"]').closest('.input-group').addClass('error_field');
                $('input[name="end_date"]').closest('.input-group').after('<p class="error_message">' + end_date_empty + '</p>');
            } else {
                var start_date = Date.parse($('input[name="start_date"]').val());
                var end_date = Date.parse($('input[name="end_date"]').val());
                if (parseInt(end_date) <= parseInt(start_date)) {
                    error = true;
                    $('input[name="end_date"]').closest('.input-group').addClass('error_field');
                    $('input[name="end_date"]').closest('.input-group').after('<span class="error_message">' + end_date_error + '</span>');
                }
            }
        } else {
            var particular_date_empty = velovalidation.checkMandatory($('input[name="particular_date"]'));
            if (particular_date_empty != true) {
                error = true;
                $('input[name="particular_date"]').closest('.input-group').addClass('error_field');
                $('input[name="particular_date"]').closest('.input-group').after('<p class="error_message">' + particular_date_empty + '</p>');
            }
        }

        var price_empty = velovalidation.checkMandatory($('input[name="reduction"]'));
        if (price_empty != true) {
            error = true;
            $('input[name="reduction"]').addClass('error_field');
            $('input[name="reduction"]').after('<p class="error_message">' + price_empty + '</p>');
        } else {
            if ($('select[name="reduction_type"]').val() == 'fixed') {
                var price_valid = velovalidation.checkAmount($('input[name="reduction"]'));
                if (price_valid != true) {
                    error = true;
                    $('input[name="reduction"]').addClass('error_field');
                    $('input[name="reduction"]').after('<span class="error_message">' + price_valid + '</span>');
                }
            } else {
                var price_valid = velovalidation.checkPercentage($('input[name="reduction"]'));
                if (price_valid != true) {
                    error = true;
                    $('input[name="reduction"]').addClass('error_field');
                    $('input[name="reduction"]').after('<span class="error_message">' + price_valid + '</span>');
                }
            }
        }

        if (error) {
            $('html, body').animate({
                scrollTop: $(".error_message").offset().top - 200
            }, 1000);
            return false;
        }
        $("button[name='submitAddkb_booking_facilities']").attr('disabled', 'disabled');
        $('#kb_booking_facilities_form').submit();
    });

    $('button[name="kb_submit_product_room_form"]').click(function () {
        var error = false;
        $(".error_message").remove();
        $('input[name="room_price"]').closest('.input-group').removeClass('error_field');
        $('input[name="room_quantity"]').closest('.input-group').removeClass('error_field');
        $('input[name="start_time"]').removeClass('error_field');
        $('input[name="end_time"]').removeClass('error_field');
        $('select[name="room_type"]').removeClass('error_field');
        $('select[name="room_category"]').removeClass('error_field');
         $("#product_room_images").closest('.form-group').find('.input-group').removeClass('error_field');

        var room_quantity_empty = velovalidation.checkMandatory($('input[name="room_quantity"]'));
        if (room_quantity_empty != true) {
            error = true;
            $('input[name="room_quantity"]').closest('.input-group').addClass('error_field');
            $('input[name="room_quantity"]').closest('.input-group').after('<p class="error_message">' + room_quantity_empty + '</p>');
        } else {
            var room_quantity_valid = velovalidation.isNumeric($('input[name="room_quantity"]'));
            if (room_quantity_valid != true) {
                error = true;
                $('input[name="room_quantity"]').closest('.input-group').addClass('error_field');
                $('input[name="room_quantity"]').closest('.input-group').after('<span class="error_message">' + room_quantity_valid + '</span>');
            }
        }

        var price_empty = velovalidation.checkMandatory($('input[name="room_price"]'));
        if (price_empty != true) {
            error = true;
            $('input[name="room_price"]').closest('.input-group').addClass('error_field');
            $('input[name="room_price"]').closest('.input-group').after('<p class="error_message">' + price_empty + '</p>');
        } else {
            var price_valid = velovalidation.checkAmount($('input[name="room_price"]'));
            if (price_valid != true) {
                error = true;
                $('input[name="room_price"]').closest('.input-group').addClass('error_field');
                $('input[name="room_price"]').closest('.input-group').after('<span class="error_message">' + price_valid + '</span>');
            }
        }

        var start_time_empty = velovalidation.checkMandatory($('input[name="start_time"]'));
        if (start_time_empty != true) {
            error = true;
            $('input[name="start_time"]').addClass('error_field');
            $('input[name="start_time"]').after('<p class="error_message">' + start_time_empty + '</p>');
        }

        var end_time_empty = velovalidation.checkMandatory($('input[name="end_time"]'));
        if (end_time_empty != true) {
            error = true;
            $('input[name="end_time"]').addClass('error_field');
            $('input[name="end_time"]').after('<p class="error_message">' + end_time_empty + '</p>');
        }

        if ($('#product_room_images').length) {
            if ($('#product_room_images').get(0).files.length) {
                for (var i = 0; i < $('#product_room_images').get(0).files.length; ++i) {
                    var file1 = $("#product_room_images").get(0).files[i].name;
                    if (file1) {
                        var file_size = $("#product_room_images").get(0).files[i].size;
                        if (file_size < 2097152) {
                            var ext = file1.split('.').pop().toLowerCase();
                            if ($.inArray(ext, ['jpg', 'jpeg', 'gif', 'png']) === -1) {
                                error = true;
                                $("#product_room_images").closest('.form-group').find('.input-group').addClass('error_field');
                                $('#product_room_images').closest('.form-group').after('<p class="error_message">' + file1 + ': ' + kb_image_valid + '</p>');
                            }
                        } else {
                            error = true;
                            $("#product_room_images").closest('.form-group').find('.input-group').addClass('error_field');
                            $('#product_room_images').closest('.form-group').after('<p class="error_message">' + file1 + ': ' + kb_image_size_valid + '</p>');
                        }
                    }
                }
            }
        }

        if ($('select[name="room_category"]').val() == '') {
            error = true;
            $('select[name="room_category"]').addClass('error_field');
            $('select[name="room_category"]').after('<p class="error_message">' +select_empty + '</p>');
        }
        if ($('select[name="room_type"]').val() == '') {
            error = true;
            $('select[name="room_type"]').addClass('error_field');
            $('select[name="room_type"]').after('<p class="error_message">' +select_empty + '</p>');
        }


        if (error) {
            $('html, body').animate({
                scrollTop: $(".error_message").offset().top - 200
            }, 1000);
            return false;
        }
        $("button[name='kb_submit_product_room_form']").attr('disabled', 'disabled');
        $('#configuration_form').submit();
    });

    $('button[name="submitAddkb_booking_facilities"]').click(function () {
        var error = false;
        var is_error = 0;
        $(".error_message").remove();
        $('input[name^="name_"]').removeClass('error_field');
        $('input[name="font_awesome_icon"]').removeClass('error_field');
        $('select[name="image_type"]').removeClass('error_field');
        $('input[name="image_upload"]').closest('.form-group').find('.input-group').removeClass('error_field');

        $('input[name^="name_"]').each(function () {
            var name_error = velovalidation.checkMandatory($(this));
            if (name_error != true) {
                error = true;
                if (is_error < 1) {
                    $(this).parents('.col-lg-5').last().append('<span class="error_message">' + name_error + ' ' + check_for_all + '</span>');
                    is_error++;
                }
                $(this).addClass('error_field');
            }
        });

        if ($('select[name="image_type"]').val() == "") {
            error = true;
            $('select[name="image_type"]').addClass('error_field');
            $('select[name="image_type"]').after('<p class="error_message">' + select_empty + '</p>');
        } else if ($('select[name="image_type"]').val() == "upload") {
            if ($('input[name="image_upload"]').prop('files').length == '0' && ($('input[name="is_image_uploaded"]').length <= 0)) {
                error = true;
                $('input[name="image_upload"]').closest('.form-group').find('.input-group').addClass('error_field');
                $('input[name="image_upload"]').closest('.form-group').after('<span class="error_message">' + upload_image_empty + '</span>');
            } else if ($('input[name="image_upload"]').prop('files').length) {
                var image_upload_valid = velovalidation.checkImage($('input[name="image_upload"]'));
                if (image_upload_valid != true) {
                    error = true;
                    $('input[name="image_upload"]').closest('.form-group').find('.input-group').addClass('error_field');
                    $('input[name="image_upload"]').closest('.form-group').after('<span class="error_message">' + image_upload_valid + '</span>');
                }
            }
        } else if ($('select[name="image_type"]').val() == "font") {
            var font_awesome_icon_empty = velovalidation.checkMandatory($('input[name="font_awesome_icon"]'));
            if (font_awesome_icon_empty != true) {
                error = true;
                $('input[name="font_awesome_icon"]').addClass('error_field');
                $('input[name="font_awesome_icon"]').after('<span class="error_message">' + font_awesome_icon_empty + '</span>');
            }
        }

        if (error) {
            $('html, body').animate({
                scrollTop: $(".error_message").offset().top - 200
            }, 1000);
            return false;
        }
        $("button[name='submitAddkb_booking_facilities']").attr('disabled', 'disabled');
        $('#kb_booking_facilities_form').submit();
    });
    
        $('button[name="submitAddkb_booking_discount_rules"]').click(function () {
        var error = false;
        $(".error_message").remove();
        $('input[name="rule_name"]').removeClass('error_field');
        $('input[name="min_amount"]').removeClass('error_field');
        $('input[name="fix_amount"]').removeClass('error_field');
        $('input[name="percent_amount"]').removeClass('error_field');
        $('input[name="validity"]').removeClass('error_field');
          
        var rule_name_err = velovalidation.checkMandatory($('input[name="rule_name"]'));
        
        if (rule_name_err != true)
        {
            error = true;
            $('input[name="rule_name"]').addClass('error_field');
            $('input[name="rule_name"]').after('<span class="error_message">' + empty_field + '</span>');
        }
        
        var fix_amount_mand = velovalidation.checkAmount($('input[name="min_amount"]'));
        var fix_amount_mand_only = velovalidation.checkMandatory($('input[name="min_amount"]'));
        if (fix_amount_mand !== true)
        {
            error = true;
            $('input[name="min_amount"]').addClass('error_field');
            $('input[name="min_amount"]').after('<span class="error_message">' + fix_amount_mand + '</span>');
        } else if (fix_amount_mand_only !== true) {
            error = true;
            $('input[name="min_amount"]').addClass('error_field');
            $('input[name="min_amount"]').after('<span class="error_message">' + fix_amount_mand_only + '</span>');
        }
        
        var fix_amount_mand = velovalidation.isNumeric($('input[name="validity"]'));
        var fix_amount_mand_only = velovalidation.checkMandatory($('input[name="validity"]'));
        if (fix_amount_mand !== true)
        {
            error = true;
            $('input[name="validity"]').addClass('error_field');
            $('input[name="validity"]').after('<span class="error_message">' + fix_amount_mand + '</span>');
        } else if (fix_amount_mand_only !== true) {
            error = true;
            $('input[name="validity"]').addClass('error_field');
            $('input[name="validity"]').after('<span class="error_message">' + fix_amount_mand_only + '</span>');
        }
            
        if ($('select[name="discount_type"]').val() == 'fixed') {
            var fix_amount_mand = velovalidation.checkAmount($('input[name="fix_amount"]'));
            var fix_amount_mand_only = velovalidation.checkMandatory($('input[name="fix_amount"]'));
            if (fix_amount_mand !== true)
            {
                error = true;
                $('input[name="fix_amount"]').addClass('error_field');
                $('input[name="fix_amount"]').after('<span class="error_message">' + fix_amount_mand + '</span>');
            } else if (fix_amount_mand_only !== true) {
                error = true;
                $('input[name="fix_amount"]').addClass('error_field');
                $('input[name="fix_amount"]').after('<span class="error_message">' + fix_amount_mand_only + '</span>');
            }

        } else {
            var percent_amount_mand = velovalidation.checkPercentage($('input[name="percent_amount"]'));
            var percent_amount_mand_only = velovalidation.checkMandatory($('input[name="percent_amount"]'));
            if (percent_amount_mand !== true)
            {
                error = true;
                $('input[name="percent_amount"]').addClass('error_field');
                $('input[name="percent_amount"]').parent().after('<span class="error_message">' + percent_amount_mand + '</span>');
            } else if (percent_amount_mand_only != true) {

                error = true;
                $('input[name="percent_amount"]').addClass('error_field');
                $('input[name="percent_amount"]').parent().after('<span class="error_message">' + percent_amount_mand_only + '</span>');
            }
        }

        if (error) {
            $('html, body').animate({
                scrollTop: $(".error_message").offset().top - 200
            }, 1000);
            return false;
        }
        $("button[name='submitAddkb_booking_discount_rules']").attr('disabled', 'disabled');
        $('#kb_booking_discount_rules_form').submit();
    });



    $('button[name="add_product_type_submit_btn"]').click(function () {
        var error = false;
        $(".error_message").remove();
        if (typeof ($('input[name="product_type"]:checked').val()) == 'undefined') {
            error = true;
            $('input[name="product_type"]').closest('.col-lg-9').append('<span class="error_message">'+select_product_type+'</span>');
        }

        if (error) {
            $('html, body').animate({
                scrollTop: $(".error_message").offset().top - 200
            }, 1000);
            return false;
        }
    });


    $('#product_room_images').on('change', function () {
        $("#product_room_images").closest('.form-group').find('.input-group').removeClass('error_field');
        $('.error_message').remove();
        if ($('#product_room_images').get(0).files.length) {
            for (var i = 0; i < $('#product_room_images').get(0).files.length; ++i) {
                var file1 = $("#product_room_images").get(0).files[i].name;
                if (file1) {
                    var file_size = $("#product_room_images").get(0).files[i].size;
                    if (file_size < 2097152) {
                        var ext = file1.split('.').pop().toLowerCase();
                        if ($.inArray(ext, ['jpg', 'jpeg', 'gif', 'png']) === -1) {
                            showErrorMessage(file1 +': '+kb_image_valid);
                            $("#product_room_images").closest('.form-group').find('.input-group').addClass('error_field');
                            $('#product_room_images').closest('.form-group').after('<p class="error_message">'+ file1 +': '+kb_image_valid + '</p>');
                        }
                    } else {
                        showErrorMessage(file1 +': '+kb_image_size_valid);
                        $("#product_room_images").closest('.form-group').find('.input-group').addClass('error_field');
                        $('#product_room_images').closest('.form-group').after('<p class="error_message">'+ file1 +': '+kb_image_size_valid + '</p>');
                    }
                }
            }
        }
    });


});

function validateKbProductForm(btn)
{

    var error = false;
    var is_error = 0;
    $(".error_message").remove();
    $('.kb-pc-error-icon').hide();
    var config_error = false;
    var booking_error = false;
    var location_error = false;
//      var image_error = false;
    var date_error = false;
    var facilities_error = false;
    $('input[name^="product_name_"]').removeClass('error_field');
    $('input[name="quantity"]').closest('.input-group').removeClass('error_field');
    $('input[name="price"]').closest('.input-group').removeClass('error_field');
    $('input[name="max_days"]').closest('.input-group').removeClass('error_field');
    $('input[name="max_hours"]').closest('.input-group').removeClass('error_field');
    $('input[name="min_hours"]').closest('.input-group').removeClass('error_field');
    $('input[name="min_days"]').closest('.input-group').removeClass('error_field');
    $('input[name="longitude"]').removeClass('error_field');
    $('input[name="latitude"]').removeClass('error_field');
    $('input[name="address"]').removeClass('error_field');
//    $('input[name="product_ean13"]').removeClass('error_field');
//    $('input[name="product_upc"]').removeClass('error_field');
    $('input[name="product_reference"]').removeClass('error_field');
    $('input[name="categoryBox[]"]').closest('.panel').removeClass('error_field');
    $('input[name^="product_name_"]').each(function () {
        var name_error = velovalidation.checkMandatory($(this));
        if (name_error != true) {
            error = true;
            config_error = true;

            if (is_error < 1) {
                $(this).parents('.col-lg-5').last().append('<span class="error_message">' + name_error + ' ' + check_for_all + '</span>');
                is_error++;
            }
            $(this).addClass('error_field');
        }
    });

    var product_reference_empty = velovalidation.checkMandatory($('input[name="product_reference"]'));
    if (product_reference_empty != true) {
        error = true;
        config_error = true;
        $('input[name="product_reference"]').addClass('error_field');
        $('input[name="product_reference"]').after('<p class="error_message">' + product_reference_empty + '</p>');
    }

    if ($('select[name="star_rating"]').length) {
        if ($('select[name="star_rating"]').val() == '') {
            error = true;
            booking_error = true;
            $('select[name="star_rating"]').addClass('error_field');
            $('select[name="star_rating"]').after('<p class="error_message">' + star_rating_empty + '</p>');
        }
    }

    if ($('input[name="quantity"]').length) {
        var quantity_empty = velovalidation.checkMandatory($('input[name="quantity"]'));
        if (quantity_empty != true) {
            error = true;
            booking_error = true;
            $('input[name="quantity"]').closest('.input-group').addClass('error_field');
            $('input[name="quantity"]').closest('.input-group').after('<p class="error_message">' + quantity_empty + '</p>');
        } else {
            var quantity_valid = velovalidation.isNumeric($('input[name="quantity"]'));
            if (quantity_valid != true) {
                error = true;
                booking_error = true;
                $('input[name="quantity"]').closest('.input-group').addClass('error_field');
                $('input[name="quantity"]').closest('.input-group').after('<span class="error_message">' + quantity_valid + '</span>');
            }
        }
    }
    var price_empty = velovalidation.checkMandatory($('input[name="price"]'));
    if (price_empty != true) {
        error = true;
        booking_error = true;
        $('input[name="price"]').closest('.input-group').addClass('error_field');
        $('input[name="price"]').closest('.input-group').after('<p class="error_message">' + price_empty + '</p>');
    } else {
        var price_valid = velovalidation.checkAmount($('input[name="price"]'));
        if (price_valid != true) {
            error = true;
            booking_error = true;
            $('input[name="price"]').closest('.input-group').addClass('error_field');
            $('input[name="price"]').closest('.input-group').after('<span class="error_message">' + price_valid + '</span>');
        }
    }
    if ($('input[name="min_days"]').length && $('input[name="min_days"]').is(':visible')) {
        var min_days_empty = velovalidation.checkMandatory($('input[name="min_days"]'));
        if (min_days_empty != true) {
            error = true;
            booking_error = true;
            $('input[name="min_days"]').closest('.input-group').addClass('error_field');
            $('input[name="min_days"]').closest('.input-group').after('<p class="error_message">' + min_days_empty + '</p>');
        } else {
            var min_days_valid = velovalidation.isNumeric($('input[name="min_days"]'));
            if (min_days_valid != true) {
                error = true;
                booking_error = true;
                $('input[name="min_days"]').closest('.input-group').addClass('error_field');
                $('input[name="min_days"]').closest('.input-group').after('<span class="error_message">' + min_days_valid + '</span>');
            }
        }
    }

    if ($('input[name="max_days"]').length && $('input[name="max_days"]').is(':visible')) {
        var min_days_empty = velovalidation.checkMandatory($('input[name="max_days"]'));
        if (min_days_empty != true) {
            error = true;
            booking_error = true;
            $('input[name="max_days"]').closest('.input-group').addClass('error_field');
            $('input[name="max_days"]').closest('.input-group').after('<p class="error_message">' + min_days_empty + '</p>');
        } else {
            var min_days_valid = velovalidation.isNumeric($('input[name="max_days"]'));
            if (min_days_valid != true) {
                error = true;
                booking_error = true;
                $('input[name="max_days"]').closest('.input-group').addClass('error_field');
                $('input[name="max_days"]').closest('.input-group').after('<span class="error_message">' + min_days_valid + '</span>');
            } else {
                var min_days = parseInt($('input[name="min_days"]').val().trim());
                var max_days = parseInt($('input[name="max_days"]').val().trim());
                if (max_days <= min_days) {
                    error = true;
                    booking_error = true;
                    $('input[name="max_days"]').closest('.input-group').addClass('error_field');
                    $('input[name="max_days"]').closest('.input-group').after('<span class="error_message">' + min_max_days_valid + '</span>');
                }
            }
        }
    }


    if ($('input[name="min_hours"]').length) {
        var min_hours_empty = velovalidation.checkMandatory($('input[name="min_hours"]'));
        if (min_hours_empty != true) {
            error = true;
            booking_error = true;
            $('input[name="min_hours"]').closest('.input-group').addClass('error_field');
            $('input[name="min_hours"]').closest('.input-group').after('<p class="error_message">' + min_hours_empty + '</p>');
        } else {
            var min_hours_valid = velovalidation.isNumeric($('input[name="min_hours"]'));
            if (min_hours_valid != true) {
                error = true;
                booking_error = true;
                $('input[name="min_hours"]').closest('.input-group').addClass('error_field');
                $('input[name="min_hours"]').closest('.input-group').after('<span class="error_message">' + min_hours_valid + '</span>');
            }
        }
        if ($('input[name="max_hours"]').length) {
            var min_hours_empty = velovalidation.checkMandatory($('input[name="max_hours"]'));
            if (min_hours_empty != true) {
                error = true;
                booking_error = true;
                $('input[name="max_hours"]').closest('.input-group').addClass('error_field');
                $('input[name="max_hours"]').closest('.input-group').after('<p class="error_message">' + min_hours_empty + '</p>');
            } else {
                var min_hours_valid = velovalidation.isNumeric($('input[name="max_hours"]'));
                if (min_hours_valid != true) {
                    error = true;
                    booking_error = true;
                    $('input[name="max_hours"]').closest('.input-group').addClass('error_field');
                    $('input[name="max_hours"]').closest('.input-group').after('<span class="error_message">' + min_hours_valid + '</span>');
                } else {
                    var min_hours = parseInt($('input[name="min_hours"]').val().trim());
                    var max_hours = parseInt($('input[name="max_hours"]').val().trim());
                    if (min_hours >= max_hours) {
                        error = true;
                        booking_error = true;
                        $('input[name="max_hours"]').closest('.input-group').addClass('error_field');
                        $('input[name="max_hours"]').closest('.input-group').after('<span class="error_message">' + min_max_hrs_valid + '</span>');
                    }
                }
            }

        }
    }


    $('.kb-datetime-row').find('.error_message').remove();
    $('.kb-datetime-row').find('input[type="text"]').removeClass('error_field');

    $('.kb-datetime-row').each(function () {
        var date_from_mand = velovalidation.checkMandatory($(this).find('.kb_date_from'));
        if (date_from_mand != true) {
            error = true;
            date_error = true;
            $(this).find('.kb_date_from').addClass('error_field');
            $(this).find('.kb_date_from').closest('.input-group').after('<span class="error_message" style="text-align: left;">' + date_from_mand + '</span>');
        }
        var date_to_mand = velovalidation.checkMandatory($(this).find('.kb_end_date'));
        if (date_to_mand != true) {
            error = true;
            date_error = true;
            $(this).find('.kb_end_date').addClass('error_field');
            $(this).find('.kb_end_date').closest('.input-group').after('<span class="error_message" style="text-align: left;">' + date_to_mand + '</span>');
        } else {
            var start_date = Date.parse($(this).find('.kb_date_from').val());
            var end_date = Date.parse($(this).find('.kb_end_date').val());
            if (parseInt(end_date) <= parseInt(start_date)) {
                error = true;
                date_error = true;
                $(this).find('.kb_date_from').addClass('error_field');
                $(this).find('.kb_end_date').addClass('error_field');
                $(this).find('.kb_booking_dates_range').append('<span class="error_message" style="text-align: left;">' + end_date_error + '</span>');
            }
        }


        $(this).find('.kb-time-tr').each(function () {
            if ($('select[name="period_type"]').val() == 'date_time') {
                var time_from_mand = velovalidation.checkMandatory($(this).find('.kb_time_from'));
                if (time_from_mand != true) {
                    error = true;
                    date_error = true;
                    $(this).find('.kb_time_from').addClass('error_field');
                    $(this).find('.kb_time_from').closest('.input-group').after('<span class="error_message" style="text-align: left;">' + time_from_mand + '</span>');
                }
                var time_to_mand = velovalidation.checkMandatory($(this).find('.kb_time_to'));
                if (time_to_mand != true) {
                    error = true;
                    date_error = true;
                    $(this).find('.kb_time_to').addClass('error_field');
                    $(this).find('.kb_time_to').closest('.input-group').after('<span class="error_message" style="text-align: left;">' + time_to_mand + '</span>');
                } else {
                    var start_time = Date.parse('2019-01-01 ' + $(this).find('.kb_time_from').val());
                    var end_time = Date.parse('2019-01-01 ' + $(this).find('.kb_time_to').val());
                    if (parseInt(end_time) <= parseInt(start_time)) {
                        error = true;
                        date_error = true;
                        $(this).find('.kb_time_from').addClass('error_field');
                        $(this).find('.kb_time_to').addClass('error_field');
                        $(this).after('<span class="error_message" style="text-align: left;">' + end_time_error + '</span>');
                    }
                }
            }
            var time_price_mand = velovalidation.checkMandatory($(this).find('.kb_time_price'));
            if (time_price_mand != true) {
                error = true;
                date_error = true;
                $(this).find('.kb_time_price').addClass('error_field');
                $(this).find('.kb_time_price').closest('.input-group').after('<span class="error_message" style="text-align: left;">' + time_price_mand + '</span>');
            } else {
                var time_price_valid = velovalidation.checkAmount($(this).find('.kb_time_price'));
                if (time_price_valid != true) {
                    error = true;
                    date_error = true;
                    $(this).find('.kb_time_price').addClass('error_field');
                    $(this).find('.kb_time_price').closest('.input-group').after('<span class="error_message" style="text-align: left;">' + time_price_valid + '</span>');
                }
            }
        });

    });
    if (!date_error) {
        var abc = [];
        $('.kb-datetime-row').each(function () {
            abc.push($(this).find('.kb_date_from').val().trim());
            abc.push($(this).find('.kb_end_date').val().trim());
        });

        var i, j;
        if (abc.length % 2 !== 0)
            throw new TypeError('Date range length must be a multiple of 2');
        for (i = 0; i < abc.length - 2; i += 2) {
            for (j = i + 2; j < abc.length; j += 2) {
                if (
                        dateRangeOverlaps(
                                abc[i], abc[i + 1],
                                abc[j], abc[j + 1]
                                )
                        )
                {
                    error = true;
                    date_error = true;
                    $('.kb-date-time-block').find('p.help-block').before('<p class="error_message">'+kb_date_override_string+' ' + abc[i] + ' '+kb_and_string+' ' + abc[i + 1] + ' '+kb_to_string+' ' + abc[j] + ' '+kb_and_string+' ' + abc[j + 1] + '</p>');
                }

            }
        }

    }
    if ((typeof $('input[name="categoryBox[]"]:checked').val()) == 'undefined') {
        error = true;
        config_error = true;
        $('input[name="categoryBox[]"]').closest('.panel').addClass('error_field');
        $('input[name="categoryBox[]"]').closest('.col-lg-9').append('<span class="error_message">' + store_category_mand + '</span>');
    }


    if ($('input[name="enable_product_map"]:checked').val() == '1') {
        var address_empty = velovalidation.checkMandatory($('input[name="address"]'));
        if (address_empty != true) {
            error = true;
            location_error = true;
            $('input[name="address"]').addClass('error_field');
            $('input[name="address"]').after('<span class="error_message">' + address_empty + '</span>');
        }
        var longitude_empty = velovalidation.checkMandatory($('input[name="longitude"]'));
        if (longitude_empty != true) {
            error = true;
            location_error = true;
            $('input[name="longitude"]').addClass('error_field');
            $('input[name="longitude"]').after('<span class="error_message">' + longitude_empty + '</span>');
        }
        var latitude_empty = velovalidation.checkMandatory($('input[name="latitude"]'));
        if (latitude_empty != true) {
            error = true;
            location_error = true;
            $('input[name="latitude"]').addClass('error_field');
            $('input[name="latitude"]').after('<span class="error_message">' + latitude_empty + '</span>');
        }
    }

    if (config_error) {
        $('#kb_add_product_form_tab_general .kb-pc-error-icon').show();
    }
    if (booking_error) {
        $('#kb_add_product_form_tab_price .kb-pc-error-icon').show();
    }
    if (location_error) {
        $('#kb_add_product_form_tab_location .kb-pc-error-icon').show();
    }
    if (date_error) {
        $('#kb_add_product_form_tab_days .kb-pc-error-icon').show();
    }

    if (error) {
        $('html, body').animate({
            scrollTop: $(".error_message").offset().top - 200
        }, 1000);
        return false;
    }
    $('#kb_booking_product_form').append('<input type="hidden" name="' + btn + '_kb_product_btn" value="1">');
    $("button[name='submitBKAddproduct']").attr('disabled', 'disabled');
    $("button[name='submitBKAddproductAndStay']").attr('disabled', 'disabled');
    $('#kb_booking_product_form').submit();
}

function dateRangeOverlaps(a_start, a_end, b_start, b_end) {
    if (a_start <= b_start && b_start <= a_end)
        return true; // b starts in a
    if (a_start <= b_end && b_end <= a_end)
        return true; // b ends in a
    if (b_start < a_start && a_end < b_end)
        return true; // a in b
    return false;
}
function multipleDateRangeOverlaps(abc) {

    return false;
}

function checkTinyMCERequired(val) {
    var new_str = str_replace_all(val, '<p>', '');
    new_str = str_replace_all(new_str, '</p>', '');
    new_str = new_str.trim();
    var return_val = true;
    if (new_str == '') {
        return_val = empty_field;
    }
    return return_val;
}

function str_replace_all(string, str_find, str_replace) {
    try {
        return string.replace(new RegExp(str_find, "gi"), str_replace);
    } catch (ex) {
        return string;
    }
}