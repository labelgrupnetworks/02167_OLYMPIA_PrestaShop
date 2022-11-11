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
class KbBookingRoomType extends ObjectModel
{
    public $id_room_type;
    public $room_name;
    public $max_allowed_child;
    public $max_allowed_adult;
    public $room_category;
    public $active;
    public $date_add;
    public $date_upd;
    
    const TABLE_NAME = 'kb_booking_room_type';
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => 'id_room_type',
        'multilang' => true,
        'multishop' => true,
        'multilang_shop' => true,
        'fields' => array(
            'max_allowed_child' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'max_allowed_adult' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'room_category' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHTML'
            ),
            'active' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'room_name' => array(
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
    
    public static function getAvailableRoomType()
    {
        return Db::getInstance()->executeS(
            'SELECT l.room_name, c.* FROM '
            . _DB_PREFIX_ . self::TABLE_NAME . ' c INNER JOIN ' ._DB_PREFIX_. self::TABLE_NAME . '_lang l on '
            . '(l.id_room_type=c.id_room_type AND l.id_lang=' . (int) Context::getContext()->language->id
            . ' AND l.id_shop=' . (int) Context::getContext()->shop->id . ') INNER JOIN ' ._DB_PREFIX_. self::TABLE_NAME . '_shop s on '
            . '(s.id_room_type=c.id_room_type and s.id_shop=' . (int) Context::getContext()->shop->id . ')'
        );
    }
    
    public static function getAvailableRoomTypeByID($id_room_type)
    {
        return Db::getInstance()->getRow(
            'SELECT l.room_name, c.* FROM '
            . _DB_PREFIX_ . self::TABLE_NAME . ' c INNER JOIN ' ._DB_PREFIX_. self::TABLE_NAME . '_lang l on '
            . '(l.id_room_type=c.id_room_type AND l.id_lang=' . (int) Context::getContext()->language->id
            . ' AND l.id_shop=' . (int) Context::getContext()->shop->id . ') INNER JOIN ' ._DB_PREFIX_. self::TABLE_NAME . '_shop s on '
            . '(s.id_room_type=c.id_room_type and s.id_shop=' . (int) Context::getContext()->shop->id . ') WHERE c.id_room_type='.(int)$id_room_type
        );
    }
}
