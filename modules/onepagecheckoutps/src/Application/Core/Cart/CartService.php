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

namespace OnePageCheckoutPS\Application\Core\Cart;

use Address;
use Carrier;
use Cart;
use Configuration;
use Currency;
use Db;
use DbQuery;
use OnePageCheckoutPS\Application\Core\AbstractStepCheckout;
use OnePageCheckoutPS\Application\Core\CoreService;
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter;
use Product;
use TaxConfiguration;
use Tools;
use Validate;

class CartService extends AbstractStepCheckout
{
    private $core;
    private $module;
    private $contextProvider;

    public const SERVICE_NAME = 'onepagecheckoutps.core.cart';

    private $templateVars = array();

    public function __construct(CoreService $core)
    {
        $this->core = $core;

        $this->module = $this->core->getModule();
        $this->contextProvider = $this->module->getContextProvider();

        $this->setModule($this->module);
    }

    public function isEditProductsCartEnabled()
    {
        return (bool) $this->module->getConfigurationList('OPC_ALLOW_EDIT_PRODUCTS_CART');
    }

    public function showHookShoppingCart()
    {
        return (bool) $this->module->getConfigurationList('OPC_ENABLE_HOOK_SHOPPING_CART');
    }

    public function showAvailability()
    {
        $psStockManagement = (bool) Configuration::get('PS_STOCK_MANAGEMENT');
        if ($psStockManagement) {
            return (bool) $this->module->getConfigurationList('OPC_SHOW_AVAILABILITY');
        }

        return false;
    }

    public function showDeliveryTime($productList)
    {
        if ($productList && (bool) $this->module->getConfigurationList('OPC_SHOW_DELIVERY_TIME')) {
            $productListDeliverytime = array();
            $langId = $this->contextProvider->getLanguageId();

            foreach ($productList as $product) {
                $productId = (int) $product->id_product;
                $productInstance = new Product($productId);

                if ($productInstance->additional_delivery_times) {
                    if ($product->stock_quantity > 0 && ($product->cart_quantity <= $product->quantity_available)) {
                        if ((int) $productInstance->additional_delivery_times === 1) {
                            $productListDeliverytime[$productId] = Configuration::get(
                                'PS_LABEL_DELIVERY_TIME_AVAILABLE',
                                $langId
                            );
                        } elseif ($productInstance->delivery_in_stock
                            && isset($productInstance->delivery_in_stock[$langId])
                        ) {
                            $productListDeliverytime[$productId] = $productInstance->delivery_in_stock[$langId];
                        }
                    } else {
                        if ((int) $productInstance->additional_delivery_times === 1) {
                            $productListDeliverytime[$productId] = Configuration::get(
                                'PS_LABEL_DELIVERY_TIME_OOSBOA',
                                $langId
                            );
                        } elseif ($productInstance->delivery_out_stock
                            && isset($productInstance->delivery_out_stock[$langId])
                        ) {
                            $productListDeliverytime[$productId] = $productInstance->delivery_out_stock[$langId];
                        }
                    }
                }
            }

            return $productListDeliverytime;
        }

        return false;
    }

    public function getCartPresenter()
    {
        $cartPresenter = new CartPresenter();

        return $cartPresenter->present($this->contextProvider->getCart());
    }

    public function updateAddress()
    {
        if ($this->getParameter('typeAddress') === 'delivery') {
            $this->setIdAddressDelivery(
                (int) $this->getParameter('addressId'),
                (bool) $this->getParameter('haveSameAddress')
            );
        } elseif ($this->getParameter('typeAddress') === 'invoice') {
            $this->setIdAddressInvoice((int) $this->getParameter('addressId'));
        }
    }

    public function setIdAddressDelivery($addressId, $setSameAddressToInvoice)
    {
        $cart = $this->contextProvider->getCart();
        $currentAddressId = $cart->id_address_delivery;
        $isInvoiceAddressEnabled = $this->getAddressesService()->isInvoiceAddressEnabled();

        if ($setSameAddressToInvoice || !$isInvoiceAddressEnabled) {
            $cart->id_address_invoice = $addressId;
        }

        $cart->id_address_delivery = $addressId;
        $cart->save();

        //Compatibilidad con mondialrealy - v3.1.1 - ScaleDEV
        //cambiamos esta variable ya que nos hace entrar en conflicto al cambiar de direcciones.
        if (isset($this->contextProvider->getCookie()->mondialrelay_id_original_delivery_address)) {
            $this->contextProvider->getCookie()->mondialrelay_id_original_delivery_address = $addressId;
        }

        //Solo disponible despues de 1.7.7.0
        //$cart->updateDeliveryAddressId($cart->id_address_delivery, $addressId);

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'cart_product`
        SET `id_address_delivery` = ' . $addressId . '
        WHERE  `id_cart` = ' . (int) $cart->id . '
            AND `id_address_delivery` = ' . $currentAddressId;
        Db::getInstance()->execute($sql);

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'customization`
            SET `id_address_delivery` = ' . $addressId . '
            WHERE  `id_cart` = ' . (int) $cart->id . '
                AND `id_address_delivery` = ' . $currentAddressId;
        Db::getInstance()->execute($sql);
    }

    public function setIdAddressInvoice($addressId)
    {
        $cart = $this->contextProvider->getCart();
        $cart->id_address_invoice = $addressId;
        $cart->save();

        $isDeliveryAddressEnabled = $this->getAddressesService()->isDeliveryAddressEnabled();
        if (!$isDeliveryAddressEnabled) {
            $this->setIdAddressDelivery($addressId, false);
        }
    }

    /*public function areProductsAvailable()
    {
        $product = $this->contextProvider->getCart()->checkQuantities(true);
        if (true === $product || !is_array($product)) {
            return true;
        }

        return $product;
    }*/

