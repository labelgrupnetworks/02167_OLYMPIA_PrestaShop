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

function upgrade_module_1_2_3($object)
{
    Configuration::updateValue('OPC_SHIPPING_COMPATIBILITY', false);
    Configuration::updateValue('OPC_REQUIRED_LOGIN_CUSTOMER', true);

    $json_networks = Configuration::get('OPC_SOCIAL_NETWORKS');
    $json_networks = Tools::jsonDecode($json_networks);

    $json_networks->biocryptology = array(
        'network'       => 'Biocryptology',
        'name_network'  => 'Biocryptology',
        'client_id'     => '',
        'client_secret' => '',
        'scope'         => 'openid+profile+email+address',
        'class_icon'    => 'biocryptology'
    );

    Configuration::updateValue('OPC_SOCIAL_NETWORKS', Tools::jsonEncode($json_networks));

    $object->registerHook('actionCustomerLogoutAfter');

    return true;
}
