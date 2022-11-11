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

function upgrade_module_2_1_19($object)
{
    $return = true;
    LRPInstall::addColumn('lrp_history', 'point_value_base_currency', 'decimal(10,4) unsigned NOT NULL DEFAULT \'0.00\'');
    $return &= Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lrp_referral_click` (
            `id_lrp_referral_click` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_referrer` int(10) unsigned NOT NULL,
            `ip_address` varchar(64),
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,            
        PRIMARY KEY (`id_lrp_referral_click`)		
		) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 ;');
    return $return;
}
