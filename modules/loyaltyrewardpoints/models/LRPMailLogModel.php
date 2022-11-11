<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Musaffar Patel
 * @copyright 2016-2021 Musaffar Patel
 * @license   LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class LRPMailLogModel extends ObjectModel
{
    /** @var integer Unique ID */
    public $id_lrp_mail_log;

    /** @var integer Customer ID */
    public $id_customer;

    /** @var string customer email */
    public $email;

    /** @var string Mail ID */
    public $id_mail;

    /** @var datetime date_upd */
    public $date_sent;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'lrp_mail_log',
        'primary' => 'id_lrp_mail_log',
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT),
            'email' => array('type' => self::TYPE_STRING),
            'id_mail' => array('type' => self::TYPE_STRING),
            'date_sent' => array('type' => self::TYPE_DATE)
        )
    );
}
