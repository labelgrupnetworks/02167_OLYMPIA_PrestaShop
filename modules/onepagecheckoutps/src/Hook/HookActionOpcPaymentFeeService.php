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

use Cart;
use Configuration;
use Db;
use DbQuery;
use Module;
use OnePageCheckoutPS;
use OnePageCheckoutPS\Application\PrestaShop\Provider\ContextProvider;
use OnePageCheckoutPS\Exception\HookException;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use Validate;

class HookActionOpcPaymentFeeService extends AbstractHook
{
    private $module;
    private $contextProvider;

    public function __construct(OnePageCheckoutPS $module, ContextProvider $contextProvider)
    {
        $this->module = $module;
        $this->contextProvider = $contextProvider;
    }

    private function getFeeByModule()
    {
        $moduleName = $this->getParameters()['option']['module_name'];

        if (Module::isInstalled($moduleName)) {
            $module = Module::getInstanceByName($moduleName);
            if (Validate::isLoadedObject($module) && $module->active) {
                switch ($moduleName) {
                    //version: 7.3.4 - Author: zIO_o
                    case 'cashondeliverywithfee':
                        if (method_exists($module, 'getFees')) {
                            $fee = $module->getFees();

                            if (is_array($fee) && isset($fee['data'], $fee['data']['fee'])) {
                                return (float) $fee['data']['fee'];
                            }
                        }
                        break;
                    //version: 1.1.4 - Author: Sakis Gkiokas
                    case 'codwfeeplus':
                        if (method_exists($module, 'getCost')) {
                            $fee = $module->getCost(array('cart' => $this->contextProvider->getCart()));

                            if (!empty($fee)) {
                                return (float) $fee;
                            }
                        }
                        break;
                    case 'codfee':
                        //version: 3.3.4 - Author: idnovate
                        if (method_exists($module, 'getFeeCost')
                            && $module->author == 'idnovate'
                            && version_compare($module->version, '3.3.4', '>=')
                        ) {
                            include_once _PS_MODULE_DIR_ . 'codfee/classes/CodfeeConfiguration.php';

                            $cart = $this->contextProvider->getCart();

                            $id_lang = $cart->id_lang;
                            $id_shop = $cart->id_shop;
                            $customer = new \Customer((int) $cart->id_customer);
                            $customer_groups = $customer->getGroupsStatic((int) $customer->id);
                            $carrier = new \Carrier((int) $cart->id_carrier);
                            $address = new \Address((int) $cart->id_address_delivery);
                            $country = new \Country((int) $address->id_country);
                            if ($address->id_state > 0) {
                                $zone = \State::getIdZone((int) $address->id_state);
                            } else {
                                $zone = $country->getIdZone((int) $country->id);
                            }
                            $manufacturers = '';
                            $suppliers = '';
                            $products = $cart->getProducts();
                            foreach ($products as $product) {
                                $manufacturers .= $product['id_manufacturer'] . ';';
                                $suppliers .= $product['id_supplier'] . ';';
                            }
                            $manufacturers = explode(';', trim($manufacturers, ';'));
                            $manufacturers = array_unique($manufacturers, SORT_REGULAR);
                            $suppliers = explode(';', trim($suppliers, ';'));
                            $suppliers = array_unique($suppliers, SORT_REGULAR);
                            $group = new \Group((int) $customer->id_default_group);
                            if ($group->price_display_method == '1') {
                                $price_display_method = false;
                            } else {
                                $price_display_method = true;
                            }
                            $order_total = $cart->getOrderTotal($price_display_method, 3);

                            $codfeeconf = new \CodfeeConfiguration();
                            $codfeeconf = $codfeeconf->getFeeConfiguration(
                                $id_shop,
                                $id_lang,
                                $customer_groups,
                                $carrier->id_reference,
                                $country,
                                $zone,
                                $products,
                                $manufacturers,
                                $suppliers,
                                $order_total
                            );

                            if ($codfeeconf) {
                                $fee = (float) \Tools::ps_round((float) $module->getFeeCost(
                                    $cart,
                                    $codfeeconf,
                                    $price_display_method
                                ), 2);
                                if ($codfeeconf['free_on_freeshipping'] == '1'
                                    && $cart->getOrderTotal($price_display_method, \Cart::ONLY_SHIPPING) == 0
                                ) {
                                    $fee = (float) 0.00;
                                }
                                if ($codfeeconf['free_on_freeshipping'] == '1'
                                    && count($cart->getCartRules(\CartRule::FILTER_ACTION_SHIPPING)) > 0
                                ) {
                                    $fee = (float) 0.00;
                                }

                                return $fee;
                            }
                        }

                        break;
                    //version: 1.5.9 - Author: PrestaHost.eu
                    case 'cashondeliveryplus':
                        if ($module->author == 'PrestaHost.eu') {
                            $fee = Configuration::get('COD_FEE');
                            $feefree = Configuration::get('COD_FEEFREE');
                            $total = $this->contextProvider->getCart()->getOrderTotal(false, Cart::ONLY_PRODUCTS);

                            if ($feefree > 0 && $total > $feefree) {
                                $fee = 0;
                            }

                            return $fee;
                        }

                        break;
                    //version: 4.0.11 - Author: 4webs.es
                    case 'paypalwithfee':
                        if ($module->author == '4webs.es') {
                            if (method_exists($module, 'getFee')) {
                                $fee = $module->getFee($this->contextProvider->getCart());

                                if (is_array($fee)) {
                                    $fee = (float) $fee['fee_with_tax'];
                                }

                                return $fee;
                            }
                        }

                        break;
                    //version: 17.1.17 - Author: AlabazWeb Pro
                    case 'megareembolso':
                        if ($module->author == 'AlabazWeb Pro') {
                            if (method_exists($module, 'getCost')) {
                                $fee = $module->getCost($this->contextProvider->getCart());

                                return $fee;
                            }
                        }

                        break;
                    default:
                        //ets_payment_with_fee - v2.2.9 - ETS-Soft.
                        if (Module::isInstalled('ets_payment_with_fee')) {
                            $etsPaymentWithFee = Module::getInstanceByName('ets_payment_with_fee');
                            if (Validate::isLoadedObject($etsPaymentWithFee) && $etsPaymentWithFee->active) {
                                $sql = new DbQuery();
                                $sql->from('ets_paymentmethod_module');
                                $sql->where('id_module = ' . (int) $module->id);

                                $existPaymentModuleFee = Db::getInstance()->getRow($sql);
                                if ($existPaymentModuleFee) {
                                    $totalCartOnly = (float) $this->contextProvider->getCart()->getOrderTotal(true, Cart::ONLY_PRODUCTS, null, null, false, false, false, true);
                                    $minOrder = (float) $existPaymentModuleFee['minimum_order'];
                                    $maxOrder = (float) $existPaymentModuleFee['maximum_order'];

                                    if ($totalCartOnly >= $minOrder && (empty($maxOrder) || ($maxOrder > 0 && $maxOrder >= $totalCartOnly))) {
                                        $feeWithTax = $etsPaymentWithFee->getFeePaymentModule($moduleName, null, true);

                                        return $feeWithTax;
                                    }
                                } elseif ($moduleName == 'ets_payment_with_fee') {
                                    if (isset($this->getParameters()['option']['inputs'])) {
                                        $feeWithTax = $etsPaymentWithFee->getFeePayment(
                                            $this->getParameters()['option']['inputs'][0]['value'],
                                            null,
                                            true
                                        );

                                        return $feeWithTax;
                                    }
                                }
                            }
                        }

                        break;
                }
            }
        }

        return false;
    }

    public function validate()
    {
        if (!isset($this->getParameters()['option'])) {
            throw new HookException(
                'The parameters sent are not valid.',
                HookException::HOOK_PARAM_SENT_INVALID
            );
        }
    }

    protected function executeRun()
    {
        $this->validate();

        $fee = $this->getFeeByModule($this->getParameters()['option']['module_name']);
        if ($fee) {
            $total = $this->contextProvider->getCart()->getOrderTotal();

            $priceFormatter = new PriceFormatter();

            return array(
                'feeLabel' => $this->module->getMessageList()['labelFee'],
                'feeAmount' => $fee,
                'feeValue' => $priceFormatter->format($fee),
                'feeTotalAmount' => $fee + $total,
                'feeTotalValue' => $priceFormatter->format($fee + $total),
            );
        }

        return array();
    }
}
