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

class LRPFrontCheckoutController extends LRPControllerCore
{
    protected $sibling;

    private $route = 'lrpfrontcheckoutcontroller';

    public function __construct(&$sibling = null)
    {
        parent::__construct($sibling);
        if ($sibling !== null) {
            $this->sibling = &$sibling;
        }
    }

    public function setMedia()
    {
        $render_media = false;
        if (Module::isEnabled('onepagecheckoutps') || Tools::getValue('controller') == 'order' || Module::isEnabled('steasycheckout')) {
            $render_media = true;
        }

        if (Tools::getValue('controller') == 'cart') {
            $render_media = true;
        }

        if ($render_media == true) {
            Context::getContext()->controller->addJS($this->sibling->_path . 'views/js/front/LRPFrontCheckoutController.js');
            Context::getContext()->controller->addCSS($this->sibling->_path . 'views/css/front/front.css');
        }
    }

    /**
     * @param $params
     */
    public function hookActionOrderStatusPostUpdate($params)
    {
        if (!isset($params['id_order']) || !isset($params['newOrderStatus']->id)) {
            return false;
        }

        $id_order = $params['id_order'];
        $order = new Order($id_order);
        $currency = new Currency($order->id_currency);
        $id_customer = $order->id_customer;

        $lrp_config = new LRPConfigModel($currency->iso_code, LRPCustomerHelper::getGroupID($id_customer), $order->id_shop);

        // store the referral cookie into database, as the order may be marked as paid by admin on a different device
        $id_referer = LRPReferralHelper::getReferrerIdFromCookie();
        if ($id_referer > 0) {
            $lrp_referral_cookie = new LRPReferralCookieModel();
            $lrp_referral_cookie->id_customer = Context::getContext()->customer->id;
            $lrp_referral_cookie->id_referrer = (int)$id_referer;
            $lrp_referral_cookie->id_cart = (int)Context::getContext()->cart->id;
            $lrp_referral_cookie->save();
        }

        if ($params['newOrderStatus']->paid == 0 && $params['newOrderStatus']->id != $lrp_config->getIdOrderStateCancel()) {
            return false;
        }

        $array_id_order_state_validation = explode(',', $lrp_config->getIdOrderStateValidation());
        if ($lrp_config->getIdOrderStateValidation() != -1 && !(in_array($params['newOrderStatus']->id, $array_id_order_state_validation)) && $params['newOrderStatus']->id != $lrp_config->getIdOrderStateCancel()) {
            return false;
        }

        $points_redeemed = 0;

        if (!empty($params['id_order'])) {
            $cart = new Cart($order->id_cart);
            $points_redeemed = LRPDiscountHelper::getPointsRedeemed(false, $cart->id, $cart->id_customer, null);
            $cart_points_to_reward = LRPDiscountHelper::getOrderPointsValue($order, $points_redeemed, $lrp_config);
        } else {
            $cart = Context::getContext()->cart;
            $points_redeemed = LRPDiscountHelper::getPointsRedeemed(false, $cart->id, $cart->id_customer, null);
            $cart_points_to_reward = LRPDiscountHelper::getCartPointsValue($cart, $points_redeemed, $lrp_config);
        }

        // Order is being cancelled
        if ($params['newOrderStatus']->id == $lrp_config->getIdOrderStateCancel()) {
            LRPCartHelper::deleteSpecific($cart->id, $cart->id_customer, LRPCartModel::TYPE_PENDING);

            // How many points were rewarded for this order before?
            $lrp_history = new LRPHistoryModel();
            $lrp_history->loadByOrder($params['id_order'], LRPHistoryModel::TYPE_REWARDED);
            /*if (empty($lrp_history->id_order)) {
                return false;
            }*/

            if ($lrp_history->points > 0) {
                LRPHistoryHelper::redeemPoints($params['id_order'], $cart->id_customer, $lrp_history->points, '', $currency);
            }

            if ($points_redeemed > 0) {
                LRPHistoryHelper::rewardPoints($params['id_order'], $cart->id_customer, $points_redeemed, '', $currency);
            }
            return true;
        }

        // do not reward same order twice
        $lrp_history = new LRPHistoryModel();
        $lrp_history->loadByOrder($params['id_order'], LRPHistoryModel::TYPE_REWARDED);
        if (!empty($lrp_history->id_order)) {
            return false;
        }

        // Reward points for purchase
        if ($cart_points_to_reward > 0) {
            LRPHistoryHelper::rewardPoints($params['id_order'], $cart->id_customer, $cart_points_to_reward, '', $currency);
        }

        // Redeem points from customer records
        /*if ($points_redeemed > 0) {
            LRPCartHelper::redeemPoints($cart->id_customer, $cart->id);
            LRPHistoryHelper::redeemPoints($params['id_order'], $cart->id_customer, $points_redeemed, '', $currency);
        }*/

        //Was this customer referred?  If so, and, customer does not have previously fulfilled orders then reward the referer
        $id_referrer = LRPReferralHelper::getReferrerIdFromStorage($cart->id_customer, $cart->id);

        if ($id_referrer > 0) {
            $customer = new Customer($cart->id_customer);
            $referrer_customer = new Customer($id_referrer);

            if ($referrer_customer->email != $customer->email) {
                if (LRPReferralHelper::getPaidOrderCount($cart->id_customer) == 1) {
                    LRPHistoryHelper::rewardPoints(0, $id_referrer, $lrp_config->getReferralPoints(), 'referral_completed', $currency);
                    LRPReferralCookieModel::deleteByCustomer($cart->id_customer);
                }
            }
        }
    }

