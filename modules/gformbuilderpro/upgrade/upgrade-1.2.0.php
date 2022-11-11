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

function upgrade_module_1_2_0($module)
{
    $module;
	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderpro` ADD COLUMN `ispopup` TINYINT(1) NULL DEFAULT  "0"');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderpro_lang` ADD COLUMN `popup_label` text  NULL');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderpro_lang` ADD COLUMN `replysubject` text  NULL');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderpro_lang` ADD COLUMN `replyemailtemplate` MEDIUMTEXT  NULL');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformrequest` ADD COLUMN `id_lang` int(10) unsigned NULL');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformrequest` ADD COLUMN `sender` text  NULL');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformrequest` ADD COLUMN `star` TINYINT(1) NULL DEFAULT  "0"');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformrequest` ADD COLUMN `viewed` TINYINT(1) NULL DEFAULT  "0"');
    Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gformrequest_reply` (
                `id_gformrequest_reply` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_gformrequest` int(10) unsigned NOT NULL,
                `replyemail` text NULL,
                `subject` text NULL,
                `request` MEDIUMTEXT  NULL,
                `date_add` datetime DEFAULT NULL,
                PRIMARY KEY (`id_gformrequest_reply`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
    /* new hook */
    $formObj = Module::getInstanceByName('gformbuilderpro');
    $formObj->registerHook("actionAdminControllerSetMedia");
	return true;
}