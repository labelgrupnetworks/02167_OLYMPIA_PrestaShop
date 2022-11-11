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


namespace OnePageCheckoutPS\Application\Core\Payment;

use Carrier;
use Cart;
use CheckoutProcess;
use CheckoutSession;
use ConditionsToApproveFinder;
use Configuration;
use DeliveryOptionsFinder;
use Hook;
use Module;
use OnePageCheckoutPS\Application\Core\AbstractStepCheckout;
use OnePageCheckoutPS\Application\Core\CoreService;
use OnePageCheckoutPS\Exception\OPCException;
use OnePageCheckoutPS\Exception\PaymentException;
use PaymentClass;
use PaymentOptionsFinder;
use PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use State;
use Tools;
use Validate;

class PaymentService extends AbstractStepCheckout
{
    private $core;
    private $module;
    private $contextProvider;

    private $objectPresenter;
    private $priceFormatter;
    private $deliveryOptionsFinder;
    private $sessionCheckout;
    private $checkoutProcess;

    private $paymentListForceShowDetails = array(
        'ps_checkout',
        'paypal',
    );

    public function __construct(CoreService $core)
    {
        $this->core = $core;

        $this->module = $this->core->getModule();
        $this->contextProvider = $this->module->getContextProvider();

        $this->setModule($this->module);

        $this->objectPresenter = new ObjectPresenter();
        $this->priceFormatter = new PriceFormatter();

        $this->deliveryOptionsFinder = new DeliveryOptionsFinder(
            $this->contextProvider->getContextLegacy(),
            $this->module->getTranslator(),
            $this->objectPresenter,
            $this->priceFormatter
        );

        $this->sessionCheckout = new CheckoutSession(
            $this->contextProvider->getContextLegacy(),
            $this->deliveryOptionsFinder
        );

        $this->checkoutProcess = new CheckoutProcess(
            $this->contextProvider->getContextLegacy(),
            $this->sessionCheckout
        );

        $this->checkoutProcess->addStep(
            (new \CheckoutPaymentStep(
                $this->contextProvider->getContextLegacy(),
                $this->module->getTranslator(),
                new PaymentOptionsFinder(),
                new ConditionsToApproveFinder(
                    $this->contextProvider->getContextLegacy(),
                    $this->module->getTranslator()
                )
            ))->setCurrent(true)
        );
    }

    public function getCheckoutProcess()
    {
        return $this->checkoutProcess;
    }

    public function getConditionList()
    {
        $conditionsToApprove = array();
        if ($this->module->getConfigurationList('OPC_ENABLE_TERMS_CONDITIONS')) {
            $conditionsToApproveFinder = new ConditionsToApproveFinder(
                $this->contextProvider->getContextLegacy(),
                $this->module->getTranslator()
            );
            $conditionsToApprove = $conditionsToApproveFinder->getConditionsToApproveForTemplate();
        }

        return $conditionsToApprove;
    }

    public function isShowDiscountBoxOnPaymentEnabled()
    {
        if ($this->module->getConfigurationList('OPC_SHOW_DISCOUNT_BOX_PAYMENT_MOBILE')
            && $this->contextProvider->isMobile()
        ) {
            return true;
        }

        return false;
    }

    public function isConfirmAddressDeliveryEnabled()
    {
        if (!$this->contextProvider->isVirtualCart()) {
            if ($this->module->getConfigurationList('OPC_CONFIRM_ADDRESS')) {
                $configurationCarrierStorePickup = $this->module->getConfigurationList('OPC_CARRIER_STORE_PICKUP');
                if (!empty($configurationCarrierStorePickup)) {
                    $selectedCarrierStorePickupList = array();

                    $configurationCarrierStorePickup = explode(',', $configurationCarrierStorePickup);
                    if ($configurationCarrierStorePickup) {
                        foreach ($configurationCarrierStorePickup as $carrierStorePickup) {
                            $carrier = Carrier::getCarrierByReference((int) $carrierStorePickup);
                            if (Validate::isLoadedObject($carrier)) {
                                $selectedCarrierStorePickupList[] = (int) $carrier->id;
                            }
                        }
                    }

                    if (in_array(
                        (int) $this->contextProvider->getCart()->id_carrier,
                        $selectedCarrierStorePickupList
                    )) {
                        return false;
                    }
                }

                $addressDelivery = $this->getAddressesService()->getAddressDeliveryId(true);
                if (Validate::isLoadedObject($addressDelivery)) {
                    $postcode = !empty($addressDelivery->postcode) ? ', ' . $addressDelivery->postcode : '';
                    $city = !empty($addressDelivery->city) ? ', ' . $addressDelivery->city : '';
                    $state = !empty($addressDelivery->id_state) ? ', ' . State::getNameById($addressDelivery->id_state) : '';

                    return $addressDelivery->address1 . $postcode . $city . $state;
                }
            }
        }

        return false;
    }

