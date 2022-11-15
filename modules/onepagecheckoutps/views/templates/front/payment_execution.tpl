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
{extends file=$layout}

{block name='content'}
    {if isset($payment_options)}
        <div id="onepagecheckoutps">
            <div class="loading_big">
                <div class="loader">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
            </div>

            <div id="opc_payment_compability">
                <div class="ps-shown-by-js">
                    <form action="{$urls.pages.order|escape:'htmlall':'UTF-8'}">
                        <button id="choose_another_payment" class="btn btn-md btn-secondary" type="submit">
                            <i class="fa-pts fa-pts-exchange"></i>
                            {l s='Choose another payment method' mod='onepagecheckoutps'}
                        </button>
                    </form>
                </div>

                {foreach from=$payment_options item="option" key="id_option"}
                    {if $payment_options|count eq 1 or $option->getCallToActionText() eq $payment_option_selected}
                        <div id="payment-option-{$id_option|escape:'htmlall':'UTF-8'}-container" class="payment-option">
                            <input class="hidden" id="payment-option-{$id_option|escape:'htmlall':'UTF-8'}" data-module-name="{$option->getModuleName()|escape:'htmlall':'UTF-8'}" name="payment-option" type="radio" checked />

                            {if $option->getForm()}
                                {$option->getForm() nofilter}
                            {else}
                                {if $option->getAction() neq ''}
                                    <form id="payment-form" method="POST" action="{$option->getAction() nofilter}">
                                        {foreach from=$option->getInputs() item=input}
                                            <input type="{$input.type|escape:'htmlall':'UTF-8'}" name="{$input.name|escape:'htmlall':'UTF-8'}" value="{$input.value|escape:'htmlall':'UTF-8'}">
                                        {/foreach}
                                        <div class="ps-shown-by-js">
                                            <button id="placer_order_payment" class="button btn-primary btn-block" type="submit">
                                                {l s='Checkout' mod='onepagecheckoutps'}
                                            </button>
                                        </div>
                                    </form>
                                {/if}
                            {/if}
                        </div>
                        <div id="payment-option-{$id_option|escape:'htmlall':'UTF-8'}-additional-information" class="definition-list additional-information">
                            {$option->getAdditionalInformation() nofilter}
                        </div>
                    {/if}
                {/foreach}
            </div>
        </div>
    {/if}
{/block}