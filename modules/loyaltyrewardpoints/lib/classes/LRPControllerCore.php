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

class LRPControllerCore extends Module
{
    protected $module_url = '';
    protected $module_ajax_url = '';
    protected $module_config_url = '';
    protected $sibling;
    protected $default_lang = null;
    protected $params = array();

    protected $key_tab = 'ModuleLoyaltyrewardpoints';

    public function __construct($sibling, $params = array())
    {
        $this->sibling = $sibling;
        if (!empty($params)) {
            $this->params = $params;
        }

        parent::__construct();

        $link = new Link();
        $this->module_ajax_url = $link->getModuleLink('loyaltyrewardpoints', 'ajax', array());
        $this->default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        if (AdminController::$currentIndex != '') {
            $this->module_config_url = $this->context->link->getAdminLink('AdminModules', true) . '&configure=loyaltyrewardpoints';
            $this->module_url = Tools::getShopProtocol() . Tools::getShopDomain() . __PS_BASE_URI__ . 'modules/loyaltyrewardpoints/';
        }
    }

    /**
     * Get the url to the module folder
     * @return string
     */
    protected function getShopBaseUrl()
    {
        if (Tools::getShopDomain() != $_SERVER['HTTP_HOST']) {
            $domain = $_SERVER['HTTP_HOST'];
        } else {
            $domain = Tools::getShopDomain();
        }

        if (empty($_SERVER['HTTPS']) || !$_SERVER['HTTPS']) {
            return "http://" . $domain . __PS_BASE_URI__ . 'modules/' . $this->sibling->name . '/';
        } else {
            return "https://" . $domain . __PS_BASE_URI__ . 'modules/' . $this->sibling->name . '/';
        }
    }

    /**
     * get pth to admin folder
     * @return mixed
     */
    protected function getAdminWebPath()
    {
        $admin_webpath = str_ireplace(_PS_CORE_DIR_, '', _PS_ADMIN_DIR_);
        $admin_webpath = preg_replace('/^' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/', '', $admin_webpath);
        return __PS_BASE_URI__ . $admin_webpath;
    }

    /* Protected Methods */
    protected function redirect($url_params)
    {
        $url = AdminController::$currentIndex . '&configure=' . $this->sibling->name . '&' . $url_params . '&token=' . Tools::getAdminTokenLite('AdminModules');
        Tools::redirectAdmin($url);
    }

    protected function setupHelperConfigForm(HelperForm &$helper, $route, $action)
    {
        $helper->module = $this->sibling;
        $helper->name_controller = $this->sibling->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->default_form_language = $this->default_lang;
        $helper->allow_employee_form_lang = $this->default_lang;
        $helper->title = $this->sibling->displayName;
        $helper->show_toolbar = false;        // false -> remove toolbar
        $helper->toolbar_scroll = false;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->sibling->name;
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->sibling->name . '&route=' . $route . '&action=' . $action;
    }
}
