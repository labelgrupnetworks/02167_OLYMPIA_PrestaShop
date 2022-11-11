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

namespace OnePageCheckoutPS\Application\Core\Form;

use AbstractForm;
use Address;
use Customer;
use CustomerAddressPersister;
use Hook;
use Module;
use OnePageCheckoutPS;
use OnePageCheckoutPS\Application\Core\Addresses\OpcCustomerAddressFormatter;
use OnePageCheckoutPS\Entity\OpcCustomerAddress;
use OnePageCheckoutPS\Entity\OpcField;
use OnePageCheckoutPS\Entity\OpcFieldCustomer;
use OnePageCheckoutPS\Exception\AddressesException;
use State;
use Tools;
use Validate;

class OpcCustomerAddressForm extends AbstractForm
{
    protected $template = 'module:onepagecheckoutps/views/templates/front/checkout/_partials/form.tpl';

    private $contextProvider;
    private $module;
    private $customer;
    private $address;
    private $persister;
    protected $formatter;

    private $fieldListRequired = array(
        'firstname',
        'lastname',
        'address1',
        'id_country',
        'city',
        'alias',
    );

    public function __construct(
        OnePageCheckoutPS $module,
        OpcCustomerAddressFormatter $formatter,
        CustomerAddressPersister $persister,
        Customer $customer
    ) {
        parent::__construct(
            $module->getContextProvider()->getSmarty(),
            $module->getTranslator(),
            $formatter
        );

        $this->contextProvider = $module->getContextProvider();
        $this->formatter = $formatter;
        $this->persister = $persister;
        $this->customer = $customer;
        $this->module = $module;
    }

    public function getAddress()
    {
        if (Validate::isLoadedObject($this->address)) {
            return $this->address;
        }

        return false;
    }

    protected function setAddress(Address $address)
    {
        $this->address = $address;
    }

    protected function getPersister()
    {
        return $this->persister;
    }

    public function loadAddressById(int $addressId)
    {
        $this->address = new Address($addressId, $this->contextProvider->getLanguageId());

        $params = get_object_vars($this->address);

        $opcFieldCustomerList = $this->getCustomFieldList($addressId);

        return $this->fillWith(
            array_merge($params, $opcFieldCustomerList)
        );
    }

    public function fillWith(array $params = array())
    {
        if (isset($params['id_country'])
            && $params['id_country'] != $this->formatter->getCountry()->id
        ) {
            $params['id_country'] = $this->formatter->getCountry()->id;
        }

        $newFields = $this->formatter->getFormat();
        foreach ($newFields as $field) {
            if (array_key_exists($field->getName(), $this->formFields)) {
                $field->setValue($this->formFields[$field->getName()]->getValue());
            }
            if (array_key_exists($field->getName(), $params)) {
                $valueParam = $params[$field->getName()];
                if (array_key_exists('validation', $field->getAvailableValues())) {
                    if ($field->getAvailableValues()['validation'] === 'number') {
                        settype($valueParam, 'int');
                    }
                }
                $field->setValue($valueParam);
            } elseif ($field->getType() === 'checkbox') {
                $field->setValue(false);
            }
        }

        $this->formFields = $newFields;

        return $this;
    }

    public function validateCustomer()
    {
        if (!$this->customer->isLogged() && !$this->customer->isGuest()) {
            throw new AddressesException(
                'It is necessary to be logged in as a customer or have an active guest session to save the address.',
                AddressesException::ADDRESS_NEED_CUSTOMER_GUEST_SESSION
            );
        }
    }

    private function charCodeAt($str, $index)
    {
        $utf16 = mb_convert_encoding($str, 'UTF-16LE', 'UTF-8');

        return ord($utf16[$index * 2]) + (ord($utf16[$index * 2 + 1]) << 8);
    }

    public function validateUniqueDni()
    {
        $dni = $this->getField('dni')->getValue();

        if (!empty($dni) && $this->module->getConfigurationList('OPC_VALIDATE_UNIQUE_DNI')) {
            $existDni = (bool) $this->module->getService('onepagecheckoutps.repository.address')->findDniAddressUsed(
                $this->customer->id,
                $dni
            );

            if ($existDni) {
                throw new AddressesException(
                    'Non-unique DNI',
                    AddressesException::NON_UNIQUE_DNI
                );
            }
        }
    }

