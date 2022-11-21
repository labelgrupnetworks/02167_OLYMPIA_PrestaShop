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

<div class="row {if isset($productLast) and $productLast && (not isset($ignoreProductLast) or !$ignoreProductLast)}last_item{elseif isset($productFirst) and $productFirst}first_item{/if} {if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0}alternate_item{/if} cart_item address_{$product.id_address_delivery|intval}"
     id="product_{$product.id_product|intval}_{$product.id_product_attribute|intval}_0_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
    <div class="col-md-1 col-xs-3 col-3 text-md-center image_product">
        {if isset($product.productmega)}
            {assign var="path_img" value="{$smarty.const._PS_MODULE_DIR_}/megaproduct/images/cart/{$mega.id_megacart|intval}.jpg"}
        {/if}
        {if isset($product.productmega) and file_exists($path_img)}
            <a href="{$product.url|escape:'htmlall':'UTF-8'}" title="{$product.name|escape:'htmlall':'UTF-8'}">
                <img class="img-fluid media-object" src="{$smarty.const._MODULE_DIR_|escape:'htmlall':'UTF-8'}megaproduct/images/cart/{$mega.id_megacart|intval}.jpg" alt="{$product.name|escape:'htmlall':'UTF-8'}"/>
            </a>

            {if $CONFIGS.OPC_SHOW_ZOOM_IMAGE_PRODUCT}
                <div class="image_zoom">
                    <img class="media-object" src="{$smarty.const._MODULE_DIR_|escape:'htmlall':'UTF-8'}megaproduct/images/cart/{$mega.id_megacart|intval}.jpg" alt="{$product.name|escape:'htmlall':'UTF-8'}"/>
                </div>
            {/if}
        {else}
            <a href="{$product.url|escape:'htmlall':'UTF-8'}" title="{$product.name|escape:'htmlall':'UTF-8'}">
                <img
                    class="img-fluid media-object"
                    src="{if isset($product.default_image) and $product.default_image}{$product.default_image.small.url|escape:'htmlall':'UTF-8'}{else}{$product.cover.small.url|escape:'htmlall':'UTF-8'}{/if}"
                    alt="{$product.name|escape:'htmlall':'UTF-8'}"
                    loading="lazy"
                />
            </a>

            {if $CONFIGS.OPC_SHOW_ZOOM_IMAGE_PRODUCT}
                <div class="image_zoom">
                    <img
                        class="media-object"
                        src="{if isset($product.default_image) and $product.default_image}{$product.default_image.medium.url|escape:'htmlall':'UTF-8'}{else}{$product.cover.medium.url|escape:'htmlall':'UTF-8'}{/if}"
                        alt="{$product.name|escape:'htmlall':'UTF-8'}"
                        loading="lazy"
                    />
                </div>
            {/if}
        {/if}
    </div>
    <div class="col-md-{if $CONFIGS.OPC_SHOW_UNIT_PRICE}4{else}6{/if} col-xs-9 col-9 cart_description">
        <p class="s_title_block">
            {if !$CONFIGS.OPC_REMOVE_LINK_PRODUCTS}
                <a href="{$product.url|escape:'htmlall':'UTF-8'}" title="{$product.name|escape:'htmlall':'UTF-8'}">
            {/if}
                <span>{$product.name|escape:'htmlall':'UTF-8'}</span>
                {if $product.reference and $CONFIGS.OPC_SHOW_REFERENCE}
                    <span class="product_reference">
                        ({l s='Ref.' mod='onepagecheckoutps'}&nbsp;{$product.reference|escape:'htmlall':'UTF-8'})
                    </span>
                {/if}
            {if !$CONFIGS.OPC_REMOVE_LINK_PRODUCTS}
                </a>
            {/if}
        </p>
        {if isset($product.attributes) && $product.attributes}
            <span class="product_attributes">
                {foreach from=$product.attributes key="attribute" item="value"}
                    <div class="product-line-info">
                        <span class="">{$attribute|escape:'htmlall':'UTF-8'}:</span>
                        <span class="">{$value|escape:'htmlall':'UTF-8'}</span>
                    </div>
                {/foreach}
            </span>
        {/if}

        {if !empty($product.customizations)}
            {foreach $product.customizations as $customization}
                {foreach from=$customization.fields item="field"}
                    {if $field.label != 'Tipo de servicio'}
                        <div class="product-customization-line row">
                            <span class="label">
                                {$field.label|escape:'htmlall':'UTF-8'}: 
                            </span>
                            <span class="value">
                                {if $field.type == 'text'}
                                    {if (int)$field.id_module}
                                        {$field.text nofilter}
                                    {else}
                                        {$field.text nofilter}
                                    {/if}
                                {elseif $field.type == 'image'}
                                    <img src="{$field.image.small.url|escape:'htmlall':'UTF-8'}">
                                {/if}
                            </span>                    
                        </div>
                    {/if}
                {/foreach}
            {/foreach}
        {/if}

        {if isset($product.productmega)}
            {if isset($mega.extraAttrLong)}{$mega.extraAttrLong nofilter}{/if}
            <br/>
            <strong>{$mega.measure|escape:'htmlall':'UTF-8'}</strong>
            {if isset($mega.personalization) && $mega.personalization neq ''}
                <br/><div class="mp-personalization">{$mega.personalization nofilter}</div>
            {/if}
        {/if}

        {if $product.weight neq 0 and $CONFIGS.OPC_SHOW_WEIGHT}
            <span class="product_weight">
                <span>{l s='Weight' mod='onepagecheckoutps'}&nbsp;:&nbsp;</span>
                {$product.weight|floatval|escape:'htmlall':'UTF-8'}{$PS_WEIGHT_UNIT|escape:'htmlall':'UTF-8'}
            </span>
        {/if}
        {if $ps_stock_management and $CONFIGS.OPC_SHOW_AVAILABILITY}
            <div class="cart_avail">
                {strip}
                {*<span class="badge {if $product.availability == 'available'}badge-success product-available{elseif $product.availability == 'last_remaining_items'}badge-warning product-last-items{else}badge-danger product-unavailable{/if}">*}
                <span class="badge {if $product.quantity_available <= 0 || $product.cart_quantity > $product.quantity_available}badge-danger product-unavailable{else}badge-success product-available{/if}">
                    {if $product.quantity_available <= 0 || $product.cart_quantity > $product.quantity_available}
                        {if (isset($product.available_later) && $product.available_later) || (isset($PS_LABEL_OOS_PRODUCTS_BOA) && $PS_LABEL_OOS_PRODUCTS_BOA)}
                            {if isset($preorder[$product.id_product])}
                                {l s='Available on' mod='onepagecheckoutps'}: {$preorder[$product.id_product]|date_format:"%A, %e %B, %Y"|escape:'htmlall':'UTF-8'}
                            {else if isset($product.available_later) && $product.available_later}
                                {$product.available_later|escape:'htmlall':'UTF-8'}
                            {else}
                                {$PS_LABEL_OOS_PRODUCTS_BOA|escape:'htmlall':'UTF-8'}
                            {/if}
                        {else}
                            {$product.availability_message|escape:'htmlall':'UTF-8'}
                        {/if}
                    {else}
                        {if $product.quantity > $product.quantity_available}
                            {if isset($product.available_later) && $product.available_later}
                                {$product.available_later|escape:'htmlall':'UTF-8'}
                            {else}
                                {$product.availability_message|escape:'htmlall':'UTF-8'}
                            {/if}
                        {else}
                            {if isset($product.available_now) && $product.available_now}
                                {$product.available_now|escape:'htmlall':'UTF-8'}
                            {else}
                                {$product.availability_message|escape:'htmlall':'UTF-8'}
                            {/if}
                        {/if}
                    {/if}
                </span>
                {/strip}
            </div>
        {/if}
        {if $CONFIGS.OPC_SHOW_DELIVERY_TIME && !empty($product.delivery_information_opc)}
            <span class="delivery-information">{$product.delivery_information_opc|escape:'htmlall':'UTF-8'}</span>
            <br/>
            {if !$product.is_virtual}{hook h="displayProductDeliveryTime" product=$product}{/if}
        {/if}
    </div>

    <div class="hidden-sm-up row clear"></div>

    {if $CONFIGS.OPC_SHOW_UNIT_PRICE}
        {*<div class="col-xs-3 col-3 text-md-right hidden-sm-up">
            <label><b>{l s='Unit price' mod='onepagecheckoutps'}:</b></label>
        </div>*}
        <div class="col-md-2 col-xs-3 col-3 col-lg-3 col-xl-3 text-md-center text-xs-left text-sm-left">
            <span class="" id="product_price_7_34_0">
                {if $product.price neq $product.regular_price}
                    <span class="old-price d-block text-right">{$product.regular_price|escape:'htmlall':'UTF-8'}</span>
                {/if}
                <span class="price special-price d-block text-right">{if isset($product.productmega)}{$mega.spricewt|escape:'htmlall':'UTF-8'}{else}{$product.price|escape:'htmlall':'UTF-8'}{/if}</span>
                {if $product.discount_type eq 'amount' and $product.discount_amount neq ''}
                    <span class="price-percent-reduction small d-block text-right">({$product.discount_amount|escape:'htmlall':'UTF-8'})</span>
                {elseif $product.discount_type eq 'percentage' and $product.discount_percentage neq ''}
                    <span class="price-percent-reduction small d-block text-right">({$product.discount_percentage|escape:'htmlall':'UTF-8'})</span>
                {/if}
            </span>
            {if $product.unit_price_full}
                <div class="unit-price-cart text-right">{$product.unit_price_full|escape:'htmlall':'UTF-8'}</div>
            {/if}
        </div>
    {/if}

    {*<div class="hidden-sm-up row clear"></div>*}

    {*<div class="col-xs-3 col-3 text-md-right hidden-sm-up">
        <label><b>{l s='Quantity' mod='onepagecheckoutps'}:</b></label>
    </div>*}
    <div class="col-md-3 col-lg-2 col-xs-6 col-6 text-md-center quantity-content">
        <div class="input-group bootstrap-touchspin">
            <span class="input-group-addon bootstrap-touchspin-prefix" style="display: none;"></span>
            {*{if isset($product.productmega)}
                <span class="cart-line-product-quantity">{$mega.quantity}</span>
            {else}*}
                <input
                    class="cart-line-product-quantity"
                    data-down-url="{$product.down_quantity_url|escape:'htmlall':'UTF-8'}{if isset($product.productmega)}&id_megacart={$mega.id_megacart|escape:'htmlall':'UTF-8'}{/if}"
                    data-up-url="{$product.up_quantity_url|escape:'htmlall':'UTF-8'}{if isset($product.productmega)}&id_megacart={$mega.id_megacart|escape:'htmlall':'UTF-8'}{/if}"
                    data-update-url="{$product.update_quantity_url|escape:'htmlall':'UTF-8'}{if isset($product.productmega)}&id_megacart={$mega.id_megacart|escape:'htmlall':'UTF-8'}{/if}"
                    data-product-id="{$product.id_product|escape:'htmlall':'UTF-8'}"
                    {if isset($product.productmega)}data-mega-id="{$mega.id_megacart|escape:'htmlall':'UTF-8'}"{/if}
                    type="text"
                    value="{if isset($product.productmega)}{$mega.quantity|escape:'htmlall':'UTF-8'}{else}{$product.quantity|escape:'htmlall':'UTF-8'}{/if}"
                    name="product-quantity-spin"
                    {if $product.quantity_available eq 1 and !$product.allow_oosp}disabled{/if}
                >
{*            {/if}*}
            <span class="input-group-addon bootstrap-touchspin-postfix" style="display: none;"></span>
{*            {if !isset($product.productmega)}*}
                <span class="input-group-btn-vertical">
                    <button class="btn btn-touchspin js-touchspin bootstrap-touchspin-up" type="button" {if $product.quantity_available <= $product.cart_quantity and !$product.allow_oosp}disabled{/if}>
                        <i class="fa-pts fa-pts-chevron-up"></i>
                    </button>
                    <button class="btn btn-touchspin js-touchspin bootstrap-touchspin-down" type="button" {if $product.minimal_quantity < $product.cart_quantity OR $product.minimal_quantity <= 1}{else}disabled{/if}>
                        <i class="fa-pts fa-pts-chevron-down"></i>
                    </button>
                </span>
{*            {/if}*}
            <a
                class                       = "remove-from-cart"
                rel                         = "nofollow"
                href                        = "{$product.remove_from_cart_url|escape:'javascript':'UTF-8'}{if isset($product.productmega)}&id_megacart={$mega.id_megacart|escape:'javascript':'UTF-8'}{/if}"
                data-link-action            = "delete-from-cart"
                data-id-product             = "{$product.id_product|escape:'javascript':'UTF-8'}"
                data-id-product-attribute   = "{$product.id_product_attribute|escape:'javascript':'UTF-8'}"
                data-id-customization   	= "{$product.id_customization|escape:'javascript':'UTF-8'}"
            >
                <i class="fa-pts fa-pts-trash-o fa-pts-1x"></i>
            </a>
        </div>

        {hook h='displayCartExtraProductActions' product=$product}
    </div>

    {*<div class="hidden-sm-up row clear"></div>*}

    {*<div class="col-xs-3 col-3 text-md-right hidden-sm-up">
        <label><b>{l s='Total' mod='onepagecheckoutps'}:</b></label>
    </div>*}
    <div class="col-md-2 col-xs-3 col-3 text-md-right text-xs-right text-sm-right">
    <span class="total-price-text d-block d-md-none text-right">{l s='Total' mod='onepagecheckoutps'}</span>
        <span class="product-price d-block text-right">{if isset($product.productmega)}{$mega.stotalwt|escape:'htmlall':'UTF-8'}{else}{$product.total|escape:'htmlall':'UTF-8'}{/if}</span>
    </div>
</div>