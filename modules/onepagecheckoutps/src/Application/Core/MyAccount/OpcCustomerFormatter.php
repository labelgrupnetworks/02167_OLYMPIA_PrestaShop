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

namespace OnePageCheckoutPS\Application\Core\MyAccount;

use Configuration;
use Customer;
use CustomerFormatter;
use FormField;
use FormFormatterInterface;
use Gender;
use OnePageCheckoutPS;
use OnePageCheckoutPS\Application\Core\Form\OpcFormField;
use OnePageCheckoutPS\Entity\OpcField;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Birthday;
use Tools;

class OpcCustomerFormatter implements FormFormatterInterface
{
    private $module;
    private $definition;

    private $isRequestConfirmationEmail = true;
    private $isRequestPassword = true;

    private $formatterPs;

    public function __construct(OnePageCheckoutPS $module)
    {
        $this->module = $module;

        $this->definition = Customer::$definition['fields'];

        $this->formatterPs = new CustomerFormatter(
            $this->module->getContextProvider()->getContextLegacy()->getTranslator(),
            $this->module->getContextProvider()->getLanguage()
        );
    }

    public function setRequestConfirmationEmail($isRequestConfirmationEmail)
    {
        //No vemos necesario el requerir confirmar el email en el nuevo OPC.
        //$this->isRequestConfirmationEmail = (bool) $isRequestConfirmationEmail;
        $isRequestConfirmationEmail = $isRequestConfirmationEmail;
        $this->isRequestConfirmationEmail = false;

        return $this;
    }

    public function isRequestConfirmationEmail()
    {
        return $this->isRequestConfirmationEmail;
    }

    public function setRequestPassword($isRequestPassword)
    {
        $this->isRequestPassword = (bool) $isRequestPassword;

        return $this;
    }

    public function isRequestPassword()
    {
        return $this->isRequestPassword;
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

        $opcFieldList = $fieldRepository->findByObject(OpcField::GROUP_CUSTOMER);
        if ($opcFieldList) {
            foreach ($opcFieldList as $opcField) {
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

                if ($opcField->getName() === 'newsletter') {
                    continue;
                }

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
                $formField->addAvailableValue('defaultValue', $opcFieldShop->getDefaultValue());
                $formField->addAvailableValue('column', $opcFieldShop->getCol());
                $formField->addAvailableValue('row', $opcFieldShop->getRow());
                $formField->addAvailableValue('active', $opcFieldShop->getActive());

                if (!empty($opcFieldLang->getLabel())) {
                    $formField->addAvailableValue('comment', $opcFieldLang->getLabel());
                }

                if (!$opcFieldOptions->isEmpty()) {
                    foreach ($opcFieldOptions as $opcFieldoption) {
                        $opcFieldOptionLang = $opcFieldoption->getLangById($langId);

                        $formField->addAvailableValue(
                            $opcFieldoption->getValue(),
                            $opcFieldOptionLang->getDescription()
                        );
                    }
                }

                if ($formField->getName() === 'id_gender') {
                    $genders = Gender::getGenders($langId);
                    if ($genders->count() > 0) {
                        foreach ($genders as $gender) {
                            $formField->addAvailableValue($gender->id, $gender->name);
                        }
                    }
                }

                //TEMPORAL, MIENTRAS SE HACE LA VALIDACION DE CUMPLEANOS
                if ($formField->getName() === 'birthday') {
                    $formField->addAvailableValue('validation', 'isDate');
                }

                if ($formField->getName() === 'passwd') {
                    $formField->setType('password');
                    $formField->setName('password');
                    $formField->setRequired($this->isRequestPassword);
                    $formField->addAvailableValue('defaultValue', Tools::passwdGen());
                    $formField->addAvailableValue('active', $this->isRequestPassword);
                }

                if ($formField->getName() === 'email') {
                    $formField->setType('email');
                    $formField->addAvailableValue('defaultValue', date('His') . '@auto-generated.opc');
                }

                if ($formField->getName() === 'birthday') {
                    $formField->addAvailableValue('defaultValue', Birthday::createEmpty()->getValue());
                    $formField->addAvailableValue(
                        'placeholder',
                        $this->module->getMessageList()['example'] . ': ' . Tools::formatDateStr('31 May 1970')
                    );
                }

                if ($formField->getName() === 'id') {
                    $formField->setType('hidden');
                }

                if ($formField->getType() === 'radio') {
                    $formField->setType('radio-buttons');
                } elseif ($formField->getType() === 'textbox') {
                    $formField->setType('text');
                }

                $confField = null;
                if (($formField->getName() === 'email'
                        && $this->isRequestConfirmationEmail
                    ) || ($formField->getName() === 'password'
                        && $this->isRequestPassword
                        && !$this->module->getContextProvider()->getCustomer()->isLogged()
                )) {
                    $confField = new OpcFormField();
                    $confField->setName($formField->getName());
                    $confField->setType($formField->getType());
                    $confField->setMaxLength($formField->getMaxLength());
                    $confField->setLabel($formField->getLabel());
                    $confField->setRequired($formField->isRequired());
                    $formField->addAvailableValue('id', $opcField->getId());
                    $confField->addAvailableValue('object', $opcField->getObject());
                    $confField->addAvailableValue('validation', $fieldType);
                    $confField->addAvailableValue('capitalize', $opcField->getCapitalize());
                    $formField->addAvailableValue('custom', $opcField->getIsCustom());
                    $confField->addAvailableValue('defaultValue', $opcFieldShop->getDefaultValue());
                    $confField->addAvailableValue('column', $opcFieldShop->getCol() + 1);
                    $confField->addAvailableValue('row', $opcFieldShop->getRow());
                    $confField->addAvailableValue('active', $opcFieldShop->getActive());

                    $formField->setName($formField->getName() . '_confirmation');
                }

                $format[$formField->getName()] = $formField;

                if (!is_null($confField)) {
                    $format[$confField->getName()] = $confField;
                }

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
            'custom',
        );
    }
}
