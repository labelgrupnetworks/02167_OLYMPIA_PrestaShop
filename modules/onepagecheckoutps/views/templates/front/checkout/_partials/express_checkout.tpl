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

{if !$OPC.General.isLogged and !$OPC.General.isGuest and $OPC.Payment.hookDisplayExpressCheckout}
    <div id="expressCheckoutButtonList" class="text-center">
        <h5 id="expressCheckoutButtonListTitle">
            {l s='Express Checkout' mod='onepagecheckoutps'}
        </h5>

        <div id="expressCheckoutButtonListContent">
            {$OPC.Payment.hookDisplayExpressCheckout nofilter}
        </div>

        <div class="or-block row mt-3 mb-3 align-items-center">
            <div class="col">
                <hr>
            </div>
            <div class="col-1">
                <span class="or-text">
                    {l s='Or' mod='onepagecheckoutps'}
                </span>
            </div>
            <div class="col">
                <hr>
            </div>
        </div>
    </div>
{/if}