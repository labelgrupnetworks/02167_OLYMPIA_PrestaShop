<?php
/**
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innovadeluxe SL
* @copyright 2016 Innovadeluxe SL

* @license   INNOVADELUXE
*/

function upgrade_module_4_4_0($module)
{
    Configuration::updateValue($module->prefix.'RELOAD', 1);
    $module->removeOverride('Hook') && $module->addOverride('Hook');
    return true;
}
