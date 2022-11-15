<?php
/**
 * This is main class of module.
 *
 * @author    Globo Software Solution JSC <contact@globosoftware.net>
 * @copyright 2020 Globo ., Jsc
 * @license   please read license in file license.txt
 * @link	     http://www.globosoftware.net
 */

if (!defined('_PS_VERSION_'))
    exit;

function upgrade_module_1_3_2($module)
{
    $module;
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderpro_lang` ADD COLUMN `redirect_link_lang` TEXT NULL DEFAULT ""');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderpro` ADD COLUMN `mailchimp` TINYINT(1) NULL DEFAULT  "0"');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderpro` ADD COLUMN `klaviyo` TINYINT(1) NULL DEFAULT  "0"');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderpro` ADD COLUMN `zapier` TINYINT(1) NULL DEFAULT  "0"');
    Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gform_mailchimp_klaviyo_map` (
                `id_gformbuilderprofields` int(10) unsigned NOT NULL,
                `mailchimp_tag` text DEFAULT NULL,
                `klaviyo_label` text DEFAULT NULL,
                PRIMARY KEY (`id_gformbuilderprofields`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
    Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gform_integration_map` (
            `id_gformbuilderpro` int(10) unsigned NOT NULL,
            `mailchimp_list` text DEFAULT NULL,
            `klaviyo_list` text DEFAULT NULL,
            `webhook_url` text DEFAULT NULL,
            PRIMARY KEY (`id_gformbuilderpro`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
    ');
    $formObj = Module::getInstanceByName('gformbuilderpro');
    $formObj->registerHook('displayGorderreference');
    return true;
}