<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2018 Innovadeluxe SL

 * @license   INNOVADELUXE
 */

class IdxcookiesAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $token = $this->context->cookie->__get($this->module->name.'ajaxToken');
        if (!$token || $token !== Tools::getValue('token')) {
            die();
        }
        if (Tools::isSubmit('action')) {
            $method = 'ajaxProcess'.Tools::ucfirst(Tools::getValue('action'));
            if (is_callable(array($this, $method))) {
                call_user_func(array($this, $method));
            }
        }
    }

    public function ajaxProcessAudit()
    {
        $cookies_php = $_COOKIE;
        $cookies_js = Tools::getValue('cookies');
        if (is_array($cookies_js)) {
            $cookies = array_merge($cookies_php, $cookies_js);
        } else {
            $cookies = $cookies_php;
        }
        $this->module->saveCookies($cookies);
        die(Tools::jsonEncode($cookies));
    }

    public function ajaxProcessGetAjaxTemplates()
    {
        die(Tools::jsonEncode($this->module->ajaxProcessGetAjaxTemplates()));
    }
}
