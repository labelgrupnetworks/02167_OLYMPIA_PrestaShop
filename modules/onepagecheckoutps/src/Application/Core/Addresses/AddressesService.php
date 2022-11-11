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
use AddressFormat;
use Carrier;
use Configuration;
use Country;
use Customer;
use CustomerAddressPersister;
use Db;
use DbQuery;
use OnePageCheckoutPS\Application\Core\AbstractStepCheckout;
use OnePageCheckoutPS\Application\Core\CoreService;
use OnePageCheckoutPS\Application\Core\Form\OpcCustomerAddressForm;
use OnePageCheckoutPS\Entity\OpcCustomerAddress;
use OnePageCheckoutPS\Entity\OpcField;
use Tools;

class AddressesService extends AbstractStepCheckout
{
    private $core;
    private $module;
    private $contextProvider;

    public const SERVICE_NAME = 'onepagecheckoutps.core.addresses';

    public function __construct(CoreService $core)
    {
        $this->core = $core;

        $this->module = $this->core->getModule();
        $this->contextProvider = $this->module->getContextProvider();

        $this->setModule($this->module);
    }

    public function isDeliveryAddressEnabled()
    {
        if ($this->getPageName() === 'addresses') {
            return true;
        }

        $opcShowDeliveryForVirtualCart = (bool) $this->module->getConfigurationList('OPC_SHOW_DELIVERY_VIRTUAL');
        if ($this->contextProvider->isVirtualCart() && !$opcShowDeliveryForVirtualCart) {
            return false;
        }

        return true;
    }

    public function isInvoiceAddressEnabled()
    {
        if ($this->getPageName() === 'addresses' || $this->contextProvider->isVirtualCart()) {
            return true;
        }

        $opcEnableInvoiceAddress = (bool) $this->module->getConfigurationList('OPC_ENABLE_INVOICE_ADDRESS');
        if ($opcEnableInvoiceAddress) {
            $customer = $this->contextProvider->getCustomer();
            if (!$customer->isLogged() && !$customer->isGuest()) {
                return false;
            }
        }

        return $opcEnableInvoiceAddress;
    }

    public function getAddressDeliveryId($loadObject = false)
    {
        $addressDeliveryId = (int) $this->contextProvider->getCart()->id_address_delivery;

        if (empty($addressDeliveryId)) {
            $addressIdOPC = $this->contextProvider->getCookie()->addressIdOPC;
            if ($addressIdOPC) {
                $addressDeliveryId = $addressIdOPC;
            }
        }

        if ($loadObject) {
            if (empty($addressDeliveryId)) {
                return false;
            }

            return new Address($addressDeliveryId);
        }

        return $addressDeliveryId;
    }

    public function getAddressInvoiceId($loadObject = false)
    {
        $addressInvoiceId = (int) $this->contextProvider->getCart()->id_address_invoice;

        if (empty($addressInvoiceId) && !$this->isDeliveryAddressEnabled()) {
            $addressIdOPC = $this->contextProvider->getCookie()->addressIdOPC;
            if ($addressIdOPC) {
                $addressInvoiceId = $addressIdOPC;
            }
        }

        if ($loadObject) {
            if (empty($addressInvoiceId)) {
                return false;
            }

            return new Address($addressInvoiceId);
        }

        return $addressInvoiceId;
    }

    public function haveSameAddress()
    {
        $cart = $this->contextProvider->getCart();

        if (isset($cart->id_address_delivery, $cart->id_address_invoice)) {
            return (int) $cart->id_address_delivery === (int) $cart->id_address_invoice;
        }

        return true;
    }

    public function isPostalCodeAutocompleGeonamesEnabled()
    {
        if ($this->module->getConfigurationList('OPC_AUTO_ADDRESS_GEONAMES')) {
            return true;
        }

        return false;
    }

    public function isAutocompleteGoogleEnabled()
    {
        if ($this->module->getConfigurationList('OPC_AUTOCOMPLETE_GOOGLE_ADDRESS')) {
            if ($this->module->getConfigurationList('OPC_GOOGLE_API_KEY')) {
                return true;
            }
        }

        return false;
    }

