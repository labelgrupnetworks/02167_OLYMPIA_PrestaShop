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

{if isset($paymentOptions)}
    <div id="payment_method_container" class="payment-options {if $isFree}hidden-xs-up{/if}">
        <input type="hidden" name="order_total" value="{$orderTotal|escape:'htmlall':'UTF-8'}" />
        <input type="hidden" name="order_total_label" value="{$orderTotalLabel|escape:'htmlall':'UTF-8'}" />
        {foreach from=$paymentOptions item="moduleOptions" key="moduleName"}
            {foreach from=$moduleOptions item="option"}
                <div class="module_payment_container {if $defaultPaymentMethod eq $option.module_name}selected{/if}">
                    <div id="{$option.id|escape:'htmlall':'UTF-8'}-container" class="payment-option">
                        {if isset($option.paymentFee) and $option.paymentFee|count > 0}
                            {foreach from=$option.paymentFee item="value" key="name"}
                                <input type="hidden" name="{$name|escape:'htmlall':'UTF-8'}" value="{$value|escape:'htmlall':'UTF-8'}" />
                            {/foreach}
                        {/if}
                        <div class="payment_input {if !$isLogged}hidden{/if}">
                            <input type="radio" id="{$option.id|escape:'htmlall':'UTF-8'}" name="payment-option"
                                class="payment_radio{if $option.binary} binary{/if}"
                                value="{$moduleName|escape:'htmlall':'UTF-8'}"
                                data-module-name="{$option.module_name|escape:'htmlall':'UTF-8'}"
                                {if $defaultPaymentMethod eq $option.module_name or $paymentOptions|count == 1}checked="true"{/if}
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
                    {if $isLogged}
                        {if $showPaymentDetail or in_array($moduleName, $paymentListForceShowDetails)}
                            {if $option.additionalInformation}
                                <div id="{$option.id|escape:'htmlall':'UTF-8'}-additional-information"
                                    class="js-additional-information additional-information" style="display: none;">
                                    {$option.additionalInformation nofilter}
                                </div>
                            {/if}
                        {/if}
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