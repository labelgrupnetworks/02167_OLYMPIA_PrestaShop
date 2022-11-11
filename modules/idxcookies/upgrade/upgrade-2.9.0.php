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

function upgrade_module_2_9_0($module)
{
    if (!$module->isRegisteredInHook('backOfficeHeader')) {
        $module->registerHook('backOfficeHeader');
    }
    return true;
}
