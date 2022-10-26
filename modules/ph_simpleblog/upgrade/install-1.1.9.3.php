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

function upgrade_module_1_1_9_3($object)
{
    if (file_exists(_PS_MODULE_DIR_.'ph_simpleblog/controllers/admin/AdminSimpleBlogSettings.php')) {
        @unlink(_PS_MODULE_DIR_.'ph_simpleblog/controllers/admin/AdminSimpleBlogSettings.php');
    }

    return true;
}
