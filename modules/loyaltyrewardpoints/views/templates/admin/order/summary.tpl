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

<div id="lrp-admin-order-summary-panel" class="panel">
	<div class="panel-heading">
    	<i class="icon-money"></i>
    	{l s='Loyalty Reward Points' mod='loyaltyrewardpoints'}
    </div>

	<h4>{l s='Redeemed' mod='loyaltyrewardpoints'}</h4>
	<dl class="well list-detail {if $redeemed_points eq 0}disabled{/if}">
		<div class="lrp-box">
			<dt>{l s='Points Redeemed' mod='loyaltyrewardpoints'}</dt>
			<dd><span class="badge badge-success">{$redeemed_points|escape:'htmlall':'UTF-8'}</span></dd>
		</div>

		<div class="lrp-box">
			<dt>{l s='Discount' mod='loyaltyrewardpoints'}</dt>
			<dd><span class="badge badge-success">{$redeemed_value|escape:'htmlall':'UTF-8'}</span></dd>
		</div>

		<div class="lrp-box">
			<dt>{l s='Point Value' mod='loyaltyrewardpoints'}</dt>
			<dd><span class="badge badge-success">{$redeemed_point_value|escape:'htmlall':'UTF-8'}</span></dd>
		</div>
	</dl>

	<h4>{l s='Rewarded' mod='loyaltyrewardpoints'}</h4>
	<dl class="well list-detail">
		<div class="lrp-box">
			<dt>{l s='Points Rewarded' mod='loyaltyrewardpoints'}</dt>
			<dd><span class="badge badge-success">{$rewarded_points|escape:'htmlall':'UTF-8'}</span></dd>
		</div>

		<div class="lrp-box">
			<dt>{l s='Point Value' mod='loyaltyrewardpoints'}</dt>
			<dd><span class="badge badge-success">{$rewarded_point_value|escape:'htmlall':'UTF-8'}</span></dd>
		</div>
	</dl>
</div>