    /**
     * Redeem points in the cart
     */
    public function processRedeemPoints()
    {
        $id_customer = Context::getContext()->customer->id;
        $id_shop = Context::getContext()->shop->id;
        $lrp_config_global = new LRPConfigModel(0, 0, $id_shop);
        $lrp_config = new LRPConfigModel(Context::getContext()->currency->iso_code, LRPCustomerHelper::getGroupID(), $id_shop);
        $lrp_customer = new LRPCustomerModel();
        $id_cart = Context::getContext()->cart->id;

        $json_return = array(
            'message' => ''
        );

        $redeem_points = (int)Tools::getValue('points');

        if (empty(Context::getContext()->cart->id)) {
            return false;
        }
        LRPCartHelper::clearAbandonedCartRedeemedPoints($id_customer, $id_cart);

        if (!$lrp_config_global->getDiscountCombinable() && LRPVoucherHelper::cartHasNonLrpVoucher($id_cart)) {
            $json_return = array(
                'message' => $this->sibling->l('Points cannot be redeemed while other vouchers have also been redeemed', $this->route)
            );
            die(json_encode($json_return));
        }

        $id_currency = Context::getContext()->cart->id_currency;
        $id_customer = Context::getContext()->cart->id_customer;
        $lrp_customer->loadByCustomerID($id_customer);
        $points_available = LRPCustomerHelper::getTotalPointsAvailable($lrp_customer->id_customer, Context::getContext()->cart->id);

        if ($redeem_points <= 0) {
            $redeem_points = 0;
        }

        if ($redeem_points > $points_available) {
            $redeem_points = $points_available;
        }

        $cart_total = Context::getContext()->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
        $min_cart_value = LRPConfigHelper::getMinCartValue(Context::getContext()->currency->iso_code, LRPCustomerHelper::getGroupID(), Context::getContext()->shop->id);

        if ($min_cart_value > 0 && $cart_total < $min_cart_value) {
            $json_return = array(
                'message' => sprintf($this->sibling->l('You must spent least %s before you can redeem', $this->route), Tools::displayPrice($min_cart_value))
            );
            die(json_encode($json_return));
        }

        $min_points_redemption = $lrp_config->getMinPointsRedemption();

        if ($min_points_redemption > 0 && $redeem_points < $min_points_redemption) {
            $json_return = array(
                'message' => sprintf($this->sibling->l('You must have at least %s points before you redeem', $this->route), $min_points_redemption)
            );
            die(json_encode($json_return));
        }

        //$cart_points_value = LRPDiscountHelper::getCartPointsValue(Context::getContext()->cart);
        $redeem_points_value = LRPDiscountHelper::getPointsMoneyValue($redeem_points, Context::getContext()->currency->iso_code);

        // determine if the discount value is less than the percentage threshold set in the config
        $max_redemption_limit_percentage = $lrp_config->getMaxRedemptionLimitPercentage();
        if ($max_redemption_limit_percentage > 0) {
            $percentage = floor(($redeem_points_value / $cart_total) * 100);
            $total_allowed = $cart_total / 100 * $max_redemption_limit_percentage;
            $max_points = floor(LRPDiscountHelper::calculatePointsFromMoney($total_allowed, Context::getContext()->currency->iso_code));
            if ($percentage > $max_redemption_limit_percentage) {
                $json_return = array(
                    'message' => sprintf($this->sibling->l('You cannot redeem more than %s points for this order', $this->route), $max_points)
                );
                die(json_encode($json_return));
            }
        }

        // do not allow customer to redeem more points than the cart is worth
        if ($redeem_points_value > $cart_total) {
            $redeem_points = LRPDiscountHelper::getMoneyPointsValue($cart_total, Context::getContext()->currency->iso_code, '');
        }

        if ($redeem_points > 0) {
            LRPDiscountHelper::setPointsRedeem($redeem_points, $id_currency);
        }
        die(json_encode($json_return));
    }

