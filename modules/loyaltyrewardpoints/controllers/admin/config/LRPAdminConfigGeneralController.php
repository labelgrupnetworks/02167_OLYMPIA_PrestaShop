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

class LRPAdminConfigGeneralController extends LRPControllerCore
{
    protected $sibling;

    private $route = 'lrpadminconfiggeneralcontroller';

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
     * Generate group of fields for specific currency for the reward configuration
     * @param $currency
     * @param LRPConfigModel $lrp_config
     * @return array
     */
    private function getCurrencyRewardFieldsGroup($currency, LRPConfigModel $lrp_config)
    {
        $array = array();
        $id_default_currency = Configuration::get('PS_CURRENCY_DEFAULT');
        if (!$lrp_config->getAutomaticCurrencySettings() || $id_default_currency == $currency['id_currency']) {
            $array[] = array(
                'label' => $this->sibling->l('Ratio', $this->route),
                'type' => 'text',
                'name' => 'ratio_' . $currency['iso_code'],
                'prefix' => $currency['sign'],
                'suffix' => $this->sibling->l('= 1 reward point.', $this->route),
                'size' => 10,
                'required' => true
            );
            $array[] = array(
                'type' => 'text',
                'label' => $this->sibling->l('1 point =', $this->route),
                'name' => 'point_value_' . $currency['iso_code'],
                'prefix' => $currency['sign'],
                'suffix' => $this->sibling->l('for the discount.', $this->route),
            );
            $array[] = array(
                'type' => 'text',
                'label' => $this->sibling->l('Minimum cart value', $this->route),
                'name' => 'min_cart_value_' . $currency['iso_code'],
                'prefix' => $currency['sign'],
                'suffix' => $this->sibling->l('inc. Tax')
            );
        };

        $array[] = array(
            'type' => 'text',
            'label' => $this->sibling->l('Points for referring customer', $this->route),
            'name' => 'referral_points_' . $currency['iso_code']
        );

        $array[] = array(
            'type' => 'text',
            'label' => $this->sibling->l('Points for friend', $this->route),
            'name' => 'referral_friend_points_' . $currency['iso_code']
        );

        $array[] = array(
            'type' => 'text',
            'label' => $this->sibling->l('Points for Birthday', $this->route),
            'name' => 'birthday_points_' . $currency['iso_code'],
            'hint' => 'Points to reward customers on their Birthday.  Enter 0 to disable this feature'
        );

        $array[] = array(
            'type' => 'text',
            'label' => $this->sibling->l('Minimum Points required for redemption', $this->route),
            'name' => 'min_points_redemption_' . $currency['iso_code']
        );
        return $array;
    }

    private function renderOrderStates($order_states)
    {
        Context::getContext()->smarty->assign(array(
        ));
        return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/config/_orderstates.tpl');
    }

