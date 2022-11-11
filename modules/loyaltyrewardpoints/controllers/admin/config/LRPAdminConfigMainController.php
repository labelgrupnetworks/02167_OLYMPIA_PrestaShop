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

class LRPAdminConfigMainController extends LRPControllerCore
{
    protected $sibling;

    public function __construct(&$sibling = null)
    {
        parent::__construct($sibling);
        if ($sibling !== null) {
            $this->sibling = &$sibling;
        }
    }

    public function setMedia()
    {
        if (Tools::getValue('controller') == 'AdminModules' && Tools::getValue('configure') == 'loyaltyrewardpoints') {
            Context::getContext()->controller->addCSS($this->sibling->_path.'views/css/lib/tools.css');
            Context::getContext()->controller->addCSS($this->sibling->_path.'views/css/lib/popup.css');
            Context::getContext()->controller->addCSS($this->sibling->_path.'views/css/lib/mpproductsearchwidget.css');
            Context::getContext()->controller->addCSS($this->sibling->_path.'views/css/admin/admin.css');

            Context::getContext()->controller->addJquery();
            //Context::getContext()->controller->addJS($this->getAdminWebPath().'/themes/new-theme/public/bundle.js');
            Context::getContext()->controller->addJS(_PS_BO_ALL_THEMES_DIR_ . 'default/js/tree.js');
            Context::getContext()->controller->addJS($this->sibling->_path . 'views/js/lib/popup.js');
            Context::getContext()->controller->addJS($this->sibling->_path . 'views/js/lib/tools.js');
            Context::getContext()->controller->addJS($this->sibling->_path . 'views/js/lib/Breadcrumb.js');
            Context::getContext()->controller->addJS($this->sibling->_path . 'views/js/admin/config/LRPAdminConfigGroupsController.js');
            Context::getContext()->controller->addJS($this->sibling->_path . 'views/js/admin/config/LRPAdminConfigGeneralController.js');
            Context::getContext()->controller->addJS($this->sibling->_path . 'views/js/admin/config/LRPAdminConfigInsightsController.js');
            Context::getContext()->controller->addJS($this->sibling->_path . 'views/js/admin/config/LRPAdminConfigRulesController.js');
            Context::getContext()->controller->addJS($this->sibling->_path . 'views/js/lib/mpproductsearchwidget.js');
        }
    }

    public function render()
    {
        Context::getContext()->smarty->assign(array(
            'address_token' => Tools::getValue('token'),
            'module_ajax_url' => $this->module_ajax_url,
            'module_config_url' => $this->module_config_url
        ));
        return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/config/main.tpl');
    }

    public function route()
    {
        switch (Tools::getValue('action')) {
            default:
                return ($this->render());
        }
    }
}
