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

class LblcustompaymentValidationModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $cart = $this->context->cart;
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 ||
                    $cart->id_address_invoice == 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        // Check that this payment option is still available in case the customer changed his
        // address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == $this->module->name) {
                $authorized = true;
                break;
            }
        }
        if (!$authorized) {
            die($this->module->l('This payment method is not available.', [], $this->module->name));
        }

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $currency = $this->context->currency;
        $total = (float)$cart->getOrderTotal(true, Cart::BOTH);

        $this->module->validateOrder(
            $cart->id,
            (int)Configuration::get('LBL_CUSTOMPAY_DEFAULT'),
            $total,
            Configuration::get('LBL_CUSTOMPAY_NAME'),
            null,
            null,
            (int)$currency->id,
            false,
            $customer->secure_key
        );
        
        Tools::redirect('index.php?controller=order-confirmation&id_cart=' . (int)$cart->id . '&id_module='
            . (int)$this->module->id . '&id_order=' . (int)$this->module->currentOrder . '&key='
            . $customer->secure_key);
    }
}
