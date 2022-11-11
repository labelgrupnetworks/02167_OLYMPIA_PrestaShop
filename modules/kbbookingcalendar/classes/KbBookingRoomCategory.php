<?php
/**
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future.If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @author    knowband.com <support@knowband.com>
* @copyright 2017 Knowband
* @license   see file: LICENSE.txt
* @category  PrestaShop Module
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

//Class and its methods to handle
class KbBookingRoomCategory extends ObjectModel
{
    public $id_booking_category;
    public $name;
    public $description;
    public $active;
    public $date_add;
    public $date_upd;
    
    const TABLE_NAME = 'kb_booking_category';
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => 'id_booking_category',
        'multilang' => true,
        'multishop' => true,
        'multilang_shop' => true,
        'fields' => array(
            'description' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHTML'
            ),
            'active' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'name' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'lang' => true,
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'copy_post' => false
            ),
            'date_upd' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'copy_post' => false
            ),
        ),
    );
    
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        Shop::addTableAssociation(self::TABLE_NAME, array('type' => 'shop'));
        parent::__construct($id, $id_lang, $id_shop);
    }
    
    public static function getAvailableRoomCategory()
    {
        return Db::getInstance()->executeS(
            'SELECT l.name, c.id_booking_category FROM '
            . _DB_PREFIX_ . self::TABLE_NAME . ' c INNER JOIN ' ._DB_PREFIX_. self::TABLE_NAME . '_lang l on '
            . '(l.id_booking_category=c.id_booking_category AND l.id_lang=' . (int) Context::getContext()->language->id
            . ' AND l.id_shop=' . (int) Context::getContext()->shop->id . ') INNER JOIN ' ._DB_PREFIX_. self::TABLE_NAME . '_shop s on '
            . '(s.id_booking_category=c.id_booking_category and s.id_shop=' . (int) Context::getContext()->shop->id . ') WHERE c.active=1'
        );
    }
    
    public static function getRoomCategoryNameByID($id)
    {
        return Db::getInstance()->getValue('SELECT l.name FROM '. _DB_PREFIX_ . self::TABLE_NAME . ' c INNER JOIN ' ._DB_PREFIX_. self::TABLE_NAME . '_lang l on (l.id_booking_category=c.id_booking_category AND l.id_lang=' . (int) Context::getContext()->language->id. ' AND l.id_shop=' . (int) Context::getContext()->shop->id . ') INNER JOIN ' ._DB_PREFIX_. self::TABLE_NAME . '_shop s on '
            . '(s.id_booking_category=c.id_booking_category and s.id_shop=' . (int) Context::getContext()->shop->id . ') WHERE c.id_booking_category='.(int)$id);
    }
}
