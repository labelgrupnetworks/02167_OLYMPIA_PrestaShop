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

function upgrade_module_1_1_4($object)
{
    $override = 'override/controllers/front/OrderController.php';
    if ($object->core->existOverride($override, '/KEY_'.$object->prefix_module.'/')) {
        $path_origin = _PS_ROOT_DIR_.'/'.$override;
        $path_destination = _PS_ROOT_DIR_.'/'.$override.'_BK-'.$object->prefix_module.'-PTS_'.date('Y-m-d');

        rename($path_origin, $path_destination);

        Tools::generateIndex();

        $object->addOverride('OrderController');
    }

    return true;
}
