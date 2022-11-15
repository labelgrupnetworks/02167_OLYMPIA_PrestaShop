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
{extends file='module:onepagecheckoutps/views/templates/front/checkout/vertical.tpl'}

{block name='checkout_steps'}
    <div id="opc_steps">
        {block name='checkout_steps_content'}
            <div id="opc_steps_content">
                {include file='module:onepagecheckoutps/views/templates/front/checkout/_partials/express_checkout.tpl'}

                {if $OPC.General.isLogged or !$OPC.General.forceCustomerRegistrationLogin}
                    {if !$OPC.General.isVirtualCart}
                        {assign var="nameNextStepAddresses" value='shipping'}
                        {assign var="nameBackStepPayment" value='shipping'}

                        {assign var="labelBackStepPayment" value={l s='Return to Shipping' mod='onepagecheckoutps'}}
                        {assign var="labelNextStepAddresses" value={l s='Go to Shipping' mod='onepagecheckoutps'}}
                    {else}
                        {assign var="nameNextStepAddresses" value='payment'}
                        {assign var="nameBackStepPayment" value='addresses'}

                        {assign var="labelBackStepPayment" value={l s='Return to Addresses' mod='onepagecheckoutps'}}
                        {assign var="labelNextStepAddresses" value={l s='Go to Payment' mod='onepagecheckoutps'}}
                    {/if}

                    {include
                        file = 'module:onepagecheckoutps/views/templates/front/checkout/steps/step.tpl'
                        name = 'my_account'
                        title = {l s='Contact information' mod='onepagecheckoutps'}
                        accordion = false
                        render = $stepMyAccountRendered
                        nameNextStep = 'addresses'
                        labelNextStep = {l s='Go to Addresses' mod='onepagecheckoutps'}
                    }
                    {include
                        file = 'module:onepagecheckoutps/views/templates/front/checkout/steps/step.tpl'
                        name = 'addresses'
                        title = {l s='Addreses' mod='onepagecheckoutps'}
                        accordion = false
                        nameBackStep = 'my_account'
                        labelBackStep = {l s='Return to Information' mod='onepagecheckoutps'}
                        nameNextStep = {$nameNextStepAddresses|escape:'htmlall':'UTF-8'}
                        labelNextStep = {$labelNextStepAddresses|escape:'htmlall':'UTF-8'}
                    }
                    {if !$OPC.General.isVirtualCart}
                        {include
                            file = 'module:onepagecheckoutps/views/templates/front/checkout/steps/step.tpl'
                            name = 'shipping'
                            title = {l s='Shipping method' mod='onepagecheckoutps'}
                            accordion = false
                            nameBackStep = 'addresses'
                            labelBackStep = {l s='Return to Addresses' mod='onepagecheckoutps'}
                            nameNextStep = 'payment'
                            labelNextStep = {l s='Go to Payment' mod='onepagecheckoutps'}
                        }
                    {/if}
                    {include
                        file = 'module:onepagecheckoutps/views/templates/front/checkout/steps/step.tpl'
                        name = 'payment'
                        title = {l s='Payment method' mod='onepagecheckoutps'}
                        accordion = false
                        nameBackStep = {$nameBackStepPayment|escape:'htmlall':'UTF-8'}
                        labelBackStep = {$labelBackStepPayment|escape:'htmlall':'UTF-8'}
                    }
                {else}
                    {include
                        file = 'module:onepagecheckoutps/views/templates/front/checkout/steps/step.tpl'
                        name = 'my_account'
                        accordion = false
                        render = $stepMyAccountRendered
                    }
                {/if}
            </div>
        {/block}
    </div>
{/block}
