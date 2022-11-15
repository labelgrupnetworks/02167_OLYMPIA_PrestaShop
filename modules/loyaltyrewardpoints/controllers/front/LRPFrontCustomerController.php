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

class LRPFrontCustomerController extends LRPControllerCore
{
    protected $sibling;

    private $route = 'lrpfrontcustomercontroller';

    public function __construct(&$sibling = null)
    {
        parent::__construct($sibling);
        if ($sibling !== null) {
            $this->sibling = &$sibling;
        }
    }

    public function setMedia()
    {
        if (Tools::getValue('controller') == 'customeraccount') {
            Context::getContext()->controller->addCSS($this->sibling->_path . 'views/css/front/front.css');
        }
    }

    /**
     * Award the customer points for signing up through a referral link
     * @param $params
     */
    public function hookActionCustomerAccountAdd($params)
    {
        $lrp_config = new LRPConfigModel(Context::getContext()->currency->iso_code, LRPCustomerHelper::getGroupID(), Context::getContext()->shop->id);
        $id_referer = (int)LRPReferralHelper::getReferrerIdFromCookie();
        $id_cart = !empty($params['cart']->id) ? $params['cart']->id : 0;

        if ($id_referer > 0 && (int)$params['newCustomer']->id > 0) {
            // Customer cannot have existing paid orders
            if (LRPReferralHelper::getPaidOrderCount($params['newCustomer']->id) == 0) {
                LRPHistoryHelper::rewardPoints(0, $params['newCustomer']->id, $lrp_config->getReferralFriendPoints(), 'referral_signup_bonus', Context::getContext()->currency);
            }

            // If points were auto redeemed before customer signup, transfer the redemption to the customer account
            $points_redeemed = LRPDiscountHelper::getPointsRedeemed(true, null, null, null);
            if ($points_redeemed > 0) {
                LRPDiscountHelper::setPointsRedeem($points_redeemed);
            }
            LRPReferralHelper::setReferrerIdStorage($id_referer, $params['newCustomer']->id, $id_cart);
        }
    }

    /**
     * Transfer any cookie based redeemed points to DB storage
     * @param $params
     */
    public function hookActionAuthentication($params)
    {
        $points_redeemed = LRPDiscountHelper::getPointsRedeemed(true, null, null, null);
        $id_referer = LRPReferralHelper::getReferrerIdFromCookie();

        // Make sure the referrer and customer are not the same account
        $referrer = new Customer($id_referer);
        if (Context::getContext()->customer->email == $referrer->email) {
            LRPDiscountHelper::setPointsRedeem(0);
            return false;
        }

        if ($points_redeemed > 0) {
            LRPDiscountHelper::setPointsRedeem($points_redeemed);
        }
    }
}