    public function validateDni()
    {
        $dniField = $this->getField('dni');

        if ($dniField->getAvailableValues()['active'] === true) {
            $dni = $dniField->getValue();

            if (!empty($dni)) {
                $isValidDni = false;

                if ($this->module->getConfigurationList('OPC_VALIDATE_DNI')) {
                    $country = $this->formatter->getCountry();
                    switch ($country->iso_code) {
                        case 'ES':
                            require_once _PS_MODULE_DIR_ . $this->module->name . '/lib/nif-nie-cif.php';
                            $isValidDni = isValidIdNumber($dni);
                            break;
                        case 'CL':
                            $dni = preg_replace('/[^k0-9]/i', '', $dni);
                            $dv = Tools::substr($dni, -1);
                            $numero = Tools::substr($dni, 0, Tools::strlen($dni) - 1);
                            $i = 2;
                            $suma = 0;
                            foreach (array_reverse(str_split($numero)) as $v) {
                                if ($i == 8) {
                                    $i = 2;
                                }
                                $suma += $v * $i;
                                ++$i;
                            }
                            $dvr = 11 - ($suma % 11);
                            if ($dvr == 11) {
                                $dvr = 0;
                            }
                            if ($dvr == 10) {
                                $dvr = 'K';
                            }
                            if ($dvr == Tools::strtoupper($dv)) {
                                $isValidDni = true;
                            } else {
                                $isValidDni = false;
                            }

                            break;
                        case 'IT':
                            $dni = Tools::strtoupper($dni);

                            if (preg_match('/^[IT]{2}[0-9]{11}$/', $dni) || preg_match('/^[0-9]{11}$/', $dni)) {
                                return true;
                            }

                            if (!preg_match('/^[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]$/', $dni)) {
                                return false;
                            }

                            $set1 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                            $set2 = 'ABCDEFGHIJABCDEFGHIJKLMNOPQRSTUVWXYZ';
                            $setpari = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                            $setdisp = 'BAKPLCQDREVOSFTGUHMINJWZYX';

                            $s = 0;
                            for ($i = 1; $i <= 13; $i += 2) {
                                $s += strpos(
                                    $setpari,
                                    Tools::substr(
                                        $set2,
                                        strpos(
                                            $set1,
                                            Tools::substr(
                                                $dni,
                                                $i,
                                                1
                                            )
                                        ),
                                        1
                                    )
                                );
                            }

                            for ($i = 0; $i <= 14; $i += 2) {
                                $s += strpos(
                                    $setdisp,
                                    Tools::substr(
                                        $set2,
                                        strpos(
                                            $set1,
                                            Tools::substr(
                                                $dni,
                                                $i,
                                                1
                                            )
                                        ),
                                        1
                                    )
                                );
                            }

                            if ($s % 26 != $this->charCodeAt($dni, 15) - $this->charCodeAt('A', 0)) {
                                $isValidDni = false;
                            }

                            $isValidDni = true;
                            break;
                        default:
                            $isValidDni = Validate::isDniLite($dni);
                            break;
                    }
                } else {
                    $isValidDni = Validate::isDniLite($dni);
                }

                if (!$isValidDni) {
                    throw new AddressesException(
                        'Invalid DNI',
                        AddressesException::ADDRESS_DNI_INVALID
                    );
                }
            }
        }
    }

    public function validatePostCode()
    {
        if ($postcode = $this->getField('postcode')) {
            $country = $this->formatter->getCountry();
            if ($country->need_zip_code) {
                if (!$country->checkZipCode($postcode->getValue())) {
                    if ($this->customer->isLogged()) {
                        throw new AddressesException(
                            sprintf(
                                'Invalid postcode - should look like "%s"',
                                str_replace(
                                    'C',
                                    $country->iso_code,
                                    str_replace(
                                        'N',
                                        '0',
                                        str_replace(
                                            'L',
                                            'A',
                                            $country->zip_code_format
                                        )
                                    )
                                )
                            ),
                            AddressesException::ADDRESS_POSTCODE_FORMAT_INVALID
                        );
                    } else {
                        $postcode->setValue($postcode->getAvailableValues()['defaultValue']);
                    }
                }
            }
        }
    }

    public function validateState()
    {
        if ($stateField = $this->getField('id_state')) {
            $country = $this->formatter->getCountry();
            if ((bool) $country->contains_states) {
                if (empty($stateField->getValue())) {
                    $stateField->setValue(0);
                } else {
                    $state = new State((int) $stateField->getValue());
                    if (Validate::isLoadedObject($state) && $state->id_country != $country->id) {
                        $stateField->setValue(0);
                    }
                }
            } else {
                $stateField->setValue(0);
            }
        }
    }

