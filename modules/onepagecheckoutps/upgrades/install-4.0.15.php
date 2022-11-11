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

function upgrade_module_4_0_15($module)
{
    $module->registerHook('actionCustomerFormBuilderModifier');
    $module->registerHook('actionCustomerAddressFormBuilderModifier');
    $module->registerHook('actionAfterCreateCustomerFormHandler');
    $module->registerHook('actionAfterUpdateCustomerFormHandler');
    $module->registerHook('actionAfterUpdateCustomerAddressFormHandler');

    $sub_query = 'SELECT id_field FROM '._DB_PREFIX_.'opc_field WHERE name = \'id_state\' OR name = \'postcode\'';

    Db::getInstance()->update(
        'opc_field_shop',
        array(
            'active' => 1,
            'required' => 0
        ),
        'id_field IN ('.$sub_query.')'
    );

    return true;
}
