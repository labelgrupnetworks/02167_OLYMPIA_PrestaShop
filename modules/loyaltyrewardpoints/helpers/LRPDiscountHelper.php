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

class LRPDiscountHelper
{
    /**
     * Determine if module is running in discount mode as opposed to voucher mode
     * @param $customer
     * @return bool
     */
    public static function isDiscountMode($customer)
    {
        return false;

        if (!empty($customer->id_default_group)) {
            $lrp_config = new LRPConfigModel(0, Context::getContext()->customer->id_default_group);
            if ($lrp_config->getDiscountMode() == LRPConfigModel::DISCOUNT_MODE_VOUCHER) {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }

    /**
     * Calculate how manny points to reward based on money
     * @param $money
     * @param $currency_iso
     * @param bool $round
     * @return float|int
     */
    public static function calculatePointsFromMoney($money, $currency_iso, $round = true, LRPConfigModel $lrp_config = null)
    {
        $ratio = LRPConfigHelper::getRatio($currency_iso, LRPCustomerHelper::getGroupID(), Context::getContext()->shop->id);

        if ($ratio == 0) {
            return 0;
        }

        if ($round) {
            $points = floor($money / $ratio);
        } else {
            $points = $money / $ratio;
        }
        return $points;
    }

    /**
     * Applpy points impact based on Points Rules
     * @param $points
     * @param $id_product
     * @param $quantity
     * @return float|int
     */
    public static function applyPointRules($points, $id_product, $quantity, int $id_product_attribute = 0)
    {
        if (empty($quantity)) {
            $quantity = 1;
        }

        $id_categories = Product::getProductCategories($id_product);
        $id_category_clause = '';

        if (!empty($id_categories)) {
            foreach ($id_categories as $id_category) {
                if ($id_category_clause != '') {
                    $id_category_clause .= ' OR ';
                }
                $id_category_clause .= 'FIND_IN_SET(' . (int)$id_category . ',id_categories)';
            }
        }

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'lrp_rule
					WHERE (' . $id_category_clause . ')
					OR FIND_IN_SET(' . (int)$id_product . ', id_products)					
					AND ("' . pSQL(date('Y-m-d h:i:s')) . '" >= date_start AND "' . pSQL(date('Y-m-d h:i:s')) . '" <= date_end)
					ORDER BY date_end, date_start
					';

        $row = DB::getInstance()->getRow($sql);


        if (!empty($row)) {
            switch ($row['operator']) {
                case '+':
                    $points += $row['points'] * $quantity;
                    break;

                case '*':
                    $points = $points * $row['points'] * $quantity;
                    break;

                case '=':
                    $points = $row['points'] * $quantity;
                    break;
            }
        } else {
            $points = $points * $quantity;
        }

        if (LRPProductHelper::isProductOnDiscount($id_product, $id_product_attribute)) {
            $points = 0;
        }
        return $points;
    }


    /**
     * Get the value of points based on the products in the cart and the reward configuration
     * @param $obj_cart
     * @param int $points_redeemed
     * @param LRPConfigModel|null $lrp_config
     * @return float|int
     */
    public static function getCartPointsValue($obj_cart, $points_redeemed = 0, LRPConfigModel $lrp_config = null)
    {
        $points = 0;
        $cart_total = $obj_cart->getOrderTotal(true, Cart::ONLY_PRODUCTS, null, null, true);
        $cart_discount = $obj_cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS, null, null, true);
        $currency = new Currency($obj_cart->id_currency);

        $cart_products = $obj_cart->getProducts(true);
        foreach ($cart_products as $cart_product) {
            $product_points = self::calculatePointsFromMoney(Tools::ps_round($cart_product['price_wt'], _PS_PRICE_DISPLAY_PRECISION_), $currency->iso_code, false);
            $product_points = self::applyPointRules($product_points, $cart_product['id_product'], $cart_product['quantity'], $cart_product['id_product_attribute']);
            $points += $product_points;
        }

        // Cart Discount Points
        if ($cart_discount > 0) {
            $cart_discount_points = self::calculatePointsFromMoney($cart_discount, $currency->iso_code, false);
            $points -= $cart_discount_points;
        }

        if ($points < 0) {
            $points = 0;
        }

        return $points;
    }

    /**
     * Get the value of points based on the products in the cart and the reward configuration
     * @param $obj_order
     * @param int $points_redeemed
     * @param LRPConfigModel|null $lrp_config
     * @return float|int
     */
    public static function getOrderPointsValue($obj_order, $points_redeemed = 0, LRPConfigModel $lrp_config = null)
    {
        $points = 0;
        $order_discount = $obj_order->total_discounts_tax_incl;
        $currency = new Currency($obj_order->id_currency);

        $order_products = $obj_order->getProducts();
        foreach ($order_products as $order_product) {
            $product_points = self::calculatePointsFromMoney(Tools::ps_round($order_product['unit_price_tax_incl'], _PS_PRICE_DISPLAY_PRECISION_), $currency->iso_code, false);
            $product_points = self::applyPointRules($product_points, $order_product['product_id'], $order_product['product_quantity']);
            $points += $product_points;
        }

        // Cart Discount Points
        if ($order_discount > 0) {
            $cart_discount_points = self::calculatePointsFromMoney($order_discount, $currency->iso_code, false);
            $points -= $cart_discount_points;
        }

        if ($points < 0) {
            $points = 0;
        }

        return $points;
    }

    /**
     * Set the points redeemed in the cookie only
     * @param $points
     */
    public static function setPointsRedeeemCookie($points)
    {
        Context::getContext()->cookie->__set('lrp_points_redeemed', $points);
    }

    /**
     * Set the points redeemed for the current cart
     * @param $points
     * @param int $id_currency *
     * @throws Exception
     */
    public static function setPointsRedeem($points, $id_currency = 0)
    {
        $customer = Context::getContext()->customer;
        $id_cart = Context::getContext()->cart->id;
        $id_shop = Context::getContext()->shop->id;
        $id_customer = Context::getContext()->customer->id;
        $id_currency = Context::getContext()->currency->id;
        $id_country = Context::getContext()->country->id;

        if ($id_currency == 0) {
            $currency = Currency::getDefaultCurrency();
        } else {
            $currency = new Currency($id_currency);
        }

        $lrp_config = new LRPConfigModel(0, $customer->id_default_group, $id_shop);

        Context::getContext()->cookie->__set('lrp_points_redeemed', $points);

        if ((int)Context::getContext()->cart->id > 0 && (int)Context::getContext()->customer->id > 0) {
            $lrp_cart = new LRPCartModel();
            $lrp_cart->load(Context::getContext()->cart->id, Context::getContext()->customer->id);
            $lrp_cart->points_redeemed = $points;
            $lrp_cart->id_cart = (int)Context::getContext()->cart->id;
            $lrp_cart->id_customer = (int)Context::getContext()->customer->id;
            $lrp_cart->type = LRPCartModel::TYPE_PENDING;
            $lrp_cart->save();
        }

        if (!LRPDiscountHelper::isDiscountMode(Context::getContext()->customer)) {
            if ($points > 0) {
                $amount = LRPDiscountHelper::getPointsMoneyValue($points, null, $currency->iso_code);
                //$amount = LRPUtilityHelper::deductTax($amount, LRPUtilityHelper::getDefaultTaxRate($id_country));
                $expire_days = $lrp_config->getPointsExpireDays();
                LRPVoucherhelper::clearVoucher($id_cart, $id_customer);
                LRPVoucherhelper::addVoucher($id_cart, $id_customer, $expire_days, $amount, $currency->id);
            } else {
                LRPVoucherhelper::clearVoucher($id_cart, $id_customer);
            }
        }
    }

    /**
     * @param bool $from_cookie
     * @param null $id_cart
     * @param null $id_customer
     * @param null $type
     * @return float|int
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getPointsRedeemed($from_cookie = true, $id_cart = null, $id_customer = null, $type = null)
    {
        if ($from_cookie) {
            $points = Context::getContext()->cookie->lrp_points_redeemed;
            if (is_int($points)) {
                return (int)Context::getContext()->cookie->lrp_points_redeemed;
            } else {
                return (int)ceil(Context::getContext()->cookie->lrp_points_redeemed);
            }
        } else {
            if (!is_null($id_cart) && !is_null($id_customer)) {
                $lrp_cart = new LRPCartModel();
                $lrp_cart->load($id_cart, $id_customer);

                if ((int)$type > 0) {
                    if (!empty($lrp_cart->points_redeemed) && ($lrp_cart->type == $type)) {
                        return $lrp_cart->points_redeemed;
                    } else {
                        return 0;
                    }
                } else {
                    if (!empty($lrp_cart->points_redeemed)) {
                        return $lrp_cart->points_redeemed;
                    } else {
                        return 0;
                    }
                }
            }
        }
    }

    /**
     * Get the monetary value of points
     */
    public static function getPointsMoneyValue($points, $point_value = null, $currency_iso = '')
    {
        if ($currency_iso == '') {
            $currency_iso = Context::getContext()->currency->iso_code;
        }
        $point_value = LRPConfigHelper::getPointValue($currency_iso, LRPCustomerHelper::getGroupID(), Context::getContext()->shop->id);
        $discount = $points * $point_value;
        return $discount;
    }

    /**
     * Get the monetary value of points in the stores default currency
     */
    public static function getPointsMoneyValueInDefaultCurrency($points, $point_value = null, $currency_iso = '')
    {
        if ($currency_iso == '') {
            $currency_iso = Context::getContext()->currency->iso_code;
        }
        $point_value = LRPConfigHelper::getPointValue($currency_iso, LRPCustomerHelper::getGroupID(), Context::getContext()->shop->id);
        $discount = $points * $point_value;
        return $discount;
    }


    /**
     * Get the value of money in points
     * @param $money
     * @param $currency_iso
     * @param $round mode up|down|
     * @return float|int
     */
    public static function getMoneyPointsValue($money, $currency_iso, $round_mode = 'down')
    {
        $lrp_config = new LRPConfigModel($currency_iso, LRPCustomerHelper::getGroupID(), Context::getContext()->shop->id);

        if ($lrp_config->getPointValue() == 0) {
            return 0;
        }

        if ($round_mode == 'down') {
            $points = floor($money / $lrp_config->getPointValue());
        } elseif ($round_mode == 'up') {
            $points = ceil($money / $lrp_config->getPointValue());
        } else {
            $points = $money / $lrp_config->getPointValue();
        }
        return $points;
    }

    /**
     * Checks to see if the customer has any past carts which were abandoned and removes anyu redeemed points from them
     * @param $id_customer
     * @param $id_cart
     */
    public static function cancelAbandonedCartDiscounts($id_customer, $id_cart)
    {
        if ($id_customer == 0 || $id_cart == 0) {
            return false;
        }

        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('lrp_cart');
        $sql->where('id_customer =' . (int)$id_customer);
        $sql->where('id_cart <> ' . (int)$id_cart);
        $sql->where('type = ' . (int)LRPCartModel::TYPE_PENDING);
        $sql->where('id_cart NOT IN (SELECT id_cart from ' . _DB_PREFIX_ . 'orders)');
        $result = Db::getInstance()->executeS($sql);

        if (!empty($result)) {
            foreach ($result as $row) {
                LRPVoucherhelper::clearVoucher($row['id_cart'], $id_customer);
                LRPCartHelper::delete($row['id_lrp_cart']);
            }
        }
    }
}
