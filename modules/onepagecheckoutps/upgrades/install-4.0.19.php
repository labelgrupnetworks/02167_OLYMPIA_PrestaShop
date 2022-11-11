<?php
/**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * @author    PresTeamShop SAS (Registered Trademark) <info@presteamshop.com>
 * @copyright 2011-2022 PresTeamShop SAS, All rights reserved.
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * @category  PrestaShop
 * @category  Module
 */

function upgrade_module_4_0_19($module)
{
    if (version_compare(_PS_VERSION_, '1.7.7.5', '>=')) {
        if (file_exists(_PS_OVERRIDE_DIR_.'classes/CartRule.php')) {
            try {
                $module->removeOverride('CartRule');
            } catch (Exception $e) {
                return $module->l('We add changes to the CartRule.php override file to fix compatibility error with PrestaShop versions equal to or greater than 1.7.7.5. The file could not be copied, it is necessary to copy it manually. To pass the changes made, please copy the file from /modules/onepagecheckoutps/override/classes/CartRule.php to /override/classes/CartRule.php', 'install-4.0.19');
            }
        }

        try {
            $module->addOverride('CartRule');
        } catch (Exception $ex) {
            return $module->l('Could not copy our override CartRule.php file to the store. To pass the changes made, please copy the file from /modules/onepagecheckoutps/override/classes/CartRule.php to /override/classes/CartRule.php', 'install-4.0.19');
        }
    }

    return true;
}
