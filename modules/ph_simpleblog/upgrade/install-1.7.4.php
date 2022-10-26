<?php
/**
 * Blog for PrestaShop module by Krystian Podemski from PrestaHome.
 *
 * @author    Krystian Podemski <krystian@prestahome.com>
 * @copyright Copyright (c) 2008-2020 Krystian Podemski - www.PrestaHome.com / www.Podemski.info
 * @license   You only can use module, nothing more!
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_7_4($module)
{
    $module->hasFieldInDatabaseIfNotCreateOne('simpleblog_post_lang', 'canonical', 'text NOT NULL AFTER `meta_keywords`');
    $module->hasFieldInDatabaseIfNotCreateOne('simpleblog_category_lang', 'canonical', 'text NOT NULL AFTER `meta_keywords`');

    return true;
}
