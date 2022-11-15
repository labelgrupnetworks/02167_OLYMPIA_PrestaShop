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
use OnePageCheckoutPS\Exception\PaymentException;

class OnePageCheckoutPSPaymentModuleFrontController extends ModuleFrontControllerCore implements IFrontController
{
    //public $php_self = 'module-onepagecheckoutps-payment';

    private $checkoutProcess;

    public function postProcess()
    {
        $coreService = $this->get('onepagecheckoutps.core.core_service');
        $coreService->executeActionRequest($this);
    }

    public function getCheckoutProcess()
    {
        return $this->checkoutProcess;
    }

    public function getPaymentList()
    {
        $customerLogged = false;

        try {
            $this->module->loginTemporalCustomer($customerLogged);

            $payment = $this->get('onepagecheckoutps.core.payment');

            //Mostramos los pagos segun la direccion de facturacion.
            if (!$this->context->customer->isLogged() && !$this->context->customer->isGuest()) {
                $addressInvoice = new Address($this->context->cart->id_address_invoice);
                if (Validate::isLoadedObject($addressInvoice)) {
                    $this->context->country = new Country($addressInvoice->id_country);
                }
            }

            $this->checkoutProcess = $payment->getCheckoutProcess();
            $stepPaymentRendered = $payment->render();

            if ($customerLogged) {
                $this->module->logoutTemporalCustomer();
            }

            return array(
                'success' => true,
                'stepPaymentRendered' => $stepPaymentRendered,
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
            case 'OnePageCheckoutPS\Exception\PaymentException':
                switch ($exception->getCode()) {
                    case PaymentException::PAYMENT_PRODUCT_WIHTOUT_STOCK:
                        $messageLang = $this->l('An item (%s) in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.', 'payment');
                        break;
                    case PaymentException::PAYMENT_NEED_ADDRESS:
                        $messageLang = $this->l('It is necessary to create an address to be able to show the different payment options.', 'payment');
                        break;
                    case PaymentException::PAYMENT_PRODUCT_NOT_AVAILABLE:
                        $messageLang = $this->l('This product (%s) is no longer available.', 'payment');
                        break;
                    case PaymentException::PAYMENT_NEED_SHIPPING:
                        $messageLang = $this->l('You must select a shipping method to view the available payment methods.', 'payment');
                        break;
                }

                break;
            default:
                break;
        }

        return $exception->getMessageFormatted($messageLang);
    }
}