    public function getExpressCheckoutModuleList()
    {
        $expressCheckoutModuleList = false;
        $moduleList = array('amazonpay', 'paypal');

        foreach ($moduleList as $key => $moduleName) {
            if ($this->core->isModuleActive($moduleName)) {
                //support module: amazonpay - v4.1.1 - patworx multimedia GmbH
                if ($moduleName === 'amazonpay'
                    && (bool) Configuration::get('AMAZONPAY_PLACEMENT_CHECKOUT')
                    && isset($this->contextProvider->getCookie()->amazon_pay_checkout_session_id)
                ) {
                    $expressCheckoutModuleList = array(
                        'ECmoduleName' => $moduleName,
                        'ECpaymentName' => 'Amazon Pay',
                        'ECpaymentLogoUrl' => $this->module->onepagecheckoutps_dir . 'views/img/payments/amazonpay_logo.png',
                        'ECsesionResetUrl' => $this->contextProvider->getLink()->getModuleLink('amazonpay', 'reset'),
                    );

                    break;
                //support module: paypal - v5.7.1 - 202 ecommerce
                } elseif ($moduleName === 'paypal'
                    && ((bool) Configuration::get('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT') || (bool) Configuration::get('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT_CART') || Configuration::get('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT_SIGNUP'))
                    && isset($this->contextProvider->getCookie()->paypal_ecs)
                ) {
                    $customer = $this->sessionCheckout->getCustomer();
                    $expressCheckoutModuleList = array(
                        'ECmoduleName' => 'express_checkout_schortcut',
                        'ECpaymentName' => 'Paypal',
                        'ECpaymentLogoUrl' => $this->module->onepagecheckoutps_dir . 'views/img/payments/paypal.jpeg',
                        'ECsesionResetUrl' => $this->contextProvider->getLink()->getModuleLink('onepagecheckoutps', 'resetec', array(
                            'payment_name' => 'express_checkout_schortcut',
                            'token' => $customer->secure_key,
                        )),
                    );

                    break;
                }
            }
        }

        return $expressCheckoutModuleList;
    }

