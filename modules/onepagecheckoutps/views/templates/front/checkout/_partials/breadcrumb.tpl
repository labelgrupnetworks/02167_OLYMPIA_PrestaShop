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

{if $OPC.General.isLogged or !$OPC.General.forceCustomerRegistrationLogin}
    <div id="breadcrumb">
        <span id="breadcrumb_my_account" class="item-breadcrumb" data-step="my_account">{l s='Information' mod='onepagecheckoutps'}</span>

        <i class="material-icons">chevron_right</i>
        <span id="breadcrumb_addresses" class="item-breadcrumb" data-step="addresses">{l s='Addresses' mod='onepagecheckoutps'}</span>

        {if !$OPC.General.isVirtualCart}
            <i class="material-icons">chevron_right</i>
            <span id="breadcrumb_shipping" class="item-breadcrumb" data-step="shipping">{l s='Shipping' mod='onepagecheckoutps'}</span>
        {/if}

        <i class="material-icons">chevron_right</i>
        <span id="breadcrumb_payment" class="item-breadcrumb" data-step="payment">{l s='Payment' mod='onepagecheckoutps'}</span>
    </div>
{/if}