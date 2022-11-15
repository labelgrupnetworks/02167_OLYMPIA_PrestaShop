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

use OnePageCheckoutPS\Application\Core\MyAccount\MyAccountService;
use OnePageCheckoutPS\Application\Core\MyAccount\SocialNetwork;
use OnePageCheckoutPS\Application\Core\Addresses\AddressesService;
use OnePageCheckoutPS\Application\Core\Cart\CartService;
use OnePageCheckoutPS\Application\Core\CoreService;
use OnePageCheckoutPS\Application\PrestaShop\Configuration\Configuration;
use OnePageCheckoutPS\Application\PrestaShop\Provider\ContextProvider;
use OnePageCheckoutPS\Application\PrestaShop\Provider\ShopProvider;
use OnePageCheckoutPS\Install\Installer;
use OnePageCheckoutPS\Exception\OPCException;
use OnePageCheckoutPS\Exception\SocialNetworkException;
use PrestaShop\ModuleLibServiceContainer\DependencyInjection\ServiceContainer;

trait OnePageCheckoutPSAdapter
{
    private $serviceContainer;
    private $psContext;
    private $psShop;
    private $entityManager;
    private $dbalConnection;

    public $tabClassName = 'AdminActionsOPC';
    public $tabName = 'One Page Checkout PrestaShop';

