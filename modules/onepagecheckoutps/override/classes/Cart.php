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

class Cart extends CartCore
{
    public function getTotalShippingCost($delivery_option = null, $use_tax = true, Country $default_country = null)
    {
        if (version_compare(_PS_VERSION_, '1.7.4.0') < 0) {
            static $_total_shipping;

            $opc = Module::getInstanceByName('onepagecheckoutps');

            if (Validate::isLoadedObject($opc)
                && Context::getContext()->customer->isLogged()
                && (int) Context::getContext()->customer->id === (int) $opc->config_vars['OPC_ID_CUSTOMER']
            ) {
                $_total_shipping = null;
            }
            //-----------------------------------------------------------------------------------

            if (null === $_total_shipping) {
                if (isset(Context::getContext()->cookie->id_country)) {
                    $default_country = new Country(Context::getContext()->cookie->id_country);
                }
                if (is_null($delivery_option)) {
                    $delivery_option = $this->getDeliveryOption($default_country, false, false);
                }

                $_total_shipping = array(
                    'with_tax' => 0,
                    'without_tax' => 0,
                );
                $delivery_option_list = $this->getDeliveryOptionList($default_country);
                foreach ($delivery_option as $id_address => $key) {
                    if (!isset($delivery_option_list[$id_address])
                        || !isset($delivery_option_list[$id_address][$key])
                    ) {
                        continue;
                    }

                    $_total_shipping['with_tax'] += $delivery_option_list[$id_address][$key]['total_price_with_tax'];
                    $_total_shipping['without_tax'] += $delivery_option_list[$id_address][$key]['total_price_without_tax'];
                }
            }

            return ($use_tax) ? $_total_shipping['with_tax'] : $_total_shipping['without_tax'];
        }

        return parent::getTotalShippingCost($delivery_option, $use_tax, $default_country);
    }

    public function getDeliveryOptionList(Country $default_country = null, $flush = false)
    {
        if (version_compare(_PS_VERSION_, '1.7.4.0') < 0) {
            $opc = Module::getInstanceByName('onepagecheckoutps');
            if (Validate::isLoadedObject($opc)
                && Context::getContext()->customer->isLogged()
                && (int) Context::getContext()->customer->id === (int) $opc->config_vars['OPC_ID_CUSTOMER']
            ) {
                $flush = true;
            }
        }

        return parent::getDeliveryOptionList($default_country, $flush);
    }
}
