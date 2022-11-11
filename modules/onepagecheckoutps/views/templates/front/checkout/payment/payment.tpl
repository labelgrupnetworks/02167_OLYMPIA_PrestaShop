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

<div class="row">
    <div class="col-xs-12">
        {if $isFree}
            <p id="free_order" class="alert alert-warning col-xs-12 col-12 text-md-center">
                {l s='Free Order.' mod='onepagecheckoutps'}
            </p>
        {else}
            {hook h='displayPaymentTop'}
        {/if}

        {include file='module:onepagecheckoutps/views/templates/front/checkout/payment/_partials/list.tpl'}

        {if $isShowDiscountBoxOnPaymentEnabled}
            {include file='module:onepagecheckoutps/views/templates/front/checkout/cart/_partials/cart-voucher.tpl'}
        {/if}
        {include file='module:onepagecheckoutps/views/templates/front/checkout/payment/_partials/create_account.tpl'}
        {include file='module:onepagecheckoutps/views/templates/front/checkout/payment/_partials/condition.tpl'}
        {include file='module:onepagecheckoutps/views/templates/front/checkout/payment/_partials/confirm_address_delivery.tpl'}
        {include file='module:onepagecheckoutps/views/templates/front/checkout/payment/_partials/payment_confirmation.tpl'}

        {hook h='displayPaymentByBinaries'}
    </div>
</div>