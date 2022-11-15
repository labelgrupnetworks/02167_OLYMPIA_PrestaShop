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

class LRPCustomerHelper
{
    /**
     * Get total points is allowed to use observing any points customer has redeemed in a previous order but yet which has not yet been approved
     * @param $id_customer
     * @param $id_cart
     */
    public static function getTotalPointsAvailable($id_customer, $id_cart)
    {
        $lrp_customer = new LRPCustomerModel();
        $lrp_customer->loadByCustomerID($id_customer);
        $points_pending = 0;

        if (empty($lrp_customer->id_customer)) {
            return 0;
        }

        if ($id_cart !== null) {
            $points_pending = LRPCartHelper::getPointsPendingAcrossCarts($id_customer, $id_cart);
        }
        $points_available = $lrp_customer->points - $points_pending;

        if ($points_available < 0) {
            $points_available = 0;
        }
        return $points_available;
    }

    /**
     * Update total customer points based on points transaction history
     * @param $id_customer
     * @throws Exception
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function calculateAndUpdatePointsTotal($id_customer)
    {
        if ($id_customer == 0) {
            return false;
        }

        $customer = new Customer($id_customer);
        $lrp_config = new LRPConfigModel(0, LRPCustomerHelper::getGroupID($id_customer), $customer->id_shop);
        $lrp_history = new LRPHistoryModel();
        $lrp_history = $lrp_history->getByCustomerID($id_customer);
        $lrp_customer = new LRPCustomerModel();
        $lrp_customer->loadByCustomerID($id_customer);

        if (empty($lrp_customer->id_customer)) {
            $lrp_customer->id_customer = $id_customer;
        }

        $points = 0;
        $lrp_history['result'] = array_reverse($lrp_history['result']);

        foreach ($lrp_history['result'] as &$history) {
            $history->expires_date = LRPHistoryHelper::getPointsExpiryDate($history->date_add, $lrp_config->getPointsExpireDays());
            $history->expires_days = LRPHistoryHelper::getDateDifferenceInDays(date('Y-m-d'), $history->expires_date);

            if ($history->type == LRPHistoryModel::TYPE_REWARDED) {
                if ($history->expires_days > 0) {
                    $points += $history->points;
                } elseif ($lrp_config->getPointsExpireDays() == 0) {
                    $points += $history->points;
                }
            } elseif ($history->type == LRPHistoryModel::TYPE_REDEEMED) {
                $points -= $history->points;
                if ($points < 0) {
                    $points = 0;
                }
            }
        }
        $lrp_customer->points = (int)$points;
        $lrp_customer->save();
    }

    /**
     * @param $source_str
     * @param $module
     * @param $route
     */
    public static function translateSource($source_str, $module, $route)
    {
        if ($route == '') {
            $route = 'LRPCustomerHelper';
        }
        $source_str = str_replace('Employee', $module->l('Employee', $route), $source_str);
        $source_str = str_replace('referral_completed', $module->l('referral_completed', $route), $source_str);
        $source_str = str_replace('referral_signup_bonus', $module->l('referral_signup_bonus', $route), $source_str);
        return $source_str;
    }

    /**
     * Get the defauklt group for a CUSTOMER
     * @param integer id_customer
     * @return int
     */
    public static function getGroupID($id_customer = 0)
    {
        if ($id_customer == 0) {
            if (!empty(Context::getContext()->customer)) {
                return Context::getContext()->customer->id_default_group;
            } else {
                return (int)Configuration::get('PS_CUSTOMER_GROUP');
            }
        } else {
            $customer = new Customer($id_customer);
            return $customer->id_default_group;
        }
    }

    /**
     * Get all customers with birthday on specified date
     * @param $date (yyyy-mm-dd)
     * @return array|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public static function getCustomersByBirthDate($date)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('customer');
        $sql->where('DATE_FORMAT(birthday, "%m-%d") = DATE_FORMAT("' . pSQL($date) . '", "%m-%d")');
        $sql->where('active = 1');
        $sql->where('deleted = 0');
        $result = Db::getInstance()->executeS($sql);
        return $result;
    }
}
