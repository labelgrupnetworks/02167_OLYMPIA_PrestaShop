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

class OnePageCheckoutPSResetECModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $moduleName = Tools::getValue('payment_name', '');
        if (!empty($moduleName) && $moduleName === 'express_checkout_schortcut') {
            $token = Tools::getValue('token', '');

            if (Context::getContext()->customer->isLogged() && Context::getContext()->customer->secure_key === $token) {
                Context::getContext()->cookie->__unset('paypal_ecs');
                Context::getContext()->cookie->__unset('paypal_ecs_email');

                Tools::redirect('order');
            }
        }

        header('HTTP/1.0 403 Forbidden');
        echo '<h1>Execution not allowed.</h1>';
        exit();
    }
}
