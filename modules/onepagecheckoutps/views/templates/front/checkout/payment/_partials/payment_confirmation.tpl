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

{if $isLogged}
    <p id="payment-confirmation-alert" class="alert alert-warning col-xs-12 text-md-center"></p>
    <div id="payment-confirmation" class="js-payment-confirmation">
        <div class="ps-shown-by-js">
            <button id="btn-placer_order" type="submit" class="btn btn-primary" {if $defaultPaymentMethod neq ''}disabled{/if}>
                <i class="material-icons">done</i>
                {l s='Checkout' mod='onepagecheckoutps'}
            </button>
        </div>
    </div>
{else}
    <p class="alert alert-warning col-xs-12 text-md-center">
        {l s='It is necessary to have an active session to complete the purchase.' mod='onepagecheckoutps'}
    </p>
{/if}