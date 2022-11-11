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
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderprofields` ADD COLUMN `dynamicval` VARCHAR(255) NULL DEFAULT ""');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderpro` ADD COLUMN `sender_name` text NULL DEFAULT ""');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformrequest` ADD COLUMN `sender_name` text NULL DEFAULT ""');
    return true;
}