    public function getFreeShip($cartPresenterVars)
    {
        $freeShipping = array();

        if ($this->module->getConfigurationList('OPC_SHOW_REMAINING_FREE_SHIPPING')) {
            if (!$cartPresenterVars['is_virtual']
                && $cartPresenterVars['subtotals']['shipping']['amount'] > 0
            ) {
                $cart = $this->contextProvider->getCart();
                $currency = new Currency((int) $cart->id_currency);

                $discounts = $cart->getCartRules();
                foreach ($discounts as $discount) {
                    if ($discount['free_shipping'] == 1) {
                        return $freeShipping;
                    }
                }

                $missingPrice = 0;
                $totalProductsTax = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
                $totalDiscounts = $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);

                if ($shippingConfiguratorPro = $this->module->core->isModuleActive('shippingconfiguratorpro')) {
                    if (version_compare($shippingConfiguratorPro->version, '4.0.2.1', '>=')) {
                        $endFreeShippingPrice = $shippingConfiguratorPro->getFreeShip(
                            $cart->id_carrier,
                            $cart->id_address_delivery,
                            $totalProductsTax - $totalDiscounts
                        );
                    }
                } else {
                    $endFreeShippingPrice = Tools::convertPrice(
                        (float) Configuration::get('PS_SHIPPING_FREE_PRICE'),
                        $currency
                    );
                }

                if (empty($endFreeShippingPrice)) {
                    $carrier = new Carrier($cart->id_carrier);

                    if (Validate::isLoadedObject($carrier)) {
                        if ($carrier->shipping_method == Carrier::SHIPPING_METHOD_PRICE && $carrier->is_free == 0) {
                            $idZone = Address::getZoneById($cart->id_address_delivery);

                            if (!empty($idZone)) {
                                $query = new DbQuery();
                                $query->select('rp.delimiter1, rp.delimiter2, d.price');
                                $query->from('range_price', 'rp');
                                $query->innerJoin('delivery', 'd', 'd.id_range_price = rp.id_range_price');
                                $query->where('rp.id_carrier = ' . $carrier->id . ' AND d.price = 0 AND d.id_zone = ' . $idZone);

                                $ranges = Db::getInstance()->executeS($query);

                                if (is_array($ranges) > 0 && count($ranges) > 0) {
                                    foreach ($ranges as $range) {
                                        $delimiter1 = Tools::convertPrice($range['delimiter1'], $currency);
                                        if (($totalProductsTax - $totalDiscounts) < $delimiter1) {
                                            $endFreeShippingPrice = $delimiter1;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if ($endFreeShippingPrice) {
                    $missingPrice = $endFreeShippingPrice - ($totalProductsTax - $totalDiscounts);
                    $actualPercent = ($missingPrice > 0) ? (100 - (($missingPrice * 100) / $endFreeShippingPrice)) : 0;
                    $actualPrice = $endFreeShippingPrice - $missingPrice;

                    $freeShipping = array(
                        'startFreeShippingPrice' => Tools::displayPrice(0, $currency),
                        'endFreeShippingPrice' => Tools::displayPrice($endFreeShippingPrice, $currency),
                        'missingPrice' => Tools::displayPrice($missingPrice, $currency),
                        'actualPercent' => $actualPercent,
                        'actualPrice' => Tools::displayPrice($actualPrice, $currency),
                    );
                }
            }
        }

        return $freeShipping;
    }

    public function getTemplateVars()
    {
        if (empty($this->templateVars)) {
            $cartPresenterVars = $this->getCartPresenter();

            //$presenterFactory = new \ProductPresenterFactory($this->contextProvider->getContextLegacy());
            //$presentationSettings = $presenterFactory->getPresentationSettings();

            /*if (count($vars['products']) <= 0 || $vars['minimalPurchaseRequired']) {
                // if there is no product in current cart, redirect to cart page
                $cartLink = $this->context->link->getPageLink('cart');
                Tools::redirect($cartLink);
            }*/

            $vars = array(
                'cartPresenterVars' => $cartPresenterVars,
                'displayPriceTaxIncluide' => (bool) (new TaxConfiguration())->includeTaxes(),
                'isTaxesEnabled' => (bool) Configuration::get('PS_TAX'),
                'isEditProductsCartEnabled' => $this->isEditProductsCartEnabled(),
                //'areProductsAvailable' => $this->areProductsAvailable(),
                'cartUrl' => $this->contextProvider->getLink()->getPageLink('cart'),
                'staticToken' => Tools::getToken(false),
                'showAvailability' => $this->showAvailability(),
                'showDeliveryTime' => $this->showDeliveryTime($cartPresenterVars['products']),
                'psLabelOssProductsBoa' => Configuration::get(
                    'PS_LABEL_OOS_PRODUCTS_BOA',
                    $this->contextProvider->getLanguageId()
                ),
                'showHookShoppingCart' => $this->showHookShoppingCart(),
                'freeShipping' => $this->getFreeShip($cartPresenterVars),
                'showVoucherBox' => $this->module->getConfigurationList('OPC_SHOW_VOUCHER_BOX'),
                'showWeight' => $this->module->getConfigurationList('OPC_SHOW_WEIGHT'),
                'weightUnit' => Configuration::get('PS_WEIGHT_UNIT'),
            );

            return array_merge($this->core->getCommonVars(), $vars);
        }

        return $this->templateVars;
    }

    public function render()
    {
        $this->contextProvider->getSmarty()->assign(
            $this->getTemplateVars()
        );

        return $this->contextProvider->getSmarty()->fetch(
            'module:onepagecheckoutps/views/templates/front/checkout/cart/cart.tpl'
        );
    }
}
