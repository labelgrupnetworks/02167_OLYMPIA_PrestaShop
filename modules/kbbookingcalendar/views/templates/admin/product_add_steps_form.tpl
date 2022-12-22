{if !isset($smarty.get.product_type) && !isset($smarty.get.id_booking_product) && isset($smarty.get.addkb_booking_product)}
    <div class="panel">
        <div>
            <form method="post" action="{$form_url}" class="defaultForm form-horizontal">
                <div class="form-wrapper">
                    <div class="form-group">
                        <label class="control-label col-lg-3">
                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Select Type of Product' mod='kbbookingcalendar'}">
                                {l s='Select type of Product' mod='kbbookingcalendar'}
                            </span>
                        </label>
                        <div class="col-lg-9">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="product_type" id="appointment" value="appointment">
                                    {l s='Appointment' mod='kbbookingcalendar'}
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="product_type" id="daily_rental" value="daily_rental">
                                    {l s='Daily Rental' mod='kbbookingcalendar'}
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="product_type" id="hotel_booking" value="hotel_booking">
                                    {l s='Hotel Booking' mod='kbbookingcalendar'}
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="product_type" id="hourly_rental" value="hourly_rental">
                                    {l s='Hourly Rental' mod='kbbookingcalendar'}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <button type="submit" value="1" id="add_product_type_submit_btn" name="add_product_type_submit_btn" class="btn btn-default pull-right">
                        <i class="process-icon-next"></i> {l s='Next' mod='kbbookingcalendar'}
                    </button>
                </div>
            </form>
        </div>
    </div>
{elseif isset($smarty.get.product_type) || isset($product_type)}
    <div class="panel">
        <h3><i class="icon-cog"></i>&nbsp; {l s='Add Product' mod='kbbookingcalendar'}</h3>
        <div class="productTabs" style="clear: both;">
            <ul class="tab nav nav-tabs">
                <li class="tab-row kb_fs_form_tab">
                    <a class="tab-page" id="kb_add_product_form_tab_general" onclick="return displayKbProductTab('general');">
                        <i class="icon-cog"></i> {l s='General Information' mod='kbbookingcalendar'} 
                        <i class="icon-exclamation-circle kb-pc-error-icon" style="display: none;"></i>
                    </a>
                </li>
                <li class="tab-row kb_fs_form_tab">
                    <a class="tab-page" id="kb_add_product_form_tab_price" onclick="return displayKbProductTab('price');">
                        <i class="icon-cog"></i> {l s='Booking Information' mod='kbbookingcalendar'} 
                        <i class="icon-exclamation-circle kb-pc-error-icon" style="display: none;"></i>
                    </a>
                </li>
                <li class="tab-row kb_fs_form_tab">
                    <a class="tab-page" id="kb_add_product_form_tab_location" onclick="return displayKbProductTab('location');">
                        <i class="icon-cog"></i> {l s='Location' mod='kbbookingcalendar'} 
                        <i class="icon-exclamation-circle kb-pc-error-icon" style="display: none;"></i>
                    </a>
                </li>
                <li class="tab-row kb_fs_form_tab">
                    <a class="tab-page" id="kb_add_product_form_tab_image" onclick="return displayKbProductTab('image');">
                        <i class="icon-cog"></i> {l s='Image' mod='kbbookingcalendar'} 
                        <i class="icon-exclamation-circle kb-pc-error-icon" style="display: none;"></i>
                    </a>
                </li>
                <li class="tab-row kb_fs_form_tab">
                    <a class="tab-page" id="kb_add_product_form_tab_days" onclick="return displayKbProductTab('days');">
                        <i class="icon-cog"></i> {l s='Date/Time' mod='kbbookingcalendar'} 
                        <i class="icon-exclamation-circle kb-pc-error-icon" style="display: none;"></i>
                    </a>
                </li>
                {if (isset($smarty.get.product_type) && ($smarty.get.product_type == 'hotel_booking'))  || (isset($product_type) && ($product_type == 'hotel_booking'))}
                    <li class="tab-row kb_fs_form_tab">
                        <a class="tab-page" id="kb_add_product_form_tab_rooms" onclick="return displayKbProductTab('rooms');">
                            <i class="icon-cog"></i> {l s='Rooms' mod='kbbookingcalendar'} 
                            <i class="icon-exclamation-circle kb-pc-error-icon" style="display: none;"></i>
                        </a>
                    </li>
                {/if}
                {if (isset($smarty.get.product_type) && ($smarty.get.product_type == 'hotel_booking' || $smarty.get.product_type == 'hourly_rental' || $smarty.get.product_type == 'daily_rental'))  || (isset($product_type) && ($product_type == 'hotel_booking' || $product_type == 'hourly_rental' || $product_type == 'daily_rental'))}
                    <li class="tab-row kb_fs_form_tab">
                        <a class="tab-page" id="kb_add_product_form_tab_facilities" onclick="return displayKbProductTab('facilities');">
                            <i class="icon-cog"></i> {l s='Facilities' mod='kbbookingcalendar'} 
                            <i class="icon-exclamation-circle kb-pc-error-icon" style="display: none;"></i>
                        </a>
                    </li>
                {/if}
            </ul>
        </div>
        {$kb_form nofilter} {* Variable contains HTML/CSS/JSON, escape not required *}
        {if isset($image_tpl)}
            {include file={$image_tpl}}
        {else}
            <div id="kbproduct-images" class="panel" style="display: none;">
                <div class="alert alert-warning">
                    {l s='You must save this product before adding images.' mod='kbbookingcalendar'}
                </div>
            </div>
        {/if}
        {include file={$datetime_tpl}}
        {include file={$weekday_tpl}}
        {include file={$rooms_tpl}}
        {include file={$facilities_tpl}}
        <div class="panel-footer">
            <input type="hidden" name="submitKbBKProductForm" value="1" />
            <button type="submit" class="btn btn-default pull-right" onclick="validateKbProductForm('submitnostay');" name="submitBKAddproduct"><i class="process-icon-save"></i> {l s='Save' mod='kbbookingcalendar'}</button>
            <button type="submit" class="btn btn-default pull-right" onclick="validateKbProductForm('submitandstay');" name="submitBKAddproductAndStay"><i class="process-icon-save"></i> {l s='Save And Stay' mod='kbbookingcalendar'}</button>
        </div>
    </div>
{/if}

