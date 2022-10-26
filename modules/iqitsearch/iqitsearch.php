<?php
/**
 * 2017 IQIT-COMMERCE.COM
 *
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement
 *
 *  @author    IQIT-COMMERCE.COM <support@iqit-commerce.com>
 *  @copyright 2017 IQIT-COMMERCE.COM
 *  @license   Commercial license (You can not resell or redistribute this software.)
 *
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class IqitSearch extends Module implements WidgetInterface
{
    private $templateFile;

    public function __construct()
    {
        $this->name = 'iqitsearch';
        $this->version = '1.1.3';
        $this->author = 'iqit-commerce.com';
        $this->need_instance = 0;
        $this->controllers = array('searchiqit');
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('IQITSEARCH');
        $this->description = $this->l('Adds a quick search field to your website.');

        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);

        $this->templateFile =  'module:' . $this->name . '/views/templates/hook/'.$this->name.'.tpl';
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('displaySearch')) {
            return false;
        }

        Configuration::updateValue('iqitsearch_typed', '1');
        return true;
    }

    public function isUsingNewTranslationSystem()
    {
        return false;
    }

    public function getContent()
    {
        $output = '';

        if (Shop::getContext() == Shop::CONTEXT_GROUP || Shop::getContext() == Shop::CONTEXT_ALL) {
            return $this->getWarningMultishopHtml();
        }

        if (Tools::isSubmit('submitModule')) {
            Configuration::updateValue('iqitsearch_type', Tools::getValue('iqitsearch_type'));
            $output .= $this->displayConfirmation($this->l('Configuration updated'));
        }
        $output .= $this->renderForm();
        return $output;
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Type'),
                        'name' => 'iqitsearch_type',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 1,
                                    'name' => $this->l('Products + brands + posts'),
                                ),
                                array(
                                    'id_option' => 2,
                                    'name' => $this->l('Products + brands'),
                                ),
                                array(
                                    'id_option' => 3,
                                    'name' => $this->l('Products + posts'),
                                ),
                                array(
                                    'id_option' => 4,
                                    'name' => $this->l('Products only'),
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name',
                        ),
                    ),
                ),
                'submit' => array(
                    'name' => 'submitModule',
                    'title' => $this->l('Save'),
                ),
            ),
        );

        if (Shop::isFeatureActive()) {
            $fields_form['form']['description'] = $this->l('The modifications will be applied to') . ' ' . (Shop::getContext() == Shop::CONTEXT_SHOP ? $this->l('shop') . ' ' . $this->context->shop->name : $this->l('all shops'));
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'iqitsearch_type' => Tools::getValue('iqitsearch_type', Configuration::get('iqitsearch_type')),
        );
    }


    protected function getWarningMultishopHtml()
    {
        if (Shop::getContext() == Shop::CONTEXT_GROUP || Shop::getContext() == Shop::CONTEXT_ALL) {
            return '<p class="alert alert-warning">' .
                $this->l('You cannot manage module from a "All Shops" or a "Group Shop" context, select directly the shop you want to edit') .
                '</p>';
        } else {
            return '';
        }
    }




    public function getWidgetVariables($hookName, array $configuration = [])
    {
        $widgetVariables = array(
            'search_controller_url' =>$this->context->link->getModuleLink('iqitsearch','searchiqit'),
            //'search_controller_url' => $this->context->link->getPageLink('search', null, null, null, false, null, true),
        );

        if (!array_key_exists('search_string', $this->context->smarty->getTemplateVars())) {
            $widgetVariables['search_string'] = '';
        }

        return $widgetVariables;
    }

    public function renderWidget($hookName, array $configuration = [])
    {
        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));

        return $this->fetch($this->templateFile);
    }

    public function updateSearchControllers(){

        foreach ($this->controllers as $controller) {
            $page = 'module-' . $this->name . '-' . $controller;
            $result = Db::getInstance()->getValue('SELECT * FROM ' . _DB_PREFIX_ . 'meta WHERE page="' . pSQL($page) . '"');
            if ((int) $result > 0) {
                continue;
            }

            $meta = new Meta();
            $meta->page = $page;
            $meta->configurable = 1;
            $meta->save();
        }
    }
}
