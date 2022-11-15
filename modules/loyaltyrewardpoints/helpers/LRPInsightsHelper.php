<?php

class LRPInsightsHelper
{
    public static function getClicksCount(string $date_start = '', string $date_end = ''): int
    {
        $sql = new DbQuery();
        $sql->select('count(*) as total');
        $sql->from(LRPReferralClickModel::$definition['table']);

        if (DateTime::createFromFormat('Y-m-d', $date_start) !== false && DateTime::createFromFormat('Y-m-d', $date_end) !== false) {
            $sql->where("date_add BETWEEN '$date_start' AND '$date_end'");
        }
        return (int)Db::getInstance()->getValue($sql);
    }

    public static function getReferralOrdersCount(string $date_start = '', string $date_end = ''): int
    {
        $sql = new DbQuery();
        $sql->select('count(*) as total');
        $sql->from(LRPHistoryModel::$definition['table']);
        $sql->where('type = ' . (int)LRPHistoryModel::TYPE_REDEEMED);
        $sql->where('id_order > 0');
        $sql->where('source = "referral_signup_discount"');
        if (DateTime::createFromFormat('Y-m-d', $date_start) !== false && DateTime::createFromFormat('Y-m-d', $date_end) !== false) {
            $sql->where("date_add BETWEEN '$date_start' AND '$date_end'");
        }
        return (int)Db::getInstance()->getValue($sql);
    }

    public static function getReferralNewCustomersCount(string $date_start = '', string $date_end = ''): int
    {
        $sql = new DbQuery();
        $sql->select('count(*) as total');
        $sql->from(LRPHistoryModel::$definition['table']);
        $sql->where('type = ' . (int)LRPHistoryModel::TYPE_REWARDED);
        $sql->where('id_order = 0');
        $sql->where('source = "referral_signup_bonus"');
        if (DateTime::createFromFormat('Y-m-d', $date_start) !== false && DateTime::createFromFormat('Y-m-d', $date_end) !== false) {
            $sql->where("date_add BETWEEN '$date_start' AND '$date_end'");
        }
        return (int)Db::getInstance()->getValue($sql);
    }

    public static function getReferralRedeemedTotal(string $date_start = '', string $date_end = ''): float
    {
        $sql = new DbQuery();
        $sql->select('SUM(point_value_base_currency) as total');
        $sql->from(LRPHistoryModel::$definition['table']);
        $sql->where('type = ' . (int)LRPHistoryModel::TYPE_REDEEMED);
        $sql->where('id_order > 0');
        $sql->where('source = "referral_signup_discount"');
        if (DateTime::createFromFormat('Y-m-d', $date_start) !== false && DateTime::createFromFormat('Y-m-d', $date_end) !== false) {
            $sql->where("date_add BETWEEN '$date_start' AND '$date_end'");
        }
        return (float)Db::getInstance()->getValue($sql);
    }

    public static function getTotalOrdersRedeemed(string $date_start = '', string $date_end = ''): int
    {
        $sql = new DbQuery();
        $sql->select('COUNT(*) as total');
        $sql->from(LRPHistoryModel::$definition['table']);
        $sql->where('type = ' . (int)LRPHistoryModel::TYPE_REDEEMED);
        $sql->where('id_order > 0');
        if (DateTime::createFromFormat('Y-m-d', $date_start) !== false && DateTime::createFromFormat('Y-m-d', $date_end) !== false) {
            $sql->where("date_add BETWEEN '$date_start' AND '$date_end'");
        }
        return (float)Db::getInstance()->getValue($sql);
    }

    /**
     * get the total points redeemed between tow dates
     */
    public static function getTotalPointsRedeemed(string $date_start = '', string $date_end = '') : int
    {
        $sql = new DbQuery();
        $sql->select('SUM(points)');
        $sql->from(LRPHistoryModel::$definition['table']);
        $sql->where('type = ' . (int)LRPHistoryModel::TYPE_REDEEMED);
        $sql->where('id_order > 0');
        if (DateTime::createFromFormat('Y-m-d', $date_start) !== false && DateTime::createFromFormat('Y-m-d', $date_end) !== false) {
            $sql->where("date_add BETWEEN '$date_start' AND '$date_end'");
        }
        return (int)Db::getInstance()->getValue($sql);
    }

    public static function getTotalPointsValueRedeemed(string $date_start = '', string $date_end = ''): float
    {
        $sql = new DbQuery();
        $sql->select('SUM(point_value_base_currency)');
        $sql->from(LRPHistoryModel::$definition['table']);
        $sql->where('type = ' . (int)LRPHistoryModel::TYPE_REDEEMED);
        $sql->where('id_order > 0');
        if (DateTime::createFromFormat('Y-m-d', $date_start) !== false && DateTime::createFromFormat('Y-m-d', $date_end) !== false) {
            $sql->where("date_add BETWEEN '$date_start' AND '$date_end'");
        }
        return (float)Db::getInstance()->getValue($sql);
    }

    public static function getTopReferrersByOrders(string $date_start = '', string $date_end = ''): array
    {
        $sql = new DbQuery();
        $sql->select('id_referrer, c.firstname, c.lastname, c.email, COUNT(*) as total');
        $sql->from(LRPReferralCookieModel::$definition['table'], 'lrc');
        $sql->innerJoin(LRPHistoryModel::$definition['table'], 'lh', 'lrc.id_customer = lh.id_customer');
        $sql->innerJoin(Customer::$definition['table'], 'c', 'lrc.id_referrer = c.id_customer');
        $sql->where('lh.type = ' . (int)LRPHistoryModel::TYPE_REDEEMED);
        $sql->where('lh.id_order > 0');
        $sql->where('source = "referral_signup_discount"');
        if (DateTime::createFromFormat('Y-m-d', $date_start) !== false && DateTime::createFromFormat('Y-m-d', $date_end) !== false) {
            $sql->where("lh.date_add BETWEEN '$date_start' AND '$date_end'");
        }
        $sql->groupBy('lrc.id_referrer');
        $sql->orderBy('total DESC');
        return Db::getInstance()->executeS($sql);
    }

    public static function getTopRedeemers(string $date_start = '', string $date_end = ''): array
    {
        $sql = new DbQuery();
        $sql->select('c.id_customer, c.firstname, c.lastname, c.email, COUNT(*) as total, SUM(points) as points, SUM(points) * point_value_base_currency AS points_value');
        $sql->from(LRPHistoryModel::$definition['table'], 'lh');
        $sql->innerJoin(Customer::$definition['table'], 'c', 'lh.id_customer = c.id_customer');
        $sql->where('lh.type = ' . (int)LRPHistoryModel::TYPE_REDEEMED);
        $sql->where('lh.id_order > 0');
        if (DateTime::createFromFormat('Y-m-d', $date_start) !== false && DateTime::createFromFormat('Y-m-d', $date_end) !== false) {
            $sql->where("lh.date_add BETWEEN '$date_start' AND '$date_end'");
        }
        $sql->groupBy('lh.id_customer');
        $sql->orderBy('points DESC');
        return Db::getInstance()->executeS($sql);
    }
}