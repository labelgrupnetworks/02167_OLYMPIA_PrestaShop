<?php

/**
 * 2019-2020 Labelgrup
 *
 * NOTICE OF LICENSE
 *
 * READ ATTACHED LICENSE.TXT
 *
 *  @author    Víctor Cinca <vcinca@labelgrup.com>
 *  @copyright 2019-2020 Labelgrup
 *  @license   LICENSE.TXT
 */

//opcache_reset();
ini_set('max_execution_time', '0');

include_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');
// include_once(dirname(__FILE__) . '/vendor/autoload.php');
require_once(dirname(__FILE__) . '/label_olympia.php');

$remainingDays = (Tools::getIsset('d') ? (int) Tools::getValue('d') : null);
echo "Deshabilitando caché....\n";
Db::getInstance()->disableCache();
echo "Caché deshabilitada....\n";
// Obtenemos los productos
$module = Module::getInstanceByName('label_olympia');
$result = $module->sendReminderEmail($remainingDays);
Db::getInstance()->enableCache();