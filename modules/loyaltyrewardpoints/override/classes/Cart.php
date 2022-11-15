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

class Cart extends CartCore
{
    /**
     * @param int $id_cart_rule
     * @return bool
     */
    public function removeCartRule($id_cart_rule, bool $useOrderPrices = false)
    {
        include_once(_PS_MODULE_DIR_ . '/loyaltyrewardpoints/lib/bootstrap.php');
        LRPDiscountHelper::setPointsRedeem(0);
        return parent::removeCartRule($id_cart_rule);
    }
}
