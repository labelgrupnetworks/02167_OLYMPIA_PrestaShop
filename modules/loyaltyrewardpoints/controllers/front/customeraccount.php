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
 * @copyright 2016-2017 Musaffar Patel
 * @license   LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class LoyaltyRewardPointsCustomerAccountModuleFrontController extends ModuleFrontControllerCore
{
    public $display_column_right = false;
    public $display_column_left = true;

    public function __construct()
    {
        $this->auth = true;
        parent::__construct();
    }

    public function setMedia()
    {
        parent::setMedia();
    }

    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        $page['body_classes']['page-customer-account'] = true;
        return $page;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();
        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Loyalty Reward Points', [], 'Breadcrumb'),
            'url' => ''
        ];
        return $breadcrumb;
    }

    public function displayMain()
    {
        $lrp_config = new LRPConfigModel(Context::getContext()->currency->iso_code, LRPCustomerHelper::getGroupID(), Context::getContext()->shop->id);
        $lrp_customer = new LRPCustomerModel();
        $lrp_history = new LRPHistoryModel();
        $lrp_customer->loadByCustomerID(Context::getContext()->customer->id);
        $lrp_history = $lrp_history->getByCustomerID(Context::getContext()->customer->id);

        foreach ($lrp_history['result'] as &$history) {
            $order = new Order($history->id_order);
            $currency = new Currency($history->id_currency);

            if (empty($currency->id) && !empty($order->id)) {
                $currency = new Currency($order->id_currency);
            }

            if (empty($currency->id) && empty($order->id)) {
                $currency = new Currency(Context::getContext()->currency->id);
            }

            $history->reference = $order->reference;
            $history->points_value = LRPUtilityHelper::formatPrice($history->point_value * $history->points, $currency);

            if ($history->type == LRPHistoryModel::TYPE_REWARDED) {
                $history->expires_date = LRPHistoryHelper::getPointsExpiryDate($history->date_add, $lrp_config->getPointsExpireDays());
                $history->expires_date_formatted = Tools::displayDate(date('Y-m-d H:i:s', strtotime($history->expires_date)), Context::getContext()->language->id, false);
                $history->expired = 0;
                $history->expires_days = LRPHistoryHelper::getDateDifferenceInDays(date('Y-m-d'), $history->expires_date);
                if ($history->expires_days <= 0) {
                    $history->expired = 1;
                }
            } else {
                $history->expires_days = 0;
                $history->expired = 0;
                $history->expires_date_formatted = '';
                $history->expires_date = '';
            }
            $history->date_add_formatted = Tools::displayDate(date('Y-m-d H:i:s', strtotime($history->date_add)), Context::getContext()->language->id, false);
        }

        if ($lrp_config->getPointsExpireDays() > 0) {
            $expiry_enabled = 1;
        } else {
            $expiry_enabled = 0;
        }

        $points_available = LRPCustomerHelper::getTotalPointsAvailable(Context::getContext()->customer->id, Context::getContext()->cart->id);

        $this->context->smarty->assign(array(
            'points_available' => $points_available,
            'lrp_history' => $lrp_history['result'],
            'lrp_customer' => $lrp_customer,
            'referral_enabled' => $lrp_config->getReferralEnabled(),
            'referral_points' => $lrp_config->getReferralPoints(),
            'referral_friend_points' => $lrp_config->getReferralFriendPoints(),
            'referral_points_value' => Tools::displayPrice(LRPDiscountHelper::getPointsMoneyValue($lrp_config->getReferralPoints())),
            'referral_friend_points_value' => Tools::displayPrice(LRPDiscountHelper::getPointsMoneyValue($lrp_config->getReferralFriendPoints())),
            'referral_link' => LRPReferralHelper::getCustomerReferralLink(),
            'expiry_enabled' => $expiry_enabled
        ));
        $this->setTemplate('module:loyaltyrewardpoints/views/templates/front/account/account.tpl');
    }

    /**
     * Display the link block on the customer account page
     * @return string
     */
    public static function renderIndexBlock($module)
    {
        Context::getContext()->smarty->assign(array(
            'baseDir' => __PS_BASE_URI__
        ));
        return $module->display($module->module_file, 'views/templates/front/account/link.tpl');
    }

    public function initContent()
    {
        parent::initContent();
        $this->displayMain();
    }
}
