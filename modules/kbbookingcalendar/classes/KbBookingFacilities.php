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
class KbBookingFacilities extends ObjectModel
{
    public $id_facilities;
    public $type;
    public $image_type;
    public $upload_image_path;
    public $upload_image;
    public $font_awesome_icon;
    public $name;
    public $active;
    public $date_add;
    public $date_upd;
    
    const TABLE_NAME = 'kb_booking_facilities';
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => 'id_facilities',
        'multilang' => true,
        'multishop' => true,
        'multilang_shop' => true,
        'fields' => array(
            'type' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHTML'
            ),
            'image_type' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHTML'
            ),
            'upload_image_path' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHTML'
            ),
            'upload_image' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHTML'
            ),
            'font_awesome_icon' => array(
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
    
    public static function getAvailableFacilitiesByType($type)
    {
        if ($type == null || $type == '') {
            return;
        }
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
        
        return Db::getInstance()->executeS(
            'SELECT a.*,l.name FROM ' . _DB_PREFIX_ . self::TABLE_NAME
            . ' a INNER JOIN ' . _DB_PREFIX_ . self::TABLE_NAME . '_lang l'
            . ' on (a.id_facilities=l.id_facilities AND l.id_lang=' . (int) $id_lang
            . ' AND l.id_shop=' . (int) $id_shop . ') INNER JOIN ' . _DB_PREFIX_ . self::TABLE_NAME . '_shop s'
            . ' on (a.id_facilities=s.id_facilities AND s.id_shop=' . (int) $id_shop
            . ') WHERE a.active=1 AND type="' . pSQL($type) . '"'
        );
    }
    
    public static function getFacilitiesMappedwithProduct($id_booking_product)
    {
        if ($id_booking_product == '' || $id_booking_product == null) {
            return;
        }
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
        return Db::getInstance()->executeS(
            'SELECT a.*,l.name FROM ' . _DB_PREFIX_ . self::TABLE_NAME
            . ' a INNER JOIN ' . _DB_PREFIX_ . self::TABLE_NAME . '_lang l'
            . ' on (a.id_facilities=l.id_facilities AND l.id_lang=' . (int) $id_lang
            . ' AND l.id_shop=' . (int) $id_shop . ') INNER JOIN ' . _DB_PREFIX_ . self::TABLE_NAME . '_shop s'
            . ' on (a.id_facilities=s.id_facilities AND s.id_shop=' . (int) $id_shop
            . ') INNER JOIN '._DB_PREFIX_.'kb_booking_product_facilities_mapping fm on (fm.id_facilities=a.id_facilities AND fm.id_booking_product='.(int)$id_booking_product.')'
        );
    }
    
    public static function getFacilitiesMappedwithHotelRooms($id_booking_room, $id_facilities)
    {
        if ($id_booking_room == '' || $id_booking_room == null) {
            return;
        }
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
       
        return Db::getInstance()->executeS(
            'SELECT a.*,l.name FROM ' . _DB_PREFIX_ . self::TABLE_NAME
            . ' a INNER JOIN ' . _DB_PREFIX_ . self::TABLE_NAME . '_lang l'
            . ' on (a.id_facilities=l.id_facilities AND l.id_lang=' . (int) $id_lang
            . ' AND l.id_shop=' . (int) $id_shop . ') INNER JOIN ' . _DB_PREFIX_ . self::TABLE_NAME . '_shop s'
            . ' on (a.id_facilities=s.id_facilities AND s.id_shop=' . (int) $id_shop
            . ') WHERE  a.id_facilities IN ('.pSQL($id_facilities).')'
        );
    }
}
