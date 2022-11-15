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
use Customer;
use CustomerPersister;
use Hook;
use Module;
use OnePageCheckoutPS;
use OnePageCheckoutPS\Application\Core\MyAccount\OpcCustomerFormatter;
use OnePageCheckoutPS\Entity\OpcField;
use OnePageCheckoutPS\Entity\OpcFieldCustomer;
use OnePageCheckoutPS\Exception\MyAccountException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Birthday;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Email;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Password;
use Tools;
use Validate;

class OpcCustomerForm extends AbstractForm
{
    protected $template = 'module:onepagecheckoutps/views/templates/front/checkout/_partials/form.tpl';

    private $contextProvider;
    private $module;
    private $persister;

    private $isGuest = false;

    private $fieldListRequired = array(
        'firstname',
        'lastname',
        'email',
    );

    public function __construct(
        OnePageCheckoutPS $module,
        OpcCustomerFormatter $formatter,
        CustomerPersister $persister
    ) {
        parent::__construct(
            $module->getContextProvider()->getSmarty(),
            $module->getTranslator(),
            $formatter
        );

        $this->contextProvider = $module->getContextProvider();
        $this->module = $module;
        $this->persister = $persister;
    }

    public function setGuest($isGuest)
    {
        $this->isGuest = $isGuest;
    }

    public function fillFromCustomer(Customer $customer)
    {
        $params = get_object_vars($customer);
        $params['birthday'] = $customer->birthday === '0000-00-00' ? null : Tools::displayDate($customer->birthday);

        if ($this->formatter->isRequestConfirmationEmail()) {
            $params['email_confirmation'] = $customer->email;
        }

        $opcFieldCustomerList = $this->getCustomFieldList($customer->id);

        return $this->fillWith(
            array_merge($params, $opcFieldCustomerList)
        );
    }

    public function validateFirstName()
    {
        $field = $this->getField('firstname');
        $object = new FirstName($field->getValue());
        $field->setValue($object->getValue());
    }

    public function validateLastName()
    {
        $field = $this->getField('lastname');
        $object = new LastName($field->getValue());
        $field->setValue($object->getValue());
    }

    public function validatePassword()
    {
        $autogeneratePassword = false;
        $passwordField = $this->getField('password');
        $passwordConfirmationField = $this->getField('password_confirmation');

        if ($this->formatter->isRequestPassword()) {
            //si no se envia password personalizado, se crea una automaticamente.
            $autogeneratePasswordField = $this->getField('onepagecheckoutps_autogenerate_password');
            if ($autogeneratePasswordField) {
                $autogeneratePassword = (bool) !$autogeneratePasswordField->getValue();
                if ($autogeneratePassword) {
                    $passwdGen = Tools::passwdGen(8, 'RANDOM');
                    $passwordField->setValue($passwdGen);
                    if ($passwordConfirmationField) {
                        $passwordConfirmationField->setValue($passwdGen);
                    }
                }
            }

            $object = new Password($passwordField->getValue());
            $passwordField->setValue($object->getValue());

            if (!$this->contextProvider->getCustomer()->isLogged()
                && $passwordConfirmationField && !$autogeneratePassword
            ) {
                if ($passwordField->getValue() !== $passwordConfirmationField->getValue()) {
                    throw new MyAccountException(
                        'The password and the confirmation must match.',
                        MyAccountException::CUSTOMER_PASSWORD_MUST_MATCH
                    );
                }
            }
        } else {
            if ($this->contextProvider->getCustomer()->isGuest()
                || !$this->contextProvider->getCustomer()->isLogged()
            ) {
                $passwdGen = Tools::passwdGen(8, 'RANDOM');
                $passwordField->setValue($passwdGen);
                if ($passwordConfirmationField) {
                    $passwordConfirmationField->setValue($passwdGen);
                }
            }
        }
    }

    public function validateEmail()
    {
        $field = $this->getField('email');
        $object = new Email($field->getValue());
        $field->setValue($object->getValue());

        $emailConfirmation = $this->getField('email_confirmation');
        if ($this->formatter->isRequestConfirmationEmail() && $field->getValue() !== $emailConfirmation->getValue()) {
            throw new MyAccountException(
                'The email and the confirmation must match.',
                MyAccountException::CUSTOMER_EMAIL_MUST_MATCH
            );
        }

        $id_customer = Customer::customerExists($field->getValue(), true, true);
        $customer = $this->getCustomer();
        if ($id_customer && $id_customer != $customer->id) {
            throw new MyAccountException(
                'An account was already registered with this email address.',
                MyAccountException::CUSTOMER_EMAIL_ALREADY_USED
            );
        }
    }

    public function validateBirthday()
    {
        $field = $this->getField('birthday');
        if ($field->getValue() && $field->getValue() !== Birthday::createEmpty()->getValue()) {
            $field->formatDateField($this->contextProvider->getLanguage());
        }
    }

