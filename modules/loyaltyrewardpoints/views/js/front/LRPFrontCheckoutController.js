/*
 * 2007-2015 PrestaShop
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
 *  @version  Release: $Revision$
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Property of Musaffar Patel
 */

LRPFrontCheckoutController = function(wrapper) {
    const self = this;
    self.wrapper = wrapper;
    self.$wrapper = $(wrapper);

    self.controller = 'lrpfrontcheckoutcontroller';

    self.redeemPoints = function() {
		$.ajax({
			type: 'POST',
			headers: {"cache-control": "no-cache"},
			url: lrp_module_ajax_url,
			async: true,
			cache: false,
			dataType: "json",
			data: 'route=lrpfrontcheckoutcontroller&action=processredeempoints&points=' + $(self.wrapper).find('input[name="points"]').val() + '&rand=' + new Date().getTime(),
			success: function (result) {
				if (result.message != '') {
					alert(result.message);
					return false;
				}
				window.location.reload();
				return false;
			}
		});
	};

	/**
	 * Clear any points redeemed ion the cart and reset to 0
 	 */
    self.clearPoints = function() {
		$.ajax({
			type: 'POST',
			headers: {"cache-control": "no-cache"},
			url: lrp_module_ajax_url,
			async: true,
			cache: false,
			//dataType: "json",
			data: 'route=' + self.controller + '&action=processclearpoints&rand=' + new Date().getTime(),
			success: function (jsonData, textStatus, jqXHR) {
				window.location.reload();
			}
		});
	};

	/**
	 * Update the number of points to be rewarded in the cart via ajax
 	 * @param id_wrapper
	 */
    self.updatePointsSummary = function(id_wrapper) {
		$.ajax({
			type: 'POST',
			headers: {"cache-control": "no-cache"},
			url: lrp_module_ajax_url,
			async: true,
			cache: false,
			data: 'route=' + self.controller + '&action=renderpointssummary&rand=' + new Date().getTime(),
			success: function (result, textStatus, jqXHR) {
				$("#"+id_wrapper).replaceWith(result);
			}
		});

	};

    self.init = function () {
    };
    self.init();

    /** Events **/

	/**
	 * Redeem points
 	 */
    $("body").on("click", "#btn-lrp-redeem", function () {
		self.redeemPoints();
		return false;
	});

	/**
	 * Clear points icon click
 	 */
    $("body").on("click", "a#lrp-points-clear", function () {
		self.clearPoints();
		return false;
	});

	/**
	 * Toggle the display of the points redeem form
 	 */
    $("body").on("click", "a#lrp-redeem-link", function () {
		$(self.wrapper).find("#lrp-redeem-form").slideToggle();
		return false;
	});

	/**
	 * On emit updateCart event
 	 */
	prestashop.on('updateCart', function (event) {
	    // @todo: update redeem points panel via ajax instead of page reload
        location.reload();
		self.updatePointsSummary("cart-points-summary");
		if (event.reason.linkAction == 'remove-voucher') {
			location.reload();
		}
	});
};