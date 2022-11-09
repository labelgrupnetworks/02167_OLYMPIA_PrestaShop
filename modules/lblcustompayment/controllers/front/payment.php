<?php
/**
 * 2019-2021 Labelgrup
 * !!!! DEPRECATED FOR 1.7 !!!!
 *
 * NOTICE OF LICENSE
 *
 * READ ATTACHED LICENSE.TXT
 *
 *  @author    Manel Alonso <malonso@labelgrup.com>
 *  @copyright 2019-2021 Labelgrup
 *  @license   LICENSE.TXT
 */

class LblcustompaymentPaymentModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $cart = $this->context->cart;
        if (!$this->module->checkCurrency($cart)) {
            Tools::redirect('index.php?controller=order');
        }

        $total = sprintf(
            $this->module->l('%1$s (tax incl.)', [], $this->module->name),
            Tools::displayPrice($cart->getOrderTotal(true, Cart::BOTH))
        );

        $this->context->smarty->assign(array(
            'back_url' => $this->context->link->getPageLink('order', true, null, "step=3"),
            'confirm_url' => $this->context->link->getModuleLink($this->module->name, 'validation', [], true),
            //'image_url' => $this->module->getPathUri() . 'payment.jpg',
            'payment_name' => Configuration::get('LBL_CUSTOMPAY_NAME'),
            'cust_currency' => $cart->id_currency,
            'currencies' => $this->module->getCurrency((int)$cart->id_currency),
            'total' => $total,
            'this_path' => $this->module->getPathUri(),
            'this_path_ssl' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->module->name . '/'
        ));

        $this->setTemplate('payment.tpl');
    }
}
