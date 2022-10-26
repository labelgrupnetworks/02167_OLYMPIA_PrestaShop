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

function upgrade_module_1_1_0($object)
{
    return Configuration::updateValue('PH_BLOG_LOAD_FA', '1')
            && Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'simpleblog_post` ADD is_featured tinyint(1) NOT NULL DEFAULT \'0\'');
}
