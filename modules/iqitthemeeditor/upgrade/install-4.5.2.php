<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_4_5_2($object)
{
    $object->registerHook('actionDispatcher');
    return true;
}

