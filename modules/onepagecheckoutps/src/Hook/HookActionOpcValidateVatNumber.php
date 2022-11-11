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

use Address;
use OnePageCheckoutPS\Application\PrestaShop\Provider\ContextProvider;
use Tools;
use Validate;

class HookActionOpcValidateVatNumber extends AbstractHook
{
    private $contextProvider;

    public function __construct(ContextProvider $contextProvider)
    {
        $this->contextProvider = $contextProvider;
    }

    private function validateVatNumber($vatNumber, $module)
    {
        if (Validate::isLoadedObject($module) && $module->active) {
            switch ($module->name) {
                case 'checkvat':
                    if (version_compare($module->version, '1.7.11', '>=')) {
                        // include_once(_PS_MODULE_DIR_.'checkvat/classes/CV.php');
                        if (!\CV::verificationVATNumber($vatNumber)) {
                            return false;
                        }
                    } else {
                        $verifications = $module->verificationVATNumber($vatNumber);
                        if (!$verifications) {
                            return false;
                        }
                    }
                    break;
                case 'advancedvatmanager':
                    $id_address = $this->contextProvider->getContextLegacy()->cart->id_address_delivery;
                    $address = new Address($id_address);
                    $advancedvatmanager = new \ValidationEngine($vatNumber);

                    $verifications = $advancedvatmanager->VATValidationProcess(
                        Tools::getValue('id_country'),
                        $address->id_customer,
                        $id_address,
                        Tools::getValue('company')
                    );

                    if (!$verifications && \ValidationEngine::$skip_validation_process === false) {
                        return false;
                    }

                    break;
                case 'vatnumbercleaner':
                    $idAddress = Tools::getValue('id');
                    $idCountry = Tools::getValue('id_country');

                    if (version_compare($module->version, '1.4.2', '<')) {
                        $verifications = $module->verificationsIdAddress(
                            $idAddress,
                            $vatNumber,
                            $idCountry
                        );
                        if ((int) $verifications['id_msg'] != 7) {
                            return false;
                        }
                    } else {
                        $verifications = $module->verificationVATNumber($vatNumber, $idCountry);
                        if (version_compare($module->version, '1.4.8', '>=')) {
                            if (in_array($verifications, array(1, 2, 3, 4))) {
                                \VNC::deleteVATNumber($vatNumber, $idAddress);

                                return false;
                            } else {
                                \VNC::saveVatNumber($vatNumber, $verifications);
                            }
                        } elseif ($verifications != 7 && $verifications != 6) {
                            return false;
                        }
                    }

                    break;
            }
        }

        return true;
    }

    protected function executeRun()
    {
        $parameters = $this->getParameters();
        $vatNumber = $parameters['vatNumber'];
        $module = $parameters['module'];

        return $this->validateVatNumber($vatNumber, $module);
    }
}