    public function getPaymentOptionList()
    {
        //Si no hay transportes y no es carrito virtual, no mostramos los pagos para evitar errores.
        if (!$this->contextProvider->isVirtualCart()) {
            try {
                $deliveryOptionList = $this->getShippingService()->getDeliveryOptionList();
                if (!$deliveryOptionList) {
                    return array();
                }
            } catch (OPCException $exception) {
                throw new PaymentException(
                    'You must select a shipping method to view the available payment methods.',
                    PaymentException::PAYMENT_NEED_SHIPPING
                );
            }
        }

        $orderTotal = (float) $this->contextProvider->getCart()->getOrderTotal(true, Cart::BOTH);
        $isFree = ($orderTotal == 0);

        $customerIp = Tools::getRemoteAddr();
        $moduleImageUrl = _PS_MODULE_DIR_ . $this->module->name . '/views/img/payments/';

        $paymentOptionsFinder = new PaymentOptionsFinder();
        $paymentOptions = $paymentOptionsFinder->present($isFree);

        if ($paymentOptions) {
            foreach ($paymentOptions as $moduleName => &$moduleOptions) {
                if ($moduleName == 'free_order') {
                    continue;
                }

                if (!$moduleOptions) {
                    unset($paymentOptions[$moduleName]);
                    continue;
                }

                foreach ($moduleOptions as $numberOption => &$option) {
                    if (empty($option['module_name'])) {
                        $option['module_name'] = $moduleName;
                    }

                    $paymentModule = Module::getInstanceByName($moduleName);
                    $option['force_display'] = 0;
                    $option['id_module_payment'] = $paymentModule->id;

                    $paymentId = PaymentClass::getIdPaymentBy('id_module', (int) $option['id_module_payment']);
                    $payment = new PaymentClass($paymentId, $this->contextProvider->getLanguageId());

                    if (Validate::isLoadedObject($payment)) {
                        $displayModule = true;
                        if ($payment->test_mode) {
                            if (!empty($payment->test_ip)) {
                                $ipAvailables = explode(',', $payment->test_ip);
                                if (!in_array($customerIp, $ipAvailables)) {
                                    $displayModule = false;
                                }
                            } else {
                                $displayModule = false;
                            }
                        }

                        if (!$displayModule) {
                            unset($paymentOptions[$moduleName]);
                            continue;
                        }

                        if (!empty($payment->name_image) && $payment->name_image != 'no-image.png') {
                            if (file_exists(_PS_MODULE_DIR_ . $this->module->name . '/views/img/payments/' . $payment->name_image)) {
                                $option['logo'] = $this->module->onepagecheckoutps_dir . 'views/img/payments/' . $payment->name_image;
                            }
                        }

                        if ($payment->title) {
                            $option['title_opc'] = $payment->title;
                        }

                        if ($payment->description) {
                            $option['description_opc'] = $payment->description;
                        }

                        $option['force_display'] = $payment->force_display;
                    }

                    if (empty($option['logo'])) {
                        if (file_exists($moduleImageUrl . $moduleName . '.png')) {
                            $option['logo'] = $this->module->onepagecheckoutps_dir . 'views/img/payments/' . $moduleName . '.png';
                        } elseif (file_exists($moduleImageUrl . $moduleName . '.gif')) {
                            $option['logo'] = $this->module->onepagecheckoutps_dir . 'views/img/payments/' . $moduleName . '.gif';
                        } elseif (file_exists($moduleImageUrl . $moduleName . '.jpeg')) {
                            $option['logo'] = $this->module->onepagecheckoutps_dir . 'views/img/payments/' . $moduleName . '.jpeg';
                        } elseif (file_exists($moduleImageUrl . $moduleName . '.jpg')) {
                            $option['logo'] = $this->module->onepagecheckoutps_dir . 'views/img/payments/' . $moduleName . '.jpg';
                        } else {
                            $option['logo'] = $this->module->onepagecheckoutps_dir . 'views/img/payments/default.png';

                            //ps_checkout - v2.15.4 - PrestaShop
                            if ($moduleName === 'ps_checkout') {
                                $nameImage = str_replace('ps_checkout-', '', $option['module_name']);
                                $option['logo'] = __PS_BASE_URI__ . 'modules/' . $moduleName . '/views/img/' . $nameImage . '.svg';
                            }

                            if (count($moduleOptions) > 1) {
                                if (file_exists($moduleImageUrl . $moduleName . '_' . $numberOption . '.png')) {
                                    $option['logo'] = $this->module->onepagecheckoutps_dir . 'views/img/payments/' . $moduleName . '_' . $numberOption . '.png';
                                } elseif (file_exists($moduleImageUrl . $moduleName . '_' . $numberOption . '.gif')) {
                                    $option['logo'] = $this->module->onepagecheckoutps_dir . 'views/img/payments/' . $moduleName . '_' . $numberOption . '.gif';
                                } elseif (file_exists($moduleImageUrl . $moduleName . '_' . $numberOption . '.jpeg')) {
                                    $option['logo'] = $this->module->onepagecheckoutps_dir . 'views/img/payments/' . $moduleName . '_' . $numberOption . '.jpeg';
                                }
                            }
                        }
                    }

                    if (Hook::isModuleRegisteredOnHook(
                        $paymentModule,
                        'actionOpcPaymentFeeService',
                        $this->contextProvider->getShopId()
                    )) {
                        $option['paymentFee'] = current(Hook::exec(
                            'actionOpcPaymentFeeService',
                            array('option' => $option),
                            $paymentModule->id,
                            true
                        ));
                    } else {
                        $option['paymentFee'] = current(Hook::exec(
                            'actionOpcPaymentFeeService',
                            array('option' => $option),
                            $this->module->id,
                            true
                        ));
                    }
                }
            }
        }

        return $paymentOptions;
    }

