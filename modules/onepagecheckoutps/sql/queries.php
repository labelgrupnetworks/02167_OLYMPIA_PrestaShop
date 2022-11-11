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

return array(
    'install' => array(
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'opc_field` (
            `id_field` int(10) NOT NULL AUTO_INCREMENT,
            `object` varchar(20) NOT NULL,
            `name` varchar(50) NOT NULL,
            `type` varchar(20) NOT NULL,
            `size` int(10) NOT NULL,
            `type_control` varchar(20) NOT NULL,
            `is_custom` tinyint(1) NOT NULL,
            `capitalize` tinyint(1) NOT NULL,
            PRIMARY KEY (`id_field`)
        )
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'opc_field_shop` (
            `id_field` int(10) NOT NULL,
            `id_shop` int(10) NOT NULL,
            `default_value` varchar(255) NOT NULL,
            `group` varchar(20) NOT NULL,
            `row` int(10) NOT NULL,
            `col` int(10) NOT NULL,
            `required` tinyint(1) NOT NULL,
            `active` tinyint(1) NOT NULL,
            PRIMARY KEY (`id_field`, `id_shop`)
        )
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'opc_field_lang` (
            `id_field` int(10) NOT NULL,
            `id_lang` int(10) NOT NULL,
            `id_shop` int(10) NOT NULL,
            `description` varchar(255) NOT NULL,
            `label` varchar(255) NOT NULL,
            PRIMARY KEY (`id_field`, `id_lang`, `id_shop`)
        )
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'opc_field_option` (
            `id_field_option` int NOT NULL AUTO_INCREMENT,
            `id_field` int(10) NOT NULL,
            `value` varchar(255) NOT NULL,
            `position` int(10) NOT NULL,
            PRIMARY KEY (`id_field_option`)
        )
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'opc_field_option_lang` (
            `id_field_option` int(10) NOT NULL,
            `id_lang` int(10) NOT NULL,
            `description` varchar(255) NOT NULL,
            PRIMARY KEY (`id_field_option`, `id_lang`)
        )
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'opc_payment` (
            `id_payment` int(10) NOT NULL AUTO_INCREMENT,
            `id_module` int(10) NOT NULL,
            `name` varchar(255) NOT NULL,
            `name_image` varchar(100) NOT NULL,
            `force_display` TINYINT(1) NOT NULL,
            `test_mode` TINYINT(1) NOT NULL,
            `test_ip` varchar(300) NOT NULL,
            PRIMARY KEY (`id_payment`)
        )
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'opc_payment_lang` (
            `id_payment` int(10) NOT NULL,
            `id_lang` int(10) NOT NULL,
            `id_shop` int(10) NOT NULL,
            `title` varchar(255) NULL,
            `description` varchar(255) NULL,
            PRIMARY KEY (`id_payment`, `id_lang`, `id_shop`)
        )
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'opc_payment_shop` (
            `id_payment` int(10) NOT NULL,
            `id_shop` int(10) NOT NULL,
            PRIMARY KEY (`id_payment`, `id_shop`)
        )
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'opc_social_network` (
            `name` varchar(50) NOT NULL,
            `id_shop` int(10) NOT NULL,
            `enabled` tinyint(1) NOT NULL,
            `keys` text NOT NULL,
            PRIMARY KEY (`name`, `id_shop`)
        )
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'opc_social_network_stats` (
            `id` int(10) NOT NULL AUTO_INCREMENT,
            `id_shop` int(10) NOT NULL,
            `id_customer` int(10) NOT NULL,
            `code_network` varchar(50) NOT NULL,
            `network` varchar(50) NOT NULL,
            PRIMARY KEY (`id`)
        )
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'opc_customer_address` (
            `id_customer` int(10) NOT NULL,
            `id_address` int(10) NOT NULL,
            `object` varchar(10) NOT NULL,
            PRIMARY KEY (`id_customer`, `id_address`, `object`)
        )
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'opc_field_customer` (
            `id` int(10) NOT NULL AUTO_INCREMENT,
            `id_field` int(10) NOT NULL,
            `id_customer` int(10) NOT NULL,
            `object` varchar(9) NOT NULL,
            `id_address` int(10) NULL,
            `id_option` int(10) NULL,
            `value` varchar(255) NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id`)
        )
        ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

        // 'CREATE TRIGGER after_delete_module
        // AFTER DELETE ON ' . _DB_PREFIX_ . 'module
        // FOR EACH ROW
        // BEGIN
        //     DECLARE module_id_removed INT(11);
        //     SELECT id_payment INTO module_id_removed FROM ' . _DB_PREFIX_ . 'opc_payment WHERE id_module = OLD.id_module;
        //     DELETE FROM ' . _DB_PREFIX_ . 'opc_payment WHERE id_payment = module_id_removed;
        //     DELETE FROM ' . _DB_PREFIX_ . 'opc_payment_lang WHERE id_payment = module_id_removed;
        //     DELETE FROM ' . _DB_PREFIX_ . 'opc_payment_shop WHERE id_payment = module_id_removed;
        // END',

        'TRUNCATE `' . _DB_PREFIX_ . 'opc_field`',
        'TRUNCATE `' . _DB_PREFIX_ . 'opc_field_lang`',
        'TRUNCATE `' . _DB_PREFIX_ . 'opc_field_shop`',

        'INSERT INTO `' . _DB_PREFIX_ . 'opc_field` (`id_field`, `object`, `name`, `type`, `size`, `type_control`, `is_custom`, `capitalize`) VALUES
        (1, "customer", "id", "number", 0, "textbox", 0, 0),
        (2, "customer", "firstname", "isCustomerName", 32, "textbox", 0, 1),
        (3, "customer", "lastname", "isCustomerName", 32, "textbox", 0, 1),
        (4, "customer", "email", "isEmail", 128, "textbox", 0, 0),
        (5, "customer", "id_gender", "number", 0, "radio", 0, 0),
        (6, "customer", "birthday", "isBirthDate", 0, "textbox", 0, 0),
        (7, "customer", "newsletter", "isBool", 0, "checkbox", 0, 0),
        (8, "customer", "optin", "isBool", 0, "checkbox", 0, 0),
        (9, "customer", "passwd", "isPasswd", 32, "textbox", 0, 0),
        (10, "customer", "siret", "isGenericName", 14, "textbox", 0, 0),
        (11, "customer", "ape", "isGenericName", 5, "textbox", 0, 0),
        (12, "customer", "website", "url", 128, "textbox", 0, 0),
        (13, "delivery", "id", "number", 0, "textbox", 0, 0),
        (14, "delivery", "dni", "isDniLite", 16, "textbox", 0, 0),
        (15, "delivery", "company", "isGenericName", 32, "textbox", 0, 1),
        (16, "delivery", "firstname", "isName", 32, "textbox", 0, 1),
        (17, "delivery", "lastname", "isName", 32, "textbox", 0, 1),
        (18, "delivery", "address1", "isAddress", 128, "textbox", 0, 1),
        (19, "delivery", "address2", "isAddress", 128, "textbox", 0, 1),
        (20, "delivery", "id_country", "number", 0, "select", 0, 0),
        (21, "delivery", "id_state", "number", 0, "select", 0, 0),
        (22, "delivery", "postcode", "isPostCode", 12, "textbox", 0, 1),
        (23, "delivery", "city", "isCityName", 64, "textbox", 0, 1),
        (24, "delivery", "phone", "isPhoneNumber", 32, "textbox", 0, 0),
        (25, "delivery", "phone_mobile", "isPhoneNumber", 32, "textbox", 0, 0),
        (26, "delivery", "other", "isMessage", 300, "textarea", 0, 0),
        (27, "delivery", "alias", "isGenericName", 32, "textbox", 0, 0),
        (28, "delivery", "vat_number", "isGenericName", 32, "textbox", 0, 0),
        (29, "invoice", "id", "number", 0, "textbox", 0, 0),
        (30, "invoice", "dni", "isDniLite", 16, "textbox", 0, 0),
        (31, "invoice", "company", "isGenericName", 32, "textbox", 0, 1),
        (32, "invoice", "firstname", "isName", 32, "textbox", 0, 1),
        (33, "invoice", "lastname", "isName", 32, "textbox", 0, 1),
        (34, "invoice", "address1", "isAddress", 128, "textbox", 0, 1),
        (35, "invoice", "address2", "isAddress", 128, "textbox", 0, 1),
        (36, "invoice", "id_country", "number", 0, "select", 0, 0),
        (37, "invoice", "id_state", "number", 0, "select", 0, 0),
        (38, "invoice", "postcode", "isPostCode", 12, "textbox", 0, 1),
        (39, "invoice", "city", "isCityName", 64, "textbox", 0, 1),
        (40, "invoice", "phone", "isPhoneNumber", 32, "textbox", 0, 0),
        (41, "invoice", "phone_mobile", "isPhoneNumber", 32, "textbox", 0, 0),
        (42, "invoice", "other", "isMessage", 300, "textarea", 0, 0),
        (43, "invoice", "alias", "isGenericName", 32, "textbox", 0, 0),
        (44, "invoice", "vat_number", "isGenericName", 32, "textbox", 0, 0),
        (45, "customer", "company", "isGenericName", 64, "textbox", 0, 1);',
    ),
    'uninstall' => array(
        'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'opc_field`;',
        'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'opc_field_lang`;',
        'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'opc_field_shop`;',
        'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'opc_field_option`;',
        'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'opc_field_option_lang`;',
        'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'opc_payment`;',
        'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'opc_payment_lang`;',
        'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'opc_payment_shop`;',
        'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'opc_social_network`;',
        'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'opc_social_network_stats`;',
        'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'opc_field_customer`;',
        'DROP TRIGGER IF EXISTS after_delete_module;',
    ),
    'shop' => array(
        'INSERT INTO `' . _DB_PREFIX_ . 'opc_field_shop` (`id_field`, `id_shop`, `default_value`, `group`, `row`, `col`, `required`, `active`) VALUES
        (1, ID_SHOP, "", "customer", 0, 0, 0, 1),
        (2, ID_SHOP, ".", "customer", 1, 0, 1, 1),
        (3, ID_SHOP, ".", "customer", 1, 1, 1, 1),
        (4, ID_SHOP, "", "customer", 2, 0, 1, 1),
        (5, ID_SHOP, "", "customer", 3, 0, 0, 0),
        (6, ID_SHOP, "", "customer", 4, 0, 0, 0),
        (7, ID_SHOP, "0", "customer", 5, 0, 0, 0),
        (8, ID_SHOP, "0", "customer", 6, 0, 0, 0),
        (9, ID_SHOP, "", "customer", 7, 0, 0, 1),
       (10, ID_SHOP, "", "customer", 8, 0, 0, 0),
       (11, ID_SHOP, "", "customer", 8, 1, 0, 0),
       (12, ID_SHOP, "", "customer", 9, 0, 0, 0),
       (13, ID_SHOP, "", "delivery", 10, 0, 0, 1),
       (14, ID_SHOP, "0", "delivery", 11, 0, 0, 1),
       (15, ID_SHOP, "", "delivery", 12, 0, 0, 1),
       (16, ID_SHOP, ".", "delivery", 13, 0, 1, 1),
       (17, ID_SHOP, ".", "delivery", 13, 1, 1, 1),
       (18, ID_SHOP, ".", "delivery", 14, 0, 1, 1),
       (19, ID_SHOP, "", "delivery", 15, 0, 0, 0),
       (20, ID_SHOP, "", "delivery", 16, 0, 1, 1),
       (22, ID_SHOP, "0", "delivery", 16, 1, 0, 1),
       (21, ID_SHOP, "", "delivery", 17, 0, 0, 1),
       (23, ID_SHOP, ".", "delivery", 17, 1, 1, 1),
       (24, ID_SHOP, "0", "delivery", 18, 0, 0, 1),
       (25, ID_SHOP, "0", "delivery", 18, 1, 1, 1),
       (26, ID_SHOP, "", "delivery", 19, 0, 0, 0),
       (27, ID_SHOP, "My address", "delivery", 20, 0, 0, 0),
       (28, ID_SHOP, "", "delivery", 21, 0, 0, 0),
       (29, ID_SHOP, "", "invoice", 22, 0, 0, 1),
       (30, ID_SHOP, "0", "invoice", 23, 0, 0, 1),
       (31, ID_SHOP, "", "invoice", 24, 0, 0, 1),
       (32, ID_SHOP, ".", "invoice", 25, 0, 1, 1),
       (33, ID_SHOP, ".", "invoice", 25, 1, 1, 1),
       (34, ID_SHOP, ".", "invoice", 26, 0, 1, 1),
       (35, ID_SHOP, "", "invoice", 27, 0, 0, 0),
       (36, ID_SHOP, "", "invoice", 28, 0, 1, 1),
       (38, ID_SHOP, "0", "invoice", 28, 1, 0, 1),
       (37, ID_SHOP, "", "invoice", 29, 0, 0, 1),
       (39, ID_SHOP, ".", "invoice", 29, 1, 1, 1),
       (40, ID_SHOP, "0", "invoice", 30, 0, 0, 1),
       (41, ID_SHOP, "0", "invoice", 30, 1, 1, 1),
       (42, ID_SHOP, "", "invoice", 31, 0, 0, 0),
       (43, ID_SHOP, "My address", "invoice", 32, 0, 0, 0),
       (44, ID_SHOP, "0", "invoice", 33, 0, 0, 0),
       (45, ID_SHOP, "", "customer", 9, 1, 0, 0);',
    ),
);
