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
 */

class OnePageCheckoutPSCronModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        if (!$this->module->core->isModuleActive($this->module->name) ||
            !Tools::isSubmit('token') ||
            Tools::encrypt($this->module->name . '/cron') != Tools::getValue('token')
        ) {
            header('HTTP/1.0 403 Forbidden');
            echo '<h1>Execution not allowed.</h1>';
            exit();
        }

        $result = $this->module->deleteEmptyAddressesOPC();

        if (isset($result['message'])) {
            die($result['message']);
        }
    }
}
