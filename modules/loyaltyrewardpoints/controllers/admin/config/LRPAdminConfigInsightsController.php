<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Musaffar Patel
 * @copyright 2016-2021 Musaffar Patel
 * @license   LICENSE.txt
 */

class LRPAdminConfigInsightsController extends LRPControllerCore
{
    protected $sibling;

    private $route = 'lrpadminconfiggroupscontroller';

    public function __construct(&$sibling = null)
    {
        parent::__construct($sibling);
        if ($sibling !== null) {
            $this->sibling = &$sibling;
        }
    }

    public function setMedia()
    {
    }

    public function getStats(): string
    {
        $date_start = '';
        $date_end = '';
        if (LRPUtilityHelper::isValidDate(Tools::getValue('date_start')) && LRPUtilityHelper::isValidDate(Tools::getValue('date_end'))) {
            $date_start = Tools::getValue('date_start');
            $date_end = Tools::getValue('date_end');
        }

        $id_default_currency = Configuration::get('PS_CURRENCY_DEFAULT');
        $currency_default = new Currency($id_default_currency);
        $json_return = array(
            'referral_clicks_count' => LRPInsightsHelper::getClicksCount($date_start, $date_end),
            'referral_orders_count' => LRPInsightsHelper::getReferralOrdersCount($date_start, $date_end),
            'referral_new_customers_count' => LRPInsightsHelper::getReferralNewCustomersCount($date_start, $date_end),
            'referral_redeemed_total' => LRPUtilityHelper::formatPrice(LRPInsightsHelper::getReferralRedeemedTotal($date_start, $date_end), $currency_default),
            'total_orders_redeemed' => LRPInsightsHelper::getTotalOrdersRedeemed($date_start, $date_end),
            'total_points_redeemed' => LRPInsightsHelper::getTotalPointsRedeemed($date_start, $date_end),
            'total_points_value_redeemed' => LRPUtilityHelper::formatPrice(LRPInsightsHelper::getTotalPointsValueRedeemed($date_start, $date_end), $currency_default),
            'total_unique_customer_redeemers' => LRPInsightsHelper::getTotalOrdersRedeemed($date_start, $date_end)
        );
        return json_encode($json_return);
    }

    public function getCustomerStats()
    {
        $date_start = '';
        $date_end = '';
        if (LRPUtilityHelper::isValidDate(Tools::getValue('date_start')) && LRPUtilityHelper::isValidDate(Tools::getValue('date_end'))) {
            $date_start = Tools::getValue('date_start');
            $date_end = Tools::getValue('date_end');
        }

        $id_default_currency = Configuration::get('PS_CURRENCY_DEFAULT');
        $currency_default = new Currency($id_default_currency);
        $top_redeemers = LRPInsightsHelper::getTopRedeemers($date_start, $date_end);
        array_walk($top_redeemers, function (&$value, $currency_default) {
            $value['points_value'] = LRPUtilityHelper::formatPrice($value['points_value'], $currency_default);
        });

        Context::getContext()->smarty->assign(array(
            'top_referrers_by_order' => LRPInsightsHelper::getTopReferrersByOrders($date_start, $date_end),
            'top_redeemers' => $top_redeemers
        ));
        return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/config/insights/top_referrers_by_order.tpl');
    }

    public function render()
    {
        Context::getContext()->smarty->assign(array(
        ));
        return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/config/insights/main.tpl');
    }

    public function route()
    {
        switch (Tools::getValue('action')) {
            case 'getstats':
                die($this->getStats());
            case 'getcustomerstats':
                die($this->getCustomerStats());
            default:
                die($this->render());
        }
    }
}