    public function validate()
    {
        $product = $this->contextProvider->getCart()->checkQuantities(true);
        if ($product !== true) {
            if ((bool) $product['active']) {
                throw new PaymentException(
                    sprintf(
                        'An item (%s) in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.',
                        $product['name']
                    ),
                    PaymentException::PAYMENT_PRODUCT_WIHTOUT_STOCK,
                    array(
                        $product['name'],
                    )
                );
            } else {
                throw new PaymentException(
                    sprintf(
                        'This product (%s) is no longer available.',
                        $product['name']
                    ),
                    PaymentException::PAYMENT_PRODUCT_NOT_AVAILABLE,
                    array(
                        $product['name'],
                    )
                );
            }
        }

        $customer = $this->sessionCheckout->getCustomer();
        if ($customer->isLogged() || $customer->isGuest()) {
            $totalCustomerAddresses = (int) $this->sessionCheckout->getCustomerAddressesCount();
            if ($totalCustomerAddresses === 0) {
                throw new PaymentException(
                    'It is necessary to create an address to be able to show the different payment options.',
                    PaymentException::PAYMENT_NEED_ADDRESS
                );
            }
        }

        //Validamos funciones de modulos externos que restringen el comprar por alguna razon.
        $resultHook = Hook::exec('actionOpcValidatePayment', array(), null, true);
        if (!empty($resultHook) && is_array($resultHook)) {
            $errorList = current($resultHook);
            if (is_array($errorList) && count($errorList) > 0) {
                $message = implode('<br />', $errorList);
                throw new PaymentException(
                    $message,
                    PaymentException::PAYMENT_HOOK_VALIDATE
                );
            }
        }
    }

    public function getTemplateVars()
    {
        $orderTotal = (float) $this->contextProvider->getCart()->getOrderTotal(true, Cart::BOTH);
        $isFree = ($orderTotal == 0);

        //soluciona problema con algunos metodos de pago que no les llega este objecto cargado. se desconoce la causa.
        $this->contextProvider->getSmarty()->assign('link', $this->contextProvider->getLink());

        $vars = array(
            'orderTotal' => $orderTotal,
            'orderTotalLabel' => $this->priceFormatter->format($orderTotal),
            'isFree' => $isFree,
            'paymentOptions' => $this->getPaymentOptionList(),
            'conditionsToApprove' => $this->getConditionList(),
            'showPaymentImage' => $this->module->getConfigurationList('OPC_SHOW_IMAGE_PAYMENT'),
            'showPaymentDetail' => $this->module->getConfigurationList('OPC_SHOW_DETAIL_PAYMENT'),
            'paymentListForceShowDetails' => $this->paymentListForceShowDetails,
            'defaultPaymentMethod' => $this->module->getConfigurationList('OPC_DEFAULT_PAYMENT_METHOD'),
            'isConfirmAddressDeliveryEnabled' => $this->isConfirmAddressDeliveryEnabled(),
            'isShowDiscountBoxOnPaymentEnabled' => $this->isShowDiscountBoxOnPaymentEnabled(),
            'showVoucherBox' => $this->module->getConfigurationList('OPC_SHOW_VOUCHER_BOX'),
        );

        if ($this->isShowDiscountBoxOnPaymentEnabled()) {
            $vars['cartPresenterVars'] = $this->getCartService()->getCartPresenter();
            $vars['cartUrl'] = $this->contextProvider->getLink()->getPageLink('cart');
            $vars['staticToken'] = Tools::getToken(false);
        }

        return array_merge($this->core->getCommonVars(), $vars);
    }

    public function render()
    {
        $this->validate();

        $this->contextProvider->getSmarty()->assign(
            $this->getTemplateVars()
        );

        $expressCheckoutModuleList = $this->getExpressCheckoutModuleList();
        if ($expressCheckoutModuleList) {
            $this->contextProvider->getSmarty()->assign($expressCheckoutModuleList);

            return $this->contextProvider->getSmarty()->fetch(
                'module:onepagecheckoutps/views/templates/front/checkout/payment/_partials/express_checkout.tpl'
            );
        }

        return $this->contextProvider->getSmarty()->fetch(
            'module:onepagecheckoutps/views/templates/front/checkout/payment/payment.tpl'
        );
    }
}
