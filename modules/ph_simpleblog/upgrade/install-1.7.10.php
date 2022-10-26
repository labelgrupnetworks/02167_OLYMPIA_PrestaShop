<?php
/**
 * Blog for PrestaShop module by PrestaHome Team.
 *
 * @author    PrestaHome Team <support@prestahome.com>
 * @copyright Copyright (c) 2011-2021 PrestaHome Team - www.PrestaHome.com
 * @license   You only can use module, nothing more!
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_7_10($module)
{
    $result = true;

    $result &= $module->registerHook('registerGDPRConsent');
    $result &= $module->registerHook('actionDeleteGDPRCustomer');
    $result &= $module->registerHook('actionExportGDPRData');

    return (bool) $result;
}
