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

function upgrade_module_4_1_3($module)
{
    $module = $module;

    $db = Db::getInstance(_PS_USE_SQL_SLAVE_);

    $sql = 'UPDATE `'._DB_PREFIX_.'opc_field` SET `type` = "number" WHERE `type` = "isInt"';
    if (!$db->execute($sql)) {
        return false;
    }

    $sql = 'UPDATE `'._DB_PREFIX_.'opc_field` SET `type` = "url" WHERE `type` = "isUrl"';
    if (!$db->execute($sql)) {
        return false;
    }

    //No se valida en caso de que ya no exista, entonces continuar con el upgrade.
    $sql = 'ALTER TABLE `'._DB_PREFIX_.'opc_customer_address` DROP `default`';
    $db->execute($sql);

    //Evita el problema de 404 de la version 1.7.8.4
    $sql = 'UPDATE `'._DB_PREFIX_.'hook` SET `name` = "moduleRoutes" WHERE name = "moduleroutes";';
    $db->execute($sql);

    return true;
}
