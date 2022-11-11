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

LRPAdminConfigMigrationPsloyaltyrewardsController = function (wrapper) {
	var self = this;
	self.wrapper = wrapper;
	self.$wrapper = $(wrapper);
	self.controller = 'lrpadminconfigmigrationpsloyaltycontroller';
	self.route_url = module_config_url + '&route=' + self.controller + '&action=render';

	/**
	 * Render general form
	 */
	self.render = function () {
		var post_data = {};

		MPTools.waitStart();
		$.ajax({
			type: 'POST',
			url: module_config_url + '&route=' + self.controller + '&action=render',
			async: true,
			cache: false,
			data: {},
			success: function (html_content) {
				MPTools.waitEnd();
				self.$wrapper.html(html_content);
			}
		});
	};

	self.process = function() {
		let $form = self.$wrapper.find("form");
		MPTools.waitStart();
		$.ajax({
			type: 'POST',
			url: module_config_url + '&route=' + self.controller + '&action=process',
			async: true,
			cache: false,
			data: $form.serialize(),
			success: function (html_content) {
				console.log(html_content);
				MPTools.waitEnd();
				$.growl.notice({title: "", message: 'Points have been migrated'});
			}
		});
	};

	/* function render main form into the tab canvas */
	self.init = function () {
		self.render();
	};
	self.init();

	/**
	 * Events
 	 */

	$("body").on("click", self.wrapper + " button[name='submitloyaltyrewardpoints']", function () {
		self.process();
		return false;
	});

};
