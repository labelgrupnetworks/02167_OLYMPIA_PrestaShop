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

LRPAdminConfigInsightsController = function (wrapper) {
	var self = this;
	self.wrapper = wrapper;
	self.$wrapper = $(wrapper);
	self.controller = 'lrpadminconfiginsightscontroller';

	self.btn_show = self.wrapper + ' .btn-show';
    self.btn_clear = self.wrapper + ' .btn-clear';
    self.input_date_start = self.wrapper + ' input[name="date-start"]';
    self.input_date_end = self.wrapper + ' input[name="date-end"]';

	self.div_referral_clicks_count = self.wrapper + ' #referral_clicks_count';
    self.div_referral_orders_count = self.wrapper + ' #referral_orders_count';
    self.div_referral_new_customers_count = self.wrapper + ' #referral_new_customers_count';
    self.div_referral_redeemed_total = self.wrapper + ' #referral_redeemed_total';
    self.div_total_orders_redeemed = self.wrapper + " #total_orders_redeemed";
    self.div_total_points_redeemed = self.wrapper + " #total_points_redeemed";
    self.div_total_points_value_redeemed = self.wrapper + " #total_points_value_redeemed";
    self.div_total_unique_customer_redeemers = self.wrapper + " #total_unique_customer_redeemers";
    self.div_customer_stats = self.wrapper + ' #customer-stats';

    self.setPanelStats = function(panel, stat) {
        $(panel).find("span.stat").html(stat);
    }

    self.getCustomerStats = function() {
        MPTools.waitStart();
        $.ajax({
            type: 'POST',
            url: module_config_url + '&route=' + self.controller + '&action=getcustomerstats',
            async: true,
            cache: false,
            data: {
                'date_start': $(self.input_date_start).val(),
                'date_end': $(self.input_date_end).val(),
            },
            success: function (result) {
                $(self.div_customer_stats).html(result);
                MPTools.waitEnd();
            }
        });
    }

	self.getStats = function() {
        MPTools.waitStart();
        $.ajax({
            type: 'POST',
            url: module_config_url + '&route=' + self.controller + '&action=getstats',
            async: true,
            cache: false,
            dataType: 'json',
            data: {
                'date_start' : $(self.input_date_start).val(),
                'date_end': $(self.input_date_end).val(),
            },
            success: function (result) {
                self.setPanelStats(self.div_referral_clicks_count, result.referral_clicks_count);
                self.setPanelStats(self.div_referral_orders_count, result.referral_orders_count);
                self.setPanelStats(self.div_referral_new_customers_count, result.referral_new_customers_count);
                self.setPanelStats(self.div_referral_redeemed_total, result.referral_redeemed_total);

                self.setPanelStats(self.div_total_orders_redeemed, result.total_orders_redeemed);
                self.setPanelStats(self.div_total_points_redeemed, result.total_points_redeemed);
                self.setPanelStats(self.div_total_points_value_redeemed, result.total_points_value_redeemed);
                self.setPanelStats(self.div_total_unique_customer_redeemers, result.total_unique_customer_redeemers);
                MPTools.waitEnd();
                self.getCustomerStats();
            }
        });
    };

	/* function render main form into the tab canvas */
	self.init = function () {
	    self.getStats();
	};
	self.init();

    /**
     * Events
     */
    $("body").on("click", self.wrapper + " .tabs .tab", function () {
        let content_id = $(this).attr('data-for');
        $(self.wrapper).find(".tabs .tab").removeClass('active');
        $(this).addClass('active');
        $(self.wrapper).find(".tabs .tab-content").hide();
        $(self.wrapper).find("#" + content_id).show();
        return false;
    });

    $("body").on("click", self.btn_show, function () {
        self.getStats();
        return false;
    });
};
