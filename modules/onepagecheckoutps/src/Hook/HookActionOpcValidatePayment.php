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

namespace OnePageCheckoutPS\Hook;

use Hook;
use Module;
use OnePageCheckoutPS\Application\PrestaShop\Provider\ContextProvider;
use Validate;

class HookActionOpcValidatePayment extends AbstractHook
{
    private $contextProvider;

    public function __construct(ContextProvider $contextProvider)
    {
        $this->contextProvider = $contextProvider;
    }

    protected function executeRun()
    {
        $moduleListSupported = array('minpurchase', 'cgma');
        foreach ($moduleListSupported as $moduleName) {
            if (Module::isInstalled($moduleName)) {
                $module = Module::getInstanceByName($moduleName);
                if (Validate::isLoadedObject($module) && $module->active) {
                    switch ($moduleName) {
                        //minpurchase - v1.1.3 - idnovate
                        case 'minpurchase':
                            $objConfig = new \MinpurchaseConfiguration();

                            return $objConfig->checkProductsAvailability(
                                $this->contextProvider->getCart()->getProducts()
                            );
                        //cgma - v1.7.0 - MyPresta.eu
                        case 'cgma':
                            $responseHook = Hook::exec('stepBeforePaymentOPC', array(), null, true);
                            if (isset($responseHook['cgma'], $responseHook['cgma']['errors'])) {
                                return $responseHook['cgma']['errors'];
                            }
                    }
                }
            }
        }

        return false;
    }
}
