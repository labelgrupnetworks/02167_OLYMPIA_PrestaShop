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

{if $cartPresenterVars.vouchers.allowed and $showVoucherBox}
    <div id="promo-code">
        <div class="promo-code">
            {* <span class="promo-title">
                {l s='Have a promo code?' mod='onepagecheckoutps'}
            </span> *}
            <form action="{$cartUrl|escape:'htmlall':'UTF-8'}" name="add-voucher" method="post">
                <input type="hidden" name="token" value="{$staticToken|escape:'htmlall':'UTF-8'}">
                <input type="hidden" name="addDiscount" value="1">
                <input type="hidden" name="action" value="update">
                <input class="promo-input" type="text" name="discount_name" placeholder="{l s='Promo code' mod='onepagecheckoutps'}">
                <button type="submit" class="btn btn-primary"><span>{l s='Add' mod='onepagecheckoutps'}</span></button>
            </form>
        </div>
    </div>

    {if $cartPresenterVars.discounts|count > 0}
        <p class="promo-highlighted">
            {l s='Take advantage of our exclusive offers:' mod='onepagecheckoutps'}
        </p>
        <ul class="js-discount promo-discounts">
            {foreach from=$cartPresenterVars.discounts item=discount}
                <li class="cart-summary-line">
                    <span class="label">
                        <span class="code">{$discount.code|escape:'htmlall':'UTF-8'}</span> - {$discount.name|escape:'htmlall':'UTF-8'}
                    </span>
                </li>
            {/foreach}
        </ul>
    {/if}
{/if}