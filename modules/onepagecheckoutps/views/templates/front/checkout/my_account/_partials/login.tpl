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

<div id="opc_login">
    <div class="text-center mb-2 title">
        {l s='Login' mod='onepagecheckoutps'}
    </div>
    <form id="form-login" autocomplete="off">
        <div class="form-group">
            <label class="control-label">
                {l s='Email' mod='onepagecheckoutps'}
            </label>
            <input
                class="form-control required"
                type="text"
                id="txt-login_email"
                name="email"
                placeholder="{l s='Email' mod='onepagecheckoutps'}"
                data-validation="isEmail" />
        </div>
        <div class="form-group">
            <label class="control-label">
                {l s='Password' mod='onepagecheckoutps'}
            </label>
            <input
                class="form-control required"
                type="password"
                id="txt-login_password"
                name="password"
                placeholder="{l s='Password' mod='onepagecheckoutps'}"
                data-validation="isPasswd" />
        </div>
        <br/>

        <div id="btn_login_content">
            <p class="forget_password">
                <a class="d-block" href="{$urls.pages.password|escape:'htmlall':'UTF-8'}">{l s='Forgot your password?' mod='onepagecheckoutps'}</a>
            </p>
            <button class="btn btn-primary d-block mb-1" type="button" id="btn-opc_login">
                {l s='Sing In' mod='onepagecheckoutps'}
            </button>
        </div>

        {* Support module: idxrdefender - v1.3.2 - innovadeluxe *}
        {hook h="displayIdxeCustomerLoginForm" seccion="login"}
    </form>
</div>