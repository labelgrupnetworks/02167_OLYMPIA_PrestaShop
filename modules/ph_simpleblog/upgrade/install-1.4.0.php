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

function upgrade_module_1_4_0($object)
{
    if (Configuration::get('PH_BLOG_NATIVE_COMMENTS')) {
        Configuration::updateGlobalValue('PH_BLOG_COMMENTS_SYSTEM', 'native');
    } else {
        Configuration::updateGlobalValue('PH_BLOG_COMMENTS_SYSTEM', 'facebook');
    }

    return true;
}
