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

class LRPAdminConfigRulesController extends LRPControllerCore
{
    protected $sibling;

    private $route = 'lrpadminconfigrulescontroller';

    public function __construct(&$sibling = null)
    {
        parent::__construct($sibling);
        if ($sibling !== null) {
            $this->sibling = &$sibling;
        }
    }

    /**
     * Render the main template which houses the add form and rules list
     */
    public function render()
    {
        return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/config/rules.tpl');
    }

    /**
     * Render Add form
     * @return string
     */
    public function renderAddForm()
    {
        $selected_products = array();
        if ((int)Tools::getValue('id_lrp_rule') > 0) {
            $lrp_rule = new LRPRuleModel((int)Tools::getValue('id_lrp_rule'));
        }

        $helper = new HelperForm();
        $this->setupHelperConfigForm($helper, $this->route, 'processrule');

        $product_search_widget = new MPProductSearchWidgetController('lrpproduct1', $this->sibling);

        if (!empty($lrp_rule->id_products)) {
            $selected_products = $product_search_widget->constructSelectedProductsArray($lrp_rule->id_products);
        }
        $selected_cats = array();
        if (!empty($lrp_rule->id_categories)) {
            $selected_cats = explode(',', $lrp_rule->id_categories);
        }

        $fields_form = array();
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->sibling->l('General', $this->route),
                'icon' => 'icon-question'
            ),
            'input' => array(
                array(
                    'name' => 'id_lrp_rule',
                    'type' => 'hidden',
                    'class' => 'fixed-width-lg',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->sibling->l('Rule Enabled?', $this->route),
                    'name' => 'enabled',
                    'options' => array(
                        'query' => array(
                            array(
                                'enabled' => 0,
                                'name' => 'No'
                            ),
                            array(
                                'enabled' => 1,
                                'name' => 'Yes'
                            )
                        ),
                        'id' => 'enabled',
                        'name' => 'name',
                    )
                ),
                array(
                    'name' => 'name',
                    'type' => 'text',
                    'label' => $this->sibling->l('Name', $this->route),
                    'class' => 'fixed-width-lg',
                ),
                array(
                    'name' => 'operator',
                    'type' => 'select',
                    'label' => $this->sibling->l('Impact operation on points', $this->route),
                    'class' => 'fixed-width-xs',
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' => '+',
                                'name' => $this->sibling->l('Add points', $this->route)
                            ),
                            array(
                                'id_option' => '*',
                                'name' => $this->sibling->l('Multiply Points', $this->route)
                            ),
                            array(
                                'id_option' => '=',
                                'name' => $this->sibling->l(' Set by exact value', $this->route)
                            )
                        ),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
                    'name' => 'points',
                    'type' => 'text',
                    'label' => $this->sibling->l('Impact value on points', $this->route),
                    'class' => 'fixed-width-xs',
                ),
                array(
                    'name' => 'date_start',
                    'type' => 'date',
                    'label' => $this->sibling->l('Start Date', $this->route)
                ),
                array(
                    'name' => 'date_end',
                    'type' => 'date',
                    'label' => $this->sibling->l('End Date', $this->route)
                ),
                array(
                    'type' => 'categories',
                    'label' => $this->sibling->l('Product Category', $this->route),
                    'desc' => $this->sibling->l('Product Category.', $this->route),
                    'name' => 'category',
                    'tree' => array(
                        'id' => 'category',
                        'use_checkbox' => true,
                        'selected_categories' => $selected_cats
                        //'selected_categories' => array((int)Configuration::get('category')),
                    )
                ),
                array(
                    'name' => '',
                    'type' => 'html',
                    'label' => $this->sibling->l('Product Restrictions', $this->route),
                    'class' => 'fixed-width-xl',
                    'required' => true,
                    'html_content' => $product_search_widget->render($selected_products),
                    'size' => 255
                )
            ),
            'submit' => array(
                'title' => $this->sibling->l('Save', $this->route),
                'class' => 'btn btn-default pull-right'
            )
        );

        $helper->fields_value['name'] = '';
        $helper->fields_value['points'] = '';
        $helper->fields_value['id_lrp_rule'] = 0;
        $helper->fields_value['date_start'] = '';
        $helper->fields_value['date_end'] = '';
        $helper->fields_value['operator'] = '';
        $helper->fields_value['enabled'] = 0;

        if (!empty($lrp_rule->id_lrp_rule)) {
            $helper->fields_value['id_lrp_rule'] = $lrp_rule->id_lrp_rule;
            $helper->fields_value['name'] = $lrp_rule->name;
            $helper->fields_value['operator'] = $lrp_rule->operator;
            $helper->fields_value['points'] = $lrp_rule->points;
            $helper->fields_value['enabled'] = $lrp_rule->enabled;
            $helper->fields_value['date_start'] = $lrp_rule->date_start;
            $helper->fields_value['date_end'] = $lrp_rule->date_end;
        }

        return $helper->generateForm($fields_form);
    }

    /**
     * Render the list of rules
     */
    public function renderList()
    {
        $lrp_rules_model = new LRPRuleModel();
        $lrp_rules = $lrp_rules_model->getAll();

        Context::getContext()->smarty->assign(array(
            'lrp_rules' => $lrp_rules,
        ));
        return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/config/_partial_rules_list.tpl');
    }

    public function process()
    {
        if ((int)Tools::getValue('id_lrp_rule') > 0) {
            $lrp_rule = new LRPRuleModel(Tools::getValue('id_lrp_rule'));
        } else {
            $lrp_rule = new LRPRuleModel();
            $lrp_rule->id_shop = (int)Context::getContext()->shop->id;
        }
        $lrp_rule->enabled = (int)Tools::getValue('enabled');
        $lrp_rule->name = pSQL(Tools::getValue('name'));
        $lrp_rule->operator = pSQL(Tools::getValue('operator'));
        $lrp_rule->points = (int)Tools::getValue('points');
        $lrp_rule->date_start = pSQL(Tools::getValue('date_start'));
        $lrp_rule->date_end = pSQL(Tools::getValue('date_end'));
        $lrp_rule->id_categories = pSQL(implode(',', Tools::getValue('category')));
        $lrp_rule->id_products = pSQL(Tools::getValue('id_product'));
        $lrp_rule->save();
    }

    /**
     * Delete a rule
     */
    public function processDelete()
    {
        if ((int)Tools::getValue('id_lrp_rule') == 0) {
            return false;
        }
        $lrp_rule = new LRPRuleModel(Tools::getValue('id_lrp_rule'));
        $lrp_rule->delete();
    }

    public function route()
    {
        switch (Tools::getValue('action')) {
            case 'renderaddform':
                die($this->renderAddForm());

            case 'renderlist':
                die($this->renderList());

            case 'process':
                die($this->process());

            case 'processdelete':
                die($this->processDelete());

            default:
                die($this->render());
        }
    }
}
