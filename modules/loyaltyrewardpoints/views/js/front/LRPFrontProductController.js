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

LRPFrontProductController = function(after_element, quickview) {
	let self = this;
	self.quickview = quickview;

	/**
	 * Get the Product ID
 	 * @returns {number}
	 */
	self.getProductID = function() {
		id_product = 0;
		if (self.quickview) {
			id_product = $(".modal").find("input[name='id_product']").val();
		} else {
			id_product = $("form#add-to-cart-or-refresh input[name='id_product']").val();
		}
		return id_product;
	};


	/**
	 * render the widget which displays points value for the product / selected attributes
 	 */
	self.renderWidget = function() {
		var query = $("#add-to-cart-or-refresh").serialize();

		var qty = 0;
		if ($("#quantity_wanted").length > 0) {
			qty = $("#quantity_wanted").val();
		}

		$.ajax({
			type: 'POST',
			url: lrp_module_ajax_url,
			async: true,
			cache: false,
			data: {
			    'route' : 'lrpfrontproductcontroller',
                'query' : query,
				'id_product': self.getProductID(),
				'action' : 'renderwidget',
				'qty' : qty,
                'rand' : new Date().getTime()
			},
			//dataType: 'json',
			success: function (html_content) {
				$("#lrp-product-widget").remove();
				if (self.quickview) {
					$(html_content).insertAfter(".quickview.modal " + after_element);
				} else {
					$(html_content).insertAfter("section#main " + after_element);
				}
			}
		});
	};

	self.init = function() {
		self.renderWidget();
	};
	self.init();

	/**
	 * On Attributes changed
	 */
	prestashop.on('updatedProduct', function (event) {
		self.renderWidget();
	});

	if (self.quickview) {
		$("body").on("change", " #quantity_wanted", function () {
			self.renderWidget();
			return false;
		});
	}
};