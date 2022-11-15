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

function upgrade_module_1_3_0($module)
{
    $module
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderpro` ADD COLUMN `admin_attachfiles` text NULL DEFAULT ""');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderpro` ADD COLUMN `sender_attachfiles` text NULL DEFAULT ""');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'gformbuilderpro_lang` DROP COLUMN `submittitle`');
    Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gform_analytics` (
                `id_gform_analytics` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_gformbuilderpro` int(10) unsigned NOT NULL,
                `ip_address` varchar(255) DEFAULT NULL,
                `browser` varchar(255) DEFAULT NULL,
                `browser_version` varchar(255) DEFAULT NULL,
                `user_agent` text DEFAULT NULL,
                `platform` varchar(255) DEFAULT NULL,
                `id_customer` int(10) NULL DEFAULT  "0",
                `date_add` datetime NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_gform_analytics`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
    /* change data struct */
    $sql = '
        SELECT a.*,b.type 
        FROM ' . _DB_PREFIX_ . 'gformbuilderprofields_lang a
        LEFT JOIN ' . _DB_PREFIX_ . 'gformbuilderprofields b ON (a.id_gformbuilderprofields = b.id_gformbuilderprofields)
        WHERE b.type IN ("checkbox","radio","select","survey")
   ';
   $result = Db::getInstance()->executeS($sql);
   if($result){
        foreach($result as $field){
            
            if($field['type'] == 'survey'){
                if($field['value'] !='' || $field['description'] !=''){
                    $value = str_replace(",","\n",$field['value']);
                    $description = str_replace(",","\n",$field['description']);
                    $update_sql = 'UPDATE ' . _DB_PREFIX_ . 'gformbuilderprofields_lang 
                                    SET value = "'.pSql($value).'",description="'.pSql($description).'"  
                                    WHERE id_lang='.(int)$field['id_lang'].' AND id_gformbuilderprofields = '.(int)$field['id_gformbuilderprofields'];
                    Db::getInstance()->execute($update_sql);
                }
            }else{
                if($field['value'] !=''){
                    $value = str_replace(",","\n",$field['value']);
                    $update_sql = 'UPDATE ' . _DB_PREFIX_ . 'gformbuilderprofields_lang 
                                    SET value = "'.pSql($value).'" 
                                    WHERE id_lang='.(int)$field['id_lang'].' AND id_gformbuilderprofields = '.(int)$field['id_gformbuilderprofields'];
                    Db::getInstance()->execute($update_sql);
                }
            }
        }
   }
    /* Add new tab */
    $formObj = Module::getInstanceByName('gformbuilderpro');
    $formObj->_createTab();
    /* register new hook */
    $formObj->registerHook('actionMailAlterMessageBeforeSend');
    return true;
}