<?php
/**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * @author    PresTeamShop SAS (Registered Trademark) <info@presteamshop.com>
 * @copyright 2011-2022 PresTeamShop SAS, All rights reserved.
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * @category  PrestaShop
 * @category  Module
 */

class PaymentClass extends ObjectModel
{
    public $id;
    public $id_module;
    public $name;
    public $title;
    public $description;
    public $name_image;
    public $force_display;
    public $test_mode;
    public $test_ip;

    public static $definition = array(
        'table'          => 'opc_payment',
        'primary'        => 'id_payment',
        'multilang'      => true,
        'multilang_shop' => true,
        'fields'         => array(
            'id_module' => array('type' => self::TYPE_INT),
            'name'      => array('type' => self::TYPE_STRING),
            'name_image' => array('type' => self::TYPE_STRING),
            'force_display' => array('type' => self::TYPE_BOOL),
            'test_mode' => array('type' => self::TYPE_BOOL),
            'test_ip' => array('type' => self::TYPE_STRING),
            /* Lang fields */
            'title'       => array('type' => self::TYPE_STRING, 'lang' => true),
            'description' => array('type' => self::TYPE_STRING, 'lang' => true)
        )
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        //create multishop assoc
        Shop::addTableAssociation(self::$definition['table'], array('type' => 'shop'));
        parent::__construct($id, $id_lang, $id_shop);
    }

    public static function getPaymentByName($name)
    {
        $query = new DbQuery();
        $query->select(self::$definition['primary']);
        $query->from(self::$definition['table']);
        $query->where('name = \''.pSQL($name).'\'');

        $id_payment = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

        if (!empty($id_payment)) {
            return $id_payment;
        }

        return null;
    }

    public static function getIdPaymentBy($field, $value)
    {
        $query = new DbQuery();
        $query->select(bqSQL(self::$definition['primary']));
        $query->from(bqSQL(self::$definition['table']));
        $query->where(pSQL($field).' = "'.pSQL($value).'"');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }
}
