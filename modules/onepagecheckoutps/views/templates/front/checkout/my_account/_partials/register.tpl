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

<div id="opc_register">
    <div class="text-center title mb-1">
        {if $OPC.MyAccount.isGuestAllowed}
            {l s='Guest checkout' mod='onepagecheckoutps'}
        {else}
            {l s='New customer' mod='onepagecheckoutps'}
        {/if}
    </div>
    <p>
        {if $OPC.MyAccount.isGuestAllowed}
            {l s='Proceed to checkout, and you can create an account at the end.' mod='onepagecheckoutps'}
        {else}
            {l s='Register with us for a faster checkout, to track the status of your order and more.' mod='onepagecheckoutps'}
        {/if}
    </p>
    <form id="form-register" autocomplete="off">
        <div class="form-group">
            {* <label class="control-label">
                {l s='Email' mod='onepagecheckoutps'}
            </label> *}
            <input
                class="form-control"
                type="text"
                id="txt-email_register"
                name="email"
                placeholder="{l s='Email' mod='onepagecheckoutps'}"
                data-validation="isEmail"
                value="{if isset($customer)}{$customer.email|escape:'htmlall':'UTF-8'}{/if}"/>
        </div>

        {block name='checkout_form_register_button'}
            <button class="btn btn-primary w-100 d-block mb-1" type="button" id="btn-register">
                {if $OPC.MyAccount.isGuestAllowed}
                    {l s='Continue as guest' mod='onepagecheckoutps'}
                {else}
                    {l s='Create account' mod='onepagecheckoutps'}
                {/if}
            </button>
        {/block}
    </form>
</div>