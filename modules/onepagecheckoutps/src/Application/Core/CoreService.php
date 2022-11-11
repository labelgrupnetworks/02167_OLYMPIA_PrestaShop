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


namespace OnePageCheckoutPS\Application\Core;

use Configuration;
use Db;
use DbQuery;
use Module;
use OnePageCheckoutPS;
use OnePageCheckoutPS\Application\PresTeamShop;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Tools;
use Validate;

class CoreService
{
    private $module;
    private $contextProvider;

    public const SERVICE_NAME = 'onepagecheckoutps.core.core_service';

    public function __construct(OnePageCheckoutPS $module)
    {
        $this->module = $module;
        $this->contextProvider = $this->module->getContextProvider();
        $this->tokenManager = new CsrfTokenManager();
    }

    public function getModule()
    {
        return $this->module;
    }

    public function isModuleActive($moduleName, $functionExist = false)
    {
        if (Module::isInstalled($moduleName)) {
            $module = Module::getInstanceByName($moduleName);
            if (Validate::isLoadedObject($module) && $module->active) {
                if ($moduleName === $this->module->name) {
                    if (!$this->isVisible()) {
                        return false;
                    }
                }

                $sql = new DbQuery();
                $sql->from('module_shop', 'm');
                $sql->where('m.id_module = ' . (int) $module->id);
                $sql->where('m.enable_device & ' . (int) $this->contextProvider->getDevice());
                $sql->where('m.id_shop = ' . (int) $this->contextProvider->getShopId());

                $deviceActived = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

                if ($deviceActived) {
                    if ($functionExist) {
                        if (method_exists($module, $functionExist)) {
                            return $module;
                        } else {
                            return false;
                        }
                    }

                    return $module;
                }
            }
        }

        return false;
    }

    public function isVisible()
    {
        $displayModule = true;

        $isDebugEnabled = $this->module->getConfigurationList($this->module->prefix_module . '_ENABLE_DEBUG');
        if ($isDebugEnabled) {
            $displayModule = false;
            $myIp = Tools::getRemoteAddr();
            $debugIpList = $this->module->getConfigurationList($this->module->prefix_module . '_IP_DEBUG');

            if (in_array($myIp, explode(',', $debugIpList))) {
                $displayModule = true;
            }
        }

        if ($displayModule) {
            $moduleVersionRegistered = $this->module->getConfigurationList($this->module->prefix_module . '_VERSION');
            if ($moduleVersionRegistered != $this->module->version) {
                $displayModule = false;
            }
        }
        return $displayModule;
    }

    public function getToken($tokenId)
    {
        return $this->tokenManager->getToken($tokenId)->getValue();
    }

    public function validateAjaxRequest($tokenId)
    {
        $token = Tools::getValue('ptsToken');

        if (!Tools::isSubmit('ptsToken')
            || !$this->tokenManager->isTokenValid(new CsrfToken($tokenId, $token))
            || !$this->isAjaxRequest()
        ) {
            header('HTTP/1.0 403 Forbidden');
            echo '<h1>Execution not allowed.</h1>';
            exit();
        }
    }

    public function validateActionRequest($class, $action)
    {
        if (!Tools::isSubmit('action')
            || empty($action)
            || !method_exists($class, $action)
        ) {
            header('HTTP/1.0 403 Forbidden');
            echo '<h1>The action was not found.</h1>';
            exit();
        }
    }

    public function isAjaxRequest()
    {
        return true;
        //return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false;
    }

    public function executeActionRequest($class, $tokenId = '')
    {
        $action = Tools::getValue('action');

        if (empty($tokenId)) {
            $tokenId = $this->module->name;
        }

        $this->validateAjaxRequest($tokenId);
        $this->validateActionRequest($class, $action);

        $response = $class->{$action}();
        $dataType = Tools::getValue('dataType', 'json');
        if ($dataType === 'json') {
            $response = (new JsonResponse(
                $response,
                Response::HTTP_OK
            ))->getContent();
        }

        exit($response);
    }

    public function getCommonVars()
    {
        return array(
            'isVirtualCart' => $this->contextProvider->isVirtualCart(),
            'isLogged' => $this->contextProvider->getCustomer()->isLogged()
                || $this->contextProvider->getCustomer()->isGuest(),
            'isCustomer' => $this->contextProvider->getCustomer()->isLogged(),
            'isGuest' => $this->contextProvider->getCustomer()->isGuest(),
            'isGuestAllowed' => (bool) Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
            'token' => Tools::getToken(true, $this->contextProvider->getContextLegacy()),
            'shopName' => $this->contextProvider->getShopName(),
            'isMobile' => $this->contextProvider->isMobile(),
        );
    }
}
