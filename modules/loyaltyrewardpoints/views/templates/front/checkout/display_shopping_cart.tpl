{*
* 2007-2017 Musaffar
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
*  @copyright  2016-2021 Musaffar Patel
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Property of Musaffar Patel
*}

{if $points_redeemed == 0}
	{if $points > 0}
		<div id="lrp-points" class="card-block">
			<span id="lrp-summary" class="label">
				<i class="material-icons" style="margin-top: -2px;">card_giftcard</i>
				{l s='You have %d points' sprintf=[$points] mod='loyaltyrewardpoints'}
				<a id="lrp-redeem-link" href="#lrp-redeem">{l s='redeem now' mod='loyaltyrewardpoints'}</a>
			</span>
			<div id="lrp-redeem-form" style="display: none">
				<input name="points" type="number" value="0" size="6" maxlength="6">
				<span class="points-label label">{l s='points' mod='loyaltyrewardpoints'}</span>
				<a href="#lrp-redeem" id="btn-lrp-redeem" class="btn btn-secondary">{l s='redeem' mod='loyaltyrewardpoints'}</a>
			</div>
		</div>
	{/if}
{else}
	<div id="lrp-points" class="card-block">
		<strong>{$points_redeemed|escape:'htmlall':'UTF-8'} {l s='points redeemed' mod='loyaltyrewardpoints'} ({$points_redeemed_value|escape:'htmlall':'UTF-8'})</strong>
		<a href="#" id="lrp-points-clear" class="material-icons">clear</a>
	</div>	
{/if}

<script>
	var isOnePageCheckoutModule = document.getElementById("onepagecheckoutps")
                                    || document.getElementById('spstepcheckout')
                                    || document.getElementById('module-steasycheckout-default')
                                    || document.getElementById('module-thecheckout-order');

	if (isOnePageCheckoutModule) {
	    if (typeof lrp_module_ajax_url === 'undefined') {
            lrp_module_ajax_url = "{$lrp_module_ajax_url|escape:'html':'UTF-8' nofilter}";
            lrp_front_checkout_controller = new LRPFrontCheckoutController("#lrp-points");
        }
	} else {
        window.addEventListener('load', function () {
            $(function () {
                lrp_module_ajax_url = "{$lrp_module_ajax_url|escape:'html':'UTF-8' nofilter}";
                let lrp_front_checkout_controller = new LRPFrontCheckoutController("#lrp-points");
            });
        });
	}
</script>
