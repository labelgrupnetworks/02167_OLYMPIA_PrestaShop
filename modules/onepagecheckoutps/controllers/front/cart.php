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

use OnePageCheckoutPS\Controller\Front\IFrontController;
use OnePageCheckoutPS\Exception\OPCException;

class OnePageCheckoutPSCartModuleFrontController extends ModuleFrontControllerCore implements IFrontController
{
    //public $php_self = 'module-onepagecheckoutps-cart';

    public function postProcess()
    {
        $coreService = $this->get('onepagecheckoutps.core.core_service');
        $coreService->executeActionRequest($this);
    }

    public function updateAddress()
    {
        try {
            $requestParameters = Tools::getAllValues();

            $addresses = $this->get('onepagecheckoutps.core.addresses');
            $cart = $this->get('onepagecheckoutps.core.cart');
            $cart->setParameters($requestParameters);
            $cart->updateAddress();

            return array(
                'success' => true,
                'addressDeliveryId' => $addresses->getAddressDeliveryId(),
                'addressInvoiceId' => $addresses->getAddressInvoiceId(),
                'haveSameAddress' => $addresses->haveSameAddress(),
                'stepAddressesRendered' => $addresses->render(),
            );
        } catch (OPCException $exception) {
            return array(
                'success' => false,
                'messageError' => $this->handleExceptionAjax($exception),
            );
        }
    }

    public function getCartSummary()
    {
        $customerLogged = false;

        try {
            $this->module->loginTemporalCustomer($customerLogged);

            $cart = $this->get('onepagecheckoutps.core.cart');
            $varsCart = $cart->getTemplateVars();

            $stepCartRendered = $cart->render();

            if ($customerLogged) {
                $this->module->logoutTemporalCustomer();
            }

            return array(
                'success' => true,
                'html' => $stepCartRendered,
                'productsTotal' => $varsCart['cartPresenterVars']['products_count'],
                'orderTotal' => $varsCart['cartPresenterVars']['totals']['total']['amount'],
                'orderTotalFormatted' => $varsCart['cartPresenterVars']['totals']['total']['value'],
                'isVirtualCart' => $this->module->getContextProvider()->isVirtualCart(),
            );
        } catch (OPCException $exception) {
            if ($customerLogged) {
                $this->module->logoutTemporalCustomer();
            }

            return array(
                'success' => false,
                'messageError' => $this->handleExceptionAjax($exception),
            );
        }
    }

    public function handleExceptionAjax($exception)
    {
        $messageLang = '';
        $exceptionClass = get_class($exception);

        switch ($exceptionClass) {
            case 'OnePageCheckoutPS\Exception\CartException':
                switch ($exception->getCode()) {
                }

                break;

            default:
                break;
        }

        return $exception->getMessageFormatted($messageLang);
    }
}
