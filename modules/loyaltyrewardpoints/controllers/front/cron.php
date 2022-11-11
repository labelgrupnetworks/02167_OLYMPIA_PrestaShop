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

class LoyaltyRewardPOintsCronModuleFrontController extends ModuleFrontController
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
        if (Tools::getValue('key') == LRPUtilityHelper::getCronSecureKey()) {
            $module = Module::getInstanceByName('loyaltyrewardpoints');
            $controller = new LRPCronController($module);
            $controller->run();
        }
    }
}
