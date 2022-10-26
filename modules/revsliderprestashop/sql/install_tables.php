<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

$sql = array();
$sql[] = "CREATE TABLE " . _DB_PREFIX_ . RevLoader::TABLE_SLIDER . " (
			  id int(9) NOT NULL AUTO_INCREMENT,
			  title tinytext NOT NULL,
			  alias tinytext,
			  params LONGTEXT NOT NULL,
			  settings LONGTEXT NULL,
			  type VARCHAR(191) NOT NULL DEFAULT '',
			  UNIQUE KEY id (id)
			);";

$sql[] = "CREATE TABLE " . _DB_PREFIX_ . RevLoader::TABLE_SLIDES . " (
			  id int(9) NOT NULL AUTO_INCREMENT,
			  slider_id int(9) NOT NULL,
			  slide_order int not NULL,
			  params LONGTEXT NOT NULL,
			  layers LONGTEXT NOT NULL,
			  settings LONGTEXT NOT NULL DEFAULT '',
			  UNIQUE KEY id (id)
			);";

$sql[] = "CREATE TABLE " . _DB_PREFIX_ . RevLoader::TABLE_STATIC_SLIDES . " (
			  id int(9) NOT NULL AUTO_INCREMENT,
			  slider_id int(9) NOT NULL,
			  params LONGTEXT NOT NULL,
			  layers LONGTEXT NOT NULL,
			  settings text NOT NULL,
			  UNIQUE KEY id (id)
			);";

$sql[] = "CREATE TABLE " . _DB_PREFIX_ . RevLoader::TABLE_CSS . " (
			  id int(9) NOT NULL AUTO_INCREMENT,
			  handle TEXT NOT NULL,
			  settings LONGTEXT,
			  hover LONGTEXT,
			  advanced LONGTEXT,
			  params LONGTEXT NOT NULL,
			  UNIQUE KEY id (id)
			);";

$sql[] = "CREATE TABLE " . _DB_PREFIX_ . RevLoader::TABLE_LAYER_ANIMATIONS . " (
			  id int(9) NOT NULL AUTO_INCREMENT,
			  handle TEXT NOT NULL,
			  params TEXT NOT NULL,
			  settings text NULL,
			  UNIQUE KEY id (id)
			);";

$sql[] = "CREATE TABLE " . _DB_PREFIX_ . RevLoader::TABLE_NAVIGATIONS . " (
			  id int(9) NOT NULL AUTO_INCREMENT,
			  name VARCHAR(191) NOT NULL,
			  handle VARCHAR(191) NOT NULL,
			  type VARCHAR(191) NOT NULL,
			  css LONGTEXT NOT NULL,
			  markup LONGTEXT NOT NULL,
			  settings LONGTEXT NULL,
			  UNIQUE KEY id (id)
			);";



$sql[] = "CREATE TABLE " . _DB_PREFIX_ . RevLoader::TABLE_OPTIONS . " (
                option_id INT(10) NOT NULL AUTO_INCREMENT, 
                option_name VARCHAR(100) NOT NULL,        
                option_value longtext NOT NULL, 
                PRIMARY KEY (option_id)
			);";


$sql[] = "CREATE TABLE " . _DB_PREFIX_ . "revslider_backup_slides" . " (
	id int(9) NOT NULL AUTO_INCREMENT,
	slide_id int(9) NOT NULL,
	slider_id int(9) NOT NULL,
	slide_order int not NULL,
	params LONGTEXT NOT NULL,
	layers LONGTEXT NOT NULL,
	settings TEXT NOT NULL,
	created DATETIME NOT NULL,
	session VARCHAR(100) NOT NULL,
	static VARCHAR(20) NOT NULL,
	PRIMARY KEY id (id)
);";

foreach ( $sql as $query ) {
	if ( Db::getInstance()->execute( $query ) == false ) {
		return false;
	}
}
