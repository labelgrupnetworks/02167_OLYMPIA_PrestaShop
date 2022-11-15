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

class LRPCartHelper
{
    /**
     * Get the total number of points for all carts or provided cart
     * @param $id_customer
     * @return float
     */
    public static function getPointsPendingAcrossCarts($id_customer, $id_cart = null)
    {
        $sql = new DbQuery();
        $sql->select('SUM(points_redeemed)');
        $sql->from('lrp_cart');
        $sql->where('id_customer =' . (int)$id_customer);
        if ((int)$id_cart > 0) {
            $sql->where('id_cart =' . (int)$id_cart);
        }
        $sql->where('type = ' . (int)LRPCartModel::TYPE_PENDING);
        $total = (float)Db::getInstance()->getValue($sql);
        return $total;
    }

    public static function clearAbandonedCartRedeemedPoints(int $id_customer, int $id_cart_current)
    {
        $sql = new DbQuery();
        $sql->select('c.id_cart');
        $sql->from('cart_cart_rule', 'ccr');
        $sql->innerJoin('cart', 'c', 'c.id_cart = ccr.id_cart');
        $sql->where('c.id_customer =' . (int)$id_customer);
        $sql->where('c.id_cart <>' . (int)$id_cart_current);
        $sql->where('c.id_cart NOT IN (SELECT id_cart from '._DB_PREFIX_.'orders)');
        $result = Db::getInstance()->executeS($sql);
        foreach ($result as $row) {
            DB::getInstance()->delete('lrp_cart', 'id_cart = ' . (int)$row['id_cart'].' AND type = '. (int)LRPCartModel::TYPE_PENDING);
            DB::getInstance()->delete('cart_cart_rule', 'id_cart = ' . (int)$row['id_cart']);
        }
        return $result;
    }

    public static function redeemPoints($id_customer, $id_cart)
    {
        Db::getInstance()->update('lrp_cart', array('type' => (int)LRPCartModel::TYPE_REDEEMED), 'id_cart=' . (int)$id_cart . ' AND id_customer = ' . (int)$id_customer);
    }

    /**
     * Delete by Primary key
     * @param $id_lrp_cart
     */
    public static function delete($id_lrp_cart)
    {
        Db::getInstance()->delete('lrp_cart', 'id_lrp_cart = ' . (int)$id_lrp_cart);
    }

    /**
     * @param $id_cart
     * @param $id_customer
     * @param $type
     */
    public static function deleteSpecific($id_cart, $id_customer, $type)
    {
        Db::getInstance()->delete('lrp_cart', 'id_cart = ' . (int)$id_cart.' AND id_customer = ' . (int)$id_customer . ' AND type = ' . (int)$type);
    }
}
