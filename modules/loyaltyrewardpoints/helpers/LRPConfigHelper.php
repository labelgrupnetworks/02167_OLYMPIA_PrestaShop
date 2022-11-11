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

class LRPConfigHelper
{
    /** @var currency_active */
    private static $currency_active;

    /** @var */
    private static $currency_default;

    /** @var */
    private static $lrp_config_global;

    /** @var */
    private static $lrp_config_default;

    /** @var */
    private static $lrp_config_active;

    /**
     * create the static objects to be used (shared across methods)
     */
    private static function init($currency_iso, $id_group, $id_shop)
    {
        self::$currency_active = new Currency(Currency::getIdByIsoCode($currency_iso));
        self::$currency_default = Currency::getDefaultCurrency();

        self::$lrp_config_global = new LRPConfigModel(0, 0, $id_shop);
        self::$lrp_config_default = new LRPConfigModel(self::$currency_default->iso_code, $id_group, $id_shop);  //default currency settings
        self::$lrp_config_active = new LRPConfigModel(self::$currency_active->iso_code, $id_group, $id_shop);  //active currency settings
    }

    /**
     * Get Ratio
     * @param $currency_iso
     * @param $id_group
     * @param $id_shop
     * @return float
     */
    public static function getRatio($currency_iso, $id_group, $id_shop)
    {
        self::init($currency_iso, $id_group, $id_shop);

        if (self::$lrp_config_global->getAutomaticCurrencySettings()) {
            // convert main currency into active currency to determine how much must be spent to earn 1 point
            if ($currency_iso != self::$currency_default->iso_code) {
                return LRPUtilityHelper::convertPriceFull(self::$lrp_config_default->getRatio(), self::$currency_default, self::$currency_active);
            } else {
                return self::$lrp_config_default->getRatio();
            }
        } else {
            return self::$lrp_config_active->getRatio();
        }
    }

    /**
     * Get the monetary value of one point in the currency supplied
     * @param $currency_iso
     * @param $id_group
     * @param $id_shop
     * @return float|int
     */
    public static function getPointValue($currency_iso, $id_group, $id_shop)
    {
        self::init($currency_iso, $id_group, $id_shop);

        if (self::$lrp_config_global->getAutomaticCurrencySettings()) {
            // convert main currency into active currency to determine how much must be spent to earn 1 point
            if ($currency_iso != self::$currency_default->iso_code) {
                $point_value = LRPUtilityHelper::convertPriceFull(self::$lrp_config_default->getPointValue(), self::$currency_default, self::$currency_active);
            } else {
                $point_value = self::$lrp_config_default->getPointValue();
            }
        } else {
            $point_value = self::$lrp_config_active->getPointValue();
        }
        return $point_value;
    }

    /**
     * Get the Minimum cart Value
     * @param $currency_iso
     * @param $id_group
     * @param $id_shop
     * @return float|int
     */
    public static function getMinCartValue($currency_iso, $id_group, $id_shop)
    {
        self::init($currency_iso, $id_group, $id_shop);

        if (self::$lrp_config_global->getAutomaticCurrencySettings()) {
            // convert main currency into active currency to determine how much must be spent to earn 1 point
            if ($currency_iso != self::$currency_default->iso_code) {
                $point_value = LRPUtilityHelper::convertPriceFull(self::$lrp_config_default->getMinCartValue(), self::$currency_default, self::$currency_active);
            } else {
                $point_value = self::$lrp_config_default->getMinCartValue();
            }
        } else {
            $point_value = self::$lrp_config_active->getMinCartValue();
        }
        return $point_value;
    }
}
