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

function upgrade_module_4_6_0($module)
{
    Configuration::updateValue($module->prefix.'COOKIES_SELECTED', 1);
    return true;
}
