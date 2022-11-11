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
class AddressController extends AddressControllerCore
{
    /*
    * module: onepagecheckoutps
    * date: 2022-11-10 12:21:34
    * version: 4.1.5
    */
    public function init()
    {
        $redirect = false;
        $opc = Module::getInstanceByName('onepagecheckoutps');
        if (Validate::isLoadedObject($opc)) {
            $coreService = $opc->getService(OnePageCheckoutPS\Application\Core\CoreService::SERVICE_NAME);
            if ($coreService->isModuleActive($opc->name) &&
                $opc->checkCustomerAccessToModule() &&
                $opc->getConfigurationList('OPC_REPLACE_ADDRESSES_CONTROLLER')
            ) {
                $redirect = true;
                if ($checkvat = $coreService->isModuleActive('checkvat') &&
                    version_compare($checkvat->version, '1.7.11', '>=')
                ) {
                    $redirect = false;
                }
            }
        }
        if ($redirect) {
            $addresses = $this->context->link->getPageLink('addresses');
            Tools::redirect($addresses);
        }
        parent::init();
    }
}
