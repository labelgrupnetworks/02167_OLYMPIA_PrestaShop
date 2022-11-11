<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innovadeluxe SL
 * @copyright 2018 Innovadeluxe SL

 * @license   INNOVADELUXE
 */

class AdminIdxrcookiesController extends ModuleAdminController
{
    public function ajaxProcessUpdateCookieModule()
    {
        $cookie = new IdxrcookiesCookie((int)Tools::getValue('id_cookie'));
        if (Validate::isLoadedObject($cookie)) {
            try {
                $cookie->date_upd = date('Y-m-d H:i:s');
                $cookie->module = Tools::getValue('module');
                $cookie->id_template = 0;
                $cookie->save();
                echo 'ok';
            } catch (Exception $e) {
                $this->module->logError($e->getMessage());
                echo 'ko';
            }
        } else {
            echo 'ko';
        }
        die();
    }

    public function ajaxProcessUpdateCookieTemplate()
    {
        $cookie = new IdxrcookiesCookie((int)Tools::getValue('id_cookie'));
        if (Validate::isLoadedObject($cookie)) {
            try {
                $cookie->date_upd = date('Y-m-d H:i:s');
                $cookie->module = null;
                $cookie->id_template = (int)Tools::getValue('id_template');
                $cookie->save();
                echo 'ok';
            } catch (Exception $e) {
                $this->module->logError($e->getMessage());
                echo 'ko';
            }
        } else {
            echo 'ko';
        }
        die();
    }
    public function ajaxProcessUpdateCookieType()
    {
        $cookie = new IdxrcookiesCookie((int)Tools::getValue('id_cookie'));
        if (Validate::isLoadedObject($cookie)) {
            try {
                $cookie->date_upd = date('Y-m-d H:i:s');
                $cookie->id_cookie_type = (int)Tools::getValue('id_cookie_type');
                $cookie->save();
                echo 'ok';
            } catch (Exception $e) {
                $this->module->logError($e->getMessage());
                echo 'ko';
            }
        } else {
            echo 'ko';
        }
        die();
    }

    public function sendJson($datos)
    {
        header("Content-type: application/json; charset=utf-8");
        die(Tools::jsonEncode($datos));
    }
}
