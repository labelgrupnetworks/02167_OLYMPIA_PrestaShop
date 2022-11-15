/**
* This is main js file. Don't edit the file if you want to update module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
if (typeof PS_ALLOW_ACCENTED_CHARS_URL == 'undefined') PS_ALLOW_ACCENTED_CHARS_URL = 0;
var selectedProduct;
function fixOldTemplate(){
    if($('.formbuilder_group').length > 0){
        if($('#formbuilder .itemfield').length > 0)
            $('#formbuilder .itemfield').removeAttr('class').addClass('itemfield');
        $('.formbuilder_group').each(function () {
            if($(this).find('.formbuilder_column').length == 0 && $(this).find('.itemfield_wp').length > 0){
                var wrapper = $('<div></div>').addClass('formbuilder_column col-md-12 col-sm-12 col-xs-12');
                $(this).find('.itemfield_wp').each(function () {
                    $(this).removeClass('row');
                    wrapper.append($(this));
                });

                $(this).html('').removeAttr('class').addClass('formbuilder_group').append(wrapper);
            }
        });
        if($('#formbuilder .itemfield').length > 0){
            var missing_icon = [];
            $('#formbuilder .itemfield').each(function () {
                if($(this).find('.field_icon').length == 0 && $(this).find('.field_desc_wp').length == 0){
                    missing_icon.push($(this).attr('id').replace(/[^0-9]/g,''));
                }
            });
            var ids = missing_icon.join('_');
            if(ids !=''){
                /*get type field */
                $.ajax({
                    url: currentIndex +'&token='+token+'&getTypeField=1',
                    type: 'POST',
                    dataType: 'json',
                    data: 'ids='+ids,
                    success: function(datas) {
                        if(datas){
                            $.each(datas,function (key,val) {
                                if($('div[data-type="'+val.type+'"]').length > 0 && $('div[data-type="'+val.type+'"] .field_icon').length > 0){
                                    var old_field = $('#gformbuilderpro_'+val.id_gformbuilderprofields+' .field_content').html();
                                    var field_icon = $('div[data-type="'+val.type+'"] .field_icon').clone();
                                    $('#gformbuilderpro_'+val.id_gformbuilderprofields+' .field_content').html('');
                                    $('#gformbuilderpro_'+val.id_gformbuilderprofields+' .field_content').append(field_icon);
                                    $('#gformbuilderpro_'+val.id_gformbuilderprofields+' .field_content').append('<span class="field_desc_wp">'+old_field+'</span>');
                                }
                            });
                        }
                    }
                });
            }
        }
    }
}
function refreshSortable(){
    if($('.itemfield_wp').length > 0) {
        $('.itemfield_wp').each(function () {
            if (!$(this).hasClass('ui-sortable')) {
                $(this).sortable({
                    connectWith: '.itemfield_wp',
                    handle: '.formbuilder_move',
                    opacity: 0.5,
                    cursor: 'move',
                    update: function (event, ui) {
                        clearBeforeSave();
                    },
                    beforeStop: function (event, ui) {
                        newItem = ui.item;
                    },
                    receive: function (event, ui) {
                        type = newItem.data('type');
                        newitem = ui.item.data('newitem');
                        if (newitem) {
                            width = 12;
                            widthsm = 12;
                            widthxs = 12;
                            addControlBt(newItem, width, widthsm, widthxs);
                            newItem.removeAttr('data-newitem');
                            $(gformbuilderpro_overlay).appendTo('body');
                            $.ajax({
                                url: $('#ajaxurl').val(),
                                type: 'POST',
                                data: 'typefield=' + type,
                            })
                                .done(function (data) {
                                    $('#gformbuilderpro_overlay').remove();
                                    if (data != '') {
                                        $("#popup_field_config #content").html(data);
                                        $("#popup_field_config_link").click();
                                        newItem.attr('id', 'newfield');
                                    } else {
                                        newItem.remove();
                                    }
                                });
                        }
                    }
                });
            }
        })
        $("#formbuilder").sortable("refresh");
        if($("#formbuilder .itemfield_wp").length > 0)
            $("#formbuilder .itemfield_wp").each(function(){
                $(this).sortable("refresh");
            });
        
    }
}
function checkIsBlankPage(){
    if($('#formbuilder .formbuilder_group').length > 0){
        $('.formbuilder_new_design').removeClass('is_blank_page');
    }else $('.formbuilder_new_design').addClass('is_blank_page');
}
function addNewColumn(configs,formbuilder_group){
    group_width_default = $('#group_width_default').val();
    if(group_width_default > 12 || group_width_default < 1 || group_width_default == '') group_width_default = 12;
    var control = $('#control_group').clone();
    control.find('.formbuilder_group_width option')
        .filter(function() {
            return this.value == group_width_default;
        })
        .attr('selected', 'selected')
        .end();
    var add_new_element = $('.control_column').clone();
    var control_column_top_wp = $('.control_column_top_wp').clone();
    if(configs){
        $.each(configs,function (key,config_val) {
            var newgroup = '<div class="formbuilder_column '+config_val+'">'+control_column_top_wp.html()+'<div class="itemfield_wp"></div>'+add_new_element.html()+'</div>';
            formbuilder_group.append(newgroup);
        });
        refreshSortable();
        checkIsBlankPage();
    }
}
function addNewGroup(configs){
    group_width_default = $('#group_width_default').val();
    if(group_width_default > 12 || group_width_default < 1 || group_width_default == '') group_width_default = 12;
    var control = $('#control_group').clone();
    control.find('.formbuilder_group_width option')
        .filter(function() {
            return this.value == group_width_default;
        })
        .attr('selected', 'selected')
        .end();
    var add_new_element = $('.control_column').clone();
    var control_column_top_wp = $('.control_column_top_wp').clone();


    newgroup = '<div class="formbuilder_group formbuilder_new_group">';
    if(configs){
        $.each(configs,function (key,config_val) {
            newgroup += '<div class="formbuilder_column '+config_val+'">'+control_column_top_wp.html()+'<div class="itemfield_wp"></div>'+add_new_element.html()+'</div>';
        });
    }else
        newgroup += '<div class="formbuilder_column col-md-12 col-sm-12 col-xs-12">'+control_column_top_wp.html()+'<div class="itemfield_wp"></div>'+add_new_element.html()+'</div>';

    newgroup += control.html()+'</div>';
    $('#formbuilder').append(newgroup);
    refreshSortable();
    checkIsBlankPage();
}

