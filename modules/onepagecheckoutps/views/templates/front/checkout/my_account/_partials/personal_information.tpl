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

<div id="opc_personal_information">
    <div id="hook-displayCustomerAccountFormTop">
        {hook h='displayCustomerAccountFormTop'}
    </div>

    <form id="form-customer" autocomplete="on">
        {$formCustomer nofilter}

        <div id="hook-displayCustomerAccountForm">
            {hook h='displayCustomerAccountForm'}
        </div>
    </form>

    {block name='checkout_personal_information_footer'}
        <div id="opc_personal_information_footer" class="opc-step-footer">
            <button type="button" class="back btn btn-secondary">
                <i class="material-icons">chevron_left</i>
                {l s='Back' mod='onepagecheckoutps'}
            </button>
            <button type="button" class="save btn btn-primary" name="opc_register">
                {l s='Save' mod='onepagecheckoutps'}
            </button>
        </div>
    {/block}
</div>