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
 *
 * @category  PrestaShop
 * @category  Module
 */

function upgrade_module_4_1_4($module)
{
    $return = true;
    $registered_version = $module->core->getRegisteredVersion();

    if (version_compare($registered_version, '4.1.3.1', '<')) {
        $return = upgrade_module_4_1_3_1($module);
    }

    return $return;
}

function upgrade_module_4_1_3_1($module)
{
    $module = $module;

    $db = Db::getInstance(_PS_USE_SQL_SLAVE_);

    $sql = 'UPDATE `'._DB_PREFIX_.'opc_field` SET `size` = 32 WHERE `type` = \'isPhoneNumber\'';
    if (!$db->execute($sql)) {
        return false;
    }

    Configuration::updateValue('OPC_VALIDATE_UNIQUE_DNI', 0);
    Configuration::updateValue('OPC_SHOW_PHONE_MASK', 0);

    return true;
}
