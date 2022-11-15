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

<div id="opc_cart" class="opc-step" data-step="cart">
    <div id="cart_body">
        <div id="opc_cart_title">
            <h5 id="opc_cart_title_text">
                {l s='My cart' mod='onepagecheckoutps'}
                {if not $isEditProductsCartEnabled}
                    &nbsp;<a class="btn-edit-cart" href="{$urls.pages.cart|escape:'htmlall':'UTF-8'}?action=show&opc=1">({l s='edit' mod='onepagecheckoutps'})</a>
                {/if}
            </h5>
            <span id="opc_cart_total_products">{$cartPresenterVars.products_count|intval}</span>
        </div>
        <div id="opc_cart_body">
            {$stepCartRendered nofilter}
        </div>
    </div>
    <div id="cart_footer">
        {hook h='displayReassurance'}
    </div>
</div>