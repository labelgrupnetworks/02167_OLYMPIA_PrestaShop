<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2018 Innova Deluxe SL
 * @license   INNOVADELUXE
 */

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'idxcookies (
  `id_cookie` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_template` int(11) NOT NULL,
  `domain` varchar(64) DEFAULT NULL,
  `name` varchar(64) NOT NULL,
  `id_cookie_type` int(11) UNSIGNED NOT NULL,
  `id_shop` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `module` varchar(255) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_cookie`),
  KEY `id_template` (`id_template`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'idxcookies_type (
      id_cookie_type int(11) unsigned NOT NULL auto_increment,
      imperative tinyint(1) unsigned NOT NULL default \'1\',
      PRIMARY KEY (id_cookie_type)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'idxcookies_type_lang (
      id_cookie_type_lang int(11) unsigned NOT NULL auto_increment,
      id_cookie_type int(11) unsigned NOT NULL,
      id_lang int(11) unsigned NOT NULL,
      name varchar(64) NOT NULL,
      description text NULL,
      PRIMARY KEY (id_cookie_type_lang)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

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
foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
