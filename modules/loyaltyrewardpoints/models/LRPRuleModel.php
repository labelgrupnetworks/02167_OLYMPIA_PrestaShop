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

class LRPRuleModel extends ObjectModel
{
    /** @var integer Rule ID */
    public $id_lrp_rule;

    /** @var integer Shop ID */
    public $id_shop;

    /** @var boolean Enabled */
    public $enabled;

    /** @var string name */
    public $name;

    /** @var string operator */
    public $operator;

    /** @var integer Points */
    public $points;

    /** @var string Product ID List */
    public $id_products;

    /** @var string Category ID List */
    public $id_categories;

    /** @var datetime Start Date */
    public $date_start;

    /** @var datetime End Date */
    public $date_end;

    /** @var datetime Date Added */
    public $date_add;


    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'lrp_rule',
        'primary' => 'id_lrp_rule',
        'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT),
            'enabled' => array('type' => self::TYPE_INT),
            'name' => array('type' => self::TYPE_STRING),
            'operator' => array('type' => self::TYPE_STRING),
            'points' => array('type' => self::TYPE_INT),
            'id_products' => array('type' => self::TYPE_STRING),
            'id_categories' => array('type' => self::TYPE_STRING),
            'date_start' => array('type' => self::TYPE_DATE),
            'date_end' => array('type' => self::TYPE_DATE),
            'date_add' => array('type' => self::TYPE_DATE)
        )
    );

    public function getAll()
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from(self::$definition['table']);
        $sql->orderBy('id_lrp_rule');
        $result = Db::getInstance()->executeS($sql);

        if (!empty($result)) {
            return $this->hydrateCollection('LRPRuleModel', $result);
        } else {
            return array();
        }
    }
}
