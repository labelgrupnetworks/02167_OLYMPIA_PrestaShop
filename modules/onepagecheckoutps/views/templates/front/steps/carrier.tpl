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

{if !$register_customer}
    <div id="onepagecheckoutps_step_two_container" class="{$classes|escape:'htmlall':'UTF-8'} {if isset($is_virtual_cart) && $is_virtual_cart}hidden{/if}">
        <h5 class="onepagecheckoutps_p_step onepagecheckoutps_p_step_two">
            <i class="fa-pts fa-pts-truck fa-pts-2x"></i>
            {l s='Shipping method' mod='onepagecheckoutps'}
        </h5>
        <div id="onepagecheckoutps_step_two"></div>
    </div>
{/if}