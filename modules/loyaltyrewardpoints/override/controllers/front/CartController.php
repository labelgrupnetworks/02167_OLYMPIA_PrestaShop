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

class CartController extends CartControllerCore
{
    /**
     * if no other PPBS customizations exist, then only Prestashop customization exists which needs to be deleted
     */
    protected function updateCart()
    {
        include_once(_PS_MODULE_DIR_ . '/loyaltyrewardpoints/lib/bootstrap.php');
        $id_shop = $this->context->shop->id;
        $lrp_config_global = new LRPConfigModel(0, 0, $id_shop);

        if ($lrp_config_global->getDiscountCombinable()) {
            parent::updateCart();
            return;
        }

        if (CartRule::isFeatureActive()) {
            if (Tools::getIsset('addDiscount') && LRPVoucherHelper::cartHasLrpVoucher($this->context->cart->id)) {
                $this->errors[] = $this->trans(
                    'This voucher is not combinable with an other voucher already in your cart: %s',
                    array('%s%' => 'Loyalty Points'),
                    'Shop.Notifications.Error'
                );
            }
        }
        parent::updateCart();
    }
}