    public function validateVatNumber()
    {
        $isVatNumberValid = true;
        $vatNumber = $this->getField('vat_number')->getValue();

        $moduleListSupported = array('vatnumbercleaner', 'validatevatnumber', 'checkvat', 'advancedvatmanager');
        foreach ($moduleListSupported as $moduleName) {
            if (Module::isInstalled($moduleName)) {
                $module = Module::getInstanceByName($moduleName);
                if (Validate::isLoadedObject($module) && $module->active) {
                    if (Hook::isModuleRegisteredOnHook(
                        $module,
                        'actionOpcValidateVatNumber',
                        $this->contextProvider->getShopId()
                    )) {
                        $isVatNumberValid = current(Hook::exec(
                            'actionOpcValidateVatNumber',
                            array('vatNumber' => $vatNumber),
                            $module->id,
                            true
                        ));
                    } else {
                        $isVatNumberValid = current(Hook::exec(
                            'actionOpcValidateVatNumber',
                            array(
                                'vatNumber' => $vatNumber,
                                'module' => $module,
                            ),
                            $this->module->id,
                            true
                        ));
                    }
                }
            }
        }

        if (!$isVatNumberValid) {
            throw new AddressesException(
                'Invalid vat_number',
                AddressesException::ADDRESS_VATNUMBER_INVALID
            );
        }
    }

    public function validate()
    {
        foreach ($this->formFields as $field) {
            if (($field->isRequired()
                || in_array($field->getName(), $this->fieldListRequired))
                && empty($field->getValue())
            ) {
                if (array_key_exists('defaultValue', $field->getAvailableValues())) {
                    $defaultValue = $field->getAvailableValues()['defaultValue'];
                    if ($defaultValue !== 'defaultValue') {
                        $field->setValue($field->getAvailableValues()['defaultValue']);
                    }
                }
            }
        }

        $customerOpcId = (int) $this->module->getConfigurationList('OPC_ID_CUSTOMER');
        if ((int) $this->customer->id !== $customerOpcId) {
            $this->validateCustomer();
            $this->validateUniqueDni();
            $this->validateDni();
            $this->validateVatNumber();
        }

        $this->validatePostCode();
        $this->validateState();

        foreach ($this->formFields as $field) {
            if ($field->getAvailableValues()['validation'] === 'isDate') {
                $field->formatDateField($this->contextProvider->getLanguage());
            }

            if ($field->isRequired() && is_null($field->getValue())) {
                throw new AddressesException(
                    sprintf('The field %s is required.', $field->getLabel()),
                    AddressesException::ADDRESS_FIELD_REQUIRED
                );
            } elseif (!$field->isRequired() && is_null($field->getValue())) {
                continue;
            }

            foreach ($field->getConstraints() as $constraint) {
                if (!Validate::$constraint($field->getValue())) {
                    $field->addError(
                        $this->constraintTranslator->translate($constraint)
                    );
                }
            }
        }

        $is_valid = !$this->hasErrors();

        if ($is_valid) {
            //Solo si hay un cliente logueado o invitado validamos la direccion contra otros modulos,
            //de lo contrario nos da error al cambiar datos de la direccion para los visitantes
            if ($this->customer->isLogged() || $this->customer->isGuest()) {
                $hookReturn = Hook::exec('actionValidateCustomerAddressForm', array('form' => $this));
                if ($hookReturn !== '' && !is_null($hookReturn)) {
                    $is_valid &= (bool) $hookReturn;
                }
            }
        }

        return $is_valid;
    }

    public function getCustomFieldList(int $addressId)
    {
        $customerFieldList = array();

        $opcFieldRepository = $this->module->getEntityManager()->getRepository(OpcField::class);

        $paramListSQL = array(
            'object' => $this->formatter->getTypeAddress(),
            'idCustomer' => $this->customer->id,
            'idAddress' => $addressId,
        );

        $opcFieldCustomerResult = $opcFieldRepository->getCustomFieldByCustomer($paramListSQL);
        if ($opcFieldCustomerResult) {
            foreach ($opcFieldCustomerResult as $opcFieldCustomer) {
                if (!empty($opcFieldCustomer['valueOption'])) {
                    $customerFieldList[$opcFieldCustomer['name']] = $opcFieldCustomer['valueOption'];
                } else {
                    $opcFieldCustomerValue = $opcFieldCustomer['value'];

                    if ($opcFieldCustomer['type'] === 'isDate') {
                        $opcFieldCustomerValue = ($opcFieldCustomerValue === '0000-00-00') ? null : Tools::displayDate($opcFieldCustomerValue);
                    }

                    $customerFieldList[$opcFieldCustomer['name']] = $opcFieldCustomerValue;
                }
            }
        }

        return $customerFieldList;
    }

