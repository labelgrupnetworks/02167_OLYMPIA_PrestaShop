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

function upgrade_module_1_2_0_1($object)
{
    Configuration::updateGlobalValue('PH_CATEGORY_IMAGE_X', '535');
    Configuration::updateGlobalValue('PH_CATEGORY_IMAGE_Y', '150');

    return true;
}
