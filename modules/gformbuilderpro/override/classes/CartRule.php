<?php
/**
* Override CartRule class
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2018 Globo ., Jsc
* @license   please read license in file license.txt
* @link      http://www.globosoftware.net
*/

class CartRule extends CartRuleCore
{
    public static function autoAddToCart(Context $context = null, bool $useOrderPrices = false)
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
}