    public function saveCustomFields(int $addressId)
    {
        foreach ($this->formFields as $field) {
            if (array_key_exists('custom', $field->getAvailableValues())) {
                if ((bool) $field->getAvailableValues()['custom']) {
                    $opcFieldRepository = $this->module->getEntityManager()->getRepository(OpcField::class);
                    $opcField = $opcFieldRepository->findOneById((int) $field->getAvailableValues()['id']);

                    if ($opcField) {
                        $opcFieldCustomer = $opcField->getFieldCustomerBy(
                            $this->formatter->getTypeAddress(),
                            $this->customer->id,
                            $addressId
                        );
                        if (!$opcFieldCustomer) {
                            $opcFieldCustomer = new OpcFieldCustomer();
                            $opcFieldCustomer->setField($opcField);
                            $opcFieldCustomer->setIdCustomer($this->contextProvider->getCustomer()->id);
                            $opcFieldCustomer->setIdAddress($addressId);
                            $opcFieldCustomer->setObject($this->formatter->getTypeAddress());
                        }

                        $opcFieldOptions = $opcField->getFieldOptions();
                        if (!$opcFieldOptions->isEmpty()) {
                            foreach ($opcFieldOptions as $opcFieldoption) {
                                if ($opcFieldoption->getValue() === $field->getValue()) {
                                    $opcFieldCustomer->setIdOption($opcFieldoption->getId());

                                    break;
                                }
                            }
                        } else {
                            $opcFieldCustomer->setValue($field->getValue());
                        }

                        $opcFieldCustomer->setDateUpd();

                        $entityManager = $this->module->getEntityManager();
                        $entityManager->persist($opcFieldCustomer);
                        $entityManager->flush();
                    }
                }
            }
        }
    }

    public function submit()
    {
        if (!$this->validate()) {
            return false;
        }

        $address = new Address(
            $this->getValue('id'),
            $this->contextProvider->getLanguageId()
        );

        foreach ($this->formFields as $formField) {
            $address->{$formField->getName()} = $formField->getValue();
        }

        //Usamos la direccion del OPC que se asocio como visitante y se la asignamos al nuevo cliente
        //de esta manera tambien dejamos de acomular direcciones temporales.
        $addressIdOPC = $this->contextProvider->getCookie()->addressIdOPC;
        $customerOpcId = (int) $this->module->getConfigurationList('OPC_ID_CUSTOMER');
        if ($addressIdOPC && (int) $this->customer->id !== $customerOpcId) {
            $address->id_customer = $this->customer->id;

            //Actualizamos el customer tambien en la tabla opc_customer_address.
            $opcCustomerAddressRepository = $this->module->getEntityManager()->getRepository(OpcCustomerAddress::class);
            $opcSocialNetworkResult = $opcCustomerAddressRepository->findOneByAddressId($address->id);
            if ($opcSocialNetworkResult) {
                $opcSocialNetworkResult->setCustomerId($this->customer->id);
            }
        }

        if ($this->formatter->getTypeAddress() === 'delivery' && $this->module->getConfigurationList('OPC_USE_SAME_NAME_CONTACT_DA')) {
            $address->firstname = $this->customer->firstname;
            $address->lastname = $this->customer->lastname;
        }

        if ($this->formatter->getTypeAddress() === 'invoice' && $this->module->getConfigurationList('OPC_USE_SAME_NAME_CONTACT_BA')) {
            $address->firstname = $this->customer->firstname;
            $address->lastname = $this->customer->lastname;
        }

        Hook::exec('actionSubmitCustomerAddressForm', array('address' => &$address));

        $this->setAddress($address);

        $isSaved = $this->getPersister()->save(
            $address,
            $this->getValue('token')
        );

        if ($isSaved) {
            if ($addressIdOPC && (int) $this->customer->id !== $customerOpcId) {
                unset($this->contextProvider->getCookie()->addressIdOPC);
            }

            $this->saveCustomFields($address->id);

            return (int) $address->id;
        }

        return $isSaved;
    }

    public function getTemplateVariables()
    {
        $this->setValue('token', $this->persister->getToken());

        $formFields = array();
        foreach ($this->formFields as $field) {
            if ((bool) $field->getAvailableValues()['active'] === false) {
                continue;
            }

            if ($field->getType() === 'hidden') {
                $formFields['hidden'][] = $field->toArray();
                continue;
            }

            $row = $field->getAvailableValues()['row'];
            $column = $field->getAvailableValues()['column'];

            $formFields[$row][$column] = $field->toArray();
        }

        ksort($formFields);

        return array(
            'errors' => $this->getErrors(),
            'formFields' => $formFields,
            'isCustomer' => $this->customer->isLogged() && !$this->customer->isGuest(),
            'privateAvailableKeys' => $this->formatter->getPrivateAvailableKeys(),
        );
    }
}
