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

LRPAdminCustomerController = function (wrapper) {
	var self = this;
	self.wrapper = wrapper;
	self.$wrapper = $(wrapper);
	self.controller = 'lrpadmincustomercontoller';

	/**
	 * Render general form
	 */
	self.renderList = function (page = 1) {
		MPTools.waitStart();
		$.ajax({
			type: 'POST',
			url: module_config_url + '&route='+self.controller+'&action=renderlist',
			async: true,
			cache: false,
			data: {
				'current_page' : page,
				'id_customer' : id_customer
			},
			success: function (html_content) {
				MPTools.waitEnd();
				self.$wrapper.find("#lrp-history-list").html(html_content);
			}
		});
	};

	/**
	 * add or subtract points from the customer
	 */
	self.processUpdatePoints = function() {
		let $form = self.$wrapper.find("#lrp-customer-points-form");
		MPTools.waitStart();
		$.ajax({
			type: 'POST',
			url: module_config_url + '&route='+self.controller+'&action=processupdatepoints',
			async: true,
			cache: false,
			data: $form.serialize() + '&id_customer='+id_customer,
			success: function (result) {
				$.growl.notice({title: "", message: 'Points Updated'});
				MPTools.waitEnd();
				self.renderList();
			}
		});
	};


	/* function render main form into the tab canvas */
	self.init = function () {
		self.renderList();
	};
	self.init();

	/**
	 * Pagination Link Click
	 */
	$("body").on("click", self.wrapper + " #lrp-history-pagination a.page", function () {
		self.renderList($(this).attr("data-page"));
		return false;
	});

	/**
	 * On points update button click
 	 */
	$("body").on("click", self.wrapper + " #btn-lrp-update", function () {
		self.processUpdatePoints();
		return false;
	});
};