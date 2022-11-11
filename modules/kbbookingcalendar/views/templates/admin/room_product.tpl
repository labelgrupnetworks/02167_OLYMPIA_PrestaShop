<div id="fieldset_hotelbookingroom" class="panel" style="display:none;">
    {if $id_booking_product}
        <div>
            <a href="{$form_url}&manageProductRoom&id_booking_product={$id_booking_product}&addProductRoom=true" class='btn btn-default pull-right'><i class="icon-plus"></i>&nbsp; {l s='Add Room' mod='kbbookingcalendar'}</a>
        </div>
        <div style="clear:both;padding-top: 15px;">
            {$room_listing_table}
        </div>
    {else}
        <div class="alert alert-warning kb-facility-add-warning-alert">
            {l s='You must save this product before adding rooms.' mod='kbbookingcalendar'}
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