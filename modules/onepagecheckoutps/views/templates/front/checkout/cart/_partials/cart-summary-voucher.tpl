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

{if $cartPresenterVars.vouchers.allowed and $cartPresenterVars.vouchers.added}
    <div class="cart-promo">
        <ul>
            {foreach from=$cartPresenterVars.vouchers.added item=voucher}
                <li class="cart-summary-line">
                    <span class="label">
                        {$voucher.name|escape:'htmlall':'UTF-8'}
                    </span>
                    <span class="value">
                        <span>{$voucher.reduction_formatted|escape:'htmlall':'UTF-8'}</span>
                        <a href="{$voucher.delete_url|escape:'htmlall':'UTF-8'}" class="btn-remove-voucher">
                            <i class="material-icons md-18">delete</i>
                        </a>
                    </span>
                </li>
            {/foreach}
        </ul>
    </div>
{/if}