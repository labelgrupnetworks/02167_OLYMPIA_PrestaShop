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

class LoyaltyRewardPointsAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->ajax = true;
        parent::initContent();
        $this->route();
    }

    /**
     * route
     */
    public function route()
    {
        $module = Module::getInstanceByName('loyaltyrewardpoints');
        if (Tools::getValue('section') == 'mpproductsearchwidgetcontroller') {
            $mp_product_search_widget = new MPProductSearchWidgetController(Tools::getValue('id'), $module);
            die(Tools::jsonEncode($mp_product_search_widget->route()));
        } else {
            switch (Tools::getValue('route')) {
                case 'lrpfrontcheckoutcontroller':
                    $lrp_front_checkout_controller = new LRPFrontCheckoutController($module);
                    die($lrp_front_checkout_controller->route());

                case 'lrpfrontproductcontroller':
                    $lrp_front_product_controller = new LRPFrontProductController($module);
                    die($lrp_front_product_controller->route());
            }
        }
    }
}
