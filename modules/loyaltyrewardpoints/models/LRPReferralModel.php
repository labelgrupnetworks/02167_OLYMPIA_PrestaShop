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

class LRPReferralModel extends ObjectModel
{
    /** @var integer Unique ID */
    public $id_lrp_referral;

    /** @var integer Customer ID */
    public $id_customer;

    /** @var string friend email */
    public $friend_email;

    /** @var string referrer email */
    public $referer_email;

    /** @var integer Status */
    public $status;

    /** @var float points */
    public $points;

    /** @var integer referral ID */
    public $rid;

    /** @var string referral code */
    public $referral_code;


    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'lrp_referral',
        'primary' => 'id_lrp_referral',
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT),
            'friend_email' => array('type' => self::TYPE_STRING),
            'referer_email' => array('type' => self::TYPE_STRING),
            'status' => array('type' => self::TYPE_INT),
            'points' => array('type' => self::TYPE_FLOAT),
            'rid' => array('type' => self::TYPE_INT),
            'referral_code' => array('type' => self::TYPE_STRING)
        )
    );
}
