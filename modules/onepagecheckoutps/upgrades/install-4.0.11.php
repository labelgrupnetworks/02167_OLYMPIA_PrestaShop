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

function upgrade_module_4_0_11()
{
    $sql = 'UPDATE `'._DB_PREFIX_.'opc_field` SET `type` = "isCustomerName" WHERE `id_field` = 2';
    if (!Db::getInstance()->execute($sql)) {
        return false;
    }

    $sql = 'UPDATE `'._DB_PREFIX_.'opc_field` SET `type` = "isCustomerName" WHERE `id_field` = 3';
    if (!Db::getInstance()->execute($sql)) {
        return false;
    }

    return true;
}
