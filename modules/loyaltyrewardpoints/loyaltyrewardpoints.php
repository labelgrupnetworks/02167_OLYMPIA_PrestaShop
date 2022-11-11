<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Musaffar Patel
 * @copyright 2016-2021 Musaffar Patel
 * @license   LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(_PS_MODULE_DIR_ . '/loyaltyrewardpoints/lib/bootstrap.php');

class LoyaltyRewardPoints extends Module
{
    const __MA_MAIL_DELIMITOR__ = ',';

    public $module_folder = 'loyaltyrewardpoints';
    public $module_file = __FILE__;
    public $base_url = '';

    public function __construct()
    {
        $this->name = 'loyaltyrewardpoints';
        $this->tab = 'others';
        $this->version = '2.2.0';
        $this->author = 'Musaffar Patel';
        parent::__construct();
        $this->displayName = $this->l('Loyalty Reward Points');
        $this->description = $this->l('Loyalty Reward Points');
        $this->module_key = 'ae5ac4d263462fae8497791a3626c91a';
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->file = __FILE__;
        $this->bootstrap = true;

        $this->base_url = Tools::getShopProtocol() . Tools::getShopDomain() . __PS_BASE_URI__;
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('backOfficeHeader')
            || !$this->registerHook('backOfficeFooter')
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('displayShoppingCart')
            || !$this->registerHook('displayShoppingCartFooter')
            || !$this->registerHook('displayPaymentTop')
            || !$this->registerHook('actionOrderStatusPostUpdate')
            || !$this->registerHook('displayAdminOrderLeft')
            || !$this->registerHook('displayAdminOrderMainBottom')
            || !$this->registerHook('displayCustomerAccount')
            || !$this->registerHook('displayAdminCustomers')
            || !$this->registerHook('actionDispatcher')
            || !$this->registerHook('actionCustomerAccountAdd')
            || !$this->registerHook('displayFooterProduct')
            || !$this->registerHook('displayProductAdditionalInfo')
            || !$this->registerHook('actionAuthentication')
            || !$this->registerHook('actionCartSave')
            || !$this->registerHook('actionValidateOrder')
            || !$this->installModule()
        ) {
            return false;
        }
        $this->l('reward discount');
        LRPInstall::installData();
        return true;
    }

    private function installModule()
    {
        return LRPInstall::installDB();
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    /* Call set media for all the various controllers in this module.  Each controller will decide if the time is appropriate for queuing it's css and js */
    public function setMedia()
    {
        (new LRPAdminConfigMainController($this))->setMedia();
        (new LRPAdminOrderController($this))->setMedia();
        (new LRPAdminCustomerController($this))->setMedia();
        (new LRPFrontCheckoutController($this))->setMedia();
        (new LRPFrontProductController($this))->setMedia();
        (new LRPFrontCustomerController($this))->setMedia();

        // Migration
        (new LRPAdminConfigMigrationController($this))->setMedia();
    }

    public function getContent()
    {
        return $this->route();
    }

    public function hookHeader($params)
    {
        $this->setMedia();
    }

    public function hookBackOfficeHeader($params)
    {
        $this->setMedia();
    }

    public function hookDisplayShoppingCart($params)
    {
        return (new LRPFrontCheckoutController($this))->hookDisplayShoppingCart($params);
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        (new LRPFrontCheckoutController($this))->hookActionOrderStatusPostUpdate($params);
    }


    public function hookDisplayAdminOrderLeft($params)
    {
        return $this->hookDisplayAdminOrderMainBottom($params);
    }

    public function hookDisplayAdminOrderMainBottom($params)
    {
        if (!empty($params['request'])) {
            $request = $params['request'];
        } else {
            $request = null;
        }
        return (new LRPAdminOrderController($this, $request))->render();
    }

    /**
     * Customer account Page
     * @param $params
     */
    public function hookDisplayCustomerAccount($params)
    {
        return LoyaltyRewardPointsCustomerAccountModuleFrontController::renderIndexBlock($this);
    }

    public function hookDisplayAdminCustomers($params)
    {
        return (new LRPAdminCustomerController($this, $params['request']))->render();
    }

    /**
     * Set referral link when landing one site if we need to
     * @param $params
     * @throws Exception
     */
    public function hookActionDispatcher($params)
    {
        if ((int)Tools::getValue('rid') > 0 && (int)Context::getContext()->customer->id == 0) {
            LRPReferralHelper::setRefererCookie(Tools::getValue('rid'));
            LRPReferralHelper::logReferralClick(Tools::getRemoteAddr(), (int)Tools::getValue('rid'));
        }

        if (!empty(Context::getContext()->customer->id)) {
            if (Context::getContext()->customer->id > 0) {
                LRPCustomerHelper::calculateAndUpdatePointsTotal(Context::getContext()->customer->id);
            }
        }
    }

    /**
     * action cart save
     * @param $params
     */
    public function hookActionCartSave($params)
    {
        $op = Tools::getValue('op');
        $delete = Tools::getValue('delete');

        if (empty(Context::getContext()->cart)) {
            return false;
        }

        $lrp_front_checkout_controller = new LRPFrontCheckoutController($this);
        if (Tools::getValue('add') == '1') {
            $lrp_front_checkout_controller->hookActionCartSave($params);
        } else {
            if ($op == 'up' || $op == 'down' || $delete == '1') {
                $lrp_front_checkout_controller->hookActionCartSaveUpdated($params);
            }
        }

        LRPReferralHelper::updateReferrerIdStorageCartId(LRPReferralHelper::getReferrerIdFromCookie(), Context::getContext()->customer->id, Context::getContext()->cart->id);
    }

    /**
     * @param $params
     */
    public function hookActionCustomerAccountAdd($params)
    {
        (new LRPFrontCustomerController($this))->hookActionCustomerAccountAdd($params);
    }

    /**
     * @param $params
     */
    public function hookActionAuthentication($params)
    {
        (new LRPFrontCustomerController($this))->hookActionAuthentication($params);
    }

    /**
     * Order validated
     * @param $params
     */
    public function hookActionValidateOrder($params)
    {
        $id_referrer = (int)LRPReferralHelper::getReferrerIdFromStorage($params['cart']->id_customer, $params['cart']->id);
        $source = '';
        LRPDiscountHelper::setPointsRedeeemCookie(0);
        LRPReferralHelper::clearRefererCookie();
        LRPCartHelper::redeemPoints($params['cart']->id_customer, $params['cart']->id);
        if ($id_referrer > 0) {
            $source = 'referral_signup_discount';
        }

        $points_redeemed = LRPDiscountHelper::getPointsRedeemed(false, $params['cart']->id, $params['cart']->id_customer, null);
        if ($points_redeemed > 0) {
            $currency = new Currency($params['cart']->id_currency);
            LRPCartHelper::redeemPoints($params['cart']->id_customer, $params['cart']->id);
            LRPHistoryHelper::redeemPoints($params['order']->id, $params['cart']->id_customer, $points_redeemed, $source, $currency);
        }
    }

    /**
     * @param $params
     * @return mixed
     */
    public function hookDisplayShoppingCartFooter($params)
    {
        return (new LRPFrontCheckoutController($this))->hookDisplayShoppingCartFooter($params);
    }

    /**
     * @param $params
     */
    public function hookDisplayFooterProduct($params)
    {
        return (new LRPFrontProductController($this))->hookDisplayFooterProduct($params);
    }

    /**
     * Product Quick View
     * @param $params
     * @return string
     */
    public function hookDisplayProductAdditionalInfo($params)
    {
        if (Tools::getValue('action') == 'quickview') {
            $params['quickview'] = 1;
            return (new LRPFrontProductController($this))->hookDisplayFooterProduct($params);
        }
    }

    /**
     * for steasycheckout module
     * @param $params
     * @return mixed
     */
    public function hookDisplayPaymentTop($params)
    {
        return (new LRPFrontCheckoutController($this))->hookDisplayShoppingCart($params);
    }

    public function route()
    {
        switch (Tools::getValue('route')) {
            case 'lrpadminconfiggroupscontroller':
                return (new LRPAdminConfigGroupsController($this))->route();

            case 'lrpadminconfiggeneralcontroller':
                return (new LRPAdminConfigGeneralController($this))->route();

            case 'lrpadminconfiginsightscontroller':
                return (new LRPAdminConfigInsightsController($this))->route();

            case 'lrpadminconfigrulescontroller':
                return (new LRPAdminConfigRulesController($this))->route();

            case 'lrpadmincustomercontoller':
                return (new LRPAdminCustomerController($this))->route();

            case 'lrpadminconfigmigrationcontroller':
                return (new LRPAdminConfigMigrationController($this))->route();

            case 'lrpadminconfigmigrationpsloyaltycontroller':
                return (new LRPAdminConfigMigrationPSLoyaltyController($this))->route();

            default:
                return (new LRPAdminConfigMainController($this))->route();
        }
    }
}
