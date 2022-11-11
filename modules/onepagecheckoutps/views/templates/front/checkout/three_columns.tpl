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

{block name='checkout_content'}
    <div id="opc_content" {if $OPC.General.isMobile}class="row"{/if}>
        <div id="opc_content_main" {if $OPC.General.isMobile}class="col-lg-7"{/if}>
            {block name='checkout_steps'}
                <div id="opc_steps">
                    {block name='checkout_steps_content'}
                        <div id="opc_steps_content">
                            {include file='module:onepagecheckoutps/views/templates/front/checkout/_partials/express_checkout.tpl'}

                            {if $OPC.General.isLogged or !$OPC.General.forceCustomerRegistrationLogin}
                                <div class="row">
                                    <div class="col-md-4">
                                        {include
                                            file = 'module:onepagecheckoutps/views/templates/front/checkout/steps/step.tpl'
                                            name = 'my_account'
                                            title = {l s='Contact information' mod='onepagecheckoutps'}
                                            accordion = false
                                            render = $stepMyAccountRendered
                                        }
                                        {include
                                            file = 'module:onepagecheckoutps/views/templates/front/checkout/steps/step.tpl'
                                            name = 'addresses'
                                            title = {l s='Addreses' mod='onepagecheckoutps'}
                                            accordion = false
                                        }
                                    </div>
                                    <div class="col-md-4">
                                        {if !$OPC.General.isVirtualCart}
                                            {include
                                                file = 'module:onepagecheckoutps/views/templates/front/checkout/steps/step.tpl'
                                                name = 'shipping'
                                                title = {l s='Shipping method' mod='onepagecheckoutps'}
                                                accordion = false
                                            }
                                        {/if}
                                        {include
                                            file = 'module:onepagecheckoutps/views/templates/front/checkout/steps/step.tpl'
                                            name = 'payment'
                                            title = {l s='Payment method' mod='onepagecheckoutps'}
                                            accordion = false
                                        }
                                    </div>
                                    {if !$OPC.General.isMobile}
                                        <div class="col-md-4">
                                            {include file='module:onepagecheckoutps/views/templates/front/checkout/steps/cart.tpl'}
                                        </div>
                                    {/if}
                                    {if $showHookShoppingCart}
                                        <div class="col-md-12">
                                            <div id="hook_shopping_cart">
                                                {hook h='displayShoppingCart'}
                                            </div>
                                            <div id="hook_shopping_cart_footer">
                                                {hook h='displayShoppingCartFooter'}
                                            </div>
                                        </div>
                                    {/if}
                                </div>
                            {else}
                                <div class="row">
                                    <div class="col-md-8">
                                        {include
                                            file = 'module:onepagecheckoutps/views/templates/front/checkout/steps/step.tpl'
                                            name = 'my_account'
                                            title = {l s='Contact information' mod='onepagecheckoutps'}
                                            accordion = false
                                            render = $stepMyAccountRendered
                                        }
                                    </div>
                                    {if !$OPC.General.isMobile}
                                        <div class="col-md-4">
                                            {include file='module:onepagecheckoutps/views/templates/front/checkout/steps/cart.tpl'}
                                        </div>
                                    {/if}
                                </div>
                            {/if}
                        </div>
                    {/block}
                </div>
            {/block}
        </div>
        {if $OPC.General.isMobile}
            <div id="opc_content_side" class="col-lg-5">
                {include file='module:onepagecheckoutps/views/templates/front/checkout/steps/cart.tpl'}
            </div>
        {/if}
    </div>
{/block}