    /**
     * Display the points redemption form
     * @param $params
     * @return mixed
     */
    public function hookDisplayShoppingCart($params)
    {
        $lrp_config = new LRPConfigModel(0, Context::getContext()->customer->id_default_group, Context::getContext()->shop->id);
        $lrp_customer = new LRPCustomerModel();
        $lrp_customer->loadByCustomerID(Context::getContext()->customer->id);

        if (!empty($lrp_customer->id_customer)) {
            $points = LRPCustomerHelper::getTotalPointsAvailable($lrp_customer->id_customer, Context::getContext()->cart->id);
        } else {
            $points = 0;
        }

        $points_redeemed = LRPDiscountHelper::getPointsRedeemed(true, null, null, LRPCartModel::TYPE_PENDING);
        $points_redeemed_value = LRPVoucherHelper::getVoucherValue(Context::getContext()->cart);

        if ($points_redeemed > 0 && $points_redeemed_value == 0) {
            LRPDiscountHelper::setPointsRedeem(0);
        }

        Context::getContext()->smarty->assign(array(
            'points' => $points,
            'points_redeemed' => $points_redeemed,
            'points_redeemed_value' => Tools::displayPrice($points_redeemed_value),
            'module_config_url' => $this->module_config_url,
            'lrp_module_ajax_url' => $this->module_ajax_url
        ));
        return $this->sibling->display($this->sibling->module_file, 'views/templates/front/checkout/display_shopping_cart.tpl');
    }

    /**
     * Display information about how many points the current cart is worth
     */
    public function hookDisplayShoppingCartFooter($params)
    {
        Context::getContext()->smarty->assign(array(
            'points' => LRPDiscountHelper::getCartPointsValue(Context::getContext()->cart, LRPDiscountHelper::getPointsRedeemed(true, null, null, LRPCartModel::TYPE_PENDING)),
        ));
        return $this->sibling->display($this->sibling->module_file, 'views/templates/front/checkout/display_shopping_cart_footer.tpl');
    }

    /**
     * Clear any points redeemed ion the cart and reset to 0
     */
    public function processClearPoints()
    {
        LRPDiscountHelper::setPointsRedeem(0);
    }

    public function renderPointsSummary()
    {
        return $this->hookDisplayShoppingCartFooter(array());
    }

    /**
     * When cart is updated by the customer
     * @param $params
     */
    public function hookActionCartSave($params)
    {
        if (empty(Context::getContext()->cart)) {
            return false;
        }

        $id_customer = Context::getContext()->customer->id;
        $id_cart = Context::getContext()->cart->id;

        LRPDiscountHelper::cancelAbandonedCartDiscounts($id_customer, $id_cart);

        $id_referer = LRPReferralHelper::getReferrerIdFromCookie();

        if ($id_referer == 0) {
            return false;
        }

        $order_total = Context::getContext()->cart->getOrderTotal(true);
        $lrp_config = new LRPConfigModel(Context::getContext()->currency->iso_code, LRPCustomerHelper::getGroupID($id_referer), Context::getContext()->shop->id);
        $cart_points_value = LRPDiscountHelper::getMoneyPointsValue($order_total, Context::getContext()->currency->iso_code, 'up');

        if ($lrp_config->getReferralFriendPoints() > $cart_points_value) {
            LRPDiscountHelper::setPointsRedeem($cart_points_value);
        } else {
            LRPDiscountHelper::setPointsRedeem($lrp_config->getReferralFriendPoints());
        }
    }

    /**
     * Called only when cart item quantity is updated or item is removed
     * @param $params
     */
    public function hookActionCartSaveUpdated($params)
    {
        if (!Validate::isLoadedObject($this->context->cart) || !Validate::isLoadedObject($params['cart'])) {
            return false;
        }

        // make sure cart total meets min cart value setting
        $cart_total = Context::getContext()->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $min_cart_value = LRPConfigHelper::getMinCartValue(Context::getContext()->currency->iso_code, LRPCustomerHelper::getGroupID(), Context::getContext()->shop->id);

        // if lrp disocunt is greater than cart total then adjust LRP voucher
        $cart_total = Context::getContext()->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $points_redeemed_value = LRPVoucherHelper::getVoucherValue(Context::getContext()->cart);
        if ($points_redeemed_value >= $cart_total) {
            $redeem_points = LRPDiscountHelper::getMoneyPointsValue($cart_total, Context::getContext()->currency->iso_code, '');
            if ($redeem_points > 0) {
                LRPDiscountHelper::setPointsRedeem($redeem_points, Context::getContext()->currency->id);
            }
        }

        if ($min_cart_value > 0 && $cart_total < $min_cart_value) {
            LRPDiscountHelper::setPointsRedeem(0);
        }
    }

    public function route()
    {
        switch (Tools::getValue('action')) {
            case 'processredeempoints':
                die($this->processRedeemPoints());

            case 'processclearpoints':
                die($this->processClearPoints());

            case 'renderpointssummary':
                die($this->renderPointsSummary());
                die();
        }
    }
}
