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

<div class="cart-summary-totals">
    {if $cartPresenterVars.subtotals.tax}
        <div class="cart-summary-line">
            <span class="label sub">{l s='%label%:' sprintf=['%label%' => $cartPresenterVars.subtotals.tax.label] d='Shop.Theme.Global'}</span>
            <span class="value sub">{$cartPresenterVars.subtotals.tax.value|escape:'htmlall':'UTF-8'}</span>
        </div>
    {/if}
    {if !$displayPriceTaxIncluide && $isTaxesEnabled}
        <div class="cart-summary-line">
            <span class="label">{$cartPresenterVars.totals.total.label|escape:'htmlall':'UTF-8'}&nbsp;{$cartPresenterVars.labels.tax_short|escape:'htmlall':'UTF-8'}</span>
            <span class="value">{$cartPresenterVars.totals.total.value|escape:'htmlall':'UTF-8'}</span>
        </div>
        <div class="cart-summary-line cart-total">
            <span class="label">{$cartPresenterVars.totals.total_including_tax.label|escape:'htmlall':'UTF-8'}</span>
            <span class="value">{$cartPresenterVars.totals.total_including_tax.value|escape:'htmlall':'UTF-8'}</span>
        </div>
    {else}
        <div class="cart-summary-line cart-total">
            <span class="label">{$cartPresenterVars.totals.total.label|escape:'htmlall':'UTF-8'}&nbsp;{if $isTaxesEnabled}{$cartPresenterVars.labels.tax_short|escape:'htmlall':'UTF-8'}{/if}</span>
            <span class="value">{$cartPresenterVars.totals.total.value|escape:'htmlall':'UTF-8'}</span>
        </div>
    {/if}
</div>