    private $moduleConfigs = array(
        'OPC_VERSION' => array('options' => array('default_value' => self::VERSION)),

        /* general */
        'OPC_ENABLE_DEBUG' => array('options' => array('default_value' => '0', 'is_bool' => true)),
        'OPC_IP_DEBUG' => array('options' => array('default_value' => '')),
        'OPC_SHOW_DELIVERY_VIRTUAL' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_CONFIRM_ADDRESS' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_ID_CONTENT_PAGE' => array('options' => array('default_value' => '#content-wrapper #main')),
        'OPC_DEFAULT_PAYMENT_METHOD' => array('options' => array('default_value' => '')),
        'OPC_DEFAULT_GROUP_CUSTOMER' => array('options' => array('default_value' => 3)),
        'OPC_GROUPS_CUSTOMER_ADDITIONAL' => array('options' => array('default_value' => '')),
        'OPC_ID_CUSTOMER' => array('options' => array('default_value' => '0')),
        'OPC_VALIDATE_DNI' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_REDIRECT_DIRECTLY_TO_OPC' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_REPLACE_AUTH_CONTROLLER' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_REPLACE_IDENTITY_CONTROLLER' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_REPLACE_ADDRESSES_CONTROLLER' => array('options' => array('default_value' => 1, 'is_bool' => true)),

        /* register - step 1 */
        'OPC_REQUIRED_LOGIN_CUSTOMER_REGISTERED' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_SHOW_BUTTON_REGISTER' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_USE_SAME_NAME_CONTACT_DA' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_USE_SAME_NAME_CONTACT_BA' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_REQUEST_PASSWORD' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_PRESEL_CREATE_ACCOUNT' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_OPTION_AUTOGENERATE_PASSWORD' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_MARK_CHECKBOX_CHANGE_PASSWD' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_ENABLE_INVOICE_ADDRESS' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_REQUIRED_INVOICE_ADDRESS' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_REQUEST_CONFIRM_EMAIL' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_CHOICE_GROUP_CUSTOMER' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_CHOICE_GROUP_CUSTOMER_ALLOW' => array('options' => array('default_value' => '')),
        'OPC_SHOW_LIST_CITIES_GEONAMES' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_AUTO_ADDRESS_GEONAMES' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_AUTOCOMPLETE_GOOGLE_ADDRESS' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_SUGGESTED_ADDRESS_GOOGLE' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_GOOGLE_API_KEY' => array('options' => array('default_value' => '')),
        'OPC_CAPITALIZE_FIELDS' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_INSERT_ISO_CODE_IN_INVOI_DNI' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_INSERT_ISO_CODE_IN_DELIV_DNI' => array('options' => array('default_value' => 0, 'is_bool' => true)),

        /* shipping - step 2 */
        'OPC_SHIPPING_COMPATIBILITY' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_RELOAD_SHIPPING_BY_STATE' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_SHOW_DESCRIPTION_CARRIER' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_SHOW_IMAGE_CARRIER' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_FORCE_NEED_POSTCODE' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_FORCE_NEED_CITY' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_MODULE_CARRIER_NEED_POSTCODE' => array('options' => array('default_value' => 'envialacarrier,bbcarrier,packlink')),
        'OPC_MODULE_CARRIER_NEED_CITY' => array('options' => array('default_value' => 'shippingconfiguratorpro,chilexpress_oficial')),

        /* payment - step 3 */
        //'OPC_SHOW_POPUP_PAYMENT' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        //'OPC_PAYMENTS_WITHOUT_RADIO' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        //'OPC_MODULES_WITHOUT_POPUP' => array('options' => array('default_value' => '')),
        'OPC_SHOW_IMAGE_PAYMENT' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_SHOW_DETAIL_PAYMENT' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_PAYMENT_NEED_REGISTER' => array('options' => array('default_value' => '')),

        /* review - step 4 */
        'OPC_SHOW_LINK_CONTINUE_SHOPPING' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_LINK_CONTINUE_SHOPPING' => array('options' => array('default_value' => '')),
        'OPC_SHOW_ZOOM_IMAGE_PRODUCT' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_REMOVE_LINK_PRODUCTS' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_SHOW_TOTAL_PRODUCT' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_SHOW_TOTAL_DISCOUNT' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_SHOW_TOTAL_WRAPPING' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_SHOW_TOTAL_SHIPPING' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_SHOW_TOTAL_WITHOUT_TAX' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_SHOW_TOTAL_TAX' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_SHOW_TOTAL_PRICE' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_SHOW_REMAINING_FREE_SHIPPING' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_ENABLE_TERMS_CONDITIONS' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_ID_CMS_TEMRS_CONDITIONS' => array('options' => array('default_value' => 0)),
        'OPC_ENABLE_PRIVACY_POLICY' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_ID_CMS_PRIVACY_POLICY' => array('options' => array('default_value' => 0)),
        'OPC_REQUIRE_PP_BEFORE_BUY' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_SHOW_WEIGHT' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_SHOW_REFERENCE' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_SHOW_UNIT_PRICE' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_SHOW_AVAILABILITY' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_SHOW_DELIVERY_TIME' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_ENABLE_HOOK_SHOPPING_CART' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_COMPATIBILITY_REVIEW' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_ALLOW_DISCOUNTS' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_SHOW_VOUCHER_BOX' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_SHOW_ORDER_MESSAGE' => array('options' => array('default_value' => 1, 'is_bool' => true)),

        /* theme */
        'OPC_THEME_BACKGROUND_COLOR' => array('options' => array('default_value' => '')),
        'OPC_THEME_BORDER_COLOR' => array('options' => array('default_value' => '')),
        'OPC_THEME_ICON_COLOR' => array('options' => array('default_value' => '')),
        'OPC_THEME_CONFIRM_COLOR' => array('options' => array('default_value' => '')),
        'OPC_THEME_CONFIRM_TEXT_COLOR' => array('options' => array('default_value' => '')),
        'OPC_THEME_TEXT_COLOR' => array('options' => array('default_value' => '')),
        'OPC_THEME_SELECTED_COLOR' => array('options' => array('default_value' => '')),
        'OPC_THEME_SELECTED_TEXT_COLOR' => array('options' => array('default_value' => '')),
        'OPC_ALREADY_REGISTER_BUTTON' => array('options' => array('default_value' => '')),
        'OPC_ALREADY_REGISTER_BUTTON_TEXT' => array('options' => array('default_value' => '')),
        'OPC_THEME_LOGIN_BUTTON' => array('options' => array('default_value' => '')),
        'OPC_THEME_LOGIN_BUTTON_TEXT' => array('options' => array('default_value' => '')),
        'OPC_THEME_VOUCHER_BUTTON' => array('options' => array('default_value' => '')),
        'OPC_THEME_VOUCHER_BUTTON_TEXT' => array('options' => array('default_value' => '')),
        'OPC_BACKGROUND_BUTTON_FOOTER' => array('options' => array('default_value' => '')),
        'OPC_THEME_BORDER_BUTTON_FOOTER' => array('options' => array('default_value' => '')),
        'OPC_CONFIRMATION_BUTTON_FLOAT' => array('options' => array('default_value' => 1, 'is_bool' => true)),

        /* social */
        'OPC_SOCIAL_NETWORKS' => array('options' => array('default_value' => '')),

        /* code editors */
        'OPC_OVERRIDE_CSS' => array('options' => array('default_value' => '', 'is_html' => true)),
        'OPC_OVERRIDE_JS' => array('options' => array('default_value' => '', 'is_html' => true)),

        //Checkout Beta - Tab
        'OPC_ENABLE_DEBUG_NEW_CHECKOUT' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_IP_CHECKOUT_BETA' => array('options' => array('default_value' => '')),
        'OPC_ALLOW_EDIT_PRODUCTS_CART' => array('options' => array('default_value' => 1, 'is_bool' => false)),
        'OPC_STYLE' => array('options' => array('default_value' => 'three_columns', 'is_bool' => false)),
        'OPC_STYLE_MOBILE' => array('options' => array('default_value' => 'steps', 'is_bool' => false)),
        'OPC_SHOW_NATIVE_HEADER' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_SHOW_NATIVE_FOOTER' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_CARRIER_STORE_PICKUP' => array('options' => array('default_value' => 0, 'is_bool' => false)),
        'OPC_FORCE_CUSTOMER_REGISTRATION_LOGIN' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_SHOW_LOGIN_REGISTER_IN_TABS' => array('options' => array('default_value' => 0, 'is_bool' => false)),
        'OPC_AUTOCOMPLETE_CUSTOMER_NAME_ON_ADDRESS' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_SHOW_DISCOUNT_BOX_PAYMENT_MOBILE' => array('options' => array('default_value' => 1, 'is_bool' => true)),
        'OPC_VALIDATE_UNIQUE_DNI' => array('options' => array('default_value' => 0, 'is_bool' => true)),
        'OPC_SHOW_PHONE_MASK' => array('options' => array('default_value' => 0, 'is_bool' => true)),
    );

