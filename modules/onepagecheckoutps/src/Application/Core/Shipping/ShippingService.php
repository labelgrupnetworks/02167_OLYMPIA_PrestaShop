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

namespace OnePageCheckoutPS\Application\Core\Shipping;

use CheckoutDeliveryStep;
use CheckoutProcess;
use CheckoutSession;
use Configuration;
use Country;
use DeliveryOptionsFinder;
use Hook;
use OnePageCheckoutPS\Application\Core\AbstractStepCheckout;
use OnePageCheckoutPS\Application\Core\CoreService;
use OnePageCheckoutPS\Exception\ShippingException;
use PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use Product;
use Validate;

class ShippingService extends AbstractStepCheckout
{
    private $core;
    private $module;
    private $contextProvider;

    private $objectPresenter;
    private $priceFormatter;
    private $deliveryOptionsFinder;
    private $sessionCheckout;
    private $checkoutProcess;
    private $checkoutDeliveryStep;

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

        $this->checkoutDeliveryStep = new CheckoutDeliveryStep(
            $this->contextProvider->getContextLegacy(),
            $this->module->getTranslator()
        );
        $this->checkoutDeliveryStep->setCheckoutProcess($this->checkoutProcess);
        $this->checkoutDeliveryStep
            ->setRecyclablePackAllowed((bool) Configuration::get('PS_RECYCLABLE_PACK'))
            ->setGiftAllowed((bool) Configuration::get('PS_GIFT_WRAPPING'))
            ->setIncludeTaxes(
                !Product::getTaxCalculationMethod((int) $this->contextProvider->getCart()->id_customer)
                && (int) Configuration::get('PS_TAX')
            )
            ->setDisplayTaxesLabel((Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC')))
            ->setGiftCost(
                $this->contextProvider->getCart()->getGiftWrappingPrice(
                    $this->checkoutDeliveryStep->getIncludeTaxes()
                )
            );
    }

    public function update($requestParameters)
    {
        $this->checkoutDeliveryStep->handleRequest($requestParameters);
    }

    public function isNeedState($country = null)
    {
        $isNeedState = false;

        if (Validate::isLoadedObject($country)) {
            if (!is_null($country)) {
                $isNeedState = (bool) $country->contains_states;
            }
        }

        return (bool) $isNeedState;
    }

    public function isNeedPostCode($country = null)
    {
        $country = $country;
        $isNeedPostCode = $this->module->getConfigurationList('OPC_FORCE_NEED_POSTCODE');
        /*if (!$isNeedPostCode && Validate::isLoadedObject($country)) {
            if (!is_null($country)) {
                $isNeedPostCode = $country->need_zip_code;
            }
        }*/

        if (!$isNeedPostCode) {
            $modulesNeedPostCode = explode(
                ',',
                $this->module->getConfigurationList('OPC_MODULE_CARRIER_NEED_POSTCODE')
            );
            foreach ($modulesNeedPostCode as $module) {
                if ($this->module->core->isModuleActive($module)) {
                    $isNeedPostCode = true;
                }
            }
        }

        return (bool) $isNeedPostCode;
    }

    public function isNeedCity()
    {
        $isNeedCity = $this->module->getConfigurationList('OPC_FORCE_NEED_CITY');
        if (!$isNeedCity) {
            $modulesNeedCity = explode(',', $this->module->getConfigurationList('OPC_MODULE_CARRIER_NEED_CITY'));
            foreach ($modulesNeedCity as $module) {
                if ($this->module->core->isModuleActive($module)) {
                    $isNeedCity = true;
                }
            }
        }

        return (bool) $isNeedCity;
    }

    public function getCarrierIdSelected()
    {
        $carrierIdSelected = 0;

        $deliveryOptionList = $this->contextProvider->getCart()->getDeliveryOptionList();
        foreach ($this->contextProvider->getCart()->getDeliveryOption() as $key => $value) {
            if (isset($deliveryOptionList[$key][$value])) {
                if (count($deliveryOptionList[$key][$value]['carrier_list']) == 1) {
                    $carrierIdSelected = current(array_keys($deliveryOptionList[$key][$value]['carrier_list']));
                }
            }
        }

        return (int) $carrierIdSelected;
    }

    public function getDeliveryOptionList()
    {
        if ($this->contextProvider->isVirtualCart()) {
            return false;
        }

        $customer = $this->sessionCheckout->getCustomer();
        if ($customer->isLogged() || $customer->isGuest()) {
            $totalCustomerAddresses = (int) $this->sessionCheckout->getCustomerAddressesCount();
            if ($totalCustomerAddresses === 0) {
                throw new ShippingException(
                    'It is necessary to create an address to be able to show the different shipping options.',
                    ShippingException::SHIPPING_NEED_ADDRESS
                );
            }

            $addressesDelivery = $this->getAddressesService()->getCustomerAddressByType('delivery');
            if (!$addressesDelivery) {
                throw new ShippingException(
                    'It is necessary to create an delivery address to be able to show the different shipping options.',
                    ShippingException::SHIPPING_NEED_ADDRESS
                );
            }
        }

        $deliveryAddress = $this->getAddressesService()->getAddressDeliveryId(true);
        if (!$deliveryAddress) {
            throw new ShippingException(
                'It is necessary to create an delivery address to be able to show the different shipping options.',
                ShippingException::SHIPPING_NEED_ADDRESS
            );
        }

        $country = new Country($deliveryAddress->id_country);
        if (!Validate::isLoadedObject($country)) {
            throw new ShippingException(
                'Select a country to show the different shipping options.',
                ShippingException::SHIPPING_NEED_COUNTRY
            );
        }

        if ($this->isNeedState($country)) {
            if (empty($deliveryAddress->id_state)) {
                throw new ShippingException(
                    'You need to place a state to show shipping options.',
                    ShippingException::SHIPPING_NEED_STATE
                );
            }
        }

        if ($this->isNeedPostCode($country)) {
            $zipCodeFormatted = $country->zip_code_format;
            if (!empty($zipCodeFormatted)) {
                $zipCodeFormatted = str_replace('N', '0', $zipCodeFormatted);
                $zipCodeFormatted = str_replace('L', 'A', $zipCodeFormatted);
                $zipCodeFormatted = str_replace('C', $country->iso_code, $zipCodeFormatted);
            }

            if (empty($deliveryAddress->postcode)
                || $deliveryAddress->postcode === $zipCodeFormatted
                || !$country->checkZipCode($deliveryAddress->postcode)
            ) {
                throw new ShippingException(
                    'You need to place a postcode to show shipping options.',
                    ShippingException::SHIPPING_NEED_POSTCODE
                );
            }
        }

        if ($this->isNeedCity()) {
            if (empty($deliveryAddress->city) || $deliveryAddress->city === '.') {
                throw new ShippingException(
                    'You need to place a city to show shipping options.',
                    ShippingException::SHIPPING_NEED_CITY
                );
            }
        }

        //Este codigo es para actualizar el carrito con los datos del transporte seleccionado por defecto
        //si no se hace, puede fallar la logica de muchos modulos.
        $cart = $this->contextProvider->getCart();
        $carrierIdSelected = $this->getCarrierIdSelected();
        $cart->id_carrier = $carrierIdSelected;
        $cart->setDeliveryOption(array(
            (int) $deliveryAddress->id => sprintf('%d,', $carrierIdSelected),
        ));
        $cart->update();

        //Para compatibilidades con modulos de terceros que comparan el controller "order" para alguna ejecucion
        $this->contextProvider->getController()->php_self = 'order';

        return $this->sessionCheckout->getDeliveryOptions();
    }

    public function getTemplateVars()
    {
        $idAddress = $this->getAddressesService()->getAddressDeliveryId();

        $vars = array(
            'hookDisplayBeforeCarrier' => Hook::exec(
                'displayBeforeCarrier',
                array(
                    'cart' => $this->contextProvider->getCart(),
                    'sessionCheckout' => $this->sessionCheckout,
                )
            ),
            'hookDisplayAfterCarrier' => Hook::exec(
                'displayAfterCarrier',
                array(
                    'cart' => $this->contextProvider->getCart(),
                    'sessionCheckout' => $this->sessionCheckout,
                )
            ),
            'isShowCarrierImage' => $this->module->getConfigurationList('OPC_SHOW_IMAGE_CARRIER'),
            'isShowCarrierDescription' => $this->module->getConfigurationList('OPC_SHOW_DESCRIPTION_CARRIER'),
            'deliveryOptions' => $this->getDeliveryOptionList(),
            'deliveryOption' => $this->sessionCheckout->getSelectedDeliveryOption(),
            'idAddress' => $idAddress,
            'recyclablePackAllowed' => $this->checkoutDeliveryStep->isRecyclablePackAllowed(),
            'recyclable' => $this->sessionCheckout->isRecyclable(),
            'isShowDeliveryMessage' => $this->module->getConfigurationList('OPC_SHOW_ORDER_MESSAGE'),
            'deliveryMessage' => $this->sessionCheckout->getMessage(),
            'Gift' => array(
                'allowed' => $this->checkoutDeliveryStep->isGiftAllowed(),
                'isGift' => $this->sessionCheckout->getGift()['isGift'],
                'label' => $this->checkoutDeliveryStep->getGiftCostForLabel(),
                'message' => $this->sessionCheckout->getGift()['message'],
            ),
            'moduleImageUrl' => $this->module->onepagecheckoutps_dir . 'views/img/',
        );

        return array_merge($this->core->getCommonVars(), $vars);
    }

    public function render()
    {
        if ($this->contextProvider->isVirtualCart()) {
            return false;
        }

        $this->contextProvider->getSmarty()->assign(
            $this->getTemplateVars()
        );

        return $this->contextProvider->getSmarty()->fetch('module:onepagecheckoutps/views/templates/front/checkout/shipping/shipping.tpl');
    }
}
