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

class LRPCustomerModel extends ObjectModel
{
    /** @var integer Unique ID */
    public $id_lrp_customer;

    /** @var integer Customer ID */
    public $id_customer;

    /** @var float Points */
    public $points;

    /** @var string referral code */
    public $referral_code;

    /** @var datetime Date Added */
    public $date_add;

    /** @var datetime Date Updated */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'lrp_customer',
        'primary' => 'id_lrp_customer',
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT),
            'points' => array('type' => self::TYPE_FLOAT),
            'referral_code' => array('type' => self::TYPE_STRING),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_upd' => array('type' => self::TYPE_DATE)
        )
    );

    /**
     * @param $id_customer
     */
    public function loadByCustomerID($id_customer)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from(self::$definition['table']);
        $sql->where('id_customer=' . (int)$id_customer);
        $row = Db::getInstance()->getRow($sql);

        if (!empty($row)) {
            $this->hydrate($row);
        } else {
            return false;
        }
    }
}
