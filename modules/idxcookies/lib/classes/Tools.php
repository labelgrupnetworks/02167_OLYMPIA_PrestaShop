<?php

class IdxrTools_2_0 extends Tools
{
    public static function usortOrdenAsc($a, $b)
    {
        return $a['orden'] - $b['orden'];
    }

    public static function usortNameAsc($a, $b)
    {
        return strcmp($a["name"], $b["name"]);
    }

    public static function dump($array, $die = true)
    {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
        if ($die) {
            die();
        }
    }

    public static function limpiaUrl($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        // trim
        $text = trim($text, '-');
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // lowercase
        $text = strtolower($text);
        // remove unwanted characters
        $text = preg_replace('~[^_\w]+~', '-', $text);
        if (empty($text)) {
            return '';
        } //empty($text)
        return $text;
    }

    public static function dashesToCamelCase($string, $capitalizeFirstCharacter = false, $dash = '_')
    {
        $str = str_replace(' ', '', ucwords(str_replace($dash, ' ', $string)));

        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }

        return $str;
    }

    public static function displayPrice($price, $currency = null, $no_utf8 = false, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        if (!$currency) {
            $currency = Context::getContext()->currency;
        }
        if (version_compare(_PS_VERSION_, '1.7.6.0', '>=')) {
            return $context->currentLocale->formatPrice((float)$price, $currency->iso_code);
        } else {
            return parent::displayPrice($price, $currency, $no_utf8, $context);
        }
    }
}