    public function customerHaveAddresses()
    {
        $customerAddressList = $this->contextProvider->getCustomer()->getSimpleAddresses();

        return count($customerAddressList) > 0;
    }

    public function getAutocompleteGoogleScript()
    {
        if ($this->isAutocompleteGoogleEnabled()) {
            $googleUrl = 'https://maps.googleapis.com/maps/api/js?key=';
            $googleUrl .= trim($this->module->getConfigurationList('OPC_GOOGLE_API_KEY'));
            $googleUrl .= '&sensor=false&libraries=places';
            $googleUrl .= '&language=' . $this->contextProvider->getLanguageIsoCode();

            return $googleUrl;
        }

        return false;
    }

    public function deleteAddress()
    {
        $addressPersister = new CustomerAddressPersister(
            $this->contextProvider->getCustomer(),
            $this->contextProvider->getCart(),
            Tools::getToken(true, $this->contextProvider->getContextLegacy())
        );

        return (bool) $addressPersister->delete(
            new Address($this->getParameter('addressId'), $this->contextProvider->getLanguageId()),
            $this->getParameter('token')
        );
    }

    public function saveAddress()
    {
        $registerForm = $this->getRegisterForm();
        $registerForm->loadAddressById((int) $this->getParameter('id'));
        $registerForm->fillWith($this->getParameters());
        if ($addressId = $registerForm->submit()) {
            $cart = $this->contextProvider->getCart();

            //Si el cliente no tenia direcciones antes y es esta la primer direccion
            //entonces se la asociamos al carrito, ya que en este punto aun el prestashop no la coloca.
            if ((int) Customer::getAddressesTotalById($this->contextProvider->getCustomer()->id) === 1) {
                $cart->id_address_delivery = $addressId;
                $cart->id_address_invoice = $addressId;
            } else {
                if ($this->getParameter('typeAddress') === 'invoice') {
                    //En el caso que la dirección de envio sea igual a la de facturacion, colocamos la nueva direccion creada en facturacion.
                    $this->getCartService()->setIdAddressInvoice($addressId);
                } elseif ($this->getParameter('typeAddress') === 'delivery') {
                    //En el caso que la dirección de facturacion sea igual a la de envio, colocamos la nueva direccion creada en envio.
                    if ((int) $cart->id_address_delivery === (int) $cart->id_address_invoice) {
                        $this->getCartService()->setIdAddressDelivery($addressId, true);
                    } else {
                        $this->getCartService()->setIdAddressDelivery($addressId, false);
                    }
                }
            }

            return $addressId;
        }

        return $registerForm->getErrors();
    }

    public function createAddress()
    {
        $addressFieldList = array(
            'firstname',
            'lastname',
            'address1',
            'city',
            'postcode',
            'id_country',
            'id_state',
            'alias',
            'dni',
            'company',
            'address2',
            'other',
            'phone',
            'phone_mobile',
            'vat_number',
        );

        $opcCustomerAddressFormatter = new OpcCustomerAddressFormatter(
            $this->module,
            $this->getCountry(),
            $this->getAvailableCountries()
        );

        $inserParamList = array(
            'date_add' => date('Y-m-d H:i:s'),
            'date_upd' => date('Y-m-d H:i:s'),
        );
        foreach ($addressFieldList as $fieldName) {
            $inserParamList[$fieldName] = $opcCustomerAddressFormatter->getDefaultValueField($fieldName);
        }

        $customerId = (int) $this->module->getConfigurationList('OPC_ID_CUSTOMER');
        $customer = $this->contextProvider->getCustomer();
        if ($customer->isLogged() || $customer->isGuest()) {
            $customerId = (int) $customer->id;
        }

        $inserParamList['id_customer'] = $customerId;

        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $db->insert('address', $inserParamList);
        $addressId = (int) $db->Insert_ID();

        if ($addressId) {
            $typeAddress = 'delivery';
            if (!$this->isDeliveryAddressEnabled()) {
                $typeAddress = 'invoice';
            }

            $this->addCustomerAddress(
                $customerId,
                $addressId,
                $typeAddress
            );

            return $addressId;
        }

        return false;
    }

