{if isset($id_product) && isset($product)}    
    <div id="kbproduct-images" class="panel" style="display: none;">
        <div class="form-wrapper">
            <div class="row">
                <div class="form-group">
                    <label class="control-label col-lg-3 file_upload_label">
                        <span class="label-tooltip" data-toggle="tooltip"
                              title="{l s='Format:' mod='kbbookingcalendar'} JPG, GIF, PNG. {l s='Filesize:' mod='kbbookingcalendar'} {$max_image_size|string_format:"%.2f"} {l s='MB max.' mod='kbbookingcalendar'}">
                    {if isset($id_image)}{l s='Edit this product\'s image:' mod='kbbookingcalendar'}{else}{l s='Add a new image to this product' mod='kbbookingcalendar'}{/if}
                </span>
            </label>
            <div class="col-lg-9">
                {$image_uploader}
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3">
                <span class="label-tooltip" data-toggle="tooltip"
                      title="{l s='Update all captions at once, or select the position of the image whose caption you wish to edit. Invalid characters: %s' sprintf=['<>;=#{}'] mod='kbbookingcalendar'}">
                    {l s='Caption' mod='kbbookingcalendar'}
                </span>
            </label>
            <div class="col-lg-4">
                {foreach from=$languages item=language}
                    {if $languages|count > 1}
                        <div class="translatable-field row lang-{$language.id_lang}" {if $language.id_lang != $id_lang}style="display: none;"{/if}>
                            <div class="col-lg-8">
                            {/if}
                            <input type="text" id="legend_{$language.id_lang}"{if isset($input_class)} class="{$input_class}"{/if} name="legend_{$language.id_lang}" value="{if $images|count}{$images[0]->legend[$language.id_lang]|escape:'html':'UTF-8'}{else}{$product->name[$language.id_lang]|escape:'html':'UTF-8'}{/if}"{if !$product->id} disabled="disabled"{/if}/>
                            {if $languages|count > 1}
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
                                    {$language.iso_code}
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach from=$languages item=language}
                                        <li>
                                            <a href="javascript:hideOtherLanguage({$language.id_lang});">{$language.name}</a>
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                    {/if}
                {/foreach}
            </div>
            <div class="col-lg-2{if $images|count <= 1} hidden{/if}" id="caption_selection">
                <select name="id_caption">
                    <option value="0">{l s='All captions' mod='kbbookingcalendar'}</option>
                    {foreach from=$images item=image}
                        <option value="{$image->id_image|intval}">
                            {l s='Position %d' sprintf=$image->position|intval mod='kbbookingcalendar'}
                        </option>
                    {/foreach}
                </select>
            </div>
            <div class="col-lg-2">
                <button type="submit" class="btn btn-default" name="submitAddproductAndStay" value="update_legends"><i class="icon-random"></i> {l s='Update' mod='kbbookingcalendar'}</button>
            </div>
        </div>
    </div>
    <table class="table tableDnD" id="imageTable">
        <thead>
            <tr class="nodrag nodrop">
                <th class="fixed-width-lg"><span class="title_box">{l s='Image' mod='kbbookingcalendar'}</span></th>
                <th class="fixed-width-lg"><span class="title_box">{l s='Caption' mod='kbbookingcalendar'}</span></th>
                <th class="fixed-width-xs"><span class="title_box">{l s='Position' mod='kbbookingcalendar'}</span></th>
                    {if $shops}
                        {foreach from=$shops item=shop}
                        <th class="fixed-width-xs"><span class="title_box">{$shop.name}</span></th>
                        {/foreach}
                    {/if}
                <th class="fixed-width-xs"><span class="title_box">{l s='Cover' mod='kbbookingcalendar'}</span></th>
                <th></th> <!-- action -->
            </tr>
        </thead>
        <tbody id="imageList">
        </tbody>
    </table>
    <table id="lineType" style="display:none;">
        <tr id="image_id">
            <td>
                <a href="{$smarty.const._THEME_PROD_DIR_}image_path.jpg" class="fancybox">
                    <img
                        src="{$smarty.const._THEME_PROD_DIR_}{$iso_lang}-default-{$imageType}.jpg"
                        alt="legend"
                        title="legend"
                        class="img-thumbnail" />
                </a>
            </td>
            <td>legend</td>
            <td id="td_image_id" class="pointer dragHandle center positionImage">
                <div class="dragGroup">
                    <div class="positions">
                        image_position
                    </div>
                </div>
            </td>
            {if $shops}
                {foreach from=$shops item=shop}
                    <td>
                        <input
                            type="checkbox"
                            class="image_shop"
                            name="id_image"
                            id="{$shop.id_shop}image_id"
                            value="{$shop.id_shop}" />
                    </td>
                {/foreach}
            {/if}
            <td class="cover">
                <a href="#">
                    <i class="icon-check-empty icon-2x covered"></i>
                </a>
            </td>
            <td>
                <a href="#" class="delete_product_image pull-right btn btn-default" >
                    <i class="icon-trash"></i> {l s='Delete this image' mod='kbbookingcalendar'}
                </a>
            </td>
        </tr>
    </table>
    <script type="text/javascript">
        var upbutton = "{l s='Upload an image' mod='kbbookingcalendar'}";
        var come_from = "{$table}";
        var success_add = "{l s='The image has been successfully added.' mod='kbbookingcalendar'}";
        var id_tmp = 0;
        var current_shop_id = {$current_shop_id|intval};
        {literal}
            //Ready Function

            function imageLine(id, path, position, cover, shops, legend)
            {
                line = $("#lineType").html();
                line = line.replace(/image_id/g, id);
                line = line.replace(/(\/)?[a-z]{0,2}-default/g, function ($0, $1) {
                    return $1 ? $1 + path : $0;
                });
                line = line.replace(/image_path/g, path);
                line = line.replace(/\.jpg"\s/g, '.jpg?time=' + new Date().getTime() + '" ');
                line = line.replace(/image_position/g, position);
                line = line.replace(/legend/g, legend);
                line = line.replace(/icon-check-empty/g, cover);
                line = line.replace(/<tbody>/gi, "");
                line = line.replace(/<\/tbody>/gi, "");
                if (shops != false)
                {
                    $.each(shops, function (key, value) {
                        if (value == 1)
                            line = line.replace('id="' + key + '' + id + '"', 'id="' + key + '' + id + '" checked=checked');
                    });
                }
                $("#imageList").append(line);
            }

            $(document).ready(function () {
        {/literal}
        {foreach from=$images item=image}
                assoc = {literal}"{"{/literal};
            {if $shops}
                {foreach from=$shops item=shop}
                assoc += '"{$shop.id_shop}" : {if $image->isAssociatedToShop($shop.id_shop)}1{else}0{/if},';
                {/foreach}
            {/if}
                if (assoc != {literal}"{"{/literal})
                {
                    assoc = assoc.slice(0, -1);
                    assoc += {literal}"}"{/literal};
                    assoc = jQuery.parseJSON(assoc);
                } else
                    assoc = false;
                imageLine({$image->id}, "{$image->getExistingImgPath()}", {$image->position}, "{if $image->cover}icon-check-sign{else}icon-check-empty{/if}", assoc, "{$image->legend[$default_language]|escape:'htmlall'}"); {*Variable contains css and html content, escape not required*}
        {/foreach}
        {literal}
                var originalOrder = false;

                $("#imageTable").tableDnD(
                        {dragHandle: 'dragHandle',
                            onDragClass: 'myDragClass',
                            onDragStart: function (table, row) {
                                originalOrder = $.tableDnD.serialize();
                                reOrder = ':even';
                                if (table.tBodies[0].rows[1] && $('#' + table.tBodies[0].rows[1].id).hasClass('alt_row'))
                                    reOrder = ':odd';
                                $(table).find('#' + row.id).parent('tr').addClass('myDragClass');
                            },
                            onDrop: function (table, row) {
                                if (originalOrder != $.tableDnD.serialize()) {
                                    current = $(row).attr("id");
                                    stop = false;
                                    image_up = "{";
                                    $("#imageList").find("tr").each(function (i) {
                                        $("#td_" + $(this).attr("id")).html('<div class="dragGroup"><div class="positions">' + (i + 1) + '</div></div>');
                                        if (!stop || (i + 1) == 2)
                                            image_up += '"' + $(this).attr("id") + '" : ' + (i + 1) + ',';
                                    });
                                    image_up = image_up.slice(0, -1);
                                    image_up += "}";
                                    updateImagePosition(image_up);
                                }
                            }
                        });
                /**
                 * on success function
                 */
                function afterDeleteProductImage(data)
                {
                    data = $.parseJSON(data);
                    if (data)
                    {
                        cover = 0;
                        id = data.content.id;
                        if (data.status == 'ok')
                        {
                            if ($("#" + id + ' .covered').hasClass('icon-check-sign'))
                                cover = 1;
                            $("#" + id).remove();
                        }
                        if (cover)
                            $("#imageTable tr").eq(1).find(".covered").addClass('icon-check-sign');
                        $("#countImage").html(parseInt($("#countImage").html()) - 1);
                        refreshImagePositions($("#imageTable"));
                        showSuccessMessage(data.confirmations);

                        if (parseInt($("#countImage").html()) <= 1)
                            $('#caption_selection').addClass('hidden');
                    }
                }

                $('.delete_product_image').die().live('click', function (e)
                {
                    e.preventDefault();
                    id = $(this).parent().parent().attr('id');
                    if (confirm("{/literal}{l s='Are you sure?' js=1 mod='kbbookingcalendar'}{literal}"))
                        doAdminAjax({
                            "action": "deleteProductImage",
                            "id_image": id,
                            "id_product": {/literal}{$id_product}{literal},
                            "id_category": {/literal}{$id_category_default}{literal},
                            "token": "{/literal}{$admin_product_token|escape:'html':'UTF-8'}{literal}",
                            "tab": "AdminProducts",
                            "ajax": 1}, afterDeleteProductImage
                                );
                });

                $('.covered').die().live('click', function (e)
                {
                    e.preventDefault();
                    id = $(this).parent().parent().parent().attr('id');
                    $("#imageList .cover i").each(function (i) {
                        $(this).removeClass('icon-check-sign').addClass('icon-check-empty');
                    });
                    $(this).removeClass('icon-check-empty').addClass('icon-check-sign');

                    if (current_shop_id != 0)
                        $('#' + current_shop_id + id).attr('check', true);
                    else
                        $(this).parent().parent().parent().children('td input').attr('check', true);
                    doAdminAjax({
                        "action": "UpdateCover",
                        "id_image": id,
                        "id_product": {/literal}{$id_product}{literal},
                        "token": "{/literal}{$admin_product_token|escape:'html':'UTF-8'}{literal}",
                        "controller": "AdminProducts",
                        "ajax": 1}
                    );
                });

                $('.image_shop').die().live('click', function ()
                {
                    active = false;
                    if ($(this).attr("checked"))
                        active = true;
                    id = $(this).parent().parent().attr('id');
                    id_shop = $(this).attr("id").replace(id, "");
                    doAdminAjax(
                            {
                                "action": "UpdateProductImageShopAsso",
                                "id_image": id,
                                "id_product": id_product,
                                "id_shop": id_shop,
                                "active": active,
                                "token": "{/literal}{$token|escape:'html':'UTF-8'}{literal}",
                                "tab": "AdminProducts",
                                "ajax": 1
                            });
                });
                function refreshImagePositions(imageTable)
                {
                    var reg = /_[0-9]$/g;
                    var up_reg = new RegExp("imgPosition=[0-9]+&");

                    imageTable.find("tbody tr").each(function (i, el) {
                        $(el).find("td.positionImage").html(i + 1);
                    });
                    imageTable.find("tr td.dragHandle a:hidden").show();
                    imageTable.find("tr td.dragHandle:first a:first").hide();
                    imageTable.find("tr td.dragHandle:last a:last").hide();
                }
                function updateImagePosition(json)
                {
                    doAdminAjax(
                            {
                                "action": "updateImagePosition",
                                "json": json,
                                "token": "{/literal}{$admin_product_token|escape:'html':'UTF-8'}{literal}",
                                "tab": "AdminProducts",
                                "ajax": 1
                            });
                }

                function delQueue(id)
                {
                    $("#img" + id).fadeOut("slow");
                    $("#img" + id).remove();
                }


                $('.fancybox').fancybox();
            });
            // if (tabs_manager.allow_hide_other_languages)
            //   hideOtherLanguage(default_language);*}
        {/literal}
    </script>
</div>
</div>
{/if}
{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2019 Knowband
* @license   see file: LICENSE.txt
*
*}