{**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * @author    PresTeamShop SAS (Registered Trademark) <info@presteamshop.com>
 * @copyright 2011-2022 PresTeamShop SAS, All rights reserved.
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @category  PrestaShop
 * @category  Module
 *}

{block name='step_carrier'}

<script type="text/javascript">
    var is_necessary_postcode = Boolean({if isset($is_necessary_postcode)}{$is_necessary_postcode|escape:'htmlall':'UTF-8'}{/if});
    var is_necessary_city = Boolean({if isset($is_necessary_city)}{$is_necessary_city|escape:'htmlall':'UTF-8'}{/if});
    var id_carrier_selected = '{if isset($delivery_option)}{$delivery_option|escape:'htmlall':'UTF-8'}{/if}';

    {if isset($deliverydate_position) && isset($deliverydate_reason)}
        var deliverydate_position = "{$deliverydate_position|escape:"htmlall":'UTF-8'}";
        var deliverydate_reason = "{$deliverydate_reason|escape:"htmlall":'UTF-8'}";
    {/if}

    {literal}
        if (!OnePageCheckoutPS.IS_LOGGED && is_necessary_postcode)
            $('div#onepagecheckoutps')
                .off('blur', 'input#delivery_postcode')
                .on('blur', 'input#delivery_postcode', function() {
                    Address.updateAddress({object: 'delivery', load_carriers: true})
                });
        if (!OnePageCheckoutPS.IS_LOGGED && is_necessary_city)
            $('div#onepagecheckoutps')
                .off('blur', 'input#delivery_city')
                .on('blur', 'input#delivery_city', function() {
                    Address.updateAddress({object: 'delivery', load_carriers: true})
                });
    {/literal}
