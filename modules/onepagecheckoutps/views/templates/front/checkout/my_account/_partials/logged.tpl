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

<div id="opc_logged">
    {if $OPC.General.isGuest}
        <div class="alert alert-info">
            {l s='Hello' mod='onepagecheckoutps'}&nbsp;<span id="btn-edit_customer">{$customer.firstname|escape:'htmlall':'UTF-8'}&nbsp;{$customer.lastname|escape:'htmlall':'UTF-8'}</span>,&nbsp;{l s='you can continue your purchase process as a guest or' mod='onepagecheckoutps'}&nbsp;<a href="{$logoutUrl|escape:'htmlall':'UTF-8'}">{l s='cancel it' mod='onepagecheckoutps'}</a>
        </div>
    {else}
        <p>
            {l s='Connected as' mod='onepagecheckoutps'}&nbsp;<span id="btn-edit_customer">{$customer.firstname|escape:'htmlall':'UTF-8'}&nbsp;{$customer.lastname|escape:'htmlall':'UTF-8'}</span>.
            <br/>
            {l s='Not are you?' mod='onepagecheckoutps'}&nbsp;<a href="{$logoutUrl|escape:'htmlall':'UTF-8'}">{l s='Log out' mod='onepagecheckoutps'}</a>
        </p>

        {if !isset($empty_cart_on_logout) || $empty_cart_on_logout}
            <div class="alert alert-info">
                <i class="material-icons">info</i>&nbsp; </i>{l s='If you sign out now, your cart will be emptied.' mod='onepagecheckoutps'}
            </div>
        {/if}
    {/if}
</div>