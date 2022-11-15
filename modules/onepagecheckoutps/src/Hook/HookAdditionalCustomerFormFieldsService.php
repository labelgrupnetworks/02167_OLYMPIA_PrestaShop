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

use FormField;
use OnePageCheckoutPS;
use OnePageCheckoutPS\Application\Core\MyAccount\MyAccountService;
use OnePageCheckoutPS\Application\PrestaShop\Provider\ContextProvider;
use Tools;

class HookAdditionalCustomerFormFieldsService extends AbstractHook
{
    private $module;
    private $contextProvider;
    private $myAccount;

    public function __construct(OnePageCheckoutPS $module, ContextProvider $contextProvider, MyAccountService $myAccount)
    {
        $this->module = $module;
        $this->contextProvider = $contextProvider;
        $this->myAccount = $myAccount;

        $requestParameters = Tools::getAllValues();
        $this->myAccount->setParameters($requestParameters);
    }

    protected function executeRun()
    {
        $extraFieldList = array();

        $privacyPolicyId = $this->myAccount->isPrivacyPolicyEnabled();
        if ($privacyPolicyId) {
            $privacyPolicyUrl = $this->contextProvider->getLink()->getCMSLink($privacyPolicyId);

            $label = $this->module->getMessageList()['haveReadAndAccept'];
            $label .= '<a href="' . $privacyPolicyUrl . '">';
            $label .= '&nbsp;';
            $label .= $this->module->getMessageList()['privacyPolicy'];
            $label .= '</a>';

            $privacyPolicyField = new FormField();
            $privacyPolicyField->setName('privacy_policy');
            $privacyPolicyField->setType('checkbox-cms');
            $privacyPolicyField->setMaxLength(1);
            $privacyPolicyField->setLabel($label);
            $privacyPolicyField->setRequired(true);

            $extraFieldList[] = $privacyPolicyField;
        }

        //logica para no mostrar el campo en el checkout si se tiene activo la compra de invitado.
        //solo mostrar en la autenticacion.
        $showAutogenerateField = $this->myAccount->isAutogeneratePasswordEnabled();
        if ($showAutogenerateField && $this->myAccount->isGuestAllowed()) {
            $showAutogenerateField = false;
        }

        if ($showAutogenerateField) {
            $autogeneratePasswordField = new FormField();
            $autogeneratePasswordField->setName('autogenerate_password');
            $autogeneratePasswordField->setType('checkbox');
            $autogeneratePasswordField->setMaxLength(1);
            $autogeneratePasswordField->setLabel($this->module->getMessageList()['wantEnterCustomPassoword']);
            $autogeneratePasswordField->setRequired(false);

            $extraFieldList[] = $autogeneratePasswordField;
        }

        return $extraFieldList;
    }
}
