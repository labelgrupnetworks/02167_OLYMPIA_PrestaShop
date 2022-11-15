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
 * @copyright 2016-2017 Musaffar Patel
 * @license   LICENSE.txt
 */

class LRPInstall
{
    public static function installDB()
    {
        $return = true;

        $return &= Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "lrp_cart` (
            `id_lrp_cart` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_cart` int(10) unsigned NOT NULL DEFAULT '0',
            `id_customer` int(10) unsigned NOT NULL DEFAULT '0',
            `points_redeemed` decimal(10,4) unsigned NOT NULL DEFAULT '0.00',
            `type` smallint(10) unsigned NOT NULL DEFAULT '0',
        PRIMARY KEY (`id_lrp_cart`)		
		) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 ;');

        $return &= Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "lrp_customer` (
            `id_lrp_customer` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_customer` int(10) unsigned NOT NULL,
            `points` decimal(10,4) unsigned NOT NULL DEFAULT '0.00',
            `referral_code` varchar(64) NOT NULL DEFAULT '',
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
        PRIMARY KEY (`id_lrp_customer`)		
		) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 ;');

        $return &= Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lrp_history` (
            `id_lrp_history` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_order` int(10) unsigned NOT NULL DEFAULT \'0\',
            `id_customer` int(10) unsigned NOT NULL DEFAULT \'0\',
            `id_currency` int(10) unsigned NOT NULL DEFAULT \'0\',            
            `points` decimal(10,4) unsigned NOT NULL DEFAULT \'0.00\',
            `point_value` decimal(10,4) unsigned NOT NULL DEFAULT \'0.00\',
            `point_value_base_currency` decimal(10,4) unsigned NOT NULL DEFAULT \'0.00\',            
            `type` smallint(10) unsigned NOT NULL DEFAULT \'0\',
            `source` varchar(255) NOT NULL,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
        PRIMARY KEY (`id_lrp_history`)		
		) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 ;');

        $return &= Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lrp_referral_click` (
            `id_lrp_referral_click` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_referrer` int(10) unsigned NOT NULL,
            `ip_address` varchar(64),
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,            
        PRIMARY KEY (`id_lrp_referral_click`)		
		) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 ;');

        $return &= Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lrp_referral_cookie` (
            `id_lrp_referral_cookie` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_cart` int(10) unsigned NOT NULL,
            `id_customer` int(10) unsigned NOT NULL DEFAULT \'0\',
            `id_referrer` int(10) unsigned NOT NULL DEFAULT \'0\',
        PRIMARY KEY (`id_lrp_referral_cookie`)		
		) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 ;');

        $return &= Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lrp_mail_log` (
            `id_lrp_mail_log` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_customer` int(10) unsigned DEFAULT \'0\',
            `email` varchar(128) DEFAULT NULL,
            `id_mail` varchar(16) DEFAULT NULL,
            `date_sent` datetime DEFAULT NULL,
        PRIMARY KEY (`id_lrp_mail_log`)		
		) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 ;');

        $return &= Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lrp_rule` (
            `id_lrp_rule` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_shop` int(10) unsigned NOT NULL DEFAULT \'0\',
            `enabled` tinyint(2) unsigned NOT NULL DEFAULT \'1\',
            `name` varchar(255) NOT NULL,
            `operator` varchar(2) NOT NULL DEFAULT \'=\',
            `points` int(10) unsigned NOT NULL DEFAULT \'0\',
            `id_products` varchar(1024) NOT NULL,
            `id_categories` varchar(1024) NOT NULL,
            `date_start` datetime NOT NULL,
            `date_end` datetime NOT NULL,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
        PRIMARY KEY (`id_lrp_rule`)		
		) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 ;');
        return $return;
    }

    public static function uninstall()
    {
    }

    public static function addColumn($table, $name, $type)
    {
        try {
            $return = Db::getInstance()->execute('ALTER TABLE  `' . _DB_PREFIX_ . bqSQL($table) . '` ADD  `' . bqSQL($name) . '` ' . bqSQL($type));
        } catch (Exception $e) {
            $return = true;
        }
    }

    public static function dropTable($table_name)
    {
        $sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . bqSQL($table_name) . '`';
        DB::getInstance()->execute($sql);
    }

    public static function installData()
    {
        foreach (Shop::getShops() as $shop) {
            $lrp_config = new LRPConfigModel(0, 0, $shop['id_shop']);
            $lrp_config->update('lrp_discount_combinable', 1, false, $shop['id_shop']);
        }
    }
}
