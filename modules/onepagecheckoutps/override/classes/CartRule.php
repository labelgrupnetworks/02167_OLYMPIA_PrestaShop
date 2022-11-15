<?php
/**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * @author    PresTeamShop SAS (Registered Trademark) <info@presteamshop.com>
 * @copyright 2011-2022 PresTeamShop SAS, All rights reserved.
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @category  PrestaShop
 * @category  Module
 */

class CartRule extends CartRuleCore
{
    public function checkValidity(
        Context $context,
        $alreadyInCart = false,
        $display_error = true,
        $check_carrier = true,
        $useOrderPrices = false
    ) {
        if (!CartRule::isFeatureActive()) {
            return false;
        }

        $opc = Module::getInstanceByName('onepagecheckoutps');
        if (Validate::isLoadedObject($opc) && $opc->core->isModuleActive($opc->name)) {
            if (!Configuration::get('OPC_ALLOW_DISCOUNTS')) {
                $cart = new Cart($context->cart->id);
                $products = $cart->getProducts();
                $is_ok = true;
                foreach ($products as $product) {
                    if ($product['reduction_applies']) {
                        $is_ok = false;
                        break;
                    }
                }
                if (!$is_ok) {
                    return (!$display_error) ? false : $opc->allow_discounts_error;
                }
            }
        }

        if (version_compare(_PS_VERSION_, '1.7.7.5', '>=')) {
            return parent::checkValidity($context, $alreadyInCart, $display_error, $check_carrier, $useOrderPrices);
        } else {
            return parent::checkValidity($context, $alreadyInCart, $display_error, $check_carrier);
        }
    }
}
