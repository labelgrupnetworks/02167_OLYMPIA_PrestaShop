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

{if $isGuest}
    <div id="opc_create_account">
        <h5>
            <i class="material-icons">info</i>
            {l s='Remember me' mod='onepagecheckoutps'}
        </h5>
        <p class="card-text">
            {l s='Create an account and save time on your next purchase. You will also have access to your order history, personalized customer service, offers and more.' mod='onepagecheckoutps'}
        </p>
        <button id="btn-create_account" type="submit" class="btn btn-secondary">
            <i class="material-icons">person</i>
            {l s='Yes, I\'d like a %s account' sprintf=['%s' => $shopName] mod='onepagecheckoutps'}
        </button>
    </div>
{/if}