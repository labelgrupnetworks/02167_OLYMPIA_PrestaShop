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

class LRPHistoryHelper
{
    /**
     * Add points to History and customer
     * @param $id_order
     * @param $id_customer
     * @param $points
     * @param $source
     * @param $currency
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function rewardPoints($id_order, $id_customer, $points, $source, $currency)
    {
        $id_default_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        $currency_default = new Currency($id_default_currency);
        $lrp_history = new LRPHistoryModel();
        $lrp_history->id_order = (int)$id_order;
        $lrp_history->id_customer = (int)$id_customer;
        $lrp_history->id_currency = (int)$currency->id;
        $lrp_history->points = (float)$points;
        $lrp_history->point_value = (float)LRPConfigHelper::getPointValue($currency->iso_code, LRPCustomerHelper::getGroupID($id_customer), Context::getContext()->shop->id);
        $lrp_history->point_value_base_currency = (float)LRPConfigHelper::getPointValue($currency_default->iso_code, LRPCustomerHelper::getGroupID($id_customer), Context::getContext()->shop->id);
        $lrp_history->type = (int)LRPHistoryModel::TYPE_REWARDED;
        $lrp_history->source = pSQL($source);
        $lrp_history->add();

        // Update points total for customer
        $lrp_customer = new LRPCustomerModel();
        $lrp_customer->loadByCustomerID((int)$id_customer);

        if (empty($lrp_customer->id_customer)) {
            $lrp_customer->id_customer = (int)$id_customer;
            $lrp_customer->points = $points;
            $lrp_customer->referral_code = '';
        } else {
            $lrp_customer->points += $points;
        }
        $lrp_customer->save();
    }

    /**
     * @param $id_order
     * @param $id_customer
     * @param $points
     * @param $source
     * @param $currency
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function redeemPoints($id_order, $id_customer, $points, $source, $currency)
    {
        //round up fractional points to nearest whole point
        if (is_float($points)) {
            $points = ceil($points);
        }
        $id_default_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        $currency_default = new Currency($id_default_currency);
        $lrp_config = new LRPConfigModel($currency->iso_code, LRPCustomerHelper::getGroupID((int)$id_customer), Context::getContext()->shop->id);

        $lrp_history = new LRPHistoryModel();
        $lrp_history->id_order = (int)$id_order;
        $lrp_history->id_customer = (int)$id_customer;
        $lrp_history->id_currency = (int)$currency->id;
        $lrp_history->points = (float)$points;
        $lrp_history->point_value = (float)LRPConfigHelper::getPointValue($currency->iso_code, LRPCustomerHelper::getGroupID((int)$id_customer), Context::getContext()->shop->id);
        $lrp_history->point_value_base_currency = (float)LRPConfigHelper::getPointValue($currency_default->iso_code, LRPCustomerHelper::getGroupID((int)$id_customer), Context::getContext()->shop->id);
        $lrp_history->type = (int)LRPHistoryModel::TYPE_REDEEMED;
        $lrp_history->source = pSQL($source);
        $lrp_history->add();

        // Update points total for customer
        $lrp_customer = new LRPCustomerModel();
        $lrp_customer->loadByCustomerID((int)$id_customer);

        if (!empty($lrp_customer->id_customer)) {
            $lrp_customer->points -= $points;
            $lrp_customer->save();
        }
    }

    /**
     * Get Points Expiry Date
     * @param $date_add
     * @param $expiry_days
     * @throws Exception
     */
    public static function getPointsExpiryDate($date_add, $expiry_days)
    {
        $date_expiry = new DateTime(date('Y-m-d', strtotime($date_add)));
        $date_expiry->add(new DateInterval('P'.(int)$expiry_days.'D'));
        return $date_expiry->format('Y-m-d');
    }

    /**
     * Get the number od days between two dates.  Returns negative if date is in the past
     * @param $date_start
     * @param $date_end
     * @return string
     */
    public static function getDateDifferenceInDays($date_start, $date_end)
    {
        $date_start = new DateTime($date_start);
        $date_end = new DateTime($date_end);
        $interval = $date_start->diff($date_end);
        return $interval->format("%r%a");
    }

    /**
     * get the source string to add to history transaction based on the current bac office employee logged in
     */
    public static function getEmployeeSource()
    {
        if ((int)Context::getContext()->employee->id == 0) {
            return '';
        }

        $employee = Context::getContext()->employee;
        $source = '(ID: ' . $employee->id . ') ' . $employee->firstname . ' ' . $employee->lastname;
        return $source;
    }

    /**
     * Check if a customer has already been rewarded birthday points this year
     * @param $id_customer
     * @param $year
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public static function hasBirthdayPoints($id_customer, $year)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('lrp_history');
        $sql->where('source = "birthday"');
        $sql->where('YEAR(date_add) = "'.pSQL($year).'"');
        $sql->where('id_customer = '.(int)$id_customer);
        $result = Db::getInstance()->executeS($sql);

        if (empty($result)) {
            return false;
        } else {
            return true;
        }
    }
}
