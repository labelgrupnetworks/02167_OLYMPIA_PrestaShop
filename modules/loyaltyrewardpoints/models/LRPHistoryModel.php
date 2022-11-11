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

class LRPHistoryModel extends ObjectModel
{
    const TYPE_REWARDED = 1;
    const TYPE_REDEEMED = 2;

    /** @var integer Unique ID */
    public $id_lrp_history;

    /** @var integer Order ID */
    public $id_order;

    /** @var integer Customer ID */
    public $id_customer;

    /** @var integer Currency ID */
    public $id_currency;

    /** @var float points */
    public $points;

    /** @var float Point Monetary Value at time transaction was added to history */
    public $point_value;

    /** @var float Point Monetary Value at time transaction was added to history in store default currency */
    public $point_value_base_currency;

    /** @var integer enumerated type */
    public $type;

    /** @var string source */
    public $source;

    /** @var datetime date_add */
    public $date_add;

    /** @var datetime date_upd */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'lrp_history',
        'primary' => 'id_lrp_history',
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT),
            'id_customer' => array('type' => self::TYPE_INT),
            'id_currency' => array('type' => self::TYPE_INT),
            'points' => array('type' => self::TYPE_FLOAT),
            'point_value' => array('type' => self::TYPE_FLOAT),
            'point_value_base_currency' => array('type' => self::TYPE_FLOAT),
            'type' => array('type' => self::TYPE_INT),
            'source' => array('type' => self::TYPE_STRING),
            'date_add' => array('type' => self::TYPE_DATE)
        )
    );

    /**
     * @param $id_order
     * @param int $type
     */
    public function loadByOrder($id_order, $type = self::TYPE_REWARDED)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from(self::$definition['table']);
        $sql->where('id_order = ' . (int)$id_order);
        $sql->where('type = ' . (int)$type);
        $sql->orderBy('date_add DESC');
        $row = Db::getInstance()->getRow($sql);

        if (!empty($row)) {
            $this->hydrate($row);
        } else {
            return false;
        }
    }

    /**
     * Get LRP history for a customer
     * @param $id_customer
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getByCustomerID($id_customer, $page = 0, $page_size = 0)
    {
        $sql = new DbQuery();
        $sql->select('SQL_CALC_FOUND_ROWS *');
        $sql->from(self::$definition['table']);
        $sql->where('id_customer = ' . (int)$id_customer);
        $sql->orderBy('date_add DESC');

        if ($page > 0) {
            $sql->limit($page_size, $page - 1);
        }
        $result = Db::getInstance()->executeS($sql);
        $total = Db::getInstance()->getValue('SELECT FOUND_ROWS()', false);

        if (!empty($result)) {
            $result = array(
                'result' => $this->hydrateCollection('LRPHistoryModel', $result),
                'total' => $total
            );
        } else {
            $result = array(
                'result' => array(),
                'total' => 0
            );
        }
        return $result;
    }
}
