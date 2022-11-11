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

class LRPCartModel extends ObjectModel
{
    const TYPE_PENDING = 1;
    const TYPE_REDEEMED = 2;

    /** @var integer Unique ID */
    public $id_lrp_cart;

    /** @var integer Order ID */
    public $id_cart;

    /** @var integer Customer ID */
    public $id_customer;

    /** @var float points */
    public $points_redeemed;

    /** @var integer enumerated type */
    public $type;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'lrp_cart',
        'primary' => 'id_lrp_cart',
        'fields' => array(
            'id_cart' => array('type' => self::TYPE_INT),
            'id_customer' => array('type' => self::TYPE_INT),
            'points_redeemed' => array('type' => self::TYPE_FLOAT),
            'type' => array('type' => self::TYPE_INT)
        )
    );

    /**
     * @param $id_customer
     * @param $id_cart
     * @return bool
     */
    public function load($id_cart, $id_customer)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from(self::$definition['table']);
        $sql->where('id_cart = ' . (int)$id_cart);
        $sql->where('id_customer = ' . (int)$id_customer);
        $row = Db::getInstance()->getRow($sql);

        if (!empty($row)) {
            $this->hydrate($row);
        } else {
            return false;
        }
    }
}
