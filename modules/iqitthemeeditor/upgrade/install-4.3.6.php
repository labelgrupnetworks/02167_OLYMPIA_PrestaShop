<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_4_3_6($object)
{

    Configuration::updateValue($object->cfgName . 'pp_image_column_nb', 1);
    Configuration::updateValue($object->cfgName . 'pp_tabs_placement', 'footer');
    Configuration::updateValue($object->cfgName . 'c_accordion_txt', '');
    Configuration::updateValue($object->cfgName . 'c_accordion_typo', '');
    Configuration::updateValue($object->cfgName . 'checkout_header', 'default');
    Configuration::updateValue($object->cfgName . 'checkout_footer', 'default');
    Configuration::updateValue($object->cfgName . 'checkout_colors', 0);


    $hook_name = 'displayCheckoutFooter';

    $id_hook = Hook::getIdByName($hook_name);
    // If hook does not exist, we create it
    if (!$id_hook) {
        $new_hook = new Hook();
        $new_hook->name = pSQL($hook_name);
        $new_hook->title = pSQL($hook_name);
        $new_hook->position = 1;
        $new_hook->add();
    }


    $object->setCachedOptions();

    return true;
}