function copyToClipboard(input) {
  var $temp = $("<input>");
  $("body").append($temp);
  var copyval = '';
  if(input.closest('.copy_group').find('.copy_data').hasClass('copy_link')){
    copyval = input.closest('.copy_group').find('.copy_data').text();
  }else if(input.closest('.copy_group').find('.copy_data').hasClass('shortcode')){
    copyval = input.closest('.copy_group').find('.copy_data').attr('rel');
  }
  else{
    copyval = input.closest('.copy_group').find('.copy_data').val();
  }
  copyval = $.trim(copyval);
  $temp.val(copyval).select();
  document.execCommand("copy");
  $temp.remove();
  showSuccessMessage(copyToClipboard_success);
}
function changeSildeValue(popup_field_content_box){
    minval = parseInt(popup_field_content_box.find('#minval').val(), 10);
    if(isNaN(minval)){
        minval = 0;
    }
    popup_field_content_box.find('#minval').val(minval);
    maxval = parseInt(popup_field_content_box.find('#maxval').val(), 10);
    if(isNaN(maxval) || maxval <= minval){
        maxval = minval+1;
    }
    popup_field_content_box.find('#maxval').val(maxval);
    rangeval = parseInt(popup_field_content_box.find('#rangeval').val(), 10);
    if(isNaN(rangeval) || rangeval < 1){
        rangeval = 1;
    }
    popup_field_content_box.find('#rangeval').val(rangeval);
    if(popup_field_content_box.find('#multi_on').is(':checked')){
        defaultval = popup_field_content_box.find('#defaultval').val();
        defaultvals = defaultval.split(';');
        defaultmin = minval;
        defaultmax = maxval;
        if (defaultvals.length > 0){
            defaultmin = parseInt(defaultvals[0], 10);
            if(isNaN(defaultmin) || defaultmin < minval || defaultmin > maxval){
                defaultmin = Math.floor((minval + maxval)/2);
            }
        }
        if (defaultvals.length > 1){
            defaultmax = parseInt(defaultvals[1], 10);
            if(isNaN(defaultmax) || defaultmax < minval || defaultmax > maxval){
                defaultmax = Math.ceil((minval + maxval)/2);
            }
        }
        popup_field_content_box.find('#defaultval').val(defaultmin+';'+defaultmax);
        popup_field_content_box.find('#slidervalue').val(minval+';'+maxval+';'+rangeval+';'+defaultmin+';'+defaultmax);
    }else{
        defaultval = parseInt(popup_field_content_box.find('#defaultval').val(), 10);
        if(isNaN(defaultval) || defaultval < minval || defaultval > maxval){
            defaultval = Math.floor((minval + maxval)/2);
        }
        popup_field_content_box.find('#defaultval').val(defaultval);
        popup_field_content_box.find('#slidervalue').val(minval+';'+maxval+';'+rangeval+';'+defaultval);
    }
}
var unicode_hack3 = (function() {
    /* Regexps to match characters in the BMP according to their Unicode category.
       Extracted from Unicode specification, version 5.0.0, source:
       http://unicode.org/versions/Unicode5.0.0/
    */
	var unicodeCategories = {
		Pi:'[\u00ab\u2018\u201b\u201c\u201f\u2039\u2e02\u2e04\u2e09\u2e0c\u2e1c]',
		Sk:'[\u005e\u0060\u00a8\u00af\u00b4\u00b8\u02c2-\u02c5\u02d2-\u02df\u02e5-\u02ed\u02ef-\u02ff\u0374\u0375\u0384\u0385\u1fbd\u1fbf-\u1fc1\u1fcd-\u1fcf\u1fdd-\u1fdf\u1fed-\u1fef\u1ffd\u1ffe\u309b\u309c\ua700-\ua716\ua720\ua721\uff3e\uff40\uffe3]',
		Sm:'[\u002b\u003c-\u003e\u007c\u007e\u00ac\u00b1\u00d7\u00f7\u03f6\u2044\u2052\u207a-\u207c\u208a-\u208c\u2140-\u2144\u214b\u2190-\u2194\u219a\u219b\u21a0\u21a3\u21a6\u21ae\u21ce\u21cf\u21d2\u21d4\u21f4-\u22ff\u2308-\u230b\u2320\u2321\u237c\u239b-\u23b3\u23dc-\u23e1\u25b7\u25c1\u25f8-\u25ff\u266f\u27c0-\u27c4\u27c7-\u27ca\u27d0-\u27e5\u27f0-\u27ff\u2900-\u2982\u2999-\u29d7\u29dc-\u29fb\u29fe-\u2aff\ufb29\ufe62\ufe64-\ufe66\uff0b\uff1c-\uff1e\uff5c\uff5e\uffe2\uffe9-\uffec]',
		So:'[\u00a6\u00a7\u00a9\u00ae\u00b0\u00b6\u0482\u060e\u060f\u06e9\u06fd\u06fe\u07f6\u09fa\u0b70\u0bf3-\u0bf8\u0bfa\u0cf1\u0cf2\u0f01-\u0f03\u0f13-\u0f17\u0f1a-\u0f1f\u0f34\u0f36\u0f38\u0fbe-\u0fc5\u0fc7-\u0fcc\u0fcf\u1360\u1390-\u1399\u1940\u19e0-\u19ff\u1b61-\u1b6a\u1b74-\u1b7c\u2100\u2101\u2103-\u2106\u2108\u2109\u2114\u2116-\u2118\u211e-\u2123\u2125\u2127\u2129\u212e\u213a\u213b\u214a\u214c\u214d\u2195-\u2199\u219c-\u219f\u21a1\u21a2\u21a4\u21a5\u21a7-\u21ad\u21af-\u21cd\u21d0\u21d1\u21d3\u21d5-\u21f3\u2300-\u2307\u230c-\u231f\u2322-\u2328\u232b-\u237b\u237d-\u239a\u23b4-\u23db\u23e2-\u23e7\u2400-\u2426\u2440-\u244a\u249c-\u24e9\u2500-\u25b6\u25b8-\u25c0\u25c2-\u25f7\u2600-\u266e\u2670-\u269c\u26a0-\u26b2\u2701-\u2704\u2706-\u2709\u270c-\u2727\u2729-\u274b\u274d\u274f-\u2752\u2756\u2758-\u275e\u2761-\u2767\u2794\u2798-\u27af\u27b1-\u27be\u2800-\u28ff\u2b00-\u2b1a\u2b20-\u2b23\u2ce5-\u2cea\u2e80-\u2e99\u2e9b-\u2ef3\u2f00-\u2fd5\u2ff0-\u2ffb\u3004\u3012\u3013\u3020\u3036\u3037\u303e\u303f\u3190\u3191\u3196-\u319f\u31c0-\u31cf\u3200-\u321e\u322a-\u3243\u3250\u3260-\u327f\u328a-\u32b0\u32c0-\u32fe\u3300-\u33ff\u4dc0-\u4dff\ua490-\ua4c6\ua828-\ua82b\ufdfd\uffe4\uffe8\uffed\uffee\ufffc\ufffd]',
		Po:'[\u0021-\u0023\u0025-\u0027\u002a\u002c\u002e\u002f\u003a\u003b\u003f\u0040\u005c\u00a1\u00b7\u00bf\u037e\u0387\u055a-\u055f\u0589\u05be\u05c0\u05c3\u05c6\u05f3\u05f4\u060c\u060d\u061b\u061e\u061f\u066a-\u066d\u06d4\u0700-\u070d\u07f7-\u07f9\u0964\u0965\u0970\u0df4\u0e4f\u0e5a\u0e5b\u0f04-\u0f12\u0f85\u0fd0\u0fd1\u104a-\u104f\u10fb\u1361-\u1368\u166d\u166e\u16eb-\u16ed\u1735\u1736\u17d4-\u17d6\u17d8-\u17da\u1800-\u1805\u1807-\u180a\u1944\u1945\u19de\u19df\u1a1e\u1a1f\u1b5a-\u1b60\u2016\u2017\u2020-\u2027\u2030-\u2038\u203b-\u203e\u2041-\u2043\u2047-\u2051\u2053\u2055-\u205e\u2cf9-\u2cfc\u2cfe\u2cff\u2e00\u2e01\u2e06-\u2e08\u2e0b\u2e0e-\u2e16\u3001-\u3003\u303d\u30fb\ua874-\ua877\ufe10-\ufe16\ufe19\ufe30\ufe45\ufe46\ufe49-\ufe4c\ufe50-\ufe52\ufe54-\ufe57\ufe5f-\ufe61\ufe68\ufe6a\ufe6b\uff01-\uff03\uff05-\uff07\uff0a\uff0c\uff0e\uff0f\uff1a\uff1b\uff1f\uff20\uff3c\uff61\uff64\uff65]',
		Mn:'[\u0300-\u036f\u0483-\u0486\u0591-\u05bd\u05bf\u05c1\u05c2\u05c4\u05c5\u05c7\u0610-\u0615\u064b-\u065e\u0670\u06d6-\u06dc\u06df-\u06e4\u06e7\u06e8\u06ea-\u06ed\u0711\u0730-\u074a\u07a6-\u07b0\u07eb-\u07f3\u0901\u0902\u093c\u0941-\u0948\u094d\u0951-\u0954\u0962\u0963\u0981\u09bc\u09c1-\u09c4\u09cd\u09e2\u09e3\u0a01\u0a02\u0a3c\u0a41\u0a42\u0a47\u0a48\u0a4b-\u0a4d\u0a70\u0a71\u0a81\u0a82\u0abc\u0ac1-\u0ac5\u0ac7\u0ac8\u0acd\u0ae2\u0ae3\u0b01\u0b3c\u0b3f\u0b41-\u0b43\u0b4d\u0b56\u0b82\u0bc0\u0bcd\u0c3e-\u0c40\u0c46-\u0c48\u0c4a-\u0c4d\u0c55\u0c56\u0cbc\u0cbf\u0cc6\u0ccc\u0ccd\u0ce2\u0ce3\u0d41-\u0d43\u0d4d\u0dca\u0dd2-\u0dd4\u0dd6\u0e31\u0e34-\u0e3a\u0e47-\u0e4e\u0eb1\u0eb4-\u0eb9\u0ebb\u0ebc\u0ec8-\u0ecd\u0f18\u0f19\u0f35\u0f37\u0f39\u0f71-\u0f7e\u0f80-\u0f84\u0f86\u0f87\u0f90-\u0f97\u0f99-\u0fbc\u0fc6\u102d-\u1030\u1032\u1036\u1037\u1039\u1058\u1059\u135f\u1712-\u1714\u1732-\u1734\u1752\u1753\u1772\u1773\u17b7-\u17bd\u17c6\u17c9-\u17d3\u17dd\u180b-\u180d\u18a9\u1920-\u1922\u1927\u1928\u1932\u1939-\u193b\u1a17\u1a18\u1b00-\u1b03\u1b34\u1b36-\u1b3a\u1b3c\u1b42\u1b6b-\u1b73\u1dc0-\u1dca\u1dfe\u1dff\u20d0-\u20dc\u20e1\u20e5-\u20ef\u302a-\u302f\u3099\u309a\ua806\ua80b\ua825\ua826\ufb1e\ufe00-\ufe0f\ufe20-\ufe23]',
		Ps:'[\u0028\u005b\u007b\u0f3a\u0f3c\u169b\u201a\u201e\u2045\u207d\u208d\u2329\u2768\u276a\u276c\u276e\u2770\u2772\u2774\u27c5\u27e6\u27e8\u27ea\u2983\u2985\u2987\u2989\u298b\u298d\u298f\u2991\u2993\u2995\u2997\u29d8\u29da\u29fc\u3008\u300a\u300c\u300e\u3010\u3014\u3016\u3018\u301a\u301d\ufd3e\ufe17\ufe35\ufe37\ufe39\ufe3b\ufe3d\ufe3f\ufe41\ufe43\ufe47\ufe59\ufe5b\ufe5d\uff08\uff3b\uff5b\uff5f\uff62]',
		Cc:'[\u0000-\u001f\u007f-\u009f]',
		Cf:'[\u00ad\u0600-\u0603\u06dd\u070f\u17b4\u17b5\u200b-\u200f\u202a-\u202e\u2060-\u2063\u206a-\u206f\ufeff\ufff9-\ufffb]',
		Ll:'[\u0061-\u007a\u00aa\u00b5\u00ba\u00df-\u00f6\u00f8-\u00ff\u0101\u0103\u0105\u0107\u0109\u010b\u010d\u010f\u0111\u0113\u0115\u0117\u0119\u011b\u011d\u011f\u0121\u0123\u0125\u0127\u0129\u012b\u012d\u012f\u0131\u0133\u0135\u0137\u0138\u013a\u013c\u013e\u0140\u0142\u0144\u0146\u0148\u0149\u014b\u014d\u014f\u0151\u0153\u0155\u0157\u0159\u015b\u015d\u015f\u0161\u0163\u0165\u0167\u0169\u016b\u016d\u016f\u0171\u0173\u0175\u0177\u017a\u017c\u017e-\u0180\u0183\u0185\u0188\u018c\u018d\u0192\u0195\u0199-\u019b\u019e\u01a1\u01a3\u01a5\u01a8\u01aa\u01ab\u01ad\u01b0\u01b4\u01b6\u01b9\u01ba\u01bd-\u01bf\u01c6\u01c9\u01cc\u01ce\u01d0\u01d2\u01d4\u01d6\u01d8\u01da\u01dc\u01dd\u01df\u01e1\u01e3\u01e5\u01e7\u01e9\u01eb\u01ed\u01ef\u01f0\u01f3\u01f5\u01f9\u01fb\u01fd\u01ff\u0201\u0203\u0205\u0207\u0209\u020b\u020d\u020f\u0211\u0213\u0215\u0217\u0219\u021b\u021d\u021f\u0221\u0223\u0225\u0227\u0229\u022b\u022d\u022f\u0231\u0233-\u0239\u023c\u023f\u0240\u0242\u0247\u0249\u024b\u024d\u024f-\u0293\u0295-\u02af\u037b-\u037d\u0390\u03ac-\u03ce\u03d0\u03d1\u03d5-\u03d7\u03d9\u03db\u03dd\u03df\u03e1\u03e3\u03e5\u03e7\u03e9\u03eb\u03ed\u03ef-\u03f3\u03f5\u03f8\u03fb\u03fc\u0430-\u045f\u0461\u0463\u0465\u0467\u0469\u046b\u046d\u046f\u0471\u0473\u0475\u0477\u0479\u047b\u047d\u047f\u0481\u048b\u048d\u048f\u0491\u0493\u0495\u0497\u0499\u049b\u049d\u049f\u04a1\u04a3\u04a5\u04a7\u04a9\u04ab\u04ad\u04af\u04b1\u04b3\u04b5\u04b7\u04b9\u04bb\u04bd\u04bf\u04c2\u04c4\u04c6\u04c8\u04ca\u04cc\u04ce\u04cf\u04d1\u04d3\u04d5\u04d7\u04d9\u04db\u04dd\u04df\u04e1\u04e3\u04e5\u04e7\u04e9\u04eb\u04ed\u04ef\u04f1\u04f3\u04f5\u04f7\u04f9\u04fb\u04fd\u04ff\u0501\u0503\u0505\u0507\u0509\u050b\u050d\u050f\u0511\u0513\u0561-\u0587\u1d00-\u1d2b\u1d62-\u1d77\u1d79-\u1d9a\u1e01\u1e03\u1e05\u1e07\u1e09\u1e0b\u1e0d\u1e0f\u1e11\u1e13\u1e15\u1e17\u1e19\u1e1b\u1e1d\u1e1f\u1e21\u1e23\u1e25\u1e27\u1e29\u1e2b\u1e2d\u1e2f\u1e31\u1e33\u1e35\u1e37\u1e39\u1e3b\u1e3d\u1e3f\u1e41\u1e43\u1e45\u1e47\u1e49\u1e4b\u1e4d\u1e4f\u1e51\u1e53\u1e55\u1e57\u1e59\u1e5b\u1e5d\u1e5f\u1e61\u1e63\u1e65\u1e67\u1e69\u1e6b\u1e6d\u1e6f\u1e71\u1e73\u1e75\u1e77\u1e79\u1e7b\u1e7d\u1e7f\u1e81\u1e83\u1e85\u1e87\u1e89\u1e8b\u1e8d\u1e8f\u1e91\u1e93\u1e95-\u1e9b\u1ea1\u1ea3\u1ea5\u1ea7\u1ea9\u1eab\u1ead\u1eaf\u1eb1\u1eb3\u1eb5\u1eb7\u1eb9\u1ebb\u1ebd\u1ebf\u1ec1\u1ec3\u1ec5\u1ec7\u1ec9\u1ecb\u1ecd\u1ecf\u1ed1\u1ed3\u1ed5\u1ed7\u1ed9\u1edb\u1edd\u1edf\u1ee1\u1ee3\u1ee5\u1ee7\u1ee9\u1eeb\u1eed\u1eef\u1ef1\u1ef3\u1ef5\u1ef7\u1ef9\u1f00-\u1f07\u1f10-\u1f15\u1f20-\u1f27\u1f30-\u1f37\u1f40-\u1f45\u1f50-\u1f57\u1f60-\u1f67\u1f70-\u1f7d\u1f80-\u1f87\u1f90-\u1f97\u1fa0-\u1fa7\u1fb0-\u1fb4\u1fb6\u1fb7\u1fbe\u1fc2-\u1fc4\u1fc6\u1fc7\u1fd0-\u1fd3\u1fd6\u1fd7\u1fe0-\u1fe7\u1ff2-\u1ff4\u1ff6\u1ff7\u2071\u207f\u210a\u210e\u210f\u2113\u212f\u2134\u2139\u213c\u213d\u2146-\u2149\u214e\u2184\u2c30-\u2c5e\u2c61\u2c65\u2c66\u2c68\u2c6a\u2c6c\u2c74\u2c76\u2c77\u2c81\u2c83\u2c85\u2c87\u2c89\u2c8b\u2c8d\u2c8f\u2c91\u2c93\u2c95\u2c97\u2c99\u2c9b\u2c9d\u2c9f\u2ca1\u2ca3\u2ca5\u2ca7\u2ca9\u2cab\u2cad\u2caf\u2cb1\u2cb3\u2cb5\u2cb7\u2cb9\u2cbb\u2cbd\u2cbf\u2cc1\u2cc3\u2cc5\u2cc7\u2cc9\u2ccb\u2ccd\u2ccf\u2cd1\u2cd3\u2cd5\u2cd7\u2cd9\u2cdb\u2cdd\u2cdf\u2ce1\u2ce3\u2ce4\u2d00-\u2d25\ufb00-\ufb06\ufb13-\ufb17\uff41-\uff5a]',
		Lm:'[\u02b0-\u02c1\u02c6-\u02d1\u02e0-\u02e4\u02ee\u037a\u0559\u0640\u06e5\u06e6\u07f4\u07f5\u07fa\u0e46\u0ec6\u10fc\u17d7\u1843\u1d2c-\u1d61\u1d78\u1d9b-\u1dbf\u2090-\u2094\u2d6f\u3005\u3031-\u3035\u303b\u309d\u309e\u30fc-\u30fe\ua015\ua717-\ua71a\uff70\uff9e\uff9f]',
		Lo:'[\u01bb\u01c0-\u01c3\u0294\u05d0-\u05ea\u05f0-\u05f2\u0621-\u063a\u0641-\u064a\u066e\u066f\u0671-\u06d3\u06d5\u06ee\u06ef\u06fa-\u06fc\u06ff\u0710\u0712-\u072f\u074d-\u076d\u0780-\u07a5\u07b1\u07ca-\u07ea\u0904-\u0939\u093d\u0950\u0958-\u0961\u097b-\u097f\u0985-\u098c\u098f\u0990\u0993-\u09a8\u09aa-\u09b0\u09b2\u09b6-\u09b9\u09bd\u09ce\u09dc\u09dd\u09df-\u09e1\u09f0\u09f1\u0a05-\u0a0a\u0a0f\u0a10\u0a13-\u0a28\u0a2a-\u0a30\u0a32\u0a33\u0a35\u0a36\u0a38\u0a39\u0a59-\u0a5c\u0a5e\u0a72-\u0a74\u0a85-\u0a8d\u0a8f-\u0a91\u0a93-\u0aa8\u0aaa-\u0ab0\u0ab2\u0ab3\u0ab5-\u0ab9\u0abd\u0ad0\u0ae0\u0ae1\u0b05-\u0b0c\u0b0f\u0b10\u0b13-\u0b28\u0b2a-\u0b30\u0b32\u0b33\u0b35-\u0b39\u0b3d\u0b5c\u0b5d\u0b5f-\u0b61\u0b71\u0b83\u0b85-\u0b8a\u0b8e-\u0b90\u0b92-\u0b95\u0b99\u0b9a\u0b9c\u0b9e\u0b9f\u0ba3\u0ba4\u0ba8-\u0baa\u0bae-\u0bb9\u0c05-\u0c0c\u0c0e-\u0c10\u0c12-\u0c28\u0c2a-\u0c33\u0c35-\u0c39\u0c60\u0c61\u0c85-\u0c8c\u0c8e-\u0c90\u0c92-\u0ca8\u0caa-\u0cb3\u0cb5-\u0cb9\u0cbd\u0cde\u0ce0\u0ce1\u0d05-\u0d0c\u0d0e-\u0d10\u0d12-\u0d28\u0d2a-\u0d39\u0d60\u0d61\u0d85-\u0d96\u0d9a-\u0db1\u0db3-\u0dbb\u0dbd\u0dc0-\u0dc6\u0e01-\u0e30\u0e32\u0e33\u0e40-\u0e45\u0e81\u0e82\u0e84\u0e87\u0e88\u0e8a\u0e8d\u0e94-\u0e97\u0e99-\u0e9f\u0ea1-\u0ea3\u0ea5\u0ea7\u0eaa\u0eab\u0ead-\u0eb0\u0eb2\u0eb3\u0ebd\u0ec0-\u0ec4\u0edc\u0edd\u0f00\u0f40-\u0f47\u0f49-\u0f6a\u0f88-\u0f8b\u1000-\u1021\u1023-\u1027\u1029\u102a\u1050-\u1055\u10d0-\u10fa\u1100-\u1159\u115f-\u11a2\u11a8-\u11f9\u1200-\u1248\u124a-\u124d\u1250-\u1256\u1258\u125a-\u125d\u1260-\u1288\u128a-\u128d\u1290-\u12b0\u12b2-\u12b5\u12b8-\u12be\u12c0\u12c2-\u12c5\u12c8-\u12d6\u12d8-\u1310\u1312-\u1315\u1318-\u135a\u1380-\u138f\u13a0-\u13f4\u1401-\u166c\u166f-\u1676\u1681-\u169a\u16a0-\u16ea\u1700-\u170c\u170e-\u1711\u1720-\u1731\u1740-\u1751\u1760-\u176c\u176e-\u1770\u1780-\u17b3\u17dc\u1820-\u1842\u1844-\u1877\u1880-\u18a8\u1900-\u191c\u1950-\u196d\u1970-\u1974\u1980-\u19a9\u19c1-\u19c7\u1a00-\u1a16\u1b05-\u1b33\u1b45-\u1b4b\u2135-\u2138\u2d30-\u2d65\u2d80-\u2d96\u2da0-\u2da6\u2da8-\u2dae\u2db0-\u2db6\u2db8-\u2dbe\u2dc0-\u2dc6\u2dc8-\u2dce\u2dd0-\u2dd6\u2dd8-\u2dde\u3006\u303c\u3041-\u3096\u309f\u30a1-\u30fa\u30ff\u3105-\u312c\u3131-\u318e\u31a0-\u31b7\u31f0-\u31ff\u3400\u4db5\u4e00\u9fbb\ua000-\ua014\ua016-\ua48c\ua800\ua801\ua803-\ua805\ua807-\ua80a\ua80c-\ua822\ua840-\ua873\uac00\ud7a3\uf900-\ufa2d\ufa30-\ufa6a\ufa70-\ufad9\ufb1d\ufb1f-\ufb28\ufb2a-\ufb36\ufb38-\ufb3c\ufb3e\ufb40\ufb41\ufb43\ufb44\ufb46-\ufbb1\ufbd3-\ufd3d\ufd50-\ufd8f\ufd92-\ufdc7\ufdf0-\ufdfb\ufe70-\ufe74\ufe76-\ufefc\uff66-\uff6f\uff71-\uff9d\uffa0-\uffbe\uffc2-\uffc7\uffca-\uffcf\uffd2-\uffd7\uffda-\uffdc]',
		Co:'[\ue000\uf8ff]',
		Nd:'[\u0030-\u0039\u0660-\u0669\u06f0-\u06f9\u07c0-\u07c9\u0966-\u096f\u09e6-\u09ef\u0a66-\u0a6f\u0ae6-\u0aef\u0b66-\u0b6f\u0be6-\u0bef\u0c66-\u0c6f\u0ce6-\u0cef\u0d66-\u0d6f\u0e50-\u0e59\u0ed0-\u0ed9\u0f20-\u0f29\u1040-\u1049\u17e0-\u17e9\u1810-\u1819\u1946-\u194f\u19d0-\u19d9\u1b50-\u1b59\uff10-\uff19]',
		Lt:'[\u01c5\u01c8\u01cb\u01f2\u1f88-\u1f8f\u1f98-\u1f9f\u1fa8-\u1faf\u1fbc\u1fcc\u1ffc]',
		Lu:'[\u0041-\u005a\u00c0-\u00d6\u00d8-\u00de\u0100\u0102\u0104\u0106\u0108\u010a\u010c\u010e\u0110\u0112\u0114\u0116\u0118\u011a\u011c\u011e\u0120\u0122\u0124\u0126\u0128\u012a\u012c\u012e\u0130\u0132\u0134\u0136\u0139\u013b\u013d\u013f\u0141\u0143\u0145\u0147\u014a\u014c\u014e\u0150\u0152\u0154\u0156\u0158\u015a\u015c\u015e\u0160\u0162\u0164\u0166\u0168\u016a\u016c\u016e\u0170\u0172\u0174\u0176\u0178\u0179\u017b\u017d\u0181\u0182\u0184\u0186\u0187\u0189-\u018b\u018e-\u0191\u0193\u0194\u0196-\u0198\u019c\u019d\u019f\u01a0\u01a2\u01a4\u01a6\u01a7\u01a9\u01ac\u01ae\u01af\u01b1-\u01b3\u01b5\u01b7\u01b8\u01bc\u01c4\u01c7\u01ca\u01cd\u01cf\u01d1\u01d3\u01d5\u01d7\u01d9\u01db\u01de\u01e0\u01e2\u01e4\u01e6\u01e8\u01ea\u01ec\u01ee\u01f1\u01f4\u01f6-\u01f8\u01fa\u01fc\u01fe\u0200\u0202\u0204\u0206\u0208\u020a\u020c\u020e\u0210\u0212\u0214\u0216\u0218\u021a\u021c\u021e\u0220\u0222\u0224\u0226\u0228\u022a\u022c\u022e\u0230\u0232\u023a\u023b\u023d\u023e\u0241\u0243-\u0246\u0248\u024a\u024c\u024e\u0386\u0388-\u038a\u038c\u038e\u038f\u0391-\u03a1\u03a3-\u03ab\u03d2-\u03d4\u03d8\u03da\u03dc\u03de\u03e0\u03e2\u03e4\u03e6\u03e8\u03ea\u03ec\u03ee\u03f4\u03f7\u03f9\u03fa\u03fd-\u042f\u0460\u0462\u0464\u0466\u0468\u046a\u046c\u046e\u0470\u0472\u0474\u0476\u0478\u047a\u047c\u047e\u0480\u048a\u048c\u048e\u0490\u0492\u0494\u0496\u0498\u049a\u049c\u049e\u04a0\u04a2\u04a4\u04a6\u04a8\u04aa\u04ac\u04ae\u04b0\u04b2\u04b4\u04b6\u04b8\u04ba\u04bc\u04be\u04c0\u04c1\u04c3\u04c5\u04c7\u04c9\u04cb\u04cd\u04d0\u04d2\u04d4\u04d6\u04d8\u04da\u04dc\u04de\u04e0\u04e2\u04e4\u04e6\u04e8\u04ea\u04ec\u04ee\u04f0\u04f2\u04f4\u04f6\u04f8\u04fa\u04fc\u04fe\u0500\u0502\u0504\u0506\u0508\u050a\u050c\u050e\u0510\u0512\u0531-\u0556\u10a0-\u10c5\u1e00\u1e02\u1e04\u1e06\u1e08\u1e0a\u1e0c\u1e0e\u1e10\u1e12\u1e14\u1e16\u1e18\u1e1a\u1e1c\u1e1e\u1e20\u1e22\u1e24\u1e26\u1e28\u1e2a\u1e2c\u1e2e\u1e30\u1e32\u1e34\u1e36\u1e38\u1e3a\u1e3c\u1e3e\u1e40\u1e42\u1e44\u1e46\u1e48\u1e4a\u1e4c\u1e4e\u1e50\u1e52\u1e54\u1e56\u1e58\u1e5a\u1e5c\u1e5e\u1e60\u1e62\u1e64\u1e66\u1e68\u1e6a\u1e6c\u1e6e\u1e70\u1e72\u1e74\u1e76\u1e78\u1e7a\u1e7c\u1e7e\u1e80\u1e82\u1e84\u1e86\u1e88\u1e8a\u1e8c\u1e8e\u1e90\u1e92\u1e94\u1ea0\u1ea2\u1ea4\u1ea6\u1ea8\u1eaa\u1eac\u1eae\u1eb0\u1eb2\u1eb4\u1eb6\u1eb8\u1eba\u1ebc\u1ebe\u1ec0\u1ec2\u1ec4\u1ec6\u1ec8\u1eca\u1ecc\u1ece\u1ed0\u1ed2\u1ed4\u1ed6\u1ed8\u1eda\u1edc\u1ede\u1ee0\u1ee2\u1ee4\u1ee6\u1ee8\u1eea\u1eec\u1eee\u1ef0\u1ef2\u1ef4\u1ef6\u1ef8\u1f08-\u1f0f\u1f18-\u1f1d\u1f28-\u1f2f\u1f38-\u1f3f\u1f48-\u1f4d\u1f59\u1f5b\u1f5d\u1f5f\u1f68-\u1f6f\u1fb8-\u1fbb\u1fc8-\u1fcb\u1fd8-\u1fdb\u1fe8-\u1fec\u1ff8-\u1ffb\u2102\u2107\u210b-\u210d\u2110-\u2112\u2115\u2119-\u211d\u2124\u2126\u2128\u212a-\u212d\u2130-\u2133\u213e\u213f\u2145\u2183\u2c00-\u2c2e\u2c60\u2c62-\u2c64\u2c67\u2c69\u2c6b\u2c75\u2c80\u2c82\u2c84\u2c86\u2c88\u2c8a\u2c8c\u2c8e\u2c90\u2c92\u2c94\u2c96\u2c98\u2c9a\u2c9c\u2c9e\u2ca0\u2ca2\u2ca4\u2ca6\u2ca8\u2caa\u2cac\u2cae\u2cb0\u2cb2\u2cb4\u2cb6\u2cb8\u2cba\u2cbc\u2cbe\u2cc0\u2cc2\u2cc4\u2cc6\u2cc8\u2cca\u2ccc\u2cce\u2cd0\u2cd2\u2cd4\u2cd6\u2cd8\u2cda\u2cdc\u2cde\u2ce0\u2ce2\uff21-\uff3a]',
		Cs:'[\ud800\udb7f\udb80\udbff\udc00\udfff]',
		Zl:'[\u2028]',
		Nl:'[\u16ee-\u16f0\u2160-\u2182\u3007\u3021-\u3029\u3038-\u303a]',
		Zp:'[\u2029]',
		No:'[\u00b2\u00b3\u00b9\u00bc-\u00be\u09f4-\u09f9\u0bf0-\u0bf2\u0f2a-\u0f33\u1369-\u137c\u17f0-\u17f9\u2070\u2074-\u2079\u2080-\u2089\u2153-\u215f\u2460-\u249b\u24ea-\u24ff\u2776-\u2793\u2cfd\u3192-\u3195\u3220-\u3229\u3251-\u325f\u3280-\u3289\u32b1-\u32bf]',
		Zs:'[\u0020\u00a0\u1680\u180e\u2000-\u200a\u202f\u205f\u3000]',
		Sc:'[\u0024\u00a2-\u00a5\u060b\u09f2\u09f3\u0af1\u0bf9\u0e3f\u17db\u20a0-\u20b5\ufdfc\ufe69\uff04\uffe0\uffe1\uffe5\uffe6]',
		Pc:'[\u005f\u203f\u2040\u2054\ufe33\ufe34\ufe4d-\ufe4f\uff3f]',
		Pd:'[\u002d\u058a\u1806\u2010-\u2015\u2e17\u301c\u3030\u30a0\ufe31\ufe32\ufe58\ufe63\uff0d]',
		Pe:'[\u0029\u005d\u007d\u0f3b\u0f3d\u169c\u2046\u207e\u208e\u232a\u2769\u276b\u276d\u276f\u2771\u2773\u2775\u27c6\u27e7\u27e9\u27eb\u2984\u2986\u2988\u298a\u298c\u298e\u2990\u2992\u2994\u2996\u2998\u29d9\u29db\u29fd\u3009\u300b\u300d\u300f\u3011\u3015\u3017\u3019\u301b\u301e\u301f\ufd3f\ufe18\ufe36\ufe38\ufe3a\ufe3c\ufe3e\ufe40\ufe42\ufe44\ufe48\ufe5a\ufe5c\ufe5e\uff09\uff3d\uff5d\uff60\uff63]',
		Pf:'[\u00bb\u2019\u201d\u203a\u2e03\u2e05\u2e0a\u2e0d\u2e1d]',
		Me:'[\u0488\u0489\u06de\u20dd-\u20e0\u20e2-\u20e4]',
		Mc:'[\u0903\u093e-\u0940\u0949-\u094c\u0982\u0983\u09be-\u09c0\u09c7\u09c8\u09cb\u09cc\u09d7\u0a03\u0a3e-\u0a40\u0a83\u0abe-\u0ac0\u0ac9\u0acb\u0acc\u0b02\u0b03\u0b3e\u0b40\u0b47\u0b48\u0b4b\u0b4c\u0b57\u0bbe\u0bbf\u0bc1\u0bc2\u0bc6-\u0bc8\u0bca-\u0bcc\u0bd7\u0c01-\u0c03\u0c41-\u0c44\u0c82\u0c83\u0cbe\u0cc0-\u0cc4\u0cc7\u0cc8\u0cca\u0ccb\u0cd5\u0cd6\u0d02\u0d03\u0d3e-\u0d40\u0d46-\u0d48\u0d4a-\u0d4c\u0d57\u0d82\u0d83\u0dcf-\u0dd1\u0dd8-\u0ddf\u0df2\u0df3\u0f3e\u0f3f\u0f7f\u102c\u1031\u1038\u1056\u1057\u17b6\u17be-\u17c5\u17c7\u17c8\u1923-\u1926\u1929-\u192b\u1930\u1931\u1933-\u1938\u19b0-\u19c0\u19c8\u19c9\u1a19-\u1a1b\u1b04\u1b35\u1b3b\u1b3d-\u1b41\u1b43\u1b44\ua802\ua823\ua824\ua827]'
	};
	/* Also supports the general category (only the first letter) */
	var firstLetters = {};
	for (var p in unicodeCategories)
	{
		if (firstLetters[p[0]])
			firstLetters[p[0]] = unicodeCategories[p].substring(0,unicodeCategories[p].length-1) + firstLetters[p[0]].substring(1);
		else
			firstLetters[p[0]] = unicodeCategories[p];
	}
	for (var p in firstLetters)
		unicodeCategories[p] = firstLetters[p];

	/* Gets a regex written in a dialect that supports unicode categories and
	   translates it to a dialect supported by JavaScript. */
	return function(regexpString, classes)
	{
		var modifiers = "";
		if ( regexpString instanceof RegExp ) {
			modifiers = (regexpString.global ? "g" : "") +
						(regexpString.ignoreCase ? "i" : "") +
						(regexpString.multiline ? "m" : "");
			regexpString = regexpString.source;
		}
		regexpString = regexpString.replace(/\\p\{(..?)\}/g, function(match,group) {
		var unicode_categorie = unicodeCategories[group];
		if (!classes)
			unicode_category = unicode_categorie.replace(/\[(.*?)\]/g,"$1")
			return unicode_category || match;
		});
		return new RegExp(regexpString,modifiers);
	};

})();
function gvalidate_isEmail(s)
{
	var reg = unicode_hack3(/^[a-z\p{L}0-9!#$%&'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+[._a-z\p{L}0-9-]*\.[a-z\p{L}0-9]+$/i, false);
	return reg.test(s);
}
function gcheckCondition(){
    var is_empty = false;
    $('#using_condition_config_wp .condition_form_item').removeClass('condition_empty');
    if($('#using_condition_config_wp .condition_form_item').length > 0){
        $('#using_condition_config_wp .condition_form_item').each(function(){
            if($(this).find('.condition_config_if').val() == '' || $(this).find('.condition_config_email_to').val() == ''){
                is_empty = true;
                $(this).addClass('condition_empty');
            }else{
                var emails = $(this).find('.condition_config_email_to').val();
                var _emails = emails.split(';');
                if(_emails){
                    var condition_wp = $(this);
                    var not_empty = false;
                    $.each(_emails,function(key,val){
                        if(!is_empty && val !='' && !gvalidate_isEmail(val)){
                            is_empty = true;
                            condition_wp.addClass('condition_empty');
                        }else{
                            not_empty = true;
                        }
                    });
                    if(!not_empty){
                        is_empty = true;
                        $(this).addClass('condition_empty');
                    }
                }else{
                    is_empty = true;
                    $(this).addClass('condition_empty');
                }
                
            } 
        });
    }
    return is_empty;
}
function refreshFormbuilderGroup(formbuilder_group,cells){
    if(cells == '') cells = 12;
    var all_cell = cells.split('_');
    var total_cell = all_cell.length;
    var current_cell_length = formbuilder_group.find('.formbuilder_column').length;
    if(current_cell_length > 0) {
        var temp_nbr = 0;
        var current_formbuilder_column = null;
        formbuilder_group.find('.formbuilder_column').each(function () {
            if (temp_nbr < total_cell) {
                var width = $(this).attr('class').match(/col-md-(\d*)/)[1];
                var widthsm = $(this).attr('class').match(/col-sm-(\d*)/)[1];
                var widthxs = $(this).attr('class').match(/col-xs-(\d*)/)[1];
                if (width >= 1 && width <= 12) $(this).removeClass('col-md-' + width);
                if (widthsm >= 1 && widthsm <= 12) $(this).removeClass('col-sm-' + widthsm);
                if (widthxs >= 1 && widthxs <= 12) $(this).removeClass('col-xs-' + widthxs);
                $(this).addClass('col-md-' + all_cell[temp_nbr] + ' col-sm-12 col-xs-12');
                current_formbuilder_column = $(this);
            } else {
                $(this).find('.itemfield').each(function () {
                    current_formbuilder_column.find('.itemfield_wp').append($(this));
                });
                $(this).remove();
            }
            temp_nbr++;
        });
        if (total_cell > temp_nbr) {
            var temp;
            for (temp = temp_nbr; temp < total_cell; temp++) {
                addNewColumn(['col-md-' + all_cell[temp] + ' col-sm-12 col-xs-12'], formbuilder_group);
            }
        }
        refreshSortable();
    }
}


function productFormatResult(item) {
    itemTemplate = "<div class='media'>";
    itemTemplate += "<div class='pull-left'>";
    itemTemplate += "<img class='media-object' width='40' src='" + item.image + "' alt='" + item.name + "'>";
    itemTemplate += "</div>";
    itemTemplate += "<div class='media-body'>";
    itemTemplate += "<h4 class='media-heading'>" + item.name + "</h4>";
    itemTemplate += "</div>";
    itemTemplate += "</div>";
    return itemTemplate;
}
function productFormatSelection(item) {
    return item.name;
}
function delGformproductItem(id) {

    var reg = new RegExp(',', 'g');
    var input = $('#inputPackItems');
    var name = $('#namePackItems');

    var inputCut = input.val().split(reg);
    input.val(null);
    name.val(null);
    for (var i = 0; i < inputCut.length; ++i)
        if (inputCut[i]) {
            if (inputCut[i] != id) {
                input.val( input.val() + inputCut[i] + ',' );
            }
        }
    var elem = $('.product-pack-item[data-product-id="' + id + '"]');
    elem.remove();
}
function getSelectedIds()
{
    var reg = new RegExp(',', 'g');
    var input = $('#inputPackItems');
    if (input.val() === undefined)
        return '';
    var inputCut = input.val().split(reg);
    if($('#loadjqueryselect2').val() == '1')
        return inputCut;
    else{
        return inputCut.join(',');
    }
}

function addPackItem() {
    if($('#loadjqueryselect2').val() == '1'){
        if (selectedProduct) {
            if (typeof selectedProduct.id_product_attribute === 'undefined')
                selectedProduct.id_product_attribute = 0;

            var divContent = $('.product_field_popup').find('#divPackItems').html();
            divContent += '<div class="product-pack-item media-product-pack" data-product-name="' + selectedProduct.name + '" data-product-id="' + selectedProduct.id + '">';
            divContent += '<span class="media-product-pack-title">' + selectedProduct.name + '</span>';
            divContent += '<button type="button" class="btn btn-default delGformproductItem media-product-pack-action" data-delete="' + selectedProduct.id + '"><i class="icon-trash"></i></button>';
            divContent += '</div>';
            var line = selectedProduct.id;
            var lineDisplay = selectedProduct.name;

            $('.product_field_popup').find('#divPackItems').html(divContent);
            $('.product_field_popup').find('#inputPackItems').val($('.product_field_popup').find('#inputPackItems').val() + line  + ',');
            $(document).on('click', '.delGformproductItem', function(){
                e.preventDefault();
                e.stopPropagation();
                delGformproductItem($(this).data('delete'));
                return false;
            });
            selectedProduct = null;
            $('.product_field_popup').find('#curPackItemName').select2("val", "");
            $('.product_field_popup').find('.pack-empty-warning').hide();
        } else {
            return false;
        }
    }else{
        curPackItemId = $('.product_field_popup').find('#curPackItemId').val();
        curPackItemName = $('.product_field_popup').find('#curPackItemName').val();
        if(curPackItemId > 0){
            var divContent = $('.product_field_popup').find('#divPackItems').html();
            divContent += '<div class="product-pack-item media-product-pack" data-product-name="' + curPackItemName + '" data-product-id="' + curPackItemId + '">';
            divContent += '<span class="media-product-pack-title">' + curPackItemName + '</span>';
            divContent += '<button type="button" class="btn btn-default delGformproductItem media-product-pack-action" data-delete="' + curPackItemId + '"><i class="icon-trash"></i></button>';
            divContent += '</div>';
            $('.product_field_popup').find('#divPackItems').html(divContent);
            $('.product_field_popup').find('#inputPackItems').val($('.product_field_popup').find('#inputPackItems').val() + curPackItemId  + ',');
            $('.product_field_popup').find('#curPackItemId').val('');
            $('.product_field_popup').find('#curPackItemName').val('');
            $('.product_field_popup').find('#curPackItemName').setOptions({
                extraParams: {
                    excludeIds :  getSelectedIds()
                }
            });
        }
    }
}
$(document).on('click', '.delGformproductItem', function(){
    delGformproductItem($(this).data('delete'));
    return false;
})
function clearBeforeSave(){
    content = $('#formbuilder').clone();
    content.find('.add_new_element').remove();
    content.find('.ui-sortable').removeClass('ui-sortable');
    content.find('.ui-draggable').removeClass('ui-draggable');
    content.find('.itemfield').removeAttr('style');
    content.find('.control_box_wp').remove();
    content.find('.control_column_top').remove();
    $('#formbuilder_content').html(content.html());
    var myString = content.html();
    var returnIds = [];
    var pattern = /\[gformbuilderpro:(\d+)\]/gi;
    var match;
    while (match = pattern.exec(myString)){
        id_match = parseInt(match[1]);
        returnIds.push(id_match);
    }
    $('#fields').val(returnIds.join());
    var admin_attachfiles = [];var sender_attachfiles = [];
    if($('#admin_attachfiles_box .gformattachfile:checked').length > 0)
        $('#admin_attachfiles_box .gformattachfile:checked').each(function(){
            admin_attachfiles.push($(this).val());
        });
    $('#admin_attachfiles').val(admin_attachfiles.join());
    if($('#sender_attachfiles_box .gformattachfile:checked').length > 0)
        $('#sender_attachfiles_box .gformattachfile:checked').each(function(){
            sender_attachfiles.push($(this).val());
        });
    $('#sender_attachfiles').val(sender_attachfiles.join());
}
function addControlBt(field,width,widthsm,widthxs){
    if(field.hasClass('formbuilder_group'))
        control = $('#control_group').clone();
    else
        control = $('#control_box').clone();
    control.find('.formbuilder_group_width_md option').filter(function() {return this.value == width;}).attr('selected', 'selected').end();
    control.find('.formbuilder_group_width_sm option').filter(function() {return this.value == widthsm;}).attr('selected', 'selected').end();
    control.find('.formbuilder_group_width_xs option').filter(function() {return this.value == widthxs;}).attr('selected', 'selected').end();
    field.append(control.html());
    if(field.hasClass('formbuilder_group')){
        if(field.find('.formbuilder_column').length > 0){
            field.find('.formbuilder_column').each(function () {
                if($(this).find('.add_new_element').length == 0){
                    $(this).append($('.control_column .add_new_element').clone());
                }
                if($(this).find('.control_column_top').length == 0){
                    $(this).prepend($('.control_column_top_wp .control_column_top').clone());
                }
            });
        }
    }
}
function removeField(id_field,multi,group){
    if(id_field){
        deletefields = $('#deletefields').val();
        if(deletefields !='')
            $('#deletefields').val(deletefields+'_'+id_field);
        else
            $('#deletefields').val(id_field);
        if(multi){
            if(group.hasClass('formbuilder_column')){
                var formbuilder_group = group.closest('.formbuilder_group');
                group.remove();
                if(formbuilder_group.find('.formbuilder_column').length == 0)
                    formbuilder_group.remove();
            }else{
                group.remove();

            }
        }else{
            $("#gformbuilderpro_"+id_field).remove();
        }
    }else{
        if(multi){
            if(group.hasClass('formbuilder_column')){
                var formbuilder_group = group.closest('.formbuilder_group');
                group.remove();
                if(formbuilder_group.find('.formbuilder_column').length == 0)
                    formbuilder_group.remove();
            }else group.remove();
        }

    }
    checkIsBlankPage();
}
function gfromloadshortcode(){
    tinyMCE.triggerSave();
    var form = $('#gformbuilderpro_form');
    var action = form.attr('action');
    var serializedForm = form.serialize();
    $.ajax({
        type: 'POST',
        url: action,
        data: serializedForm+'&gfromloadshortcode=true',
        success: function (data, textStatus, request) {
            var result = $.parseJSON(data);
            if(result.errors=='0'){
                $.each(result.datas,function(index, value){
                        htmlshortcode = '<table>';
                        $('#using_condition_to_clone .condition_config_if').html('<option value=""  selected="selected"></option>');
                        $.each(value,function(_index, _value){
                            /* from 1.2.2 */
                            if($('#using_condition_to_clone').length > 0){
                                var select_option = '<option value="'+_value.shortcode+'">'+_value.label+':'+_value.shortcode+'</option>';
                                $(select_option).appendTo($('#using_condition_to_clone .condition_config_if'));
                            }
                            /* # from 1.2.2 */

                            if(parseInt(_index%2) == 1){
                                htmlshortcode+='<tr class="odd copy_group"><td><span data-toggle="tooltip" class="glabel-tooltip  copy_data copy_link" data-original-title="'+_value.label+'">'+_value.shortcode+'</span></td></tr>';
                            }else{
                                htmlshortcode+='<tr  class="even copy_group"><td><span data-toggle="tooltip" class="glabel-tooltip  copy_data copy_link" data-original-title="'+_value.label+'">'+_value.shortcode+'</span></td></tr>';
                            }
                        });

                        if($('#using_condition_config_wp .condition_form_item').length > 0){
                            $('#using_condition_config_wp .condition_form_item').each(function(key,val){
                                var condition_form_if = $(this).find('.condition_config_if');
                                var old_val = condition_form_if.val();
                                condition_form_if.html('<option value=""  selected="selected"></option>');
                                $.each(value,function(_index, _value){
                                    var select_option = '<option value="'+_value.shortcode+'" '+(old_val == _value.shortcode ? ' selected="selected" ' : '')+'>'+_value.label+':'+_value.shortcode+'</option>';
                                    $(select_option).appendTo(condition_form_if);
                                });
                                condition_form_if.change();
                            });
                        }

                        htmlshortcode += '</table>';
                        if($('.emailshortcode_wp .translatable-field.lang-'+index).length > 0){
                            $('.emailshortcode_wp .translatable-field.lang-'+index+' .emailshortcode').html(htmlshortcode);
                        }else{
                            $('.emailshortcode_wp .emailshortcode').html(htmlshortcode);
                        }
                        if($('.glabel-tooltip').length > 0)
                            $('.glabel-tooltip').tooltip();
                                
                    });
                $.each(result.file_fields,function(index, value){
                    if($('#admin_attachfiles_box .attachfiles_dynamic .file_'+value).length == 0){
                        var new_file_field = '<p class="file_'+value+'"><input class="gformattachfile" id="admin_attachfiles_'+value+'" type="checkbox" value="'+value+'" checked="checked" ><label for="admin_attachfiles_'+value+'"><code>{'+value+'}</code></label></p>';
                        $('#admin_attachfiles_box .attachfiles_dynamic').append(new_file_field);
                    }
                    if($('#sender_attachfiles_box .attachfiles_dynamic .file_'+value).length == 0){
                        var new_file_field = '<p class="file_'+value+'"><input class="gformattachfile" id="sender_attachfiles_'+value+'" type="checkbox"  value="'+value+'" checked="checked" ><label for="sender_attachfiles_'+value+'"><code>{'+value+'}</code></label></p>';
                        $('#sender_attachfiles_box .attachfiles_dynamic').append(new_file_field);
                    }
                });

            }else
                alert&("Error occurred!");

        },
        error: function (req, status, error) {
            $('#gformbuilderpro_overlay').remove();
            alert&("Error occurred!");
        }
    });
}
function date_format(format){
    if (format === undefined)
        return this.toString();

    var formatSeparator = format.match(/[.\/\-\s].*?/);
    var formatParts     = format.split(/\W+/);
    var result          = '';

    for (var i=0; i<=formatParts.length; i++) {
        switch(formatParts[i]) {
            case 'd':
            case 'j':
                result += this.getDate() + formatSeparator;
                break;

            case 'dd':
                result += (this.getDate() < 10 ? '0' : '')+this.getDate() + formatSeparator;
                break;

            case 'm':
                result += (this.getMonth() + 1) + formatSeparator;
                break;

            case 'mm':
                result += (this.getMonth() < 9 ? '0' : '')+(this.getMonth() + 1) + formatSeparator;
                break;

            case 'yy':
            case 'y':
                result += this.getFullYear() + formatSeparator;
                break;

            case 'yyyy':
            case 'Y':
                result += this.getFullYear() + formatSeparator;
                break;
        }
    }

    return result.slice(0, -1);
}
function addFieldData(data){
    var id_field = 0;
    let extra_   = ''; 
    if(typeof data.id != "undefined" && data.id > 0) id_field = data.id;
    else if(typeof data.id_gformbuilderprofields != "undefined" && data.id_gformbuilderprofields > 0) id_field = data.id_gformbuilderprofields;
    if(id_field > 0){
        var gfield_data = '';
        $.each(data,function(key,val){
            if(key == 'label' || key == 'placeholder' || key == 'value' || key == 'description'){
                $.each(val,function(_key,_val){
                    gfield_data+='<textarea class="'+key+'_'+_key+'">'+_val+'</textarea>';
                });
            } else if(key == 'condition_listoptions') {
                var number_listcondition = 0;
                $.each(val,function(_key,_val){
                    gfield_data+='<input type="text" class="'+key+'_id_field'+_key+'" value="'+_val.id_field+'">';
                    gfield_data+='<input type="text" class="'+key+'_conditionvalue'+_key+'" value="'+_val.conditionvalue+'">';
                    gfield_data+='<input type="text" class="'+key+'_condition'+_key+'" value="'+_val.condition+'">';
                    number_listcondition++;
                });
                gfield_data+='<input type="text" class="number_listcondition" value="'+number_listcondition+'">';
            } else{
                if (key == 'extra') {
                    extra_ = val;
                }
                gfield_data+='<input type="text" class="'+key+'" value="'+val+'">';
            }
        });
        if($('#gfield_datas #gfield_data_'+id_field).length > 0){
            $('#gfield_datas #gfield_data_'+id_field).html(gfield_data);
        }else{
            $('#gfield_datas').append('<div id="gfield_data_'+id_field+'" class="gfield_data">'+gfield_data+'</div>');
        }
        if ($('#gfield_datas #gfield_data_'+id_field).find('.type').val() == 'wholesale') {
            $('#gfield_datas #gfield_data_'+id_field).find('.extra').val(JSON.stringify(extra_));
        }
    }
}
function Delay(callback, ms) {
    var timer = 0;
    return function() {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
        callback.apply(context, args);
        }, ms || 0);
    };
}
/*search product new verion 1.3.6*/
function searchProductItems(el, number, empty, excludeids, includeids, combins) {
    var url = currentIndex + "&token=" + token + '&gformgetproductwhosale';
    if (empty)  $('.show_htmlsearch').append('<div class="gform_overlay"><div class="container"><div class="content"><div class="circle"></div></div></div></div>');
    $.ajax({
        type: "POST",
        url: url,
        data: "&q=" + $(el).val() + '&search_number=' + number+'&excludeids='+excludeids+'&includeids='+includeids+'&combins='+combins,
        dataType: 'json',
        async: true,
        success: function(datas) {
            if (datas) {
                if (empty) $('.show_htmlsearch').html('');
                const products_shows = datas;
                if (products_shows.length > 0) {
                    $('.show_htmlsearch div.gform_overlay').remove();
                    for (const [key, value] of Object.entries(products_shows)) {
                        product_whosale = html_product_whosale(value, includeids, combins);
                        $('.show_htmlsearch').append(product_whosale);
                    }
                } else {
                    $('.show_htmlsearch div.gform_overlay').remove();
                }
            } else {
                $('.show_htmlsearch div.gform_overlay').remove();
                $('.show_htmlsearch').html('');
            }
        },
        error: function(datas) {
            $('.show_htmlsearch div.gform_overlay').remove();
        },
    });
}
function html_product_whosale (value, includeids, combins) {
    includeids = includeids.split(',');
    combins    = JSON.parse(combins);
    var style_showcombin = 'style="display: none"';
    var style_showcombin_active = '';
    var checked_product = "";
    var product_whosale = '';
    product_whosale += '<div class="productbox-show-row" id="shows_row_' + value.id + '" data-id="' + value.id + '">';
    product_whosale += '<div class="productbox-show-row-info">';
    product_whosale += '<label class="productbox-show-checkbox-wrapper-checked" for="checkbox_shows_' + value.id + '">';
    product_whosale += '<span class="productbox-show-checkbox-checked">';
    if (includeids.includes(""+value.id+"")) {
        checked_product = 'checked="checked"';
        if (combins.length > 0) {
            style_showcombin = 'style="display: inline-block"';
            style_showcombin_active = 'active';
        }
    }
    product_whosale += '<input type="checkbox" class="productbox-show-checkbox-input" '+checked_product+' value="' + value.id + '" id="checkbox_shows_' + value.id + '"><span class="productbox-show-checkbox-inner"></span>';
    product_whosale += '</span></label>';
    product_whosale += '<div class="title"><div class="productbox-show-ui-item productbox-show-img">';
    product_whosale += '<span class="productbox-show-pro-img" style="background-image: url(' + value.image + ');"></span></div>';
    product_whosale += '<div class="productbox-show-ui-item productbox-show-row-title productbox-show-ui-item-fill"><label class="control-label" for="checkbox_shows_'+value.id+'">' + value.name + '</label>';
    
    if (value.conbination !== null && value.conbination !== '' && value.conbination.trim().length !== 0) {
        product_whosale += '<a class="ufe-variants-selected" '+style_showcombin+' href="#"><span>'+combins.length+'</span> ' + $('.itempage_text_variants').val() + '</a>';
    }
    product_whosale += '</div></div>';
    if ( value.conbination  !== null &&  value.conbination  !== '' &&  value.conbination.trim().length !== 0) {
        product_whosale += '<div class="productbox-show-col"><a><i class="icon-caret-down"></i></a></div>';
    }
    product_whosale += '</div>';
    product_whosale += '<div class="combination_show_search '+style_showcombin_active+'">' + value.conbination + '</div>';
    product_whosale += '</div>';
    

    return product_whosale;
}
function htmloptiondiscount(vouchers, number, currency_alls, currency_default) {
    let qty = 0;
    let type = 0;
    let currency = currency_default;
    let value_discount = 0;
    let tax = 0;
    let active_none = 'style="display:none;"'
    if (vouchers) {
        qty = vouchers.qty;
        type = vouchers.type;
        currency = vouchers.currency;
        value_discount = vouchers.value;
        tax = vouchers.tax;
    }
    number = parseInt(number) + 1;
    currency_alls = JSON.parse(currency_alls);
    var html_option_discount = '<tr class="option_discount" id="option_discount_'+number+'" data-number="'+number+'">';
    html_option_discount += '<td class="gform_discountqty"><input value="'+qty+'" type="number" min="0" class="form-control gformoption_discounts_qty gformvalidate-error" name="gformoption_discounts['+number+'][qty]"/></td>';
    
    html_option_discount += '<td> <select name="gformoption_discounts['+number+'][type]" class="gformoption_discounts_type"> ';
    if (type == '0') {
        html_option_discount += '<option value="0" selected="selected">'+$('.text_discount_Percentage').val()+'</option>';
        html_option_discount += '<option value="1">'+$('.text_discount_Amount').val()+'</option>';
    } else {
        active_none = '';
        html_option_discount += '<option value="0">'+$('.text_discount_Percentage').val()+'</option>';
        html_option_discount += '<option value="1" selected="selected">'+$('.text_discount_Amount').val()+'</option>';
    }
    html_option_discount += '</select></td>';
    
    html_option_discount += '<td>';
    html_option_discount += '<div class="col-lg-4"><input type="number" value="'+value_discount+'" class="form-control gformoption_discounts_value gformvalidate-error" min="0" name="gformoption_discounts['+number+'][value]"/></div>';
    html_option_discount += '<div class="col-lg-4 gformnone" '+active_none+'><select class="gformoption_discounts_currency" name="gformoption_discounts['+number+'][currency]">';
    if (currency_alls.length > 0) {
        $.each(currency_alls,function(key,value){
            var selectoption = '';
            if (value.id == currency) {
                selectoption = 'selected="selected"';
            }
            html_option_discount += '<option value="'+value.id+'" '+selectoption+'>'+value.name+'</option>';
        });
    }
    html_option_discount +='</select></div>';
    html_option_discount += '<div class="col-lg-4 gformnone" '+active_none+'><select name="gformoption_discounts['+number+'][tax]" class="gformoption_discounts_tax">';
    if (tax == '0') {
        html_option_discount += '<option value="0" selected="selected">'+$('.text_discount_exctax').val()+'</option>';
        html_option_discount += '<option value="1">'+$('.text_discount_inctax').val()+'</option>';
    } else {
        html_option_discount += '<option value="0">'+$('.text_discount_exctax').val()+'</option>';
        html_option_discount += '<option value="1" selected="selected">'+$('.text_discount_inctax').val()+'</option>';
    }
    html_option_discount += '</select></div>';
    html_option_discount += '</td>';
    html_option_discount += '<td> <a class="btn btn-default pull-right gformremove_discount"><i class="icon-times-circle text-danger"></i></a> </td>';
    html_option_discount += '</tr>';
    $('.show_htmlsearch_discount .plus_newrowdiscount').data('number', number);
    return html_option_discount;
}
$(document).ready(function(){
    if(typeof tooltip != 'undefined')
        if($('.glabel-tooltip').length > 0)
            $('.glabel-tooltip').tooltip();
    $(document).on('click', '.block_this_ip', function(){
        var block_this_ip = $(this);
        var gbanned = block_this_ip.hasClass('gbanned');
        var ip_address = $(this).attr('rel');
        var idshop = $(this).attr('data-shop');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: '&ajax=1&gBanIp=1&ip_address='+ip_address+'&idshop='+idshop+'&banned='+gbanned,
            success: function(datas) {
                if(!datas.error){
                    showSuccessMessage(datas.warrning);
                    if(gbanned){
                        block_this_ip.removeClass('gbanned');
                    }else{
                        block_this_ip.addClass('gbanned');
                    }
                }else{
                    showErrorMessage(datas.warrning);
                }
            }
        });
        return false;
    });
    $(document).on('click', '.gremove_analytics_datas', function(){
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: $('.gcalendar_chart_form').serialize()+'&ajax=1&gremoveAnalyticsAatas=1',
            success: function(datas) {
                if(!datas.error){
                    showSuccessMessage(datas.warrning);
                }else{
                    showErrorMessage(datas.warrning);
                }
                setTimeout(function(){
                    window.location.href = window.location.href;
                }, 1000);
            }
        });
        return false;
    });

    $(document).on('click', '.gpagination a', function(){
        $.ajax({
            url: $(this).attr('href'),
            type: 'POST',
            dataType: 'json',
            data: $('.gcalendar_chart_form').serialize()+'&ajax=1',
            success: function(datas) {
                $('.gpagination').html(datas.pagination);
                $('.analytics_datas_table tbody').html('');
                if(datas.analytics_datas){
                    $.each(datas.analytics_datas,function (key,val) {
                        var tr_data = '<tr>';
                        tr_data += '<td>'+val.title+'</td>';
                        tr_data += '<td>'+(val.id_customer > 0 ? '<a href="'+datas.customer_link+val.id_customer+'" target="_blank">'+(val.firstname === null ? '' : val.firstname)+' '+(val.lastname === null ? '' : val.lastname)+'</a>' : '')+'</td>';
                        tr_data += '<td><a href="/'+'/whatismyipaddress.com/ip/'+val.ip_address+'" target="_blank" title="'+datas.click_to_view+'">'+val.ip_address+'</a></td>';
                        tr_data += '<td>'+val.platform+'</td>';
                        tr_data += '<td>'+val.browser+'</td>';
                        tr_data += '<td>'+val.browser_version+'</td>';
                        tr_data += '<td><div class="user_agent_box">'+val.user_agent+'</div></td>';
                        tr_data += '<td>'+val.date_add+'</td>';
                        tr_data += '<td><a href="" data-shop="'+datas.id_shop+'" rel="'+val.ip_address+'" class="block_this_ip btn btn-default '+(datas.banned ? 'gbanned' : '')+'"><span class="gbantitle">'+datas.ban_title+'</span><span class="gunbantitle">'+datas.gunbantitle+'</span></a></td>';
                        tr_data += '</tr>';
                        $('.analytics_datas_table tbody').append(tr_data);
                    });
                }
            }
        });
        return false;
    });
    if($('#browser_chart').length > 0)
    {
        var height = 230;

        nv.addGraph(function() {
            var chart = nv.models.pieChart()
                .x(function(d) { return d.key })
                .y(function(d) { return d.y })
                .height(height);
            d3.select("#browser_chart svg")
                .datum(brower_chart_datas)
                .transition().duration(1200)
                .attr('height', height)
                .call(chart);
            nv.utils.windowResize(chart.update);
            return chart;
        });
        nv.addGraph(function() {
            var chart3 = nv.models.pieChart()
                .x(function(d) { return d.key })
                .y(function(d) { return d.y })
                .height(height);
            d3.select("#platform_chart svg")
                .datum(platform_chart_datas)
                .transition().duration(1200)
                .attr('height', height)
                .call(chart3);
            nv.utils.windowResize(chart3.update);
            return chart3;
        });

    }
    if($('#mainchart2').length > 0)
    {
        if(typeof Date.prototype.format == "undefined"){
            Date.prototype.format = function(format) {
                if (format === undefined)
                    return this.toString();

                var formatSeparator = format.match(/[.\/\-\s].*?/);
                var formatParts     = format.split(/\W+/);
                var result          = '';

                for (var i=0; i<=formatParts.length; i++) {
                    switch(formatParts[i]) {
                        case 'd':
                        case 'j':
                            result += this.getDate() + formatSeparator;
                            break;

                        case 'dd':
                            result += (this.getDate() < 10 ? '0' : '')+this.getDate() + formatSeparator;
                            break;

                        case 'm':
                            result += (this.getMonth() + 1) + formatSeparator;
                            break;

                        case 'mm':
                            result += (this.getMonth() < 9 ? '0' : '')+(this.getMonth() + 1) + formatSeparator;
                            break;

                        case 'yy':
                        case 'y':
                            result += this.getFullYear() + formatSeparator;
                            break;

                        case 'yyyy':
                        case 'Y':
                            result += this.getFullYear() + formatSeparator;
                            break;
                    }
                }

                return result.slice(0, -1);
            }
        }
        var mainchart2_width2 = $('#mainchart2').width();
        var mainchart2_height2 = 550;
        if($('.admingformdashboard').length > 0) mainchart2_height2 = 345;
        nv.addGraph(function() {
            chart2 = nv.models.lineChart()
                .options({
                    duration: 300,
                    useInteractiveGuideline: true
                })
                .x(function(d) { return d.key })
                .y(function(d) { return d.y })
                .height(mainchart2_height2)
            ;
            chart2.xAxis
                .axisLabel('')
                .tickFormat(function(d) {
                    var date = new Date(d*1000);
                    return date.format(gchart_date_format);
                })
                .staggerLabels(true)
            ;
            chart2.yAxis
                .axisLabel('')
                .tickFormat(function(d) {
                    if (d == null) {
                        return 'N/A';
                    }
                    return d3.format(',.2d')(d);
                })
            ;
            d3.select('#mainchart2 svg')
                .datum(mainchartdatas)
                .attr('height', mainchart2_height2)
                .call(chart2);
            nv.utils.windowResize(chart2.update);
            return chart2;
        });
    }


    fixOldTemplate();
    checkIsBlankPage();


    $('.select_form_calender').change(function(){
        $(this).closest('form').submit();
    });


    $(document).on('click', '.formrequest_link', function(){
        if($('#submit_to_formrequest').length > 0) $('#submit_to_formrequest').submit();
        return false;
    });
    $(document).on('click', '.formbuilder_popup_set_columns', function(){
        var cells = $(this).attr('data-cells');
        $('.set_column_custom_data').val(cells);
        return false;
    });
    $(document).on('click', '#add_products_item_fromlist', function(){
        $('.box_setting_products').addClass('active');
        $('.box_setting_products').find('.itempage_product_search').val('add');
        excludeids = new Array();
        combins = new Array();
        if ($('.wholesale-product').find('li').length > 0) {
            $('.wholesale-product').find('li').each(function() {
                if ($(this).find('input.configproduct-show-checkbox-input').is(':checked') == true) {
                    var product_id = $(this).find('input.configproduct-show-checkbox-input').val();
                    excludeids.push(product_id);
                }
            });
        }
        searchProductItems('#gform_search_product', '', true, excludeids.join(','), '',JSON.stringify(combins));
    });
    $(document).on('click', '.whosale_voucher_optionproduct', function(){
        var id = $(this).data('id');
        var vouchers = $('#option_voucher_'+id).val();
        var currency_alls = $('.gfomCurrencies').val();
        var currency_default = $('.gfomid_currency_default').val();
        $('.box_setting_products_discount').addClass('active');
        $('.box_setting_products_discount').find('.itempage_discount_edit').val(id);
        
        if (vouchers !='' && vouchers.length > 0 && vouchers != null && vouchers != 'null') {
            vouchers = JSON.parse(vouchers);
            vouchers.forEach(function(val, key) {
                var number = $('.show_htmlsearch_discount table tbody tr').length;
                var html = htmloptiondiscount(val, number, currency_alls, currency_default);
                $('.show_htmlsearch_discount table tbody').append(html);
            });

        } else {
            var number = $('.show_htmlsearch_discount table tbody tr').length;
            var html = htmloptiondiscount('',number, currency_alls, currency_default);
            $('.show_htmlsearch_discount table tbody').append(html);
        }

    });
    $(document).on('click', '.gformremove_discount', function(){
        $(this).closest('tr.option_discount').remove();
        return false;
    });
    $(document).on('change', '.gformoption_discounts_type', function(){
        if ($(this).val() == 1) {
            $(this).closest('tr.option_discount').find('.gformnone').show();
        } else {
            $(this).closest('tr.option_discount').find('.gformnone').hide();
        }
        return false;
    });
    
    $(document).on('click', '.plus_newrowdiscount', function(){
        var number = $(this).data('number');
        var currency_alls = $('.gfomCurrencies').val();
        var currency_default = $('.gfomid_currency_default').val();
        var html = htmloptiondiscount('',number, currency_alls, currency_default);
        $('.show_htmlsearch_discount table tbody').append(html);
    });

    /*search product*/
    $(document).on('keyup', 'input#gform_search_product', Delay(function (e) {
        excludeids = new Array();
        combins = new Array();
        if ($('.wholesale-product').find('li').length > 0) {
            $('.wholesale-product').find('li').each(function() {
                if ($(this).find('input.configproduct-show-checkbox-input').is(':checked') == true) {
                    var product_id = $(this).find('input.configproduct-show-checkbox-input').val();
                    excludeids.push(product_id);
                }
            });
        }
        searchProductItems('#gform_search_product', '', true, excludeids.join(','), '',JSON.stringify(combins));
    }, 1500));

    $(document).on('click', '.box_setting_displayclose', function(){
        $('.box_setting_products').removeClass('active');
        $('.show_htmlsearch').html('');
        $('#gform_search_product').val('');
    });
    $(document).on('click', '.box_setting_displayclose_discount', function(){
        $('.box_setting_products_discount').removeClass('active');
        $('.show_htmlsearch_discount table tbody').html('');
    });
    $(document).on('click', '.productbox-show-col', function(){
        $(this).closest('.productbox-show-row').find('.combination_show_search').toggleClass('active');
        $(this).find("i").toggleClass('icon-caret-up');
    });

    $('.show_htmlsearch').on('scroll', function() {
        if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
            var number_search = parseInt($(this).find('.productbox-show-row').length);
            excludeids = new Array();
            combins = new Array();
            if ($('.wholesale-product').find('li').length > 0) {
                $('.wholesale-product').find('li').each(function() {
                    if ($(this).find('input.configproduct-show-checkbox-input').is(':checked') == true) {
                        var product_id = $(this).find('input.configproduct-show-checkbox-input').val();
                        excludeids.push(product_id);
                    }
                });
            }
            
            if ($('.box_setting_products').find('.itempage_product_search').val() != 'edit') {
                searchProductItems("#gform_search_product", parseInt(number_search), false, excludeids.join(','), '', JSON.stringify(combins));
            }
            return false;
        }
    });
    $(document).on('click', '.action_productwhosale .whosale_remove_optionproduct', function(){
        var id = $(this).data('id');
        $('#configproduct_'+id).closest('li').remove();
    });
    $(document).on('click', '.action_productwhosale .whosale_edit_optionproduct', function(){
        var id = $(this).data('id');
        $('#gform_search_product').val(id);
        $('#gform_search_product').attr('disabled', 'disabled');
        
        var includeids = new Array();
        var combins  = new Array(); 
        includeids.push(id);
        if ($('#configproduct_'+ id).length > 0) {
            combins[id] = new Array();
            $('#configproduct_'+ id +' .configproduct_combination_show_search input').each(function() {
                if ($(this).is(':checked') == true) {
                    var conbination_id = $(this).val();
                    combins[id][conbination_id]= conbination_id ;
                }
            });
        }
        searchProductItems('#gform_search_product', '', true, '', includeids.join(','), JSON.stringify(combins));
        $('.box_setting_products').find('.itempage_product_search').val('edit');
        $('.box_setting_products').addClass('active');
    });
    
    
    $(document).on('click', '.formbuilder_set_columns', function(){
        if(!$(this).hasClass('formbuilder_set_column_custom')){
            var cells = $(this).attr('data-cells');
            var formbuilder_group  = $(this).closest('.formbuilder_group');
            refreshFormbuilderGroup(formbuilder_group,cells);
        }
        return false;
    });
    $(document).on('click', '#change_row_btn', function(){
        var cells = $('.set_column_custom_data').val();
        var formbuilder_group  = $('.formbuilder_set_column_custom.pendding').closest('.formbuilder_group');
        refreshFormbuilderGroup(formbuilder_group,cells);
        $.fancybox.close();
        if($('.pendding').length > 0)
            $('.pendding').removeClass('pendding');
        return false;
    });


    $(document).on('click', '#new_condition_config', function(){
        var is_empty = gcheckCondition();
        if(!is_empty){
            var new_condition_config = $('#using_condition_to_clone tr').clone();
            $(new_condition_config).appendTo($('#using_condition_config_wp tbody'));
        }
        return false;
    });
    $(document).on('click', '.gremove_condition', function(){
        $(this).parents('.condition_form_item').remove();
        return false;
    });
    $(document).on('change', '.condition_config_if,.condition_config_email_to', function(){
        gcheckCondition();
    });
    /*new version 1.3.6*/
    $(document).on('change', 'input[name="condition"]', function(){
        if ($(this).val() == 0) {
            $('.popup_field_content .gfield_condition_display').css('display', 'none');
        } else {
            $('.popup_field_content .gfield_condition_display').css('display', 'block');
        }
    });
    $(document).on('click', '.ufe-variants-selected', function(){
        $(this).closest('.productbox-show-row').find('.productbox-show-col').trigger('click');
    });

    $(document).on('change', 'input.productbox-show-checkbox-input', function() {
        if ($(this).is(':checked') == true) {
            if ($(this).closest('.productbox-show-row').find('.combination_show_search input').length > 0) {
                var number_select = 0;
                $(this).closest('.productbox-show-row').find('.combination_show_search input').prop('checked', true);
                $(this).closest('.productbox-show-row').find('.ufe-variants-selected').show();
                $(this).closest('.productbox-show-row').find('.combination_show_search .gform_variant-list li').each(function() {
                    if ($(this).find('input').is(':checked') == true) {
                        number_select = number_select + 1;
                    }
                });
                if (number_select > 0) {
                    $(this).closest('.productbox-show-row').find('a.ufe-variants-selected span').text(number_select);
                } else {
                    $(this).closest('.productbox-show-row').find('input.productbox-show-checkbox-input').prop('checked', false);
                }
            }
        } else {
            if ($(this).closest('.productbox-show-row').find('.combination_show_search input').length > 0) {
                $(this).closest('.productbox-show-row').find('.combination_show_search input').prop('checked', false);
                $(this).closest('.productbox-show-row').find('.ufe-variants-selected').hide();
            }
        }
    });

    $(document).on('change', '.combination_show_search.active input', function() {
        if ($(this).closest('.combination_show_search .gform_variant-list').find('li').length > 0) {
            var number_select = 0;
            $(this).closest('.combination_show_search .gform_variant-list').find('li').each(function() {
                if ($(this).find('input').is(':checked') == true) {
                    number_select++;
                }
            });
            if (number_select > 0) {
                $(this).closest('.productbox-show-row').find('a.ufe-variants-selected span').text(number_select);
                $(this).closest('.productbox-show-row').find('input.productbox-show-checkbox-input').prop('checked', true);
                $(this).closest('.productbox-show-row').find('.ufe-variants-selected').show();
            } else {
                $(this).closest('.productbox-show-row').find('input.productbox-show-checkbox-input').prop('checked', false);
                $(this).closest('.productbox-show-row').find('a.ufe-variants-selected span').text(number_select);
                $(this).closest('.productbox-show-row').find('.ufe-variants-selected').hide();
            }
        }
    });

    $(document).on('click', '#box_setting_showinsavedisplay_discount', function(){
        var check = false;
        var old_value = '';
        var id_product = $('.box_setting_products_discount .itempage_discount_edit').val();
        var discounts = new Array();
        if ($('.show_htmlsearch_discount table tbody tr').length > 0) {
            $('.show_htmlsearch_discount table tbody tr').each(function(){
                var number = $(this).data('number');
                var qty = $(this).find('.gformoption_discounts_qty').val();
                var type = $(this).find('.gformoption_discounts_type').val();
                var value = $(this).find('.gformoption_discounts_value').val();
                var currency = $(this).find('.gformoption_discounts_currency').val();
                var tax = $(this).find('.gformoption_discounts_tax').val();
                discounts.push(
                    {
                        "qty":qty,
                        "type":type,
                        "value":value,
                        "currency":currency,
                        "tax":tax
                    });
                if (qty == '') {
                    check = true;
                    $(this).find('.gformoption_discounts_qty').addClass('gform-error');
                } else {
                    if (old_value != '' && parseInt(old_value) >= parseInt(qty)) {
                        check = true;
                        $(this).find('.gformoption_discounts_qty').addClass('gform-error');
                    } else {
                        $(this).find('.gformoption_discounts_qty').removeClass('gform-error');
                    }
                    old_value = qty;
                }
                if (value == '') {
                    check = true;
                    $(this).find('.gformoption_discounts_value').addClass('gform-error');
                } else {
                    $(this).find('.gformoption_discounts_value').removeClass('gform-error');
                }
            });
        }
        if (check) {
            showErrorMessage($('#gtext_cutom .gformtext_check_qtydiscount').val());
            return false;
        } else {
            $('#option_voucher_'+id_product).val(JSON.stringify(discounts));
            $('.box_setting_products_discount').removeClass('active');
            $('.show_htmlsearch_discount table tbody').html('');
            return false;
        }
    });
    $(document).on('click', '#box_setting_showinsavedisplay', function(){
        var products =  new Array();
        var combins  = new Array();
        if ($('.show_htmlsearch input.productbox-show-checkbox-input').length > 0) {
            $('.show_htmlsearch input.productbox-show-checkbox-input').each(function() {
                if ($(this).is(':checked') == true) {
                    var product_id = $(this).val();
                    products.push(product_id);
                    combins[product_id] = new Array();
                    
                    if ($(this).closest('.productbox-show-row').find(".gform_variant-list").find('input').length > 0) {
                        $(this).closest('.productbox-show-row').find(".gform_variant-list").find('input').each(function () {
                            if ($(this).is(':checked') == true) {
                                var conbination_id = $(this).val();
                                combins[product_id][conbination_id]= conbination_id ;
                            }
                        });
                    }
                }
            });
        }
        if (products.length > 0) {
            var url = currentIndex + "&token=" + token + '&getProductwhosaleConfig';
            $.ajax({
                type: "POST",
                url: url,
                data: "&products=" + products.join(',') +"&combins="+ JSON.stringify(combins),
                dataType: 'json',
                async: true,
                success: function(datas) {
                    if (datas) { 
                        const products_shows = datas;
                        if (products_shows.length > 0) {
                            for (const [key, value] of Object.entries(products_shows)) {
                                var product_whosale = '';
                                product_whosale += '<div class="configproduct-row" id="configproduct_' + value.id + '" data-id="' + value.id + '">';
                                product_whosale += '<div class="configproduct-show-row-info">';
                                product_whosale += '<input type="text" class="hidden" value="" id="option_voucher_' + value.id + '"  name="gform_products_voucher[' + value.id + ']">';
                                product_whosale += '<input type="checkbox" class="configproduct-show-checkbox-input hidden" value="' + value.id + '" id="checkbox_option_shows_' + value.id + '" checked="checked" name="gform_idproducts[]">';
                                product_whosale += '<div class="title"><div class="configproduct-show-ui-item productbox-show-img">';
                                product_whosale += '<span class="configproduct-show-pro-img" style="background-image: url(' + value.image + ');"></span></div>';
                                product_whosale += '<div class="configproduct-show-ui-item configproduct-show-row-title configproduct-show-ui-item-fill"><label class="control-label">' + value.name + '</label>';
                                product_whosale += '</div>';
                                product_whosale += '<div class="action_productwhosale"><a  class="btn btn-default whosale_voucher_optionproduct" data-id="' + value.id + '"><i class="icon-AdminPriceRule"></i></a><a type="button" class="pull-right btn btn-default gbtn-default-red whosale_remove_optionproduct" data-id="' + value.id + '" data-type=""><i class="icon-trash"></i></a><a type="button" class="pull-right btn btn-default gbtn-default whosale_edit_optionproduct" data-id="' + value.id + '" data-type=""><i class="icon-pencil"></i></a></div>';
                                product_whosale += '</div></div>';
                                product_whosale += '<div class="configproduct_combination_show_search">' + value.conbination + '</div>';
                                product_whosale += '</div>';
                                if ($('#configproduct_' + value.id).length > 0) {
                                    $('.wholesale_field_popup .gfield_extra_extraproducts .wholesale-product').find('#configproduct_' + value.id).closest('li').html(product_whosale);
                                } else {
                                    $('.wholesale_field_popup .gfield_extra_extraproducts .wholesale-product').append('<li>'+product_whosale+'</li>');
                                }
                            }
                        }
                        $('.box_setting_products').removeClass('active');
                        $('.show_htmlsearch').html('');
                        $('#gform_search_product').val('');
                    }
                },
                error: function(datas) {
                    return false;
                },
            });
        } else {
            if ($('.box_setting_products').find('.itempage_product_search').val() == 'edit') {
                if ($('#gform_search_product').val() != '') {
                    $('#configproduct_'+$('#gform_search_product').val()).closest('li').remove();
                    $('.box_setting_products').removeClass('active');
                    $('.show_htmlsearch').html('');
                    $('#gform_search_product').val('');
                }
            }
        }
    });

    $(document).on('click', '.formbuilder_add_more', function(){
        var formbuilder_group = $(this).closest('.formbuilder_group');
        addNewColumn(['col-md-12 col-sm-12 col-xs-12'],formbuilder_group);
        return false;
    });
    $(document).on('click', '.add_multival_newval', function(){
        rel = $(this).parents('.multival_box').attr('rel');
        var multival_box = $(this).parents('.multival_box');
        default_val = multival_box.find('.multival_newval_'+default_language).val();
        if(default_val == ''){
            /* get other data val*/
            $.each(languages,function(key,val){
                default_val = multival_box.find('.multival_newval_'+val.id_lang).val();
                if(default_val != '') return false;
            })
        }
        
        if(default_val == ''){
            multival_box.find('.value_invalid').stop().slideDown(500);
        }else{
            multival_box.find('.value_invalid').slideUp(500);
            if($(this).hasClass('updatebt')){
                /*update*/
                $.each(languages,function(key,val){
                    _val = multival_box.find('.multival_newval_'+val.id_lang).val();
                    if(_val == '') _val = default_val;
                    multival_box.find('.multival.inedit .lang-'+val.id_lang).html(_val);
                });
                multival_box.find('.cancel_multival_newval').slideUp(500);
                multival_box.find('.updatelabel').css('display','none');
                multival_box.find('.addlabel').css('display','inline-block');
                $.each(languages,function(key,val){
                    multival_box.find('.multival_newval_'+val.id_lang).val('');
                });
                multival_box.find('.multival').removeClass('inedit');
                multival_box.find('.add_multival_newval').removeClass('updatebt');
            }else{
                /*add new*/
                new_val = '<div class="multival">';
                    $.each(languages,function(key,val){
                        
                        new_val+='<div class="translatable-field lang-'+val.id_lang+'" ';
                        if(id_language != val.id_lang) new_val+=' style="display:none"';
                        new_val+='>';
                        
                        newval = multival_box.find('.multival_newval_'+val.id_lang).val();
                        if(newval == '') newval = default_val;
                        new_val+=newval;
                        new_val+='</div>';
                    });
                new_val +=multival_box.find('.multival_action_wp').html();
                new_val += '</div>';
                $(new_val).appendTo(multival_box.find('.multival_wp'));
                $.each(languages,function(key,val){
                    multival_box.find('.multival_newval_'+val.id_lang).val('');
                });
                multival_box.find(".multival_wp").sortable({
                    handle: '.multival_move',
                    opacity:0.5,
                    cursor:'move',
                });
            }
        }
        return false;
    });
    $(document).on('click', '.multival_edit', function(){
        var multival_box = $(this).parents('.multival_box');
        rel = multival_box.attr('rel');
        $('#multival_'+rel+' .multival').removeClass('inedit');
        $(this).parents('.multival').addClass('inedit');
        $.each(languages,function(key,val){
            multival_box.find('.multival_newval_'+val.id_lang).val(multival_box.find('.multival.inedit .lang-'+val.id_lang).text());
        });
        multival_box.find('.cancel_multival_newval').slideDown(500);
        multival_box.find('.updatelabel').css('display','inline-block');
        multival_box.find('.addlabel').css('display','none');
        multival_box.find('.add_multival_newval').addClass('updatebt');
        multival_box.find('.value_invalid').slideUp(500);
        return false;
    });
    $(document).on('click', '.cancel_multival_newval', function(){
        var rel = $(this).parents('.multival_box').attr('rel');
        var multival_box = $(this).parents('.multival_box');
        multival_box.find('.updatelabel').css('display','none');
        multival_box.find('.addlabel').css('display','inline-block');
        $.each(languages,function(key,val){
            multival_box.find('.multival_newval_'+val.id_lang).val('');
        });
        multival_box.find('.multival').removeClass('inedit');
        multival_box.find('.add_multival_newval').removeClass('updatebt');
        multival_box.find('.cancel_multival_newval').slideUp(500);
        multival_box.find('.value_invalid').slideUp(500);
        return false;
    });
    $(document).on('click', '.multival_delete', function(){
        $(this).parents('.multival').remove();
        return false;
    });
    $(document).on('click', '.formbuilder_minify', function(){
        itemfield_wp = $(this).parents('.formbuilder_group');
        if(itemfield_wp.hasClass('has_minify')){
            itemfield_wp.removeClass('has_minify');
        }else{
            itemfield_wp.addClass('has_minify');
        }
        return false;
    });
    $('#gformbuilderpro_change_status').change(function(){
        val = $(this).val();
        id = $(this).attr('rel');
        $.ajax({
            url: currentIndex +'&token='+token+'&changeStatus=1&id='+id+'&val='+val,
            type: 'POST',
            dataType: 'json',
            success: function(datas) {
                if(datas.success){
                    showSuccessMessage(datas.warrning); 
                }else{
                    showErrorMessage(datas.warrning); 
                }
            }
        });
        
    });
    
    if(typeof psversion15 === 'undefined'){ 
    }else 
    if(psversion15 == -1){
        $('body').addClass('psversion15');
    }
    gformbuilderpro_overlay = '<div id="gformbuilderpro_overlay"><div class="container"><div class="content"><div class="circle"></div></div></div></div></div>';
    
    var beforeShow_call = 0;
    
    
    $('.choose_email_template_box').fancybox({
        'closeBtn' : false ,
        'padding': 0,
        'wrapCSS'    : 'fancybox_gcustom_style',
        helpers: {
            overlay: { closeClick: false }
        }
        }
    );
    $('.add_element').fancybox({
        'closeBtn' : false ,
        'padding': 0,
        'wrapCSS'    : 'fancybox_gcustom_style',
        helpers: {
            overlay: { closeClick: false }
        }
        }
    );
    $('.add_new_element').fancybox({
            'closeBtn' : false ,
            'beforeLoad': function(){
                $(this.element).addClass('pendding');
            },
            'padding': 0,
            'wrapCSS'    : 'fancybox_gcustom_style',
            helpers: {
                overlay: { closeClick: false }
            }
        }
    );
    $('.add_new_element_top').fancybox({
            'closeBtn' : false ,
            'beforeLoad': function(){
                $(this.element).addClass('pendding');
            },
            'padding': 0,
            'wrapCSS'    : 'fancybox_gcustom_style',
            helpers: {
                overlay: { closeClick: false }
            }
        }
    );
    $('.formbuilder_set_column_custom').fancybox({
            'closeBtn' : false ,
            'beforeLoad': function(){
                $(this.element).addClass('pendding');
                $('.set_column_custom_data').val('');
            },
            'padding': 0,
            'wrapCSS'    : 'fancybox_gcustom_style',
            helpers: {
                overlay: { closeClick: false }
            }
        }
    );


    $('.edit_control_column_top').fancybox({
            'closeBtn' : false ,
            'beforeLoad': function(){
                $('.edit_control_column_top').removeClass('column_pendding');
                $(this.element).addClass('column_pendding');
                var formbuilder_column = $(this.element).closest('.formbuilder_column');
                var column_class = formbuilder_column.attr('class');
                var id_element = formbuilder_column.attr('id');
                if(typeof id_element =="undefined") id_element = '';
                $('#edit_column_wp .element_id').val(id_element);
                var width = column_class.match(/col-md-(\d*)/)[1];
                var widthsm = column_class.match(/col-sm-(\d*)/)[1];
                var widthxs = column_class.match(/col-xs-(\d*)/)[1];
                if(width < 1 || width > 12 || width == '') width = 12;
                if(widthsm < 1 || widthsm > 12 || widthsm == '') widthsm = 12;
                if(widthxs < 1 || widthxs > 12 || widthxs == '') widthxs = 12;
                column_class = column_class.replace('col-md-'+width,'').replace('col-sm-'+widthsm,'').replace('col-xs-'+widthxs,'').replace('formbuilder_column','');
                $('#edit_column_wp .element_extra_class').val($.trim(column_class));
                $('#edit_column_wp').find('.formbuilder_column_width option').filter(function() {return this.value == width;}).attr('selected', 'selected').end();
                $('#edit_column_wp').find('.formbuilder_column_width_sm option').filter(function() {return this.value == widthsm;}).attr('selected', 'selected').end();
                $('#edit_column_wp').find('.formbuilder_column_width_xs option').filter(function() {return this.value == widthxs;}).attr('selected', 'selected').end();

            },
            'padding': 0,
            'wrapCSS'    : 'fancybox_gcustom_style',
            helpers: {
                overlay: { closeClick: false }
            }
        }
    );
    $(document).on('click', '#change_column_btn', function(){
        if($('.formbuilder_new_design .column_pendding').length > 0){
            var formbuilder_column = $('.column_pendding').closest('.formbuilder_column');
            formbuilder_column.attr('id',$('#edit_column_wp .element_id').val());
            formbuilder_column.removeAttr('class');
            formbuilder_column.addClass('formbuilder_column col-md-'+$('#edit_column_wp .formbuilder_column_width').val()+' col-sm-'+$('#edit_column_wp .formbuilder_column_width_sm').val()+' col-xs-'+$('#edit_column_wp .formbuilder_column_width_sm').val());
            var element_extra_class = $('#edit_column_wp .element_extra_class').val();
            if(element_extra_class !='')
                formbuilder_column.addClass(element_extra_class);
            $('.formbuilder_new_design .column_pendding').removeClass('column_pendding');
        }
        $.fancybox.close();
        return false;
    });
    $(document).on('click', '.formbuilder_duplicate', function(){
        var itemfield = $(this).parents('.itemfield');
        var ids = itemfield.attr("id").match(/gformbuilderpro_(\d*)/);
        if(ids.length > 1) {
            var id = ids[1];
            id = id.replace(/\s/g, '');
            if (id) {
                $(gformbuilderpro_overlay).appendTo('body');
                $.ajax({
                    url: $('#ajaxurl').val(),
                    type: 'POST',
                    dataType: "json",
                    data: 'duplicateField=true&id_gformbuilderprofield=' + id,
                })
                .done(function (data) {
                    $('#gformbuilderpro_overlay').remove();
                    if(data.error == '1'){
                        if (data.warrning == 'wholesale') {
                            showErrorMessage($('#gtext_cutom .gformtext_nonewholesale').val());
                        } else {
                            showErrorMessage(data.warrning);
                        }
                    }else{
                        itemfield_new = itemfield.clone();
                        itemfield_new.find('.ui-sortable').removeClass('ui-sortable');
                        itemfield_new.attr('id',data.new_id);
                        itemfield_new.find('.shortcode').html(data.new_shortcode);
                        itemfield_new.find('.feildname').html(data.field_name);
                        /* itemfield.closest('.itemfield_wp').append(itemfield_new); */
                        itemfield.after(itemfield_new);
                        var allfields = $('#fields').val().split(',');
                        allfields.push(data.id);
                        $('#fields').val(allfields.join(','));
                        refreshSortable();
                        if(typeof data.object != "undefined")
                            addFieldData(data.object);
                    }
                });
            }
        }
        return false;
    });
    $(document).on('click', '.formbuilder_duplicate_group', function(){
        var formbuilder_group = $(this).closest('.formbuilder_group');
        itemfields = formbuilder_group.find(".itemfield");
        var ids = [];
        allfields = $('#fields').val().split(',');
        if(itemfields) {
            itemfields.each(function () {
                var id = $(this).attr("id").match(/gformbuilderpro_(\d*)/)[1];
                if (id > 0) {
                    ids.push(id);
                }
            });
            $(gformbuilderpro_overlay).appendTo('body');
            $.ajax({
                url: $('#ajaxurl').val(),
                type: 'POST',
                dataType: "json",
                data: 'duplicateGroupField=true&id_gformbuilderprofields=' + ids.join('_'),
            })
            .done(function (data) {
                $('#gformbuilderpro_overlay').remove();
                if(data.error == '1'){
                    if (data.warrning == 'wholesale') {
                        showErrorMessage($('#gtext_cutom .gformtext_nonewholesale').val());
                    } else {
                        showErrorMessage(data.warrning);
                    }
                }else{
                    var formbuilder_group_new = formbuilder_group.clone();
                    formbuilder_group_new.find('.itemfield_wp.ui-sortable').removeClass('ui-sortable');
                    var allfields = $('#fields').val().split(',');
                    $.each(data.duplicatedatas,function (fieldid,filedval) {
                        var itemfield_old = formbuilder_group_new.find('#gformbuilderpro_'+filedval.id_old);
                        itemfield_old.attr('id',filedval.new_id);
                        itemfield_old.find('.shortcode').html(filedval.new_shortcode);
                        itemfield_old.find('.feildname').html(filedval.field_name);
                        allfields.push(data.id);
                        if(typeof filedval.object != "undefined")
                            addFieldData(filedval.object);
                    });
                    formbuilder_group.after(formbuilder_group_new);
                    $('#fields').val(allfields.join(','));
                    refreshSortable();
                }
            });
        }
        return false;
    });
    /* test*/
    $('.test_fancybox').fancybox({

        'closeBtn' : false ,
        'padding': 0,
        'wrapCSS'    : 'fancybox_gcustom_style',
        helpers: {
            overlay: { closeClick: false } 
        },
        'beforeShow': function(){
            var itemfield = $(this.element).closest('.itemfield');
            var ginput = itemfield.attr('data-type');
            var idatt = '';
            var classatt = '';
            var name = '';
            if(itemfield.attr('data-newitem') == 1){
                var rand_nbr = (Math.floor(Math.random()*100000)+1);
                idatt = ginput+'_'+rand_nbr;
                name = idatt;classatt = idatt;
            }
            var popup_field_config_hidden = $('#popup_field_config_hidden').clone();
            popup_field_config_hidden.find('form').addClass(ginput+'_field_popup');
            /* set data*/
            popup_field_config_hidden.find('#type').val(ginput);
            popup_field_config_hidden.find('#idatt').val(idatt);
            popup_field_config_hidden.find('#classatt').val(classatt);
            popup_field_config_hidden.find('#name').val(name);
            
            $.each(allfieldstype[ginput]['config'],function(key,val){
                var gfield_name = val.name;
                if(gfield_name == 'extra') gfield_name+='_'+val.type;
                if($('#popup_field_config_item .gfield_'+gfield_name).length > 0){
                    var gfield = $('#popup_field_config_item .gfield_'+gfield_name).clone();
                    if(typeof val.label != "undefined")
                        gfield.find('.control-label').html(val.label);
                    switch(gfield_name) {
                      case 'validate':
                      case 'extra_select':
                        $.each(val.options['query'],function(_key,_val){
                            gfield.find('select').append('<option value="'+_val.value+'">'+_val.name+'</option>');
                        });
                        break;
                      default:
                        // code block
                    }
                    popup_field_config_hidden.find('.popup_field_content_box').append(gfield);
                }
            });
            if(popup_field_config_hidden.find('.need_change_id').length > 0){
                popup_field_config_hidden.find('.need_change_id').each(function(){
                    $(this).attr('id',$(this).attr('data-id'));
                });
            }
            $('#popup_field_wp').html('').append(popup_field_config_hidden);
            
            if($('#popup_field_wp .tagify').length > 0){
                $('#popup_field_wp .tagify').each(function(){
                    var addTagPrompt = $(this).attr('data-addTagPrompt');
                    if(addTagPrompt == '' || addTagPrompt == "undefined") addTagPrompt = 'Add tag';
                   $(this).tagify({delimiters: [13,44], addTagPrompt: addTagPrompt});
                });
            }
            if($('#popup_field_wp .gcolor').length > 0){
                $('#popup_field_wp .gcolor').each(function(){
                   $(this).attr('type','color').addClass('mColorPicker').mColorPicker();  
                });
            }
                
        }
    });


    $("#popup_field_config_link").fancybox({
        'closeBtn' : false ,
        'padding': 0,
        'wrapCSS'    : 'fancybox_gcustom_style',
        helpers: {
            overlay: { closeClick: false } 
        },
        'beforeShow': function(){
            beforeShow_call = 1;
            if($('#popup_field_config .textareatiny').length > 0){
                default_config = {
            		selector: ".textareatiny" ,
            		plugins : "colorpicker link image paste pagebreak table contextmenu filemanager table code media autoresize textcolor anchor",
            		browser_spellcheck : true,
            		toolbar1 : "code,|,bold,italic,underline,strikethrough,|,alignleft,aligncenter,alignright,alignfull,formatselect,|,blockquote,colorpicker,pasteword,|,bullist,numlist,|,outdent,indent,|,link,unlink,|,anchor,|,media,image",
            		toolbar2: "",
            		external_filemanager_path: ad+"/filemanager/",
            		filemanager_title: "File manager" ,
            		external_plugins: { "filemanager" : ad+"/filemanager/plugin.min.js"},
            		language: iso,
            		skin: "prestashop",
            		statusbar: false,
            		relative_urls : false,
            		convert_urls: false,
            		entity_encoding: "raw",
            		extended_valid_elements : "em[class|name|id]",
            		valid_children : "+*[*]",
            		valid_elements:"*[*]",
            		menu: {
            			edit: {title: 'Edit', items: 'undo redo | cut copy paste | selectall'},
            			insert: {title: 'Insert', items: 'media image link | pagebreak'},
            			view: {title: 'View', items: 'visualaid'},
            			format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
            			table: {title: 'Table', items: 'inserttable tableprops deletetable | cell row column'},
            			tools: {title: 'Tools', items: 'code'}
            		}
            	};
    	       tinyMCE.init(default_config);
           }
        if($('#popup_field_config .mColorPickerInput').length > 0)   
            $('.mColorPickerInput').mColorPicker();  
            
        if($('#popup_field_config #curPackItemName').length > 0) 
        if($('#loadjqueryselect2').val() == '1'){   
            $('#curPackItemName').select2({
        			placeholder: search_product_msg,
        			minimumInputLength: 2,
        			width: '100%',
        			dropdownCssClass: "bootstrap",
        			ajax: {
        				url:$('#ajaxaction').val(),
        				dataType: 'json',
        				data: function (term) {
        					return {
        						q: term,
                                gformgetproduct: true
        					};
        				},
        				results: function (data) {
        					var excludeIds = getSelectedIds();
        					var returnIds = new Array();
        					if (data) {
        						for (var i = data.length - 1; i >= 0; i--) {
        							var is_in = 0;
        							for (var j = 0; j < excludeIds.length; j ++) {
        								if (data[i].id == excludeIds[j][0] && (typeof data[i].id_product_attribute == 'undefined' || data[i].id_product_attribute == excludeIds[j][1]))
        									is_in = 1;
        							}
        							if (!is_in)
        								returnIds.push(data[i]);
        						}
        						return {
        							results: returnIds
        						}
        					} else {
        						return {
        							results: []
        						}
        					}
        				}
        			},
        			formatResult: productFormatResult,
        			formatSelection: productFormatSelection,
        		})
        		.on("select2-selecting", function(e) {
        			selectedProduct = e.object
        		});
                $('.product_field_popup').find('#add_pack_item').on('click', addPackItem);
          }else{
                    $('#curPackItemName').autocomplete('ajax_products_list.php', {
                		delay: 100,
                		minChars: 1,
                		autoFill: true,
                		max:20,
                		matchContains: true,
                		mustMatch:true,
                		scroll:false,
                		cacheLength:0,
                		multipleSeparator:'||',
                		formatItem: function(item) {
                			return item[1]+' - '+item[0];
                		},
                		extraParams: {
                			excludeIds : getSelectedIds(),
                			excludeVirtuals : 1,
                			exclude_packs: 1
                		}
                	}).result(function(event, item){
                		$('#curPackItemId').val(item[1]);
                	});
                    $('.product_field_popup').find('#add_pack_item').on('click', addPackItem);
                }
            },
            'onComplete': function(){
            if(beforeShow_call !=1){
                
            beforeShow_call = 0;
            if($('#popup_field_config .textareatiny').length > 0){
                default_config = {
            		selector: ".textareatiny" ,
            		plugins : "colorpicker link image paste pagebreak table contextmenu filemanager table code media autoresize textcolor anchor",
            		browser_spellcheck : true,
            		toolbar1 : "code,|,bold,italic,underline,strikethrough,|,alignleft,aligncenter,alignright,alignfull,formatselect,|,blockquote,colorpicker,pasteword,|,bullist,numlist,|,outdent,indent,|,link,unlink,|,anchor,|,media,image",
            		toolbar2: "",
            		external_filemanager_path: ad+"/filemanager/",
            		filemanager_title: "File manager" ,
            		external_plugins: { "filemanager" : ad+"/filemanager/plugin.min.js"},
            		language: iso,
            		skin: "prestashop",
            		statusbar: false,
            		relative_urls : false,
            		convert_urls: false,
            		entity_encoding: "raw",
            		extended_valid_elements : "em[class|name|id]",
            		valid_children : "+*[*]",
            		valid_elements:"*[*]",
            		menu: {
            			edit: {title: 'Edit', items: 'undo redo | cut copy paste | selectall'},
            			insert: {title: 'Insert', items: 'media image link | pagebreak'},
            			view: {title: 'View', items: 'visualaid'},
            			format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
            			table: {title: 'Table', items: 'inserttable tableprops deletetable | cell row column'},
            			tools: {title: 'Tools', items: 'code'}
            		}
            	};
    	       tinyMCE.init(default_config);
           }
        if($('#popup_field_config .mColorPickerInput').length > 0)
            $('.mColorPickerInput').mColorPicker();  
        if($('#popup_field_config #curPackItemName').length > 0)
        if($('#loadjqueryselect2').val() == '1'){   
            $('#curPackItemName').select2({
        			placeholder: search_product_msg,
        			minimumInputLength: 2,
        			width: '100%',
        			dropdownCssClass: "bootstrap",
        			ajax: {
        				url:$('#ajaxaction').val(),
        				dataType: 'json',
        				data: function (term) {
        					return {
        						q: term,
                                gformgetproduct: true
        					};
        				},
        				results: function (data) {
        					var excludeIds = getSelectedIds();
        					var returnIds = new Array();
        					if (data) {
        						for (var i = data.length - 1; i >= 0; i--) {
        							var is_in = 0;
        							for (var j = 0; j < excludeIds.length; j ++) {
        								if (data[i].id == excludeIds[j][0] && (typeof data[i].id_product_attribute == 'undefined' || data[i].id_product_attribute == excludeIds[j][1]))
        									is_in = 1;
        							}
        							if (!is_in)
        								returnIds.push(data[i]);
        						}
        						return {
        							results: returnIds
        						}
        					} else {
        						return {
        							results: []
        						}
        					}
        				}
        			},
        			formatResult: productFormatResult,
        			formatSelection: productFormatSelection,
        		})
        		.on("select2-selecting", function(e) {
        			selectedProduct = e.object
        		});
                $('.product_field_popup').find('#add_pack_item').on('click', addPackItem);
          }else{
                    $('#curPackItemName').autocomplete('ajax_products_list.php', {
                		delay: 100,
                		minChars: 1,
                		autoFill: true,
                		max:20,
                		matchContains: true,
                		mustMatch:true,
                		scroll:false,
                		cacheLength:0,
                		multipleSeparator:'||',
                		formatItem: function(item) {
                			return item[1]+' - '+item[0];
                		},
                		extraParams: {
                			excludeIds : getSelectedIds(),
                			excludeVirtuals : 1,
                			exclude_packs: 1
                		}
                	}).result(function(event, item){
                		$('#curPackItemId').val(item[1]);
                	});
                    $('.product_field_popup').find('#add_pack_item').on('click', addPackItem);
                }
            }
          }
    });



    $(document).on('click', '.cancel_column_btn', function(){
        $.fancybox.close();
        if($('.pendding').length > 0)
            $('.pendding').removeClass('pendding');
        return false;
    });

    $(document).on('click', '#add_thumb_item_fromlist', function(){
        var popupform = $(this).parents('#popup_field_config_hidden');
        formURL = $(this).parents('form').attr('action');
        $.ajax({
            url: formURL+'&getThumb=true',
            type: 'POST',
            success: function(thumbs) {
                if(thumbs !=''){
                    divThumbItems = '';
                    thumbsdata = thumbs.split(',');
                    $.each(thumbsdata, function( index, value ) {
                        divThumbItems += '<div class="gthumb_item">';
                        divThumbItems += '<img src="'+popupform.find('#thumb_url').val()+value+'" alt="">';
                        divThumbItems += '<input type="checkbox" name="item_fromlist[]" value="'+value+'" class="item_fromlist" />';
                        divThumbItems += '</div>';
                    });
                    popupform.find('#thumbs_fromlist').html(divThumbItems);
                }
            }
        });
        return false;
    });
    $(document).on('click', '#add_thumb_item', function(){
        var popupform = $(this).parents('#popup_field_config_hidden');
        var thumbs = popupform.find('#thumbchoose').val();
        thumbsdata = [];
        if(thumbs !=''){
            console.debug(thumbs);
            thumbsdata = thumbs.split(',');
        }
        if(popupform.find('.item_fromlist').length > 0){
            popupform.find('.item_fromlist').each(function(){
                if(this.checked){
                    
                    item_fromlist_val = $(this).val();
                    thumbsdata = $.grep(thumbsdata, function(val) {
                        return item_fromlist_val != val;
                    });
                    thumbsdata.push(item_fromlist_val);
                }
            })
        }
        var filedata = popupform.find("#imagethumbupload").prop("files");
        formURL = $(this).parents('form').attr('action');
        if (window.FormData !== undefined) {
            var formData = new FormData();
            len = filedata.length;
            if(len > 0){
                for (var i = 0; i < len; i++) {
                        formData.append("file[]", filedata[i]);
                }
                $.ajax({
                    url: formURL+'&addThumb=true',
                    type: 'POST',
                    data: formData,
                    mimeType: "multipart/form-data",
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data, textStatus, jqXHR) {
                        if(data !=''){
                            datas = data.split(',');
                            
                            $.each(datas, function( index, value ) {
                                thumbsdata = $.grep(thumbsdata, function(val) {
                                  return value != val;
                                });
                                thumbsdata.push(value);
                            });
                            _allfields = thumbsdata.join(',');
                            if(_allfields.charAt(0) == ',')
                                popupform.find('#thumbchoose').val(_allfields.slice(1));
                            else
                                popupform.find('#thumbchoose').val(_allfields);
                            divThumbItems = '';
                            $.each(thumbsdata, function( index, value ) {
                                divThumbItems += '<div class="gthumb_item">';
                                divThumbItems += '<img src="'+popupform.find('#thumb_url').val()+value+'" alt="">';
                                divThumbItems += '<button type="button" class="btn btn-default delThumbItem" data-delete="'+value+'"><span><i class="icon-trash"></i></button>';
                                divThumbItems += '</div>';
                            });
                            popupform.find('#divThumbItems').html(divThumbItems);
                            popupform.find("#imagethumbupload").val('');
                            popupform.find('#thumbs_fromlist').html('');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        var err = eval("(" + jqXHR.responseText + ")");
                        alert(err.Message);
                        
                    }
                });
            }else{
                _allfields = thumbsdata.join(',');
                if(_allfields.charAt(0) == ',')
                    popupform.find('#thumbchoose').val(_allfields.slice(1));
                else
                    popupform.find('#thumbchoose').val(_allfields);
                divThumbItems = '';
                $.each(thumbsdata, function( index, value ) {
                    divThumbItems += '<div class="gthumb_item">';
                    divThumbItems += '<img src="'+popupform.find('#thumb_url').val()+value+'" alt="">';
                    divThumbItems += '<button type="button" class="btn btn-default delThumbItem" data-delete="'+value+'"><span><i class="icon-trash"></i></button>';
                    divThumbItems += '</div>';
                });
                popupform.find('#divThumbItems').html(divThumbItems);
                popupform.find("#imagethumbupload").val('');
                popupform.find('#thumbs_fromlist').html('');
            }
        }
        return false;
    });
    $(document).on('click', '.delThumbItem', function(){
        data = $(this).data('delete');
        var popupform = $(this).parents('#popup_field_config_hidden');
        thumbs = popupform.find('#thumbchoose').val();
        thumbsdata = [];
        if(thumbs !=''){
            thumbsdata = thumbs.split(',');
        }
        thumbsdata = $.grep(thumbsdata, function(val) {
          return data != val;
        });
        _allfields = thumbsdata.join(',');
        if(_allfields.charAt(0) == ',')
            popupform.find('#thumbchoose').val(_allfields.slice(1));
        else
            popupform.find('#thumbchoose').val(_allfields);
        divThumbItems = '';
        $.each(thumbsdata, function( index, value ) {
            divThumbItems += '<div class="gthumb_item">';
            divThumbItems += '<img src="'+popupform.find('#thumb_url').val()+value+'" alt="">';
            divThumbItems += '<button type="button" class="btn btn-default delThumbItem" data-delete="'+value+'"><span><i class="icon-trash"></i></button>';
            divThumbItems += '</div>';
        });
        popupform.find('#divThumbItems').html(divThumbItems);
        return false;
    });
    $(document).on('click', '.gfield_deletelistoption', function(){
        var id = $(this).data('idoption');
        $('#option_condition_'+id).remove();
    });
    $(document).on('change', '.gformcondition_listoption_idfield', function(){
        var vlue = $(this).val();
        if ($('#gfield_data_'+vlue).find('.type').val() == 'fileupload') {
            var name_old = $(this).closest('.option_condition').find('.col-lg-3 input').attr('name');
            $(this).closest('.option_condition').find('.gformcondition_listoption_condition').hide();
            $(this).closest('.option_condition').find('.col-lg-3').html($('#file_condition_option select').clone().attr('name', name_old));
        } else {
            if ($(this).closest('.option_condition').find('.col-lg-3 select').length > 0) {
                var name_old = $(this).closest('.option_condition').find('.col-lg-3 select').attr('name');
                $(this).closest('.option_condition').find('.gformcondition_listoption_condition').show();
                $(this).closest('.option_condition').find('.col-lg-3').html('<input type="text" class="gformcondition_listoption_conditionvalue" name="'+name_old+'" value=""/>');
            }
        }
    });
    $(document).on('click', '.gfield_addlistoption', function(){
        var default_option = $('#listoption_datas .option_condition').clone();
        var id_old = $(this).data('number');
        var html ='<option value="0"> '+$('#gtext_cutom .gformtext_noneoption').val()+' </option>';
        var id = $('#popup_field_config #id_gformbuilderprofields').val();
        default_option.attr('id', 'option_condition_'+id_old);
        default_option.find('.gformcondition_listoption_idfield').attr('name', 'listoptions['+id_old+'][id_field]');
        if ($('.itemfield_wp .itemfield').length > 0) {
            $('.itemfield_wp .itemfield').each(function(){
                if ($(this).attr('id') == 'newfield') 
                    var id_listfield = 0
                else 
                    var id_listfield = $(this).attr('id').match(/gformbuilderpro_(\d*)/)[1];

                if ($('#gfield_datas #gfield_data_'+id_listfield).length > 0 && id_listfield != id) {
                    var id_extra_filed_selectoption = $('#gfield_datas #gfield_data_'+id_listfield);
                    if (id_extra_filed_selectoption.find('input.type').val() != 'captcha' 
                    && id_extra_filed_selectoption.find('input.type').val() != 'googlemap' 
                    && id_extra_filed_selectoption.find('input.type').val() != 'html' 
                    && id_extra_filed_selectoption.find('input.type').val() != 'hidden' 
                    && id_extra_filed_selectoption.find('input.type').val() != 'wholesale' 
                    && id_extra_filed_selectoption.find('input.type').val() != 'submit') {
                        if (id_extra_filed_selectoption.attr('id') != 'gfield_data_'+id_old) {
                            check_datafields = true;
                            html +='<option value="'+id_extra_filed_selectoption.find('input.id_gformbuilderprofields').val()+'">'+id_extra_filed_selectoption.find('input.name').val()+'</option>';
                        }
                    }
                } else {
                    return false;
                }
            });
        }
        default_option.find('.gformcondition_listoption_idfield').html(html);
        default_option.find('.gformcondition_listoption_condition').attr('name', 'listoptions['+id_old+'][condition]');
        default_option.find('.gformcondition_listoption_conditionvalue').attr('name', 'listoptions['+id_old+'][conditionvalue]');
        default_option.find('.gfield_deletelistoption').attr('data-idoption', id_old);
        if (default_option.find('.gformcondition_listoption_idfield').val() > 0) {
            if ($('#gfield_data_'+default_option.find('.gformcondition_listoption_idfield').val()).find('.type').val() == 'fileupload') {
                var name_old = default_option.find('.col-lg-3 input').attr('name');
                default_option.find('.gformcondition_listoption_condition').hide();
                default_option.find('.col-lg-3').html($('#file_condition_option select').clone().attr('name', name_old));
            } 
        }
        $('.popup_field_content').find('.gformcondition_listoption').append(default_option);
        $(this).data('number', parseInt(id_old) + 1);
    });
    $(document).on('click', '#add_color_item', function(){
        var popupform = $(this).parents('#popup_field_config_hidden');
        data = popupform.find('.mColorPickerinput').val();
        if(data !='' && /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(data)){
            colors = $('#colorchoose').val();
            colorsdata = [];
            if(colors !=''){
                colorsdata = colors.split(',');
            }
            colorsdata = $.grep(colorsdata, function(value) {
              return value != data;
            });
            colorsdata.push(data);
            _allfields = colorsdata.join(',');
            if(_allfields.charAt(0) == ',')
                popupform.find('#colorchoose').val(_allfields.slice(1));
            else
                popupform.find('#colorchoose').val(_allfields);
            divColorItems = '';
            
            $.each(colorsdata, function( index, value ) {
                divColorItems += '<div style="background-color: '+value+';" class="color_item">';
                divColorItems += '<button type="button" class="btn btn-default delColorItem" data-delete="'+value+'"><span><i class="icon-trash"></i> '+value+'</button>';
                divColorItems += '</div>';
            });
            popupform.find('#divColorItems').html(divColorItems);
        }
        return false;
    });
    $(document).on('click', '.delColorItem', function(){
        var popupform = $(this).parents('#popup_field_config_hidden');
            data = $(this).data('delete');
            colors = popupform.find('#colorchoose').val();
            colorsdata = [];
            if(colors !=''){
                colorsdata = colors.split(',');
            }
            colorsdata = $.grep(colorsdata, function(value) {
              return value != data;
            });
            _allfields = colorsdata.join(',');
            if(_allfields.charAt(0) == ',')
                popupform.find('#colorchoose').val(_allfields.slice(1));
            else
                popupform.find('#colorchoose').val(_allfields);
            divColorItems = '';
            
            $.each(colorsdata, function( index, value ) {
                divColorItems += '<div style="background-color: '+value+';" class="color_item">';
                divColorItems += '<button type="button" class="btn btn-default delColorItem" data-delete="'+value+'"><span><i class="icon-trash"></i> '+value+'</button>';
                divColorItems += '</div>';
            });
            popupform.find('#divColorItems').html(divColorItems);
            return false;
    });
    
    $('#formbuilder .itemfield').each(function(){
        /*
        width = $(this).attr("class").match(/col-md-(\d*)/)[1];
        widthsm = $(this).attr("class").match(/col-sm-(\d*)/)[1];
        widthxs = $(this).attr("class").match(/col-xs-(\d*)/)[1];
        if(width < 1 || width > 12 || width == '') width = 12;
        if(widthsm < 1 || widthsm > 12 || widthsm == '') widthsm = 12;
        if(widthxs < 1 || widthxs > 12 || widthxs == '') widthxs = 12;

         */

        width = 12;widthsm = 12;widthxs = 12;
        addControlBt($(this),width,widthsm,widthxs);
    });
    $('#formbuilder .formbuilder_group').each(function(){
        /*
        width = $(this).attr("class").match(/col-md-(\d*)/)[1];
        widthsm = $(this).attr("class").match(/col-sm-(\d*)/)[1];
        widthxs = $(this).attr("class").match(/col-xs-(\d*)/)[1];
        if(width < 1 || width > 12 || width == '') width = 12;
        if(widthsm < 1 || widthsm > 12 || widthsm == '') widthsm = 12;
        if(widthxs < 1 || widthxs > 12 || widthxs == '') widthxs = 12;

         */
        width = 12;widthsm = 12;widthxs = 12;
        addControlBt($(this),width,widthsm,widthxs);
    });
    if($("#formbuilder").length > 0)
    $("#formbuilder").sortable({
        opacity:0.5,
        cursor:'move',
        handle: '.formbuilder_move',
        update:function() {
            clearBeforeSave();
        },
    });
    if($(".formbuilder_group").length > 0)
        $(".formbuilder_group").sortable({
            opacity:0.5,
            cursor:'move',
            handle: '.formbuilder_column_move',
            update:function() {
                clearBeforeSave();
            },
        });
    if($(".itemfield_wp").length > 0)
    $(".itemfield_wp").sortable({
        connectWith: '.itemfield_wp',
        handle: '.formbuilder_move',
        opacity:0.5,
        cursor:'move',
        update: function(event, ui) {
            clearBeforeSave();
        },
        beforeStop: function (event, ui) { 
          newItem = ui.item;
        },
        receive: function(event,ui) {
            type = newItem.data('type');
            newitem = ui.item.data('newitem');
            if(newitem=='1'){
                /*
                width = newItem.attr("class").match(/col-md-(\d*)/)[1];
                widthsm = newItem.attr("class").match(/col-sm-(\d*)/)[1];
                widthxs = newItem.attr("class").match(/col-xs-(\d*)/)[1];
                if(width < 1 || width > 12 || width == '') width = 12;
                if(widthsm < 1 || widthsm > 12 || widthsm == '') widthsm = 12;
                if(widthxs < 1 || widthxs > 12 || widthxs == '') widthxs = 12;
                */
                width = 12;widthsm = 12;widthxs = 12;
                addControlBt(newItem,width,widthsm,widthxs);
                newItem.removeAttr('data-newitem');
                $(gformbuilderpro_overlay).appendTo('body');
                $.ajax({
                      url: $('#ajaxurl').val(),
                      type : 'POST',                      
                      data: 'typefield='+type,
                })
                .done(function(data) {
                    $('#gformbuilderpro_overlay').remove();
                    if(data !=''){
                        $("#popup_field_config #content").html(data);
                        $("#popup_field_config_link").click();
                        newItem.attr('id','newfield'); 
                    }else{
                        newItem.remove();
                    }
                });
            }
        }
    });
    
    function setPopupData(isnew,ginput,id){
        /* popup */
        var popup_field_config_hidden = $('#popup_field_config_hidden').clone();
        popup_field_config_hidden.find("input[type=text], textarea").val("");
        popup_field_config_hidden.find('.alert-danger').remove();
        popup_field_config_hidden.find('#popup_field_config_wp').removeAttr('class').addClass(ginput+'_field_popup');
        popup_field_config_hidden.find('.popup_field_content_box').html('');
        popup_field_config_hidden.find('.popup_field_content .gfield_condition').remove();
        popup_field_config_hidden.find('.popup_field_content .gfield_condition_display').remove();
        /* set data*/
        popup_field_config_hidden.find('#type').val(ginput);
        popup_field_config_hidden.find('#id_gformbuilderprofields').val('');
        if(isnew){
            var rand_nbr = (Math.floor(Math.random()*100000)+1);
            idatt = ginput+'_'+rand_nbr;
            name = idatt;classatt = idatt;
            popup_field_config_hidden.find('#idatt').val(idatt);
            popup_field_config_hidden.find('#classatt').val(classatt);
            popup_field_config_hidden.find('#name').val(name);
            $.each(languages,function(key,val){
                popup_field_config_hidden.find('input[name="label_'+val.id_lang+'"]').val(allfieldstype[ginput]['label']);
            });
        }else{
            var field = $('#gfield_data_'+id);
            popup_field_config_hidden.find('#id_gformbuilderprofields').val(id);
            popup_field_config_hidden.find('#idatt').val(field.find('.idatt').val());
            popup_field_config_hidden.find('#classatt').val(field.find('.classatt').val());
            popup_field_config_hidden.find('#name').val(field.find('.name').val());
            $.each(languages,function(key,val){
                if(field.find('.label_'+val.id_lang).length > 0)
                    popup_field_config_hidden.find('input[name="label_'+val.id_lang+'"]').val(field.find('.label_'+val.id_lang).val());
                else
                    popup_field_config_hidden.find('input[name="label_'+val.id_lang+'"]').val('');
            });
            
            
        }
        $.each(allfieldstype[ginput]['config'],function(key,val){
            var gfield_name = val.name;
            if(gfield_name == 'extra' || gfield_name == 'description' || gfield_name== 'value') gfield_name+='_'+val.type;
            if($('#popup_field_config_item .gfield_'+gfield_name).length > 0 ){
                var gfield = $('#popup_field_config_item .gfield_'+gfield_name).clone();
                if(typeof val.label != "undefined")
                    gfield.find('.control-label').html(val.label);
                switch(gfield_name) {
                  case 'placeholder':
                    if(!isnew){
                        $.each(languages,function(key,val){
                            if(field.find('.placeholder_'+val.id_lang).length > 0)
                                gfield.find('input[name="placeholder_'+val.id_lang+'"]').val(field.find('.placeholder_'+val.id_lang).val());
                            else
                                gfield.find('input[name="placeholder_'+val.id_lang+'"]').val('');
                        });
                    }
                    break;
                  case 'validate':
                    var selected = '';
                    if(!isnew) selected = field.find('.validate').val();
                    $.each(val.options['query'],function(_key,_val){
                        gfield.find('select').append('<option value="'+_val.value+'" '+(_val.value == selected ? ' selected="selected" ' : '')+'>'+_val.name+'</option>');
                    });
                    break;
                   case 'dynamicval':
                    var selected = '';
                    if(!isnew) selected = field.find('.dynamicval').val();
                    $.each(val.options['query'],function(_key,_val){
                        gfield.find('select').append('<option value="'+_val.value+'" '+(_val.value == selected ? ' selected="selected" ' : '')+'>'+_val.name+'</option>');
                    });
                    break;
                  case 'labelpos':
                    var selected = '';
                    if(!isnew) selected = field.find('.labelpos').val();
                    $.each(val.options['query'],function(_key,_val){
                        gfield.find('select').append('<option value="'+_val.value+'" '+(_val.value == selected ? ' selected="selected" ' : '')+'>'+_val.name+'</option>');
                    });
                    break;
                  case 'description_textarea':
                    if(!isnew){
                        $.each(languages,function(_key,_val){
                            if(field.find('.description_'+_val.id_lang).length > 0){
                                gfield.find('.description_'+_val.id_lang).val(field.find('.description_'+_val.id_lang).val());
                            }
                                
                        });
                    }
                    if(typeof val.class != "undefined"){
                        gfield.find('textarea').addClass(val.class);
                        if(gfield.find('textarea').hasClass('textareatiny')){
                            gfield.addClass('enable_shortcode_txt');
                        }
                    }
                    break;
                  case  'description_multival':
                    if(!isnew)
                        $.each(languages,function(key,val){
                            if(field.find('.description_'+val.id_lang).length > 0)
                                gfield.find('textarea[name="description_'+val.id_lang+'"]').val(field.find('.description_'+val.id_lang).val());
                        });
                    break;
                  case 'extra_switch':
                        if(!isnew){
                            if(field.find('.extra').length > 0){
                                if(field.find('.extra').val() == '1'){
                                    gfield.find('input[value="0"]').removeAttr('checked');
                                    gfield.find('input[value="1"]').prop("checked", true);
                                }else{
                                    gfield.find('input[value="1"]').removeAttr('checked');
                                    gfield.find('input[value="0"]').prop("checked", true);
                                }
                            }
                        }
                    break;
                  case 'extra_select':
                    var selected = '';
                    if(!isnew) selected = field.find('.extra').val();
                    $.each(val.options['query'],function(_key,_val){
                        gfield.find('select').append('<option value="'+_val.value+'" '+(_val.value == selected ? ' selected="selected" ' : '')+'>'+_val.name+'</option>');
                    });
                    break;
                  case 'extra_color':
                  case 'extra_colorchoose':
                    if(!isnew)
                        if(field.find('.extra').length > 0)
                            gfield.find('input[name="extra"]').val(field.find('.extra').val());
                    break;
                  case 'extra_slidervalue':
                    if(!isnew)
                        if(field.find('.extra').length > 0){
                            var slidervalue = field.find('.extra').val();
                            gfield.find('input[name="extra"]').val(slidervalue);
                            if(slidervalue !=''){
                                slidervalues = slidervalue.split(';');
                                if(typeof slidervalues[0] != "undefined")
                                    gfield.find('input[name="minval"]').val(slidervalues[0]);
                                if(typeof slidervalues[1] != "undefined")
                                    gfield.find('input[name="maxval"]').val(slidervalues[1]);
                                if(typeof slidervalues[2] != "undefined")
                                    gfield.find('input[name="rangeval"]').val(slidervalues[2]);
                                if(typeof slidervalues[4] != "undefined")
                                    gfield.find('input[name="defaultval"]').val(slidervalues[3]+';'+slidervalues[4]);
                                else if(typeof slidervalues[3] != "undefined")
                                    gfield.find('input[name="defaultval"]').val(slidervalues[3]);
                            }
                        } 
                    break;
                  case 'extra_extraproducts':
                        if(!isnew){
                            if(field.find('.extra').length > 0){
                                var wholesales = field.find('.extra').val();
                                wholesales = JSON.parse(wholesales);
                                let products = new Array();
                                var combins  = new Array();
                                var gform_products_vouchers  = new Array();
                                $.each(wholesales,function(key,val){
                                    products.push(key);
                                    combins[key] = val.gform_attribute;
                                    gform_products_vouchers[key] = val.gform_products_voucher;
                                });
                                var url = currentIndex + "&token=" + token + '&getProductwhosaleConfig';
                                $.ajax({
                                    type: "POST",
                                    url: url,
                                    data: "&products=" + products.join(',') +"&combins="+ JSON.stringify(combins),
                                    dataType: 'json',
                                    async: true,
                                    success: function(datas) {
                                        if (datas) { 
                                            const products_shows = datas;
                                            if (products_shows.length > 0) {
                                                for (const [key, value] of Object.entries(products_shows)) {
                                                    var product_whosale = '';
                                                    product_whosale += '<div class="configproduct-row" id="configproduct_' + value.id + '" data-id="' + value.id + '">';
                                                    product_whosale += '<div class="configproduct-show-row-info">';
                                                    product_whosale += '<input type="text" class="hidden" value="" id="option_voucher_' + value.id + '" checked="checked" name="gform_products_voucher[' + value.id + ']">';
                                                    product_whosale += '<input type="checkbox" class="configproduct-show-checkbox-input hidden" value="' + value.id + '" id="checkbox_option_shows_' + value.id + '" checked="checked" name="gform_idproducts[]">';
                                                    product_whosale += '<div class="title"><div class="configproduct-show-ui-item productbox-show-img">';
                                                    product_whosale += '<span class="configproduct-show-pro-img" style="background-image: url(' + value.image + ');"></span></div>';
                                                    product_whosale += '<div class="configproduct-show-ui-item configproduct-show-row-title configproduct-show-ui-item-fill"><label class="control-label">' + value.name + '</label>';
                                                    product_whosale += '</div>';
                                                    product_whosale += '<div class="action_productwhosale"><a  class="btn btn-default whosale_voucher_optionproduct" data-id="' + value.id + '"><i class="icon-AdminPriceRule"></i></a><a type="button" class="pull-right btn btn-default gbtn-default-red whosale_remove_optionproduct" data-id="' + value.id + '" data-type=""><i class="icon-trash"></i></a><a type="button" class="pull-right btn btn-default gbtn-default whosale_edit_optionproduct" data-id="' + value.id + '" data-type=""><i class="icon-pencil"></i></a></div>';
                                                    product_whosale += '</div></div>';
                                                    product_whosale += '<div class="configproduct_combination_show_search">' + value.conbination + '</div>';
                                                    product_whosale += '</div>';
                                                    if ($('#configproduct_' + value.id).length > 0) {
                                                        $('.wholesale_field_popup .gfield_extra_extraproducts .wholesale-product').find('#configproduct_' + value.id).closest('li').html(product_whosale);
                                                    } else {
                                                        $('.wholesale_field_popup .gfield_extra_extraproducts .wholesale-product').append('<li>'+product_whosale+'</li>');
                                                    }
                                                    if ($('#configproduct_' + value.id).find('#option_voucher_' + value.id).length > 0) {
                                                        $('#configproduct_' + value.id).find('#option_voucher_' + value.id).val(JSON.stringify(gform_products_vouchers[value.id]));
                                                    }
                                                }
                                                $("#popup_field_config #add_products_item_fromlist .gformload_oldpro").show();
                                                $("#popup_field_config #add_products_item_fromlist .gformonload_pro").hide();
                                                $("#popup_field_config #add_products_item_fromlist").removeAttr("disabled");
                                            }
                                        }
                                    },
                                });
                            }
                            setTimeout(function() {
                                $("#popup_field_config #add_products_item_fromlist .gformload_oldpro").show();
                                $("#popup_field_config #add_products_item_fromlist .gformonload_pro").hide();
                                $("#popup_field_config #add_products_item_fromlist").removeAttr("disabled");
                            }, 2000);
                        } else {
                            setTimeout(function() {
                                $("#popup_field_config #add_products_item_fromlist .gformload_oldpro").show();
                                $("#popup_field_config #add_products_item_fromlist .gformonload_pro").hide();
                                $("#popup_field_config #add_products_item_fromlist").removeAttr("disabled");
                            }, 2000);
                        }
                    break;
                  case 'extra_tags':
                    if(isnew){
                        if(ginput == 'fileupload')
                            gfield.find('input[name="extra"]').val('jpg,jpeg,gif,png,doc,docx,xls,xlsx');
                    }
                    else{
                        if(field.find('.extra').length > 0)
                            gfield.find('input[name="extra"]').val(field.find('.extra').val());
                    }
                    break;
                  case 'extra_imagethumb':
                    if(!isnew){
                        gfield.find('#divThumbItems').html('');
                        if(field.find('.extra').length > 0){
                            var thumb = field.find('.extra').val();
                            if(thumb !=''){
                                gfield.find('textarea[name="extra"]').val(thumb);
                                var thumbs = thumb.split(',');
                                $.each(thumbs,function(key,val){
                                    var thumb_box = '<div class="gthumb_item">';
                                    thumb_box += '<img src="'+gfield.find('#thumb_url').val()+val+'" alt="" />';
                                    thumb_box += '<button type="button" class="btn btn-default delThumbItem" data-delete="'+val+'"><span><i class="icon-trash"></i></button>';
                                    thumb_box += '</div>';
                                    gfield.find('#divThumbItems').append(thumb_box);
                                });
                            }
                        }
                            
                    }
                    break;
                  case 'value_text':
                    if(typeof val.class != "undefined")
                        gfield.find('input[type="text"]').addClass(val.class);
                    if(!isnew)
                        $.each(languages,function(key,val){
                            if(field.find('.value_'+val.id_lang).length > 0)
                                gfield.find('input[name="value_'+val.id_lang+'"]').val(field.find('.value_'+val.id_lang).val());
                        });
                    break;
                  case  'value_multival':
                    if(!isnew)
                        $.each(languages,function(key,val){
                            if(field.find('.value_'+val.id_lang).length > 0)
                                gfield.find('textarea[name="value_'+val.id_lang+'"]').val(field.find('.value_'+val.id_lang).val());
                        });
                    break;
                  case 'required':
                        if(!isnew){
                            if(field.find('.required').length > 0){
                                if(field.find('.required').val() == '1' || field.find('.required').val() == 'true'){
                                    gfield.find('input[value="0"]').removeAttr('checked');
                                    gfield.find('input[value="1"]').prop("checked", true);
                                }else{
                                    gfield.find('input[value="1"]').removeAttr('checked');
                                    gfield.find('input[value="0"]').prop("checked", true);
                                }
                            }
                        }
                    break;
                  case  'multi':
                        if(!isnew){
                            if(field.find('.multi').length > 0){
                                if(field.find('.multi').val() == '1' || field.find('.multi').val() == 'true'){
                                    gfield.find('input[value="0"]').removeAttr('checked');
                                    gfield.find('input[value="1"]').prop("checked", true);
                                }else{
                                    gfield.find('input[value="1"]').removeAttr('checked');
                                    gfield.find('input[value="0"]').prop("checked", true);
                                }
                            }
                        }
                    break;
                    case  'extra_option':
                        if(!isnew){
                            if(field.find('.extra_option').length > 0){
                                if(field.find('.extra_option').val() == '1' || field.find('.extra_option').val() == 'true'){
                                    gfield.find('input[value="0"]').removeAttr('checked');
                                    gfield.find('input[value="1"]').prop("checked", true);
                                }else{
                                    gfield.find('input[value="1"]').removeAttr('checked');
                                    gfield.find('input[value="0"]').prop("checked", true);
                                }
                            }
                        }
                    break;
                  case 'free':
                    gfield.html(val.desc);
                  default:
                    // code block
                }
                popup_field_config_hidden.find('.popup_field_content_box').append(gfield);
            }
        });
        var firts_id_listfield = 0;
        if ($('.itemfield_wp .itemfield').length > 0) {
            $('.itemfield_wp .itemfield').each(function(){
                firts_id_listfield = $(this).attr('id').match(/gformbuilderpro_(\d*)/)[1];
                return false;
            });
        }
        /*new version 1.3.6*/
        if ($('#popup_field_config_item .gfield_condition').length > 0 
        && $('#popup_field_config_item .gfield_condition_display').length > 0 
        && ginput != 'hidden' && firts_id_listfield !=0 && firts_id_listfield != id
        ) {
            if ($('#gfield_datas').length > 0 && $('#gfield_datas .gfield_data').length > 0){
                var check_datafields = false;
                var number_options = $('#gfield_datas #gfield_data_'+id+' .number_listcondition').val();
                var html ='<option value="0"> '+$('#gtext_cutom .gformtext_noneoption').val()+' </option>';
                if ($('.itemfield_wp .itemfield').length > 0) {
                    $('.itemfield_wp .itemfield').each(function(){
                        var id_listfield = $(this).attr('id').match(/gformbuilderpro_(\d*)/)[1];
                        if ($('#gfield_datas #gfield_data_'+id_listfield).length > 0 && id_listfield != id) {
                            var id_extra_filed_selectoption = $('#gfield_datas #gfield_data_'+id_listfield);
                            if (id_extra_filed_selectoption.find('input.type').val() != 'captcha' 
                            && id_extra_filed_selectoption.find('input.type').val() != 'googlemap' 
                            && id_extra_filed_selectoption.find('input.type').val() != 'html' 
                            && id_extra_filed_selectoption.find('input.type').val() != 'hidden' 
                            && id_extra_filed_selectoption.find('input.type').val() != 'wholesale' 
                            && id_extra_filed_selectoption.find('input.type').val() != 'submit') {
                                if (id_extra_filed_selectoption.attr('id') != 'gfield_data_'+id) {
                                    check_datafields = true;
                                    html +='<option value="'+id_extra_filed_selectoption.find('input.id_gformbuilderprofields').val()+'">'+id_extra_filed_selectoption.find('input.name').val()+'</option>';
                                }
                            }
                        } else {
                            return false;
                        }
                    });
                }
                if (check_datafields) {
                    var gfield_condition = $('#popup_field_config_item .gfield_condition').clone();
                    var gfield_condition_display = $('#popup_field_config_item .gfield_condition_display').clone();
                    popup_field_config_hidden.find('.popup_field_content').append(gfield_condition);

                    popup_field_config_hidden.find('.popup_field_content').append(gfield_condition_display);
                    if (id > 0 && !isnew) {
                        var gfield_datas_byid = $('#gfield_datas #gfield_data_'+id);
                        /*condition*/
                        if (gfield_datas_byid.find('.condition').val() == 1) {
                            popup_field_config_hidden.find('.gfield_condition').find('input[value="0"]').removeAttr('checked');
                            popup_field_config_hidden.find('.gfield_condition').find('input[value="1"]').prop("checked", true);
                            
                            popup_field_config_hidden.find('.gfield_condition_display').css('display', 'block');
                        } else {
                            popup_field_config_hidden.find('.gfield_condition').find('input[value="0"]').prop("checked", true);
                            popup_field_config_hidden.find('.gfield_condition').find('input[value="1"]').removeAttr('checked');
                            popup_field_config_hidden.find('.gfield_condition_display').css('display', 'none');
                        }
                        /*condition play*/
                        if (gfield_datas_byid.find('.condition_display').val() == 1) {
                            popup_field_config_hidden.find('.gformcondition_display_extra').find('option[value="0"]').removeAttr('selected');
                            popup_field_config_hidden.find('.gformcondition_display_extra').find('option[value="1"]').prop("selected", true);
                        } else {
                            popup_field_config_hidden.find('.gformcondition_display_extra').find('option[value="0"]').prop("selected", true);
                            popup_field_config_hidden.find('.gformcondition_display_extra').find('option[value="1"]').removeAttr('selected');
                        }
                        /*condition play value*/
                        if (gfield_datas_byid.find('.condition_must_match').val() == 1) {
                            popup_field_config_hidden.find('.gformcondition_value_extra').find('option[value="0"]').removeAttr('selected');
                            popup_field_config_hidden.find('.gformcondition_value_extra').find('option[value="1"]').prop("selected", true);
                        } else {
                            popup_field_config_hidden.find('.gformcondition_value_extra').find('option[value="0"]').prop("selected", true);
                            popup_field_config_hidden.find('.gformcondition_value_extra').find('option[value="1"]').removeAttr('selected');
                        }
                        /*condition play value*/
                        if (number_options > 0) {
                            for (let i = 1; i <= number_options; i++) {
                                var value_condition = gfield_datas_byid.find('.condition_listoptions_condition'+i).val();
                                var value_id_field = gfield_datas_byid.find('.condition_listoptions_id_field'+i).val();
                                var value_conditionvalue = gfield_datas_byid.find('.condition_listoptions_conditionvalue'+i).val();
                                var default_option = $('#listoption_datas .option_condition').clone();
                                var id_old = popup_field_config_hidden.find('.gfield_addlistoption').data('number');
                                default_option.attr('id', 'option_condition_'+id_old);
                                default_option.find('.gformcondition_listoption_idfield').attr('name', 'listoptions['+id_old+'][id_field]');
                                default_option.find('.gformcondition_listoption_idfield').html(html);
                                
                                default_option.find('.gformcondition_listoption_condition').attr('name', 'listoptions['+id_old+'][condition]');
                                default_option.find('.gformcondition_listoption_conditionvalue').attr('name', 'listoptions['+id_old+'][conditionvalue]');
                                default_option.find('.gfield_deletelistoption').attr('data-idoption', id_old);
                                popup_field_config_hidden.find('.gformcondition_listoption').append(default_option);
                                popup_field_config_hidden.find('.gfield_addlistoption').data('number', parseInt(id_old) + 1);

                                default_option.find('.gformcondition_listoption_idfield').find('option[value="'+value_id_field+'"]').prop("selected", true);
                                default_option.find('.gformcondition_listoption_condition').find('option[value="'+value_condition+'"]').prop("selected", true);
                                default_option.find('.gformcondition_listoption_conditionvalue').val(value_conditionvalue);
                                
                                if ($('#gfield_data_'+value_id_field).find('.type').val() == 'fileupload') {
                                    var name_old = default_option.find('.col-lg-3 input').attr('name');
                                    var html_option_typefile = $('#file_condition_option select').clone().attr('name', name_old);
                                    default_option.find('.gformcondition_listoption_condition').hide();
                                    if (value_conditionvalue == "1") {
                                        html_option_typefile.find('option[value="0"]').prop("selected", true);
                                        html_option_typefile.find('option[value="1"]').removeAttr('selected');
                                    } else {
                                        html_option_typefile.find('option[value="0"]').removeAttr('selected');
                                        html_option_typefile.find('option[value="1"]').prop("selected", true);
                                    }
                                    default_option.find('.col-lg-3').html(html_option_typefile);
                                }
                            }
                        }
                    } else {
                        var default_option = $('#listoption_datas .option_condition').clone();
                        var id_old = popup_field_config_hidden.find('.gfield_addlistoption').data('number');
                        default_option.attr('id', 'option_condition_'+id_old);
                        default_option.find('.gformcondition_listoption_idfield').attr('name', 'listoptions['+id_old+'][id_field]');
                        default_option.find('.gformcondition_listoption_idfield').html(html);
                        default_option.find('.gformcondition_listoption_condition').attr('name', 'listoptions['+id_old+'][condition]');
                        default_option.find('.gformcondition_listoption_conditionvalue').attr('name', 'listoptions['+id_old+'][conditionvalue]');
                        default_option.find('.gfield_deletelistoption').attr('data-idoption', id_old);
                        popup_field_config_hidden.find('.gformcondition_listoption').append(default_option);
                        popup_field_config_hidden.find('.gfield_addlistoption').data('number', parseInt(id_old) + 1);
                    }
                }
            }
        }
        /*end*/
        if(popup_field_config_hidden.find('.need_change_id').length > 0){
            popup_field_config_hidden.find('.need_change_id').each(function(){
                $(this).attr('id',$(this).attr('data-id'));
            });
        }
        $("#popup_field_config #content").html('').append(popup_field_config_hidden);
        if($('#popup_field_config .tagify').length > 0){
            $('#popup_field_config .tagify').each(function(){
                var addTagPrompt = $(this).attr('data-addTagPrompt');
                if(addTagPrompt == '' || addTagPrompt == "undefined") addTagPrompt = 'Add tag';
                $(this).tagify({delimiters: [13,44], addTagPrompt: addTagPrompt});
            });
        }
        if($('#popup_field_config .gcolor').length > 0){
            $('#popup_field_config .gcolor').each(function(){
                $(this).attr('type','color').addClass('mColorPicker').mColorPicker();  
            });
        }
        $("#popup_field_config_link").click();
        /* popup */
    }


    $(document).on('click','#itemfieldparent .itemfield',function () {
        /*new version 1.3.6*/
        var type = $(this).attr('data-type');
        if (type == 'wholesale' && $('.itemfield_wp .itemfield').length > 0) {
            var check_onewhosale = false;
            $('.itemfield_wp .itemfield').each(function(){
                var id_listfield = $(this).attr('id').match(/gformbuilderpro_(\d*)/)[1];
                if ($('#gfield_datas #gfield_data_'+id_listfield).length > 0) {
                    var id_extra_filed_selectoption = $('#gfield_datas #gfield_data_'+id_listfield);
                    if (id_extra_filed_selectoption.find('input.type').val() == 'wholesale') {
                        check_onewhosale = true;
                    }
                }
            });
            if (check_onewhosale){
                showErrorMessage($('#gtext_cutom .gformtext_nonewholesale').val());
                return false;
            }
        }
        /*end*/
        $.fancybox.close();
        if(type == 'newrow'){
            addNewGroup(['col-md-12 col-sm-12 col-xs-12']);
            if($('.formbuilder_new_group').length > 0){
                var new_group_offset = $('.formbuilder_new_group').offset().top;
                setTimeout(function(){
                    $('html,body').animate({
                        scrollTop: new_group_offset
                    }, 300);
                    
                }, 500);
                
            }
        }else{
            /* new design*/
            var newItem = $(this).clone();
            
            var ginput = newItem.attr('data-type');
            newItem.removeAttr('data-newitem');
            width = 12;widthsm = 12;widthxs = 12;
            addControlBt(newItem,width,widthsm,widthxs);
            setPopupData(1,ginput,0);
            newItem.attr('id','newfield');
            if($('.add_new_element.pendding').length > 0){
                $('.add_new_element.pendding').closest('.formbuilder_column').find('.itemfield_wp').append(newItem);
            }else if($('.add_new_element_top.pendding').length > 0){
                $('.add_new_element_top.pendding').closest('.formbuilder_column').find('.itemfield_wp').prepend(newItem);
            }else{
                addNewGroup(['col-md-12 col-sm-12 col-xs-12']);
                $('#formbuilder').find('.formbuilder_new_group').find('.itemfield_wp').append(newItem);
            }
            refreshSortable();
        }
        checkIsBlankPage();
        if($('#formbuilder .formbuilder_new_group').length > 0)
            $('#formbuilder .formbuilder_new_group').removeClass('formbuilder_new_group');
        $('#formbuilder .pendding').removeClass('pendding');
        return false;
    });


    
    $('#addnewgroup').click(function(){
        addNewGroup(['col-md-6 col-sm-6 col-xs-6','col-md-6 col-sm-6 col-xs-6']);
    });
    /* edit field */
    $(document).on('click', '.formbuilder_edit', function(){
        
        var ids = $(this).parents('.itemfield').attr("id").match(/gformbuilderpro_(\d*)/);
        if(ids.length > 1) {
            var id = ids[1];
            id = id.replace(/\s/g, '');
            if (id) {
                if($('#gfield_data_'+id).length > 0){
                    var ginput = $('#gfield_data_'+id).find('.type').val().trim();
                    setPopupData(0,ginput,id);
                }
                console.debug(id);
                /*
                $(gformbuilderpro_overlay).appendTo('body');
                $.ajax({
                    url: $('#ajaxurl').val(),
                    type: 'POST',
                    data: 'typefield=true&id_gformbuilderprofields=' + id,
                })
                    .done(function (data) {
                        $('#gformbuilderpro_overlay').remove();
                        if (data != '') {
                            $("#popup_field_config #content").html(data);
                            $("#popup_field_config_link").click();
                        }
                    });
                    */
            }
        }
        return false;
    });
    /* delete field */
    $(document).on('click', '.formbuilder_delete', function(){
        if($(this).hasClass('formbuilder_delete_group')){
            var group = $(this).parents('.formbuilder_group');
            itemfields = group.find(".itemfield");
            var ids = [];
            allfields = $('#fields').val().split(',');
            if(itemfields)
                itemfields.each(function(){
                    var id = $(this).attr("id").match(/gformbuilderpro_(\d*)/)[1];
                    if(id>0){
                        ids.push(id);
                        allfields = $.grep(allfields, function(value) {
                          return value != id;
                        });
                        $('#fields').val(allfields);
                    } 
                });
            removeField(ids.join('_'),1,group);
        }else if($(this).hasClass('delete_control_column_top')){
            var column = $(this).closest('.formbuilder_column');
            itemfields = column.find(".itemfield");
            var ids = [];
            allfields = $('#fields').val().split(',');
            if(itemfields)
                itemfields.each(function(){
                    var id = $(this).attr("id").match(/gformbuilderpro_(\d*)/)[1];
                    if(id>0){
                        ids.push(id);
                        allfields = $.grep(allfields, function(value) {
                            return value != id;
                        });
                        $('#fields').val(allfields);
                    }
                });
            removeField(ids.join('_'),1,column);
        }
        else{
            id = $(this).parents('.itemfield').attr("id").match(/gformbuilderpro_(\d*)/)[1];
            if(id){
                removeField(id,0,null);
                allfields = $('#fields').val().split(',');
                allfields = $.grep(allfields, function(value) {
                  return value != id;
                });
                $('#fields').val(allfields);
            }
        }
        return false;
    });
    $('.label_lang').attr('style','');
    $('.label_lang_'+$('#idlang_default').val()).css('display','block');
    $(document).on('click', 'button[name="addShortcode"],input[name="addShortcode"]', function(){
        
        form = $(this).parents('form');
        if(form.find('.slidervalue').length > 0){
            form.find('.slidervalue').each(function(){
                changeSildeValue($(this).closest('.popup_field_content_box'));
            });
        }
        if(form.find('.textareatiny').length > 0) tinyMCE.triggerSave();
        form.find('.alert-danger').remove();
        formok = true;
        form.find('.tagify').each(function(){
            $(this).val($(this).tagify('serialize'));
        });
        if($('.multival_wp').length > 0){
            $.each(languages,function(key,val){
                if(form.find('#multival_value').length > 0){
                    multival_wp = [];
                    form.find('#multival_value .multival_wp .multival').each(function(){
                        multival_wp.push($(this).find('.lang-'+val.id_lang).html());
                    });
                    form.find('#multival_value #value_'+val.id_lang).val(multival_wp.join());
                }
                if(form.find('#multival_description').length > 0){
                    multival_wp = [];
                    form.find('#multival_description .multival_wp .multival').each(function(){
                        multival_wp.push($(this).find('.lang-'+val.id_lang).html());
                    });
                    form.find('#multival_description #description_'+val.id_lang).val(multival_wp.join());
                }
            });
        }
        form.find('.gvalidate_isRequired').each(function(){
            if($(this).val() == ''){
                namenolang = $(this).attr('name').split('_');
                if(namenolang.length > 1){
                    if($('input[name="'+namenolang[0]+'_'+gdefault_language+'"]').length > 0 && $('input[name="'+namenolang[0]+'_'+gdefault_language+'"]').val() !=''){
                        $(this).val($('input[name="'+namenolang[0]+'_'+gdefault_language+'"]').val());
                    }else{
                        langerror = '';
                        $.each( languages, function( index, value ){
                            if(value.id_lang == namenolang[1]){
                                langerror = '('+value.name+')';
                            }
                        });
                        if( typeof psversion15 != 'undefined' && psversion15 != -1)
                            form.find('.form-wrapper').append('<div class="alert alert-danger">'+$(this).parents('.form-group').find('.control-label').html()+langerror+empty_danger+'</div>');
                        else 
                            form.find('fieldset').append('<div class="alert alert-danger">'+$(this).parents('.margin-form').prev('label').html()+langerror+empty_danger+'</div>');
                        formok = false;
                    }
                }else{
                    if( typeof psversion15 != 'undefined' && psversion15 != -1)
                        form.find('.form-wrapper').append('<div class="alert alert-danger">'+$(this).parents('.form-group').find('.control-label').html()+empty_danger+'</div>');
                    else 
                        form.find('fieldset').append('<div class="alert alert-danger">'+$(this).parents('.margin-form').prev('label').html()+empty_danger+'</div>');
                    formok = false;
                }
            }
        });
        form.find('.gvalidate_isRequired2').each(function(){
            if($(this).val() == ''){
                namenolang = $(this).attr('name').split('_');
                if(namenolang.length > 1){
                    if($('input[name="'+namenolang[0]+'_'+gdefault_language+'"]').length > 0 && $('input[name="'+namenolang[0]+'_'+gdefault_language+'"]').val() !=''){
                        $(this).val($('input[name="'+namenolang[0]+'_'+gdefault_language+'"]').val());
                    }else
                        if(form.find('#extra').val() == ''){
                            langerror = '';
                            $.each( languages, function( index, value ){
                                if(value.id_lang == namenolang[1]){
                                    langerror = '('+value.name+')';
                                }
                            });
                            if( typeof psversion15 != 'undefined' && psversion15 != -1)
                                form.find('.form-wrapper').append('<div class="alert alert-danger">'+$(this).parents('.form-group').find('.control-label').html()+langerror+empty_danger+'</div>');
                            else
                                form.find('fieldset').append('<div class="alert alert-danger">'+$(this).parents('.margin-form').prev('label').html()+langerror+empty_danger+'</div>');
                            formok = false;
                        }
                }else{
                    if(form.find('#extra').val() == ''){
                        if( typeof psversion15 != 'undefined' && psversion15 != -1)
                            form.find('.form-wrapper').append('<div class="alert alert-danger">'+$(this).parents('.form-group').find('.control-label').html()+empty_danger+'</div>');
                        else
                            form.find('fieldset').append('<div class="alert alert-danger">'+$(this).parents('.margin-form').prev('label').html()+empty_danger+'</div>');
                        formok = false;
                    }
                }
                
            }
        });
        form.find('.gvalidate_isRequired3').each(function(){
            val = $(this).val();
            if($(this).val() == ''){
                if( typeof psversion15 != 'undefined' && psversion15 != -1)
                    form.find('.form-wrapper').append('<div class="alert alert-danger">'+$(this).parents('.form-group').find('.control-label').html()+empty_danger+'</div>');
                else form.find('fieldset').append('<div class="alert alert-danger">'+$(this).parents('.margin-form').prev('label').html()+empty_danger+'</div>');
                formok = false;
            }else{
                vals =val.split(',');
                if (vals[0] == "undefined") {
                    if( typeof psversion15 != 'undefined' && psversion15 != -1)
                        form.find('.form-wrapper').append('<div class="alert alert-danger">'+$(this).parents('.form-group').find('.control-label').html()+empty_danger+'</div>');
                    else form.find('fieldset').append('<div class="alert alert-danger">'+$(this).parents('.margin-form').prev('label').html()+empty_danger+'</div>');
                    formok = false;
                }else
                if (vals[1] == "undefined") {
                    if( typeof psversion15 != 'undefined' && psversion15 != -1)
                        form.find('.form-wrapper').append('<div class="alert alert-danger">'+$(this).parents('.form-group').find('.control-label').html()+empty_danger+'</div>');
                    else form.find('fieldset').append('<div class="alert alert-danger">'+$(this).parents('.margin-form').prev('label').html()+empty_danger+'</div>');
                    formok = false;
                }else
                if (vals[2] == "undefined") {
                    if( typeof psversion15 != 'undefined' && psversion15 != -1)
                        form.find('.form-wrapper').append('<div class="alert alert-danger">'+$(this).parents('.form-group').find('.control-label').html()+empty_danger+'</div>');
                    else form.find('fieldset').append('<div class="alert alert-danger">'+$(this).parents('.margin-form').prev('label').html()+empty_danger+'</div>');
                    formok = false;
                }else
                if (vals[3] == "undefined") {
                    if( typeof psversion15 != 'undefined' && psversion15 != -1)
                        form.find('.form-wrapper').append('<div class="alert alert-danger">'+$(this).parents('.form-group').find('.control-label').html()+empty_danger+'</div>');
                    else form.find('fieldset').append('<div class="alert alert-danger">'+$(this).parents('.margin-form').prev('label').html()+empty_danger+'</div>');
                    formok = false;
                }
            }
        });
        if(!formok){
            return false;
        } 
        btform =  $(this);
        btform.attr('disable','disable');
        action = form.attr('action');
        serializedForm = form.serialize();
        name = form.find('#name').val();
        labels = '';
        $.each(languages, function( index, value ) {
            labels += '<span class="label_lang label_lang_'+value.id_lang+'">'+form.find('input[name="label_'+value.id_lang+'"]').val()+'</span>';
        });
        $(gformbuilderpro_overlay).appendTo('body');
        $.ajax({
             type: 'POST',
             url: action,
             data: serializedForm,
             dataType: "json",
             async: false,
             success: function (data, textStatus, request) {
                btform.removeAttr('disable');
                $('#gformbuilderpro_overlay').remove();
                var id_field = 0;
                if(typeof data.id != "undefined" && data.id > 0) id_field = data.id;
                else if(typeof data.id_gformbuilderprofields != "undefined" && data.id_gformbuilderprofields > 0) id_field = data.id_gformbuilderprofields;
                if(id_field == 0){
                    alert&("Error occurred!");
                }else{
                    if($('#formbuilder #newfield').length > 0){
                        $('#formbuilder #newfield .shortcode').html('[gformbuilderpro:'+id_field+']');
                        $('#formbuilder #newfield').removeAttr('id').attr('id','gformbuilderpro_'+id_field);
                    }
                    if($('#gformbuilderpro_'+id_field).length > 0){
                        $('#gformbuilderpro_'+id_field).find('.feildname').html('{'+name+'}');
                        $('#gformbuilderpro_'+id_field).find('.feildlabel').html(labels);
                    }
                    $('.label_lang').attr('style','');
                    $('.label_lang_'+$('#idlang_default').val()).css('display','block');
                    fields =  $('#fields').val().split(',');
                    fields = $.grep(fields, function(value) {
                      return value != id_field;
                    });
                    fields.push(id_field);
                    _allfields = fields.join(',');
                    if(_allfields.charAt(0) == ',')
                        $('#fields').val(_allfields.slice(1));
                    else
                        $('#fields').val(_allfields);
                    if($('#formbuilder .pendding').length > 0)
                        $('#formbuilder .pendding').removeClass('pendding');
                    $.fancybox.close();
                    addFieldData(data);
                }
             },
             error: function (req, status, error) {
                $('#gformbuilderpro_overlay').remove();
                 alert&("Error occurred!");
             }
        });
        return false;
    });
    
    $(document).on('click', 'button[name="cancelShortcode"],input[name="cancelShortcode"]', function(){
        $('#newfield').remove();
        if($('#formbuilder .pendding').length > 0)
            $('#formbuilder .pendding').removeClass('pendding');
        $.fancybox.close();
        if($('#popup_field_config_wp').length > 0) $('#popup_field_config_wp').removeAttr('class');
        if($('#popup_field_config_wp .popup_field_content_box').length > 0) $('#popup_field_config_wp .popup_field_content_box').html('');
        return false;
    });
    
    /*  change width group box */
    $('.formbuilder_group_width').live('change',function(){
        rel = $(this).attr('rel');
        width = $(this).val();
        if($(this).hasClass('control_box_select')){
            if(rel == 'sm'){
                $(this).parents('.itemfield').attr('class',$(this).parents('.itemfield').attr('class').replace(/(^|\s)col-sm-\S+/g, ' col-sm-'+width+' '));
            }else if(rel == 'xs'){
                $(this).parents('.itemfield').attr('class',$(this).parents('.itemfield').attr('class').replace(/(^|\s)col-xs-\S+/g, ' col-xs-'+width+' '));
            }else{
                $(this).parents('.itemfield').attr('class',$(this).parents('.itemfield').attr('class').replace(/(^|\s)col-md-\S+/g, ' col-md-'+width+' '));
            }
        }else{
            if(rel == 'sm'){
                $(this).parents('.formbuilder_group').attr('class',$(this).parents('.formbuilder_group').attr('class').replace(/(^|\s)col-sm-\S+/g, ' col-sm-'+width+' '));
            }else if(rel == 'xs'){
                $(this).parents('.formbuilder_group').attr('class',$(this).parents('.formbuilder_group').attr('class').replace(/(^|\s)col-xs-\S+/g, ' col-xs-'+width+' '));
            }else{
                $(this).parents('.formbuilder_group').attr('class',$(this).parents('.formbuilder_group').attr('class').replace(/(^|\s)col-md-\S+/g, ' col-md-'+width+' '));
            }
        }
        clearBeforeSave();
    });
    
    $('#gformbuilderpro_form').submit(function(){
        clearBeforeSave();
        if($('#title_'+gdefault_language).val() == ''){
            alertdanger = '<div class="alert alert-danger">'+gtitleform+' '+empty_danger+'</div>';
            $(alertdanger).insertBefore( ".gformbuilderpro_admintab" );
            return false;
        }else{
            $.each( languages, function( index, value ){
                if($('#title_'+value.id_lang).val() == ''){
                    $('#title_'+value.id_lang).val($('#title_'+gdefault_language).val());
                }
            });
        }
        $.each( languages, function( index, value ){
            if($('#rewrite_'+value.id_lang).val() == ''){
                val = $('#title_'+value.id_lang).val();
                val = val.replace(/[^0-9a-zA-Z:._-]/g, '').replace(/^[^a-zA-Z]+/, '');
                $('#rewrite_'+value.id_lang).val(val.toLowerCase());
            }
        });
        
        /* from 1.2.2 */
        if($('input[name="using_condition"]').length > 0){
            if($('input[name="using_condition"]:checked').val() == '1'){
                var is_empty = gcheckCondition();
                if(is_empty){
                    $('.tab-page[href="#tabemail"]').click();
                    return false;
                }
            }
            if($('#using_condition_config_wp .condition_form_item').length > 0){
                var condition_config_data = [];
                $('#using_condition_config_wp .condition_form_item').each(function(){
                    values = {};
                    $(this).find('.condition_config_value').each(function(){
                        values[$(this).attr('rel')] = $(this).val().trim();
                    });
                    condition_config_data.push(
                        {
                            'if' : $(this).find('.condition_config_if').val(),
                            'state' : $(this).find('.condition_config_state').val(),
                            'value' : values,
                            'email' : $(this).find('.condition_config_email_to').val()
                        }
                    );
                });
                $('#condition_config_data').val(JSON.stringify(condition_config_data));
            }else{
                $('#condition_config_data').val('');
            }
        }
        /* # from 1.2.2 */
        
        
        return true;
    });
    /* admin tab */
    $('.gformbuilderpro_admintab .tab-page').click(function(){
        if (!$(this).parent('.tab-row').hasClass('active')) {
            $('.gformbuilderpro_admintab .tab-row').removeClass('active');
            $(this).parent('.tab-row').addClass('active');
            $('.formbuilder_tab').removeClass('activetab');
            idtabactive = $(this).attr('href');
            if ($(idtabactive).length > 0) $(idtabactive).addClass('activetab');
        }
        if ($(this).attr('href') == '#tabemail' || $(this).attr('href') == '#tabemail') {
            $('.gformbuilderpro_admintab_level2').addClass('active');
            gfromloadshortcode();
        } else {
            $('.gformbuilderpro_admintab_level2').removeClass('active');
        }
        return false;
    });
    $('.gformbuilderpro_admintab_level2 .tab_email').click(function(){
        if (!$(this).parent('.tab-row').hasClass('active')) {
            $(this).closest('.gformbuilderpro_admintab_level2').find('.tab-row').removeClass('active');
            $(this).parent('.tab-row').addClass('active');
            $('.formbuilder_email_tab').removeClass('activetab');
            idtabactive = $(this).attr('href');
            if ($(idtabactive).length > 0) $(idtabactive).addClass('activetab');
        }
        return false;
    });
    $('.gformbuilderpro_admintab_level2 .tab_integration').click(function(){
        if (!$(this).parent('.tab-row').hasClass('active')) {
            $(this).closest('.gformbuilderpro_admintab_level2').find('.tab-row').removeClass('active');
            $(this).parent('.tab-row').addClass('active');
            $('.formbuilder_integration_tab').removeClass('activetab');
            idtabactive = $(this).attr('href');
            if ($(idtabactive).length > 0) $(idtabactive).addClass('activetab');
        }
        return false;
    });
    
    $('select[name="groupaccessbox[]"]').change(function(){
        $('#groupaccess').val($(this).val().join(','));
    });
    
    $(document).on('click', '.gemail_template_select', function(){
        $('.list_email_template .gemail_template').removeClass('active');
        $(this).closest('.gemail_template').addClass('active');
        return false;
    })
    
    
    $('#gfromloaddefault').click(function(){
        tinyMCE.triggerSave();
        form = $('#gformbuilderpro_form');/* $(this).parents('form'); */
        action = form.attr('action');
        serializedForm = form.serialize();
        $(gformbuilderpro_overlay).appendTo('body');
        
        var template = $(this).closest('#choose_email_template_box').find('.gemail_template.active').attr('rel');
        $.ajax({
             type: 'POST',
             url: action,
             data: serializedForm+'&gfromloaddefault=true&template='+template,
             async: false,
             success: function (data, textStatus, request) {
                $('#gformbuilderpro_overlay').remove();
                var result = $.parseJSON(data);
                if(result.errors=='0'){
                    $.each(result.datas,function(index, value){
                        if($('iframe[id^="emailtemplate_'+index+'_ifr"]').length > 0)
                            $('iframe[id^="emailtemplate_'+index+'_ifr"]').contents().find('body').html(value);
                        if($('#emailtemplate_'+index+' .mceIframeContainer iframe').length > 0)
                            $('#emailtemplate_'+index+' .mceIframeContainer iframe').contents().find('body').html(value);
                    });
                    $.each(result.datassender,function(index, value){
                        if($('iframe[id^="emailtemplatesender_'+index+'_ifr"]').length > 0)
                            $('iframe[id^="emailtemplatesender_'+index+'_ifr"]').contents().find('body').html(value);
                        if($('#emailtemplatesender_'+index+' .mceIframeContainer iframe').length > 0)
                            $('#emailtemplatesender_'+index+' .mceIframeContainer iframe').contents().find('body').html(value);
                    });
                    $.each(result.datassendersubject,function(index, value){
                        if($('textarea[name="subjectsender_'+index+'"]').length > 0)
                            $('textarea[name="subjectsender_'+index+'"]').html(value);
                        if($('#subjectsender_'+index+' .mceIframeContainer iframe').length > 0)
                            $('#subjectsender_'+index+' .mceIframeContainer iframe').contents().find('body').html(value);
                    });
                    $.each(result.subject,function(index, value){
                        if($('textarea[name="subject_'+index+'"]').length > 0)
                            $('textarea[name="subject_'+index+'"]').html(value);
                        if($('#subject_'+index+' .mceIframeContainer iframe').length > 0)
                            $('#subject_'+index+' .mceIframeContainer iframe').contents().find('body').html(value);
                    });
                    
                    /* from version 1.2.0 */
                    $.each(result.replysubject,function(index, value){
                        if($('textarea[name="replysubject_'+index+'"]').length > 0)
                            $('textarea[name="replysubject_'+index+'"]').html(value);
                        if($('#replysubject_'+index+' .mceIframeContainer iframe').length > 0)
                            $('#replysubject_'+index+' .mceIframeContainer iframe').contents().find('body').html(value);
                    });
                    $.each(result.replyemailtemplate,function(index, value){
                        if($('iframe[id^="replyemailtemplate_'+index+'_ifr"]').length > 0)
                            $('iframe[id^="replyemailtemplate_'+index+'_ifr"]').contents().find('body').html(value);
                        if($('#replyemailtemplate_'+index+' .mceIframeContainer iframe').length > 0)
                            $('#replyemailtemplate_'+index+' .mceIframeContainer iframe').contents().find('body').html(value);
                    });
                    $.fancybox.close();
                    if($('.pendding').length > 0)
                        $('.pendding').removeClass('pendding');
                    
                }else
                    alert&("Error occurred!");
                
             },
             error: function (req, status, error) {
                $('#gformbuilderpro_overlay').remove();
                 alert&("Error occurred!");
             }
        });
        return false;
    });

    
    $('.emailshortcode_panel .panel-heading,.emailshortcode_panel .box-heading').click(function(){
        if($(this).next('.emailshortcode').css('display') !='none'){
            $(this).removeClass('active');
            $(this).next('.emailshortcode').stop(true,true).slideUp(300);
        }else{
            $(this).addClass('active');
            $(this).next('.emailshortcode').stop(true,true).slideDown(300);
        }
    });    
    $('#multi_on').live('change',function(){
        if($('.popup_field_content_box .slidervalue').length > 0) changeSildeValue($(this).closest('.popup_field_content_box'));
        
    });
    $('#multi_off').live('change',function(){
        if($('.popup_field_content_box .slidervalue').length > 0) changeSildeValue($(this).closest('.popup_field_content_box'));
    });
    $(document).on('click', '.copy_link', function(){
        copyToClipboard($(this));
        return false;
    });
    
    $('input[name="autoredirect"]').change(function(){
        if($('input[name="autoredirect"]:checked').val() == '1'){
            $('.autoredirect_config').stop(true,true).slideDown(500);
        }else{
            $('.autoredirect_config').stop(true,true).slideUp(500);
        }
    });
    $('input[name="ispopup"]').change(function(){
        if($('input[name="ispopup"]:checked').val() == '1'){
            $('.ispopup_config').stop(true,true).slideDown(500);
        }else{
            $('.ispopup_config').stop(true,true).slideUp(500);
        }
    });


    $('input[name="mailchimp"]').change(function(){
        if($('input[name="mailchimp"]:checked').val() == '1'){
            $('.mailchimpmap_wp').stop(true,true).slideDown(500);
        }else{
            $('.mailchimpmap_wp').stop(true,true).slideUp(500);
        }
    });
    $('input[name="klaviyo"]').change(function(){
        if($('input[name="klaviyo"]:checked').val() == '1'){
            $('.klaviyomap_wp').stop(true,true).slideDown(500);
        }else{
            $('.klaviyomap_wp').stop(true,true).slideUp(500);
        }
    });
    $('input[name="zapier"]').change(function(){
        if($('input[name="zapier"]:checked').val() == '1'){
            $('.zapiermap_wp').stop(true,true).slideDown(500);
        }else{
            $('.zapiermap_wp').stop(true,true).slideUp(500);
        }
    });



    /* from 1.2.2 */
    $('input[name="using_condition"]').change(function(){
        if($('input[name="using_condition"]:checked').val() == '1'){
            $('.using_condition_config').stop(true,true).slideDown(500);
        }else{
            $('.using_condition_config').stop(true,true).slideUp(500);
        }
    });
    /* #from 1.2.2 */
    if($('#gformbuilderpro_reply').length > 0){
        tinySetup({
    		editor_selector :"gautoload_rte"
    	});
    }
    $('.submit_reply').click(function(){
        tinyMCE.triggerSave();
        form = $(this).parents('form');
        action = form.attr('action');
        serializedForm = form.serialize();
        $(gformbuilderpro_overlay).appendTo('body');
        $.ajax({
             type: 'POST',
             url: action,
             data: serializedForm+'&gfromSubmitReply=1',
             dataType: "json",
             async: false,
             success: function (data, textStatus, request) {
                if(data.error == 0){
                    showSuccessMessage(data.warrning); 
                }else{
                    showErrorMessage(data.warrning); 
                }
                $('#gformbuilderpro_overlay').remove();
             }
        });
        return false;
    });
    $('#desc-gformbuilderpro-exportgform,#gformbuilderpro_form_export').click(function(){
        form_checked = '';
        if($('input[name="gformbuilderproBox[]"]').length > 0){
            var checkedVals = $('input[name="gformbuilderproBox[]"]:checked').map(function() {
                return this.value;
            }).get();
            form_checked = checkedVals.join(",");
        }else{
            if($('#table-gformbuilderpro').length > 0){
                if($('#table-gformbuilderpro tbody tr').length > 0){
                    form_checked = parseInt($('#table-gformbuilderpro tbody tr').first().children('td').first().html());
                }
            }
        }
        if(form_checked == ''){
            if($('.gimport_export_form .export_warrning').length > 0 && $('#form-gformbuilderpro .export_warrning').length == 0){
                $('#form-gformbuilderpro').prepend($('.gimport_export_form .export_warrning').clone());
            }
        }else{
            if($('#form-gformbuilderpro .export_warrning').length > 0){
                $('#form-gformbuilderpro .export_warrning').remove();
            }
            $('#gid_forms').val(form_checked);
            $('form[name="gexport_form"]').submit();
        }
        return false;
    });
    if($('.gfancybox_btn').length > 0) $('.gfancybox_btn').fancybox();
    if($('.gformrequest_quickview').length > 0){
        $('.gformrequest_quickview').fancybox();
        $('.gformrequest_quickview').click(function(){
            if($(this).closest('tr').find('.gform_subject').length > 0){
                if($(this).closest('tr').find('.gform_subject').hasClass('gunread')){
                    var gform_subject = $(this).closest('tr').find('.gform_subject');
                    $.ajax({
                         type: 'POST',
                         data: 'gfromViewedRequest=1&id_gformrequest='+$(this).attr('rel'),
                         dataType: "json",
                         success: function (data, textStatus, request) {
                            if(data.error == 0)
                                gform_subject.removeClass('gunread');
                            if (typeof getUnReadReceived !== "undefined")
                                getUnReadReceived();
                         }
                    });
                }
            }
            
        });
    }
        
    $('.giconstar').click(function(){
        var gstarred_toggle = 1;
        if($(this).hasClass('gstarred')) gstarred_toggle = 0;
        starfield = $(this);
        $.ajax({
             type: 'POST',
             data: 'gfromToggleStar=1&star='+gstarred_toggle+'&id_gformrequest='+$(this).attr('rel'),
             dataType: "json",
             async: false,
             success: function (data, textStatus, request) {
                if(data.error == 0){
                    if(gstarred_toggle){
                        starfield.addClass('gstarred');
                        starfield.attr('data-original-title',starfield.attr('data-iststar'));
                    }  
                    else{
                        starfield.removeClass('gstarred');
                        starfield.attr('data-original-title',starfield.attr('data-notstar'));
                    } 
                }else{
                    showErrorMessage(data.warrning); 
                }
             }
        });
    });
    $('.choose_hook a').click(function(){
        var addTagPrompt = 'Add hook';
        if($('.addtagprompt').length > 0){
            addTagPrompt = $('.addtagprompt').val();
        }
        var hook = $(this).attr('data-shortcode');
        $('#hooks').val($('#hooks').tagify('serialize'));
        var current_hook = $('#hooks').val();
        if(current_hook == '') current_hook = hook;
        else{
            var current_hooks = current_hook.split(',');
            current_hooks = $.grep(current_hooks, function(val) {
                return hook != val;
            });
            current_hooks.push(hook);
            current_hook = current_hooks.join(',');
        }
        $('#hooks').tagify('destroy');
        $('#hooks').val(current_hook);
        $('#hooks').tagify({delimiters: [13,44], addTagPrompt: addTagPrompt});
        return false;
    });
});
$(document).on('focusout', '.rewrite_url', function() {
	val = $(this).val();
    val = val.replace(/[^0-9a-zA-Z:._-]/g, '').replace(/^[^a-zA-Z]+/, '');
    $(this).val(val.toLowerCase());
});
$(document).on('focusout', '.slidervalue', function() {
    changeSildeValue($(this).closest('.gfield_extra_slidervalue'));
});

