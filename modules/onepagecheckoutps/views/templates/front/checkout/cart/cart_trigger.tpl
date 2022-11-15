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

<aside id="opc_mobile_cart_trigger" class="btn-primary">
    <span id="cart_total_label_open">
        <i class="material-icons">shopping_cart</i>
        {l s='Show my cart' mod='onepagecheckoutps'}
    </span>
    <span id="cart_total_label_close" style="display: none;">
        <i class="material-icons">shopping_cart</i>
        {l s='Hide my cart' mod='onepagecheckoutps'}
    </span>
    <span id="cart_total_value">
        <span class="value_formatted">{$order_value|escape:'htmlall':'UTF-8'}</span>
        <i class="material-icons expand_more">expand_more</i>
        <i class="material-icons expand_less" style="display: none;">expand_less</i>
    </span>
</aside>
