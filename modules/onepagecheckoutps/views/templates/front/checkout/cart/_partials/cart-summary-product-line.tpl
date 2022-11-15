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

<div class="media-left">
    <a href="{$product.url|escape:'htmlall':'UTF-8'}" title="{$product.name|escape:'htmlall':'UTF-8'}">
        <img
            class="media-object"
            src="{if $product.default_image}{$product.default_image.medium.url|escape:'htmlall':'UTF-8'}{else}{$product.cover.small.url|escape:'htmlall':'UTF-8'}{/if}"
            alt="{$product.name|escape:'htmlall':'UTF-8'}"
            loading="lazy"
        />
    </a>
</div>
<div class="media-body">
    <div class="media-body-left">
        <div class="product-detail">
            <span class="product-name">{$product.name|escape:'htmlall':'UTF-8'}</span>
            {include file='module:onepagecheckoutps/views/templates/front/checkout/cart/_partials/cart-summary-product-line-available.tpl'}

            {if isset($showDeliveryTime[$product.id_product]) and $showDeliveryTime[$product.id_product]}
                <span class="delivery-information">{$showDeliveryTime[$product.id_product]|escape:'htmlall':'UTF-8'}</span>
                <br />
                {if !$product.is_virtual}
                    {hook h="displayProductDeliveryTime" product=$product}
                {/if}
            {/if}

            {if $product.weight neq 0 and $showWeight}
                <div class="product-weight product-line-info product-line-info-secondary text-muted">
                    <span class="label">{l s='Weight' mod='onepagecheckoutps'}:</span>
                    <span class="value">{$product.weight|floatval|escape:'htmlall':'UTF-8'}{$weightUnit|escape:'htmlall':'UTF-8'}</span>
                </div>
            {/if}

            {foreach from=$product.attributes key="attribute" item="value"}
                <div class="product-line-info product-line-info-secondary text-muted">
                    <span class="label">{$attribute|escape:'htmlall':'UTF-8'}:</span>
                    <span class="value">{$value|escape:'htmlall':'UTF-8'}</span>
                </div>
            {/foreach}

            {if is_array($product.customizations) && $product.customizations|count}
                {foreach from=$product.customizations item="customization"}
                    <a href="#" class='product-customizations' data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' mod='onepagecheckoutps'}</a>

                    <div class="modal fade customization-modal" id="product-customizations-modal-{$customization.id_customization}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' mod='onepagecheckoutps'}">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title">{l s='Product customization' mod='onepagecheckoutps'}</h4>
                                </div>
                                <div class="modal-body">
                                {foreach from=$customization.fields item="field"}
                                    <div class="product-customization-line row">
                                        <div class="col-sm-3 col-xs-4 label">
                                            {$field.label}
                                        </div>
                                        <div class="col-sm-9 col-xs-8 value">
                                            {if $field.type == 'text'}
                                                {if (int)$field.id_module}
                                                    {$field.text nofilter}
                                                {else}
                                                    {$field.text}
                                                {/if}
                                            {elseif $field.type == 'image'}
                                                <img src="{$field.image.small.url}">
                                            {/if}
                                        </div>
                                    </div>
                                {/foreach}
                                </div>
                            </div>
                        </div>
                    </div>
                {/foreach}
            {/if}

            {if isset($product.productmega)}
                {$mega = $product.productmega|reset}

                {if isset($mega.extraAttrLong) and $mega.extraAttrLong neq ''}
                    <span class="d-block">
                        {$mega.extraAttrLong nofilter}
                    </span>
                    <br/>
                {/if}
                <span class="d-block">
                    <strong>{$mega.measure|escape:'htmlall':'UTF-8'}</strong>
                </span>
                {if isset($mega.personalization) && $mega.personalization neq ''}
                    <br/>
                    <div class="mp-personalization">
                        {$mega.personalization nofilter}
                    </div>
                {/if}
            {/if}

            <span class="product-price-label">{l s='Price' mod='onepagecheckoutps'}: </span>
            <span class="product-price">{$product.price|escape:'htmlall':'UTF-8'}</span>
            {if $product.has_discount}
                <span class="product-discount">
                    <span class="regular-price">{$product.regular_price|escape:'htmlall':'UTF-8'}</span>
                    {if $product.discount_type === 'percentage'}
                        <span class="discount discount-percentage">
                            (-{$product.discount_percentage_absolute|escape:'htmlall':'UTF-8'})
                        </span>
                    {else}
                        <span class="discount discount-amount">
                            (-{$product.discount_to_display|escape:'htmlall':'UTF-8'})
                        </span>
                    {/if}
                </span>
            {/if}
        </div>
        <div class="product-qty-price qty">
            {if $isEditProductsCartEnabled}
                {if isset($product.is_gift) && $product.is_gift}
                    <span class="gift-quantity">{$product.quantity|floatval}</span>
                {else}
                    <input class="opc-cart-line-product-quantity text-center" data-down-url="{$product.down_quantity_url|escape:'htmlall':'UTF-8'}"
                        data-up-url="{$product.up_quantity_url|escape:'htmlall':'UTF-8'}" data-update-url="{$product.update_quantity_url|escape:'htmlall':'UTF-8'}"
                        data-product-id="{$product.id_product|intval}"
                        data-up-disabled="{if $product.quantity_available <= $product.cart_quantity and !$product.allow_oosp}true{else}false{/if}"
                        data-min-quantity="{$product.minimal_quantity|floatval}" type="text" value="{$product.quantity|floatval}"
                        name="opc-product-quantity-spin" />
                {/if}

                <a class="remove-from-cart" rel="nofollow" href="{$product.remove_from_cart_url|escape:'htmlall':'UTF-8'}"
                    data-id-product="{$product.id_product|escape:'javascript':'UTF-8'}"
                    data-id-product-attribute="{$product.id_product_attribute|escape:'javascript':'UTF-8'}"
                    data-id-customization="{$product.id_customization|escape:'javascript':'UTF-8'}">
                    {if !isset($product.is_gift) || !$product.is_gift}
                        <i class="material-icons">delete</i>
                    {/if}
                </a>
            {else}
                <span class="product-quantity">x{if isset($product.cart_quantity_to_display_full)}{$product.cart_quantity_to_display_full nofilter}{else}{$product.quantity|floatval}{/if}</span>
                {if $isMobile}
                    &nbsp;<a class="btn-edit-cart" href="{$cartUrl|escape:'htmlall':'UTF-8'}?action=show&opc=1">({l s='edit' mod='onepagecheckoutps'})</a>
                {/if}
            {/if}
        </div>

        {hook h='displayProductPriceBlock' product=$product type="unit_price"}
    </div>
    <div class="media-body-right total-price">
        <span class="product-total-price">{$product.total|escape:'htmlall':'UTF-8'}</span>
    </div>
</div>