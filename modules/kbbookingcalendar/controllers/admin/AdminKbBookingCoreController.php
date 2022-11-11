<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2018 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */

class AdminKbBookingCoreController extends ModuleAdminController
{
    /*
     * Default function, used here to set the required variables in this and its child classes
     */
    public function __construct()
    {
        $this->bootstrap = true;
        $this->allow_export = true;
        $this->context = Context::getContext();
        $this->list_no_link = true;
        $this->all_languages = $this->getAllLanguages();
        parent::__construct();
    }
    
    
    /*
     * Function for returning the URL of PrestaShop Root Modules Directory
     */
    protected function getModuleDirUrl()
    {
        $module_dir = '';
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        return $module_dir;
    }
    
    /*
     * Function for checking SSL
     */
    private function checkSecureUrl()
    {
        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] == 'on') {
                $custom_ssl_var = 1;
            }
        } else if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $custom_ssl_var = 1;
        }
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            return true;
        } else {
            return false;
        }
    }
    
    /*
     * function for Returning the Base URL of the store
     */
    protected function getBaseUrl()
    {
        $module_dir = '';
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ ;
        } else {
            $module_dir = _PS_BASE_URL_ ;
        }
        return $module_dir;
    }

    /*
     * Function for returning all the languages in the system
     */
    public function getAllLanguages()
    {
        return Language::getLanguages(false);
    }

    /*
     * Function for returning the HTML of Helper Form
     */
    public function renderGenericForm($fields_form, $fields_value, $admin_token, $tpl_vars = array())
    {
        $languages = $this->all_languages;
        foreach ($languages as $k => $language) {
            $languages[$k]['is_default'] = ((int) ($language['id_lang'] == $this->context->language->id));
        }
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->languages = $languages;
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $this->fields_form = array();
        $helper->token = $admin_token;
        $helper->tpl_vars = array_merge(array(
                'fields_value' => $fields_value
            ), $tpl_vars);

        return $helper->generateForm($fields_form);
    }
    
    /*
     * Function for returning the absolute path of the module directory
     */
    protected function getKbModuleDir()
    {
        return _PS_MODULE_DIR_.$this->module->name.'/';
    }
    
    /*
     * Default function, used here to set required smarty variables
     */
    public function initContent()
    {
        if (isset($this->context->cookie->kb_redirect_error)) {
            $this->errors[] = $this->context->cookie->kb_redirect_error;
            unset($this->context->cookie->kb_redirect_error);
        }

        if (isset($this->context->cookie->kb_redirect_success)) {
            $this->confirmations[] = $this->context->cookie->kb_redirect_success;
            unset($this->context->cookie->kb_redirect_success);
        }
        parent::initContent();
    }
    
    public function init()
    {
        parent::init();
    }

    public function initProcess()
    {
        return parent::initProcess();
    }
    
    public function renderForm()
    {
        return parent::renderForm();
    }
    
    /*
     * Default function, used here to include JS/CSS files for the module.
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addCSS($this->getKbModuleDir().'views/css/admin/kb_admin.css');
        $this->addJS($this->getKbModuleDir().'views/js/velovalidation.js');
        $this->addJS($this->getKbModuleDir().'views/js/admin/kb_admin.js');
        $this->addJS($this->getKbModuleDir().'views/js/admin/validation_admin.js');
    }
}
