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

use CartRule;
use Configuration;
use Customer;
use CustomerPersister;
use Db;
use Hook;
use Hybridauth\Hybridauth;
use Hybridauth\Storage\Session;
use OnePageCheckoutPS\Application\Core\AbstractStepCheckout;
use OnePageCheckoutPS\Application\Core\CoreService;
use OnePageCheckoutPS\Application\Core\Form\OpcCustomerForm;
use OnePageCheckoutPS\Entity\OpcField;
use OnePageCheckoutPS\Exception\MyAccountException;
use Tools;
use Validate;

class MyAccountService extends AbstractStepCheckout
{
    private $core;
    private $module;
    private $contextProvider;

    public const SERVICE_NAME = 'onepagecheckoutps.core.myaccount';

    public function __construct(CoreService $core)
    {
        $this->core = $core;

        $this->module = $this->core->getModule();
        $this->contextProvider = $this->module->getContextProvider();

        $this->setModule($this->module);
    }

    public function getCustomerName()
    {
        return $this->contextProvider->getCustomer()->firstname . ' ' . $this->contextProvider->getCustomer()->lastname;
    }

    public function isPrivacyPolicyEnabled()
    {
        if (!$this->contextProvider->getCustomer()->isLogged()
            && $this->module->getConfigurationList('OPC_ENABLE_PRIVACY_POLICY')
        ) {
            if ($privacyPolicyId = $this->module->getConfigurationList('OPC_ID_CMS_PRIVACY_POLICY')) {
                return (int) $privacyPolicyId;
            }
        }

        return false;
    }

    public function isShowLoginAndRegistrationInTabsEnabled()
    {
        if ($this->module->getConfigurationList('OPC_SHOW_LOGIN_REGISTER_IN_TABS')
            || $this->module->getStyleModule() === 'three_columns'
        ) {
            return true;
        }

        return false;
    }

    public function isGuestAllowed($conditionPage = true)
    {
        if ((bool) Configuration::get('PS_GUEST_CHECKOUT_ENABLED')) {
            if (!$conditionPage) {
                return true;
            }

            $pageName = $this->contextProvider->getController()->php_self;
            if ($this->exitsParameter('pageName')) {
                $pageName = $this->getParameter('pageName');
            }

            if ($this->contextProvider->getCustomer()->isGuest()
                || (!$this->contextProvider->getCustomer()->isLogged() && $pageName !== 'authentication')
            ) {
                return true;
            }
        }

        return false;
    }

    public function isRequestPasswordEnabled()
    {
        return (bool) $this->module->getConfigurationList('OPC_REQUEST_PASSWORD');
    }

    public function isAutogeneratePasswordEnabled()
    {
        if (!$this->isRequestPasswordEnabled()) {
            return false;
        }

        return (bool) $this->module->getConfigurationList('OPC_OPTION_AUTOGENERATE_PASSWORD');
    }

    public function customerExists()
    {
        if (Customer::customerExists($this->getParameter('email'))) {
            throw new MyAccountException(
                'An account was already registered with this email address.',
                MyAccountException::CUSTOMER_EMAIL_ALREADY_USED
            );
        }
    }

    public function login($email, $password)
    {
        if (empty($email)) {
            throw new MyAccountException(
                'The email cannot be empty',
                MyAccountException::CUSTOMER_EMAIL_EMPTY
            );
        }
        if (!Validate::isEmail($email)) {
            throw new MyAccountException(
                sprintf(
                    'The email %s format is invalid',
                    $email
                ),
                MyAccountException::CUSTOMER_EMAIL_INVALID,
                array(
                    $email,
                )
            );
        }

        if (empty($password)) {
            throw new MyAccountException(
                'The password cannot be empty',
                MyAccountException::CUSTOMER_PASSWORD_EMPTY
            );
        }
        if (!Validate::isPasswd($password)) {
            throw new MyAccountException(
                sprintf(
                    'The password is not valid, it must be at least %s characters long',
                    Validate::PASSWORD_LENGTH
                ),
                MyAccountException::CUSTOMER_PASSWORD_INVALID,
                array(
                    Validate::PASSWORD_LENGTH,
                )
            );
        }

        if ($this->contextProvider->getCustomer()->isLogged()) {
            throw new MyAccountException(
                'The customer is already logged in',
                MyAccountException::CUSTOMER_ALREADY_LOGGED_IN
            );
        }

        Hook::exec('actionAuthenticationBefore');

        $customer = new Customer();
        $authentication = $customer->getByEmail(
            $email,
            $password
        );

        if (isset($authentication->active) && !$authentication->active) {
            throw new MyAccountException(
                'Your account isn\'t available at this time, please contact us',
                MyAccountException::CUSTOMER_DISABLED
            );
        } elseif (!$authentication || !$customer->id || $customer->is_guest) {
            throw new MyAccountException(
                'The email or password is incorrect. Verify your information and try again',
                MyAccountException::CUSTOMER_ACCESS_INCORRECT
            );
        } else {
            $this->singInCustomer($customer);
        }
    }

