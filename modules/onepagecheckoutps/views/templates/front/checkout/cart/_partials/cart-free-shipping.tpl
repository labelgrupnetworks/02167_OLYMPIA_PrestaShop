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

{if $freeShipping|count > 0}
    <div id="cart_remaining_free_shipping" class="alert alert-info">
        <div id="cart_remaining_free_shipping_header">
            {l s='Add ' mod='onepagecheckoutps'} <b>{$freeShipping.missingPrice|escape:'htmlall':'UTF-8'}</b> {l s='to the cart to have FREE SHIPPING' mod='onepagecheckoutps'}
        </div>
        <div id="cart_remaining_free_shipping_body">
            <div id='cart_remaining_free_shipping_start_price'>
                <b>{$freeShipping.startFreeShippingPrice|escape:'htmlall':'UTF-8'}</b>
            </div>
            <div id='cart_remaining_free_shipping_bar'>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: {$freeShipping.actualPercent|escape:'htmlall':'UTF-8'|string_format:"%.2f"|cat:'%'}">
                        &nbsp;
                    </div>
                </div>
            </div>
            <div id='cart_remaining_free_shipping_end_price'>
                <b>{$freeShipping.endFreeShippingPrice|escape:'htmlall':'UTF-8'}</b>
            </div>
        </div>

    </div>
{/if}
