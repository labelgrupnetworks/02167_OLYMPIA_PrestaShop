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

function upgrade_module_1_2_2_5($object)
{
    Configuration::updateGlobalValue('PH_BLOG_COMMENT_STUFF_HIGHLIGHT', 1);
    Configuration::updateGlobalValue('PH_BLOG_COMMENT_ALLOW', 0);
    Configuration::updateGlobalValue('PH_BLOG_COMMENT_NOTIFY_EMAIL', Configuration::get('PS_SHOP_EMAIL'));

    return true;
}
