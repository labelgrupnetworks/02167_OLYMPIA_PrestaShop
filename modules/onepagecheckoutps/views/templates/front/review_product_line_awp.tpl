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

{assign var="product_link" value=$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'htmlall':'UTF-8'}
{if isset($awp_url_rewrite) and $awp_url_rewrite}
	{assign var="awp_product_link" value="?"}
{else}
	{assign var="awp_product_link" value="&"}
{/if}
{assign var="awp_product_link" value=$awp_product_link|cat:'ipa='|cat:$product.id_product_attribute|cat:'&ins='|cat:$product.instructions_valid}
{if $product_link|strpos:'#' > 0}
	{assign var='amp_pos' value=$product_link|strpos:'#'}
	{assign var='product_link' value=$product_link|substr:0:$amp_pos}
{/if}
{assign var='product_link' value=$product_link|cat:$awp_product_link}
<script type="text/javascript">
    if (typeof awpProducts == 'undefined')
        var awpProducts = new Array();
    awpProducts['{$product.id_product|intval}_{$product.id_product_attribute|intval}'] = '{$product.instructions_valid|escape:'htmlall':'UTF-8'}';
</script>

<div class="row {if isset($productLast) and $productLast && (not isset($ignoreProductLast) or !$ignoreProductLast)}last_item{elseif isset($productFirst) and $productFirst}first_item{/if} {if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0}alternate_item{/if} cart_item address_{$product.id_address_delivery|intval}"
     id="product_{$product.id_product|intval}_{$product.id_product_attribute|intval}_0_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
    <div class="col-md-1 col-xs-3 col-3 text-md-center image_product">
        <a href="{$product.url|escape:'htmlall':'UTF-8'}" title="{$product.name|escape:'htmlall':'UTF-8'}">
            <img class="img-fluid media-object" src="{$product.cover.small.url|escape:'htmlall':'UTF-8'}" alt="{$product.name|escape:'htmlall':'UTF-8'}">
        </a>
        {if $CONFIGS.OPC_SHOW_ZOOM_IMAGE_PRODUCT}
            <div class="image_zoom">
                <img class="media-object" src="{$product.cover.medium.url|escape:'htmlall':'UTF-8'}" alt="{$product.name|escape:'htmlall':'UTF-8'}">
            </div>
        {/if}
    </div>
    <div class="col-md-{if $CONFIGS.OPC_SHOW_UNIT_PRICE}4{else}6{/if} col-xs-9 col-9 cart_description">
        <p class="s_title_block">
            {if !$CONFIGS.OPC_REMOVE_LINK_PRODUCTS}
                <a href="{$product.url|escape:'htmlall':'UTF-8'}" title="{$product.name|escape:'htmlall':'UTF-8'}">
            {/if}
                {$product.name|escape:'htmlall':'UTF-8'}
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

        {if isset($product.productmega)}
            {if isset($mega.extraAttrLong)}{$mega.extraAttrLong nofilter}{/if}
            <br/>
            <strong>{$mega.measure|escape:'htmlall':'UTF-8'}</strong>
            {if isset($mega.personalization) && $mega.personalization neq ''}
                <br/><div class="mp-personalization">{$mega.personalization nofilter}</div>
            {/if}
        {/if}

        {if isset($product.instructions) && $product.instructions}
            <a href="{$product_link|escape:'htmlall':'UTF-8'}">{$product.instructions nofilter}</a>
        {/if}

        {if $product.weight neq 0 and $CONFIGS.OPC_SHOW_WEIGHT}
            <span class="product_weight">
                <span>{l s='Weight' mod='onepagecheckoutps'}&nbsp;:&nbsp;</span>
                {$product.weight|string_format:"%.3f"|escape:'htmlall':'UTF-8'}{$PS_WEIGHT_UNIT|escape:'htmlall':'UTF-8'}
            </span>
        {/if}

        {if $ps_stock_management and $CONFIGS.OPC_SHOW_AVAILABILITY}
            <div class="cart_avail">
                {*<span class="badge {if $product.availability == 'available'}badge-success product-available{elseif $product.availability == 'last_remaining_items'}badge-warning product-last-items{else}badge-danger product-unavailable{/if}">*}
                <span class="badge {if $product.quantity_available <= 0 || $product.cart_quantity > $product.quantity_available}badge-danger product-unavailable{else}badge-success product-available{/if}">
                    {if $product.quantity_available <= 0 || $product.cart_quantity > $product.quantity_available}
                        {if (isset($product.available_later) && $product.available_later) || (isset($PS_LABEL_OOS_PRODUCTS_BOA) && $PS_LABEL_OOS_PRODUCTS_BOA)}
                            {if isset($preorder[$product.id_product])}
                                {l s='Available on:' mod='onepagecheckoutps'} {$preorder[$product.id_product]|date_format:"%A, %e %B, %Y"|escape:'htmlall':'UTF-8'}
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
                {if $CONFIGS.OPC_SHOW_DELIVERY_TIME && !empty($product.delivery_information_opc)}
                    <br/>
                    <span class="delivery-information">{$product.delivery_information_opc|escape:'htmlall':'UTF-8'}</span>
                {/if}
                {if !$product.is_virtual}{hook h="displayProductDeliveryTime" product=$product}{/if}
            </div>
        {/if}
    </div>

    <div class="hidden-sm-up row clear"></div>

    {if $CONFIGS.OPC_SHOW_UNIT_PRICE}
        <div class="col-xs-3 col-3 text-md-right hidden-sm-up">
            <label><b>{l s='Unit price' mod='onepagecheckoutps'}:</b></label>
        </div>
        <div class="col-md-2 col-xs-3 col-3 col-lg-3 col-xl-3 text-md-center text-xs-left text-sm-left">
            <span class="" id="product_price_7_34_0">
                <span class="price special-price">{if isset($product.productmega)}{$mega.spricewt|escape:'htmlall':'UTF-8'}{else}{$product.price|escape:'htmlall':'UTF-8'}{/if}</span>
                <br>
                {if $product.price neq $product.regular_price}
                    <span class="old-price">{$product.regular_price|escape:'htmlall':'UTF-8'}</span>
                {/if}
                {if $product.discount_type eq 'amount' and $product.discount_amount neq ''}
                    <span class="price-percent-reduction small">({$product.discount_amount|escape:'htmlall':'UTF-8'})</span>
                {elseif $product.discount_type eq 'percentage' and $product.discount_percentage neq ''}
                    <span class="price-percent-reduction small">({$product.discount_percentage|escape:'htmlall':'UTF-8'})</span>
                {/if}
            </span>
            {if $product.unit_price_full}
                <div class="unit-price-cart text-right">{$product.unit_price_full|escape:'htmlall':'UTF-8'}</div>
            {/if}
        </div>
    {/if}

    <div class="hidden-sm-up row clear"></div>

    <div class="col-xs-3 col-3 text-md-right hidden-sm-up">
        <label><b>{l s='Quantity' mod='onepagecheckoutps'}:</b></label>
    </div>
    <div class="col-md-3 col-lg-2 col-xs-6 col-6 text-md-center quantity-content">
        <div class="input-group bootstrap-touchspin">
            <span class="input-group-addon bootstrap-touchspin-prefix" style="display: none;"></span>
            <input
                class="cart-line-product-quantity"
                data-down-url="{$product.down_quantity_url|escape:'htmlall':'UTF-8'}&special_instructions={$product.instructions_valid|escape:'htmlall':'UTF-8'}&special_instructions_id={$product.instructions_id|escape:'htmlall':'UTF-8'}{if isset($product.productmega)}&id_megacart={$mega.id_megacart|intval}{/if}"
                data-up-url="{$product.up_quantity_url|escape:'htmlall':'UTF-8'}&special_instructions={$product.instructions_valid|escape:'htmlall':'UTF-8'}&special_instructions_id={$product.instructions_id|escape:'htmlall':'UTF-8'}{if isset($product.productmega)}&id_megacart={$mega.id_megacart|intval}{/if}"
                data-update-url="{$product.update_quantity_url|escape:'htmlall':'UTF-8'}&special_instructions={$product.instructions_valid|escape:'htmlall':'UTF-8'}&special_instructions_id={$product.instructions_id|escape:'htmlall':'UTF-8'}{if isset($product.productmega)}&id_megacart={$mega.id_megacart|intval}{/if}"
                data-product-id="{$product.id_product|intval}"
                awp-data-product-attribute-id="{$product.id_product_attribute|intval}"
                {if isset($product.productmega)}data-mega-id="{$mega.id_megacart|intval}"{/if}
                type="text"
                value="{if isset($product.productmega)}{$mega.quantity|escape:'htmlall':'UTF-8'}{else}{$product.quantity|escape:'htmlall':'UTF-8'}{/if}"
                name="product-quantity-spin"
                min="{$product.minimal_quantity|escape:'htmlall':'UTF-8'}"
              />
            <span class="input-group-addon bootstrap-touchspin-postfix" style="display: none;"></span>
            <span class="input-group-btn-vertical">
                <button class="btn btn-touchspin js-touchspin bootstrap-touchspin-up" type="button">
                    <i class="fa-pts fa-pts-chevron-up"></i>
                </button>
                <button class="btn btn-touchspin js-touchspin bootstrap-touchspin-down" type="button">
                    <i class="fa-pts fa-pts-chevron-down"></i>
                </button>
            </span>
            <a
                class = "remove-from-cart"
                rel = "nofollow"
                href = "{$product.remove_from_cart_url|escape:'htmlall':'UTF-8'}&special_instructions={$product.instructions_valid|escape:'htmlall':'UTF-8'}{if isset($product.productmega)}&id_megacart={$mega.id_megacart|intval}{/if}"
                data-link-action            = "delete-from-cart"
                data-id-product             = "{$product.id_product|escape:'javascript':'UTF-8'}"
                data-id-product-attribute   = "{$product.id_product_attribute|escape:'javascript':'UTF-8'}_{$product.instructions_valid|escape:'javascript':'UTF-8'}"
                data-id-customization   	  = "{$product.id_customization|escape:'javascript':'UTF-8'}"
            >
                <i class="fa-pts fa-pts-trash-o fa-pts-1x"></i>
            </a>
        </div>

        {hook h='displayCartExtraProductActions' product=$product}
    </div>

    <div class="hidden-sm-up row clear"></div>

    <div class="col-xs-3 col-3 text-md-right hidden-sm-up">
        <label><b>{l s='Total' mod='onepagecheckoutps'}:</b></label>
    </div>
    <div class="col-md-2 col-xs-9 col-9 text-md-right text-xs-left text-sm-left">
        <span class="product-price pull-right">{if isset($product.productmega)}{$mega.stotalwt|floatval}{else}{$product.total|floatval}{/if}</span>
    </div>
</div>