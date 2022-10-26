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

function upgrade_module_1_2_2_6($object)
{
    Configuration::updateGlobalValue('PH_BLOG_FACEBOOK_MODERATOR', '');
    Configuration::updateGlobalValue('PH_BLOG_FACEBOOK_APP_ID', '');
    Configuration::updateGlobalValue('PH_BLOG_FACEBOOK_COLOR_SCHEME', 'light');

    return true;
}
