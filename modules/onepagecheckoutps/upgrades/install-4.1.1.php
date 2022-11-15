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

function upgrade_module_4_1_1($module)
{
    $return = true;
    $registered_version = $module->core->getRegisteredVersion();

    if (version_compare($registered_version, '4.1.0', '<')) {
        $return = upgrade_module_4_1_0($module);
    }
    if (version_compare($registered_version, '4.1.0.1', '<')) {
        $return = upgrade_module_4_1_0_1($module);
    }
    if (version_compare($registered_version, '4.1.0.2', '<')) {
        $return = upgrade_module_4_1_0_2($module);
    }
    if (version_compare($registered_version, '4.1.0.3', '<')) {
        $return = upgrade_module_4_1_0_3();
    }
    if (version_compare($registered_version, '4.1.0.4', '<')) {
        $return = upgrade_module_4_1_0_4();
    }
    if (version_compare($registered_version, '4.1.0.5', '<')) {
        $return = upgrade_module_4_1_0_5($module);
    }
    if (version_compare($registered_version, '4.1.0.6', '<')) {
        $return = upgrade_module_4_1_0_6($module);
    }
    if (version_compare($registered_version, '4.1.0.7', '<')) {
        $return = upgrade_module_4_1_0_7();
    }

    Configuration::updateValue('OPC_ENABLE_DEBUG_NEW_CHECKOUT', true);

    return $return;
}

function upgrade_module_4_1_0($module)
{
    $module->registerHook('moduleRoutes');
    $module->registerHook('actionUpdateLangAfter');
    $module->registerHook('additionalCustomerFormFields');
    $module->registerHook('actionOpcPaymentFeeService');

    Configuration::updateValue('OPC_IP_CHECKOUT_BETA', '');
    Configuration::updateValue('OPC_ALLOW_EDIT_PRODUCTS_CART', true);
    Configuration::updateValue('OPC_SHOW_NATIVE_HEADER', false);
    Configuration::updateValue('OPC_SHOW_NATIVE_FOOTER', false);
    Configuration::updateValue('OPC_STYLE', 'three_columns');
    Configuration::updateValue('OPC_STYLE_MOBILE', 'steps');
    Configuration::updateValue('OPC_CARRIER_STORE_PICKUP', '0');
    Configuration::updateValue('OPC_FORCE_CUSTOMER_REGISTRATION_LOGIN', false);
    Configuration::updateValue('OPC_SHOW_LOGIN_REGISTER_IN_TABS', false);

    $db = Db::getInstance(_PS_USE_SQL_SLAVE_);

    $sql = 'RENAME TABLE `'._DB_PREFIX_.'opc_social_network` TO `'._DB_PREFIX_.'opc_social_network_stats`;';
    $db->execute($sql);

    $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'opc_social_network` (
        `name` varchar(50) NOT NULL,
        `id_shop` int(10) NOT NULL,
        `enabled` tinyint(1) NOT NULL,
        `keys` text NOT NULL,
        PRIMARY KEY (`name`, `id_shop`)
    )
    ENGINE=MYSQL_ENGINE DEFAULT CHARSET=utf8;';
    $db->execute($sql);

    return true;
}

function upgrade_module_4_1_0_1($module)
{
    $module->registerHook('actionOpcPaymentFeeService');

    Configuration::updateValue('OPC_AUTOCOMPLETE_CUSTOMER_NAME_ON_ADDRESS', true);

    $db = Db::getInstance(_PS_USE_SQL_SLAVE_);

    $sql = 'DELETE FROM `'._DB_PREFIX_.'configuration` WHERE name = "'.$module->prefix_module.'_VERSION" AND id_shop IS NOT NULL AND id_shop_group IS NOT NULL';
    if (!$db->execute($sql)) {
        return false;
    }

    return true;
}

function upgrade_module_4_1_0_2($module)
{
    $module->registerHook('actionOpcValidatePayment');

    return true;
}

function upgrade_module_4_1_0_3()
{
    Configuration::updateValue('OPC_SHOW_DISCOUNT_BOX_ON_PAYMENT', false);

    return true;
}

function upgrade_module_4_1_0_4()
{
    Configuration::deleteByName('OPC_SHOW_DISCOUNT_BOX_ON_PAYMENT');
    Configuration::updateValue('OPC_SHOW_DISCOUNT_BOX_PAYMENT_MOBILE', true);

    return true;
}

function upgrade_module_4_1_0_5($module)
{
    $module->registerHook('actionOpcValidateVatNumber');

    return true;
}

function upgrade_module_4_1_0_6($module)
{
    $module->deleteEmptyAddressesOPC();

    $db = Db::getInstance(_PS_USE_SQL_SLAVE_);

    //Elimina las direcciones de opc_customer_address que no ya existan en address
    $sql = 'DELETE FROM '._DB_PREFIX_.'opc_customer_address WHERE id_address NOT IN (SELECT id_address FROM '._DB_PREFIX_.'address)';
    $db->execute($sql);

    //Elimina las direcciones de opc_customer_address que esten eliminadas (deleted = 1)
    $sql = 'DELETE FROM '._DB_PREFIX_.'opc_customer_address WHERE id_address IN (SELECT id_address FROM '._DB_PREFIX_.'address WHERE deleted = 1)';
    $db->execute($sql);

    // Pasa todas las direcciones a la tabla de opc_customer_address.
    // Si son mas los productos virtuales, se pasan las direcciones con tipo 'invoice', de lo contrario con 'delivery'
    $sql = 'SELECT COUNT(*) FROM '._DB_PREFIX_.'product WHERE is_virtual = 1';
    $virtual_products = (int) $db->getValue($sql);

    $sql = 'SELECT COUNT(*) FROM '._DB_PREFIX_.'product WHERE is_virtual = 0';
    $no_virtual_products = (int) $db->getValue($sql);

    $object_address = 'delivery';
    if ($virtual_products > $no_virtual_products) {
        $object_address = 'invoice';
    }

    $sql = 'INSERT INTO '._DB_PREFIX_.'opc_customer_address SELECT id_customer, id_address, \''.$object_address.'\', 0 FROM '._DB_PREFIX_.'address WHERE deleted = 0 AND id_customer != 0 AND id_address NOT IN (SELECT id_address FROM '._DB_PREFIX_.'opc_customer_address)';
    $db->execute($sql);

    $module->registerHook('actionObjectAddressAddAfter');
    $module->registerHook('actionObjectAddressUpdateAfter');
    $module->registerHook('actionObjectAddressDeleteAfter');

    return true;
}

function upgrade_module_4_1_0_7()
{
    $db = Db::getInstance(_PS_USE_SQL_SLAVE_);

    $sql = 'UPDATE `'._DB_PREFIX_.'opc_field` SET `type` = "number" WHERE `type` = "isInt"';
    if (!$db->execute($sql)) {
        return false;
    }

    $sql = 'UPDATE `'._DB_PREFIX_.'opc_field` SET `type` = "url" WHERE `type` = "isUrl"';
    if (!$db->execute($sql)) {
        return false;
    }

    return true;
}
