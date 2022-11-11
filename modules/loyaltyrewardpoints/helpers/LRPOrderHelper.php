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

class LRPOrderHelper
{
    /**
     * @param $id_mail
     * @param $day_trigger
     * @return array|bool|mysqli_result|PDOStatement|resource|null
     */
    public static function getOrdersBetweenDayTriggers($id_mail, $day_trigger)
    {
        $sql = new DbQuery();
        $sql->select('o.*, c.email, c.firstname, c.lastname, c.id_lang, c.newsletter, c.id_default_group, c.id_shop');
        $sql->from('orders', 'o');
        $sql->innerJoin('customer', 'c', 'c.id_customer = o.id_customer');
        $sql->where('CURDATE() >= DATE_ADD((select date_add from ' . _DB_PREFIX_ . 'orders where id_customer = c.id_customer order by date_add DESC limit 1), INTERVAL ' . (int)$day_trigger . ' DAY)');
        $sql->where('c.newsletter = 1');
        $sql->where('c.id_customer NOT IN (SELECT id_customer FROM ' . _DB_PREFIX_ . 'lrp_mail_log WHERE id_customer = c.id_customer AND id_mail = ' . (int)$id_mail . ')');
        $sql->groupBy('c.id_customer');
        $result = Db::getInstance()->executeS($sql);
        return $result;
    }
}
