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


<div class="lrp-breadcrumb" style="margin-bottom: 10px;">
	<i class="material-icons" style="position: relative; top: 7px;">home</i>
	<span data-url="" data-index="0">
		<a href="{$module_config_url|escape:'quotes':'UTF-8'}" style="color:#444; text-decoration: underline">
			{l s='Customer Groups' mod='loyaltyrewardpoints'}
		</a>
	</span>
</div>

<ul class="nav nav-tabs lrp-nav-tabs" id="myTab" role="tablist">
	<li class="nav-item active">
		<a class="nav-link" data-toggle="tab" href="#lrp-general-tab" role="tab">{l s='General' mod='loyaltyrewardpoints'}</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#lrp-rules-tab" role="tab">{l s='Point Rules' mod='loyaltyrewardpoints'}</a>
	</li>
</ul>

<div class="tab-content">
	<div class="tab-pane pap-tab-pane active" id="lrp-groups-tab" role="tabpanel">
		<div class="canvas"></div>
	</div>
	<div class="tab-pane pap-tab-pane" id="lrp-general-tab" role="tabpanel">
		<div class="canvas panel"></div>
	</div>
	<div class="tab-pane pap-tab-pane" id="lrp-rules-tab" role="tabpanel">
		<div class="canvas panel"></div>
	</div>
</div>

{*
<div>
	<a href="{$module_config_url|escape:'quotes':'UTF-8'}&route=lrpadminconfigmigrationcontroller" class="btn btn-primary">
		{l s='Migrate Points from Prestashop Loyalty Module' mod='loyaltyrewardpoints'}
	</a>
</div>
*}

<script>
	$(document).ready(function () {
        if (typeof address_token === "undefined") {
            address_token = '{$address_token|escape:'quotes':'UTF-8'}';
        }
        breadcrumb = new Breadcrumb(".lrp-breadcrumb", "#lrp-groups-tab");
		module_config_url = '{$module_config_url|escape:'quotes':'UTF-8'}';
		module_ajax_url_lrp = '{$module_ajax_url|escape:'quotes':'UTF-8'}';
		lrp_groups_controller = new LRPAdminConfigGroupsController('#lrp-groups-tab .canvas');
        lrp_insights_controller = new LRPAdminConfigInsightsController('#lrp-insights-tab');
	});
</script>