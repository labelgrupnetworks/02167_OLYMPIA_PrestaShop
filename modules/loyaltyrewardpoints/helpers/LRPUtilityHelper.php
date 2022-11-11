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

use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;

class LRPUtilityHelper
{
    /**
     * Determine of code is being executed is from Web Service request
     * @return bool
     */
    public static function isWebServiceRequest()
    {
        if (Tools::getValue('url') != '' && Tools::getValue('ws_key') != '') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get security key for the cron url
     * @return string
     */
    public static function getCronSecureKey()
    {
        return md5(_COOKIE_KEY_ . Configuration::get('PS_SHOP_NAME'));
    }

    public static function isFileInStackTrace($file, $count = 10)
    {
        $file = Tools::strtolower($file);
        $stack = debug_backtrace();
        for ($x = 0; $x <= $count-1; $x++) {
            if (!empty($stack[$x]['file'])) {
                if ($file == Tools::strtolower(basename($stack[$x]['file']))) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get default tax rate
     * @param $id_country
     */
    public static function getDefaultTaxRate($id_country)
    {
        $sql = new DbQuery();
        $sql->select('t.rate');
        $sql->from('tax', 't');
        $sql->innerJoin('tax_rule', 'tr', 't.id_tax = tr.id_tax');
        $sql->where('tr.id_country =' . (int)$id_country);
        $rate = Db::getInstance()->getValue($sql);
        return $rate;
    }

    /**
     * @param $gross_amount
     * @param $tax_rate
     * @return float|int
     */
    public static function deductTax($gross_amount, $tax_rate)
    {
        $gross_amount = $gross_amount / (1 + ($tax_rate / 100));
        return $gross_amount;
    }

    /**
     * Convert amount from a currency to an other currency automatically.
     *
     * @param float $amount
     * @param Currency $currency_from if null we used the default currency
     * @param Currency $currency_to if null we used the default currency
     */
    public static function convertPriceFull($amount, Currency $currency_from = null, Currency $currency_to = null, $round = false)
    {
        if ($currency_from == $currency_to) {
            return $amount;
        }

        if ($currency_from === null) {
            $currency_from = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        }

        if ($currency_to === null) {
            $currency_to = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        }

        if ($currency_from->id == Configuration::get('PS_CURRENCY_DEFAULT')) {
            $amount *= $currency_to->conversion_rate;
        } else {
            $conversion_rate = ($currency_from->conversion_rate == 0 ? 1 : $currency_from->conversion_rate);
            // Convert amount to default currency (using the old currency rate)
            $amount = $amount / $conversion_rate;
            // Convert to new currency
            $amount *= $currency_to->conversion_rate;
        }

        if ($round) {
            return Tools::ps_round($amount, _PS_PRICE_COMPUTE_PRECISION_);
        } else {
            return $amount;
        }
    }

    /**
     * @param $price
     * @param $currency
     * @return string
     */
    public static function formatPrice($price, $currency)
    {
        $priceFormatter = new PriceFormatter();
        return $priceFormatter->format($price, $currency);
    }

    public static function isValidDate(string $date): bool
    {
        if (DateTime::createFromFormat('Y-m-d',$date) !== false) {
            return true;
        } else {
            return false;
        }
    }
}
