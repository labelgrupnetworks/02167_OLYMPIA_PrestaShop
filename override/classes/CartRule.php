<?php

class CartRule extends CartRuleCore
{
    /*
    * module: gformbuilderpro
    * date: 2022-11-08 16:03:44
    * version: 2.0.1
    */
    public static function autoAddToCart(Context $context = null)
    {
        
        parent::autoAddToCart($context);
        if((int)Tools::getValue('discounts_value') > 0){
            if(Module::isInstalled('gformbuilderpro') && Module::isEnabled('gformbuilderpro')){
                $moduleObj = Module::getInstanceByName('gformbuilderpro');
                $discounts = array(
                    'type'  => (int)Tools::getValue('discounts_type'),
                    'tax'   => (int)Tools::getValue('discounts_tax'),
                    'value' => (int)Tools::getValue('discounts_value'),
                );
                if($context == null) $context = Context::getContext();
                $moduleObj->createDiscount((int)Tools::getValue('id_product'), (int)Tools::getValue('ipa'), $discounts, $context);
            }
        }
    }
    /*
    * module: onepagecheckoutps
    * date: 2022-11-10 12:21:34
    * version: 4.1.5
    */
    public function checkValidity(
        Context $context,
        $alreadyInCart = false,
        $display_error = true,
        $check_carrier = true,
        $useOrderPrices = false
    ) {
        if (!CartRule::isFeatureActive()) {
            return false;
        }
        $opc = Module::getInstanceByName('onepagecheckoutps');
        if (Validate::isLoadedObject($opc) && $opc->core->isModuleActive($opc->name)) {
            if (!Configuration::get('OPC_ALLOW_DISCOUNTS')) {
                $cart = new Cart($context->cart->id);
                $products = $cart->getProducts();
                $is_ok = true;
                foreach ($products as $product) {
                    if ($product['reduction_applies']) {
                        $is_ok = false;
                        break;
                    }
                }
                if (!$is_ok) {
                    return (!$display_error) ? false : $opc->allow_discounts_error;
                }
            }
        }
        if (version_compare(_PS_VERSION_, '1.7.7.5', '>=')) {
            return parent::checkValidity($context, $alreadyInCart, $display_error, $check_carrier, $useOrderPrices);
        } else {
            return parent::checkValidity($context, $alreadyInCart, $display_error, $check_carrier);
        }
    }
}