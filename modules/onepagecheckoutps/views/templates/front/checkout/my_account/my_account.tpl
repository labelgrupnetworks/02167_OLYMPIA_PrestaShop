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

<div class="block-1">
    {if !$OPC.General.isLogged and !$OPC.General.isGuest}
        {include file='module:onepagecheckoutps/views/templates/front/checkout/my_account/_partials/login_social.tpl'}

        {block name='checkout_my_account_content'}
            {if $isShowLoginAndRegistrationInTabsEnabled}
                <div id="register_option_content" class="show-tabs">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-toggle="tab" data-bs-target="#tab_login" data-bs-toggle="tab" role="tab"
                                aria-selected="true">
                                {l s='Login' mod='onepagecheckoutps'}
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-toggle="tab" data-bs-target="#tab_register" data-bs-toggle="tab" role="tab"
                                aria-selected="false">
                                {if $OPC.MyAccount.isGuestAllowed}
                                    {l s='Guest checkout' mod='onepagecheckoutps'}
                                {else}
                                    {l s='New customer' mod='onepagecheckoutps'}
                                {/if}
                            </a>
                        </li>
                        {block name='checkout_my_account_content_item_tab'}{/block}
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade" id="tab_login" role="tabpanel">
                            {include file='module:onepagecheckoutps/views/templates/front/checkout/my_account/_partials/login.tpl'}
                        </div>
                        <div class="tab-pane fade show active" id="tab_register" role="tabpanel">
                            {include file='module:onepagecheckoutps/views/templates/front/checkout/my_account/_partials/register.tpl'}
                        </div>
                        {block name='checkout_my_account_content_item_content_tab'}{/block}
                    </div>
                </div>
            {else if}
                <div id="register_option_content">
                    <div class="left-content">
                        {include file='module:onepagecheckoutps/views/templates/front/checkout/my_account/_partials/login.tpl'}
                    </div>
                    <div class="right-content">
                        {include file='module:onepagecheckoutps/views/templates/front/checkout/my_account/_partials/register.tpl'}
                    </div>
                </div>
            {/if}
        {/block}
    {else}
        {include file='module:onepagecheckoutps/views/templates/front/checkout/my_account/_partials/logged.tpl'}
    {/if}
</div>
<div class="block-2" {if !$OPC.General.isLogged}style="display: none;" {/if}>
    {include file='module:onepagecheckoutps/views/templates/front/checkout/my_account/_partials/personal_information.tpl'}
</div>