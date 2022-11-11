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

class LRPAdminConfigMigrationPSLoyaltyController extends LRPControllerCore
{
    protected $sibling;

    private $route = 'lrpadminconfigmigrationpsloyaltycontroller';

    public function __construct(&$sibling = null)
    {
        parent::__construct($sibling);
        if ($sibling !== null) {
            $this->sibling = &$sibling;
        }
    }

    public function migrate()
    {
        $migration_controller = new LRPMigrationPSLoyalty();
        $migration_controller->route();
        $migration_controller->migrate();
    }

    /**
     * Generate form for selecting reward states
     * @return string
     * @throws PrestaShopDatabaseException
     */
    private function getRewardStatesForm()
    {
        $loyalty_states = LRPMigrationPSLoyalty::getStates(Context::getContext()->language->id);
        $loyalty_states_options = array();
        $fields_form = array();

        foreach ($loyalty_states as $loyalty_state) {
            $loyalty_states_options[] = array(
                'id_check' => $loyalty_state['id_loyalty_state'],
                'name' => $loyalty_state['name']
            );
        }

        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->sibling->l('Reward States', $this->route),
                'icon' => 'icon-question'
            ),
            'input' => array(
                array(
                    'name' => 'reward_states',
                    'type' => 'checkbox',
                    'label' => $this->sibling->l('Select states corresponding to the Reward states', $this->route),
                    'class' => 'fixed-width-xs',
                    'values' => array(
                        'query' => $loyalty_states_options,
                        'id' => 'id_check',
                        'name' => 'name'
                    )
                ),
            ),
        );

        $helper = new HelperForm();
        $this->setupHelperConfigForm($helper, '', 'process');
        return $helper->generateForm($fields_form);
    }

    /**
     * Generate form for selecting redeem states
     * @return string
     * @throws PrestaShopDatabaseException
     */
    private function getRedeemStatesForm()
    {
        $loyalty_states = LRPMigrationPSLoyalty::getStates(Context::getContext()->language->id);
        $loyalty_states_options = array();
        $fields_form = array();

        foreach ($loyalty_states as $loyalty_state) {
            $loyalty_states_options[] = array(
                'id_check' => $loyalty_state['id_loyalty_state'],
                'name' => $loyalty_state['name']
            );
        }

        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->sibling->l('Redeem States', $this->route),
                'icon' => 'icon-question'
            ),
            'input' => array(
                array(
                    'name' => 'redeem_states',
                    'type' => 'checkbox',
                    'label' => $this->sibling->l('Select states corresponding to the Redeem states', $this->route),
                    'class' => 'fixed-width-xs',
                    'values' => array(
                        'query' => $loyalty_states_options,
                        'id' => 'id_check',
                        'name' => 'name'
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->sibling->l('Save', $this->route),
                'class' => 'btn btn-default pull-right'
            )
        );

        $helper = new HelperForm();
        $this->setupHelperConfigForm($helper, '', 'process');
        return $helper->generateForm($fields_form);
    }

    /**
     * Render the forms
     * @return mixed
     * @throws PrestaShopDatabaseException
     */
    public function render()
    {
        Context::getContext()->smarty->assign(array(
            'module_ajax_url' => $this->module_ajax_url,
            'module_config_url' => $this->module_config_url,
            'reward_states_form' => $this->getRewardStatesForm(),
            'redeem_states_form' => $this->getRedeemStatesForm()
        ));
        return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/config/migration/psloyaltyrewards.tpl');
    }

    public function process()
    {
        $reward_states = array();
        $redeem_states = array();

        foreach (Tools::getAllValues() as $key => $value) {
            $parts = explode('_', $key);
            if (strpos($key, "reward_states") === 0 && $value == 'on') {
                $reward_states[] = $parts[2];
            }
            if (strpos($key, "redeem_states") === 0 && $value == 'on') {
                $redeem_states[] = $parts[2];
            }
        }

        $migration_helper = new LRPMigrationPSLoyalty();
        $migration_helper->redeem_states = $redeem_states;
        $migration_helper->reward_states = $reward_states;
        $migration_helper->migrate();
    }

    public function route()
    {
        switch (Tools::getValue('action')) {
            case 'process':
                die($this->process());

            default:
                die($this->render());
        }
    }
}
