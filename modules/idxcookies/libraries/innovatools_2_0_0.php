<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2015 Innova Deluxe SL

 * @license   INNOVADELUXE
 */

class InnovaTools_2_0_0
{
    /* jj */

    public static function convertParamsToXML($varsPay)
    {
        $xml = '';
        if (sizeof($varsPay)) {
            $xml = '';

            foreach ($varsPay as $key => $value) {
                $xml.= '<' . $key . '>' . $value . '</' . $key . '>';
            }
        }
        return $xml;
    }

    //la de la api de redsys
    public static function encrypt3DES($message, $key)
    {
        // Se establece un IV por defecto
        $bytes = array(0, 0, 0, 0, 0, 0, 0, 0); //byte [] IV = {0, 0, 0, 0, 0, 0, 0, 0}
        $iv = implode(array_map("chr", $bytes)); //PHP 4 >= 4.0.2
        // Se cifra
        $ciphertext = mcrypt_encrypt(MCRYPT_3DES, $key, $message, MCRYPT_MODE_CBC, $iv); //PHP 4 >= 4.0.2
        return $ciphertext;
    }

    /* jj */

    public static function getHostSign($key, $datos, $varsPay)
    {
        $key = base64_decode($key);
        $key = self::encrypt3DES($varsPay['DS_MERCHANT_ORDER'], $key);

        $sign = hash_hmac('sha256', $datos, $key, true);
        return base64_encode($sign);
    }
    
    public static function adminTabWrap($module) {
        $ht=Context::getContext()->smarty->fetch(_PS_MODULE_DIR_ .$module->name.'/libraries/admintabwrapinit.tpl');
        foreach($module->innovatabs as $tab) {
            if ($tab['type']=='tab') {
               $active = '';
               if (isset($tab['active']) && $tab['active']) {
                   $active = 'active in';
               }
               Context::getContext()->smarty->assign(array(
                    'activeTab' => $active,
                    'idTab' => $tab['link'],
               ));
               $ht.= Context::getContext()->smarty->fetch(_PS_MODULE_DIR_ .$module->name.'/libraries/admintabwrapmiddle.tpl');
               $ht.= $module->{$tab['link']}();
               $ht.= Context::getContext()->smarty->fetch(_PS_MODULE_DIR_ .$module->name.'/libraries/admintabwrapend.tpl');
            }
        }
        $ht.= Context::getContext()->smarty->fetch(_PS_MODULE_DIR_ .$module->name.'/libraries/admintabwrapend.tpl');
        return $ht;
    }
    
    public static function getIsoLinks($module) {
        $isoLinks=array();
        $language=  Context::getContext()->language;
        if($language->iso_code == 'es') {
            $isoLinks['certified'] = 'https://www.prestashop.com/es/expertos/agencias-web/innovadeluxe';
        } else if ($language->iso_code == 'it') {
            $isoLinks['certified'] = 'https://www.prestashop.com/it/esperti/agenzie-web/innovadeluxe';
        } else {
            $isoLinks['certified'] = 'https://www.prestashop.com/en/experts/web-agencies/innovadeluxe';
        }
        $isAddons = InnovaTools_2_0_0::isAddons($module);
        if($isAddons) {
            $isoLinks['ratings'] = 'https://addons.prestashop.com/'.$language->iso_code.'/ratings.php';
            $isoLinks['ourmodules'] = 'https://addons.prestashop.com/'.$language->iso_code.'/76_innovadeluxe';
            //$isoLinks['ourmodules'] = 'https://addons.prestashop.com/'.$language->iso_code.'/2_community-developer?contributor=73473';
        } else {
            $isoLinks['ratings'] = 'https://www.prestashop.com/';
            $isoLinks['ourmodules'] = 'https://www.prestashop.com/';
        }
        $isoLinks['web'] = 'https://www.prestashop.com/';
        $isoLinks['support'] = 'https://www.prestashop.com/';
        return $isoLinks;
    }
    
    public static function isAddons($module) {
        $isaddons=false;
        if (!method_exists($module, "renderFormLicense")) {
            $isaddons=true;
        }
        return $isaddons;
    }
    
    public static function getVersionTabs($module) {
        foreach($module->innovatabs as $innovatab) {
            $tabDoc = true;
            if($innovatab['type'] == 'doc' && !@file_get_contents($innovatab['link'])) {
                $isaddons=true;
                $filedoc = _PS_MODULE_DIR_.$innovatab['link'];
                if(file_exists($filedoc)) {
                    $innovatab['link'] = _MODULE_DIR_.$innovatab['link'];
                } else {
                    $tabDoc = false;
                }
            }
            $isaddons=InnovaTools_2_0_0::isAddons($module);
            if(!($innovatab['type'] == 'doc' && !$tabDoc) && !(!$isaddons && $innovatab['show']=='addons') && !($isaddons && $innovatab['show']=='whmcs')) {
                $innovatabs[] = $innovatab;
            }
        }
        
        return $innovatabs;
    }
        
}
