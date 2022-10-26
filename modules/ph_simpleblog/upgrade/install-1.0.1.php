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

function upgrade_module_1_0_1()
{
    return Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'simpleblog_post` ADD date_add DATETIME NOT NULL')
        && Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'simpleblog_post` ADD date_upd DATETIME NOT NULL');
}
