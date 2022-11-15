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

{if $showAvailability}
    <div class="cart_avail">
        {strip}
            <span class="badge
                {if $product.quantity_available <= 0 || $product.cart_quantity > $product.quantity_available}
                    badge-danger product-unavailable
                {else}
                    badge-success product-available
                {/if}"
            >
                {if $product.quantity_available <= 0 || $product.cart_quantity > $product.quantity_available}
                    {if (isset($product.available_later) && $product.available_later) || (isset($psLabelOssProductsBoa) && $psLabelOssProductsBoa)}
                        {if isset($product.available_later) && $product.available_later}
                            {$product.available_later|escape:'htmlall':'UTF-8'}
                        {else}
                            {$psLabelOssProductsBoa|escape:'htmlall':'UTF-8'}
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
