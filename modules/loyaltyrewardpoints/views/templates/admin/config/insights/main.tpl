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

<div id="date-filter">
    <div class="date-filter-info">
        {l s='Show insights in the following date range:' mod='loyaltyrewardpoints'}
    </div>
    <div class="date-filter-row">
        <div id="date-filter-start">
            <input type="date" name="date-start">
        </div>
        <div>
            {l s='and' mod='loyaltyrewardpoints'}
        </div>
        <div id="date-filter-end">
            <input type="date" name="date-end">
        </div>
        <div id="date-filter-actions">
            <button class="btn-show btn btn-primary">{l s='show' mod='loyaltyrewardpoints'}</button>
            <button class="btn-show btn btn-secondary">{l s='clear' mod='loyaltyrewardpoints'}</button>
        </div>
    </div>
</div>

<h3>{l s='Referrals Insights' mod='loyaltyrewardpoints'}</h3>

<div class="insights-row">
    <div id="referral_clicks_count" class="insight-summary-panel">
        <i class="material-icons">share</i>
        <div>
            <h4>{l s='Referral clicks' mod='loyaltyrewardpoints'}</h4>
            {l s='Number of clicks from referral links' mod='loyaltyrewardpoints'}
        </div>
        <span class="stat">-</span>
    </div>

    <div id="referral_orders_count" class="insight-summary-panel">
        <i class="material-icons">shopping_cart</i>
        <div>
            <h4>{l s='Referral Orders' mod='loyaltyrewardpoints'}</h4>
            {l s='Successfull orders placed referrals' mod='loyaltyrewardpoints'}
        </div>
        <span class="stat">-</span>
    </div>

    <div id="referral_new_customers_count" class="insight-summary-panel">
        <i class="material-icons">person_add</i>
        <div>
            <h4>{l s='Referral New Customers' mod='loyaltyrewardpoints'}</h4>
            {l s='New Cusotmer accounts acquired through referrals' mod='loyaltyrewardpoints'}
        </div>
        <span class="stat">-</span>
    </div>

    <div id="referral_redeemed_total" class="insight-summary-panel">
        <i class="material-icons">account_balance</i>
        <div>
            <h4>{l s='Referral Redeemed total' mod='loyaltyrewardpoints'}</h4>
            {l s='New Cusotmer accounts acquired through referrals' mod='loyaltyrewardpoints'}
        </div>
        <span class="stat">-</span>
    </div>
</div>

<h3>{l s='Points Insights' mod='loyaltyrewardpoints'}</h3>

<div class="insights-row">
    <div id="total_orders_redeemed" class="insight-summary-panel">
        <i class="material-icons">account_balance</i>
        <div>
            <h4>{l s='Orders with redeemed points' mod='loyaltyrewardpoints'}</h4>
            {l s='Number of orders for which points were redeemed' mod='loyaltyrewardpoints'}
        </div>
        <span class="stat">-</span>
    </div>

    <div id="total_points_redeemed" class="insight-summary-panel">
        <i class="material-icons">account_balance</i>
        <div>
            <h4>{l s='Points Redeemed' mod='loyaltyrewardpoints'}</h4>
            {l s='Total number of points redeemed' mod='loyaltyrewardpoints'}
        </div>
        <span class="stat">-</span>
    </div>

    <div id="total_unique_customer_redeemers" class="insight-summary-panel">
        <i class="material-icons">account_balance</i>
        <div>
            <h4>{l s='Redeeming customers' mod='loyaltyrewardpoints'}</h4>
            {l s='Number of unique customers which have redeemed points ' mod='loyaltyrewardpoints'}
        </div>
        <span class="stat">-</span>
    </div>

    <div id="total_points_value_redeemed" class="insight-summary-panel">
        <i class="material-icons">account_balance</i>
        <div>
            <h4>{l s='Points Value Redeemed' mod='loyaltyrewardpoints'}</h4>
            {l s='Total valud of points redeemed' mod='loyaltyrewardpoints'}
        </div>
        <span class="stat">-</span>
    </div>
</div>


<div id="customer-stats">

</div>