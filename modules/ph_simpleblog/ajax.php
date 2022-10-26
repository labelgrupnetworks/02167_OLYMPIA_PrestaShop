<?php
/**
 * Blog for PrestaShop module by PrestaHome Team.
 *
 * @author    PrestaHome Team <support@prestahome.com>
 * @copyright Copyright (c) 2011-2021 PrestaHome Team - www.PrestaHome.com
 * @license   You only can use module, nothing more!
 */
include dirname(__FILE__).'/../../config/config.inc.php';
include dirname(__FILE__).'/../../init.php';

$status = 'success';
$message = '';

include_once dirname(__FILE__).'/ph_simpleblog.php';
include_once dirname(__FILE__).'/models/SimpleBlogPost.php';

$action = Tools::getValue('action');

switch ($action) {
    case 'addRating':
        $id_simpleblog_post = Tools::getValue('id_simpleblog_post');
        $reply = SimpleBlogPost::changeRating('up', (int) $id_simpleblog_post);
        $message = $reply[0]['likes'];
        break;

    case 'removeRating':
        $id_simpleblog_post = Tools::getValue('id_simpleblog_post');
        $reply = SimpleBlogPost::changeRating('down', (int) $id_simpleblog_post);
        $message = $reply[0]['likes'];
        break;

    default:
        $status = 'error';
        $message = 'Unknown parameters!';
        break;
}
$response = new stdClass();
$response->status = $status;
$response->message = $message;
$response->action = $action;
echo json_encode($response);
