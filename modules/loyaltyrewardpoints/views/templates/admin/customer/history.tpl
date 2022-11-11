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

<div class="col-lg-6">
	<div id="lrp-customer-history" class="panel card">
		<h3 class="card-header">
      		<i class="material-icons">remove_red_eye</i>
			{l s='Loyalty Reward Points' mod='loyaltyrewardpoints'}
    	</h3>

		<div class="card-body">

			<div class="card">
				<div class="card-body">
					<div class="row">
						<div class="col">
							{l s='Customer Points' mod='loyaltyrewardpoints'} :
							<span class="badge badge-success rounded" style="font-size: 14px;">
								{$points|escape:'htmlall':'UTF-8'}
							</span>
						</div>
					</div>
					<div class="row">
						<div class="col">
							<form id="lrp-customer-points-form" style="margin-top: 10px;">
								<select id="type" name="type" style="width: 100px; float: left; padding: 4px; margin-right: 10px;">
									<option value="add">{l s='Add' mod='loyaltyrewardpoints'}</option>
									<option value="subtract">{l s='Subtract' mod='loyaltyrewardpoints'}</option>
								</select>

								<select id="id_currency" name="id_currency" style="width: 100px; float: left; padding: 4px;">
									{foreach from=$currencies item=currency}
										<option value="{$currency.id_currency|escape:'htmlall':'UTF-8'}">{$currency.iso_code|escape:'htmlall':'UTF-8'}</option>
									{/foreach}
								</select>

								<div class="input-group" style="width: 180px; float: left; margin-left: 10px;">
									<input type="text" id="points" name="points" value="0" size="10" style="padding: 4px;">
									<span class="input-group-addon" style="margin-left: 10px; padding-top: 3px;">{l s='points' mod='loyaltyrewardpoints'}</span>
								</div>

								<button type="submit" id="btn-lrp-update" class="btn btn-default" style="margin-left: 10px;">
									<i class="icon-save"></i>
									{l s='Update' mod='loyaltyrewardpoints'}
								</button>

							</form>
						</div>
					</div>
				</div>
			</div>

			<div id="lrp-history-list">
			</div>

		</div>


	</div>
</div>

<script>
	id_customer = {$id_customer|escape:'htmlall':'UTF-8'};
	module_config_url = '{$module_config_url|escape:'quotes':'UTF-8'}';

	$(document).ready(function () {
		lrp_admin_customer_controller = new LRPAdminCustomerController('#lrp-customer-history');
	});
</script>