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

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(_PS_MODULE_DIR_ . '/loyaltyrewardpoints/lib/bootstrap.php');

function upgrade_module_2_0_5($object)
{
    $return = true;

    $return &= Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "lrp_cart` (
            `id_lrp_cart` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_cart` int(10) unsigned NOT NULL DEFAULT '0',
            `id_customer` int(10) unsigned NOT NULL DEFAULT '0',
            `points_redeemed` int(10) unsigned NOT NULL DEFAULT '0',
        PRIMARY KEY (`id_lrp_cart`)		
		) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 ;');

    $return &= Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lrp_referral_cookie` (
            `id_lrp_referral_cookie` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_cart` int(10) unsigned NOT NULL,
            `id_customer` int(10) unsigned NOT NULL DEFAULT \'0\',
            `id_referrer` int(10) unsigned NOT NULL DEFAULT \'0\',
        PRIMARY KEY (`id_lrp_referral_cookie`)		
		) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 ;');

    return $return;
}