    public function render()
    {
        $order_states = OrderState::getOrderStates($this->context->language->id);

        $order_states[] = array(
            'name' => "Any Order State flagged as 'Paid'",
            'id_order_state' => -1
        );

        $fields_form = array();
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->sibling->l('General', $this->route),
                'icon' => 'icon-question'
            ),
            'input' => array(
                array(
                    'type' => 'checkbox',
                    'expand' => array(
                        'default' => 'show',
                        'show' => array(
                            'icon' => 'collapse',
                            'text' => $this->sibling->l('Choose order states', $this->route)
                        ),
                        'hide' => array(
                            'icon' => 'collapse',
                            'text' => $this->sibling->l('Hide order states', $this->route)
                        )
                    ),
                    'label' => $this->sibling->l('Points are awarded when the order is', $this->route),
                    'name' => 'id_order_state_validation',
                    'values' => array(
                        'query' => $order_states,
                        'id' => 'id_order_state',
                        'name' => 'name',
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->sibling->l('Points are cancelled when the order is', $this->route),
                    'name' => 'id_order_state_cancel',
                    'options' => array(
                        'query' => $order_states,
                        'id' => 'id_order_state',
                        'name' => 'name',
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->sibling->l('Customer Referral Enabled?', $this->route),
                    'name' => 'referral_enabled',
                    'options' => array(
                        'query' => array(
                            array(
                                'referral_enabled' => 0,
                                'name' => 'No'
                            ),
                            array(
                                'referral_enabled' => 1,
                                'name' => 'Yes'
                            )
                        ),
                        'id' => 'referral_enabled',
                        'name' => 'name',
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->sibling->l('Points expire after', $this->route),
                    'name' => 'points_expire_days',
                    'prefix' => $this->sibling->l('days', $this->route),
                    'suffix' => $this->sibling->l('enter 0 for no expiration', $this->route),
                    'class' => 'col-md-4',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->sibling->l('Max redemption limit', $this->route),
                    'name' => 'max_redemption_limit_percentage',
                    'suffix' => $this->sibling->l('% of cart total (0 for no limit)', $this->route),
                    'desc' => $this->sibling->l('Limit the monetary value of the points redeemed to a percentage of the cart total', $this->route),
                    'class' => 'col-md-4',
                )
            )
        );

        $lrp_config = new LRPConfigModel(0, 0, Context::getContext()->shop->id);
        $id_default_currency = Configuration::get('PS_CURRENCY_DEFAULT');
        $currencies = Currency::getCurrencies(false, true, true);
        $i = 1;

        // move default currency to the beginning for rendering
        foreach ($currencies as $key => $currency) {
            if ($currency['id_currency'] == $id_default_currency) {
                $currencies = array_merge(array($key => $currency) + $currencies);
            }
        }

        foreach ($currencies as $currency) {
            $fields_form[$i]['form'] = array(
                'legend' => array(
                    'title' => $this->sibling->l('Reward Structure for ', $this->route).$currency['iso_code'],
                    'icon' => 'icon-question'
                ),
                'input' => $this->getCurrencyRewardFieldsGroup($currency, $lrp_config)
            );
            $i++;
        }

        $fields_form[$i]['form'] = array(
            'submit' => array(
                'title' => $this->sibling->l('Save', $this->route),
                'class' => 'btn btn-default pull-right')
        );

        $lrp_config = new LRPConfigModel(0, Tools::getValue('id_group'), Context::getContext()->shop->id);
        $helper = new HelperForm();
        $this->setupHelperConfigForm($helper, $this->route, 'process');

        $id_order_state_validation = (Tools::getValue('id_order_state_validation') != '' ? Tools::getValue('id_order_state_validation') : $lrp_config->getIdOrderStateValidation());
        $array_id_order_state_validation = explode(',', $id_order_state_validation);

        foreach ($array_id_order_state_validation as $key => $id) {
            $helper->fields_value['id_order_state_validation_'.$id] = 'on';
        }

        $helper->fields_value['discount_mode'] = (Tools::getValue('discount_mode') != '' ? Tools::getValue('discount_mode') : $lrp_config->getDiscountMode());
        $helper->fields_value['id_order_state_cancel'] = (Tools::getValue('id_order_state_cancel') != '' ? Tools::getValue('id_order_state_cancel') : $lrp_config->getIdOrderStateCancel());
        $helper->fields_value['referral_enabled'] = (Tools::getValue('referral_enabled') != '' ? Tools::getValue('referral_enabled') : $lrp_config->getReferralEnabled());
        $helper->fields_value['points_expire_days'] = (Tools::getValue('points_expire_days') != '' ? Tools::getValue('points_expire_days') : $lrp_config->getPointsExpireDays());
        $helper->fields_value['max_redemption_limit_percentage'] = (Tools::getValue('max_redemption_limit_percentage') != '' ? Tools::getValue('max_redemption_limit_percentage') : $lrp_config->getMaxRedemptionLimitPercentage());

        foreach ($currencies as $currency) {
            $lrp_config = new LRPConfigModel($currency['iso_code'], Tools::getValue('id_group'), Context::getContext()->shop->id);
            $helper->fields_value['ratio_' . $currency['iso_code']] = (Tools::getValue('ratio_' . $currency['iso_code']) != '' ? Tools::getValue('ratio_' . $currency['iso_code']) : $lrp_config->getRatio());
            $helper->fields_value['point_value_' . $currency['iso_code']] = (Tools::getValue('point_value_' . $currency['iso_code']) != '' ? Tools::getValue('point_value_' . $currency['iso_code']) : $lrp_config->getPointValue());
            $helper->fields_value['referral_points_' . $currency['iso_code']] = (Tools::getValue('referral_points_' . $currency['iso_code']) != '' ? Tools::getValue('referral_points_' . $currency['iso_code']) : $lrp_config->getReferralPoints());
            $helper->fields_value['referral_friend_points_' . $currency['iso_code']] = (Tools::getValue('referral_friend_points_' . $currency['iso_code']) != '' ? Tools::getValue('referral_friend_points_' . $currency['iso_code']) : $lrp_config->getReferralFriendPoints());
            $helper->fields_value['birthday_points_' . $currency['iso_code']] = (Tools::getValue('birthday_points_' . $currency['iso_code']) != '' ? Tools::getValue('birthday_points_' . $currency['iso_code']) : $lrp_config->getBirthdayPoints());
            $helper->fields_value['min_cart_value_' . $currency['iso_code']] = (Tools::getValue('min_cart_value_' . $currency['iso_code']) != '' ? Tools::getValue('min_cart_value_' . $currency['iso_code']) : $lrp_config->getMinCartValue());
            $helper->fields_value['min_points_redemption_' . $currency['iso_code']] = (Tools::getValue('min_points_redemption_' . $currency['iso_code']) != '' ? Tools::getValue('min_points_redemption_' . $currency['iso_code']) : $lrp_config->getMinPointsRedemption());
        }
        return $helper->generateForm($fields_form);
    }

    public function process()
    {
        $output = null;
        $id_shop = Context::getContext()->shop->id;

        if (Tools::isSubmit('submit' . $this->sibling->name)) {
            $currencies = Currency::getCurrencies(false, true, true);

            if (!Validate::isInt(Tools::getValue('points_expire_days'))) {
                $output .= $this->displayError($this->sibling->l('Invalid value for Points Expiry Days', $this->route));
            }

            if (!Validate::isInt(Tools::getValue('max_redemption_limit_percentage'))) {
                $output .= $this->displayError($this->sibling->l('Invalid value for Max Redemption Limit', $this->route));
            }

            $lrp_config = new LRPConfigModel(0, 0, Context::getContext()->shop->id);
            $id_default_currency = Configuration::get('PS_CURRENCY_DEFAULT');

            foreach ($currencies as $currency) {
                if (!$lrp_config->getAutomaticCurrencySettings()) {
                    if (!Validate::isFloat(Tools::getValue('ratio_' . $currency['iso_code']))) {
                        $output .= $this->displayError($this->sibling->l('Invalid value for Ratio (' . $currency['iso_code'] . ')', $this->route));
                    }

                    if (!Validate::isFloat(Tools::getValue('point_value_' . $currency['iso_code']))) {
                        $output .= $this->displayError($this->sibling->l('Invalid value for Point Value (' . $currency['iso_code'] . ')', $this->route));
                    }
                } elseif ($lrp_config->getAutomaticCurrencySettings() && $id_default_currency == $currency['id_currency']) {
                    if (!Validate::isFloat(Tools::getValue('ratio_' . $currency['iso_code']))) {
                        $output .= $this->displayError($this->sibling->l('Invalid value for Ratio (' . $currency['iso_code'] . ')', $this->route));
                    }

                    if (!Validate::isFloat(Tools::getValue('point_value_' . $currency['iso_code']))) {
                        $output .= $this->displayError($this->sibling->l('Invalid value for Point Value (' . $currency['iso_code'] . ')', $this->route));
                    }
                }
            }

            if ($output == '') {
                $lrp_config = new LRPConfigModel(0, Tools::getValue('id_group'), Context::getContext()->shop->id);
                $array_id_order_state_validation= array();
                foreach (Tools::getAllValues() as $key => $value) {
                    if (strpos($key, 'id_order_state_validation_') !== false) {
                        $id = str_replace('id_order_state_validation_', '', $key);
                        $array_id_order_state_validation[] = $id;
                    }
                }
                $list_id_order_state_validation = implode(',', $array_id_order_state_validation);

                //Configuration::updateValue('lrp_discount_mode', (int)Tools::getValue('discount_mode'), false, null, (int)$id_shop);
                Configuration::updateValue('lrp_id_order_state_validation', pSQL($list_id_order_state_validation), false, null, (int)$id_shop);
                Configuration::updateValue('lrp_id_order_state_cancel', (int)Tools::getValue('id_order_state_cancel'), false, null, (int)$id_shop);
                Configuration::updateValue('lrp_referral_enabled', (int)Tools::getValue('referral_enabled'), false, null, (int)$id_shop);
                Configuration::updateValue('lrp_points_expire_days', (int)Tools::getValue('points_expire_days'), false, null, (int)$id_shop);
                Configuration::updateValue('lrp_max_redemption_limit_percentage', (int)Tools::getValue('max_redemption_limit_percentage'), false, null, (int)$id_shop);

                $lrp_config->update('lrp_discount_mode', (int)Tools::getValue('discount_mode'), false, $id_shop);
                $lrp_config->update('lrp_id_order_state_validation', pSQL($list_id_order_state_validation), false, $id_shop);
                $lrp_config->update('lrp_id_order_state_cancel', (int)Tools::getValue('id_order_state_cancel'), false, $id_shop);
                $lrp_config->update('lrp_referral_enabled', (int)Tools::getValue('referral_enabled'), false, $id_shop);
                $lrp_config->update('lrp_points_expire_days', (int)Tools::getValue('points_expire_days'), false, $id_shop);
                $lrp_config->update('lrp_max_redemption_limit_percentage', (int)Tools::getValue('max_redemption_limit_percentage'), false, $id_shop);

                foreach ($currencies as $currency) {
                    $lrp_config = new LRPConfigModel($currency['iso_code'], Tools::getValue('id_group'), Context::getContext()->shop->id);
                    $lrp_config->update('lrp_ratio', (float)Tools::getValue('ratio_' . $currency['iso_code']), true, $id_shop);
                    $lrp_config->update('lrp_point_value', (float)Tools::getValue('point_value_' . $currency['iso_code']), true, $id_shop);
                    $lrp_config->update('lrp_referral_points', (int)Tools::getValue('referral_points_' . $currency['iso_code']), true, $id_shop);
                    $lrp_config->update('lrp_referral_friend_points', (int)Tools::getValue('referral_friend_points_' . $currency['iso_code']), true, $id_shop);
                    $lrp_config->update('lrp_birthday_points', (int)Tools::getValue('birthday_points_' . $currency['iso_code']), true, $id_shop);
                    $lrp_config->update('lrp_min_cart_value', (float)Tools::getValue('min_cart_value_' . $currency['iso_code']), true, $id_shop);
                    $lrp_config->update('lrp_min_points_redemption', (int)Tools::getValue('min_points_redemption_' . $currency['iso_code']), true, $id_shop);
                }
            }
        }
        return $output . $this->render();
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
