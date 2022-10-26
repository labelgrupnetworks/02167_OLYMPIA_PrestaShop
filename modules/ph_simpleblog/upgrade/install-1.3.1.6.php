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

function upgrade_module_1_3_1_6($object)
{
    if (function_exists('date_default_timezone_get')) {
        $timezone = @date_default_timezone_get();
    } else {
        $timezone = 'Europe/Warsaw';
    }

    Configuration::updateGlobalValue('PH_BLOG_TIMEZONE', $timezone);

    return true;
}
