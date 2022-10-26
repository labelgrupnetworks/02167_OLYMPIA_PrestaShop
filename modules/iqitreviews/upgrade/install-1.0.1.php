<?php
/**
 * 2007-2015 IQIT-COMMERCE.COM
 *
 * NOTICE OF LICENSE
 *
 *  @author    IQIT-COMMERCE.COM <support@iqit-commerce.com>
 *  @copyright 2007-2015 IQIT-COMMERCE.COM
 *  @license   GNU General Public License version 2
 *
 * You can not resell or redistribute this software.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_1($object)
{



    Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'iqitreviews_products`
	CHANGE COLUMN `id_review` `id_iqitreviews_products` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT FIRST');



    return true;
}
