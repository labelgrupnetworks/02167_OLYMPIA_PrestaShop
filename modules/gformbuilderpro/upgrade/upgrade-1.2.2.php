<?php
/**
* This is main class of module. 
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2017 Globo ., Jsc
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_2_2($module)
{
    $module;
	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderpro` ADD COLUMN `using_condition` TINYINT(1) NULL DEFAULT  "0"');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderpro` ADD COLUMN `condition_configs` MEDIUMTEXT NULL');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderpro` MODIFY `sendto` text NULL DEFAULT NULL');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderpro` ADD COLUMN `autostar` TINYINT(1) NULL DEFAULT  "0"');
    return true;
}