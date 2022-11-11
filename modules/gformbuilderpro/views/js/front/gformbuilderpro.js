/**
 * This is main js file. Don't edit the file if you want to update module in future.
 * 
 * @author    Globo Software Solution JSC <contact@globosoftware.net>
 * @copyright 2015 GreenWeb Team
 * @link	     http://www.globosoftware.net
 * @license   please read license in file license.txt
 */
if (typeof $.uniform !== 'undefined')
    if (typeof $.uniform.defaults !== 'undefined') {
        if (typeof contact_fileDefaultHtml !== 'undefined')
            $.uniform.defaults.fileDefaultHtml = contact_fileDefaultHtml;
        if (typeof contact_fileButtonHtml !== 'undefined')
            $.uniform.defaults.fileButtonHtml = contact_fileButtonHtml;
    }
var CaptchaCallback = function() {
    if ($('.gformbuilderpro_form .g-recaptcha').length > 0)
        $('.gformbuilderpro_form .g-recaptcha').each(function(index, el) {
            if (!$(this).hasClass('added_grecaptcha')) {
                if ($(this).data('sitekey') != '') {
                    grecaptcha.render(this, { 'sitekey': $(this).data('sitekey') });
                    $(this).addClass('added_grecaptcha');
                }
            }
        });
};

function init_gmap() {
    $('.google-maps').each(function() {
        map_description = $(this).data('description');
        value = $(this).data('value');
        value_latlng = value.split(',');
        name = $(this).data('name');
        label = $(this).data('label');
        var myOptions = {
            zoom: 15,
            center: new google.maps.LatLng(value_latlng[0], value_latlng[1]),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById('google-maps-' + name), myOptions);
        marker = new google.maps.Marker({
            map: map,
            position: new google.maps.LatLng(value_latlng[0], value_latlng[1])
        });
        infowindow = new google.maps.InfoWindow({
            content: "<strong>" + label + "</strong><br/>" + map_description
        });
        google.maps.event.addListener(marker, 'click', function() {
            infowindow.open(map, marker);
        });
        infowindow.open(map, marker);
        google.maps.event.trigger(map, "resize");
    });
}

function loadrecaptchav3(sitekey) {
    grecaptcha.ready(function() {
        grecaptcha.execute(sitekey, { action: 'gformbuilderpro' }).then(function(token) {
            var recaptchaResponse = document.getElementById('recaptchaResponse');
            recaptchaResponse.value = token;
        });
    });
}

