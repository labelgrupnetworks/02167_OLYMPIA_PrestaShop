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

class LRPTranslationHelper
{
    /**
     * Return the translation for a string given a language iso code 'en' 'fr' ..
     *
     * @public
     * @param $string string to translate
     * @param $iso_lang language iso code
     * @param $source source file without extension
     * @param $js if it's inside a js string
     * @return string translation
     */
    public static function translate($string, $iso_lang, $module, $source = '', $js = false)
    {
        $_MODULE = array();
        $file = dirname($module->module_file) . '/translations/' . $iso_lang . '.php';

        if (!file_exists($file)) {
            return $string;
        }

        if ($source == '') {
            $source = $module->name;
        }

        include($file);
        $key = md5(str_replace('\'', '\\\'', $string));
        $current_key = Tools::strtolower('<{' . $module->name . '}' . _THEME_NAME_ . '>' . $source) . '_' . $key;
        $default_key = Tools::strtolower('<{' . $module->name . '}prestashop>' . $source) . '_' . $key;
        $ret = $string;

        // $_MODULE is defined in the translation file
        if (isset($_MODULE[$current_key])) {
            $ret = Tools::stripslashes($_MODULE[$current_key]);
        } elseif (isset($_MODULE[$default_key])) {
            $ret = Tools::stripslashes($_MODULE[$default_key]);
        } if ($js) {
            $ret = addslashes($ret);
        }
        return $ret;
    }
}
