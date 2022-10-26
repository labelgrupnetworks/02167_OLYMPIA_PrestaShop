<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_4_4_2($object)
{
    $hook_name = 'displayAboveMobileMenu';

    $id_hook = Hook::getIdByName($hook_name);
    // If hook does not exist, we create it
    if (!$id_hook) {
        $new_hook = new Hook();
        $new_hook->name = pSQL($hook_name);
        $new_hook->title = pSQL($hook_name);
        $new_hook->position = 1;
        $new_hook->add();
    }


    $hook_name = 'displayBelowMobileMenu';

    $id_hook = Hook::getIdByName($hook_name);
    // If hook does not exist, we create it
    if (!$id_hook) {
        $new_hook = new Hook();
        $new_hook->name = pSQL($hook_name);
        $new_hook->title = pSQL($hook_name);
        $new_hook->position = 1;
        $new_hook->add();
    }


    $hook_name = 'displayCartAjaxInfoModal';

    $id_hook = Hook::getIdByName($hook_name);
    // If hook does not exist, we create it
    if (!$id_hook) {
        $new_hook = new Hook();
        $new_hook->name = pSQL($hook_name);
        $new_hook->title = pSQL($hook_name);
        $new_hook->position = 1;
        $new_hook->add();
    }


    $hook_name = 'displayCartAjaxInfoBlock';

    $id_hook = Hook::getIdByName($hook_name);
    // If hook does not exist, we create it
    if (!$id_hook) {
        $new_hook = new Hook();
        $new_hook->name = pSQL($hook_name);
        $new_hook->title = pSQL($hook_name);
        $new_hook->position = 1;
        $new_hook->add();
    }

    $hook_name = 'displayHeaderTop';

    $id_hook = Hook::getIdByName($hook_name);
    // If hook does not exist, we create it
    if (!$id_hook) {
        $new_hook = new Hook();
        $new_hook->name = pSQL($hook_name);
        $new_hook->title = pSQL($hook_name);
        $new_hook->position = 1;
        $new_hook->add();
    }

    $hook_name = 'displayNotFound';

    $id_hook = Hook::getIdByName($hook_name);
    // If hook does not exist, we create it
    if (!$id_hook) {
        $new_hook = new Hook();
        $new_hook->name = pSQL($hook_name);
        $new_hook->title = pSQL($hook_name);
        $new_hook->position = 1;
        $new_hook->add();
    }


    Configuration::updateValue($object->cfgName . 'pl_crsl_overflow_visible', 0);
    Configuration::updateValue($object->cfgName . 'pl_faceted_checkbox_radio', 'default');

    return true;
}