    private $moduleHooks = array(
        'displayHeader',
        'displayShoppingCart',
        'actionShopDataDuplication',
        'displayAdminOrder',
        'registerGDPRConsent',
        'actionDeleteGDPRCustomer',
        'actionCustomerLogoutAfter',
        'actionAuthentication',
        'actionObjectAddressAddAfter',
        'actionObjectAddressUpdateAfter',
        'actionObjectAddressDeleteAfter',
        'actionObjectCustomerAddAfter',
        'actionObjectCustomerUpdateAfter',
        'actionAdminCustomersFormModifier',
        'actionAdminAddressesFormModifier',
        'actionCustomerFormBuilderModifier',
        'actionCustomerAddressFormBuilderModifier',
        'actionAfterCreateCustomerFormHandler',
        'actionAfterUpdateCustomerFormHandler',
        'actionAfterUpdateCustomerAddressFormHandler',
        'moduleRoutes',
        'actionUpdateLangAfter',
        'additionalCustomerFormFields',
        'actionOpcPaymentFeeService',
        'actionOpcValidateVatNumber',
        'actionOpcValidationCustomerRegister',
    );

    private $moduleQueries;

    private $styleList = array(
        array('style' => self::VERTICAL, 'name' => 'Vertical'),
        array('style' => self::STEPS, 'name' => 'Steps'),
        array('style' => self::THREE_COLUMNS, 'name' => '3 Columns'),
    );

    public function getModuleConfigList()
    {
        return $this->moduleConfigs;
    }

    public function getConfigurationList($key)
    {
        return $this->getModuleConfigList()[$key]['value'];
    }

    public function setConfigurations()
    {
        $this->getService(Configuration::SERVICE_NAME)->fill(
            $this->moduleConfigs
        );

        //funcion adapter al funcionamiento de acceso a la configuracion.
        foreach ($this->moduleConfigs as $key => $config) {
            $this->config_vars[$key] = $config['value'];
        }
    }

    public function getModuleHooks()
    {
        return $this->moduleHooks;
    }

    public function getModuleQueries()
    {
        $queries = require_once _PS_MODULE_DIR_ . 'onepagecheckoutps/sql/queries.php';
        if ($queries !== true) {
            $this->moduleQueries = $queries;
        }

        return $this->moduleQueries;
    }

    public function getService($serviceName)
    {
        if (is_null($this->serviceContainer)) {
            $this->serviceContainer = new ServiceContainer(
                $this->name . str_replace('.', '', $this->version),
                $this->getLocalPath()
            );
        }

        return $this->serviceContainer->getService($serviceName);
    }

    public function getContextProvider()
    {
        if (is_null($this->psContext)) {
            $this->psContext = $this->getService(ContextProvider::SERVICE_NAME);
        }

        return $this->psContext;
    }

    public function getShopProvider()
    {
        if (is_null($this->psShop)) {
            $this->psShop = $this->getService(ShopProvider::SERVICE_NAME);
        }

        return $this->psShop;
    }

    public function getEntityManager()
    {
        if (is_null($this->entityManager)) {
            $this->entityManager = $this->get('doctrine.orm.entity_manager');
        }

        return $this->entityManager;
    }

    public function getDbalConnection()
    {
        if (is_null($this->dbalConnection)) {
            $this->dbalConnection = $this->get('doctrine.dbal.default_connection');
        }

        return $this->dbalConnection;
    }

    public function getStyleModule()
    {
        if ($this->getContextProvider()->isMobile()) {
            return $this->getConfigurationList('OPC_STYLE_MOBILE');
        }

        return $this->getConfigurationList('OPC_STYLE');
    }

    public function isCheckoutBetaEnabled()
    {
        if (version_compare(_PS_VERSION_, '1.7.6', '>=')) {
            if ($this->getConfigurationList('OPC_ENABLE_DEBUG_NEW_CHECKOUT') === true) {
                $ipList = $this->getConfigurationList('OPC_IP_CHECKOUT_BETA');
                if (!empty($ipList)) {
                    $ipArrayList = explode(',', $ipList);
                    $remoteAddr = Tools::getRemoteAddr();

                    if (in_array($remoteAddr, $ipArrayList) || in_array('PRODUCTION', $ipArrayList)) {
                        return true;
                    }
                }
            } else {
                return true;
            }
        }

        return false;
    }

