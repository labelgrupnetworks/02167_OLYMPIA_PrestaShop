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
class KbBookingDiscountRules extends ObjectModel
{
    public $id_rules;
    public $rule_type;
    public $discount_type;
    public $active;
    public $value;
    public $shop_id;
    public $position;
    public $date_add;
    public $date_upd;
    
    const TABLE_NAME = 'kb_booking_discount_rules';
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => 'id_rules',
        'fields' => array(
            'active' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'rule_type' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
            ),
            'discount_type' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
            ),
            'shop_id' => array(
                'type' => self::TYPE_STRING,
            ),
            'value' => array(
                'type' => self::TYPE_STRING
            ),
            'position' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
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

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
    }

    // Function to update Rule position
    public function updateRulePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS('SELECT `id_rules`, `position` FROM `'._DB_PREFIX_.'kb_booking_discount_rules` ORDER BY `position` ASC')) {
            return false;
        }
        foreach ($res as $plans) {
            if ((int)$plans['id_rules'] == (int)$this->id) {
                $moved_plans = $plans;
            }
        }

        if (!isset($moved_plans) || !isset($position)) {
            return false;
        }
        return (Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'kb_booking_discount_rules` SET `position` = `position` '.($way ? '- 1' : '+ 1').' WHERE `position` '.($way ? '> '.(int)$moved_plans['position'].' AND `position` <= '.(int)$position : '< '.(int)$moved_plans['position'].' AND `position` >= '.(int)$position)) && Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'kb_booking_discount_rules` SET `position` = '.(int)$position.' WHERE `id_rules` = '.(int)$moved_plans['id_rules']));
    }
}
