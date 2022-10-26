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

function upgrade_module_1_0_7($object)
{
    Configuration::updateValue('PH_BLOG_COLUMNS', 'prestashop');
    Configuration::updateValue('PH_BLOG_LAYOUT', 'left_sidebar');
    Configuration::updateValue('PH_BLOG_LIST_LAYOUT', 'grid');
    Configuration::updateValue('PH_BLOG_GRID_COLUMNS', '2');
    Configuration::updateValue('PH_BLOG_MAIN_TITLE', 'Blog - what\'s new?');
    $object->registerHook('displayHeader');

    return true;
}
