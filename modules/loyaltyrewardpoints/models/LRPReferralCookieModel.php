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

class LRPReferralCookieModel extends ObjectModel
{
    /** @var integer Unique ID */
    public $id_lrp_referral_cookie;

    /** @var integer Cart ID */
    public $id_cart;

    /** @var integer Customer ID */
    public $id_customer;

    /** @var integer Referring Customer IDs */
    public $id_referrer;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'lrp_referral_cookie',
        'primary' => 'id_lrp_referral_cookie',
        'fields' => array(
            'id_cart' => array('type' => self::TYPE_INT),
            'id_customer' => array('type' => self::TYPE_INT),
            'id_referrer' => array('type' => self::TYPE_INT)
        )
    );

    /**
     * @param $id_customer
     */
    public static function deleteByCustomer($id_customer)
    {
        DB::getInstance()->delete(self::$definition['table'], 'id_customer='.(int)$id_customer);
    }
}