</script>

    {if isset($is_virtual_cart) && $is_virtual_cart}
        <input id="input_virtual_carrier" class="hidden" type="hidden" name="id_carrier" value="0" />
    {else}
        {if ($hasError)}
            <p class="alert alert-warning">
                {foreach from=$errors key=k item="error" name="f_errors"}
                    -&nbsp;{$error|escape:'htmlall':'UTF-8'}
                    {if !$smarty.foreach.f_errors.last}<br/><br/>{/if}
                {/foreach}
            </p>

			<button class="btn btn-info pull-right btn-sm" type="button" onclick="Address.updateAddress({ldelim}object: 'delivery', update_cart: true, load_carriers: true{rdelim});">
				<i class="fa-pts fa-pts-refresh"></i>
				{l s='Reload' mod='onepagecheckoutps'}
			</button>
            <div class="clear"></div>
        {else}
            <div id="hook-display-before-carrier">
                {$hookDisplayBeforeCarrier nofilter}
            </div>

            <div id="shipping_container">
                {if $delivery_options|count}
                    <div id="js-delivery" class="delivery-options">
                        {foreach from=$delivery_options item=carrier key=carrier_id}
                            {if $CONFIGS.OPC_SHIPPING_COMPATIBILITY and $delivery_option neq $carrier_id}
                                {continue}
                            {/if}
                            <div class="delivery-option delivery_option_{$carrier.id|escape:'htmlall':'UTF-8'}{if $delivery_option == $carrier_id} selected alert alert-info{/if}">
                                <div class="carrier-item-content">
                                    <div class="delivery_radio_content">
                                        <input class="delivery_option_radio not_unifrom not_uniform {if $CONFIGS.OPC_SHIPPING_COMPATIBILITY}hidden{/if}" type="radio" name="delivery_option[{$id_address|escape:'htmlall':'UTF-8'}]" id="delivery_option_{$carrier.id|escape:'htmlall':'UTF-8'}" value="{$carrier_id|escape:'htmlall':'UTF-8'}" {if $delivery_option == $carrier_id}checked{/if} />
                                    </div>
                                        {if ($CONFIGS.OPC_SHOW_IMAGE_CARRIER)}
                                            <div class="delivery_option_logo">
                                            {if $carrier.logo}
                                                <img src="{$carrier.logo|escape:'htmlall':'UTF-8'}" alt="{$carrier.name|escape:'htmlall':'UTF-8'}" class="img-thumbnail"/>
                                            {else}
                                                <img src="{$ONEPAGECHECKOUTPS_IMG|escape:'htmlall':'UTF-8'}shipping.png" alt="{$carrier.name|escape:'htmlall':'UTF-8'}" class="img-thumbnail"/>
                                            {/if}
                                        {else}
                                            <div class="delivery_option_logo wo_image">
                                            <div class="delivery_option_title">{$carrier.name|escape:'htmlall':'UTF-8'}</div>
                                            {if !$CONFIGS.OPC_SHOW_IMAGE_CARRIER and !$CONFIGS.OPC_SHOW_DESCRIPTION_CARRIER}
                                                <div class="delivery_option_price">
                                                    ({$carrier.price|escape:'htmlall':'UTF-8'})
                                                </div>
                                            {/if}
                                        {/if}

                                        {if $carrier.external_module_name != ''}
                                            <input type="hidden" class="module_carrier" name="{$carrier.external_module_name|escape:'htmlall':'UTF-8'}" value="delivery_option_{$id_address|escape:'htmlall':'UTF-8'}_{$carrier@index|escape:'htmlall':'UTF-8'}" />
                                            <input type="hidden" name="name_carrier" id="name_carrier_{$id_address|escape:'htmlall':'UTF-8'}_{$carrier@index|escape:'htmlall':'UTF-8'}" value="{$carrier.name|escape:'htmlall':'UTF-8'}" />
                                        {/if}
                                    </div>
                                    {if $CONFIGS.OPC_SHOW_IMAGE_CARRIER || $CONFIGS.OPC_SHOW_DESCRIPTION_CARRIER}
                                            {if $CONFIGS.OPC_SHOW_IMAGE_CARRIER}
                                                <div class="carrier_delay">
                                                    <div class="delivery_option_title">{$carrier.name|escape:'htmlall':'UTF-8'}</div>
                                            {else}
                                                <div class="carrier_delay wo_image">
                                            {/if}
                                            {if $CONFIGS.OPC_SHOW_DESCRIPTION_CARRIER and $carrier.delay}
                                                <div class="delivery_option_delay">
                                                    {if !empty($carrier.estimate_days)}{$carrier.estimate_days|escape:'htmlall':'UTF-8'} {l s='Day(s)' mod='onepagecheckoutps'}{else}{$carrier.delay|escape:'htmlall':'UTF-8'}{/if}
                                                </div>
                                            {/if}
                                            <div class="delivery_option_price">
                                                ({$carrier.price|escape:'htmlall':'UTF-8'})
                                            </div>
                                        </div>
                                    {/if}
                                    {if $carrier.external_module_name != '' && isset($carrier.extra_info_carrier)}
                                        <div class="extra_info_carrier pull-right" style="display:{if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}block{else}none{/if}">
                                            {if not empty($carrier.extra_info_carrier)}
                                                <span>{$carrier.extra_info_carrier|escape:'htmlall':'UTF-8'}</span>
                                                <br />
                                                <a class="edit_pickup_point" onclick="Carrier.displayPopupModule_{$carrier.external_module_name|escape:'htmlall':'UTF-8'}({$carrier.id|escape:'htmlall':'UTF-8'})">{l s='Edit pickup point' mod='onepagecheckoutps'}</a>
                                            {else}
                                                <a class="select_pickup_point" onclick="Carrier.displayPopupModule_{$carrier.external_module_name|escape:'htmlall':'UTF-8'}({$carrier.id|escape:'htmlall':'UTF-8'})">{l s='Select pickup point' mod='onepagecheckoutps'}</a>
                                            {/if}
                                        </div>
                                    {/if}
                                </div>
                                <div class="row carrier-extra-content"{if $delivery_option != $carrier_id} style="display:none;"{/if}>{$carrier.extraContent nofilter}</div>
                            </div>
                        {/foreach}
                    </div>

                    {if ($recyclablePackAllowed or $gift.allowed) and not $CONFIGS.OPC_SHIPPING_COMPATIBILITY}
                        <div class="row">
                            {if $recyclablePackAllowed}
                                <div class="col-xs-12">
                                    <label for="recyclable">
                                        <input type="checkbox" name="recyclable" id="recyclable" value="1" {if $recyclable == 1}checked="checked"{/if} class="carrier_checkbox not_unifrom not_uniform"/>
                                        {l s='I agree to receive my order in recycled packaging' mod='onepagecheckoutps'}
                                    </label>
                                </div>
                            {/if}
                            {if $gift.allowed}
                                <div class="col-xs-12" id ="div-gift">
                                    <label for="gift">
                                        <input type="checkbox" name="gift" id="gift" value="1" {if $gift.isGift}checked{/if} class="carrier_checkbox not_unifrom not_uniform"/>
                                        {l s='I would like the order to be gift-wrapped.' mod='onepagecheckoutps'}
                                    </label>
                                </div>
                            {/if}
                        </div>
                    {/if}

                    {if $gift.allowed and not $CONFIGS.OPC_SHIPPING_COMPATIBILITY}
                        <div class="row">
                            <div class="col-xs-12">
                                <p id="gift_div_opc" class="textarea {if !$gift.isGift}hidden{/if}">
                                    <label for="gift_message">{l s='If you\'d like, you can add a note to the gift:' mod='onepagecheckoutps'}</label>
                                    <textarea rows="1" id="gift_message" name="gift_message" class="form-control">{$gift.message|escape:'htmlall':'UTF-8'}</textarea>
                                </p>
                            </div>
                        </div>
                    {/if}

                    {if $CONFIGS.OPC_SHIPPING_COMPATIBILITY}
                        <div class="pts-btn-secondary" id="show_carrier_embed">
                            <span>
                                <i class="fa-pts fa-pts-refresh"></i>
                                {l s='Change shipping carrier' mod='onepagecheckoutps'}
                            </span>
                        </div>
                    {/if}
                {else}
                    <p class="alert alert-danger">{l s='Unfortunately, there are no carriers available for your delivery address.' mod='onepagecheckoutps'}</p>
                {/if}

                {* compatibilidad carrierpickupstore - v4.0.4 - PresTeamShop *}
                {if $cps_message}
                    <br class="clearfix"/>
                    <p class="alert alert-warning">
                        {l s='If you want to select another carrier you must add a delivery address' mod='onepagecheckoutps'}
                    </p>
                {/if}
            </div>
            {if !$CONFIGS.OPC_SHIPPING_COMPATIBILITY}
                <div id="hook-display-after-carrier">
                    {$hookDisplayAfterCarrier nofilter}
                </div>
            {/if}

            <div id="extra_carrier"></div>
        {/if}
    {/if}
{/block}