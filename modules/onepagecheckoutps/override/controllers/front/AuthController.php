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


class AuthController extends AuthControllerCore
{
    public $opc = false;

    public function init()
    {
        parent::init();

        $opc = Module::getInstanceByName('onepagecheckoutps');
        if (Validate::isLoadedObject($opc) &&
            $opc->getService(OnePageCheckoutPS\Application\Core\CoreService::SERVICE_NAME)->isModuleActive($opc->name) &&
            $opc->checkCustomerAccessToModule() &&
            $opc->getConfigurationList('OPC_REPLACE_AUTH_CONTROLLER')
        ) {
            $this->opc = $opc;
        }
    }

    public function initContent()
    {
        if ($this->opc) {
            FrontController::initContent();
            $this->opc->initContentRegisterControllerOPC($this, '');
        } elseif (Tools::isSubmit('submitCreate')) {
            Hook::exec('actionContactFormSubmitCaptcha');

            if (!sizeof($this->context->controller->errors)) {
                parent::initContent();
            } else {
                $register_form = $this
                    ->makeCustomerForm()
                    ->setGuestAllowed(false)
                    ->fillWith(Tools::getAllValues());

                FrontController::initContent();

                $this->context->smarty->assign(array(
                    'register_form' => $register_form->getProxy(),
                    'hook_create_account_top' => Hook::exec('displayCustomerAccountFormTop'),
                ));
                $this->setTemplate('customer/registration');
            }
        } else {
            parent::initContent();
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        if ($this->opc) {
            $this->opc->getMediaFront();
        }
    }
}