    public function existNotification($controller)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $notifications = array();
        if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION['notifications'])) {
            $notifications = json_decode($_SESSION['notifications'], true);
        } elseif (isset($_COOKIE['notifications'])) {
            $notifications = json_decode($_COOKIE['notifications'], true);
        }
        if ($notifications) {
            foreach ($notifications as $notification) {
                if ($notification) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Trae la informacion necesaria del pais y sus estados activos
     * Usada para mostrar ciertos campos dependiendo del pais seleccionado en el form de address
     */
    public function getCountryList()
    {
        $countryListReturn = array();

        $countryList = Country::getCountries($this->getContextProvider()->getLanguageId(), true);
        if ($countryList) {
            foreach ($countryList as $countryId => $country) {
                $zipCodeFormatted = $country['zip_code_format'];
                if (!empty($zipCodeFormatted)) {
                    $zipCodeFormatted = str_replace('N', '0', $zipCodeFormatted);
                    $zipCodeFormatted = str_replace('L', 'A', $zipCodeFormatted);
                    $zipCodeFormatted = str_replace('C', $country['iso_code'], $zipCodeFormatted);
                }

                $countryListReturn[$countryId] = array(
                    'isoCode' => $country['iso_code'],
                    'needIdentification' => (bool) $country['need_identification_number'],
                    'needZipCode' => (bool) $country['need_zip_code'],
                    'zipCodeFormat' => $country['zip_code_format'],
                    'zipCodeFormatted' => $zipCodeFormatted,
                );

                if ((bool) $country['contains_states'] && isset($country['states']) && count($country['states']) > 0) {
                    foreach ($country['states'] as $state) {
                        if ((bool) $state['active']) {
                            $stateId = (int) $state['id_state'];

                            $countryListReturn[$countryId]['states'][$stateId] = array(
                                'name' => $state['name'],
                                'isoCode' => $state['iso_code'],
                            );
                        }
                    }
                }
            }
        }

        return $countryListReturn;
    }

    public function addSocialNetwork()
    {
        try {
            $requestParameters = Tools::getAllValues();

            $socialNetwork = $this->getService(SocialNetwork::SERVICE_NAME);
            $socialNetwork->fillWith($requestParameters);
            $socialNetwork->add();

            return array(
                'message_code' => $this->core->CODE_SUCCESS,
                'message' => $this->l('The social network was successfully added.'),
            );
        } catch (OPCException $exception) {
            if ($exception->getCode() === SocialNetworkException::SOCIAL_NETWORK_DUPLICATED) {
                return array(
                    'message_code' => $this->core->CODE_ERROR,
                    'message' => $this->l('The social network is already added, if you want to modify it, please first delete it and add it again.'),
                );
            }

            return array(
                'message_code' => $this->core->CODE_ERROR,
                'message' => $this->l('An error occurred while trying to add.'),
                'messageException' => $exception->getMessage(),
            );
        }
    }

    public function deleteSocialNetwork()
    {
        try {
            $requestParameters = Tools::getAllValues();

            $socialNetwork = $this->getService(SocialNetwork::SERVICE_NAME);
            $socialNetwork->fillWith($requestParameters);
            $socialNetwork->delete();

            return array(
                'message_code' => $this->core->CODE_SUCCESS,
                'message' => $this->l('The social network was successfully deleted.'),
            );
        } catch (OPCException $exception) {
            return array(
                'message_code' => $this->core->CODE_ERROR,
                'message' => $this->l('An error occurred while trying to delete.'),
                'messageException' => $exception->getMessage(),
            );
        }
    }

    public function getSocialNetworkList()
    {
        $socialNetwork = $this->getService(SocialNetwork::SERVICE_NAME);
        $sociaNetworkList = $socialNetwork->getList();

        $content = array();
        $headers = array(
            'name' => $this->l('Name'),
            'enabled' => $this->l('Enabled'),
        );

        foreach ($sociaNetworkList as $name => $network) {
            $item = array(
                'name' => $name,
                'enabled' => (int) $network['enabled'],
            );

            foreach ($network['keys'] as $key => $value) {
                if ($key == 'id') {
                    $key = 'Key';
                } elseif ($key == 'secret') {
                    $key = 'Secret';
                }

                $item[$key] = $value;
                $headers[$key] = $key;
            }

            array_push($content, $item);
        }

        $actions = array(
            'delete' => array(
                'action_class' => 'SocialNetwork',
                'class' => 'has-action',
                'icon' => 'trash-o',
                'title' => $this->l('Remove'),
            ),
        );

        $headers['actions'] = $this->l('Actions');

        return array(
            'message_code' => 1,
            'content' => $content,
            'table' => 'table-form-list-socialNetwort',
            'headers' => $headers,
            'actions' => $actions,
            'truncate' => array(),
            'prefix_row' => 'network',
            'masive' => array(),
            'status' => array(
                'enabled' => array(),
            ),
            'color' => array(),
        );
    }

    public function sendEmailPasswordRecovery()
    {
        $customer = $this->getContextProvider()->getCustomer();
        $mailParams = array(
            '{email}' => $customer->email,
            '{lastname}' => $customer->lastname,
            '{firstname}' => $customer->firstname,
            '{url}' => $this->context->link->getPageLink(
                'password',
                true,
                null,
                'token=' . $customer->secure_key . '&id_customer=' . (int) $customer->id . '&reset_token=' . $customer->reset_password_token
            ),
        );
        Mail::Send(
            $this->getContextProvider()->getLanguageId(),
            'password_query',
            $this->l('Password query confirmation'),
            $mailParams,
            $customer->email,
            $customer->firstname . ' ' . $customer->lastname
        );

        return $this->l('A link has been sent to your email so that you can set your password safely.');
    }

    public function getMessageList()
    {
        return array(
            'close' => $this->l('Close', 'onepagecheckoutps_adapter'),
            'labelFee' => $this->l('Fee', 'onepagecheckoutps_adapter'),
            'haveReadAndAccept' => $this->l('I have read and accept the', 'onepagecheckoutps_adapter'),
            'privacyPolicy' => $this->l('Privacy Policy', 'onepagecheckoutps_adapter'),
            'myAddress' => $this->l('My address', 'onepagecheckoutps_adapter'),
            'conditionsRequired' => $this->l('Accept all the conditions to continue', 'onepagecheckoutps_adapter'),
            'confimDeliveryAddressRequired' => $this->l('Confirm the delivery address to continue', 'onepagecheckoutps_adapter'),
            'pickupStoreRequired' => $this->l('It is necessary to select a collection point', 'onepagecheckoutps_adapter'),
            'paymentRequired' => $this->l('Select a payment method to continue', 'onepagecheckoutps_adapter'),
            'wantEnterCustomPassoword' => $this->l('I want to enter a custom password.', 'onepagecheckoutps_adapter'),
            'infoUpdated' => $this->l('The information has been updated correctly.', 'onepagecheckoutps_adapter'),
            'example' => $this->l('Example', 'onepagecheckoutps_adapter'),
            'FormValidator' => array(
                'errorGlobal' => $this->l('This is not a valid.', 'onepagecheckoutps_adapter'),
                'errorIsName' => $this->l('This is not a valid name.', 'onepagecheckoutps_adapter'),
                'errorIsEmail' => $this->l('This is not a valid email address.', 'onepagecheckoutps_adapter'),
                'errorIsPostCode' => $this->l('This is not a valid post code.', 'onepagecheckoutps_adapter'),
                'errorIsAddress' => $this->l('This is not a valid address.', 'onepagecheckoutps_adapter'),
                'errorIsCityName' => $this->l('This is not a valid city.', 'onepagecheckoutps_adapter'),
                'isMessage' => $this->l('This is not a valid message.', 'onepagecheckoutps_adapter'),
                'errorIsDniLite' => $this->l('This is not a valid document identifier.', 'onepagecheckoutps_adapter'),
                'errorIsDniUsed' => $this->l('The DNI is already in use.', 'onepagecheckoutps_adapter'),
                'errorIsPhoneNumber' => $this->l('This is not a valid phone.', 'onepagecheckoutps_adapter'),
                'errorIsPasswd' => $this->l('This is not a valid password. Minimum 5 characters.', 'onepagecheckoutps_adapter'),
                'errorisBirthDate' => $this->l('This is not a valid birthdate.', 'onepagecheckoutps_adapter'),
                'errorisDate' => $this->l('This is not a valid date.', 'onepagecheckoutps_adapter'),
                'badUrl' => $this->l('This is not a valid url.', 'onepagecheckoutps_adapter') . 'ex: http://www.domain.com',
                'badInt' => $this->l('This is not a valid.', 'onepagecheckoutps_adapter'),
                'notConfirmed' => $this->l('The values do not match.', 'onepagecheckoutps_adapter'),
                'lengthTooLongStart' => $this->l('It is only possible enter', 'onepagecheckoutps_adapter'),
                'lengthTooShortStart' => $this->l('The input value is shorter than ', 'onepagecheckoutps_adapter'),
                'lengthBadEnd' => $this->l('characters.', 'onepagecheckoutps_adapter'),
                'requiredField' => $this->l('This is a required field.', 'onepagecheckoutps_adapter'),
            ),
        );
    }

    public function getTemplateVars()
    {
        $link = $this->getContextProvider()->getLink();
        $language = $this->getContextProvider()->getLanguage();
        $customer = $this->getContextProvider()->getCustomer();
        $baseLink = $link->getPageLink('index');

        $myAccount = $this->getService(MyAccountService::SERVICE_NAME);
        $addresses = $this->getService(AddressesService::SERVICE_NAME);
        $cartService = $this->getService(CartService::SERVICE_NAME);
        $coreService = $this->getService(CoreService::SERVICE_NAME);

        $dateFormatLanguage = $this->dateFormartPHPtoJqueryUI($this->getContextProvider()->getLanguage()->date_format_lite);

        $showInlineBadge = false;
        if ($coreService->isModuleActive('securitypro')) {
            if (true === (bool) \Configuration::get('PRO_RECAPTCHA_V3_CONTACT_ACTIVATE')
                || true === (bool) \Configuration::get('PRO_RECAPTCHA_V3_REGISTRATION_ACTIVATE')
            ) {
                $showInlineBadge = true;
            }
        }

        return array(
            'PresTeamShop' => array(
                'ptsToken' => $coreService->getToken($this->name),
            ),
            $this->prefix_module => array(
                'Module' => array(
                    'name' => $this->name,
                    'version' => $this->version,
                    'domain' => $_SERVER['SERVER_NAME'],
                    'token' => $coreService->getToken($this->name),
                    'addons' => !file_exists(dirname(__FILE__).'/'.Tools::strtolower($this->prefix_module).'.php'),
                    'checksum' => Tools::file_get_contents(dirname(__FILE__).'/src/checksum'),
                ),
                'General' => array(
                    'Design' => array(
                        'style' => $this->getStyleModule(),
                    ),
                    'showInlineBadge' => $showInlineBadge,
                    'isLogged' => $customer->isLogged(true),
                    'isCustomer' => $customer->isLogged(),
                    'isGuest' => $customer->isGuest(),
                    'isVirtualCart' => $this->getContextProvider()->isVirtualCart(),
                    'isMobile' => $this->getContextProvider()->isMobile(),
                    'varGlobals' => $this->globals,
                    'socialNetworkList' => $myAccount->getSocialNetworkList(),
                    'countryList' => $this->getCountryList(),
                    'showNativeHeader' => $this->getConfigurationList('OPC_SHOW_NATIVE_HEADER'),
                    'showNativeFooter' => $this->getConfigurationList('OPC_SHOW_NATIVE_FOOTER'),
                    'showButtonContinueShopping' => $this->getConfigurationList('OPC_SHOW_LINK_CONTINUE_SHOPPING'),
                    'linkContinueShopping' => $this->getConfigurationList('OPC_LINK_CONTINUE_SHOPPING'),
                    'forceCustomerRegistrationLogin' => $this->getConfigurationList('OPC_FORCE_CUSTOMER_REGISTRATION_LOGIN'),
                    'dateFormatLanguage' => $dateFormatLanguage,
                    'themeName' => $this->getContextProvider()->getCurrentThemeName(),
                    'pageName' => $this->getContextProvider()->getController()->php_self,
                    'Directories' => array(
                        'root' => $this->onepagecheckoutps_dir,
                        'img' => $this->onepagecheckoutps_dir . 'views/img/',
                        'formValidator' => $this->onepagecheckoutps_dir . 'views/js/lib/form-validator/',
                    ),
                ),
                'Language' => array(
                    'dateFormatJquery' => $this->dateFormartPHPtoJqueryUI($language->date_format_lite),
                    'isRTL' => (bool) $language->is_rtl,
                ),
                'Message' => $this->getMessageList(),
                'MyAccount' => array(
                    'customerExistsUrl' => $baseLink . 'checkout/myaccount/customerExists',
                    'loginUrl' => $baseLink . 'checkout/myaccount/loginCustomer',
                    'saveUrl' => $baseLink . 'checkout/myaccount/saveCustomer',
                    'convertGuestToCustomerUrl' => $baseLink . 'checkout/myaccount/convertGuestToCustomerUrl',
                    'isGuestAllowed' => $myAccount->isGuestAllowed(),
                    'isConfirmEmail' => $this->getConfigurationList('OPC_REQUEST_CONFIRM_EMAIL'),
                    'isAutogeneratePasswordEnabled' => $myAccount->isAutogeneratePasswordEnabled(),
                ),
                'Addresses' => array(
                    'saveUrl' => $baseLink . 'checkout/addresses/save',
                    'deleteUrl' => $baseLink . 'checkout/addresses/delete',
                    'listUrl' => $baseLink . 'checkout/addresses/list',
                    'validateUniqueDniUrl' => $baseLink . 'checkout/addresses/validateUniqueDni',
                    'isValidateDNI' => $this->getConfigurationList('OPC_VALIDATE_DNI'),
                    'isValidateUniqueDni' => $this->getConfigurationList('OPC_VALIDATE_UNIQUE_DNI'),
                    'showPhoneMask' => $this->getConfigurationList('OPC_SHOW_PHONE_MASK'),
                    'isAutocompleteGoogleEnabled' => $addresses->isAutocompleteGoogleEnabled(),
                    'isPostalCodeAutocompleGeonamesEnabled' => $addresses->isPostalCodeAutocompleGeonamesEnabled(),
                    'isDeliveryAddressEnabled' => $addresses->isDeliveryAddressEnabled(),
                    'isInvoiceAddressEnabled' => $addresses->isInvoiceAddressEnabled(),
                    'customerHaveAddresses' => $addresses->customerHaveAddresses(),
                    'country' => $addresses->getCountry()
                ),
                'Shipping' => array(
                    'listUrl' => $baseLink . 'checkout/shipping/list',
                    'updateUrl' => $baseLink . 'checkout/shipping/update',
                ),
                'Payment' => array(
                    'listUrl' => $baseLink . 'checkout/payment/list',
                    'isEnableTermsAndConditions' => $this->getConfigurationList('OPC_ENABLE_TERMS_CONDITIONS'),
                    'hookDisplayExpressCheckout' => Hook::exec('displayExpressCheckout'),
                ),
                'Cart' => array(
                    'cartSummaryUrl' => $baseLink . 'checkout/cart',
                    'updateAddressUrl' => $baseLink . 'checkout/cart/updateAddress',
                    'haveSameAddress' => $addresses->haveSameAddress(),
                    'isEditProductsCartEnabled' => $cartService->isEditProductsCartEnabled(),
                    'showHookShoppingCart' => $cartService->showHookShoppingCart(),
                ),
            ),
        );
    }

    public function initBeforeOrderController($controller)
    {
        $controller = $controller;
    }

    public function initAfterOrderController($controller)
    {
        $controller = $controller;

        //se crea una direccion solo para visitantes y poder mostrar las direfentes opciones de envio y pago.
        $cart = $this->getContextProvider()->getCart();
        $customer = $this->getContextProvider()->getCustomer();
        if ($cart->nbProducts() > 0) {
            if (empty($this->getContextProvider()->getCookie()->addressIdOPC)
                && !$customer->isLogged()
                && !$customer->isGuest()
            ) {
                $coreService = $this->getService(CoreService::SERVICE_NAME);

                if (empty($cart->id_address_delivery) && empty($cart->id_address_invoice)) {
                    $addresses = $this->getService(AddressesService::SERVICE_NAME);

                    if ($this->getContextProvider()->isVirtualCart()) {
                        $addresses->setParameters(array(
                            'typeAddress' => 'invoice'
                        ));
                    } else {
                        $addresses->setParameters(array(
                            'typeAddress' => 'delivery'
                        ));
                    }

                    $addressId = $addresses->createAddress();

                    //Guardamos el addressId para facilitar el registro de la primer direccion del cliente.
                    $this->getContextProvider()->getCookie()->addressIdOPC = $addressId;
                //ets_geolocation - v1.1.4 - ETS-Soft
                } else if ($coreService->isModuleActive('ets_geolocation')) {
                    $this->getContextProvider()->getCookie()->addressIdOPC = $cart->id_address_delivery;
                }
            }

            $cart->setNoMultishipping();

            //ets_payment_with_fee - v2.2.9 - ETS-Soft.
            //Eliminamos la seleccion del pago con fee cuando recargamos el checkout.
            if (Module::isInstalled('ets_payment_with_fee')) {
                Db::getInstance()->execute(
                    'DELETE FROM `' . _DB_PREFIX_ . 'ets_payment_cart` WHERE `id_cart` = '. (int) $this->getContextProvider()->getCart()->id
                );
            }
        }
    }

    public function initContentControllerOPCBeta($controller, $pageName = '')
    {
        $pathTemplate = $this->name . '/views/templates/front/checkout/';
        $pageName = empty($pageName) ? $this->getContextProvider()->getController()->php_self : $pageName;
        $template = $pageName;
        $templateVars = $this->getTemplateVars();

        $this->getContextProvider()->getSmarty()->assign($templateVars);
        Media::addJsDef($templateVars);

        if (in_array($pageName, array('authentication', 'identity'))) {
            $myAccount = $this->getService(MyAccountService::SERVICE_NAME);
            $this->getContextProvider()->getSmarty()->assign(
                'stepMyAccountRendered',
                $myAccount->render()
            );

            $template = $pageName;
        } elseif ($pageName === 'addresses') {
            $addresses = $this->getService(AddressesService::SERVICE_NAME);

            if ($addresses->isDeliveryAddressEnabled()) {
                $addresses->setParameters(array('typeAddress' => 'delivery'));
            } else {
                $addresses->setParameters(array('typeAddress' => 'invoice'));
            }

            $this->getContextProvider()->getSmarty()->assign(
                'stepAddressesRendered',
                $addresses->render()
            );

            $template = 'addresses';
        } else {
            $myAccount = $this->getService(MyAccountService::SERVICE_NAME);
            $this->getContextProvider()->getSmarty()->assign(
                'stepMyAccountRendered',
                $myAccount->render()
            );

            $cart = $this->getService(CartService::SERVICE_NAME);
            $this->getContextProvider()->getSmarty()->assign(
                'stepCartRendered',
                $cart->render()
            );

            if ($template === 'order') {
                $template = 'checkout';
            }
        }

        if (file_exists(_PS_THEME_DIR_ . 'modules/' . $pathTemplate . $template . '.tpl')) {
            $controller->setTemplate(
                '../../../themes/' . _THEME_NAME_ . '/modules/' . $pathTemplate . $template
            );
        } else {
            $controller->setTemplate(
                '../../../modules/' . $pathTemplate . $template
            );
        }
    }

    public function loginTemporalCustomer(&$customerLogged)
    {
        $cookie = $this->getContextProvider()->getCookie();
        $cart = $this->getContextProvider()->getCart();
        $customer = $this->getContextProvider()->getCustomer();
        $customerTemporalId = $this->getConfigurationList('OPC_ID_CUSTOMER');

        if (empty($cookie->id_customer)) {
            $cookie->id_customer = $customerTemporalId;

            if (empty($customer->id)) {
                $customer = new Customer($customerTemporalId);
                $customer->logged = 1;
            }

            if (empty($cart->id_customer)) {
                $cart->id_customer = $customerTemporalId;
            }

            $customerLogged = true;

            if (!empty($cookie->addressIdOPC)) {
                $cart->id_address_delivery = $cookie->addressIdOPC;
                $cart->id_address_invoice = $cookie->addressIdOPC;
            }

            if (method_exists('Cart', 'resetStaticCache')) {
                Cart::resetStaticCache();
            }
            Cache::clean('*');
        }
    }

    public function logoutTemporalCustomer()
    {
        $cookie = $this->getContextProvider()->getCookie();
        $cart = $this->getContextProvider()->getCart();
        $customer = $this->getContextProvider()->getCustomer();

        $customer = new Customer();
        $customer->logged = 0;
        unset($cookie->id_customer);

        $cart->id_customer = null;
        $cart->id_address_delivery = 0;
        $cart->id_address_invoice = 0;

        $cart->setDeliveryOption(array(
            0 => sprintf('%d,', $cart->id_carrier),
        ));

        $cart->update();
        $cart->setNoMultishipping();
    }

    public function isUsingNewTranslationSystem()
    {
        return false;
    }

    public function isBackOffice()
    {
        return defined('_PS_ADMIN_DIR_') || defined('PS_INSTALLATION_IN_PROGRESS') || PHP_SAPI === 'cli';
    }

    public function hookActionUpdateLangAfter($params)
    {
        $iso_code = $params['lang']->iso_code;
        if (!file_exists(dirname(__FILE__) . '/sql/languages/' . $iso_code . '.sql')) {
            $iso_code = 'en';
        }

        $sqlLangs = Tools::file_get_contents(dirname(__FILE__) . '/sql/languages/' . $iso_code . '.sql');
        if ($sqlLangs) {
            $sqlLangs = str_replace('PREFIX_', _DB_PREFIX_, $sqlLangs);
            $sqlLangs = str_replace('ID_LANG', $params['lang']->id, $sqlLangs);

            $shopList = $this->getService(ShopProvider::SERVICE_NAME)->getShops();
            if ($shopList) {
                foreach ($shopList as $shop) {
                    $sqlLangShop = str_replace('ID_SHOP', $shop['id_shop'], $sqlLangs);
                    $sqlLangShop = preg_split("/;\s*[\r\n]+/", $sqlLangShop);

                    foreach ($sqlLangShop as $query) {
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute(trim($query));
                    }
                }
            }
        }
    }

    public function displayHeader()
    {
        if ($this->isBackOffice()) {
            return;
        }

        return $this->getService('onepagecheckoutps.hook.display.header')->run(array(
            'context' => $this->getContextProvider()->getContextLegacy()
        ));
    }

    public function hookModuleRoutes()
    {
        return $this->getService('onepagecheckoutps.hook.module_routes')->run();
    }

    public function hookAdditionalCustomerFormFields()
    {
        if (!$this->isCheckoutBetaEnabled()) {
            return array();
        }

        return $this->getService('onepagecheckoutps.hook.additional_customer_form_fields')->run();
    }

    public function hookActionOpcPaymentFeeService($params)
    {
        return $this->getService('onepagecheckoutps.hook.action.opc_payment_fee')->run($params);
    }

    public function hookActionOpcValidatePayment()
    {
        return $this->getService('onepagecheckoutps.hook.action.opc_validate_payment')->run();
    }

    public function hookActionOpcValidateVatNumber($params)
    {
        return $this->getService('onepagecheckoutps.hook.action.opc_validate_vat_number')->run($params);
    }

    public function hookActionCustomerLogoutAfter($params)
    {
        $this->getService('onepagecheckoutps.hook.action.customer_logout_after')->run($params);

        //se llama esto para soportar el antiguo OPC.
        $this->oldHookActionCustomerLogoutAfter($params);
    }

    public function hookActionOpcValidationCustomerRegister()
    {
        return $this->getService('onepagecheckoutps.hook.action.opc_validation_customer_register')->run();
    }

    public function updateTableFieldLang()
    {
        $this->getService(Installer::SERVICE_NAME)->insertQueriesByLang();

        die('OK');
    }
}
