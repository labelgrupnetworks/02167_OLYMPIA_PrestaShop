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

<div class="delivery-options">
    {foreach from=$deliveryOptions item=carrier key=carrierId}
        <div
            class="delivery-option js-delivery-option delivery_option_{$carrier.id|escape:'htmlall':'UTF-8'}">
            <div class="carrier-item-content{if not $isShowCarrierImage} no-image{/if}">
                <div class="delivery_radio_content">
                    <input class="delivery_option_radio not_unifrom not_uniform" type="radio"
                        name="delivery_option[{$idAddress|escape:'htmlall':'UTF-8'}]"
                        id="delivery_option_{$carrier.id|escape:'htmlall':'UTF-8'}"
                        value="{$carrierId|escape:'htmlall':'UTF-8'}"
                        {if $deliveryOption == $carrierId}checked{/if}>
                </div>
                {if $isShowCarrierImage}
                    <div class="delivery_option_logo">
                        {if $carrier.logo}
                            <img src="{$carrier.logo|escape:'htmlall':'UTF-8'}"
                                alt="{$carrier.name|escape:'htmlall':'UTF-8'}"
                                class="img-thumbnail"
                                loading="lazy"
                            />
                        {else}
                            <img src="{$moduleImageUrl|escape:'htmlall':'UTF-8'}shipping.png"
                                alt="{$carrier.name|escape:'htmlall':'UTF-8'}"
                                class="img-thumbnail"
                                loading="lazy"
                            />
                        {/if}
                    </div>
                {/if}
                <div class="delivery-detail">
                    <div class="carrier-content-top">
                        <div class="delivery_option_title">
                            <span>
                                {$carrier.name|escape:'htmlall':'UTF-8'}
                            </span>
                        </div>
                        <div class="delivery_option_price">
                            <span>
                                {$carrier.price|escape:'htmlall':'UTF-8'}
                            </span>
                        </div>
                    </div>
                    {if $isShowCarrierDescription and $carrier.delay}
                        <div class="carrier-content-bottom">
                            <div class="delivery_option_delay">
                                <span>
                                    {$carrier.delay|escape:'htmlall':'UTF-8'}
                                </span>
                            </div>
                        </div>
                    {/if}
                </div>
            </div>
            <div class="row carrier-extra-content" {if $deliveryOption != $carrierId}style="display:none;"{/if}>
                {$carrier.extraContent nofilter}
            </div>
        </div>
    {/foreach}
</div>