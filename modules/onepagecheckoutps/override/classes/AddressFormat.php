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

class AddressFormat extends AddressFormatCore
{
    public static function generateAddress(
        Address $address,
        $patternRules = array(),
        $newLine = "\r\n",
        $separator = ' ',
        $style = array()
    ) {
        $addressFields = AddressFormat::getOrderedAddressFields($address->id_country);

        $opc = Module::getInstanceByName('onepagecheckoutps');
        if (Validate::isLoadedObject($opc) && $opc->core->isModuleActive($opc->name)) {
            if ((int) $address->id > 0) {
                $custom_fields = FieldCustomerClass::getData(null, $address->id_customer, null, $address->id);

                if ($custom_fields) {
                    foreach ($custom_fields as &$custom_field) {
                        $custom_field['name'] = str_replace('-', '_', $custom_field['name']);
                        $addressFields[] = $custom_field['name'];
                        if ($custom_field['type'] === 'isDate') {
                            $custom_field['value'] = ($custom_field['value'] === '0000-00-00') ? null : Tools::displayDate($custom_field['value']);
                        }
                        $address->{$custom_field['name']} = $custom_field['value'];
                    }
                }
            }
        }

        $addressFormatedValues = AddressFormat::getFormattedAddressFieldsValues($address, $addressFields);

        $addressText = '';
        foreach ($addressFields as $line) {
            if (($patternsList = preg_split(self::_CLEANING_REGEX_, $line, -1, PREG_SPLIT_NO_EMPTY))) {
                $tmpText = '';
                foreach ($patternsList as $pattern) {
                    if ((!array_key_exists('avoid', $patternRules))
                        || (
                            is_array($patternRules)
                            && array_key_exists('avoid', $patternRules)
                            && !in_array($pattern, $patternRules['avoid'])
                    )) {
                        $tmpText .= (isset($addressFormatedValues[$pattern]) && !empty($addressFormatedValues[$pattern])) ?
                                (((isset($style[$pattern])) ?
                                    (sprintf($style[$pattern], $addressFormatedValues[$pattern])) :
                                    $addressFormatedValues[$pattern]) . $separator) : '';
                    }
                }
                $tmpText = trim($tmpText);
                $addressText .= (!empty($tmpText)) ? $tmpText . $newLine : '';
            }
        }

        $addressText = preg_replace('/' . preg_quote($newLine, '/') . '$/i', '', $addressText);
        $addressText = rtrim($addressText, $separator);

        return $addressText;
    }
}
