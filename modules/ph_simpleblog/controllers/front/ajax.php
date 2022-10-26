<?php

/**
 * Blog for PrestaShop module by PrestaHome Team.
 *
 * @author    PrestaHome Team <support@prestahome.com>
 * @copyright Copyright (c) 2011-2021 PrestaHome Team - www.PrestaHome.com
 * @license   You only can use module, nothing more!
 */

require_once _PS_MODULE_DIR_ . 'ph_simpleblog/ph_simpleblog.php';

class Ph_SimpleBlogAjaxModuleFrontController extends ModuleFrontController
{
    public $product;

    public function init()
    {
        parent::init();
    }

    public function postProcess()
    {
        if (
            Module::isEnabled('ph_simpleblog')
            && (Tools::getValue('action') == 'addRating' || Tools::getValue('action') == 'removeRating')
            && Tools::getValue('secure_key') == $this->module->secure_key
        ) {
            parent::postProcess();
        } else {
            die('Access denied');
        }
    }

    public function displayAjaxAddRating()
    {
        $id_simpleblog_post = Tools::getValue('id_simpleblog_post');
        $reply = SimpleBlogPost::changeRating('up', (int) $id_simpleblog_post);
        $message = $reply[0]['likes'];

        $this->ajaxDie(
            json_encode(
                [
                    'hasError' => false,
                    'status' => 'success',
                    'message' => $message,
                ]
            )
        );
    }

    public function displayAjaxRemoveRating()
    {
        $id_simpleblog_post = Tools::getValue('id_simpleblog_post');
        $reply = SimpleBlogPost::changeRating('down', (int) $id_simpleblog_post);
        $message = $reply[0]['likes'];

        $this->ajaxDie(
            json_encode(
                [
                    'hasError' => false,
                    'status' => 'success',
                    'message' => $message,
                ]
            )
        );
    }
}
