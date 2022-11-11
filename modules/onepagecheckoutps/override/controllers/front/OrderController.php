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


class OrderController extends OrderControllerCore
{
    public $checkoutDeliveryStep;

    public $opc;
    public $is_active_module;

    public function init()
    {
        //Cuando se eliminan productos del resumen y luego se intenta ir al checkout
        //pero siempre redirecciona al carrito y no avanza al checkout, este codigo ayuda:
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');

        $this->cartChecksum = new CartChecksum(new AddressChecksum());

        $this->opc = Module::getInstanceByName('onepagecheckoutps');
        if (Validate::isLoadedObject($this->opc) &&
            $this->opc->getService(OnePageCheckoutPS\Application\Core\CoreService::SERVICE_NAME)->isModuleActive($this->opc->name)
        ) {
            $this->is_active_module = true;
        } else {
            $this->is_active_module = false;
        }
        if (!$this->opc->checkCustomerAccessToModule()) {
            $this->is_active_module = false;
        }
        if ($this->is_active_module) {
            $this->opc->initBeforeControllerOPC($this);
            FrontController::init();
            $this->opc->initAfterControllerOPC($this);

            return;
        }

        parent::init();
    }

    public function initContent()
    {
        if ($this->is_active_module) {
            if (Configuration::isCatalogMode()) {
                Tools::redirect('index.php');
            }

            $presentedCart = $this->cart_presenter->present($this->context->cart, true);

            if (count($presentedCart['products']) <= 0 || $presentedCart['minimalPurchaseRequired']) {
                $cartLink = $this->context->link->getPageLink('cart');
                Tools::redirect($cartLink);
            }

            $product = $this->context->cart->checkQuantities(true);
            if (is_array($product)) {
                $cartLink = $this->context->link->getPageLink('cart', null, null, array('action' => 'show'));
                Tools::redirect($cartLink);
            }

            $this->checkoutProcess
                ->setNextStepReachable()
                ->markCurrentStep()
                ->invalidateAllStepsAfterCurrent();

            $this->saveDataToPersist($this->checkoutProcess);

            if (!$this->checkoutProcess->hasErrors()) {
                if ($_SERVER['REQUEST_METHOD'] !== 'GET' && !$this->ajax) {
                    return $this->redirectWithNotifications(
                        $this->checkoutProcess->getCheckoutSession()->getCheckoutURL()
                    );
                }
            }

            $this->preorderCompatibility();

            FrontController::initContent();

            $this->opc->initContentControllerOPC($this);

            return;
        }

        parent::initContent();
    }

    public function getCheckoutSession()
    {
        $deliveryOptionsFinder = new DeliveryOptionsFinder(
            $this->context,
            $this->getTranslator(),
            $this->objectPresenter,
            new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter()
        );

        $session = new CheckoutSession(
            $this->context,
            $deliveryOptionsFinder
        );

        return $session;
    }

