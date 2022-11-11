{*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author Musaffar Patel <musaffar.patel@gmail.com>
*  @copyright 2016-2021 Musaffar Patel
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Property of Musaffar Patel
*}

<div class="tabs">
    <div class="tabs-row">
        <div class="tab active" data-for="top-referrers-by-order">
            {l s='Top Referrers' mod='loyaltyrewardpoints'}
        </div>
        <div class="tab" data-for="top_redeemers">
            {l s='Top Redeemers' mod='loyaltyrewardpoints'}
        </div>
    </div>

        <div id="top-referrers-by-order" class="tab-content">
            <p class="alert alert-info">
                {l s='Below are the list of customers who have referred the most new customers to the site and which have placed orders' mod='loyaltyrewardpoints'}
            </p>
            <div class="table-responsive-row clearfix">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{l s='Customer ID' mod='loyaltyrewardpoints'}</th>
                            <th>{l s='Name' mod='loyaltyrewardpoints'}</th>
                            <th>{l s='Email' mod='loyaltyrewardpoints'}</th>
                            <th>{l s='Orders from referrals' mod='loyaltyrewardpoints'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$top_referrers_by_order item=referrer}
                            <tr>
                                <td>
                                    {$referrer.id_referrer|escape:'htmlall':'UTF-8'}
                                </td>
                                <td>
                                    {$referrer.firstname|escape:'htmlall':'UTF-8'} {$referrer.lastname|escape:'htmlall':'UTF-8'}
                                </td>
                                <td>
                                    {$referrer.email|escape:'htmlall':'UTF-8'}
                                </td>
                                <td>
                                    {$referrer.total|escape:'htmlall':'UTF-8'}
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>

    <div id="top_redeemers" class="tab-content" style="display: none">
        <p class="alert alert-info">
            {l s='Below are the list of customers who have redeemed the most points' mod='loyaltyrewardpoints'}
        </p>
        <div class="table-responsive-row clearfix">
            <table class="table">
                <thead>
                <tr>
                    <th>{l s='Customer ID' mod='loyaltyrewardpoints'}</th>
                    <th>{l s='Name' mod='loyaltyrewardpoints'}</th>
                    <th>{l s='Email' mod='loyaltyrewardpoints'}</th>
                    <th>{l s='Orders' mod='loyaltyrewardpoints'}</th>
                    <th>{l s='Points Total' mod='loyaltyrewardpoints'}</th>
                    <th>{l s='Points Total Value' mod='loyaltyrewardpoints'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$top_redeemers item=referrer}
                    <tr>
                        <td>
                            {$referrer.id_customer|escape:'htmlall':'UTF-8'}
                        </td>
                        <td>
                            {$referrer.firstname|escape:'htmlall':'UTF-8'} {$referrer.lastname|escape:'htmlall':'UTF-8'}
                        </td>
                        <td>
                            {$referrer.email|escape:'htmlall':'UTF-8'}
                        </td>
                        <td>
                            {$referrer.total|escape:'htmlall':'UTF-8'}
                        </td>
                        <td>
                            {$referrer.points|escape:'htmlall':'UTF-8'}
                        </td>
                        <td>
                            {$referrer.points_value|escape:'htmlall':'UTF-8'}
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</div>

