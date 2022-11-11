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

<div id="opc_addresses">
    {if !$isCustomer and !$isGuest}
        <p class="alert alert-info text-md-center">
            {if $isVirtualCart}
                {l s='You can see the different payment methods depending on your address' mod='onepagecheckoutps'}
            {else}
                {l s='You can see the different shipping and payment methods depending on your address' mod='onepagecheckoutps'}
            {/if}
        </p>
    {/if}

    {if $isDeliveryAddressEnabled and (isset($formDeliveryAddress) or isset($addressesDelivery))}
        {block name='checkout_addresses_delivery_content'}
            <div id="opc_addresses_delivery_content">
                <span class="title-address-content">
                    {l s='Delivery address' mod='onepagecheckoutps'}:
                </span>

                {if !isset($formDeliveryAddress)}
                    <button
                        class="add btn btn-outline-primary btn-sm"
                        data-type-address="delivery"
                    >
                        <i class="material-icons">add</i>
                        {l s='Add new address' mod='onepagecheckoutps'}
                    </button>
                    <div class="clear clearfix"></div>
                {/if}

                {if isset($addressesDelivery)}
                    {include
                        file='module:onepagecheckoutps/views/templates/front/checkout/addresses/_partials/address-block.tpl'
                        addresses=$addressesDelivery
                        typeAddress="delivery"
                        name="id_address_delivery"
                        selected=$addressDeliveryId
                        showFooter=true
                    }
                {/if}
                {if isset($formDeliveryAddress)}
                    {include
                        file='module:onepagecheckoutps/views/templates/front/checkout/addresses/_partials/form-address.tpl'
                        typeAddress="delivery"
                        form=$formDeliveryAddress
                    }
                {/if}
            </div>
        {/block}
    {/if}

    {if $isInvoiceAddressEnabled}
        {if $isDeliveryAddressEnabled and
            $pageName neq 'addresses' and
            (
                isset($addressesInvoice) or
                (!$isCustomer and !$isGuest)
            )
        }
            <label for="chk-set_invoice_address">
                <input type="checkbox" name=set_invoice_address" id="chk-set_invoice_address" {if $haveSameAddress}checked{/if} />
                {l s='Use this same address for my invoice.' mod='onepagecheckoutps'}
            </label>
        {/if}

        {if isset($addressesInvoice) or isset($formInvoiceAddress)}
            {block name='checkout_addresses_invoice_content'}
                <div id="opc_addresses_invoice_content" {if $haveSameAddress and $isDeliveryAddressEnabled and $pageName neq 'addresses' and (!isset($formInvoiceAddress) or !$customerHaveAddresses)}style="display: none;"{/if}>
                    <span class="title-address-content">
                        {l s='Invoice address' mod='onepagecheckoutps'}:
                    </span>

                    {if !isset($formInvoiceAddress)}
                        <button
                            class="add btn btn-outline-primary btn-sm"
                            data-type-address="invoice"
                        >
                            <i class="material-icons">add</i>
                            {l s='Add new address' mod='onepagecheckoutps'}
                        </button>
                        <div class="clear clearfix"></div>
                    {/if}

                    {if isset($addressesInvoice)}
                        {include
                            file='module:onepagecheckoutps/views/templates/front/checkout/addresses/_partials/address-block.tpl'
                            addresses=$addressesInvoice
                            typeAddress="invoice"
                            name="id_address_invoice"
                            selected=$addressInvoiceId
                            showFooter=true
                        }
                    {/if}
                    {if isset($formInvoiceAddress)}
                        {include
                            file='module:onepagecheckoutps/views/templates/front/checkout/addresses/_partials/form-address.tpl'
                            typeAddress="invoice"
                            form=$formInvoiceAddress
                        }
                    {/if}
                </div>
            {/block}
        {/if}
    {/if}

    {if ($isCustomer or $isGuest) and (isset($formDeliveryAddress) or isset($formInvoiceAddress))}
        <div class="row">
            <div class="fields_required col-xs-12 col-12">
                <span>{l s='The fields with red asterisks(*) are required.' mod='onepagecheckoutps'}</span>
            </div>
        </div>
    {/if}

    {if (($isCustomer or $isGuest) and (isset($formDeliveryAddress) or isset($formInvoiceAddress)))}
        {block name='checkout_addresses_footer'}
            <div id="opc_addresses_footer" class="opc-step-footer">
                {if $customerHaveAddresses}
                    <button type="button" class="back btn btn-secondary">
                        <i class="material-icons">chevron_left</i>
                        {l s='Back' mod='onepagecheckoutps'}
                    </button>
                {/if}

                <button type="button" class="save btn btn-primary">
                    {l s='Save' mod='onepagecheckoutps'}
                </button>
            </div>
        {/block}
    {/if}
</div>