    protected function bootstrap()
    {
        $translator = $this->getTranslator();
        $session = $this->getCheckoutSession();

        $this->checkoutProcess = new CheckoutProcess(
            $this->context,
            $session
        );

        $this->checkoutProcess
            ->addStep(new CheckoutPersonalInformationStep(
                $this->context,
                $translator,
                $this->makeLoginForm(),
                $this->makeCustomerForm()
            ))
            ->addStep(new CheckoutAddressesStep(
                $this->context,
                $translator,
                $this->makeAddressForm()
            ));

        if (!$this->context->cart->isVirtualCart()) {
            $this->checkoutDeliveryStep = new CheckoutDeliveryStep(
                $this->context,
                $translator
            );

            $this->checkoutDeliveryStep
                ->setRecyclablePackAllowed((bool) Configuration::get('PS_RECYCLABLE_PACK'))
                ->setGiftAllowed((bool) Configuration::get('PS_GIFT_WRAPPING'))
                ->setIncludeTaxes(
                    !Product::getTaxCalculationMethod((int) $this->context->cart->id_customer)
                    && (int) Configuration::get('PS_TAX')
                )
                ->setDisplayTaxesLabel((Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC')))
                ->setGiftCost(
                    $this->context->cart->getGiftWrappingPrice(
                        $this->checkoutDeliveryStep->getIncludeTaxes()
                    )
                );

            $this->checkoutProcess->addStep($this->checkoutDeliveryStep);
        }

        $this->checkoutProcess
            ->addStep(new CheckoutPaymentStep(
                $this->context,
                $translator,
                new PaymentOptionsFinder(),
                new ConditionsToApproveFinder(
                    $this->context,
                    $translator
                )
            ));

        Hook::exec('actionCheckoutRender', array('checkoutProcess' => &$this->checkoutProcess));

        if ($this->is_active_module) {
            foreach ($this->checkoutProcess->getSteps() as $step) {
                $step->setReachable(true)->setComplete(true)->setCurrent(true);
            }
        }

        //support module: quantitydiscountpro - v2.1.27 - idnovate
        if (Module::isEnabled('quantitydiscountpro') && Tools::getValue('action') == 'updateCarrier') {
            include_once _PS_MODULE_DIR_ . 'quantitydiscountpro/quantitydiscountpro.php';
            $quantityDiscount = new QuantityDiscountRule();
            $quantityDiscount->createAndRemoveRules();
        }
    }

    public function updateCarrier()
    {
        $this->opc->updateCarrierBeforeControllerOPC($this);
        $this->checkoutDeliveryStep->handleRequest(Tools::getAllValues());
        $this->opc->updateCarrierControllerOPC($this);
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitCreate') &&
            Module::isInstalled('recaptcha') &&
            Module::isEnabled('recaptcha') &&
            Configuration::get('CAPTCHA_ENABLE_ACCOUNT')
        ) {
            require_once _PS_ROOT_DIR_ . '/modules/recaptcha/recaptcha.php';
            $recaptcha = new Recaptcha();
            $recaptcha->validateCaptcha();
            if (!empty($this->errors)) {
                //avoids further process
                unset($_POST['password']);
            }
        }

        if (Tools::isSubmit('submitCreate')
            && Module::isInstalled('eicaptcha')
            && Module::isEnabled('eicaptcha')
            && false === Module::getInstanceByName('eicaptcha')->hookActionContactFormSubmitCaptcha(array())
            && !empty($this->errors)
        ) {
            unset($_POST['submitCreate']);
        }

        if ($this->is_active_module) {
            parent::postProcess();
            $this->bootstrap();
            $this->opc->postProcessControllerOPC($this);

            return;
        }

        parent::postProcess();
    }

    protected function saveDataToPersist(CheckoutProcess $process)
    {
        $data = $process->getDataToPersist();
        $cart = $this->context->cart;
        $shouldGenerateChecksum = false;

        if (class_exists('AddressValidator')) {
            $addressValidator = new AddressValidator($this->context);
            $customer = $this->context->customer;

            if ($customer->isGuest()) {
                $shouldGenerateChecksum = true;
            } else {
                $invalidAddressIds = $addressValidator->validateCartAddresses($cart);
                if (empty($invalidAddressIds)) {
                    $shouldGenerateChecksum = true;
                }
            }
        } else {
            $shouldGenerateChecksum = true;
        }

        $data['checksum'] = $shouldGenerateChecksum
            ? $this->cartChecksum->generateChecksum($cart)
            : null;

        Db::getInstance()->execute(
            'UPDATE ' . _DB_PREFIX_ . 'cart SET checkout_session_data = "' . pSQL(json_encode($data)) . '"
                WHERE id_cart = ' . (int) $cart->id
        );
    }

    /**
     * Compatibilidad: preorder - v5.0.2 - Webkul
     */
    public function preorderCompatibility()
    {
        if ($this->is_active_module && $this->opc->getService(OnePageCheckoutPS\Application\Core\CoreService::SERVICE_NAME)->isModuleActive('preorder')) {
            $context = Context::getContext();
            include_once _PS_MODULE_DIR_ . 'preorder/classes/PreorderClasses.php';
            $idCustomer = $context->cart->id_customer;
            if ($cartProducts = $context->cart->getProducts()) {
                foreach ($cartProducts as $productData) {
                    $idProduct = $productData['id_product'];
                    $idAttr = $productData['id_product_attribute'];
                    $preorderObj = new PreorderProduct();
                    $existingPreorderProduct = $preorderObj->getExistingActivePreOrderProduct(
                        $idProduct,
                        $idAttr
                    );
                    if ($existingPreorderProduct) {
                        if (Configuration::get('PS_GUEST_CHECKOUT_ENABLED')) {
                            if (!Configuration::get('WK_GUEST_PREORDER_ENABLED')) {
                                if (!$idCustomer
                                    && $existingPreorderProduct['is_preorder'] == 1
                                    && $existingPreorderProduct['payment_type'] != 1
                                ) {
                                    $this->errors[] = $this->trans(
                                        'Please login to buy preorder product!',
                                        array(),
                                        'Shop.Notifications.Error'
                                    );
                                }
                            }
                        }
                        if ($existingPreorderProduct['payment_type'] == 3
                            && $existingPreorderProduct['is_preorder'] == 1
                        ) {
                            $isSpecificExist = SpecificPrice::getSpecificPrice(
                                $idProduct,
                                0,
                                0,
                                0,
                                0,
                                1,
                                $idAttr,
                                $idCustomer,
                                0,
                                0
                            );
                            if (!$isSpecificExist) {
                                $this->errors[] = $this->trans(
                                    'You can not buy preorder product - %product% by paying full amount of product.',
                                    array('%product%' => $productData['name']),
                                    'Shop.Notifications.Error'
                                );
                            }
                        }
                        $remainingQty = $existingPreorderProduct['maxquantity'] -
                            $existingPreorderProduct['prebooked_quantity'];
                        if ($productData['cart_quantity'] > $remainingQty) {
                            $this->errors[] = $this->trans(
                                'The item %product% in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.',
                                array('%product%' => $productData['name']),
                                'Shop.Notifications.Error'
                            );
                        }
                    }
                    if ($preorderObj->getExistingPreOrderByProductId($productData['id_product'])) {
                        $availableQuantity = StockAvailable::getQuantityAvailableByProduct(
                            $productData['id_product'],
                            $productData['id_product_attribute']
                        );
                        if ($availableQuantity <= 0) {
                            $existingPreorderProductData = $preorderObj->getExistingPreOrderProduct(
                                $productData['id_product'],
                                $productData['id_product_attribute']
                            );
                            if ($existingPreorderProductData) {
                                if ($existingPreorderProductData['is_preorder'] == 0) {
                                    $preorderCustomerObj = new PreorderProductCustomer();
                                    $existingCustomerPreorder = $preorderCustomerObj->getCustomerPreOrderByIdPIdC(
                                        (int) $idCustomer,
                                        $productData['id_product'],
                                        $productData['id_product_attribute']
                                    );
                                    if ($existingCustomerPreorder
                                        && !$existingCustomerPreorder['preorder_complete']
                                    ) {
                                    } else {
                                        $this->errors[] = $this->trans(
                                            'The item %product% in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.',
                                            array('%product%' => $productData['name']),
                                            'Shop.Notifications.Error'
                                        );
                                    }
                                }
                            } else {
                                $this->errors[] = $this->trans(
                                    'The item %product% in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.',
                                    array('%product%' => $productData['name']),
                                    'Shop.Notifications.Error'
                                );
                            }
                        }
                    }
                }
            }
        }
    }
}
