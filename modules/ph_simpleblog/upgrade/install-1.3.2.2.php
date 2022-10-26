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

function upgrade_module_1_3_2_2($object)
{
    if (!Shop::isFeatureActive()) {
        $categories = SimpleBlogCategory::getCategories(Context::getContext()->language->id, false);

        foreach ($categories as $id_category => $category) {
            $instance = new SimpleBlogCategory($id_category, Context::getContext()->language->id);
            $instance->associateTo(Shop::getCompleteListOfShopsID());
            unset($instance);
        }
    }

    return true;
}
