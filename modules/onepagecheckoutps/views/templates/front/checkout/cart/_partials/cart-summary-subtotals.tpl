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

<div class="cart-summary-subtotals-container">
    {foreach from=$cartPresenterVars.subtotals key="type_subtotal" item="subtotal"}
        {if $subtotal && $subtotal.value|count_characters > 0 && $subtotal.type !== 'tax'}
            {if $subtotal.type eq 'shipping' and $isVirtualCart}
                {continue}
            {/if}

            <div class="cart-summary-line cart-summary-subtotals" id="cart-subtotal-{$subtotal.type|escape:'htmlall':'UTF-8'}">
                <span class="label">
                    {$subtotal.label|escape:'htmlall':'UTF-8'}
                </span>
                <span class="value">
                    {if $type_subtotal eq 'discounts'}-{/if}
                    {$subtotal.value|escape:'htmlall':'UTF-8'}
                </span>
            </div>
        {/if}
    {/foreach}
</div>