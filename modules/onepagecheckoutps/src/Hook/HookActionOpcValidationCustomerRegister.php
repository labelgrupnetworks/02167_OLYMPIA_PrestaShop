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

use Customer;
use Module;
use OnePageCheckoutPS\Application\PrestaShop\Provider\ContextProvider;
use Validate;

class HookActionOpcValidationCustomerRegister extends AbstractHook
{
    private $contextProvider;

    public function __construct(ContextProvider $contextProvider)
    {
        $this->contextProvider = $contextProvider;
    }

    protected function executeRun()
    {
        $moduleListSupported = array('idxrvalidatinguser');
        foreach ($moduleListSupported as $moduleName) {
            if (Module::isInstalled($moduleName)) {
                $module = Module::getInstanceByName($moduleName);
                if (Validate::isLoadedObject($module) && $module->active) {
                    switch ($moduleName) {
                        //idxvalidatinguser - v4.1.6 - innovadeluxe
                        case 'idxrvalidatinguser':
                            $customer = new Customer($this->contextProvider->getCustomer()->id);

                            if ((bool) $customer->active === false) {
                                return array(
                                    'redirect' => $this->contextProvider->getLink()->getModuleLink(
                                        'idxrvalidatinguser',
                                        'deluxevalidatinguser'
                                    ),
                                );
                            }

                            return false;
                    }
                }
            }
        }

        return false;
    }
}