function getValuecheck(value_1) {
    var val = '';
    if ($('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').hasClass('fileupload_box')) {

        val = $('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').find('input').val();
    } else if ($('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').hasClass('select_box')) {

        val = $('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').find('select').val();
    } else if ($('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').hasClass('color_box')) {
        val = $('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').find('input').val();
    } else if ($('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').hasClass('input_box') ||
        $('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').hasClass('time_box') ||
        $('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').hasClass('datepicker_box') ||
        $('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').hasClass('slider_box') ||
        $('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').hasClass('spinner_box')) {

        val = $('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').find('input').val();
    } 
    if (
        $('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').hasClass('checkbox_box') ||
        $('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').hasClass('product_box') ||
        $('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').hasClass('imagethumb_box') ||
        $('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').hasClass('yesno_box')
    ) {
        if ($('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').find('input[type="checkbox"]').length > 0) {
            $('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').find('input[type="checkbox"]').each(
                function() {
                    if ($(this).is(":checked")) {
                        val += $(this).val() + ',';
                    }
                }
            );
            val = val.slice(0, -1);
        }
    } 
    if (
        $('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').hasClass('radio_box') ||
        $('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').hasClass('colorchoose_box') ||
        $('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').hasClass('survey_box') ||
        $('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').hasClass('rating_box') ||
        $('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').hasClass('product_box') ||
        $('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').hasClass('imagethumb_box')
    ) {
        if ($('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').find('input[type="radio"]').length > 0) {
            $('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').find('input[type="radio"]').each(
                function() {
                    if ($(this).is(":checked")) {
                        val += $(this).val() + ',';
                    }
                }
            );
            val = val.slice(0, -1);
        }
    } 
    return val;
}
function ConnditionDisplay(gFormConditions) {
    if (gFormConditions) {
        gFormConditions = JSON.parse(gFormConditions);
        $.each(gFormConditions, function(index, value) {
            if (index > 1 && value['condition'] > 0 && value['condition_listoptions']) {
                if (value['condition_must_match'] == '0') {
                    var condition_for = true;
                } else {
                    var condition_for = false;
                }
                $.each(value['condition_listoptions'], function(index_1, value_1) {
                    if (value_1['id_field'] != '0') {
                        if ($('#gformbuilderpro_' + value_1['id_field']).length > 0) {
                            var value_check = getValuecheck(value_1);
                            var value_condition = value_1['conditionvalue'];
                            if ($('#gformbuilderpro_' + value_1['id_field'] + '> .form-group').hasClass('fileupload_box')) {
                                if (value_check != '') {
                                    value_check = '1';
                                } else {
                                    value_check = '0';
                                }
                            }
                            switch (value_1['condition']) {
                                case 'IS_EQUAL':
                                    if (value_check == value_condition) {
                                        if (value['condition_must_match'] == '1') {
                                            condition_for = true;
                                            return false;
                                        }
                                    } else {
                                        if (value['condition_must_match'] == '0') {
                                            condition_for = false;
                                            return false;
                                        }
                                    }
                                    break;
                                case 'ISNOT_EQUAL':
                                    if (value_check != value_condition) {
                                        if (value['condition_must_match'] == '1') {
                                            condition_for = true;
                                            return false;
                                        }
                                    } else {
                                        if (value['condition_must_match'] == '0') {
                                            condition_for = false;
                                            return false;
                                        }
                                    }
                                    break;
                                case 'IS_GREATER':
                                    if ( parseInt(value_check)  > parseInt(value_condition)) {
                                        if (value['condition_must_match'] == '1') {
                                            condition_for = true;
                                            return false;
                                        }
                                    } else {
                                        if (value['condition_must_match'] == '0') {
                                            condition_for = false;
                                            return false;
                                        }
                                    }
                                    break;
                                case 'IS_LESS':
                                    if (parseInt(value_check) < parseInt(value_condition)) {
                                        if (value['condition_must_match']) {
                                            condition_for = true;
                                            return false;
                                        }
                                    } else {
                                        if (value['condition_must_match'] == '0') {
                                            condition_for = false;
                                            return false;
                                        }
                                    }
                                    break;
                                case 'STARTS':
                                    if (value_check.startsWith(value_condition)) {
                                        if (value['condition_must_match'] == '1') {
                                            condition_for = true;
                                            return false;
                                        }
                                    } else {
                                        if (value['condition_must_match'] == '0') {
                                            condition_for = false;
                                            return false;
                                        }
                                    }
                                    break;
                                case 'ENDS':
                                    if (value_check.endsWith(value_condition)) {
                                        if (value['condition_must_match'] == '1') {
                                            condition_for = true;
                                            return false;
                                        }
                                    } else {
                                        if (value['condition_must_match'] == '0') {
                                            condition_for = false;
                                            return false;
                                        }
                                    }
                                    break;
                                case 'IS_CONTAINS':
                                    if (Array.isArray(value_check)){
                                        value_check = value_check.join(',');
                                    }
                                    if (value_check != null && value_check.indexOf(value_condition) >= 0 ) {
                                        if (value['condition_must_match'] == '1') {
                                            condition_for = true;
                                            return false;
                                        }
                                    } else {
                                        if (value['condition_must_match'] == '0') {
                                            condition_for = false;
                                            return false;
                                        }
                                    }
                                    break;
                                case 'ISNOT_CONTAINS':
                                    if (Array.isArray(value_check)){
                                        value_check = value_check.join(',');
                                    }
                                    if (value_check != null &&  value_check.indexOf(value_condition) < 0 ) {
                                        if (value['condition_must_match'] == '1') {
                                            condition_for = true;
                                            return false;
                                        }
                                    } else {
                                        if (value['condition_must_match'] == '0') {
                                            condition_for = false;
                                            return false;
                                        }
                                    }
                                    break;
                            }
                        }
                    }
                });
                if (value['condition_must_match'] == '1') {
                    if (value['condition_display'] == '1') {
                        if (condition_for) {
                            $('#gformbuilderpro_' + index).addClass('gformnone');
                            if ($('#gformbuilderpro_' + index).find('.form-group').hasClass("input_box") 
                            || $('#gformbuilderpro_' + index).find('.form-group').hasClass("select_box")) {
                                if ($('#gformbuilderpro_' + index).find('label').hasClass("required_label")) {
                                    $('#gformbuilderpro_' + index).find('select').removeAttr("required");
                                    $('#gformbuilderpro_' + index).find('input').removeAttr("required");
                                }
                            }
                        } else {
                            $('#gformbuilderpro_' + index).removeClass('gformnone');
                            if ($('#gformbuilderpro_' + index).find('.form-group').hasClass("input_box") 
                            || $('#gformbuilderpro_' + index).find('.form-group').hasClass("select_box")) {
                                if ($('#gformbuilderpro_' + index).find('label').hasClass("required_label")) {
                                    $('#gformbuilderpro_' + index).find('select').attr("required", "required");
                                    $('#gformbuilderpro_' + index).find('input').attr("required", "required");
                                }
                            }
                        }
                    } else {
                        if (condition_for) {
                            $('#gformbuilderpro_' + index).removeClass('gformnone');
                            if ($('#gformbuilderpro_' + index).find('.form-group').hasClass("input_box") 
                            || $('#gformbuilderpro_' + index).find('.form-group').hasClass("select_box")) {
                                if ($('#gformbuilderpro_' + index).find('label').hasClass("required_label")) {
                                    $('#gformbuilderpro_' + index).find('select').attr("required", "required");
                                    $('#gformbuilderpro_' + index).find('input').attr("required", "required");
                                }
                            }
                        } else {
                            $('#gformbuilderpro_' + index).addClass('gformnone');
                            if ($('#gformbuilderpro_' + index).find('.form-group').hasClass("input_box") 
                            || $('#gformbuilderpro_' + index).find('.form-group').hasClass("select_box")) {
                                if ($('#gformbuilderpro_' + index).find('label').hasClass("required_label")) {
                                    $('#gformbuilderpro_' + index).find('select').removeAttr("required");
                                    $('#gformbuilderpro_' + index).find('input').removeAttr("required");
                                }
                            }
                        }
                    }
                } else {
                    if (value['condition_display'] == '1') {
                        if (condition_for) {
                            $('#gformbuilderpro_' + index).addClass('gformnone');
                            if ($('#gformbuilderpro_' + index).find('.form-group').hasClass("input_box") 
                            || $('#gformbuilderpro_' + index).find('.form-group').hasClass("select_box")) {
                                if ($('#gformbuilderpro_' + index).find('label').hasClass("required_label")) {
                                    $('#gformbuilderpro_' + index).find('select').removeAttr("required");
                                    $('#gformbuilderpro_' + index).find('input').removeAttr("required");
                                }
                            }
                        } else {
                            $('#gformbuilderpro_' + index).removeClass('gformnone');
                            if ($('#gformbuilderpro_' + index).find('.form-group').hasClass("input_box") 
                            || $('#gformbuilderpro_' + index).find('.form-group').hasClass("select_box")) {
                                if ($('#gformbuilderpro_' + index).find('label').hasClass("required_label")) {
                                    $('#gformbuilderpro_' + index).find('select').attr("required", "required");
                                    $('#gformbuilderpro_' + index).find('input').attr("required", "required");
                                }
                            }
                        }
                    } else {
                        if (condition_for) {
                            $('#gformbuilderpro_' + index).removeClass('gformnone');
                            if ($('#gformbuilderpro_' + index).find('.form-group').hasClass("input_box") 
                            || $('#gformbuilderpro_' + index).find('.form-group').hasClass("select_box")) {
                                if ($('#gformbuilderpro_' + index).find('label').hasClass("required_label")) {
                                    $('#gformbuilderpro_' + index).find('select').attr("required", "required");
                                    $('#gformbuilderpro_' + index).find('input').attr("required", "required");
                                }
                            }
                        } else {
                            $('#gformbuilderpro_' + index).addClass('gformnone');
                            if ($('#gformbuilderpro_' + index).find('.form-group').hasClass("input_box") 
                            || $('#gformbuilderpro_' + index).find('.form-group').hasClass("select_box")) {
                                if ($('#gformbuilderpro_' + index).find('label').hasClass("required_label")) {
                                    $('#gformbuilderpro_' + index).find('select').removeAttr("required");
                                    $('#gformbuilderpro_' + index).find('input').removeAttr("required");
                                }
                            }
                        }
                    }
                }
            }
        });
    }
}
function whosaleProductActive(el, number_qty ) {
    if (el.closest('.gform_allcombin').find('.gform-combination-checkbox').length > 0) {
        el.closest('.gform_allcombin').find('.gform-combination-checkbox').each(function(){
            if ($(this).is(':checked')) {
                var qty = $(this).closest('tr').find('.variant-quantity').val();
                number_qty =  parseInt(number_qty) + parseInt(qty);
            }
        });
    }
    if (el.closest('.gform_card').find('.gform-discounts .gformdiscount-desc').length > 0) {
        el.closest('.gform_card').find('.gform-discounts .gformdiscount-desc').each(function() {
            $(this).closest('.gform_card').find('.gform-discounts .gformdiscount-desc').removeClass('active');
            if (($(this).data('min') < number_qty && $(this).data('max') > number_qty) ||  ($(this).data('min') == number_qty)) {
                $(this).addClass('active');
                return false;
            }
        });
    }
}
function getPriceWhosaleProduct (el) {
    var formsubmit  = el.closest('.gformbuilderpro_form').find('form');
    var id_fields   = el.closest('.itemfield').attr("id").match(/gformbuilderpro_(\d*)/);
    var id_field = id_fields[1];
    var formURL = formsubmit.attr("action");
    if (window.FormData !== undefined) {
        var formData = new FormData();
        formData.append('getPriceWhosaleProduct', '1');
        formData.append('id_field', id_field);
        if (el.closest('.itemfield').find('.gform_card').find('.gform_allcombin').length > 0) {
            el.closest('.itemfield').find('.gform_card').find('.gform_allcombin').find('.gform-combination-checkbox').each(function() {
                if ($(this).is(':checked')) {
                    var id_product = $(this).closest('.gform_card').find('.gform-checkbox').val();
                    var name = $(this).attr('name');
                    var value = $(this).val();
                    var name_qty = $(this).closest('tr').find('.variant-quantity').attr('name');
                    var value_qty = $(this).closest('tr').find('.variant-quantity').val();
                    var name_discount = 'wholesaleboxProductDiscount['+id_product+'][value]';
                    var value_discount = 0;
                    var name_discount_type = 'wholesaleboxProductDiscount['+id_product+'][type]';
                    var value_discount_type = 0;
                    if ($(this).closest('.gform_card').find('.gformdiscount-desc.active').length > 0) {
                        value_discount = $(this).closest('.gform_card').find('.gformdiscount-desc.active').data('value');
                        value_discount_type = $(this).closest('.gform_card').find('.gformdiscount-desc.active').data('type');
                    }
                    formData.append(name, value);
                    formData.append(name_qty, value_qty);
                    formData.append(name_discount, value_discount);
                    formData.append(name_discount_type, value_discount_type);
                }
            });
        }
        $.ajax({
            url: formURL ,
            type: 'POST',
            data: formData,
            mimeType: "multipart/form-data",
            contentType: false,
            cache: false,
            processData: false,
            dataType: 'json',
            success: function(data, textStatus, jqXHR) {
                if (data.products) {
                    $.each(data.products,function (key,vals) {
                        if (vals)
                            $.each(vals,function (key_,val_) {
                                if ($('#wholesale-trbox-'+ key +'-'+key_).length > 0) {
                                    if (val_.check_discount == 1) {
                                        $('#wholesale-trbox-'+ key +'-'+key_).find('.gform_discount-price').text(val_.old_price);
                                    } else {
                                        $('#wholesale-trbox-'+ key +'-'+key_).find('.gform_discount-price').text('');
                                    }
                                    $('#wholesale-trbox-'+ key +'-'+key_).find('.gform_total-price').text(val_.price);
                                }
                            });
                    });
                    if ($('#gformbuilderpro_'+ data.id_field).length > 0) {
                        $('#gformbuilderpro_'+ data.id_field).find('.gformwholesale-total').find('.gformwholesale-subtotalprice-label').text(data.totalprice);
                    }
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {

            }
        });
    }
}
$(window).load(function() {
    if ($('.gformbuilderpro_form .g-recaptcha').length > 0) {
        var script = document.createElement("script");
        script.src = "https:/" + "/www.google.com/recaptcha/api.js?onload=CaptchaCallback&render=explicit";
        script.type = "text/javascript";
        document.getElementsByTagName("head")[0].appendChild(script);
    }
    if ($('#recaptchaResponse').length > 0) {
        var sitekey = $('#recaptchaResponse').attr('data-sitekey');
        var script = document.createElement("script");
        script.src = "https:/" + "/www.google.com/recaptcha/api.js?render=" + sitekey;
        script.type = "text/javascript";
        document.getElementsByTagName("head")[0].appendChild(script);
        gv3interval = setInterval(
            function() {
                if (typeof grecaptcha === "undefined") {} else {
                    if ($('#recaptchaResponse').val() == '') {
                        loadrecaptchav3(sitekey);
                        clearInterval(gv3interval);
                    }
                };
            }, 500);
    }


    if ($('.google-maps').length > 0) {
        _gmap_key = '';
        $('.google-maps').each(function() {
            gmap_key = $(this).data('gmap_key');
            if (gmap_key != '') {
                _gmap_key = '&key=' + gmap_key;
                return false;
            }
        });
        var script = document.createElement("script");
        script.src = "https:/" + "/maps.googleapis.com/maps/api/js?v=3.exp&callback=init_gmap" + _gmap_key;
        script.type = "text/javascript";
        document.getElementsByTagName("head")[0].appendChild(script);
        //google.maps.event.addDomListener(window, 'load', init_gmap);
    }
});

$(document).ready(function() {
    if ($('.gformbuilderpro_form').length > 0) {
        $('.gformbuilderpro_form').each(function() {
            if ($(this).find('input[name="Conditions"]').length > 0) {
                var gFormConditions = $(this).find('input[name="Conditions"]').val();
                ConnditionDisplay(gFormConditions);
            }
            if ($(this).find('button[name="submitForm"]').length == 0) {
                var submitbutton = '<div class="formbuilder_group"><div class="formbuilder_column col-md-12 col-sm-12 col-xs-12"><div class="itemfield_wp"><div class="itemfield"><button type="submit" name="submitForm" id="submitForm" class="button btn btn-default button-medium"><span>Submit</span></button></div></div></div></div>';
                $(this).find('.gformbuilderpro_content').append(submitbutton);
            }
        });
    }
    $('.gformbuilderpro_form .select_box select, .gformbuilderpro_form input').on('change', function() {
        if ($(this).closest('.gformbuilderpro_form').find('input[name="Conditions"]').length > 0) {
            var gFormConditions = $(this).closest('.gformbuilderpro_form').find('input[name="Conditions"]').val();
            ConnditionDisplay(gFormConditions);
        }
    });
    $('.gformbuilderpro_form input[type=text]').on('change invalid', function() {
        var textfield = $(this).get(0);
        textfield.setCustomValidity('');
        if (!textfield.validity.valid) {
            textfield.setCustomValidity($('#gformbuilderpro_formValidity').val());
        }
    });
    if ($('.htmlinput').length > 0) {
        tinymce.init({ selector: '.htmlinput' });
    }
    if ($('.mColorPickerInput').length > 0)
        $('.mColorPickerInput').each(function() {
            $(this).minicolors({
                control: $(this).attr('data-control') || 'hue',
                defaultValue: $(this).attr('data-defaultValue') || '',
                format: $(this).attr('data-format') || 'hex',
                keywords: $(this).attr('data-keywords') || '',
                inline: $(this).attr('data-inline') === 'true',
                letterCase: $(this).attr('data-letterCase') || 'lowercase',
                opacity: $(this).attr('data-opacity'),
                position: $(this).attr('data-position') || 'bottom left',
                swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
                change: function(value, opacity) {
                    if (!value) return;
                    if (opacity) value += ', ' + opacity;
                    if (typeof console === 'object') {
                        //console.log(value);
                    }
                },
                theme: 'bootstrap'
            });

        });
    if ($(".datepicker").length > 0) {
        $(".datepicker").datepicker({
            changeYear: true,
            changeMonth: true,
        });
    }
    if ($(".time_input").length > 0) {
        $(".time_input").each(function() {
            rel = $(this).attr('name');
            time = $('.' + rel + '_hour').val() + ':' + $('.' + rel + '_minute').val();
            if ($('.' + rel + '_apm').length > 0) {
                time = time + ' ' + $('.' + rel + '_apm').val();
            }
            $(this).val(time);
        })
    }
    $('.time_select').change(function() {
        rel = $(this).attr('rel');
        time = $('.' + rel + '_hour').val() + ':' + $('.' + rel + '_minute').val();
        if ($('.' + rel + '_apm').length > 0) {
            time = time + ' ' + $('.' + rel + '_apm').val();
        }
        $('input[name="' + rel + '"]').val(time);
    });
    gformbuilderpro_overlay = '<div id="gformbuilderpro_overlay"><div class="container"><div class="content"><div class="circle"></div></div></div></div></div>';
    $('.grating').change(function() {
        rateval = $(this).val();
        ratename = $(this).attr('name');
        for (i = 1; i <= 5; i++) {
            if (i > rateval) {
                $('.' + ratename + 'star' + i).removeClass('active');
            } else {
                $('.' + ratename + 'star' + i).addClass('active');
            }
        }
    });
    $('.slider-range').each(function() {
        $(this).slider({
            range: "min",
            value: $('#' + $(this).data('id')).val(),
            min: $('#' + $(this).data('id')).data('min'),
            max: $('#' + $(this).data('id')).data('max'),
            step: $('#' + $(this).data('id')).data('range'),
            slide: function(event, ui) {
                $('#' + $(this).data('id')).val(ui.value);
                $('#' + $(this).data('id') + '-value').html(ui.value);
            }
        });
    });
    $('.slider-range-multi').each(function() {
        valmin = $('#' + $(this).data('id')).data('valmin');
        valmax = $('#' + $(this).data('id')).data('valmax');
        $(this).slider({
            range: true,
            values: [valmin, valmax],
            min: $('#' + $(this).data('id')).data('min'),
            max: $('#' + $(this).data('id')).data('max'),
            step: $('#' + $(this).data('id')).data('range'),
            slide: function(event, ui) {
                $('#' + $(this).data('id')).val(ui.values[0] + '->' + ui.values[1]);
                $('#' + $(this).data('id') + '-value').html(ui.values[0] + '->' + ui.values[1]);
            }
        });
    });
    $('.spinner_plus').click(function() {
        spinner_value = parseInt($('#' + $(this).data('id')).val());
        maxval = parseInt($('#' + $(this).data('id')).data('max'));
        range = parseInt($('#' + $(this).data('id')).data('range'));
        if (maxval > (spinner_value + range)) {
            $('#' + $(this).data('id')).val(parseInt(spinner_value) + range);
        } else {
            $('#' + $(this).data('id')).val(parseInt(maxval));
        }
        if ($(this).closest('.gformbuilderpro_form').find('input[name="Conditions"]').length > 0) {
            var gFormConditions = $(this).closest('.gformbuilderpro_form').find('input[name="Conditions"]').val();
            ConnditionDisplay(gFormConditions);
        }
    });
    $('.spinner_sub').click(function() {
        spinner_value = parseInt($('#' + $(this).data('id')).val());
        minval = parseInt($('#' + $(this).data('id')).data('min'));
        range = parseInt($('#' + $(this).data('id')).data('range'));
        if (minval < (spinner_value - range)) {
            $('#' + $(this).data('id')).val(parseInt(spinner_value) - range);
        } else {
            $('#' + $(this).data('id')).val(parseInt(minval));
        }
        if ($(this).closest('.gformbuilderpro_form').find('input[name="Conditions"]').length > 0) {
            var gFormConditions = $(this).closest('.gformbuilderpro_form').find('input[name="Conditions"]').val();
            ConnditionDisplay(gFormConditions);
        }
    });
    /**/
    $('.wholesale_spinner_plus').click(function() {
        var number_qty = 0;
        spinner_value = parseInt($('#' + $(this).data('id')).val());
        maxval = parseInt($('#' + $(this).data('id')).data('max'));
        range = parseInt($('#' + $(this).data('id')).data('range'));
        if (maxval > (spinner_value + range)) {
            $('#' + $(this).data('id')).val(parseInt(spinner_value) + range);
        } else {
            $('#' + $(this).data('id')).val(parseInt(maxval));
        }
        $(this).closest('.gform_card').find('.gform-checkbox').prop('checked', true);
        $(this).closest('tr').find('.gform-combination-checkbox').prop('checked', true);
        whosaleProductActive($(this), number_qty);
        getPriceWhosaleProduct($(this));
    });
    $('.wholesale_spinner_sub').click(function() {
        var number_qty = 0;
        spinner_value = parseInt($('#' + $(this).data('id')).val());
        minval = parseInt($('#' + $(this).data('id')).data('min'));
        range = parseInt($('#' + $(this).data('id')).data('range'));
        if (minval < (spinner_value - range)) {
            $('#' + $(this).data('id')).val(parseInt(spinner_value) - range);
        } else {
            $('#' + $(this).data('id')).val(parseInt(minval));
        }
        $(this).closest('.gform_card').find('.gform-checkbox').prop('checked', true);
        $(this).closest('tr').find('.gform-combination-checkbox').prop('checked', true);
        whosaleProductActive($(this), number_qty);
        getPriceWhosaleProduct($(this));
    });
    $('.variant-quantity').change(function() { 
        var number_qty = 0;
            $(this).closest('.gform_card').find('.gform-checkbox').prop('checked', true);
            $(this).closest('.gform_card').find('.gform-combination-checkbox').prop('checked', true);
                if ($(this).closest('.gform_card').find('.gform-combination-checkbox').length > 0) {
                $(this).closest('.gform_card').find('.gform-combination-checkbox').each(function(){
                    if ($(this).is(':checked')) {
                        var qty = $(this).closest('tr').find('.variant-quantity').val();
                        number_qty =  parseInt(number_qty) + parseInt(qty);
                    }
                });
            }
            if ($(this).closest('.gform_card').find('.gform-discounts .gformdiscount-desc').length > 0) {
                $(this).closest('.gform_card').find('.gform-discounts .gformdiscount-desc').each(function() {
                    $(this).closest('.gform_card').find('.gform-discounts .gformdiscount-desc').removeClass('active');
                    if (($(this).data('min') < number_qty && $(this).data('max') > number_qty) ||  ($(this).data('min') == number_qty)) {
                        $(this).addClass('active');
                        return false;
                    }
                });
            }
        getPriceWhosaleProduct($(this));

    });
    $('.gform_card_header_left_default .gform-checkbox').change(function() {
        var number_qty = 0;
        if ($(this).is(':checked')) {
            $(this).closest('.gform_card').find('.gform-combination-checkbox').prop('checked', true);
            if ($(this).closest('.gform_card').find('.gform-combination-checkbox').length > 0) {
                $(this).closest('.gform_card').find('.gform-combination-checkbox').each(function(){
                    if ($(this).is(':checked')) {
                        var qty = $(this).closest('tr').find('.variant-quantity').val();
                        number_qty =  parseInt(number_qty) + parseInt(qty);
                    }
                });
            }
            if ($(this).closest('.gform_card').find('.gform-discounts .gformdiscount-desc').length > 0) {
                $(this).closest('.gform_card').find('.gform-discounts .gformdiscount-desc').each(function() {
                    $(this).closest('.gform_card').find('.gform-discounts .gformdiscount-desc').removeClass('active');
                    if (($(this).data('min') < number_qty && $(this).data('max') > number_qty) ||  ($(this).data('min') == number_qty)) {
                        $(this).addClass('active');
                        return false;
                    }
                });
            }
        } else {
            $(this).closest('.gform_card').find('.gform-combination-checkbox').prop('checked', false);
            $(this).closest('.gform_card').find('.gform-discounts .gformdiscount-desc').removeClass('active');
        }
        
        getPriceWhosaleProduct($(this));
    });
    $('.gform-combination-checkbox').change(function() {
        var number_qty = 0;
        if ($(this).is(':checked')) {
            $(this).closest('tr').find('.gform-combination-checkbox').prop('checked', true);
            $(this).closest('.gform_card').find('.gform-checkbox').prop('checked', true);
            whosaleProductActive($(this), number_qty);
        } else {
            var check_all_combin = false;
            if ($(this).closest('.gform_allcombin').find('.gform-combination-checkbox').length > 0) {
                $(this).closest('.gform_allcombin').find('.gform-combination-checkbox').each(function(){
                    if ($(this).is(':checked')) {
                        check_all_combin = true;
                        return false;
                    }
                });
            }
            if (check_all_combin == false) {
                $(this).closest('.gform_card').find('.gform-checkbox').prop('checked', false);
            }
            whosaleProductActive($(this), number_qty);
        }
        getPriceWhosaleProduct($(this));
        
    });
    /**/
    $('.onoffswitch-checkbox').change(function() {
        if ($(this).is(':checked')) {
            $(this).parents('.onoffswitch').find('.onoffswitch-label').addClass('onoffswitch-active');
        } else {
            $(this).parents('.onoffswitch').find('.onoffswitch-label').removeClass('onoffswitch-active');
        }
        if ($(this).closest('.gformbuilderpro_form').find('input[name="Conditions"]').length > 0) {
            var gFormConditions = $(this).closest('.gformbuilderpro_form').find('input[name="Conditions"]').val();
            ConnditionDisplay(gFormConditions);
        }
    });
    $('.onoffswitch-checkbox').each(function() {
        if ($(this).is(':checked')) {
            $(this).parents('.onoffswitch').find('.onoffswitch-label').addClass('onoffswitch-active');
        } else {
            $(this).parents('.onoffswitch').find('.onoffswitch-label').removeClass('onoffswitch-active');
        }
        if ($(this).closest('.gformbuilderpro_form').find('input[name="Conditions"]').length > 0) {
            var gFormConditions = $(this).closest('.gformbuilderpro_form').find('input[name="Conditions"]').val();
            ConnditionDisplay(gFormConditions);
        }
    });

    function getDoc(frame) {
        var doc = null;
        try {
            if (frame.contentWindow) {
                doc = frame.contentWindow.document;
            }
        } catch (err) {}
        if (doc) {
            return doc;
        }
        try {
            doc = frame.contentDocument ? frame.contentDocument : frame.document;
        } catch (err) {
            doc = frame.document;
        }
        return doc;
    }
    $(".gformbuilderpro_form form").submit(function(e) {
        if (!$(this).hasClass('form_using_ajax')) {
            var onoffswitchextra = '';
            $('.onoffswitch-checkbox').each(function() {
                if ($(this).is(':checked')) {} else {
                    onoffswitchextra += '&' + $(this).attr('name') + '=' + $(this).data('value');
                }
            });
            if ($('#product_page_product_id').length > 0 && $('.hidden_productatt').length > 0) {
                if ($('#idCombination').length > 0) {
                    $('.hidden_productatt').val($('#product_page_product_id').val() + '-' + $('#idCombination').val());
                } else {
                    if ($('#add-to-cart-or-refresh').length > 0) {
                        var productUrl = window.location.href;
                        var formSerialized = $('#add-to-cart-or-refresh').serialize();
                        $.ajax({
                            url: productUrl,
                            type: 'POST',
                            async: false,
                            data: (formSerialized != '' ? formSerialized + '&ajax=1&action=refresh&quantity_wanted=1' : 'ajax=1&action=refresh&quantity_wanted=1') + onoffswitchextra,
                            dataType: 'json',
                            success: function(data, textStatus, jqXHR) {
                                var idCombination = data.id_product_attribute;
                                $('.hidden_productatt').val($('#product_page_product_id').val() + '-' + idCombination);
                            }
                        });
                    }
                }
            }
        }
    });
    $(document).on('click','.gformbuilderpro_form form #submitForm',function (e) {
        var fromSubmit = $(this).closest('form');
        var formURL = fromSubmit.attr("action");
        var numbercheck = 0;
        var hidefields  ='';
        var urlajax_cart = baseUri + '?rand=' + new Date().getTime();
        
        fromSubmit.find('input[name="ConditionsHide"]').val('');
        if (fromSubmit.find('.itemfield').hasClass('gformnone')) {
            fromSubmit.find('.itemfield.gformnone').each(function(){
                var ids = $(this).attr("id").match(/gformbuilderpro_(\d*)/)
                hidefields += ids[1] + ',';
            });
            if (fromSubmit.find('input[name="ConditionsHide"]').length > 0) {
                fromSubmit.find('input[name="ConditionsHide"]').val(hidefields);
            }
        }
        if (fromSubmit.find('.wholesale_box').length > 0) {
            var id_fields= fromSubmit.find('.wholesale_box').closest('.itemfield').attr("id").match(/gformbuilderpro_(\d*)/);
            if (fromSubmit.find('.wholesale_box').find('.gform_card').find('.gform_allcombin').length > 0) {
                fromSubmit.find('.wholesale_box').find('.gform_card').find('.gform_allcombin').find('.gform-combination-checkbox').each(function() {
                    if ($(this).is(':checked')) {
                        
                        var qty = $(this).closest('tr').find('.variant-quantity').val();
                        var id_product = $(this).closest('.gform_card').find('.gform-checkbox').val();
                        var idCombination  = $(this).val();
                        var static_token   = fromSubmit.find('.wholesale_box').data('token');
                        var discounts_value= 0;
                        var discounts_type= 0;
                        var discounts_tax= 0;

                        if ($(this).closest('.gform_card').find('.gformdiscount-desc.active').length > 0) {
                            discounts_value =  $(this).closest('.gform_card').find('.gformdiscount-desc.active').data('value');
                            discounts_type  =  $(this).closest('.gform_card').find('.gformdiscount-desc.active').data('type');
                            discounts_tax   =  $(this).closest('.gform_card').find('.gformdiscount-desc.active').data('tax');
                        }

                        if (!numbercheck) {
                            if (window.FormData !== undefined) {
                                var formData = new FormData();
                                formData.append('deleteCartModule', '1');
                                $.ajax({
                                    url: formURL ,
                                    type: 'POST',
                                    data: formData,
                                    mimeType: "multipart/form-data",
                                    contentType: false,
                                    cache: false,
                                    processData: false,
                                    dataType: 'json',
                                    success: function(data, textStatus, jqXHR) {
                                        if(data) {
                                            if (fromSubmit.find('.wholesale_box').hasClass('ps17')) {
                                                urlajax_cart = baseUri + 'cart';
                                                $.ajax({
                                                    type: 'POST',
                                                    headers: { "cache-control": "no-cache" },
                                                    url: urlajax_cart,
                                                    async: true,
                                                    cache: false,
                                                    dataType: "json",
                                                    data: 'action=update&update=1&ajax=true&qty=' + qty + '&id_product=' + id_product + '&ipa=' + idCombination + '&token=' + static_token +'&discounts_value='+discounts_value+'&discounts_type='+discounts_type+'&discounts_tax='+discounts_tax ,
                                                    success: function(jsonData) {
                                                    },
                                                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                                                        alert(XMLHttpRequest.responseText);
                                                    },
                                                });
                                            } else {
                                                $.ajax({
                                                    type: 'POST',
                                                    headers: { "cache-control": "no-cache" },
                                                    url: urlajax_cart,
                                                    async: true,
                                                    cache: false,
                                                    dataType: "json",
                                                    data: 'controller=cart&add=1&ajax=true&qty=' + qty + '&id_product=' + id_product + '&ipa=' + idCombination + '&token=' + static_token +'&discounts_value='+discounts_value+'&discounts_type='+discounts_type+'&discounts_tax='+discounts_tax,
                                                    success: function(jsonData) {
                                                    },
                                                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                                                        alert(XMLHttpRequest.responseText);
                                                    },
                                                });
                                            }
                                        }
                                    }
                                });
                            }
                        } else {
                            if (fromSubmit.find('.wholesale_box').hasClass('ps17')) {
                                urlajax_cart = baseUri + 'cart';
                                $.ajax({
                                    type: 'POST',
                                    headers: { "cache-control": "no-cache" },
                                    url: urlajax_cart,
                                    async: true,
                                    cache: false,
                                    dataType: "json",
                                    data: 'action=update&update=1&ajax=true&qty=' + qty + '&id_product=' + id_product + '&ipa=' + idCombination + '&token=' + static_token +'&discounts_value='+discounts_value+'&discounts_type='+discounts_type+'&discounts_tax='+discounts_tax ,
                                    success: function(jsonData) {
                                    },
                                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                                        alert(XMLHttpRequest.responseText);
                                    },
                                });
                            } else {
                                $.ajax({
                                    type: 'POST',
                                    headers: { "cache-control": "no-cache" },
                                    url: urlajax_cart,
                                    async: true,
                                    cache: false,
                                    dataType: "json",
                                    data: 'controller=cart&add=1&ajax=true&qty=' + qty + '&id_product=' + id_product + '&ipa=' + idCombination + '&token=' + static_token +'&discounts_value='+discounts_value+'&discounts_type='+discounts_type+'&discounts_tax='+discounts_tax,
                                    success: function(jsonData) {
                                    },
                                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                                        alert(XMLHttpRequest.responseText);
                                    },
                                });
                            }
                        }
                        fromSubmit.find('.wholesale_box').find('.wholesale_box_idcart').val('1');
                        numbercheck++;
                    }
                });
            }
        }
        fromSubmit.submit();
    });
    $(".gformbuilderpro_form form.form_using_ajax").submit(function(e) {
        var onoffswitchextra = '';
        $('.onoffswitch-checkbox').each(function() {
            if ($(this).is(':checked')) {} else {
                onoffswitchextra += '&' + $(this).attr('name') + '=' + $(this).data('value');
            }
        });

        if ($('#product_page_product_id').length > 0 && $('.hidden_productatt').length > 0) {
            if ($('#idCombination').length > 0)
                $('.hidden_productatt').val($('#product_page_product_id').val() + '-' + $('#idCombination').val());
            else {
                if ($('#add-to-cart-or-refresh').length > 0) {
                    var productUrl = window.location.href;
                    var formSerialized = $('#add-to-cart-or-refresh').serialize();
                    $.ajax({
                        url: productUrl,
                        type: 'POST',
                        async: false,
                        data: (formSerialized != '' ? formSerialized + '&ajax=1&action=refresh&quantity_wanted=1' : 'ajax=1&action=refresh&quantity_wanted=1'),
                        dataType: 'json',
                        success: function(data, textStatus, jqXHR) {
                            var idCombination = data.id_product_attribute;
                            $('.hidden_productatt').val($('#product_page_product_id').val() + '-' + idCombination);
                        }
                    });
                }
            }
        }
        var formsubmit = $(this);
        if (typeof tinymce != "undefined") {
            tinymce.triggerSave();
        }
        /* fix missing file upload in safari */
        if (formsubmit.find('input[type="file"]').length > 0)
            formsubmit.find('input[type="file"]').each(function() {
                var vidFileLength = $(this)[0].files.length;
                if (vidFileLength === 0) {
                    $(this).attr('data-type', 'file').attr('data-bkname', $(this).attr('name')).removeAttr('name');
                }
            })
        var formURL = formsubmit.attr("action");
        $(gformbuilderpro_overlay).appendTo(formsubmit);
        if (window.FormData !== undefined) {
            var formData = new FormData(this);
            $.ajax({
                url: formURL,
                type: 'POST',
                data: formData,
                mimeType: "multipart/form-data",
                contentType: false,
                cache: false,
                processData: false,
                success: function(data, textStatus, jqXHR) {
                    if (formsubmit.find('input[data-type="file"]').length > 0)
                        formsubmit.find('input[data-type="file"]').each(function() {
                            if (typeof $(this).attr('data-bkname') !== typeof undefined && $(this).attr('data-bkname') !== false)
                                $(this).removeAttr('data-type').attr('name', $(this).attr('data-bkname')).removeAttr('data-bkname');
                        })
                    formsubmit.find('#gformbuilderpro_overlay').remove();
                    var result = $.parseJSON(data);
                    if (formsubmit.find('.formajaxresult').length > 0) {
                        if (result.errors == '0') {
                            formsubmit[0].reset();
                            formsubmit.find('.formajaxresult').html('<div class="success_box">' + result.thankyou + '</div>');
                        } else
                            formsubmit.find('.formajaxresult').html(result.thankyou);
                    } else {
                        resulthtml = '';
                        if (result.errors == '0') {
                            formsubmit[0].reset();
                            resulthtml = '<div class="formajaxresult"><div class="success_box">' + result.thankyou + '</div><div>';
                        } else
                            resulthtml = '<div class="formajaxresult">' + result.thankyou + '<div>';
                        $(resulthtml).insertBefore(formsubmit.find('.gformbuilderpro_content'));
                    }
                    $('html,body').animate({
                            scrollTop: formsubmit.find('.formajaxresult').offset().top
                        },
                        'slow');
                    if (result.autoredirect == true) {
                        setTimeout(function() {
                            window.location.href = result.redirect_link;
                        }, result.timedelay);
                    }
                    if ($('#recaptchaResponse').length > 0) {
                        var sitekey = $('#recaptchaResponse').attr('data-sitekey');
                        loadrecaptchav3(sitekey);
                    } else
                    if (typeof grecaptcha != "undefined" && $('.gformbuilderpro_form .g-recaptcha').length > 0) {
                        grecaptcha.reset();
                    }

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    if (formsubmit.find('input[data-type="file"]').length > 0)
                        formsubmit.find('input[data-type="file"]').each(function() {
                            if (typeof $(this).attr('data-bkname') !== typeof undefined && $(this).attr('data-bkname') !== false)
                                $(this).removeAttr('data-type').attr('name', $(this).attr('data-bkname')).removeAttr('data-bkname');
                        })
                    if ($('#recaptchaResponse').length > 0) {
                        var sitekey = $('#recaptchaResponse').attr('data-sitekey');
                        loadrecaptchav3(sitekey);
                    } else
                    if (typeof grecaptcha != "undefined" && $('.gformbuilderpro_form .g-recaptcha').length > 0) {
                        grecaptcha.reset();
                    }
                    var err = eval("(" + jqXHR.responseText + ")");
                    alert(err.Message);

                }
            });
            e.preventDefault();
            //e.unbind();
        } else {
            var iframeId = 'unique' + (new Date().getTime());
            var iframe = $('<iframe src="javascript:false;" name="' + iframeId + '" />');
            iframe.hide();
            formsubmit.attr('target', iframeId);
            iframe.appendTo('body');
            iframe.load(function(e) {
                $('#gformbuilderpro_overlay').remove();
                var doc = getDoc(iframe[0]);
                var docRoot = doc.body ? doc.body : doc.documentElement;
                var data = docRoot.innerHTML;
                var result = $.parseJSON(data);
                if (formsubmit.find('.formajaxresult').length > 0) {
                    if (result.errors == '0') {
                        formsubmit[0].reset();
                        formsubmit.find('.formajaxresult').html('<div class="success_box">' + result.thankyou + '</div>');
                    } else
                        formsubmit.find('.formajaxresult').html(result.thankyou);
                } else {
                    resulthtml = '';
                    if (result.errors == '0') {
                        formsubmit[0].reset();
                        resulthtml = '<div class="formajaxresult"><div class="success_box">' + result.thankyou + '</div><div>';
                    } else
                        resulthtml = '<div class="formajaxresult">' + result.thankyou + '<div>';
                    $(resulthtml).insertBefore(formsubmit.find('.gformbuilderpro_content'));
                }

                $('html,body').animate({
                        scrollTop: formsubmit.find('.formajaxresult').offset().top
                    },
                    'slow');

                if ($('#recaptchaResponse').length > 0) {
                    var sitekey = $('#recaptchaResponse').attr('data-sitekey');
                    loadrecaptchav3(sitekey);
                } else
                if (typeof grecaptcha != "undefined" && $('.gformbuilderpro_form .g-recaptcha').length > 0) {
                    grecaptcha.reset();
                }

            });
        }
    });
    $('.color_box .mColorPickerinput').on('click', function() {
        id = $(this).attr('id');
        if ($('#icp_' + id).length > 0) {
            $('#icp_' + id).click();
        }
    });


    /* from version 1.2.0 */
    $('.gformbuilderpro_openform').fancybox({
        'beforeShow': function() {
            if ($('.formajaxresult').length > 0)
                $('.formajaxresult').html('');
        },
        afterLoad: function() {
            if (typeof tinymce != "undefined") {
                tinymce.remove();
                setTimeout(function() { tinymce.init({ selector: '.htmlinput' }); }, 500);
            }
        }
    });
    /*form version 1.3.6*/
    
    $('.wholesale_box .icon_click_opend').on('click', function() {
        $(this).toggleClass('icon-minus');
        $(this).closest('.gform_card').find('.gform_card_body').toggleClass('gformnone');
    });
})