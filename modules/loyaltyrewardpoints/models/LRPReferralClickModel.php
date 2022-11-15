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

class LRPReferralClickModel extends ObjectModel
{
    public $id_lrp_referral_click = 0;
    public $id_referrer = 0;
    public $ip_address = '';
    public $date_add = '';
    public $date_upd = '';

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'lrp_referral_click',
        'primary' => 'id_lrp_referral_click',
        'fields' => array(
            'id_referrer' => array('type' => self::TYPE_INT),
            'ip_address' => array('type' => self::TYPE_STRING),
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        )
    );
}
