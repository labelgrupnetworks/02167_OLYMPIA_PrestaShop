<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_4_3_3($object)
{

	$version = '4.0.0';
	$name = 'iqitmegamenu';

    Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'module` m
            SET m.version = \'' . pSQL($version) . '\'
            WHERE m.name = \'' . pSQL($name) . '\'');

    $version = '4.0.0';
	$name = 'iqitfreedeliverycount';

    Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'module` m
            SET m.version = \'' . pSQL($version) . '\'
            WHERE m.name = \'' . pSQL($name) . '\'');


    $hook_name = 'displayBelowHeader';

    $id_hook = Hook::getIdByName($hook_name);
    // If hook does not exist, we create it
    if (!$id_hook) {
        $new_hook = new Hook();
        $new_hook->name = pSQL($hook_name);
        $new_hook->title = pSQL($hook_name);
        $new_hook->position = 1;
        $new_hook->add();
    }


    $hook_name = 'displayAfterProductThumbs2';

    $id_hook = Hook::getIdByName($hook_name);
    // If hook does not exist, we create it
    if (!$id_hook) {
        $new_hook = new Hook();
        $new_hook->name = pSQL($hook_name);
        $new_hook->title = pSQL($hook_name);
        $new_hook->position = 1;
        $new_hook->add();
    }

    Configuration::updateValue($object->cfgName . 'pp_cart_mobile', 'default');
    Configuration::updateValue($object->cfgName . 'pp_cart_mobile_bg', '#ffffff');
    Configuration::updateValue($object->cfgName . 'h_user_dropdown', 0);

    $object->setCachedOptions();

    return true;
}
