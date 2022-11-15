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

function upgrade_module_3_0_0($object)
{
    $object = $object;

    Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'opc_social_network` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `id_shop` int(10) NOT NULL,
    `id_customer` int(10) NOT NULL,
    `code_network` varchar(50) NOT NULL,
    `network` varchar(50) NOT NULL,
    PRIMARY KEY (`id`)
    )
    ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');

    return true;
}
