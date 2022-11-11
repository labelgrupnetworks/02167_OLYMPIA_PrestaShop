<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2015 Innova Deluxe SL

 * @license   INNOVADELUXE
 */

$sql = array();
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'idxcookies';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'idxcookies_type';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'idxcookies_type_lang';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'idxcookies_templates';
$sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'idxcookies_templates_lang';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
