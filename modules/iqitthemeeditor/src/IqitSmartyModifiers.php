<?php
/**
 * 2017 IQIT-COMMERCE.COM
 *
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement
 *
 *  @author    IQIT-COMMERCE.COM <support@iqit-commerce.com>
 *  @copyright 2017 IQIT-COMMERCE.COM
 *  @license   Commercial license (You can not resell or redistribute this software.)
 *
 */

class IqitSmartyModifiers {

    public static function iqitDateFormat($string, $format = null, $default_date = '', $formatter = 'auto')
    {
        if ($format === null) {
            $format = '%y-%m-%d';
        }
        /**
         * require_once the {@link shared.make_timestamp.php} plugin
         */
        static $is_loaded = false;
        if (!$is_loaded) {
            if (!is_callable('smarty_make_timestamp')) {
                require_once(SMARTY_PLUGINS_DIR . 'shared.make_timestamp.php');
            }
            $is_loaded = true;
        }
        if ($string !== '' && $string !== '0000-00-00' && $string !== '0000-00-00 00:00:00') {
            $timestamp = smarty_make_timestamp($string);
        } elseif ($default_date !== '') {
            $timestamp = smarty_make_timestamp($default_date);
        } else {
            return;
        }
        if ($formatter === 'strftime' || ($formatter === 'auto' && strpos($format, '%') !== false)) {
            if (Smarty::$_IS_WINDOWS) {
                $_win_from = array('%D',
                    '%h',
                    '%n',
                    '%r',
                    '%R',
                    '%t',
                    '%T');
                $_win_to = array('%m/%d/%y',
                    '%b',
                    "\n",
                    '%I:%M:%S %p',
                    '%H:%M',
                    "\t",
                    '%H:%M:%S');
                if (strpos($format, '%e') !== false) {
                    $_win_from[] = '%e';
                    $_win_to[] = sprintf('%\' 2d', date('j', $timestamp));
                }
                if (strpos($format, '%l') !== false) {
                    $_win_from[] = '%l';
                    $_win_to[] = sprintf('%\' 2d', date('h', $timestamp));
                }
                $format = str_replace($_win_from, $_win_to, $format);
            }

            return IqitSmartyModifiers::dateV('j f Y',$timestamp);
        } else {
            return IqitSmartyModifiers::dateV('j f Y',$timestamp);
        }
    }


    public static function  dateV($format,$timestamp=null){
        $to_convert = array(
            'l'=>array('dat'=>'N','str'=>array('Poniedziałek','Wtorek','Środa','Czwartek','Piątek','Sobota','Niedziela')),
            'F'=>array('dat'=>'n','str'=>array('styczeń','luty','marzec','kwiecień','maj','czerwiec','lipiec','sierpień','wrzesień','październik','listopad','grudzień')),
            'f'=>array('dat'=>'n','str'=>array('stycznia','lutego','marca','kwietnia','maja','czerwca','lipca','sierpnia','września','października','listopada','grudnia'))
        );
        if ($pieces = preg_split('#[:/.\-, ]#', $format)){
            if ($timestamp === null) { $timestamp = time(); }
            foreach ($pieces as $datepart){
                if (array_key_exists($datepart,$to_convert)){
                    $replace[] = $to_convert[$datepart]['str'][(date($to_convert[$datepart]['dat'],$timestamp)-1)];
                }else{
                    $replace[] = date($datepart,$timestamp);
                }
            }
            $result = strtr($format,array_combine($pieces,$replace));
            return $result;
        }
    }




    public static function  smarty_modifier_add_url_param($url, $parameter, $paramValue = NULL)
    {

        if ($paramValue !== NULL) {

            // we were passed the parameter and value as
            // separate plug-in parameters, so just apply
            // them to the URL.
            $url = self::_addURLParameter ($url, $parameter, $paramValue) ;

        } elseif (is_array($parameter)) {

            // we were passed an assoc. array containing
            // parameter names and parameter values, so
            // apply them all to the URL.
            foreach ($parameter as $paramName => $paramValue) {
                $url = self::_addURLParameter ($url, $paramName, $paramValue) ;
            }

        } else {

            // was passed a string containing at least one parameter
            // so parse out those passed and apply them separately
            // to the URL.

            $numParams = preg_match_all('/([^=?&]+?)=([^&]*)/', $parameter, $matches, PREG_SET_ORDER) ;

            foreach ($matches as $match) {
                $url = self::_addURLParameter ($url, $match[1], $match[2]) ;
            }

        }

        return $url ;
    }

    public static function _addURLParameter ($url, $paramName, $paramValue) {

        // first check whether the parameter is already
        // defined in the URL so that we can just update
        // the value if that's the case.

        if (preg_match('/[?&]('.$paramName.')=[^&]*/', $url)) {

            // parameter is already defined in the URL, so
            // replace the parameter value, rather than
            // append it to the end.
            $url = preg_replace('/([?&]'.$paramName.')=[^&]*/', '$1='.$paramValue, $url) ;

        } else {
            // can simply append to the end of the URL, once
            // we know whether this is the only parameter in
            // there or not.
            $url .= strpos($url, '?') ? '&' : '?';
            $url .= $paramName . '=' . $paramValue;

        }
        return $url ;
    }

    public static function smarty_modifier_sort_array($array)
    {

        if (array_key_exists('Ć',$array))
        {
            $array['ĆZZZ'] = $array['Ć'];
            unset($array['Ć']);
        }
        if (array_key_exists('Ś',$array))
        {
            $array['SZZZ'] = $array['Ś'];
            unset($array['Ś']);
        }
        if (array_key_exists('Ł',$array))
        {
            $array['LZZZ'] = $array['Ł'];
            unset($array['Ł']);
        }

        ksort($array);

        $array = self::replace_key($array, 'LZZZ', 'Ł');
        $array = self::replace_key($array, 'SZZZ', 'Ś');
        $array = self::replace_key($array, 'ĆZZZ', 'Ć');

        return $array;
    }


    public static function replace_key($arr, $oldkey, $newkey) {
        if(array_key_exists( $oldkey, $arr)) {
            $keys = array_keys($arr);
            $keys[array_search($oldkey, $keys)] = $newkey;
            return array_combine($keys, $arr);
        }
        return $arr;
    }

}