    public function addCustomerAddress($customerId, $addressId, $typeAddress)
    {
        $opcCustomerAddress = new OpcCustomerAddress();
        $opcCustomerAddress->setCustomerId($customerId);
        $opcCustomerAddress->setAddressId($addressId);
        $opcCustomerAddress->setObject($typeAddress);

        $entityManager = $this->module->getEntityManager();
        $entityManager->persist($opcCustomerAddress);
        $entityManager->flush();
    }

    public function getCustomerAddressByType(string $typeAddress)
    {
        $addressList = array();
        $customer = $this->contextProvider->getCustomer();
        if ($customer->isLogged() || $customer->isGuest()) {
            $query = new DbQuery();
            $query->select('a.id_address as id, a.*');
            $query->from('address', 'a');
            $query->leftJoin('opc_customer_address', 'ca', 'a.id_address = ca.id_address');
            $query->where('ca.`object` = "' . pSQL($typeAddress) . '" OR ca.`object` IS NULL');
            $query->where('a.id_customer = ' . (int) $customer->id);
            $query->where('a.active = 1');
            $query->where('a.deleted = 0');

            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        }

        return $addressList;
    }

    public function getRegisterForm()
    {
        $opcCustomerAddressFormatter = new OpcCustomerAddressFormatter(
            $this->module,
            $this->getCountry(),
            $this->getAvailableCountries()
        );
        $opcCustomerAddressFormatter->setTypeAddress($this->getParameter('typeAddress'));

        $customer = $this->contextProvider->getCustomer();
        if (!$customer->isLogged() && !$customer->isGuest()) {
            $customer = new Customer($this->module->getConfigurationList('OPC_ID_CUSTOMER'));
        }

        $opcCustomerAddressForm = new OpcCustomerAddressForm(
            $this->module,
            $opcCustomerAddressFormatter,
            new CustomerAddressPersister(
                $customer,
                $this->contextProvider->getCart(),
                Tools::getToken(true, $this->contextProvider->getContextLegacy())
            ),
            $customer
        );

        return $opcCustomerAddressForm;
    }

    public function getAvailableCountries()
    {
        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $availableCountries = Carrier::getDeliveredCountries(
                $this->contextProvider->getLanguageId(),
                true,
                true
            );
        } else {
            $availableCountries = Country::getCountries($this->contextProvider->getLanguageId(), true);
        }

