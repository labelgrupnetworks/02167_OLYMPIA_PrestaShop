<?php
/**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * @author    PresTeamShop SAS (Registered Trademark) <info@presteamshop.com>
 * @copyright 2011-2022 PresTeamShop SAS, All rights reserved.
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * @category  PrestaShop
 * @category  Module
 */

function upgrade_module_1_1_3($object)
{
    $object = $object;

    $create_table = '
        CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'opc_customer_address` (
            `id_customer` int(10) NOT NULL,
            `id_address` int(10) NOT NULL,
            `object` varchar(10) NOT NULL,
            PRIMARY KEY (`id_customer`, `id_address`, `object`)
        )
        ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($create_table);

    return true;
}
