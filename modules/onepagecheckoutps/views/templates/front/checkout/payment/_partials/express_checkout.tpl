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

<div class="row">
    <div class="col-xs-12">
        <p>
            {l s='You are going to finalize the purchase with' mod='onepagecheckoutps'}&nbsp;{$ECpaymentName|escape:'htmlall':'UTF-8'}.
        </p>
        <p>
            <img src="{$ECpaymentLogoUrl|escape:'htmlall':'UTF-8'}" alt="{$ECpaymentName|escape:'htmlall':'UTF-8'}"/>
        </p>
        <p>
            {l s='If you want to change your payment option and address in' mod='onepagecheckoutps'}&nbsp;{$ECpaymentName|escape:'htmlall':'UTF-8'}&nbsp;{l s='or select another payment method, click' mod='onepagecheckoutps'}&nbsp;<a href="{$ECsesionResetUrl|escape:'htmlall':'UTF-8'}">{l s='here' mod='onepagecheckoutps'}</a>
        </p>

        {if isset($paymentOptions)}
            <div id="payment_method_container" class="payment-options">
                <input type="hidden" name="order_total" value="{$orderTotal|escape:'htmlall':'UTF-8'}" />
                <input type="hidden" name="order_total_label" value="{$orderTotalLabel|escape:'htmlall':'UTF-8'}" />
                {foreach from=$paymentOptions item="moduleOptions" key="moduleName"}
                    {foreach from=$moduleOptions item="option"}
                        {if $option.module_name neq $ECmoduleName}
                            {continue}
                        {/if}

                        <div class="module_payment_container selected">
                            <div id="{$option.id|escape:'htmlall':'UTF-8'}-container" class="payment-option">
                                {if $option.paymentFee|count > 0}
                                    {foreach from=$option.paymentFee item="value" key="name"}
                                        <input type="hidden" name="{$name|escape:'htmlall':'UTF-8'}" value="{$value|escape:'htmlall':'UTF-8'}" />
                                    {/foreach}
                                {/if}
                                <div class="payment_input {if !$isLogged}hidden{/if}">
                                    <input type="radio" id="{$option.id|escape:'htmlall':'UTF-8'}" name="payment-option"
                                        class="payment_radio hidden"
                                        value="{$moduleName|escape:'htmlall':'UTF-8'}"
                                        data-module-name="{$option.module_name|escape:'htmlall':'UTF-8'}"
                                        checked="true"
                                    />
                                </div>
                                {if !empty($option.logo) and $showPaymentImage}
                                    <div class="payment_image">
                                        <img src="{$option.logo|escape:'htmlall':'UTF-8'}"
                                            title="{$option.call_to_action_text|escape:'htmlall':'UTF-8'}"
                                            class="img-thumbnail {$moduleName|escape:'htmlall':'UTF-8'}"
                                        />
                                    </div>
                                {/if}
                                <div class="payment_content">
                                    <span>
                                        {$option.call_to_action_text nofilter}
                                    </span>
                                </div>
                            </div>
                            <div id="pay-with-{$option.id|escape:'htmlall':'UTF-8'}-form"
                                class="js-payment-option-form ps-hidden">
                                {if $option.form}
                                    {$option.form nofilter}
                                {else}
                                    <form id="payment-form" method="POST" action="{$option.action nofilter}">
                                        {foreach from=$option.inputs item=input}
                                            <input type="{$input.type|escape:'htmlall':'UTF-8'}"
                                                name="{$input.name|escape:'htmlall':'UTF-8'}"
                                                value="{$input.value|escape:'htmlall':'UTF-8'}">
                                        {/foreach}
                                        <button style="display:none" id="pay-with-{$option.id|escape:'htmlall':'UTF-8'}"
                                            type="submit"></button>
                                    </form>
                                {/if}
                            </div>
                        </div>
                    {/foreach}
                {/foreach}
            </div>
        {/if}

        {include file='module:onepagecheckoutps/views/templates/front/checkout/payment/_partials/condition.tpl'}
        {include file='module:onepagecheckoutps/views/templates/front/checkout/payment/_partials/payment_confirmation.tpl'}

        {hook h='displayPaymentByBinaries'}
    </div>
</div>