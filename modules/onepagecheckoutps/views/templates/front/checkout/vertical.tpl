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

{block name='checkout_main'}
    <div id="opc_main" class="opc-bootstrap-513 opc-{$OPC.General.Design.style|escape:'htmlall':'UTF-8'}">
        {if $OPC.General.Design.style eq 'steps'}
            {include file='module:onepagecheckoutps/views/templates/front/checkout/_partials/breadcrumb.tpl'}
        {/if}

        {include file='module:onepagecheckoutps/views/templates/front/checkout/cart/cart_trigger.tpl' order_value=$cart.totals.total.value}

        {block name='checkout_content'}
            <div id="opc_content" class="row">
                <div id="opc_content_main" class="col-lg-7">
                    {block name='checkout_steps'}
                        <div id="opc_steps">
                            {block name='checkout_steps_content'}
                                <div id="opc_steps_content">
                                    {include file='module:onepagecheckoutps/views/templates/front/checkout/_partials/express_checkout.tpl'}

                                    {if $OPC.General.isLogged or !$OPC.General.forceCustomerRegistrationLogin}
                                        {if !$OPC.General.isVirtualCart}
                                            {assign var="labelNextStepAddresses" value={l s='Go to Shipping' mod='onepagecheckoutps'}}
                                        {else}
                                            {assign var="labelNextStepAddresses" value={l s='Go to Payment' mod='onepagecheckoutps'}}
                                        {/if}
                                        {include
                                            file = 'module:onepagecheckoutps/views/templates/front/checkout/steps/step.tpl'
                                            name = 'my_account'
                                            title = {l s='Contact information' mod='onepagecheckoutps'}
                                            accordion = true
                                            render = $stepMyAccountRendered
                                            nameNextStep = 'addresses'
                                            labelNextStep = {l s='Go to Addresses' mod='onepagecheckoutps'}
                                        }
                                        {include
                                            file = 'module:onepagecheckoutps/views/templates/front/checkout/steps/step.tpl'
                                            name = 'addresses'
                                            title = {l s='Addreses' mod='onepagecheckoutps'}
                                            accordion = true
                                            nameNextStep = 'shipping'
                                            labelNextStep = {$labelNextStepAddresses|escape:'htmlall':'UTF-8'}
                                        }
                                        {if !$OPC.General.isVirtualCart}
                                            {include
                                                file = 'module:onepagecheckoutps/views/templates/front/checkout/steps/step.tpl'
                                                name = 'shipping'
                                                title = {l s='Shipping method' mod='onepagecheckoutps'}
                                                accordion = true
                                                nameNextStep = 'payment'
                                                labelNextStep = {l s='Go to Payment' mod='onepagecheckoutps'}
                                            }
                                        {/if}
                                        {include
                                            file = 'module:onepagecheckoutps/views/templates/front/checkout/steps/step.tpl'
                                            name = 'payment'
                                            title = {l s='Payment method' mod='onepagecheckoutps'}
                                            accordion = true
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
                </div>
                <div id="opc_content_side" class="col-lg-5">
                    {include file='module:onepagecheckoutps/views/templates/front/checkout/steps/cart.tpl'}
                </div>
                {if $showHookShoppingCart}
                    <div class="col-lg-12">
                        <div id="hook_shopping_cart">
                            {hook h='displayShoppingCart'}
                        </div>
                        <div id="hook_shopping_cart_footer">
                            {hook h='displayShoppingCartFooter'}
                        </div>
                    </div>
                {/if}
            </div>
        {/block}
    </div>
{/block}
