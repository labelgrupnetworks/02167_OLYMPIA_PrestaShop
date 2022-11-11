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

LRPAdminConfigGroupsController = function (wrapper) {
	var self = this;
	self.wrapper = wrapper;
	self.$wrapper = $(wrapper);
	self.controller = 'lrpadminconfiggroupscontroller';
    self.switch_send_reminders = self.wrapper + ' input[name="send_point_reminder_emails"]';
    self.input_days1 = 'input[name="points_reminder_email_trigger_days_1"]';
    self.input_days2 = 'input[name="points_reminder_email_trigger_days_2"]';
    self.input_days3 = 'input[name="points_reminder_email_trigger_days_3"]';
    self.controller = 'lrpadminconfiggroupscontroller';
    self.route_url = module_config_url + '&route=' + self.controller + '&action=render';

	/**
	 * Hide the main tabs until a customer group is selected
 	 */
	self.hideTabs = function() {
		$(".lrp-nav-tabs").hide();
	};

	/**
	 * Show the tabs
 	 */
	self.showTabs = function() {
		$(".lrp-nav-tabs").show();
	};

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
                if ($(self.switch_send_reminders + ':checked').val() === '0') {
                    self.setReminderFieldsStatus(false);
                }
            }
		});
	};

	/**
	 * On group clicked
 	 * @param $sender
	 */
	self.onGroupClick = function($sender) {
		var id_group = $sender.attr("data-id_group");
		var post_data = {};

		self.showTabs();
		$("#lrp-groups-tab").hide();
		$("#lrp-general-tab").addClass("active");

		lrp_general_controller = new LRPAdminConfigGeneralController('#lrp-general-tab .canvas', id_group);
		lrp_rules_controller = new LRPAdminConfigRulesController('#lrp-rules-tab .canvas', id_group);

		let selected_group = $sender.attr("data-name");
		$(".lrp-breadcrumb").append("<span>&nbsp;" + selected_group + "</span>")
	};

    /**
     * set reminder fields enabled status
     */
    self.setReminderFieldsStatus = function (enabled) {
        let $wrapper_1 = self.$wrapper.find(self.input_days1).parent().closest('div.form-group');
        let $wrapper_2 = self.$wrapper.find(self.input_days2).parent().closest('div.form-group');
        let $wrapper_3 = self.$wrapper.find(self.input_days3).parent().closest('div.form-group');
        $wrapper_1.removeClass('disabled');
        $wrapper_2.removeClass('disabled');
        $wrapper_3.removeClass('disabled');
        $wrapper_1.removeClass('enabled');
        $wrapper_2.removeClass('enabled');
        $wrapper_3.removeClass('enabled');

        if (enabled) {
            $wrapper_1.addClass('enabled');
            $wrapper_2.addClass('enabled');
            $wrapper_3.addClass('enabled');
        } else {
            $wrapper_1.addClass('disabled');
            $wrapper_2.addClass('disabled');
            $wrapper_3.addClass('disabled');
        }
    };

    /**
     * on send reminder switch toggle
     * @param $sender
     */
    self.switchSendRemindersChange = function ($sender) {
        if ($sender.val() === '0') {
            self.setReminderFieldsStatus(false);
        } else {
            self.setReminderFieldsStatus(true);
        }
    };

    /**
     * save the global options
     */
	self.processGlobalForm = function() {
        MPTools.waitStart();
        $.ajax({
            type: 'POST',
            url: module_config_url + '&route=' + self.controller + '&action=processglobalform',
            async: true,
            cache: false,
            data: $(self.wrapper + " :input").serialize(),
            success: function (result) {
                $.growl.notice({title: "", message: 'Saved'});
                MPTools.waitEnd();
            }
        });
    };

	/* function render main form into the tab canvas */
	self.init = function () {
		self.hideTabs();
		self.render();
	};
	self.init();

	/**
	 * Events
 	 */

	$("body").on("click", self.wrapper + " #lrp-groups-list table tr.group", function () {
		self.onGroupClick($(this));
		return false;
	});


    $("body").on("click", self.wrapper + " .btn-global-save", function () {
        self.processGlobalForm();
        return false;
    });

    $("body").on("change", self.switch_send_reminders, function () {
        self.switchSendRemindersChange($(this));
        return false;
    });
};
