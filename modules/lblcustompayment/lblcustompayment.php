<?php
/**
* 2019-2021 Labelgrup
*
* NOTICE OF LICENSE
*
* READ ATTACHED LICENSE.TXT
*
*  @author    Manel Alonso <malonso@labelgrup.com>
*  @copyright 2019-2021 Labelgrup
*  @license   LICENSE.TXT
*/

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Lblcustompayment extends PaymentModule
{
    public function __construct()
    {
        $this->name = 'lblcustompayment';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = array('min' => '1.7.5.0', 'max' => _PS_VERSION_);
        $this->author = 'LabelGrup Networks S.L.';
        $this->controllers = array('payment', 'validation');
        $this->is_eu_compatible = 1;

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('LabelGrup Custom Payment', [], 'Modules.Lblcustompayment.displayName');
        $this->description = $this->trans('This addon simulates a payment gateway', [], 'Modules.Lblcustompayment.Description');
        $this->confirmUninstall = $this->trans('Do you want to uninstall this addon?', [], 'Modules.Lblcustompayment.Uninstall');
    }

    public function install()
    {
        if (!parent::install() || !$this->registerHook('paymentReturn') || !$this->registerHook('paymentOptions')) {
            return false;
        }
        // Creamos el método de pago por defecto
        Configuration::updateValue('LBL_CUSTOMPAY_DEFAULT', Configuration::get('PS_OS_PAYMENT'));
        Configuration::updateValue('LBL_CUSTOMPAY_NAME', $this->trans('Payment name', [], 'Modules.Lblcustompayment.defaultName'));

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        // Borramos la configuracion
        Configuration::deleteByName('LBL_CUSTOMPAY_DEFAULT');

        return true;
    }

    /**
     * Show the payment method in checkout
     * Replaces "payment" controller
     */
    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }

        if (!$this->checkCurrency($params['cart'])) {
            return;
        }

        $newOption = new PaymentOption();
        $newOption
            ->setModuleName($this->name)
            ->setCallToActionText(Configuration::get('LBL_CUSTOMPAY_NAME'))
            ->setAction($this->context->link->getModuleLink($this->name, 'validation', [], true));
        
        $payment_options = [
            $newOption,
        ];

        return $payment_options;
    }

    /**
     * Hook for payment validation
     */
    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }

        $this->smarty->assign(array(
            'shop_name' => $this->context->shop->name,
            'total' => Tools::displayPrice(
                $params['order']->getOrdersTotalPaid(),
                new Currency($params['order']->id_currency),
                false
            ),
            'reference' => $params['order']->reference,
            'contact_url' => $this->context->link->getPageLink('contact', true)
        ));

        return $this->fetch('module:lblcustompayment/views/templates/hook/payment_return.tpl');
    }

    /**
     * Function to check currency of the cart
     */
    public function checkCurrency($cart)
    {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);

        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /*
     * Show the configuration options and message
     */
    public function getContent()
    {
        $message = '';
        if (((bool)Tools::isSubmit('saveOptions')) == true) {
            $message = $this->postProcess();
            $message .= $this->displayConfirmation($this->trans('Options saved successfully!', [], 'Modules.Lblcustompayment.OptionsOK'));
        }

        return $message . $this->renderForm();
    }
    
    /*
     * Render the config HF
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'saveOptions';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /*
     * Get the config
     */
    protected function getConfigForm()
    {
        // Obtenemos los estado de pedido
        $order_statuses = OrderState::getOrderStates($this->context->language->id);
        return array(
            'form' => array(
            'legend' => array(
                'title' => $this->trans('Setup', [], 'Modules.Lblcustompayment.Setup'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->trans('Default state for the processed order', [], 'Modules.Lblcustompayment.defaultStateOrder'),
                        'name' => 'LBL_CUSTOMPAY_DEFAULT',
                        'required' => true,
                        'desc' => $this->trans('This will be the status after order confirmation.', [], 'Modules.Lblcustompayment.descDefaultStateOrder'),
                        'options' => array(
                            'query' => $order_statuses,
                            'id' => 'id_order_state',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Payment name', [], 'Modules.Lblcustompayment.paymentName'),
                        'name' => 'LBL_CUSTOMPAY_NAME',
                        'required' => true,
                        'desc' => $this->trans('The custom text to be shown on the payment.', [], 'Modules.Lblcustompayment.descPaymentName')
                    ),
                ),
                'submit' => array(
                'title' => $this->trans('Save', [], 'Modules.Lblcustompayment.Save'),
                ),
            ),
        );
    }
    
    /*
     * Save the options
     */
    protected function postProcess()
    {
        $order_state = (int)Tools::getValue('LBL_CUSTOMPAY_DEFAULT');
        $payment_name = trim(Tools::getValue('LBL_CUSTOMPAY_NAME'));

        Configuration::updateValue('LBL_CUSTOMPAY_DEFAULT', $order_state);
        Configuration::updateValue('LBL_CUSTOMPAY_NAME', $payment_name);
    }

    /*
     * Get the default values
     */
    protected function getConfigFormValues()
    {
        return array(
            'LBL_CUSTOMPAY_DEFAULT' => (int)Configuration::get('LBL_CUSTOMPAY_DEFAULT'),
            'LBL_CUSTOMPAY_NAME' => Configuration::get('LBL_CUSTOMPAY_NAME')
        );
    }
}
