{*
* 2017 IQIT-COMMERCE.COM
*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement
*
* @author    IQIT-COMMERCE.COM <support@iqit-commerce.com>
* @copyright 2017 IQIT-COMMERCE.COM
* @license   Commercial license (You can not resell or redistribute this software.)
*
*}

<div id="iqitwishlist-product-{$product.id_iqitwishlist_product|intval}" class="iqitwishlist-product product-miniature-grid ">
    <div class="row align-items-center">
        <div class="col-3 col-sm-auto">
            <a href="{$product.url}"> 
            {if $product.default_image}
                <img src="{$product.default_image.bySize.cart_default.url}" alt="{$product.name|escape:'quotes'}"  class="img-fluid" loading="lazy">
            {else}
                <img src="{$urls.no_picture_image.bySize.cart_default.url}" class="img-fluid"  loading="lazy" />
            {/if}
            
            </a>
        </div>

        <div class="col _name">
            <a href="{$product.url}">{$product.name}</a>
            <div class="text-muted">
            {foreach from=$product.attributes item="attribute"}
                {$attribute.group}: {$attribute.name}
            {/foreach}
            </div>
        </div>

        <div class="col {if $readOnly} text-right{/if}">
            <span class="product-price">{$product.price}</span>
        </div>
        <div class="col col-auto{if $readOnly} text-right{/if}">
            {include file='catalog/_partials/miniatures/_partials/product-miniature-btn.tpl'}
        </div>
      
        {if !$readOnly}
            <div class="col col-auto">
                <a href="#" class="js-iqitwishlist-remove"
                   data-id-product="{$product.id_iqitwishlist_product|intval}"
                   data-url="{url entity='module' name='iqitwishlist' controller='actions'}">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </a>
            </div>
        {/if}
    </div>
    <hr>
</div>