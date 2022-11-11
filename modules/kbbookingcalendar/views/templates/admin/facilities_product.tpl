<div id="fieldset_hotelbookingfacilities" class="panel" style="display:none;">
    {if $id_booking_product}
        <div class="form-wrapper">
            <div>
                <div class="alert alert-success kb-display-success-facilities" style="display: none;">
                </div>
                <script>
                    var kb_available_facilities_data = '{$availibleFacilities|@json_encode nofilter}';  {* Variable contains HTML/CSS/JSON, escape not required *}
                </script>
                <input type="hidden" name="kb_booking_id" value="{$id_booking_product}">
                <a id="kb-add-product-facilities-link" class="btn btn-default pull-right" title="{l s='Add Facilities' mod='kbbookingcalendar'}" href="javascript:addProductKbFacilities();"><i class="icon-plus"></i>&nbsp;{l s='Add Facilities' mod='kbbookingcalendar'}</a>
            </div>
            <div style="clear: both;">
                <div class="table-responsive-row clearfix">
                    <input type="hidden" name="kb-added-facilities-data" value="{if isset($mapped_facilities_product) && !empty($mapped_facilities_product)}{foreach $mapped_facilities_product as $product}{$product['id_facilities']|escape:'htmlall':'UTF-8'}-{/foreach}{/if}">
                    <table id="table-product-facilities-list" class="table">
                        <thead>
                            <tr class="nodrag nodrop">
                                <th class=" left">
                                    <span class="title_box">
                                        {l s='ID' mod='kbbookingcalendar'}
                                    </span>
                                </th>
                                <th class=" left">
                                    <span class="title_box">
                                        {l s='Facilities' mod='kbbookingcalendar'}
                                    </span>
                                </th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody id="kb-append-facilities-data">
                            {if !empty($mapped_facilities_product)}
                                {foreach $mapped_facilities_product as $facilities}
                                    <tr id='kb-already-product-facilites_{$facilities['id_facilities']}'>
                                        <td>{$facilities['id_facilities']}</td>
                                        <td>{$facilities['name']}</td>
                                        <td>
                                            <a class="btn btn-default" href="javascript:removeProductFacilities({$facilities['id_facilities']});">
                                                <i class="icon-trash"></i>&nbsp;{l s='Remove' mod='kbbookingcalendar'}
                                            </a>
                                        </td>
                                    </tr>
                                {/foreach}
                            {else}
                                <tr class="kb-no-record-list">
                                    <td class="list-empty" colspan="7">
                                        <div class="list-empty-msg">
                                            <i class="icon-warning-sign list-empty-icon"></i>
                                            {l s='No records found' mod='kbbookingcalendar'}
                                        </div>
                                    </td>
                                </tr>
                            {/if}
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

        <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="kbaddFacilitiesBlockModel" class="modal fade" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                        <h4 class="modal-title">{l s='Add Facilities' mod='kbbookingcalendar'}</h4>
                    </div>
                    <form id="request_manf_form" action="#" method="post" class="defaultForm form-horizontal">
                        <div class="modal-body">
                            <div class="">
                                <div class="form-wrapper kb-productfacilities-dialogue-form">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th class="fixed-width-xs"><span class="title_box">{l s='Selected' mod='kbbookingcalendar'}</span></th>
                                                <th><span class="title_box">{l s='ID' mod='kbbookingcalendar'}</span></th>
                                                <th class="fixed-width-xs"><span class="title_box">{l s='Facilities' mod='kbbookingcalendar'}</span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {if !empty($availibleFacilities)}
                                                {foreach $availibleFacilities as $facilities}
                                                    <tr id="kb-added-product-facilites_{$facilities['id_facilities']}">
                                                        <td class="fixed-width-xs">
                                                            <input type="checkbox" class="kbaddFacilitiesCheckBox" name="selected_add_facilities[]" value="{$facilities['id_facilities']}"> 
                                                        </td>
                                                        <td class="fixed-width-xs">
                                                            {$facilities['id_facilities']}
                                                        </td>
                                                        <td class="fixed-width-xs">{$facilities['name']}</td>
                                                    </tr>
                                                {/foreach}
                                            {else}
                                            {/if}
                                            <tr id="kb-no-available-facilities" style="display: none;">
                                                <td class="list-empty" colspan="7">
                                                    <div class="alert alert-warning">
                                                        {l s='No Facilities available' mod='kbbookingcalendar'}
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="process-icon-cancel"></i>{l s='Cancel' mod='kbbookingcalendar'}</button>
                            <button type="button" name="submitAddKbFacilitiesProduct" class="btn btn-default pull-right" ><i class="process-icon-save"></i> {l s='Submit' mod='kbbookingcalendar'}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    {else}
        <div class="alert alert-warning kb-facility-add-warning-alert">
            {l s='You must save this product before adding facilities.' mod='kbbookingcalendar'}
        </div>
    {/if}
</div>
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