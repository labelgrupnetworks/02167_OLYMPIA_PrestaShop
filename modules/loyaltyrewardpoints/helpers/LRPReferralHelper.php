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

class LRPReferralHelper
{
    const ID_OFFSET = 1000;

    /**
     * @param null $id_customer
     * @return string
     */
    public static function getCustomerReferralLink($id_customer = null)
    {
        if ((int)$id_customer > 0) {
            $rid = $id_customer + self::ID_OFFSET;
        } else {
            $rid = Context::getContext()->customer->id + self::ID_OFFSET;
        }
        return Tools::getShopProtocol() . Tools::getShopDomain() . __PS_BASE_URI__ . '?rid=' . $rid;
    }

    /**
     * If this visitor is using a referral link, set the cookie
     * @param $referer
     * @throws Exception
     */
    public static function setRefererCookie($referer)
    {
        if ((int)$referer > 0) {
            Context::getContext()->cookie->__set('lrp_referer', (int)$referer);
        }
    }

    /**
     * Clear the referral cookie
     */
    public static function clearRefererCookie()
    {
        Context::getContext()->cookie->__unset('lrp_referer');
    }

    /**
     * Get Customer ID stored in cookie
     * @return int
     */
    public static function getReferrerIdFromCookie()
    {
        $id_referer = (int)Context::getContext()->cookie->lrp_referer;
        if ($id_referer > 0) {
            return $id_referer - self::ID_OFFSET;
        }
    }

    /**
     * get the referrer for this customer and cart
     * @param $id_customer
     * @param $id_cart
     */
    public static function getReferrerIdFromStorage($id_customer, $id_cart)
    {
        $sql = new DbQuery();
        $sql->select('id_referrer');
        $sql->from('lrp_referral_cookie');
        $sql->where('id_customer =' . (int)$id_customer);
        $sql->where('id_cart =' . (int)$id_cart);
        $id_referrer = Db::getInstance()->getValue($sql);
        return $id_referrer;
    }

    /**
     * Save referral cookie to storage if not already exists
     * @param $id_referrer
     * @param $id_customer
     * @param $id_cart
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function setReferrerIdStorage($id_referrer, $id_customer, $id_cart)
    {
        $sql = new DbQuery();
        $sql->select('count(*)as total_count');
        $sql->from('lrp_referral_cookie');
        $sql->where('id_customer =' . (int)$id_customer);
        $sql->where('id_referrer =' . (int)$id_referrer);
        $sql->where('id_cart =' . (int)$id_cart);
        $count = Db::getInstance()->getValue($sql);

        if ($count > 0) {
            return false;
        }

        $lrp_referral_cookie_model = new LRPReferralCookieModel();
        $lrp_referral_cookie_model->id_referrer = (int)$id_referrer;
        $lrp_referral_cookie_model->id_customer = (int)$id_customer;
        $lrp_referral_cookie_model->id_cart = (int)$id_cart;
        $lrp_referral_cookie_model->save();
        return true;
    }

    /**
     * Sets the cart ID of the referral entry (if no cart ID has been set)
     * @param $id_referrer
     * @param $id_customer
     * @param $id_cart
     */
    public static function updateReferrerIdStorageCartId($id_referrer, $id_customer, $id_cart)
    {
        Db::getInstance()->update('lrp_referral_cookie', array('id_cart' => (int)$id_cart), 'id_cart= 0 AND id_customer = ' . (int)$id_customer .' AND id_referrer = '.(int)$id_referrer);
    }


    /**
     * Determine how many orders a customer has which have been paid
     * @param $id_customer
     */
    public static function getPaidOrderCount($id_customer)
    {
        $customer = new Customer($id_customer);
        $lrp_config = new LRPConfigModel('GBP', LRPCustomerHelper::getGroupID($id_customer), $customer->id_shop);
        $lrp_config->getIdOrderStateValidation();

        $sql = new DbQuery();
        $sql->select('count(*) AS total_count');
        $sql->from('orders', 'o');
        $sql->innerJoin('order_state', 'os', 'o.current_state = os.id_order_state AND (os.paid = 1 OR id_order_state = ' . (int)$lrp_config->getIdOrderStateValidation() . ')');
        $sql->where('o.id_customer =' . (int)$id_customer);
        $total = Db::getInstance()->getValue($sql);
        return $total;
    }

    /**
     * Check that the provided customer is genuinely new customer with no previous paid orders
     * @param $id_customer
     */
    public static function isGenuineNewCustomer($id_customer)
    {
        $is_genuine = true;
        $customer = new Customer($id_customer);
        $id_address_delivery = Context::getContext()->cart->id_address_delivery;

        if ((int)$id_address_delivery == 0) {
            return true;
        }

        $address = new Address($id_address_delivery);

        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('address', 'a');
        $sql->innerJoin('customer', 'c', 'c.id_customer = a.id_customer');
        $sql->where('a.firstname LIKE "' .pSQL($customer->firstname) . '"');
        $sql->where('a.address1 LIKE "' . pSQL($address->address1) . '"');
        $sql->where('a.postcode LIKE "' . pSQL($address->postcode) . '"');
        $sql->where('a.id_customer <> ' . (int)$id_customer);
        $result = Db::getInstance()->executeS($sql);
        if (!empty($result)) {
            foreach ($result as $address) {
                if (self::getPaidOrderCount($address['id_customer']) > 0) {
                    $is_genuine = false;
                }
            }
        }
        return $is_genuine;
    }

    public static function logReferralClick(string $ip_address, int $id_referrer)
    {
        $id_referrer -= self::ID_OFFSET;
        $customer = new Customer($id_referrer);
        if (empty($customer->id)) {
            return false;
        }

        $sql = new DbQuery();
        $sql->select('count(*) as total');
        $sql->from(LRPReferralClickModel::$definition['table']);
        $sql->where('ip_address = "' . pSQL($ip_address) . '"');
        $total = Db::getInstance()->getValue($sql);

        if ($total > 0) {
            return false;
        }

        $click_entry = new LRPReferralClickModel();
        $click_entry->ip_address = pSQL($ip_address);
        $click_entry->id_referrer = $id_referrer;
        $click_entry->add(true);
    }
}
