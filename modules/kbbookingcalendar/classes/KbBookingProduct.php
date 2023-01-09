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
class KbBookingProduct extends ObjectModel
{
    public $id_booking_product;
    public $id_product;
    public $product_type;
//    public $stock_status;
    public $service_type;
    public $period_type;
    public $quantity;
    public $price;
//    public $start_date;
    public $date_details;
    public $weekday_price_details;
    public $min_hours;
    public $max_hours;
    public $min_days;
    public $max_days;
    public $star_rating;
    public $enable_product_map;
    public $address;
    public $longitude;
    public $latitude;
    public $disable_days;
    public $active;
    // Anulación de desarrollo de configuración de precio por día de la semana
    // public $is_weekday_price_active;
    public $date_add;
    public $date_upd;
    
    const TABLE_NAME = 'kb_booking_product';
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => 'id_booking_product',
//        'multilang' => true,
        'multishop' => true,
//        'multilang_shop' => true,
        'fields' => array(
            'id_product' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'quantity' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'product_type' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHTML'
            ),
//            'stock_status' => array(
//                'type' => self::TYPE_HTML,
//                'validate' => 'isCleanHTML'
//            ),
            'service_type' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHTML'
            ),
            'period_type' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHTML'
            ),
            'price' => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat'
            ),
            'date_details' => array(
                'type' =>self::TYPE_HTML,
                'validate' => 'isCleanHTML'
            ),
            'weekday_price_details' => array(
                'type' =>self::TYPE_HTML,
                'validate' => 'isCleanHTML'
            ),
//            'end_date' => array(
//                'type' => self::TYPE_DATE,
//                'validate' => 'isDate'
//            ),
            'min_hours' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'max_hours' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'min_days' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'max_days' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'star_rating' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'enable_product_map' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'address' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHTML'
            ),
            'longitude' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHTML'
            ),
            'latitude' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHTML'
            ),
            'disable_days' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHTML'
            ),
            'active' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            // Anulación de desarrollo de configuración de precio por día de la semana
            /*'is_weekday_price_active' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),*/
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
    
    public static function getProductDetailsByID($id_product)
    {
        if (empty($id_product) || $id_product == null) {
            return;
        }
        $id_shop = Context::getContext()->shop->id;
        return Db::getInstance()->getRow('SELECT a.* FROM '._DB_PREFIX_.self::TABLE_NAME.' as a INNER JOIN '._DB_PREFIX_.'product p on (a.id_product=p.id_product) INNER JOIN '._DB_PREFIX_.'product_shop ps on (a.id_product=ps.id_product AND ps.id_shop='.(int)$id_shop.') WHERE a.id_product='.(int)$id_product);
    }
    
    public static function getProductsByType($type)
    {
        if ($type == null || empty($type)) {
            return;
        }
        $id_shop = Context::getContext()->shop->id;
        return Db::getInstance()->getRow('SELECT a.* FROM '._DB_PREFIX_.self::TABLE_NAME.' as a INNER JOIN '._DB_PREFIX_.'product p on (a.id_product=p.id_product) INNER JOIN '._DB_PREFIX_.'product_shop ps on (a.id_product=ps.id_product AND ps.id_shop='.(int)$id_shop.') WHERE a.active=1 AND a.product_type="'.pSQL($type).'"');
    }
    
    public static function getHotelProductRoomsByID($id_booking_product, $id_booking_room = null)
    {
        if ($id_booking_product == null || empty($id_booking_product)) {
            return;
        }
        $str = '';
        if (!empty($id_booking_room)) {
            $str .= ' AND b.id_booking_room_facilities_map='.(int)$id_booking_room;
        }
        
        return Db::getInstance()->executeS(
            'SELECT b.* FROM '._DB_PREFIX_.self::TABLE_NAME.' as a INNER JOIN '._DB_PREFIX_.'kb_booking_product_room_facilities_mapping b '
            . 'on (a.id_booking_product=b.id_booking_product) WHERE b.active=1 AND a.id_booking_product='.(int)$id_booking_product.$str
        );
    }
    
    public static function getHotelProductRoomsByIDANDCategory($id_booking_product, $id_room_category = null)
    {
        if ($id_booking_product == null || empty($id_booking_product)) {
            return;
        }
        $str = '';
        if (!empty($id_room_category)) {
            $str .= ' AND b.id_room_category='.(int)$id_room_category;
        }
        
        return Db::getInstance()->executeS(
            'SELECT b.* FROM '._DB_PREFIX_.self::TABLE_NAME.' as a INNER JOIN '._DB_PREFIX_.'kb_booking_product_room_facilities_mapping b '
            . 'on (a.id_booking_product=b.id_booking_product) WHERE b.active=1 AND a.id_booking_product='.(int)$id_booking_product.$str
        );
    }
    
    /**
    * Get all available products
    *
    * @param int $id_lang Language id
    * @param int $start Start number
    * @param int $limit Number of products to return
    * @param string $order_by Field for ordering
    * @param string $order_way Way for ordering (ASC or DESC)
    * @return array Products details
    */
    public static function getAllProducts(
        $id_lang,
        $start,
        $limit,
        $order_by,
        $order_way,
        $id_category = false,
        $only_active = false,
        Context $context = null
    ) {
        if (!$context) {
            $context = Context::getContext();
        }

        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }

        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'p';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        } elseif ($order_by == 'position') {
            $order_by_prefix = 'c';
        }

        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }
        $sql = 'SELECT p.*, product_shop.*, pl.* , m.`name` AS manufacturer_name, s.`name` AS supplier_name
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
                                INNER JOIN '._DB_PREFIX_.self::TABLE_NAME.' as b on (b.id_product=p.id_product)
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)'.
                ($id_category ? 'LEFT JOIN `'._DB_PREFIX_.'category_product` c ON (c.`id_product` = p.`id_product`)' : '').'
				WHERE pl.`id_lang` = '.(int)$id_lang.
                    ($id_category ? ' AND c.`id_category` = '.(int)$id_category : '').
                    ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
                    ($only_active ? ' AND product_shop.`active` = 1' : '').'
				ORDER BY '.(isset($order_by_prefix) ? pSQL($order_by_prefix).'.' : '').'`'.pSQL($order_by).'` '.pSQL($order_way).
                ($limit > 0 ? ' LIMIT '.(int)$start.','.(int)$limit : '');
        $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if ($order_by == 'price') {
            Tools::orderbyPrice($rq, $order_way);
        }

        foreach ($rq as &$row) {
            $row = Product::getTaxesInformations($row);
        }

        return ($rq);
    }
}
