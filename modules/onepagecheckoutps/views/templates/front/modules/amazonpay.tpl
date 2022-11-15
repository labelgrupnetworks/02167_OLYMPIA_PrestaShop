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

<div id="amazonpay_information" class="alert alert-info">
    <p>
        {l s='You are going to finalize the purchase with Amazon Pay' mod='onepagecheckoutps'}
    </p>
    <p>
        <img src="{$amazonpay_logo_url|escape:'htmlall':'UTF-8'}" alt="{l s='Amazon Pay' mod='onepagecheckoutps'}"/>
    </p>
    <p>
        {l s='If you want to change your payment option and address in Amazon Pay or select another payment method, click' mod='onepagecheckoutps'}&nbsp;<a href="{$amazonpay_reset_session_url|escape:'htmlall':'UTF-8'}">{l s='here' mod='onepagecheckoutps'}</a>
    </p>
    <div class="module_payment_container hidden">
        <div class="payment_input">
            <input type="radio" id="payment-option-1" name="payment-option" class="payment_radio not_unifrom not_uniform" value="amazonpay" data-module-name="amazonpay" data-force-display="0" checked />
            <input type="hidden" id="url_module_payment_1" value="{$amazonpay_redirect_url|escape:'htmlall':'UTF-8'}">
        </div>
    </div>
</div>