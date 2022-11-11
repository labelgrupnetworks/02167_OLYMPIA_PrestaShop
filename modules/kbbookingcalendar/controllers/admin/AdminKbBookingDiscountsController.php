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

require_once dirname(__FILE__).'/AdminKbBookingCoreController.php';
require_once _PS_MODULE_DIR_.'kbbookingcalendar/classes/KbBookingDiscountRules.php';

class AdminKbBookingDiscountsController extends AdminKbBookingCoreController
{
    protected $ps_shop = array();
    protected $rule_type_arr = array();
    protected $discount_type_arr = array();
    protected $position_identifier = 'id_rules';
    public function __construct()
    {
        $this->table = 'kb_booking_discount_rules';
        $this->className = 'KbBookingDiscountRules';
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->identifier = 'id_rules';
        $this->lang = false;
        $this->display = 'list';
        $this->all_languages = Language::getLanguages(false);
        parent::__construct();
            $this->toolbar_title = $this->module->l('Discount Rules', 'AdminKbBookingDiscountsController');
        foreach (Shop::getShops(false) as $shop) {
            $this->ps_shop[$shop['id_shop']] = $shop['name'];
        }
        $this->rule_type_arr['booking'] = $this->module->l('Booking Products', 'AdminKbBookingDiscountsController');
        $this->rule_type_arr['cart'] = $this->module->l('Cart', 'AdminKbBookingDiscountsController');
        $this->discount_type_arr['fixed'] = $this->module->l('Fixed', 'AdminKbBookingDiscountsController');
        $this->discount_type_arr['percentage'] = $this->module->l('Percentage', 'AdminKbBookingDiscountsController');
        $this->fields_list = array(
            'id_rules' => array(
                'title' => $this->module->l('ID', 'AdminKbBookingDiscountsController'),
                'align' => 'center',
//                'filter_key' => 'ps.id_product',
                'class' => 'fixed-width-xs'
            ),
            'value' => array(
                'title' => $this->module->l('Rule Name', 'AdminKbBookingDiscountsController'),
//                'search' => false,
//                 'type' => 'select',
                'align' => 'left',
                'callback' => 'displayRuleNameArr',
//                 'filter_key' => 'a!value',
            ),
            'rule_type' => array(
                'title' => $this->module->l('Rule Type', 'AdminKbBookingDiscountsController'),
//                'search' => false,
                 'type' => 'select',
                  'list' => $this->rule_type_arr,
                'align' => 'left',
                'callback' => 'displayRuleTypeArr',
                 'filter_key' => 'a!rule_type',
            ),
            'discount_type' => array(
                'title' => $this->module->l('Discount Type', 'AdminKbBookingDiscountsController'),
//                'search' => false,
                 'type' => 'select',
                  'list' => $this->discount_type_arr,
                'align' => 'left',
                'callback' => 'displayDiscountTypeArr',
                 'filter_key' => 'a!discount_type',
            ),
            'active' => array(
                'title' => $this->module->l('Status', 'AdminKbBookingDiscountsController'),
                'align' => 'text-center',
                'active' => 'active',
                'type' => 'bool',
                'order_key' => 'active',
            ),
            'shop_id' => array(
                'title' => $this->module->l('Shop', 'AdminKbBookingDiscountsController'),
                'align' => 'center',
                'type' => 'select',
                'list' => $this->ps_shop,
                'filter_key' => 'a!shop_id',
                'callback' => 'psShopList',
//                'search' => false
            ),
            'position' => array(
                'title' => $this->module->l('Priority', 'AdminKbBookingDiscountsController'),
                'filter_key' => 'position',
                'search' => false,
                'align' => 'text-center',
//                'class' => 'fixed-width-sm',
                'position' => 'position',
            ),
            'date_upd' => array(
                'title' => $this->module->l('Updated On', 'AdminKbBookingDiscountsController'),
                'type' => 'datetime'
            )
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->module->l('Delete selected', 'AdminKbBookingDiscountsController'),
                'confirm' => $this->module->l('Delete selected Rules?', 'AdminKbBookingDiscountsController'),
                'icon' => 'icon-trash'
            )
        );
        $this->_select = 'a.*';
        $this->_orderBy = 'position';
        $this->_orderWay = 'ASC';
        $this->addRowAction('edit');
        $this->addRowAction('delete');
    }

    public function initContent()
    {
        parent::initContent();
    }

    public function displayRuleTypeArr($echo, $tr)
    {
        unset($tr);
        if (isset($this->rule_type_arr[$echo])) {
            return $this->rule_type_arr[$echo];
        }
    }
    
    public function displayDiscountTypeArr($echo, $tr)
    {
        unset($tr);
        if (isset($this->discount_type_arr[$echo])) {
            return $this->discount_type_arr[$echo];
        }
    }
    
    public function displayRuleNameArr($echo, $tr)
    {
        unset($tr);
        $data_value = Tools::jsonDecode($echo);
        if (!empty($data_value->rule_name)) {
            return $data_value->rule_name;
        }
    }

    public function postProcess()
    {
        parent::postProcess();

        if (Tools::isSubmit('active'.$this->table)) {
            $id = Tools::getValue('id_rules');
            $object = new $this->className((int) $id);
            if ($object->active == 1) {
                $object->active = 0;
            } else {
                $object->active = 1;
            }
            $object->update();
            $this->kbDeleteDiscountCoupons($id);
            $this->context->cookie->__set(
                'kb_redirect_success',
                $this->module->l('The status has been successfully updated.', 'AdminKbBookingDiscountsController')
            );
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBookingDiscounts', true));
        }
        
        if (Tools::isSubmit('action') && Tools::getValue('action') == 'updatePositions') {
            $json = $this->ajaxProcessUpdateRulesPositions();
            if (isset($json['success'])) {
                die(true);
            }
        }
    }

    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }
        
        $data_value = array();
        $data_value = Tools::jsonDecode($obj->value);
        $stores = array();
        foreach (Shop::getShops() as $shop) {
            $stores[] = array('id_shop' => $shop['id_shop'], 'name' => $shop['name']);
        }
        
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->module->l('Discount Rules Configuration', 'AdminKbBookingDiscountsController'),
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_rules',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Rule Name'),
                    'name' => 'rule_name',
                    'hint' => $this->l('Enter the name of the rule to be saved'),
                    'required' => true,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->module->l('Rule Type', 'AdminKbBookingDiscountsController'),
                    'name' => 'rule_type',
                    'hint' => $this->module->l('Select the type of rule', 'AdminKbBookingDiscountsController'),
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'booking',
                                'name' => $this->module->l('For Booking Products Only', 'AdminKbBookingDiscountsController'),
                            ),
                            array(
                                'id' => 'cart',
                                'name' => $this->module->l('Total Cart', 'AdminKbBookingDiscountsController'),
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'disabled' => ($obj->id) ? true : false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Minimum Amount'),
                    'name' => 'min_amount',
                    'class' => 'vss-text-width',
                    'hint' => $this->l('Enter the minimum amount to provide discount'),
                    'required' => true,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->module->l('Select Discount Type', 'AdminKbBookingDiscountsController'),
                    'name' => 'discount_type',
                    'hint' => $this->module->l('Select the type of discount', 'AdminKbBookingDiscountsController'),
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'fixed',
                                'name' => $this->module->l('Fixed', 'AdminKbBookingDiscountsController'),
                            ),
                            array(
                                'id' => 'percentage',
                                'name' => $this->module->l('Pecentage', 'AdminKbBookingDiscountsController'),
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'disabled' => ($obj->id) ? true : false,
                ),
                array(
                    'label' => $this->module->l('Fixed Amount', 'AdminKbBookingDiscountsController'),
                    'type' => 'text',
                    'required' => true,
                    'name' => 'fix_amount',
                    'desc' => $this->module->l('Please enter the fix discount value.', 'AdminKbBookingDiscountsController'),
                    'col' => 2
                ),
                array(
                    'label' => $this->module->l('Percentage', 'AdminKbBookingDiscountsController'),
                    'type' => 'text',
                    'required' => true,
                    'name' => 'percent_amount',
                    'hint' => $this->module->l('Please enter the discount value in percentage.', 'AdminKbBookingDiscountsController'),
                    'desc' => $this->module->l('Please do not provide % sign.', 'AdminKbBookingDiscountsController'),
                    'col' => 2,
                    'suffix' => '%'
                ),
                array(
                    'label' => $this->module->l('Coupon Validity (in Days)', 'AdminKbBookingDiscountsController'),
                    'type' => 'text',
                    'required' => true,
                    'name' => 'validity',
                    'hint' => $this->module->l('Date upto which coupon will be valid for this incentive.', 'AdminKbBookingDiscountsController'),
                    'col' => 2
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Enable/Disable Discount For Next Order', 'AdminKbBookingDiscountsController'),
                    'name' => 'order_discount',
                    'hint' => $this->module->l('In case of enabling option, we will share the discount coupon after placing the order and In case of disabling the same, we will provide discount on the user current order by adding discount coupon in the cart.', 'AdminKbBookingDiscountsController'),
                    'desc' => $this->module->l('In case of enabling option, we will share the discount coupon after placing the order and In case of disabling the same, we will provide discount on the user current order by adding discount coupon in the cart.', 'AdminKbBookingDiscountsController'),
                    'values' => array(
                        array(
                            'id' => 'order_discount_on',
                            'value' => 1
                        ),
                        array(
                            'id' => 'order_discount_off',
                            'value' => 0
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Active', 'AdminKbBookingDiscountsController'),
                    'name' => 'active',
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0
                        )
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->module->l('Shops', 'AdminKbBookingDiscountsController'),
                    'multiple' => true,
                    'name' => 'stores[]',
                    'hint' => $this->module->l('Allow this Rule for the selected stores.', 'AdminKbBookingDiscountsController'),
                    'desc' => $this->module->l('1. Hold CTRL to select multiple. 2. If no store is selected then Rule will be enable for all stores.', 'AdminKbBookingDiscountsController'),
                    'is_bool' => true,
                    'options' => array(
                        'query' => $stores,
                        'id' => 'id_shop',
                        'name' => 'name',
                    ),
                    'size' => 3
                ),
            ),
            'buttons' => array(
                array(
                    'title' => $this->module->l('Save', 'AdminKbBookingDiscountsController'),
                    'type' => 'submit',
                    'icon' => 'process-icon-save',
                    'class' => 'btn btn-default pull-right velsof_gift_product_add_rule',
                    'id' => 'submit_add',
                    'name' => 'submitAdd' . $this->table,
                ),
            )
        );
        
        $stores_edit = array();
        $store_id = explode(",", $obj->shop_id);
        if (count($this->ps_shop) == count($store_id)) {
            $stores_edit = array();
        } else {
            foreach ($store_id as $store) {
                $stores_edit[] = $store;
            }
        }
        
        $this->fields_value = array(
            'id_rules' => ($obj->id)?$obj->id:'',
            'rule_type' => ($obj->rule_type)?$obj->rule_type:'',
            'rule_name' => (isset($data_value->rule_name))?$data_value->rule_name:'',
            'min_amount' => (isset($data_value->min_amount))?$data_value->min_amount:'',
            'discount_type' => ($obj->discount_type)?$obj->discount_type:'',
            'fix_amount' => (isset($data_value->fix_amount))?$data_value->fix_amount:'',
            'percent_amount' => (isset($data_value->percent_amount))?$data_value->percent_amount:'',
            'validity' => (isset($data_value->validity))?$data_value->validity:'',
            'order_discount' => (isset($data_value->order_discount))?$data_value->order_discount:'',
            'active' => ($obj->active)?$obj->active:'',
            'stores[]' => $stores_edit,
        );

        $tpl = $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/velovalidation.tpl');
        return $tpl . parent::renderForm();
    }

    public function displayKbRuleForm($obj)
    {
        $data_value = array();
        $data_value = Tools::jsonDecode($obj->value);
        $stores = array();
        foreach (Shop::getShops() as $shop) {
            $stores[] = array('id_shop' => $shop['id_shop'], 'name' => $shop['name']);
        }

       
        $fields_form = array(
            'general' => array(
                'form' => array(
                    'input' => array(
                        array(
                            'type' => 'hidden',
                            'name' => 'id_rules',
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->module->l('Rule Type', 'AdminKbBookingDiscountsController'),
                            'name' => 'rule_type',
                            'hint' => $this->module->l('Select the type of rule', 'AdminKbBookingDiscountsController'),
                            'options' => array(
                                'query' => array(
                                    array(
                                        'id' => 'price',
                                        'name' => $this->module->l('Price Rule', 'AdminKbBookingDiscountsController'),
                                    ),
                                    array(
                                        'id' => 'product',
                                        'name' => $this->module->l('Product Rule', 'AdminKbBookingDiscountsController'),
                                    ),
                                ),
                                'id' => 'id',
                                'name' => 'name'
                            ),
                            'disabled' => ($obj->id) ? true : false,
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Rule Name'),
                            'name' => 'price_rule_name',
                            'hint' => $this->l('Enter the name of the rule to be saved'),
                            'required' => true,
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Minimum Cart Amount'),
                            'name' => 'min_cart_amount',
                            'class' => 'vss-text-width',
                            'hint' => $this->l('Enter the minimum cart amount to decide the gift products'),
                            'required' => true,
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->module->l('Active', 'AdminKbBookingDiscountsController'),
                            'name' => 'active',
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0
                                )
                            ),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->module->l('Shops', 'AdminKbBookingDiscountsController'),
                            'multiple' => true,
                            'name' => 'stores[]',
                            'hint' => $this->module->l('Allow this Rule for the selected stores.', 'AdminKbBookingDiscountsController'),
                            'desc' => $this->module->l('1. Hold CTRL to select multiple. 2. If no store is selected then Rule will be enable for all stores.', 'AdminKbBookingDiscountsController'),
                            'is_bool' => true,
                            'options' => array(
                                'query' => $stores,
                                'id' => 'id_shop',
                                'name' => 'name',
                            ),
                            'size' => 3
                        ),
                    ),
                    'buttons' => array(
                        array(
                            'title' => $this->module->l('Save', 'AdminKbBookingDiscountsController'),
                            'type' => 'submit',
                            'icon' => 'process-icon-save',
                            'class' => 'btn btn-default pull-right velsof_gift_product_add_rule',
                            'id' => 'submit_add',
                            'name' => 'submitAdd' . $this->table,
                        ),
                    )
                ),
            ),
        );

        $stores_edit = array();
        $store_id = explode(",", $obj->shop_id);
        if (count($this->ps_shop) == count($store_id)) {
            $stores_edit = array();
        } else {
            foreach ($store_id as $store) {
                $stores_edit[] = $store;
            }
        }

        $fields_value = array(
            'id_rules' => ($obj->id)?$obj->id:'',
            'rule_type' => ($obj->rule_type)?$obj->rule_type:'',
            'price_rule_name' => (isset($data_value->price_rule_name))?$data_value->price_rule_name:'',
            'min_cart_amount' => (isset($data_value->min_cart_amount))?$data_value->min_cart_amount:'',
            'max_cart_amount' => (isset($data_value->max_cart_amount))?$data_value->max_cart_amount:'',
            'min_product_amount' => (isset($data_value->min_product_amount))?$data_value->min_product_amount:'',
            'max_product_amount' => (isset($data_value->max_product_amount))?$data_value->max_product_amount:'',
            'category_type' => (isset($data_value->category_type))?$data_value->category_type:'',
            'exceptional_product_id' => (isset($data_value->exceptional_product_id))?$data_value->exceptional_product_id:'',
            'kb_specific_products' => '',
            'kb_specific_product_items' => (isset($data_value->kb_specific_product_items))?$data_value->kb_specific_product_items:'',
            'active' => ($obj->active)?$obj->active:'',
            'stores[]' => $stores_edit,
            'kb_products_map_id_items' => (isset($data_value->kb_products_map_id_items))?$data_value->kb_products_map_id_items:'',
            'kb_products_map_id' => '',
            'banner_image_update' => '',
        );

        $helper = new HelperForm();
        $this->setHelperDisplay($helper);
        $helper->fields_value = $fields_value;
        $helper->submit_action = $this->submit_action;
        $helper->show_cancel_button = (isset($this->show_form_cancel_button)) ? $this->show_form_cancel_button : ($this->display == 'add' || $this->display == 'edit');

        return $helper->generateForm($fields_form);
    }

    public function processAdd()
    {
        if (Tools::isSubmit('submitAdd'.$this->table)) {
            $data = array();
            $rule_type = Tools::getValue('rule_type');
            $discount_type = Tools::getValue('discount_type');
            $data['rule_name'] = trim(Tools::getvalue('rule_name'));
            $data['min_amount'] = trim(Tools::getvalue('min_amount'));
            $data['order_discount'] = trim(Tools::getvalue('order_discount'));
            $data['validity'] = (int) trim(Tools::getvalue('validity'));
            if ($discount_type == 'fixed') {
                $data['fix_amount'] = trim(Tools::getvalue('fix_amount'));
            } else {
                $data['percent_amount'] = trim(Tools::getvalue('percent_amount'));
            }
            $active = Tools::getValue('active');
            $stores = Tools::getValue('stores');
            $store_ids = '';
            if (!empty($stores)) {
                foreach ($stores as $value) {
                    $store_ids = $store_ids . $value . ',';
                }
            } else {
                foreach (Shop::getShops(false) as $shop) {
                    $store_ids = $store_ids . $shop['id_shop'] . ',';
                }
            }
            $store_ids = Tools::substr($store_ids, 0, -1);
            $this->object = new KbBookingDiscountRules();
            $this->object->rule_type = $rule_type;
            $this->object->discount_type = $discount_type;
            $this->object->active = $active;
            $this->object->shop_id = $store_ids;
            $this->object->value = Tools::jsonEncode($data);
            $this->object->position = $this->getNextAvailablePosition();

            if ($this->object->add()) {
                $this->context->cookie->__set('kb_redirect_success', $this->module->l('Rule successfully created', 'AdminKbBookingDiscountsController'));
                    $this->redirect_after = self::$currentIndex . '&token=' . $this->token;
            } else {
                $this->context->cookie->__set('kb_redirect_error', $this->module->l('Something went wrong while creating the rule. Please try again.', 'AdminKbBookingDiscountsController'));
            }
        }
    }


    public function processUpdate()
    {
        if (Tools::isSubmit('submitAdd' . $this->table)) {
            $id_rule = Tools::getValue('id_rules');
            $this->object = new KbBookingDiscountRules((int) $id_rule);
            $data = array();
            $rule_type = $this->object->rule_type;
            $discount_type = $this->object->discount_type;
            $data['rule_name'] = trim(Tools::getvalue('rule_name'));
            $data['min_amount'] = trim(Tools::getvalue('min_amount'));
            $data['order_discount'] = trim(Tools::getvalue('order_discount'));
            $data['validity'] = (int) trim(Tools::getvalue('validity'));
            if ($discount_type == 'fixed') {
                $data['fix_amount'] = trim(Tools::getvalue('fix_amount'));
            } else {
                $data['percent_amount'] = trim(Tools::getvalue('percent_amount'));
            }
            $active = Tools::getValue('active');
            $stores = Tools::getValue('stores');
            $store_ids = '';
            if (!empty($stores)) {
                foreach ($stores as $value) {
                    $store_ids = $store_ids . $value . ',';
                }
            } else {
                foreach (Shop::getShops(false) as $shop) {
                    $store_ids = $store_ids . $shop['id_shop'] . ',';
                }
            }
            $store_ids = Tools::substr($store_ids, 0, -1);
            $this->object->active = $active;
            $this->object->shop_id = $store_ids;
            $this->object->value = Tools::jsonEncode($data);

            if ($this->object->update()) {
                if ($active == 0) {
                    $this->kbDeleteDiscountCoupons($id_rule);
                }
                $this->context->cookie->__set('kb_redirect_success', $this->module->l('Rule successfully updated', 'AdminKbBookingDiscountsController'));
                $this->redirect_after = self::$currentIndex . '&token=' . $this->token;
            } else {
                $this->context->cookie->__set('kb_redirect_error', $this->module->l('Something went wrong while updating the rule. Please try again.', 'AdminKbBookingDiscountsController'));
            }
        }
    }


    public function psShopList($echo, $tr)
    {
        unset($tr);
        $store_id = explode(",", $echo);
        if (count($this->ps_shop) == count($store_id)) {
            return $this->module->l('All Shops', 'AdminKbBookingDiscountsController');
        } else {
            $shop_name = '';
            foreach ($store_id as $value) {
                $shop_name = $this->ps_shop[$value] . ' ,';
            }
            $shop_name = Tools::substr($shop_name, 0, -1);
            return $shop_name;
        }
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
    }

    public function setMedia($newTheme = false)
    {
        parent::setMedia($newTheme);
        $this->addJS(_PS_MODULE_DIR_ . 'gifttheproduct/views/js/velovalidation.js');
    }

    public function getModuleDirUrl()
    {
        $module_dir = '';
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        return $module_dir;
    }

    public function checkSecureUrl()
    {
        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] == 'on') {
                $custom_ssl_var = 1;
            }
        } else if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $custom_ssl_var = 1;
        }
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            return true;
        } else {
            return false;
        }
    }
    
    public function kbDeleteDiscountCoupons($rule_id)
    {
        $sql = "SELECT id_cart_rule FROM `" . _DB_PREFIX_ . "cart_rule` WHERE `description` = 'BOOKING' AND `active` = '1' AND `code` LIKE '%_" . $rule_id . "%'";
        $exist_rules = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        foreach ($exist_rules as $value) {
            $cart_rule = new CartRule((int)$value['id_cart_rule']);
            $cart_rule->delete();
        }
    }

    public static function getNextAvailablePosition()
    {
        $sql = 'SELECT MAX(position) as max_pos from ' . _DB_PREFIX_ . 'kb_booking_discount_rules';
        $max_pos = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        if ((!isset($max_pos) && $max_pos != 0 && empty($max_pos)) || $max_pos == null) {
            $max_pos = 0;
            return ($max_pos);
        }
        return ($max_pos + 1);
    }
    
    // Function to update Rule positions
    public function ajaxProcessUpdateRulesPositions()
    {
        $response_array = array();
        $id_rule = (int) Tools::getValue('id');
        $way = (int) Tools::getValue('way');
        $positions = Tools::getValue('rules');

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);
            if (isset($pos[2]) && (int) $pos[2] === $id_rule) {
                if ($fee_obj = new KbBookingDiscountRules((int) $pos[2])) {
                    if (isset($position) && $fee_obj->updateRulePosition($way, $position)) {
                        $response_array['success'] = true;
                    } else {
                        $response_array['hasError'] = true;
                        $response_array['errors'] = $this->module->l('Position Could not be updated.', 'AdminKbBookingDiscountsController');
                    }
                } else {
                    $response_array['hasError'] = true;
                    $response_array['errors'] = $this->module->l('Rule Could not be loaded.', 'AdminKbBookingDiscountsController');
                }
            }
        }
        return $response_array;
    }
}
