<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_4_3_5($object)
{
    Configuration::updateValue($object->cfgName . 'cp_position', 0);



    Configuration::updateValue($object->cfgName . 'cp_float_bg', '#ffffff');
    Configuration::updateValue($object->cfgName . 'cp_float_txt', '');
    Configuration::updateValue($object->cfgName . 'cp_float_boxshadow', '');
    Configuration::updateValue($object->cfgName . 'cp_float_border', '');
    Configuration::updateValue($object->cfgName . 'pp_image_layout', 'carousel');


    $object->setCachedOptions();

    return true;
}
