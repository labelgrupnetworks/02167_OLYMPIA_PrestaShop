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

namespace OnePageCheckoutPS\Application\Core\Addresses;

use Address;
use Configuration;
use Country;
use CustomerAddressFormatter;
use FormField;
use FormFormatterInterface;
use OnePageCheckoutPS;
use OnePageCheckoutPS\Application\Core\Form\OpcFormField;
use OnePageCheckoutPS\Entity\OpcField;
use State;
use Tools;

class OpcCustomerAddressFormatter implements FormFormatterInterface
{
    private $module;
    private $country;
    private $availableCountries;
    private $formatterPs;

    private $definition;
    private $typeAddress = 'delivery';

    public function __construct(
        OnePageCheckoutPS $module,
        Country $country,
        array $availableCountries
    ) {
        $this->module = $module;
        $this->country = $country;
        $this->availableCountries = $availableCountries;

        $this->definition = Address::$definition['fields'];

        $this->formatterPs = new CustomerAddressFormatter(
            $this->country,
            $this->module->getContextProvider()->getContextLegacy()->getTranslator(),
            $this->availableCountries
        );
    }

    public function setTypeAddress(string $typeAddress)
    {
        $this->typeAddress = $typeAddress;

        return $this;
    }

    public function getTypeAddress()
    {
        return $this->typeAddress;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry(Country $country)
    {
        $this->country = $country;

        return $this;
    }

    public function getAvailableCountries()
    {
        return $this->availableCountries;
    }

    public function getDefaultValueField($name, $validation = '')
    {
        if (in_array($name, array('dni', 'phone', 'phone_mobile'))) {
            return '00';
        } elseif (in_array($name, array('firstname', 'lastname', 'address1', 'city'))) {
            return '.';
        } elseif ($name === 'alias') {
            return $this->module->getMessageList()['myAddress'];
        } elseif ($name === 'id_country') {
            return (int) $this->country->id;
        } elseif ($name === 'id_state') {
            $shopId = $this->module->getContextProvider()->getShopId();
            $fieldRepository = $this->module->getEntityManager()->getRepository(OpcField::class);
            $opcField = $fieldRepository->getDefaultValueByFieldName($shopId, $this->typeAddress, 'id_state');
            if (!empty($opcField['defaultValue'])) {
                return (int) $opcField['defaultValue'];
            }
        } elseif ($name === 'postcode') {
            $zipCodeFormatted = $this->country->zip_code_format;
            if (!empty($zipCodeFormatted)) {
                $zipCodeFormatted = str_replace('N', '0', $zipCodeFormatted);
                $zipCodeFormatted = str_replace('L', 'A', $zipCodeFormatted);
                $zipCodeFormatted = str_replace('C', $this->country->iso_code, $zipCodeFormatted);
            }

            return $zipCodeFormatted;
        }

        if ($validation === 'isDate') {
            return Tools::formatDateStr('31 May 1970');
        } elseif ($validation === 'isInt') {
            return 00;
        } elseif ($validation === 'isGenericName') {
            return '.';
        }

        return '';
    }

    private function convertToOpcFormField(FormField $formField)
    {
        $opcFormField = new OpcFormField();
        $opcFormField->setName($formField->getName());
        $opcFormField->setType($formField->getType());
        $opcFormField->setMaxLength($formField->getMaxLength());
        $opcFormField->setLabel($formField->getLabel());
        $opcFormField->setRequired($formField->isRequired());
        $opcFormField->setAvailableValues($formField->getAvailableValues());

        return $opcFormField;
    }

    public function getFormat()
    {
        $format = array();
        $formatPs = $this->formatterPs->getFormat();

        $langId = $this->module->getContextProvider()->getLanguageId();
        $shopId = $this->module->getContextProvider()->getShopId();
        $fieldRepository = $this->module->getEntityManager()->getRepository(OpcField::class);

        $row = 0;

        //token customer
        $tokenField = new OpcFormField();
        $tokenField->setName('token');
        $tokenField->setType('hidden');
        $tokenField->addAvailableValue('object', $this->typeAddress);
        $tokenField->addAvailableValue('validation', '');
        $tokenField->addAvailableValue('capitalize', false);
        $tokenField->addAvailableValue('defaultValue', '');
        $tokenField->addAvailableValue('column', 0);
        $tokenField->addAvailableValue('row', -1);
        $tokenField->addAvailableValue('active', true);
        $format[$tokenField->getName()] = $tokenField;

        $opcFieldList = $fieldRepository->findByObject($this->typeAddress);
        if ($opcFieldList) {
            foreach ($opcFieldList as $opcField) {
                /*if ($opcField->getIsCustom() && $opcField->getObject() === 'invoice') {
                    continue;
                }*/

                $opcFieldLang = $opcField->getLangById($langId);
                //Si no existe la traduccion, ponemos la del idioma por defecto que si debe existir.
                if (!$opcFieldLang) {
                    $opcFieldLang = $opcField->getLangById((int) Configuration::get('PS_LANG_DEFAULT'));
                }

                $opcFieldShop = $opcField->getShopById($shopId);
                //En el caso que un campo no exista para una tienda, se omite.
                if (!$opcFieldShop) {
                    continue;
                }

                $opcFieldOptions = $opcField->getFieldOptions();

                $fieldType = $opcField->getType();
                if ($fieldType === 'number') {
                    $fieldType = 'isInt';
                }
                if ($fieldType === 'url') {
                    $fieldType = 'isUrl';
                }

                if (isset($formatPs[$opcField->getName()])) {
                    $formField = $this->convertToOpcFormField($formatPs[$opcField->getName()]);
                } else {
                    $formField = new OpcFormField();
                }
                $formField->setName($opcField->getName());
                $formField->setType($opcField->getTypeControl());
                $formField->setMaxLength($opcField->getSize());
                $formField->setLabel($opcFieldLang->getDescription());
                $formField->setRequired($opcFieldShop->getRequired());
                $formField->addAvailableValue('id', $opcField->getId());
                $formField->addAvailableValue('object', $opcField->getObject());
                $formField->addAvailableValue('validation', $fieldType);
                $formField->addAvailableValue('capitalize', $opcField->getCapitalize());
                $formField->addAvailableValue('custom', $opcField->getIsCustom());
                $formField->addAvailableValue('column', $opcFieldShop->getCol());
                $formField->addAvailableValue('row', $opcFieldShop->getRow());
                $formField->addAvailableValue('active', $opcFieldShop->getActive());

                $defaultValue = $this->getDefaultValueField(
                    $formField->getName(),
                    $formField->getType()
                );
                $formField->addAvailableValue('defaultValue', $defaultValue);

                if (!empty($opcFieldLang->getLabel())) {
                    $formField->addAvailableValue('comment', $opcFieldLang->getLabel());
                }

                if (!$opcFieldOptions->isEmpty()) {
                    foreach ($opcFieldOptions as $opcFieldoption) {
                        if ($opcFieldoption->getObject() === $this->typeAddress) {
                            $opcFieldOptionLang = $opcFieldoption->getLangById($langId);

                            $formField->addAvailableValue(
                                $opcFieldoption->getValue(),
                                $opcFieldOptionLang->getDescription()
                            );
                        }
                    }
                }

                if ($formField->getType() === 'radio') {
                    $formField->setType('radio-buttons');
                } elseif ($formField->getType() === 'textbox') {
                    $formField->setType('text');
                }

                if ($formField->getName() === 'id') {
                    $formField->setType('hidden');
                } elseif ($formField->getName() === 'postcode') {
                    $zipCodeFormatted = $this->country->zip_code_format;
                    if (!empty($zipCodeFormatted)) {
                        $zipCodeFormatted = str_replace('N', '0', $zipCodeFormatted);
                        $zipCodeFormatted = str_replace('L', 'A', $zipCodeFormatted);
                        $zipCodeFormatted = str_replace('C', $this->country->iso_code, $zipCodeFormatted);
                    }

                    $formField->addAvailableValue('placeholder', $zipCodeFormatted);
                    $formField->addAvailableValue('format', $this->country->zip_code_format);
                    $formField->addAvailableValue('countryIsoCode', $this->country->iso_code);

                    if ($this->country->need_zip_code) {
                        $formField->setRequired(true);
                    } else {
                        $formField->setRequired(false);
                    }
                } elseif ($formField->getName() === 'dni') {
                    if ($this->country->need_identification_number) {
                        $formField->setRequired(true);
                    } else {
                        $formField->setRequired(false);
                    }

                    if ($this->module->getConfigurationList('OPC_VALIDATE_UNIQUE_DNI')) {
                        $formField->addAvailableValue('validation', 'isUniqueDni,isDniLite');
                    }
                } elseif ($formField->getName() === 'id_country') {
                    $formField->setType('country');
                    $formField->addAvailableValue('availableCountries', $this->availableCountries);
                } elseif ($formField->getName() === 'id_state') {
                    $formField->setType('state');
                    if ((bool) $this->country->contains_states) {
                        $states = State::getStatesByIdCountry($this->country->id, true);
                        $formField->addAvailableValue('availableStates', $states);
                        $formField->setRequired(true);
                    } else {
                        $formField->setRequired(false);
                    }
                } elseif ($formField->getName() === 'vat_number') {
                    //$formField->addAvailableValue('validation', 'isVatNumber');
                }

                $format[$formField->getName()] = $formField;

                ++$row;
            }
        }

        $row = 50;

        foreach ($formatPs as $fieldName => $formField) {
            if (in_array($fieldName, array('back', 'id_customer', 'id_address', 'token'))) {
                continue;
            }

            if (!isset($format[$fieldName])) {
                $opcFormField = $this->convertToOpcFormField($formField);

                $availableValueList = $formField->getAvailableValues();

                $opcFormField = new OpcFormField();
                $opcFormField->setName($formField->getName());
                $opcFormField->setType($formField->getType());
                $opcFormField->setRequired($formField->isRequired());
                $opcFormField->setLabel($formField->getLabel());
                $opcFormField->setValue($formField->getValue());
                $opcFormField->setMaxLength($formField->getMaxLength());
                $opcFormField->setAvailableValues($availableValueList);

                if (!array_key_exists('id', $availableValueList)) {
                    $opcFormField->addAvailableValue('id', 0);
                }
                if (!array_key_exists('object', $availableValueList)) {
                    $opcFormField->addAvailableValue('object', 'custom');
                }
                if (!array_key_exists('validation', $availableValueList)) {
                    $opcFormField->addAvailableValue('validation', '');
                }
                if (!array_key_exists('capitalize', $availableValueList)) {
                    $opcFormField->addAvailableValue('capitalize', false);
                }
                if (!array_key_exists('custom', $availableValueList)) {
                    $opcFormField->addAvailableValue('custom', false);
                }
                if (!array_key_exists('defaultValue', $availableValueList)) {
                    $opcFormField->addAvailableValue('defaultValue', '');
                }
                if (!array_key_exists('column', $availableValueList)) {
                    $opcFormField->addAvailableValue('column', 0);
                }
                if (!array_key_exists('row', $availableValueList)) {
                    $opcFormField->addAvailableValue('row', $row);
                }
                if (!array_key_exists('active', $availableValueList)) {
                    $opcFormField->addAvailableValue('active', true);
                }

                $format[$opcFormField->getName()] = $opcFormField;

                unset($opcFormField);

                ++$row;
            }
        }

        return $this->addConstraints($format);
    }

    private function addConstraints(array $format)
    {
        foreach ($format as $field) {
            if (!empty($this->definition[$field->getName()]['validate'])) {
                $field->addConstraint(
                    $this->definition[$field->getName()]['validate']
                );
            } elseif (array_key_exists('validation', $field->getAvailableValues())
                && array_key_exists('custom', $field->getAvailableValues())
            ) {
                if (!empty($field->getAvailableValues()['validation'])
                    && $field->getAvailableValues()['custom'] === true
                ) {
                    $field->addConstraint(
                        $field->getAvailableValues()['validation']
                    );
                }
            }
        }

        return $format;
    }

    public function getPrivateAvailableKeys()
    {
        return array(
            'id',
            'object',
            'validation',
            'capitalize',
            'defaultValue',
            'column',
            'row',
            'active',
            'format',
            'countryIsoCode',
            'custom',
        );
    }
}
