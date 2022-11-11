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

<aside id="notifications"></aside>

{hook h='displayCheckoutSummaryTop'}

<div class="cart-summary-products">
    <div id="cart-summary-product-list">
        <ul class="media-list">
            {foreach from=$cartPresenterVars.products item=product}
                <li class="media {if $product@first}first{else if $product@last}last{/if}">
                    {include file='module:onepagecheckoutps/views/templates/front/checkout/cart/_partials/cart-summary-product-line.tpl' product=$product}
                </li>
            {/foreach}
        </ul>
    </div>
</div>

{include file='module:onepagecheckoutps/views/templates/front/checkout/cart/_partials/cart-summary-voucher.tpl'}
{include file='module:onepagecheckoutps/views/templates/front/checkout/cart/_partials/cart-voucher.tpl'}
{include file='module:onepagecheckoutps/views/templates/front/checkout/cart/_partials/cart-free-shipping.tpl'}
{include file='module:onepagecheckoutps/views/templates/front/checkout/cart/_partials/cart-summary-subtotals.tpl'}
{include file='module:onepagecheckoutps/views/templates/front/checkout/cart/_partials/cart-summary-totals.tpl'}
