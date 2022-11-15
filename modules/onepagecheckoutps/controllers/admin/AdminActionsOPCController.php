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
 *
 * @category  PrestaShop
 * @category  Module
 *
 * Revision   6
 */

class AdminActionsOPCController extends ModuleAdminController
{
    public function init()
    {
        parent::init();

        if (!Validate::isLoadedObject($this->context->employee) || !$this->context->employee->isLoggedBack()) {
            die('You do not have permission to make this request');
        }

        $module_name = 'onepagecheckoutps';

        if (!Tools::isSubmit('token')
            || Tools::encrypt($module_name.'/index') != Tools::getValue('token')
            || !Module::isInstalled($module_name)
        ) {
            $params = array(
                'token' => Tools::getAdminTokenLite('AdminModules'),
                'configure' => $module_name
            );
            $url = Dispatcher::getInstance()->createUrl('AdminModules', $this->context->language->id, $params);

            Tools::redirectAdmin($url);
        }

        if (Tools::isSubmit('action')) {
            $action = Tools::getValue('action');
            $module = Module::getInstanceByName($module_name);
            $exist_method = false;
            $exist_method_core = false;

            if (!method_exists($module, $action)) {
                if (method_exists($module->core, $action)) {
                    $exist_method_core = true;
                }
            } else {
                $exist_method = true;
            }

            if ($exist_method || $exist_method_core) {
                define('_PTS_SHOW_ERRORS_', true);

                $data_type = 'json';
                if (Tools::isSubmit('dataType')) {
                    $data_type = Tools::getValue('dataType');
                }

                switch ($data_type) {
                    case 'html':
                        if ($exist_method_core) {
                            die($module->core->$action());
                        }

                        die($module->$action());
                    case 'json':
                        if ($exist_method_core) {
                            $response = Tools::jsonEncode($module->core->$action());
                        } else {
                            $response = Tools::jsonEncode($module->$action());
                        }

                        die($response);
                    default:
                        die('Invalid data type.');
                }
            } else {
                die('403 Forbidden');
            }
        } else {
            die('403 Forbidden');
        }
    }
}
