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

class LRPAdminConfigMigrationController extends LRPControllerCore
{
    protected $sibling;

    private $route = 'lrpadminconfigmigrationcontroller';

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
            Context::getContext()->controller->addJS($this->sibling->_path . 'views/js/admin/config/migration/LRPAdminConfigMigrationPsloyaltyrewardsController.js');
        }
    }

    public function migrate()
    {
        $migration_controller = new LRPMigrationPSLoyalty();
        $migration_controller->route();
        $migration_controller->migrate();
    }

    public function render()
    {
        Context::getContext()->smarty->assign(array(
            'module_ajax_url' => $this->module_ajax_url,
            'module_config_url' => $this->module_config_url,
        ));
        return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/config/migration/main.tpl');
        //return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/config/migration/psloyaltyrewards.tpl');
    }

    public function route()
    {
        switch (Tools::getValue('action')) {
            default:
                return $this->render();
        }
    }
}
