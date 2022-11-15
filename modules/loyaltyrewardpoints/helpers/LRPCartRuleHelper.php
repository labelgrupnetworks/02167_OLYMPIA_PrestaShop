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

class LRPCartRuleHelper
{

    public static function productHasActiveCartRule(int $id_product): bool
    {
        $sql = new DbQuery();
        $sql->select('cr.id_cart_rule');
        $sql->from('cart_rule', 'cr');
        $sql->innerJoin('cart_rule_product_rule_group', 'cart_rule_product_rule_group',  'cr.id_cart_rule = cart_rule_product_rule_group.id_cart_rule');
        $sql->innerJoin('cart_rule_product_rule', 'cart_rule_product_rule', 'cart_rule_product_rule_group.id_product_rule_group = cart_rule_product_rule.id_product_rule_group');
        $sql->innerJoin('cart_rule_product_rule_value', 'cart_rule_product_rule_value', 'cart_rule_product_rule_value.id_product_rule = cart_rule_product_rule.id_product_rule AND id_item = '.(int)$id_product);
        $sql->where('NOW() BETWEEN cr.date_from AND cr.date_to');
        $sql->where('cr.product_restriction = 1');
        $sql->where('cr.active = 1');
        $result = Db::getInstance()->executeS($sql);

        if (empty($result)) {
            return false;
        } else {
            return true;
        }
    }
}
