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
use OnePageCheckoutPS\Exception\ShippingException;

class OnePageCheckoutPSShippingModuleFrontController extends ModuleFrontControllerCore implements IFrontController
{
    //public $php_self = 'module-onepagecheckoutps-shipping';

    public function postProcess()
    {
        $coreService = $this->get('onepagecheckoutps.core.core_service');
        $coreService->executeActionRequest($this);
    }

    public function getCarrierList()
    {
        $customerLogged = false;

        try {
            $this->module->loginTemporalCustomer($customerLogged);

            $shipping = $this->get('onepagecheckoutps.core.shipping');
            $stepShippingRendered = $shipping->render();

            if ($customerLogged) {
                $this->module->logoutTemporalCustomer();
            }

            return array(
                'success' => true,
                'stepShippingRendered' => $stepShippingRendered,
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

    public function update()
    {
        $customerLogged = false;

        try {
            $this->module->loginTemporalCustomer($customerLogged);

            $requestParameters = Tools::getAllValues();

            $cart = $this->get('onepagecheckoutps.core.cart');
            $shipping = $this->get('onepagecheckoutps.core.shipping');
            $shipping->update($requestParameters);

            $stepShippingRendered = $shipping->render();
            $stepCartRendered = $cart->render();

            $varsCart = $cart->getTemplateVars();

            if ($customerLogged) {
                $this->module->logoutTemporalCustomer();
            }

            return array(
                'success' => true,
                'stepShippingRendered' => $stepShippingRendered,
                'stepCartRendered' => $stepCartRendered,
                'productsTotal' => $varsCart['cartPresenterVars']['products_count'],
                'orderTotal' => $varsCart['cartPresenterVars']['totals']['total']['amount'],
                'orderTotalFormatted' => $varsCart['cartPresenterVars']['totals']['total']['value'],
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
            case 'OnePageCheckoutPS\Exception\ShippingException':
                switch ($exception->getCode()) {
                    case ShippingException::SHIPPING_NEED_ADDRESS:
                        $messageLang = $this->l('It is necessary to create an delivery address to be able to show the different shipping options.', 'shipping');
                        break;
                    case ShippingException::SHIPPING_NEED_COUNTRY:
                        $messageLang = $this->l('Select a country to show the different shipping options.', 'shipping');
                        break;
                    case ShippingException::SHIPPING_NEED_POSTCODE:
                        $messageLang = $this->l('You need to place a postcode to show shipping options.', 'shipping');
                        break;
                    case ShippingException::SHIPPING_NEED_CITY:
                        $messageLang = $this->l('You need to place a city to show shipping options.', 'shipping');
                        break;
                    case ShippingException::SHIPPING_NEED_STATE:
                        $messageLang = $this->l('You need to place a state to show shipping options.', 'shipping');
                        break;
                }

                break;

            default:
                break;
        }

        return $exception->getMessageFormatted($messageLang);
    }
}
