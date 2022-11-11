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

namespace OnePageCheckoutPS\Hook;

use Configuration;
use OnePageCheckoutPS;
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter;
use Tools;

class HookDisplayHeaderService extends AbstractHook
{
    private $module;

    protected $allowControllerList = array(
        'cart',
        'order',
        'authentication',
        'identity',
        'addresses',
    );

    public function __construct(OnePageCheckoutPS $module)
    {
        $this->module = $module;
    }

    protected function executeRun()
    {
        $parameters = $this->getParameters();
        if (!isset($parameters['context'])) {
            return;
        }

        $context = $parameters['context'];
        $controller = $context->controller;

        $coreService = $this->module->getService('onepagecheckoutps.core.core_service');

        if ($controller->php_self == 'cart'
            && !Tools::getIsset('ajax')
            && (!Tools::getIsset('action') || Tools::getValue('action') == 'show')
            && (!Tools::getIsset('opc'))
            && $context->cart->nbProducts() > 0
            && $context->cart->checkQuantities()
            && $this->module->getConfigurationList('OPC_REDIRECT_DIRECTLY_TO_OPC')
            && !$this->module->existNotification($controller)
        ) {
            $cartPresenter = new CartPresenter();
            $presentedCart = $cartPresenter->present($context->cart, true);

            if (count($presentedCart['products']) > 0 && !$presentedCart['minimalPurchaseRequired']) {
                Tools::redirect('order');
            }
        } elseif ($controller->php_self == 'cart'
            || ($this->module->getConfigurationList('OPC_REPLACE_AUTH_CONTROLLER') === false && $controller->php_self == 'authentication')
            || ($this->module->getConfigurationList('OPC_REPLACE_IDENTITY_CONTROLLER') === false && $controller->php_self == 'identity')
            || ($this->module->getConfigurationList('OPC_REPLACE_ADDRESSES_CONTROLLER') === false && $controller->php_self == 'addresses')
        ) {
            return;
        }

        //CSS
        $controller->registerStylesheet(
            'module-onepagecheckoutps-checkout',
            'modules/onepagecheckoutps/views/css/front/checkout.css',
            array(
                'media' => 'all',
                'priority' => 50,
            )
        );

        $fileOverrideCss = Tools::file_get_contents('modules/onepagecheckoutps/views/css/front/override.css');
        if (!empty($fileOverrideCss)) {
            $controller->registerStylesheet(
                'module-onepagecheckoutps-override',
                'modules/onepagecheckoutps/views/css/front/override.css',
                array(
                    'media' => 'all',
                    'priority' => 50,
                )
            );
        }

        $controller->registerStylesheet(
            'module-onepagecheckoutps-compatibilities',
            'modules/onepagecheckoutps/views/css/front/compatibilities.css',
            array(
                'media' => 'all',
                'priority' => 50,
            )
        );

        $controller->registerJavascript(
            'module-onepagecheckoutps-compatibilities',
            'modules/onepagecheckoutps/views/js/front/compatibilities.js',
            array(
                'priority' => 50,
                'attribute' => 'defer',
            )
        );

        $controller->registerJavascript(
            'module-onepagecheckoutps-checkout',
            'modules/onepagecheckoutps/views/js/front/checkout.js',
            array(
                'priority' => 50,
                'attribute' => 'defer',
            )
        );

        $fileOverrideJs = Tools::file_get_contents('modules/onepagecheckoutps/views/js/front/override.js');
        if (!empty($fileOverrideJs)) {
            $controller->registerJavascript(
                'module-onepagecheckoutps-override',
                'modules/onepagecheckoutps/views/js/front/override.js',
                array(
                    'priority' => 50,
                    'attribute' => 'defer',
                )
            );
        }

        if (in_array($controller->php_self, array('order', 'addresses'))) {
            $addresses = $this->module->getService('onepagecheckoutps.core.addresses');
            if ($googleUrl = $addresses->getAutocompleteGoogleScript()) {
                $controller->registerJavascript(
                    sha1($googleUrl),
                    $googleUrl,
                    array(
                        'server' => 'remote',
                        'priority' => 50,
                        'attribute' => 'defer',
                    )
                );
            }
        }

        if ($controller->php_self == 'order') {
            $controller->unregisterJavascript('modules-shoppingcart');

            $page = $controller->getTemplateVarPage();
            $page['body_classes']['opc-step-header'] = true;
            $context->smarty->assign('page', $page);
        }

        if ($securitypro = $coreService->isModuleActive('securitypro')) {
            if (Configuration::get('PRO_RECAPTCHA_V3_REGISTRATION_ACTIVATE')) {
                return $securitypro->displayGoogleRecaptchaV3('opc_register');
            }
        }
    }
}
