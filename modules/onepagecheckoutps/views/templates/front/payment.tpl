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

{block name='step_payment'}
    {if $total_order <= 0}
        <span id="free_order" class="alert alert-warning col-xs-12 col-12 text-md-center">{l s='Free Order.' mod='onepagecheckoutps'}</span>
    {else}
        {hook h='displayPaymentTop'}

        {*support module: amazonpay - v1.1.4 - patworx multimedia GmbH*}
        {if isset($amazonpay_session_opc)}
            {$onepagecheckoutps->includeTpl('modules/amazonpay.tpl', []) nofilter}
        {/if}

        <input type="hidden" value="{$payment_modules_fee|json_encode|escape:'htmlall':'UTF-8'}" id="hdn-payment_modules_fee">
        <input type="hidden" value="{$payment_with_discount|json_encode|escape:'htmlall':'UTF-8'}" id="hdn-payment_with_discount">
        <div id="payment_method_container" class="payment-options">
            {foreach from=$payment_options item="module_options" key="name_module"}
                {foreach from=$module_options item="option"}
                    <div class="module_payment_container">
                        <div id="{$option.id|escape:'htmlall':'UTF-8'}-container" class="payment-option">
                            <div class="payment_input">
                                <input type="radio" id="{$option.id|escape:'htmlall':'UTF-8'}" name="payment-option" class="payment_radio not_unifrom not_uniform" value="{$name_module|escape:'htmlall':'UTF-8'}" data-module-name="{$option.module_name|escape:'htmlall':'UTF-8'}" data-force-display="{$option.force_display|escape:'htmlall':'UTF-8'}">
                                <input type="hidden" id="url_module_payment_{$option.id_module_payment|escape:'htmlall':'UTF-8'}" value="{$option.action|escape:'htmlall':'UTF-8'}">
                            </div>
                            {if !empty($option.logo) and $CONFIGS.OPC_SHOW_IMAGE_PAYMENT}
                                <div class="payment_image">
                                    <img src="{$option.logo|escape:'htmlall':'UTF-8'}" title="{$option.call_to_action_text|escape:'htmlall':'UTF-8'}" class="img-thumbnail {$name_module|escape:'htmlall':'UTF-8'}">
                                </div>
                            {/if}
                            <div class="payment_content">
                                <span>
                                    {if isset($option.title_opc)}
                                        {$option.title_opc nofilter}
                                    {else}
                                        {$option.call_to_action_text nofilter}
                                    {/if}
                                </span>
                                {if isset($option.description_opc)}
                                    <p>
                                        {$option.description_opc|escape:'htmlall':'UTF-8'}
                                    </p>
                                {/if}
                            </div>
                        </div>
                        {if $option.force_display eq 0}
                            {if $CONFIGS.OPC_SHOW_DETAIL_PAYMENT or $name_module eq 'ps_checkout'}
                                {if $option.additionalInformation}
                                    <div id="payment_content_html_{$option.id|escape:'htmlall':'UTF-8'}" class="payment_content_html hidden">
                                        <div id="{$option.id|escape:'htmlall':'UTF-8'}-additional-information"
                                            class="js-additional-information additional-information">
                                            {$option.additionalInformation nofilter}
                                        </div>
                                    </div>
                                {/if}
                            {/if}
                            <div
                                id="pay-with-{$option.id|escape:'htmlall':'UTF-8'}-form"
                                class="js-payment-option-form {if $option.id != $selected_payment_option} ps-hidden {/if}"
                            >
                                {if $option.form}
                                    {$option.form nofilter}
                                {else}
                                    <form id="payment-form" method="POST" action="{$option.action nofilter}">
                                        {foreach from=$option.inputs item=input}
                                            <input type="{$input.type|escape:'htmlall':'UTF-8'}" name="{$input.name|escape:'htmlall':'UTF-8'}" value="{$input.value|escape:'htmlall':'UTF-8'}">
                                        {/foreach}
                                        <button style="display:none" id="pay-with-{$option.id|escape:'htmlall':'UTF-8'}" type="submit"></button>
                                    </form>
                                {/if}
                            </div>
                        {/if}
                    </div>
                {/foreach}
            {foreachelse}
                <p class="alert alert-danger">
                    {l s='Unfortunately, there are no payment method available.' mod='onepagecheckoutps'}
                </p>
            {/foreach}
        </div>
    {/if}
{/block}