    public function getSocialNetworkList($enabled = true)
    {
        $socialNetwork = new SocialNetwork($this->module);

        return $socialNetwork->getList($enabled);
    }

    public function loginSocial()
    {
        $baseLink = $this->contextProvider->getLink()->getBaseLink();
        $callback = $baseLink . 'checkout/myaccount/loginSocialCustomer';

        $config = array(
            'callback' => $callback,
            'providers' => $this->getSocialNetworkList(),
        );

        $hybridauth = new Hybridauth($config);
        $storage = new Session();

        if ($this->exitsParameter('provider')) {
            $provider = $this->getParameter('provider');

            $storage->set('provider', $provider);
        }

        if ($provider = $storage->get('provider')) {
            if (in_array($provider, $hybridauth->getProviders())) {
                $shopId = $this->contextProvider->getShopId();
                $customer = new Customer();

                $hybridauth->authenticate($provider);

                $storage->set('provider', null);

                $adapter = $hybridauth->getAdapter($provider);
                $userProfile = $adapter->getUserProfile();

                $data = array(
                    'email' => $userProfile->email,
                    'firstname' => $userProfile->firstName,
                    'lastname' => $userProfile->lastName,
                    'address1' => $userProfile->address,
                    'country' => $userProfile->country,
                    'state' => $userProfile->region,
                    'city' => $userProfile->city,
                    'postcode' => $userProfile->zip,
                    'phone' => $userProfile->phone,
                );

                if (empty($userProfile->email)) {
                    die($provider . ' has not allowed access to your email, for this reason you can not login.');
                }
                if (empty($userProfile->firstName) || empty($userProfile->lastName)) {
                    die($provider . ' has not allowed access to your name, for this reason you can not login.');
                }

                $adapter->disconnect();

                $customer->getByEmail($userProfile->email);
                if (!Validate::isLoadedObject($customer)) {
                    foreach ($data as $property => $value) {
                        if (property_exists($customer, $property)) {
                            $customer->{$property} = $value;
                        }
                    }

                    $fieldRepository = $this->module->getEntityManager()->getRepository(OpcField::class);
                    $opcFieldNewsletter = $fieldRepository->getDefaultValueNewsletter($shopId);
                    if (!empty($opcFieldNewsletter['defaultValue'])) {
                        $customer->newsletter = (int) $opcFieldNewsletter['defaultValue'];
                    }

                    $password = Tools::passwdGen();
                    $clearTextPassword = md5(pSQL(_COOKIE_KEY_ . $password));

                    $customerPersister = new CustomerPersister(
                        $this->module->getContextProvider()->getContextLegacy(),
                        $this->module->get('hashing'),
                        $this->module->getTranslator(),
                        false
                    );

                    $customerPersister->save(
                        $customer,
                        $clearTextPassword,
                        '',
                        true
                    );
                } else {
                    if ($customer->active == 0) {
                        throw new MyAccountException(
                            'It is not possible to login with your account. Please contact the store administrator for more details.',
                            MyAccountException::CUSTOMER_DISABLED
                        );
                    }

                    $this->singInCustomer($customer);
                }

                Db::getInstance(_PS_USE_SQL_SLAVE_)->insert(
                    'opc_social_network_stats',
                    array(
                        'id_shop' => (int) $shopId,
                        'id_customer' => (int) $customer->id,
                        'code_network' => pSQL($userProfile->identifier),
                        'network' => pSQL($provider),
                    )
                );
            } else {
                throw new MyAccountException(
                    'The requested social network is not supported.',
                    MyAccountException::SOCIAL_NETWORK_NOT_SUPPORTED
                );
            }
        }
    }

    public function singInCustomer($customer)
    {
        $this->contextProvider->getContextLegacy()->updateCustomer($customer);

        Hook::exec('actionAuthentication', array('customer' => $customer));

        CartRule::autoRemoveFromCart();
        CartRule::autoAddToCart();

        unset($this->contextProvider->getCookie()->addressIdOPC);

        return true;
    }