        return $availableCountries;
    }

    public function getCountry()
    {
        $countryId = 0;
        if ($this->exitsParameter('id_country')) {
            $countryId = (int) $this->getParameter('id_country');
        } elseif ($this->exitsParameter('addressId')) {
            $address = new Address((int) $this->getParameter('addressId'));
            $countryId = $address->id_country;
        } elseif ($this->exitsParameter('typeAddress')) {
            $typeAddress = $this->getParameter('typeAddress');
            if ($typeAddress === 'delivery' && $address = $this->getAddressDeliveryId(true)) {
                $countryId = $address->id_country;
            } elseif ($typeAddress === 'invoice' && $address = $this->getAddressInvoiceId(true)) {
                $countryId = $address->id_country;
            } else {
                $shopId = $this->contextProvider->getShopId();
                $fieldRepository = $this->module->getEntityManager()->getRepository(OpcField::class);
                $opcField = $fieldRepository->getDefaultValueCountry($shopId, $typeAddress);
                if (!empty($opcField['defaultValue'])) {
                    $countryId = (int) $opcField['defaultValue'];
                }
            }
        } else {
            return $this->contextProvider->getCountry();
        }

        return new Country($countryId);
    }

    public function getPageName()
    {
        $pageName = $this->contextProvider->getController()->php_self;
        if ($this->exitsParameter('pageName')) {
            $pageName = $this->getParameter('pageName');
        }

        return $pageName;
    }

    public function getTemplateVars()
    {
        $pageName = $this->getPageName();

        $nbItemsPerLine = 3;
        if ($this->module->getStyleModule() === $this->module::THREE_COLUMNS && $pageName !== 'addresses') {
            $nbItemsPerLine = 2;
        }

        $vars = array(
            'haveSameAddress' => $this->haveSameAddress(),
            'customerHaveAddresses' => $this->customerHaveAddresses(),
            'isDeliveryAddressEnabled' => $this->isDeliveryAddressEnabled(),
            'isInvoiceAddressEnabled' => $this->isInvoiceAddressEnabled(),
            'nbItemsPerLine' => $nbItemsPerLine,
            'pageName' => $pageName,
        );

        return array_merge($this->core->getCommonVars(), $vars);
    }

    public function getTemplate()
    {
        return 'addresses.tpl';
    }

    public function render()
    {
        $vars = $this->getTemplateVars();

        //Si existe alguna sesion activa de un pago, solo cargamos la direccion asociada al carrito.
        $expressCheckoutModuleList = $this->getPaymentService()->getExpressCheckoutModuleList();
        if ($expressCheckoutModuleList) {
            $addressesDelivery = $this->getCustomerAddressByType('delivery');
            if ($addressesDelivery) {
                $cart = $this->contextProvider->getCart();

                foreach ($addressesDelivery as $key => &$address) {
                    if ((int) $address['id'] === (int) $cart->id_address_delivery) {
                        $address['formatted'] = AddressFormat::generateAddress(
                            new Address((int) $address['id']),
                            array(),
                            '<br>'
                        );
                    } else {
                        unset($addressesDelivery[$key]);
                    }
                }

                $vars['addressesDelivery'] = $addressesDelivery;
                $vars['addressDeliveryId'] = (int) $cart->id_address_delivery;

                $this->contextProvider->getSmarty()->assign($vars);

                return $this->contextProvider->getSmarty()->fetch(
                    'module:onepagecheckoutps/views/templates/front/checkout/addresses/_partials/address-actived.tpl'
                );
            }
        }

        $paramList = array(
            'id_country' => $this->getCountry()->id,
        );

        if ($this->module->getConfigurationList('OPC_AUTOCOMPLETE_CUSTOMER_NAME_ON_ADDRESS')) {
            $customer = $this->contextProvider->getCustomer();

            $paramList['firstname'] = $customer->firstname;
            $paramList['lastname'] = $customer->lastname;
        }

        if ($this->exitsParameter('editAddress')) {
            $formAddress = $this->getRegisterForm();
            $formAddress->loadAddressById((int) $this->getParameter('addressId'));

            if ($this->getParameter('typeAddress') === 'delivery') {
                $vars['formDeliveryAddress'] = $formAddress->render();
            } else {
                $vars['formInvoiceAddress'] = $formAddress->render();
            }
        } elseif ($this->exitsParameter('addAddress')) {
            $formAddress = $this->getRegisterForm();
            $formAddress->fillWith($paramList);

            if ($this->getParameter('typeAddress') === 'delivery') {
                $vars['formDeliveryAddress'] = $formAddress->render();
            } else {
                $vars['formInvoiceAddress'] = $formAddress->render();
            }
        } else {
            $addresses = $this->contextProvider->getCustomer()->getSimpleAddresses();
            if (count($addresses) > 0) {
                $cart = $this->contextProvider->getCart();
                $addressesDelivery = array();
                $addressesInvoice = array();
                $isDeliveryAddressEnabled = $this->isDeliveryAddressEnabled();
                $isInvoiceAddressEnabled = $this->isInvoiceAddressEnabled();

                if ($isDeliveryAddressEnabled) {
                    $addressesDelivery = $this->getCustomerAddressByType('delivery');
                    if ($addressesDelivery) {
                        $addressDeliverySelectedExist = false;

                        foreach ($addressesDelivery as &$address) {
                            $address['formatted'] = AddressFormat::generateAddress(
                                new Address((int) $address['id']),
                                array(),
                                '<br>'
                            );

                            if ((int) $address['id'] === (int) $cart->id_address_delivery) {
                                $addressDeliverySelectedExist = true;
                            }
                        }

                        if ($addressDeliverySelectedExist === false) {
                            $this->getCartService()->setIdAddressDelivery((int) $addressesDelivery[0]['id'], true);
                        }
                    }
                }

                if ($isInvoiceAddressEnabled) {
                    $addressesInvoice = $this->getCustomerAddressByType('invoice');
                    if ($addressesInvoice) {
                        $addressInvoiceSelectedExist = false;

                        foreach ($addressesInvoice as &$address) {
                            $address['formatted'] = AddressFormat::generateAddress(
                                new Address((int) $address['id']),
                                array(),
                                '<br>'
                            );

                            if ((int) $address['id'] === (int) $cart->id_address_invoice) {
                                $addressInvoiceSelectedExist = true;
                            }
                        }

                        if ($addressInvoiceSelectedExist === false && !$isDeliveryAddressEnabled) {
                            $this->getCartService()->setIdAddressDelivery((int) $addressesInvoice[0]['id'], true);
                        }
                    }
                }

                $vars['addressesDelivery'] = $addressesDelivery;
                $vars['addressDeliveryId'] = (int) $cart->id_address_delivery;
                $vars['addressesInvoice'] = $addressesInvoice;
                $vars['addressInvoiceId'] = (int) $cart->id_address_invoice;
            } else {
                if ($this->isDeliveryAddressEnabled()) {
                    $formDeliveryAddress = $this->getRegisterForm();

                    if ($address = $this->getAddressDeliveryId(true)) {
                        $paramList['id'] = $address->id;
                        $paramList['id_country'] = $address->id_country;
                        $paramList['id_state'] = $address->id_state;

                        $opcCustomerAddressFormatter = new OpcCustomerAddressFormatter(
                            $this->module,
                            $this->getCountry(),
                            $this->getAvailableCountries()
                        );

                        if ($opcCustomerAddressFormatter->getDefaultValueField('postcode') !== $address->postcode) {
                            $paramList['postcode'] = $address->postcode;
                        }
                        if ($opcCustomerAddressFormatter->getDefaultValueField('city') !== $address->city) {
                            $paramList['city'] = $address->city;
                        }

                        if ($this->core->isModuleActive('paypal') && Tools::getIsset('newAddress')) {
                            $paramList['address1'] = Tools::getValue('address1');
                            $paramList['firstname'] = Tools::getValue('firstname');
                            $paramList['lastname'] = Tools::getValue('lastname');
                            $paramList['postcode'] = Tools::getValue('postcode');
                            $paramList['id_country'] = Tools::getValue('id_country');
                            $paramList['city'] = Tools::getValue('city');
                            $paramList['phone'] = Tools::getValue('phone');
                            $paramList['address2'] = Tools::getValue('address2');
                            $paramList['id_state'] = Tools::getValue('id_state');
                        }

                        $formDeliveryAddress->fillWith($paramList);
                    } else {
                        $formDeliveryAddress->fillWith($paramList);
                    }

                    $vars['formDeliveryAddress'] = $formDeliveryAddress->render();
                } else {
                    $formInvoiceAddress = $this->getRegisterForm();

                    if ($address = $this->getAddressInvoiceId(true)) {
                        $paramList['id'] = $address->id;
                        $paramList['id_country'] = $address->id_country;
                        $paramList['id_state'] = $address->id_state;

                        $opcCustomerAddressFormatter = new OpcCustomerAddressFormatter(
                            $this->module,
                            $this->getCountry(),
                            $this->getAvailableCountries()
                        );

                        if ($opcCustomerAddressFormatter->getDefaultValueField('postcode') !== $address->postcode) {
                            $paramList['postcode'] = $address->postcode;
                        }
                        if ($opcCustomerAddressFormatter->getDefaultValueField('city') !== $address->city) {
                            $paramList['city'] = $address->city;
                        }
                        $formInvoiceAddress->fillWith($paramList);
                    } else {
                        $formInvoiceAddress->fillWith($paramList);
                    }

                    $vars['formInvoiceAddress'] = $formInvoiceAddress->render();
                }
            }
        }

        $this->contextProvider->getSmarty()->assign($vars);

        return $this->contextProvider->getSmarty()->fetch(
            'module:onepagecheckoutps/views/templates/front/checkout/addresses/' . $this->getTemplate()
        );
    }

    public function validateUniqueDni()
    {
        $registerForm = $this->getRegisterForm();
        $registerForm->fillWith($this->getParameters());
        $registerForm->validateUniqueDni();
    }
}
