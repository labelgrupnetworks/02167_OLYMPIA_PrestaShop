<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_6_2_21_2($module) {

    if (!$module->moduleControllerRegistration()) return false;

    if (!upgradeDbTables()) return false;

    if (!uninstallOldTabs()) return false;

    return true;

}

function upgradeDbTables() {

    $queries_set = [
        'revslider_options' => [
            'id' => 'ALTER TABLE `'._DB_PREFIX_.'revslider_options` CHANGE `id` `option_id` INT(10) NOT NULL AUTO_INCREMENT;',
            'name' => 'ALTER TABLE `'._DB_PREFIX_.'revslider_options` CHANGE `name` `option_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;',
            'value' => 'ALTER TABLE `'._DB_PREFIX_.'revslider_options` CHANGE `value` `option_value` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;'
        ],
        'revslider_layer_animations' => [
            'settings' => 'ALTER TABLE `'._DB_PREFIX_.'revslider_layer_animations` ADD `settings` TEXT NULL;'
        ],
        'revslider_navigations' => [
            'type' => 'ALTER TABLE `'._DB_PREFIX_.'revslider_navigations` ADD `type` VARCHAR(191) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;'
        ],
        'revslider_static_slides' => [
            'settings' => 'ALTER TABLE `'._DB_PREFIX_.'revslider_static_slides` ADD `settings` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;'
        ],
    ];

    foreach ($queries_set as $table_name => $queries) {
        $table_columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `'._DB_PREFIX_.$table_name.'`');

        $columns = array_map(function($column) {
            return $column['Field'];
        }, $table_columns);

        foreach ($queries as $column => $query) {

            if ($table_name == 'revslider_options') {
                if ((array_search($column, $columns)) === false) continue;
            } else {
                if ((array_search($column, $columns)) !== false) continue;
            }

            if (!Db::getInstance()->execute($query)) return false;
        }
    }

    return true;
}

function uninstallOldTabs() {
    $tabList = [
        'AdminRevolutionsliderSettings',
        'AdminRevolutionsliderGlobalSettings',
        'AdminRevolutionsliderAddons',
        'AdminRevolutionsliderNavigation'
    ];

    foreach ($tabList as $className) {
        $id_tab = Tab::getIdFromClassName($className);
        if ($id_tab != 0) {
            $tab = new Tab($id_tab);
            $r = $tab->delete();
        }
    }

    return true;
}