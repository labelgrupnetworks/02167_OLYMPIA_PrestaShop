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

function upgrade_module_1_3_4($module)
{
    $module;
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderpro` ADD COLUMN `customcss` text NULL DEFAULT ""');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderprofields` ADD COLUMN `extra_option` tinyint(1) NULL DEFAULT "0"');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderprofields` ADD COLUMN `condition` tinyint(1) NOT NULL DEFAULT "0"');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderprofields` ADD COLUMN `condition_display` tinyint(1) NOT NULL DEFAULT "0"');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderprofields` ADD COLUMN `condition_must_match` tinyint(1) NOT NULL DEFAULT "0"');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderprofields` ADD COLUMN `condition_listoptions` text NULL DEFAULT ""');
    return true;
}