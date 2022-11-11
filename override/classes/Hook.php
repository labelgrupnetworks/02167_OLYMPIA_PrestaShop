<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2019 Innova Deluxe SL
 * @license   INNOVADELUXE
 */
class Hook extends HookCore
{
    /*
    * module: idxcookies
    * date: 2022-11-11 05:13:58
    * version: 4.8.2
    */
    public static function getHookModuleExecList($hook_name = null)
    {
        $module_list = parent::getHookModuleExecList($hook_name);
        $context = Context::getContext();
        if (isset($context->controller->controller_type) && in_array($context->controller->controller_type, array('admin','moduleadmin'))) {
            return $module_list;
        }
        $hooks = array(
            'moduleRoutes'
        );
        if (in_array($hook_name, $hooks)) {
            return $module_list;
        }
        if ((!Module::isEnabled('idxcookies') && !Module::isEnabled('deluxecookies')) || !is_array($module_list)) {
            return $module_list;
        }
        if (Module::isEnabled('deluxecookies')) {
            $idxcookies = Module::getInstanceByName('deluxecookies');
        } elseif (Module::isEnabled('idxcookies')) {
            $idxcookies = Module::getInstanceByName('idxcookies');
        } else {
            return $module_list;
        }
        if (Validate::isLoadedObject($idxcookies)) {
            return $idxcookies->filterHookModuleExecList($module_list, $hook_name);
        }
        return $module_list;
    }
}
