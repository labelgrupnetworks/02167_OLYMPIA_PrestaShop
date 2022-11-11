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

LRPAdminConfigRulesController = function (wrapper, id_group) {
	var self = this;
	self.wrapper = wrapper;
	self.$wrapper = $(wrapper);
	self.id_group = id_group;
	self.controller = 'lrpadminconfigrulescontroller';

	self.render = function() {
		MPTools.waitStart();
		$.ajax({
			type: 'POST',
			url: module_config_url + '&route='+self.controller+'&action=render',
			async: true,
			cache: false,
			data: {},
			success: function (html_content) {
				MPTools.waitEnd();
				self.$wrapper.html(html_content);
				self.renderAddForm(0);
				self.renderList();
			}
		});
	};

	/**
	 * Render general form
	 */
	self.renderAddForm = function (id_lrp_rule) {
		MPTools.waitStart();
		$.ajax({
			type: 'POST',
			url: module_config_url + '&route='+self.controller+'&action=renderaddform',
			async: true,
			cache: false,
			data: {
				'id_lrp_rule' : id_lrp_rule
			},
			success: function (html_content) {
				MPTools.waitEnd();
				self.$wrapper.find("#lrp-rule-add-wrapper").html(html_content);
			}
		});
	};

	/**
	 * Save the general settings
	 */
	self.processAddForm = function () {
		let $form = self.$wrapper.find("form");

		MPTools.waitStart();
		$.ajax({
			type: 'POST',
			url: module_config_url + '&route='+self.controller+'&action=process',
			async: true,
			cache: false,
			//dataType: 'text json',
			data: $form.serialize(),
			success: function (result) {
				$.growl.notice({title: "", message: 'Saved'});
				MPTools.waitEnd();
				self.renderList();
				self.renderAddForm();
			}
		});
	};

	/**
	 *
 	 */
	self.renderList = function() {
		MPTools.waitStart();
		$.ajax({
			type: 'POST',
			url: module_config_url + '&route=' + self.controller + '&action=renderlist',
			async: true,
			cache: false,
			data: {},
			success: function (html_content) {
				MPTools.waitEnd();
				self.$wrapper.find("#lrp-rule-list").html(html_content);
				return false;
			}
		});
	};

	/**
	 * Delete a rule
 	 * @param id_lrp_rule
	 */
	self.processDelete = function(id_lrp_rule) {
		MPTools.waitStart();
		$.ajax({
			type: 'POST',
			url: module_config_url + '&route=' + self.controller + '&action=processdelete',
			async: true,
			cache: false,
			data: {
				'id_lrp_rule' : id_lrp_rule
			},
			success: function (result) {
				MPTools.waitEnd();
				$.growl.notice({title: "", message: 'Removed Rule'});
				self.renderList();
				self.renderAddForm();
				return false;
			}
		});
	};


	/* function render main form into the tab canvas */
	self.init = function () {
		self.render();
	};
	self.init();

	/**
	 * On Add Rule form submit
	 */
	$("body").on("click", self.wrapper + " button[name='submitloyaltyrewardpoints']", function () {
		self.processAddForm();
		return false;
	});

	/**
	 * On Edit Rule click
	 */
	$("body").on("click", self.wrapper + " .lrp-rule-edit", function () {
		self.renderAddForm($(this).attr("data-id_lrp_rule"));
		return false;
	});

	/**
	 * delete action click
 	 */
	$("body").on("click", self.wrapper + " .lrp-rule-delete", function () {
		self.processDelete($(this).attr("data-id_lrp_rule"));
		return false;
	});

};