    public function saveCustomer()
    {
        $registerForm = $this->getRegisterForm();
        $registerForm->fillWith($this->getParameters());

        $hookResult = array_reduce(
            Hook::exec('actionSubmitAccountBefore', array(), null, true),
            function ($carry, $item) {
                return $carry && ($item || is_null($item));
            },
            true
        );

        if ($hookResult && $registerForm->submit()) {
            return true;
        }

        return $registerForm->getErrors();
    }

    public function convertGuestToCustomer()
    {
        $customer = $this->contextProvider->getCustomer();

        if (!$customer->isGuest()) {
            throw new MyAccountException(
                'The active session is not a guest.',
                MyAccountException::CUSTOMER_SESSION_NOT_GUEST
            );
        }

        $customer->transformToCustomer($this->contextProvider->getLanguageId());

        $this->contextProvider->getContextLegacy()->updateCustomer($customer);
    }

    public function getRegisterForm()
    {
        $opcCustomerFormatter = new OpcCustomerFormatter(
            $this->module
        );
        if (!$this->contextProvider->getCustomer()->isLogged()) {
            $opcCustomerFormatter->setRequestConfirmationEmail(
                $this->module->getConfigurationList('OPC_REQUEST_CONFIRM_EMAIL')
            );
            $opcCustomerFormatter->setRequestPassword($this->isRequestPasswordEnabled());
        } else {
            $opcCustomerFormatter->setRequestConfirmationEmail(false);
            $opcCustomerFormatter->setRequestPassword(false);
        }

        $pageName = $this->contextProvider->getController()->php_self;
        if ($this->exitsParameter('pageName')) {
            $pageName = $this->getParameter('pageName');
        }

        //Se elimina el requerir la contraseña en el caso que se tenga activo la compra de invitado y se este logueado
        //como invitado o bien no se este logueado.
        if ($this->isGuestAllowed()) {
            $opcCustomerFormatter->setRequestPassword(false);
        }

        $opcCustomerForm = new OpcCustomerForm(
            $this->module,
            $opcCustomerFormatter,
            new CustomerPersister(
                $this->contextProvider->getContextLegacy(),
                $this->module->get('hashing'),
                $this->module->getTranslator(),
                $this->isGuestAllowed()
            )
        );

        if ($this->isGuestAllowed()) {
            $opcCustomerForm->setGuest(true);
        }

        return $opcCustomerForm;
    }

    public function getTemplateVars()
    {
        $varList = array(
            'isShowLoginAndRegistrationInTabsEnabled' => $this->isShowLoginAndRegistrationInTabsEnabled(),
            'logoutUrl' => $this->contextProvider->getLink()->getPageLink('index', true, null, 'mylogout'),
        );

        return array_merge($this->core->getCommonVars(), $varList);
    }

    public function getTemplate()
    {
        $pageName = $this->contextProvider->getController()->php_self;
        if ($this->exitsParameter('pageName')) {
            $pageName = $this->getParameter('pageName');
        }

        $this->contextProvider->getSmarty()->assign('pageName', $pageName);

        $idxrvalidatinguser = false;
        if ($this->core->isModuleActive('idxrvalidatinguser')) {
            if (Configuration::get('IDXRVALIDATINGUSER_WHOLESALEFORM') && Configuration::get('IDXRVALIDATINGUSER_VALIDITYREGISTER')) {
                $idxrvalidatinguser = true;
            }
        }

        if ($pageName === 'identity') {
            return 'identity.tpl';
        } elseif ($pageName === 'authentication') {
            if ($idxrvalidatinguser) {
                return 'compatibilities/idxrvalidatinguser.tpl';
            }

            return 'my_account.tpl';
        }

        if ($idxrvalidatinguser) {
            return 'compatibilities/idxrvalidatinguser.tpl';
        }

        return 'my_account.tpl';
    }

    public function render()
    {
        $varList = $this->getTemplateVars();

        $formCustomer = $this->getRegisterForm();

        if ($this->contextProvider->getCustomer()->isLogged() ||
            $this->contextProvider->getCustomer()->isGuest()
        ) {
            $formCustomer->fillFromCustomer($this->contextProvider->getCustomer());
        } else {
            $formCustomer->fillWith();
        }

        $this->contextProvider->getSmarty()->assign(
            'formCustomer',
            $formCustomer->render()
        );

        $this->contextProvider->getSmarty()->assign($varList);

        return $this->contextProvider->getSmarty()->fetch(
            'module:onepagecheckoutps/views/templates/front/checkout/my_account/' . $this->getTemplate()
        );
    }
}
