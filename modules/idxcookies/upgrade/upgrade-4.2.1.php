<?php
/**
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innovadeluxe SL
* @copyright 2019 Innovadeluxe SL

* @license   INNOVADELUXE
*/

function upgrade_module_4_2_1($module)
{
    $module->checktables();
    $sql = array();
    $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'idxcookies` ADD `module` VARCHAR(255) NULL AFTER `id_shop`;';
    $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'idxcookies` ADD `id_template` INT NOT NULL AFTER `id_cookie`, ADD INDEX (`id_template`);';
    $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'idxcookies` ADD `date_add` DATETIME NOT NULL AFTER `module`, ADD `date_upd` DATETIME NOT NULL AFTER `date_add`;';
    $sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'idxcookies_templates (
  `id_template` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `hook` varchar(255) NOT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `tag_script` tinyint(1) DEFAULT NULL,
  `tag_literal` tinyint(1) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_template`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
    $sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'idxcookies_templates_lang (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_template` int(11) NOT NULL,
  `id_lang` int(11) NOT NULL,
  `id_shop` int(11) NOT NULL,
  `contenido` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_lang` (`id_lang`,`id_shop`),
  KEY `id_template` (`id_template`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
    foreach ($sql as $elemento) {
        try {
            Db::getInstance()->execute($elemento);
        } catch (Exception $e) {
            PrestaShopLogger::addLog('[MODULE] '.Tools::strtoupper($module->name).': '.$e->getMessage(), 3);
        }
    }
    $module->registerHook('displayBeforeBodyClosingTag');
    $module->registerHook('displayAfterBodyOpeningTag');
    $lang_examples = Language::getLanguages(false);
    $text_delete_example = array();

    foreach ($lang_examples as $lang_example) {
        $text_delete_example[$lang_example['id_lang']] = urlencode('
                <p>Se informa al usuario de que tiene la posibilidad de configurar su navegador de modo que se le informe de la recepción de cookies, pudiendo, si así lo desea, impedir que sean instaladas en su disco duro.</p>
<p>A continuación le proporcionamos los enlaces de diversos navegadores, a través de los cuales podrá realizar dicha configuración:</p>
<p><strong><em>Firefox desde aquí:</em></strong> <a target="_blank" href="https://support.mozilla.org/t5/Cookies-y-cach%C3%A9/Habilitar-y-deshabilitar-cookies-que-los-sitios-web-utilizan/ta-p/13811">http://support.mozilla.org/es/kb/habilitar-y-deshabilitar-cookies-que-los-sitios-web</a></p>
<p><strong><em>Chrome desde aquí:</em></strong> <a target="_blank" href="https://support.google.com/chrome/answer/95647?hl=es">https://support.google.com/chrome/answer/95647?hl=es</a></p>
<p><strong><em>Explorer desde aquí:</em></strong><span> <a target="_blank" href="https://support.microsoft.com/es-es/help/17442/windows-internet-explorer-delete-manage-cookies">https://support.microsoft.com/es-es/help/17442/windows-internet-explorer-delete-manage-cookies</a></span></p>
<p><strong><em>Safari desde aquí: </em></strong><a target="_blank" href="https://support.apple.com/kb/ph5042?locale=es_ES"><span>http://support.apple.com/kb/ph5042</span></a></p>
<p><strong><em>Opera desde aquí:</em></strong><a target="_blank" href="http://help.opera.com/Windows/11.50/es-ES/cookies.html"><span>http://help.opera.com/Windows/11.50/es-ES/cookies.html</span></a></p>
            ');
    }
    Configuration::updateValue(Tools::strtoupper($module->name).'_DELETECOOKIESTEXT', $text_delete_example);
    return true;
}
