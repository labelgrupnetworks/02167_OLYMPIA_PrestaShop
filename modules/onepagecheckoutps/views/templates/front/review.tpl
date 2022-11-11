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

<script type="text/javascript">
    {literal}
    if (typeof prestashop !== typeof undefined) {
        if (typeof prestashop.cart !== typeof undefined) {
            {/literal}
            {if isset($cart)}
                prestashop.cart.totals = {$cart.totals|json_encode nofilter};
                prestashop.cart.subtotals = {$cart.subtotals|json_encode nofilter};
            {/if}
            {literal}
        }
    }
    {/literal}
</script>

{block name='step_review'}
    {if isset($errors)}
        {if is_array($errors) && count($errors) > 0}
            <div class="alert alert-danger" id="review_errors">
                <ul>
                    {foreach from=$errors item=$error}
                        <li>{$error|escape:'htmlall':'UTF-8'}</li>
                    {/foreach}
                </ul>
            </div>
        {/if}
    {/if}

    {if isset($minimal_purchase)}
        <div class="alert alert-warning">
            {$minimal_purchase|escape:'htmlall':'UTF-8'}
        </div>
    {/if}

    {if $CONFIGS.OPC_SHOW_REMAINING_FREE_SHIPPING}
        {if $cart.subtotals.shipping.amount > 0 && !isset($virtualCart) && $free_ship && !empty($free_ship)}
            <div class="row" id="remaining_amount_free_shipping">
                <div class="col-xs-12 text-center" id="remaining_amount_free_shipping-text">
                    {l s='You must add' mod='onepagecheckoutps'} <span>{$free_ship|escape:'htmlall':'UTF-8'}</span> {l s='to the cart to have' mod='onepagecheckoutps'} <span>{l s='free shipping' mod='onepagecheckoutps'}</span>
                </div>
                <div class="col-xs-12 col-12">
                    <div class="row">
                        <div class="col-3 col-xs-3 col-lg-2 col-md-2 col-sm-2">
                            <b>{$price_0|escape:'htmlall':'UTF-8'}</b>
                        </div>
                        <div class="col-5 col-xs-5 col-lg-8 col-md-8 col-sm-8">
                            <div class="progress">
                                <div class="progress-bar bg-{if $percent_free_shipping > 50}success{else}warning{/if}" role="progressbar" style="width: {$percent_free_shipping|escape:'htmlall':'UTF-8'|string_format:"%.2f"|cat:'%'}">&nbsp;</div>
                            </div>
                        </div>
                        <div class="col-4 col-xs-4 col-lg-2 col-md-2 col-sm-2 text-right">
                            <b>{$free_ship_preferences|escape:'htmlall':'UTF-8'}</b>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
        {/if}
    {/if}

    {hook h='displayCheckoutSummaryTop'}

    <div id="header-order-detail-content" class="row">
        <div class="hidden-md-down d-none d-md-block col-md-{if $CONFIGS.OPC_SHOW_UNIT_PRICE}4{else}6{/if} offset-xs-1 offset-1">
            {l s='Description' mod='onepagecheckoutps'}
        </div>
        {if $CONFIGS.OPC_SHOW_UNIT_PRICE}
            <div class="hidden-md-down d-none d-md-block col-md-2 col-lg-3 col-xl-3 text-right">
                {l s='Unit price' mod='onepagecheckoutps'}
            </div>
        {/if}
        <div class="hidden-md-down d-none d-md-block col-md-3 col-lg-2 col-xs-6 text-md-center">
            {l s='Qty' mod='onepagecheckoutps'}
        </div>
        <div class="hidden-md-down d-none d-md-block col-md-2 text-md-right">
            {l s='Total' mod='onepagecheckoutps'}
        </div>
    </div>

    <div id="order-detail-content" class="cart-detailed-totals">
        {foreach from=$cart.products item=product}
            {assign var='productId' value=$product.id_product}
            {assign var='productAttributeId' value=$product.id_product_attribute}
            {assign var='quantityDisplayed' value=0}
            {assign var='odd' value=$product@iteration%2}
            {assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId)}

            {if isset($product.productmega)}
                {foreach from=$product.productmega item=mega name=productMegas}
                    {if isset($attributewizardpro)}
                        {include file="./review_product_line_awp.tpl" CONFIGS=$CONFIGS productLast=$product@last productFirst=$product@first mega=$mega PS_LABEL_OOS_PRODUCTS_BOA=$PS_LABEL_OOS_PRODUCTS_BOA}
                    {else}
                        {include file="./review_product_line.tpl" CONFIGS=$CONFIGS productLast=$product@last productFirst=$product@first mega=$mega PS_LABEL_OOS_PRODUCTS_BOA=$PS_LABEL_OOS_PRODUCTS_BOA}
                    {/if}
                {/foreach}
            {else if isset($pproperties)}
                {include file="./review_product_line_pp.tpl" CONFIGS=$CONFIGS productLast=$product@last productFirst=$product@first PS_LABEL_OOS_PRODUCTS_BOA=$PS_LABEL_OOS_PRODUCTS_BOA}
            {else}
                {if isset($attributewizardpro)}
                    {include file="./review_product_line_awp.tpl" CONFIGS=$CONFIGS productLast=$product@last productFirst=$product@first PS_LABEL_OOS_PRODUCTS_BOA=$PS_LABEL_OOS_PRODUCTS_BOA}
                {else}
                    {include file="./review_product_line.tpl" CONFIGS=$CONFIGS productLast=$product@last productFirst=$product@first PS_LABEL_OOS_PRODUCTS_BOA=$PS_LABEL_OOS_PRODUCTS_BOA}
                {/if}
            {/if}
        {/foreach}

        <div class="order_total_items">
            {if $CONFIGS.OPC_SHOW_TOTAL_PRODUCT && $cart.subtotals.products}
                <div class="row middle item_total cart_total_price cart_total_product" id="cart-subtotal-{$cart.subtotals.products.type|escape:'htmlall':'UTF-8'}">
                    <div class="col-xs-8 col-8 col-md-10">
                        <span class="text-md-right">
                            {$cart.subtotals.products.label|escape:'htmlall':'UTF-8'}:
                        </span>
                    </div>
                    <div class="col-xs-4 col-4 col-md-2">
                        <span class="price" id="total_product">
                            {$cart.subtotals.products.value|escape:'htmlall':'UTF-8'}
                        </span>
                    </div>
                </div>
                {if !empty($free_ship) && isset($total_products_wt) && $CONFIGS.OPC_SHOW_REMAINING_FREE_SHIPPING}
                    <div class="row middle item_total cart_total_price cart_total_product" id="cart-subtotal-products-with-tax">
                        <div class="col-xs-8 col-8 col-md-10">
                            <span class="text-md-right">
                                {l s='Subtotal (tax incl.):' mod='onepagecheckoutps'}
                            </span>
                        </div>
                        <div class="col-xs-4 col-4 col-md-2">
                            <span class="price" id="total_product">
                                {$total_products_wt|escape:'htmlall':'UTF-8'}
                            </span>
                        </div>
                    </div>
                {/if}
            {/if}

            {if $CONFIGS.OPC_SHOW_TOTAL_SHIPPING && $cart.subtotals.shipping}
                <div class="row middle item_total cart_total_delivery" id="cart-subtotal-{$cart.subtotals.shipping.type|escape:'htmlall':'UTF-8'}">
                    <div class="col-xs-8 col-8 col-md-10">
                        <span class="text-md-right">
                            {$cart.subtotals.shipping.label|escape:'htmlall':'UTF-8'}:
                        </span>
                    </div>
                    <div class="col-xs-4 col-4 col-md-2">
                        <span class="price" id="total_shipping">
                            {$cart.subtotals.shipping.value|escape:'htmlall':'UTF-8'}
                        </span>
                    </div>
                </div>

                <div class="row middle">
                    <div class="col-xs-12">
                        {hook h='displayCheckoutSubtotalDetails' subtotal=$cart.subtotals.shipping}
                    </div>
                </div>
            {/if}

            {hook h='displayPaymentRuleCartVoucher'}
            {hook h="displayCartRuleCartVoucher" discounts=$cart.vouchers}

            {if $cart.vouchers.added}
                {foreach from=$cart.vouchers.added item=voucher}
                    <div class="row middle item_total cart_discount" id="cart_discount_{$voucher.id_cart_rule|intval}">
                        <div class="col-xs-8 col-8 col-md-10 text-md-right">
                            <span class="cart_discount_name text-md-right">
                                {$voucher.name|escape:'htmlall':'UTF-8'}:
                            </span>
                        </div>
                        <div class="col-xs-4 col-4 col-md-2 cart_discount_price">
                            <span class="price-discount price">
                                <i class="fa-pts fa-pts-trash-o cart_quantity_delete pull-left" data-id-cart-rule="{$voucher.id_cart_rule|intval}"></i>
                                {$voucher.reduction_formatted|escape:'htmlall':'UTF-8'}
                            </span>
                        </div>
                    </div>
                {/foreach}
            {/if}

            {if $CONFIGS.OPC_SHOW_TOTAL_DISCOUNT && $cart.subtotals.discounts}
                <div class="row middle item_total cart_total_voucher" id="cart-subtotal-{$cart.subtotals.discounts.type|escape:'htmlall':'UTF-8'}">
                    <div class="col-xs-8 col-8 col-md-10">
                        <span class="text-md-right">
                            {$cart.subtotals.discounts.label|escape:'htmlall':'UTF-8'}:
                        </span>
                    </div>
                    <div class="col-xs-4 col-4 col-md-2">
                        <span class="price-discount price" id="total_discount">
                            {$cart.subtotals.discounts.value|escape:'htmlall':'UTF-8'}
                        </span>
                    </div>
                </div>
            {/if}

            {if $CONFIGS.OPC_SHOW_TOTAL_WITHOUT_TAX && isset($cart.totals.total_excluding_tax) && $cart.totals.total_excluding_tax}
                <div class="row middle item_total cart_total_without_tax">
                    <div class="col-xs-8 col-8 col-md-10">
                        <span class="text-md-right">
                            {$cart.totals.total_excluding_tax.label|escape:'htmlall':'UTF-8'}:
                        </span>
                    </div>
                    <div class="col-xs-4 col-4 col-md-2 text-md-right">
                        <span class="price" id="total_without_tax">
                            {$cart.totals.total_excluding_tax.value|escape:'htmlall':'UTF-8'}
                        </span>
                    </div>
                </div>
            {/if}
            {if $CONFIGS.OPC_SHOW_TOTAL_TAX && $cart.subtotals.tax}
                <div class="row middle item_total cart_total_tax">
                    <div class="col-xs-8 col-8 col-md-10">
                        <span class="text-md-right">
                            {$cart.subtotals.tax.label|escape:'htmlall':'UTF-8'}:
                        </span>
                    </div>
                    <div class="col-xs-4 col-4 col-md-2 text-md-right">
                        <span class="price" id="total_tax">
                            {$cart.subtotals.tax.value|escape:'htmlall':'UTF-8'}
                        </span>
                    </div>
                </div>
            {/if}
            {if $CONFIGS.OPC_SHOW_TOTAL_WRAPPING && isset($cart.subtotals.gift_wrapping)}
                <div class="row middle item_total cart_total_price total_price">
                    <div class="col-xs-8 col-8 col-md-10">
                        <span class="text-md-right">
                            {$cart.subtotals.gift_wrapping.label|escape:'htmlall':'UTF-8'}:
                        </span>
                    </div>
                    <div class="col-xs-4 col-4 col-md-2">
                        <span class="price" id="total_price">
                            {$cart.subtotals.gift_wrapping.value|escape:'htmlall':'UTF-8'}
                        </span>
                    </div>
                </div>
            {/if}
            {if $CONFIGS.OPC_SHOW_TOTAL_PRICE && $cart.totals.total}
                <div class="row middle item_total cart_total_price total_price">
                    <div class="col-xs-8 col-8 col-md-10">
                        <span class="text-md-right">
                            {$cart.totals.total.label|escape:'htmlall':'UTF-8'}:
                        </span>
                    </div>
                    <div class="col-xs-4 col-4 col-md-2">
                        <span class="price" id="total_price">
                            {*{if isset($cart.totals.total.value) and $cart.totals.total.value}
                                {$cart.totals.total.value}
                            {else}*}
                                {$total_cart|escape:'htmlall':'UTF-8'}
                           {* {/if}*}
                        </span>
                    </div>
                </div>
            {/if}

            <div class="row middle item_total extra_fee_tax cart_total_price end-xs hidden">
                <div class="col-xs-8 col-8 col-md-10 text-right">
                    <span class="bold text-right" id="extra_fee_tax_label"></span>
                </div>
                <div class="col-xs-4 col-4 col-md-2 text-right">
                    <span class="price" id="extra_fee_tax_price"></span>
                </div>
            </div>
            <div class="row middle item_total extra_fee cart_total_price end-xs hidden">
                <div class="col-xs-8 col-8 col-md-10 text-right">
                    <span class="bold text-right" id="extra_fee_label"></span>
                </div>
                <div class="col-xs-4 col-4 col-md-2 text-right">
                    <span class="price" id="extra_fee_price"></span>
                </div>
            </div>
            <div class="row middle item_total cart_total_price total_price extra_fee end-xs hidden">
                <div class="col-xs-8 col-8 col-md-10 text-right">
                    <span class="bold text-right" id="extra_fee_total_price_label"></span>
                </div>
                <div class="col-xs-4 col-4 col-md-2 text-right">
                    <span class="price" id="extra_fee_total_price"></span>
                </div>
            </div>

            <div class="row middle item_total module_discount cart_total_price end-xs hidden">
                <div class="col-xs-8 col-8 col-md-10 text-right">
                    <span class="bold text-right" id="module_discount_label"></span>
                </div>
                <div class="col-xs-4 col-4 col-md-2 text-right">
                    <span class="price" id="module_discount_price"></span>
                </div>
            </div>
            <div class="row middle item_total cart_total_price total_price module_discount end-xs hidden">
                <div class="col-xs-8 col-8 col-md-10 text-right">
                    <span class="bold text-right" id="module_discount_total_price_label"></span>
                </div>
                <div class="col-xs-4 col-4 col-md-2 text-right">
                    <span class="price" id="module_discount_total_price"></span>
                </div>
            </div>

            <div class="row middle">
                <div class="col-xs-12">
                    {hook h='displayExpressCheckout'}
                </div>
            </div>

            {if $CONFIGS.OPC_SHOW_VOUCHER_BOX && $cart.vouchers.allowed}
                <div class="cart_total_price" id="list-voucher-allowed">
                    <div class="row">
                        <div class="col-xs-12 col-12 col-md-6">
                            <span class="promo-code-button">
                                {l s='Do you have a promotional code?' mod='onepagecheckoutps'}
                            </span>
                            <div class="promo-code">
                                <input type="text" class="promo-input" id="discount_name" name="discount_name" placeholder="{l s='Promo code' mod='onepagecheckoutps'}"/>
                                <button id="submitAddDiscount" class="btn btn-primary btn-sm">
                                    <span>{l s='Add' mod='onepagecheckoutps'}</span>
                                </button>
                            </div>
                        </div>
                        {if $cart.discounts|count > 0}
                            <div class="col-xs-12 col-12 col-md-6">
                                <div id="display_cart_vouchers">
                                    <p>
                                        {l s='Take advantage of our exclusive offers:' mod='onepagecheckoutps'}
                                    </p>
                                    <ul class="js-discount">
                                        {foreach from=$cart.discounts item=discount}
                                            <li class="cart-summary-line">
                                                <i class="fa-pts fa-pts-caret-right"></i>
                                                <span class="code">{$discount.code|escape:'htmlall':'UTF-8'}</span>
                                                 - {$discount.name|escape:'htmlall':'UTF-8'}
                                            </li>
                                        {/foreach}
                                    </ul>
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>
            {/if}
        </div>
    </div>
{/block}