    private function validateByModules()
    {
        $formFieldsAssociated = array();

        foreach ($this->formFields as $formField) {
            if (!empty($formField->moduleName)) {
                $formFieldsAssociated[$formField->moduleName][] = $formField;
            }
        }

        foreach ($formFieldsAssociated as $moduleName => $formFields) {
            if ($moduleId = Module::getModuleIdByName($moduleName)) {
                // ToDo : replace Hook::exec with HookFinder, because we expect a specific class here
                $validatedCustomerFormFields = Hook::exec(
                    'validateCustomerFormFields',
                    array('fields' => $formFields),
                    $moduleId,
                    true
                );

                if (is_array($validatedCustomerFormFields)) {
                    array_merge($this->formFields, $validatedCustomerFormFields);
                }
            }
        }
    }

    public function validate()
    {
        foreach ($this->formFields as $field) {
            //if (!$field->isRequired() && !$field->getValue()) {
            if (in_array($field->getName(), $this->fieldListRequired) && !$field->getValue()) {
                if (array_key_exists('defaultValue', $field->getAvailableValues())) {
                    $defaultValue = $field->getAvailableValues()['defaultValue'];
                    if ($defaultValue !== 'defaultValue') {
                        $field->setValue($field->getAvailableValues()['defaultValue']);
                    }
                }
            }
            //}
        }

        $this->validateFirstName();
        $this->validateLastName();
        $this->validateEmail();
        $this->validatePassword();
        $this->validateBirthday();
        $this->validateByModules();

        foreach ($this->formFields as $field) {
            if ($field->getAvailableValues()['validation'] === 'isDate') {
                $field->formatDateField($this->contextProvider->getLanguage());
            }

            if ($field->isRequired() && !$field->getValue()) {
                throw new MyAccountException(
                    sprintf('The field %s is required.', $field->getLabel()),
                    MyAccountException::CUSTOMER_FIELD_REQUIRED
                );
            } elseif (!$field->isRequired() && !$field->getValue()) {
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

        Hook::exec('actionContactFormSubmitCaptcha');

        $errorControllerList = $this->contextProvider->getController()->errors;
        if (is_array($errorControllerList) && count($errorControllerList) > 0) {
            throw new MyAccountException(
                $errorControllerList[0],
                MyAccountException::CUSTOMER_CAPTCHA_NOT_VALID
            );
        }

        return !$this->hasErrors();
    }

    public function getCustomer()
    {
        $customer = new Customer($this->contextProvider->getCustomer()->id);

        foreach ($this->formFields as $field) {
            $customerField = $field->getName();
            if (property_exists($customer, $customerField)) {
                $customer->$customerField = $field->getValue();
            }
        }

        if ($this->isGuest) {
            $customer->is_guest = true;
        }

        return $customer;
    }

    public function getCustomFieldList(int $customerId)
    {
        $customerFieldList = array();

        $opcFieldRepository = $this->module->getEntityManager()->getRepository(OpcField::class);

        $paramListSQL = array(
            'object' => 'customer',
            'idCustomer' => $customerId,
            //'idAddress' => 0,
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

    public function saveCustomFields()
    {
        foreach ($this->formFields as $field) {
            if (array_key_exists('custom', $field->getAvailableValues())) {
                if ((bool) $field->getAvailableValues()['custom']) {
                    $opcFieldRepository = $this->module->getEntityManager()->getRepository(OpcField::class);
                    $opcField = $opcFieldRepository->findOneById((int) $field->getAvailableValues()['id']);

                    if ($opcField) {
                        $opcFieldCustomer = $opcField->getFieldCustomerBy(
                            'customer',
                            $this->contextProvider->getCustomer()->id,
                            0
                        );
                        if (!$opcFieldCustomer) {
                            $opcFieldCustomer = new OpcFieldCustomer();
                            $opcFieldCustomer->setField($opcField);
                            $opcFieldCustomer->setIdCustomer($this->contextProvider->getCustomer()->id);
                            $opcFieldCustomer->setIdAddress(0);
                            $opcFieldCustomer->setObject('customer');
                        }

                        $opcFieldOptions = $opcField->getFieldOptions();
                        if (!$opcFieldOptions->isEmpty()) {
                            foreach ($opcFieldOptions as $opcFieldoption) {
                                if ($opcFieldoption->getValue() === $field->getValue()) {
                                    $opcFieldCustomer->setIdOption($opcFieldoption->getId());

                                    break;
                                }
                            }
                        }

                        $opcFieldCustomer->setValue($field->getValue());
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
        if ($this->validate()) {
            $customer = $this->getCustomer();

            $clearTextPassword = $this->getValue('password');
            $newPassword = $this->getValue('new_password');

            $ok = $this->getPersister()->save(
                $customer,
                $clearTextPassword,
                $newPassword,
                $this->formatter->isRequestPassword()
            );

            if (!$ok) {
                foreach ($this->getPersister()->getErrors() as $field => $errors) {
                    $this->formFields[$field]->setErrors($errors);
                }
            } else {
                $this->saveCustomFields();
            }

            return $ok;
        }

        return false;
    }

    protected function getPersister()
    {
        return $this->persister;
    }

    public function getTemplateVariables()
    {
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
            'isCustomer' => $this->contextProvider->getCustomer()->isLogged()
                && !$this->contextProvider->getCustomer()->isGuest(),
            'privateAvailableKeys' => $this->formatter->getPrivateAvailableKeys(),
            'isRequestConfirmationEmail' => $this->formatter->isRequestConfirmationEmail(),
        );
    }
}
