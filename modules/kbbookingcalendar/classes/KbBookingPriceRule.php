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
class KbBookingPriceRule extends ObjectModel
{
    public $id_booking_price_rule;
    public $id_product;
    public $date_selection;
    public $start_date;
    public $end_date;
    public $particular_date;
    public $reduction_type;
//    public $reduction_tax;
    public $reduction;
    public $name;
    public $active;
    public $date_add;
    public $date_upd;
    
    const TABLE_NAME = 'kb_booking_price_rule';
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => 'id_booking_price_rule',
        'multilang' => true,
        'multishop' => true,
        'multilang_shop' => true,
        'fields' => array(
            'id_product' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'date_selection' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHTML'
            ),
            'start_date' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate'
            ),
            'end_date' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate'
            ),
            'particular_date' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate'
            ),
            'reduction_type' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHTML'
            ),
            'reduction' => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat'
            ),
//            'reduction_tax' => array(
//                'type' => self::TYPE_HTML,
//                'validate' => 'isCleanHTML'
//            ),
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
    
    public static function getRulebyProductID($id_product)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . self::TABLE_NAME .' WHERE `id_product` = '
            . (int) $id_product . ' AND active=1'
        );
    }
    
    public static function isPriceRuleApplicable($id_product)
    {
        $price_rule = self::getRulebyProductID($id_product);
//        Tools::dieObject($price_rule);
        $applicable_rule = array();
        if (!empty($price_rule)) {
            foreach ($price_rule as $key => $rule) {
                $booking_rule = new self($rule['id_booking_price_rule'], Context::getContext()->language->id);
                $current_time = time();
                $x = date('j', $current_time);
                $rule['name'] = $booking_rule->name;
                if ($rule['date_selection'] == 'date_range') {
                    $start_date = strtotime($rule['start_date']);
                    $end_date = strtotime($rule['end_date']);
                    $y = date('j', $start_date);
                    $z = date('j', $end_date);
                    if ($current_time >= $start_date && $current_time <= $end_date) {
                        $applicable_rule[] = $rule;
                    }
                } else {
                    $specific_date = strtotime($rule['particular_date']);
                    $z = date('j', $specific_date);
                    if ($x <= $z) {
                        $applicable_rule[] = $rule;
                    }
                }
            }
        }
        return $applicable_rule;
    }
}