{if !$id_booking_product}
    <div class="alert alert-warning kb-room-add-warning-alert" style="display: none;">
        {l s='You must save this product before adding rooms.' mod='kbbookingcalendar'}
    </div>
{/if}
<script type="text/javascript">
    var currentFormTab = '{if isset($smarty.post.currentFormTab)}{$smarty.post.currentFormTab|escape:'quotes':'UTF-8'}{else}general{/if}';
        var no_match_err = "{l s='No Matches Found' mod='kbbookingcalendar'}";
        var select_placeholder = "{l s='Select' mod='kbbookingcalendar'}";
        var no_country_selected = "{l s='No Country/Region selected' mod='kbbookingcalendar'}";
        var check_for_all = "{l s='Kindly check for all available languages' mod='kbbookingcalendar'}";
        var empty_field = "{l s='Field cannot be empty' mod='kbbookingcalendar'}";
        var currentText = '{l s='Now'  mod='kbbookingcalendar' js=1}';
        var closeText = '{l s='Done'  mod='kbbookingcalendar' js=1}';
        var timeonlytext = '{l s='Choose Time'  mod='kbbookingcalendar' js=1}';
        var admin_form_url = "{$form_url}";
        var remove_string = "{l s='Remove' mod='kbbookingcalendar'}";
        var kb_facility_map_success = "{l s='Facilities successfully mapped.' mod='kbbookingcalendar'}";
        var kb_facility_remove_success = "{l s='Facility successfully removed.' mod='kbbookingcalendar'}";
        var kb_facility_remove_error = "{l s='Facility cannot be removed.' mod='kbbookingcalendar'}";
        var no_record_found = "{l s='No records found' mod='kbbookingcalendar'}";
    {if !$id_booking_product}
        var is_kb_object_created = true;
    {/if}
        var KbcurrentToken = "{$KbcurrentToken}";
        var facilities_admin_controller = "{$facilities_admin_controller}";
</script>

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