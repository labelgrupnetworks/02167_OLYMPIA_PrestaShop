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

class LoyaltyRewardPointsUnsubscribeModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $this->route();
        $this->setTemplate('module:loyaltyrewardpoints/views/templates/front/account/unsubscribe.tpl');
    }

    /**
     * route
     */
    public function route()
    {
        if (Tools::getValue('sc') != '' && (int)Tools::getValue('i') > 0) {
            $module = Module::getInstanceByName('loyaltyrewardpoints');
            $controller = new LRPCronController($module);
            $controller->unsubscribe((int)Tools::getValue('i'), Tools::getValue('sc'));
        }
    }
}
