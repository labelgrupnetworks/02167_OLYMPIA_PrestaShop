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

class LRPAdminConfigGroupsController extends LRPControllerCore
{
    protected $sibling;

    private $route = 'lrpadminconfiggroupscontroller';

    public function __construct(&$sibling = null)
    {
        parent::__construct($sibling);
        if ($sibling !== null) {
            $this->sibling = &$sibling;
        }
    }

    public function setMedia()
    {
    }

    /**
     * render the global config form
     * @return string
     */
    public function renderGlobalForm()
    {
        $id_lang = $this->context->language->id;
        $fields_form = array();
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->sibling->l('General', $this->route),
                'icon' => 'icon-question'
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->sibling->l('Automatic currency points configuration?', $this->route),
                    'name' => 'automatic_currency',
                    'desc' => $this->sibling->l('Choose yes if the points values for all your currencies should be determined automatically based on your default store currency and conversion rate', $this->route),
                    'values' => array(
                        array(
                            'id' => 'automatic_currency_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', array(), 'Admin.Global'),
                        ),
                        array(
                            'id' => 'automatic_currency_off',
                            'value' => 0,
                            'label' => $this->trans('No', array(), 'Admin.Global'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->sibling->l('Loyalty discounts can be combined with other vouchers?', $this->route),
                    'name' => 'discount_combinable',
                    'desc' => $this->sibling->l('Allow customer to redeem other vouchers while loyalty points discount is also active', $this->route),
                    'values' => array(
                        array(
                            'id' => 'discount_combinable_on',
                            'value' => 1,
                            'label' => $this->sibling->l('Yes', $this->route),
                        ),
                        array(
                            'id' => 'discount_combinable_off',
                            'value' => 0,
                            'label' => $this->sibling->l('No', $this->route),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->sibling->l('Send reminder emails for unredeemed points', $this->route),
                    'name' => 'send_point_reminder_emails',
                    'desc' => $this->sibling->l('Choose yes if you would like to send reminder emails for customers who have un-redeemed points.  You can send up to 3 reminder emails.', $this->route),
                    'values' => array(
                        array(
                            'id' => 'send_point_reminder_emails_on',
                            'value' => 1,
                            'label' => $this->sibling->l('Yes', $this->route),
                        ),
                        array(
                            'id' => 'send_point_reminder_emails_off',
                            'value' => 0,
                            'label' => $this->sibling->l('No', $this->route),
                        ),
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->sibling->l('Send first reminder email', $this->route),
                    'name' => 'points_reminder_email_trigger_days_1',
                    'suffix' => $this->sibling->l('days after last order', $this->route),
                    'class' => 'col-md-4',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->sibling->l('First reminder email subject', $this->route),
                    'name' => 'points_reminder_email_subject_1',
                    'lang' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->sibling->l('Send second reminder email', $this->route),
                    'name' => 'points_reminder_email_trigger_days_2',
                    'suffix' => $this->sibling->l('days after last order', $this->route),
                    'class' => 'col-md-4',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->sibling->l('Second reminder email subject', $this->route),
                    'name' => 'points_reminder_email_subject_2',
                    'lang' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->sibling->l('Send third reminder email', $this->route),
                    'name' => 'points_reminder_email_trigger_days_3',
                    'suffix' => $this->sibling->l('days after last order', $this->route),
                    'class' => 'col-md-4',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->sibling->l('Third reminder email subject', $this->route),
                    'name' => 'points_reminder_email_subject_3',
                    'lang' => true
                )
            ),
            'submit' => array(
                'title' => $this->sibling->l('Save', $this->route),
                'class' => 'btn btn-global-save pull-right'
            )
        );

        $languages = $this->context->controller->getLanguages();
        $lrp_config_global = new LRPConfigModel(0, 0, Context::getContext()->shop->id);
        $helper = new HelperForm();
        $this->setupHelperConfigForm($helper, $this->route, 'process');
        $helper->fields_value['automatic_currency'] = $lrp_config_global->getAutomaticCurrencySettings();
        $helper->fields_value['discount_combinable'] = $lrp_config_global->getDiscountCombinable();
        $helper->fields_value['send_point_reminder_emails'] = $lrp_config_global->getSendPointReminderEmails();
        $helper->fields_value['points_reminder_email_trigger_days_1'] = $lrp_config_global->getPointsReminderEmailTriggerDays1();
        $helper->fields_value['points_reminder_email_trigger_days_2'] = $lrp_config_global->getPointsReminderEmailTriggerDays2();
        $helper->fields_value['points_reminder_email_trigger_days_3'] = $lrp_config_global->getPointsReminderEmailTriggerDays3();

        foreach ($languages as $language) {
            $helper->fields_value['points_reminder_email_subject_1'][$language['id_lang']] = $lrp_config_global->getPointsReminderEmailSubject1($language['id_lang']);
            $helper->fields_value['points_reminder_email_subject_2'][$language['id_lang']] = $lrp_config_global->getPointsReminderEmailSubject2($language['id_lang']);
            $helper->fields_value['points_reminder_email_subject_3'][$language['id_lang']] = $lrp_config_global->getPointsReminderEmailSubject3($language['id_lang']);
        }

        $helper->tpl_vars = array(
            'languages' => $languages,
            'id_language' => $id_lang
        );
        return $helper->generateForm($fields_form);
    }

    /**
     * render groups and global config
     * @return mixed
     */
    public function render()
    {
        $customer_groups = Group::getGroups(Context::getContext()->language->id, Context::getContext()->shop->id);
        $cron_url = Context::getContext()->link->getModuleLink('loyaltyrewardpoints', 'cron', array('key' => LRPUtilityHelper::getCronSecureKey()));

        Context::getContext()->smarty->assign(array(
            'customer_groups' => $customer_groups,
            'cron_url' => $cron_url,
            'form_global' => $this->renderGlobalForm(),
            'insights' => (new LRPAdminConfigInsightsController($this->sibling))->render()
        ));
        return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/config/groups.tpl');
    }

    /**
     * process the global options form
     */
    public function processGlobalForm()
    {
        $languages = $this->context->controller->getLanguages();
        $id_shop = Context::getContext()->shop->id;
        $lrp_config = new LRPConfigModel(0, 0, $id_shop);
        $lrp_config->update('lrp_automatic_currency', (int)Tools::getValue('automatic_currency'), false, $id_shop);
        $lrp_config->update('lrp_discount_combinable', (int)Tools::getValue('discount_combinable'), false, $id_shop);
        $lrp_config->update('lrp_send_point_reminder_emails', (int)Tools::getValue('send_point_reminder_emails'), false, $id_shop);
        $lrp_config->update('lrp_points_reminder_email_trigger_days_1', (int)Tools::getValue('points_reminder_email_trigger_days_1'), false, $id_shop);
        $lrp_config->update('lrp_points_reminder_email_trigger_days_2', (int)Tools::getValue('points_reminder_email_trigger_days_2'), false, $id_shop);
        $lrp_config->update('lrp_points_reminder_email_trigger_days_3', (int)Tools::getValue('points_reminder_email_trigger_days_3'), false, $id_shop);

        foreach ($languages as $language) {
            $lrp_config->update('lrp_points_reminder_email_subject_1_' . $language['id_lang'], Tools::getValue('points_reminder_email_subject_1_' . $language['id_lang']), false, $id_shop);
            $lrp_config->update('lrp_points_reminder_email_subject_2_' . $language['id_lang'], Tools::getValue('points_reminder_email_subject_2_' . $language['id_lang']), false, $id_shop);
            $lrp_config->update('lrp_points_reminder_email_subject_3_' . $language['id_lang'], Tools::getValue('points_reminder_email_subject_3_' . $language['id_lang']), false, $id_shop);
        }
    }

    public function route()
    {
        switch (Tools::getValue('action')) {
            case 'processglobalform':
                die($this->processGlobalForm());

            default:
                die($this->render());
        }
    }
}
