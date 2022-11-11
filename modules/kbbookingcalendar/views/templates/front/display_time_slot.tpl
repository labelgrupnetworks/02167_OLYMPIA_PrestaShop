{if !empty($kb_time_slot)}
    <div class="form-group col-lg-7 kb-timeslot-block" style="">
        <label class="control-label" style="    text-align: left;">
            {l s='Time Slot' mod='kbbookingcalendar' }
        </label>
        <select name="kb_checkin_time_slot" class="form-control form-control-select">
            {foreach $kb_time_slot as $key=> $slots}
                <option value="{$slots['from_time']} - {$slots['to_time']}" data-display-price="{if isset($slots['price_with_tax'])}{Tools::displayPrice(Tools::convertPrice($slots['price_with_tax'])|string_format:"%.2f")}{else}{Tools::displayPrice(Tools::convertPrice($slots['price'])|string_format:"%.2f")}{/if}" data-price="{Tools::convertPrice($slots['price'])|string_format:"%.2f"}">{$slots['from_time']} - {$slots['to_time']} {if isset($slots['price_with_tax'])}({Tools::displayPrice(Tools::convertPrice($slots['price_with_tax'])|string_format:"%.2f")}){else}({Tools::displayPrice(Tools::convertPrice($slots['price'])|string_format:"%.2f")}){/if}</option>
            {/foreach}
        </select>
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