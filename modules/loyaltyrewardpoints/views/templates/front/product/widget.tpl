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

{if $points > 0}
	<div id="lrp-product-widget" class="card" style="padding: 20px; display: block;">
		{l s='Earn [1]%d points[/1] when you buy me!' sprintf=[$points] tags=['<strong>'] mod='loyaltyrewardpoints'}<br>
		{l s='That\'s worth [1]%s[/1]' sprintf=[$points_money_value] tags=['<strong>'] mod='loyaltyrewardpoints'}<br>
		<i class="material-icons">card_giftcard</i>
	</div>
{/if}