<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2018 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 *
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'kbbookingcalendar/classes/KbBookingProduct.php';
require_once _PS_MODULE_DIR_ . 'kbbookingcalendar/classes/KbBookingFacilities.php';
require_once _PS_MODULE_DIR_ . 'kbbookingcalendar/classes/KbBookingRoomCategory.php';
require_once _PS_MODULE_DIR_ . 'kbbookingcalendar/classes/KbBookingRoomType.php';
require_once _PS_MODULE_DIR_ . 'kbbookingcalendar/classes/KbBookingPriceRule.php';

class Kbbookingcalendar extends Module
{

    const MODEL_FILE = 'model.sql';
    const MODEL_DATA_FILE  = 'data.sql';
    const PARENT_TAB_CLASS = 'AdminKbBookingConfigure';
    const SELL_CLASS_NAME = 'SELL';
    
    public function __construct()
    {
        $this->name = 'kbbookingcalendar';
        $this->tab = 'front_office_features';
        $this->version = '2.0.0';
        $this->author = 'knowband';
        $this->lang = true;
        $this->bootstrap = true;
        $this->module_key = '36591156608fc74df0da0751f0182282';
        $this->author_address = '0x2C366b113bd378672D4Ee91B75dC727E857A54A6';
        parent::__construct();
        $this->displayName = $this->l('Knowband Booking/Rent/Reservation');
        $this->description = $this->l('This plugin provide the customers to book services from your shop. It builds a schedule according to the reservations set by your customers.');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        /*
         * Create Database table and if there is some problem then display error message
         */
        if (!$this->installModel()) {
            $this->custom_errors[] = $this->l('Error occurred while installing/upgrading modal.');
            return false;
        }
        
        //Admin tabs for AMP module
        $this->installKbTabs();
        
        /*
         * Register various hook functions
         */
        if (!parent::install() ||
            !$this->registerHook('displayHeader') ||
            !$this->registerHook('displayFooterProduct') ||
            !$this->registerHook('displayProductAdditionalInfo') ||
            !$this->registerHook('displayOrderConfirmation') ||
            !$this->registerHook('actionOrderStatusUpdate') ||
            !$this->registerHook('actionValidateOrder') ||
            !$this->registerHook('actionCartSave') ||
            !$this->registerHook('actionEmailAddAfterContent') ||
            !$this->registerHook('actionObjectProductInCartDeleteAfter') ||
            !$this->registerHook('displayReassurance')) {
            return false;
        }
        $select_datatype = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'._DB_NAME_.'" AND TABLE_NAME="'._DB_PREFIX_.'kb_booking_product_order" AND column_name="is_cancelled"';
        $data_type = Db::getInstance()->getValue($select_datatype);
        if (empty($data_type)) {
            Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'kb_booking_product_order ADD COLUMN `is_cancelled` int(11) DEFAULT 0');
        }
        
        $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "kb_booking_discount_rules` (
        `id_rules` int(10) unsigned NOT NULL auto_increment,
        `rule_type` enum('booking','cart') NOT NULL DEFAULT 'booking',
        `discount_type` enum('fixed','percentage') NOT NULL DEFAULT 'fixed',
        `active` int(5) NOT NULL DEFAULT '0',
        `value` text,
        `shop_id` text,
        `position` INT(10),
        `date_add` datetime NOT NULL,
        `date_upd` datetime NOT NULL,
        PRIMARY KEY (`id_rules`)
        ) ENGINE=" . _MYSQL_ENGINE_ . "  DEFAULT CHARSET=utf8;";
        Db::getInstance()->Execute($sql);
        return true;
    }
    
    /*
     * To install Database Table during install of the module
     */
    protected function installModel()
    {
        $installation_error = false;
        if (!file_exists(_PS_MODULE_DIR_ . $this->name . '/' . self::MODEL_FILE)) {
            $this->custom_errors[] = $this->l('Model installation file not found.');
            $installation_error = true;
        } elseif (!is_readable(_PS_MODULE_DIR_ . $this->name . '/' . self::MODEL_FILE)) {
            $this->custom_errors[] = $this->l('Model installation file is not readable.');
            $installation_error = true;
        } elseif (!$sql = Tools::file_get_contents(_PS_MODULE_DIR_ . $this->name . '/' . self::MODEL_FILE)) {
            $this->custom_errors[] = $this->l('Model installation file is empty.');
            $installation_error = true;
        }

        if (!$installation_error) {
            /*
             * Replace _PREFIX_ and ENGINE_TYPE with default Prestashop values
             */
            $sql = str_replace(
                array('_PREFIX_', 'ENGINE_TYPE'),
                array(_DB_PREFIX_, _MYSQL_ENGINE_),
                $sql
            );
            $sql = preg_split("/;\s*[\r\n]+/", trim($sql));
            foreach ($sql as $query) {
                if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->execute(trim($query))) {
                    $installation_error = true;
                }
            }
        }
        
        
        if ($installation_error) {
            return false;
        } else {
            /*Start- RJ made changes on 05-08-19 to to add column for fing per slot quantity issue in daily rental and appointment case*/
            $select_datatype = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'._DB_NAME_.'" AND TABLE_NAME="'._DB_PREFIX_.'kb_booking_product_cart" AND column_name="time_slot"';
            $data_type = Db::getInstance()->getValue($select_datatype);
            if (empty($data_type)) {
                Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'kb_booking_product_cart ADD COLUMN `time_slot` varchar(2000) DEFAULT NULL');
            }
            /*End*/
            return true;
        }
    }
    
    /*
     * Function to uninstall the module with
     * unregister various hook and
     * also delete the configuration setting
     */
    public function uninstall()
    {
        $this->unInstallKbTabs();
        
        if (!parent::uninstall()) {
            return false;
        }
        return true;
    }
    
    protected function removeBookingMenu($page_key)
    {
        $shops = Shop::getContextListShopID();
        foreach ($shops as $shop_id) {
            $shop_group_id = Shop::getGroupFromShop($shop_id);
            $id_linksmenutop = Configuration::get($page_key, null, $shop_group_id, $shop_id);
//        die($id_linksmenutop);
            if ($id_linksmenutop) {
                Db::getInstance()->delete('linksmenutop', 'id_linksmenutop = ' . (int) $id_linksmenutop . ' AND id_shop = ' . (int) $shop_id);
                Db::getInstance()->delete('linksmenutop_lang', 'id_linksmenutop = ' . (int) $id_linksmenutop);
                Configuration::updateValue('MOD_BLOCKTOPMENU_ITEMS', str_replace(array('LNK' . $id_linksmenutop . ',', 'LNK' . $id_linksmenutop), '', Configuration::get('MOD_BLOCKTOPMENU_ITEMS')));
                Configuration::deleteByName($page_key);
            }
        }
        return true;
    }


    /*
     * Function removes module tabs to the admin panel
     */
    public function unInstallKbTabs()
    {
        $parentTab = new Tab(Tab::getIdFromClassName(self::PARENT_TAB_CLASS));
        $parentTab->delete();
        
        $this->removeBookingMenu('KB_DAILY_RENTAL_LINK');
        $this->removeBookingMenu('KB_HOTELS_RENTAL_LINK');
        $this->removeBookingMenu('KB_APPOINTMENTS_RENTAL_LINK');
        $this->removeBookingMenu('KB_HOURLY_RENTAL_LINK');

        $admin_menus = $this->adminSubMenus();

        foreach ($admin_menus as $menu) {
            $sql = 'SELECT id_tab FROM `' . _DB_PREFIX_ . 'tab` Where class_name = "' . pSQL($menu['class_name']) . '" 
				AND module = "' . pSQL($this->name) . '"';
            $id_tab = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        return true;
    }
    
    //Function definition to install module tabs
    public function installKbTabs()
    {
        $parentTab = new Tab();
        $parentTab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $parentTab->name[$lang['id_lang']] = $this->l('Knowband Booking/Reservation');
        }

        $parentTab->class_name = self::PARENT_TAB_CLASS;
        $parentTab->module = $this->name;
        $parentTab->active = 1;
        $parentTab->id_parent = Tab::getIdFromClassName(self::SELL_CLASS_NAME);
        $parentTab->icon = 'bookmark';
//        Tools::dieObject($parentTab, true);
        $parentTab->add();

        $id_parent_tab = (int) Tab::getIdFromClassName(self::PARENT_TAB_CLASS);
        $admin_menus = $this->adminSubMenus();

        foreach ($admin_menus as $menu) {
            $tab = new Tab();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = $this->l($menu['name']);
            }

            $tab->class_name = $menu['class_name'];
            $tab->module = $this->name;
            $tab->active = $menu['active'];
            $tab->id_parent = $id_parent_tab;
            $tab->add($this->id);
        }
        return true;
    }
    
    /*
     * Function definition to create submenus list
     */
    public function adminSubMenus()
    {
        $subMenu = array(
            array(
                'class_name' => 'AdminKbBookingSettings',
                'name' => $this->l('General Settings'),
                'active' => 1,
            ),
            array(
                'class_name' => 'AdminKbBookingProducts',
                'name' => $this->l('Products'),
                'active' => 1,
            ),
            array(
                'class_name' => 'AdminKbBookingRoomCategory',
                'name' => $this->l('Booking Room Category'),
                'active' => 1,
            ),
            array(
                'class_name' => 'AdminKbBookingRoomType',
                'name' => $this->l('Booking Room Type'),
                'active' => 1,
            ),
            array(
                'class_name' => 'AdminKbBookingFacilities',
                'name' => $this->l('Booking Facilities'),
                'active' => 1,
            ),
            array(
                'class_name' => 'AdminKbBookingPriceRule',
                'name' => $this->l('Booking Price Rule'),
                'active' => 1,
            ),
            array(
                'class_name' => 'AdminKbBookingOrders',
                'name' => $this->l('Orders'),
                'active' => 1,
            ),
            array(
                'class_name' => 'AdminKbBookingDiscounts',
                'name' => $this->l('Booking Discounts'),
                'active' => 1,
            ),
            array(
                'class_name' => 'AdminKbBookingCalender',
                'name' => $this->l('Calender'),
                'active' => 1,
            ),
        );

        return $subMenu;
    }
    
    public function createBookingProductMenu($pagename, $pro_lab, $page_key)
    {
        $languages = Language::getLanguages(false);
        $labels = array();
        $links_label = array();
        foreach ($languages as $key => $val) {
            $links_label[$val['id_lang']] = $this->context->link->getModuleLink($this->name, $pagename, array(), null, $val['id_lang'], (int)$this->context->shop->id);
            $labels[$val['id_lang']] = $pro_lab;
        }
        $shops = Shop::getContextListShopID();
        foreach ($shops as $shop_id) {
            $shop_group_id = Shop::getGroupFromShop($shop_id);
            if (!Configuration::get($page_key, null, $shop_group_id, $shop_id)) {
                Db::getInstance()->insert(
                    'linksmenutop',
                    array(
                        'new_window' => (int) 0,
                        'id_shop' => (int) (int) $shop_id
                    )
                );
                $id_linksmenutop = Db::getInstance()->Insert_ID();
                $result = true;
                foreach ($labels as $id_lang => $label) {
                    $result &= Db::getInstance()->insert(
                        'linksmenutop_lang',
                        array(
                            'id_linksmenutop' => (int) $id_linksmenutop,
                            'id_lang' => (int) $id_lang,
                            'id_shop' => (int) $shop_id,
                            'label' => pSQL($label),
                            'link' => pSQL($links_label[$id_lang])
                        )
                    );
                }
                $conf = Configuration::get('MOD_BLOCKTOPMENU_ITEMS', null, $shop_group_id, $shop_id);
                if (Tools::strlen($conf)) {
                    $conf = explode(',', $conf);
                    if (!empty($conf) && is_array($conf)) {
                        $conf[] = 'LNK' . $id_linksmenutop;
                        Configuration::updateValue('MOD_BLOCKTOPMENU_ITEMS', (string) implode(',', $conf), false, (int) $shop_group_id, (int) $shop_id);
                        Configuration::updateValue($page_key, $id_linksmenutop, false, (int) $shop_group_id, (int) $shop_id);
                    }
                }
            }
        }
        return true;
    }
    
    public function getContent()
    {
        $error = false;
        $this->addBackOfficeMedia();

        /*
         * loop to fetch all language with default language in an array
         */
        $languages = Language::getLanguages(false);
        $output = '';
        if (isset($this->context->cookie->kb_redirect_success)) {
            $output .= $this->displayConfirmation($this->context->cookie->kb_redirect_success);
            unset($this->context->cookie->kb_redirect_success);
        }
        
        if (Tools::isSubmit('generalsettingsubmitkbbookingcalendar')) {
            $kbbooking = Tools::getValue('kbbooking');
            if (isset($kbbooking['api_key'])) {
                $kbbooking['api_key'] = trim($kbbooking['api_key']);
            }
            if (isset($kbbooking['kb_coupon_name']) && !empty($kbbooking['kb_coupon_name'])) {
                $kbbooking['kb_coupon_name'] = trim($kbbooking['kb_coupon_name']);
            } else {
                $kbbooking['kb_coupon_name'] = 'BOOKING';
            }
            Configuration::updateValue('KB_BOOKING_CALENDAR_GENERAL_SETTING', Tools::jsonEncode($kbbooking));
            if ($kbbooking['enable']) {
                $this->createBookingProductMenu('dailyrentals', $this->l('Daily Rentals'), 'KB_DAILY_RENTAL_LINK');
                $this->createBookingProductMenu('hourlyrentals', $this->l('Hourly Rentals'), 'KB_HOURLY_RENTAL_LINK');
                $this->createBookingProductMenu('appointments', $this->l('Appointments'), 'KB_APPOINTMENTS_RENTAL_LINK');
                $this->createBookingProductMenu('hotels', $this->l('Hotels'), 'KB_HOTELS_RENTAL_LINK');
//                die;
            } else {
                $this->removeBookingMenu('KB_DAILY_RENTAL_LINK');
                $this->removeBookingMenu('KB_HOTELS_RENTAL_LINK');
                $this->removeBookingMenu('KB_APPOINTMENTS_RENTAL_LINK');
                $this->removeBookingMenu('KB_HOURLY_RENTAL_LINK');
            }
            $this->context->cookie->__set('kb_redirect_success', $this->l('Setting successfully saved.'));
            Tools::redirectAdmin($this->context->link->getAdminlink('AdminModules', true) . '&configure=' . $this->name);
        }
        
        $kb_db_data = Tools::jsonDecode(Configuration::get('KB_BOOKING_CALENDAR_GENERAL_SETTING'), true);
        $config_field_value = array(
            'kbbooking[enable]' => (!empty($kb_db_data) && isset($kb_db_data['enable'])) ? $kb_db_data['enable'] : 0,
            'kbbooking[api_key]' => (!empty($kb_db_data) && isset($kb_db_data['api_key'])) ? $kb_db_data['api_key'] : '',
            'kbbooking[display_price_rule]' => (!empty($kb_db_data) && isset($kb_db_data['display_price_rule'])) ? $kb_db_data['display_price_rule'] : 0,
            'kbbooking[kb_coupon_name]' => (!empty($kb_db_data) && isset($kb_db_data['kb_coupon_name'])) ? $kb_db_data['kb_coupon_name'] : 'BOOKING',
        );

        foreach ($languages as $k => $language) {
            $languages[$k]['is_default'] = ((int) ($language['id_lang'] == $this->context->language->id));
        }
        
        /*
        * Create configuration setting form
        */
        $this->fields_form = $this->getConfigurationForm();
        
        /*
         * Create helper form for configuration setting form
         */
        $form = $this->getform(
            $this->fields_form,
            $languages,
            $this->l('Configuration'),
            false,
            $config_field_value,
            'general',
            'generalsetting'
        );
        
        $this->context->smarty->assign('form', $form);
        $this->context->smarty->assign('firstCall', false);
        
        /*
         * Generate form using Helper class
         */
        $helper = new Helper();
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')
            ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->override_folder = 'helpers/';
        $helper->base_folder = 'form/';
        $tpl = 'Form_custom.tpl';
        $helper->setTpl($tpl);
        $tpl = $helper->generate();
        
        $output .= $tpl;
        return $output;
    }
    
    public function hookActionOrderStatusUpdate($params = null)
    {
        $id_order = $params['id_order'];
        $order_obj = new Order($id_order);
        $order_state = $params['newOrderStatus'];
        $errorOrCanceledStatuses = array(Configuration::get('PS_OS_ERROR'), Configuration::get('PS_OS_CANCELED'));
        if (in_array($order_state->id, $errorOrCanceledStatuses)) {
            $sql = 'Select id_booking_order from ' . _DB_PREFIX_ . 'kb_booking_product_order where id_order ='. (int) $order_obj->id;
            $orders_detail = Db::getInstance()->executeS($sql);
            if (count($orders_detail) > 0 && is_array($orders_detail)) {
                foreach ($orders_detail as $order_detail_key => $order_data) {
                    $sql_update = 'update ' . _DB_PREFIX_ . 'kb_booking_product_order set is_cancelled = 1 where id_order ='. (int) $order_obj->id.' and id_booking_order ='.(int)  $order_data['id_booking_order'];
                    Db::getInstance()->execute($sql_update);
                }
            }
        }
    }
    
    /*
     * Function for including the media files in the admin panel
     */
    protected function addBackOfficeMedia()
    {
        /* CSS files */
        $this->context->controller->addCSS($this->_path . 'views/css/admin/kb_admin.css');
        
        /* JS files */
        $this->context->controller->addJS($this->_path . 'views/js/velovalidation.js');
        $this->context->controller->addJS($this->_path . 'views/js/admin/kb_admin.js');
        $this->context->controller->addJS($this->_path . 'views/js/admin/validation_admin.js');
    }
    
    public function getConfigurationForm()
    {
        //Store Order Statuses List
        $orderStatuses = array();
        $statuses = OrderState::getOrderStates((int) $this->context->language->id);
        foreach ($statuses as $status) {
            $orderStatuses[] = array(
                'id_option' => $status['id_order_state'],
                'name' => $status['name']
            );
        }
        
        $form = array(
            'form' => array(
                'id_form' => 'general_configuration_form',
                'legend' => array(
                    'title' => $this->l('General Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'label' => $this->l('Enable/Disable'),
                        'type' => 'switch',
                        'name' => 'kbbooking[enable]',
                        'values' => array(
                            array(
                                'id' => 'kbbooking[enable]_on',
                                'value' => 1,
                            ),
                            array(
                                'id' => 'kbbooking[enable]_off',
                                'value' => 0,
                            ),
                        ),
                        'hint' => $this->l('Enable/Disable the plugin')
                    ),
                    array(
                        'label' => $this->l('Google Map API Key'),
                        'type' => 'text',
                        'required' => true,
                        'name' => 'kbbooking[api_key]',
                        'hint' => $this->l('Enter the Google Map API key'),
                        'col'=> 5,
                        'desc' => $this->l('Click here to').' <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key">'.$this->l('create Google Map API key').'</a>',
                    ),
                    array(
                        'label' => $this->l('Display price rule'),
                        'type' => 'switch',
                        'name' => 'kbbooking[display_price_rule]',
                        'values' => array(
                            array(
                                'id' => 'kbbooking[display_price_rule]_on',
                                'value' => 1,
                            ),
                            array(
                                'id' => 'kbbooking[display_price_rule]_off',
                                'value' => 0,
                            ),
                        ),
                        'hint' => $this->l('Enable/Disable to display price rule')
                    ),
                    array(
                        'label' => $this->l('Coupon Name'),
                        'type' => 'text',
                        'required' => true,
                        'name' => 'kbbooking[kb_coupon_name]',
                        'hint' => $this->l('Enter the Name of the Coupons which created by our module.'),
                        'col'=> 5,
                        'desc' => $this->l('Enter the Name of the Coupons which created by our module. Also, If the value is not set then it set BOOKING by default'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right form_general'
                ),
            ),
        );
        return $form;
    }
    
    public function setKbMedia($front = false)
    {
        if ($front) {
            $this->context->controller->addJquery();
            $this->context->controller->addJQuery();
            $this->context->controller->addCSS($this->_path . '/views/css/front/datetimepicker/bootstrap-datetimepicker.css');
//            $this->context->controller->addJS($this->_path . '/views/js/front/datetimepicker/locales/bootstrap-datetimepicker.en.js');
            $this->context->controller->addJS($this->_path . '/views/js/front/datetimepicker/bootstrap-datetimepicker.js');
            $this->context->controller->addCSS($this->_path . 'views/css/front/kb_front.css');
            $this->context->controller->addCSS($this->_path . 'views/css/front/slick-theme.css');
            $this->context->controller->addCSS($this->_path . 'views/css/font-awesome.min.css');
            $this->context->controller->addCSS($this->_path . 'views/css/front/slick.css');
            $this->context->controller->addJS($this->_path . 'views/js/front/kb_front.js');
            $this->context->controller->addJS($this->_path . 'views/js/front/slick.js');
        }
    }
    
    public function hookDisplayHeader()
    {
        $this->setKbMedia(true);
        $php_self = $this->context->controller->php_self;
        // changes by rishabh jain for booking calendar hide quick view
        
        if ($php_self != 'product') {
            $this->context->controller->addJS($this->_path . 'views/js/front/booking_quick_view.js');
        }

        // changes over
        if ($php_self == 'history' || $php_self == 'order-detail' || $php_self == 'cart') {
            $id_customer = $this->context->customer->id;
            if ($php_self != 'cart') {
                $orders = array();
                $kb_orders = array();
                if ($id_customer) {
                    $orders = self::getKbCustomerOrders($id_customer);
                    foreach ($orders as $order) {
                        $kb_orders[] = $order['id_order'];
                    }
                }
                if (!empty($kb_orders)) {
                    $this->context->smarty->assign('history_reorder', 1);
                    $this->context->smarty->assign('kb_history_orders', Tools::jsonEncode($kb_orders));
                }
            } else {
                $id_cart = $this->context->cart->id;
                $cart_details = Db::getInstance()->executeS('SELECT c.*,cp.* FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart c INNER JOIN ' . _DB_PREFIX_ . 'cart_product cp on (c.id_cart=cp.id_cart) WHERE c.id_cart=' . (int) $id_cart);
                $product_arr = array();
                if (!empty($cart_details)) {
                    foreach ($cart_details as $cart) {
                        $product_arr[] = $cart['id_product'];
                    }
                    $this->context->smarty->assign('kb_cart_validate', 1);
                    $this->context->smarty->assign('kb_cart_prod', Tools::jsonEncode($product_arr));
                }
            }
            return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/hook/order_history.tpl');
        }
    }
    
    public function hookactionObjectProductInCartDeleteAfter($params)
    {
        if (!empty($params)) {
            $id_cart = $params['id_cart'];
            $id_product = $params['id_product'];
            $id_customization = $params['customization_id'];
            Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'kb_booking_product_cart WHERE id_product='.(int)$id_product.' AND id_cart='.(int)$id_cart.' AND id_customization='.(int)$id_customization);
        }
    }
    
    public function hookActionValidateOrder($params)
    {
        $this->addKBBCOrders($params['order'], $params['cart']);
    }
    
    public function addKBBCOrders($order_obj, $cart_obj)
    {
        $orders_by_reference = Order::getByReference($order_obj->reference);
        $orders = $orders_by_reference->getResults();
        if ($orders && is_array($orders) && count($orders) > 0) {
            foreach ($orders as $order) {
                $order_product_detail = $order->getProducts();
                if ($order_product_detail && is_array($order_product_detail) && count($order_product_detail) > 0) {
                    foreach ($order_product_detail as $detail) {
                        $booking_product = KbBookingProduct::getProductDetailsByID($detail['product_id']);
                        if (!empty($booking_product)) {
                            Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'kb_booking_product_order set id_cart=' . (int) $cart_obj->id . ', id_customization=' . (int) $detail['id_customization'] . ',id_order=' . (int) $order_obj->id . ',id_product=' . (int) $detail['product_id'] . ', price="' . pSQL($detail['unit_price_tax_incl']) . '",qty=' . (int) $detail['product_quantity'] . ', date_add=now(),date_upd=now()');
                        }
                    }
                }
            }
        }
    }
    
    public function hookDisplayFooterProduct()
    {
        $kb_setting = Tools::jsonDecode(Configuration::get('KB_BOOKING_CALENDAR_GENERAL_SETTING'), true);
        if (!empty($kb_setting) && $kb_setting['enable']) {
            $id_product = Tools::getValue('id_product');
            if (!empty($id_product)) {
                $booking_product = KbBookingProduct::getProductDetailsByID($id_product);
                if (!empty($booking_product)) {
                    $id_booking_product = $booking_product['id_booking_product'];
                    $booking_facilities = KbBookingFacilities::getFacilitiesMappedwithProduct($id_booking_product);
                    $booking_room_tpl = '';
                    if ($booking_product['product_type'] == 'hotel_booking') {
                        $hotel_rooms = KbBookingProduct::getHotelProductRoomsByID($id_booking_product);
                        
                        if (!empty($hotel_rooms)) {
                            foreach ($hotel_rooms as $room_key => $room_data) {
                                $room_type_obj = new KbBookingRoomType($room_data['id_room_type']);
                                if ((int)$room_type_obj->active == 0) {
                                    unset($hotel_rooms[$room_key]);
                                }
                            }
                            if (!empty($hotel_rooms)) {
                                foreach ($hotel_rooms as &$rooms) {
    //                                $room_type_obj = new KbBookingRoomType($rooms['id_room_type']);
    //                                if ((int)$room_type_obj->active == 0) {
    //                                    unset($rooms);
    //                                } else {
                                        $rooms['room_facilities'] = (!empty($rooms['id_facilities'])) ? KbBookingFacilities::getFacilitiesMappedwithHotelRooms($rooms['id_booking_room_facilities_map'], $rooms['id_facilities']) : '';
                                        $room_type_obj = new KbBookingRoomType($rooms['id_room_type']);
                                        $rooms['room_type'] = KbBookingRoomType::getAvailableRoomTypeByID($rooms['id_room_type']);
                                        $rooms['room_category_name'] = KbBookingRoomCategory::getRoomCategoryNameByID($rooms['id_room_category']);
                                        $rooms['price'] = Tools::displayPrice(Tools::convertPrice($rooms['price']));
    //                                }
                                }
                                $this->context->smarty->assign(array(
                                    'hotel_rooms' => $hotel_rooms,
                                    'no_img' => $this->getModuleDirUrl() . $this->name . '/views/img/404.gif?time=' . time(),
                                    'hotel_url' => $this->context->link->getModuleLink($this->name, 'hotels'),
                                ));
                            }
                        }
                        $booking_room_tpl = $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/hook/hotel_rooms.tpl');
                    }
                    $book_faci_tpl = '';
                    if (!empty($booking_facilities)) {
                        $this->context->smarty->assign(
                            array(
                                'booking_facilities' => $booking_facilities,
                            )
                        );
                        $book_faci_tpl = $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/hook/product_facilities.tpl');
                    }
                    return $booking_room_tpl . $book_faci_tpl;
                }
            }
        }
    }
    
    
    public function hookActionEmailAddAfterContent($params)
    {
        $content = '';
        $payment_result = '';
        if ($params['template'] == 'order_conf') { // Let's edit content of Order's Confirmation email
            $id_cart = $params['cart']->id;
            $cart = new Cart($id_cart);
            $product_details = $cart->getProducts(true);
            $id_order = Order::getOrderByCartId($id_cart);
            if (!empty($id_order)) {
                $order = new Order($id_order);
            }
            $virtual_product = true;
            $product_var_tpl_list = array();
            foreach ($product_details as $product) {
                $booking_product_details = DB::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'kb_booking_product_order where id_order='.(int)$id_order.' AND id_product='.(int)$product['id_product'].' AND id_customization='.(int)$product['id_customization']);
                if (!empty($booking_product_details)) {
                    $price = $price_wt = $booking_product_details['price'];
                    $product_price = Product::getTaxCalculationMethod() == PS_TAX_EXC
                        ? Tools::ps_round($price, 2)
                        : $price_wt;

                    $product_var_tpl = array(
                        'reference' => $product['reference'],
                        'name' => $product['name']
                            . (isset($product['attributes']) ? ' - ' . $product['attributes'] : ''),
                        'unit_price' => Tools::displayPrice($product_price, $this->context->currency, false),
                        'price' => Tools::displayPrice(
                            $product_price * $product['quantity'],
                            $this->context->currency,
                            false
                        ),
                        'quantity' => $product['quantity'],
                        'customization' => array()
                    );

                    if (isset($product['price']) && $product['price']) {
                        $product_var_tpl['unit_price'] = Tools::displayPrice($product_price, $this->context->currency, false);
                        $product_var_tpl['unit_price_full'] = Tools::displayPrice($product_price, $this->context->currency, false)
                                . ' ' . $product['unity'];
                    } else {
                        $product_var_tpl['unit_price'] = $product_var_tpl['unit_price_full'] = '';
                    }

                    $customized_datas = Product::getAllCustomizedDatas((int) $order->id_cart, null, true, null, (int) $product['id_customization']);
                    if (isset($customized_datas[$product['id_product']][$product['id_product_attribute']])) {
                        $product_var_tpl['customization'] = array();
                        foreach ($customized_datas[$product['id_product']][$product['id_product_attribute']][$order->id_address_delivery] as $customization) {
                            $customization_text = '';
                            if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD])) {
                                foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text) {
                                    $customization_text .= '<strong>' . $text['name'] . '</strong>: ' . $text['value'] . '<br />';
                                }
                            }

                            if (isset($customization['datas'][Product::CUSTOMIZE_FILE])) {
                                $customization_text .= $this->trans('%d image(s)', array(count($customization['datas'][Product::CUSTOMIZE_FILE])), 'Admin.Payment.Notification') . '<br />';
                            }

                            $customization_quantity = (int) $customization['quantity'];

                            $product_var_tpl['customization'][] = array(
                                'customization_text' => $customization_text,
                                'customization_quantity' => $customization_quantity,
                                'quantity' => Tools::displayPrice($customization_quantity * $product_price, $this->context->currency, false)
                            );
                        }
                    }

                    $product_var_tpl_list[] = $product_var_tpl;
                    // Check if is not a virutal product for the displaying of shipping
                    if (!$product['is_virtual']) {
                        $virtual_product &= false;
                    }
                }
            }
//            d($product_var_tpl_list);
            $product_list_txt = '';
            $product_list_html = '';
            if (count($product_var_tpl_list) > 0) {
                $product_list_txt = $this->getEmailTemplateContent(
                    'order_conf_product_list.txt',
                    Mail::TYPE_TEXT,
                    $product_var_tpl_list
                );
                $product_list_html = $this->getEmailTemplateContent(
                    'order_conf_product_list.tpl',
                    Mail::TYPE_HTML,
                    $product_var_tpl_list
                );
            }
            $params['template_html'] = str_replace("{products}", $product_list_html, $params['template_html']); // and add text to end of {products} variable
        }
    }
    
    protected function getEmailTemplateContent($template_name, $mail_type, $var)
    {
        $email_configuration = Configuration::get('PS_MAIL_TYPE');
        if ($email_configuration != $mail_type && $email_configuration != Mail::TYPE_BOTH) {
            return '';
        }

        $theme_template_path = _PS_MODULE_DIR_ . $this->name .'/views/templates/hook/'. $template_name;

        if (Tools::file_exists_cache($theme_template_path)) {
            $this->context->smarty->assign('list', $var);
            return $this->context->smarty->fetch($theme_template_path);
        }
        return '';
    }
    
    public function hookDisplayReassurance($params)
    {
        $kb_setting = Tools::jsonDecode(Configuration::get('KB_BOOKING_CALENDAR_GENERAL_SETTING'), true);
        if ((bool) Context::getContext()->customer->isLogged()) {
            $customer = new Customer((int) Context::getContext()->customer->id);
            $customer_group = $customer->getGroups();
        } else {
            $customer_group = array(Configuration::get('PS_UNIDENTIFIED_GROUP'));
        }
        $is_display_tax_excl_price = 0;
        foreach ($customer_group as $key => $group_id) {
            $grp_obj = new group($group_id);
            $is_display_tax_excl_price = (int) $grp_obj->price_display_method;
        }
        if (!empty($kb_setting) && $kb_setting['enable']) {
            $id_product = Tools::getValue('id_product');
            if (!empty($id_product)) {
                $booking_product = KbBookingProduct::getProductDetailsByID($id_product);
                if (!empty($booking_product)) {
                    $product_type = $booking_product['product_type'];
                    if ($is_display_tax_excl_price) {
                        $product_price = Product::getPriceStatic($id_product, false, null, 6);
                    } else {
                        $product_price = Product::getPriceStatic($id_product, true, null, 6);
                    }
                    $product_price = Tools::convertPriceFull($product_price, Currency::getDefaultCurrency());
                    $room_category = KbBookingRoomCategory::getAvailableRoomCategory();
                    $booking_date_details = Tools::jsonDecode($booking_product['date_details'], true);
                    $price_rule_applicable = KbBookingPriceRule::isPriceRuleApplicable($id_product);
                    $disable_days = Tools::jsonDecode($booking_product['disable_days'], true);
                    if (!empty($disable_days)) {
                        $disable_string = array();
                        for ($i = 0; $i < 7; $i++) {
                            if (isset($disable_days['disable_days_'.$i])) {
                                 $disable_string[] = $i;
                            }
                        }
                        $string_not_avail = implode(',', $disable_string);
                        $this->context->smarty->assign(
                            'kbdisable_days',
                            $string_not_avail
                        );
                    } else {
                        $this->context->smarty->assign(
                            'kbdisable_days',
                            '7'
                        );
                    }
                    $this->context->smarty->assign(
                        array(
                            'booking_product_details' => $booking_product,
                            'product_type' => $product_type,
                            'product_price' => $product_price,
                            'render_dates' => $booking_product['date_details'],
                            'display_product_price' => Tools::displayPrice($product_price),
                            'map_api_key' => $kb_setting['api_key'],
                            'room_category' => $room_category,
                            'hotel_url' => $this->context->link->getModuleLink($this->name, 'hotels'),
                            'cart_url' => $this->context->link->getModuleLink($this->name, 'cart'),
                            'currency_sign' => $this->context->currency->sign,
                            'price_rule_applicable' => $price_rule_applicable,
                            'kb_setting' => $kb_setting,
                            'actual_cart_url' => $this->context->link->getPageLink('cart'),
                            'current_date' => date('Y-m-d'),
                        )
                    );
                    return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/hook/product_addcart.tpl');
                }
            }
        }
    }
    public function hookDisplayProductAdditionalInfo($params)
    {
        $kb_setting = Tools::jsonDecode(Configuration::get('KB_BOOKING_CALENDAR_GENERAL_SETTING'), true);
        $id_product = (int) $params['product']['id_product'];
        $booking_product = KbBookingProduct::getProductDetailsByID($id_product);
        if (!empty($kb_setting) && $kb_setting['enable']  && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && Tools::getIsset('action') && Tools::getValue('action') == 'quickview') {
            if (!empty($booking_product)) {
                $product_obj = new Product($id_product, true, $this->context->language->id, $this->context->shop->id);
                $product_page_link = $this->context->link->getProductLink($product_obj);
                $this->context->smarty->assign('product_page_link', $product_page_link);
                return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/hook/quick_view_product_link.tpl');
            }
        }
    }
    /**
     * Get customer orders
     *
     * @param int $id_customer Customer id
     * @param bool $show_hidden_status Display or not hidden order statuses
     * @return array Customer orders
     */
    public static function getKbCustomerOrders($id_customer, $show_hidden_status = false, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT o.*, (SELECT SUM(od.`product_quantity`) FROM `' . _DB_PREFIX_ . 'order_detail` od WHERE od.`id_order` = o.`id_order`) nb_products
            FROM `' . _DB_PREFIX_ . 'orders` o INNER JOIN '._DB_PREFIX_.'kb_booking_product_order b on (b.`id_order` = o.`id_order`)
            WHERE o.`id_customer` = ' . (int) $id_customer .
            Shop::addSqlRestriction(Shop::SHARE_ORDER) . '
            GROUP BY o.`id_order`
            ORDER BY o.`date_add` DESC'
        );
        if (!$res) {
            return array();
        }

        foreach ($res as $key => $val) {
            $res2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                'SELECT os.`id_order_state`, osl.`name` AS order_state, os.`invoice`, os.`color` as order_state_color
                FROM `' . _DB_PREFIX_ . 'order_history` oh
                LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
                INNER JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = ' . (int) $context->language->id . ')
                WHERE oh.`id_order` = ' . (int) $val['id_order'] . (!$show_hidden_status ? ' AND os.`hidden` != 1' : '') . '
                ORDER BY oh.`date_add` DESC, oh.`id_order_history` DESC
                LIMIT 1'
            );

            if ($res2) {
                $res[$key] = array_merge($res[$key], $res2[0]);
            }
        }
        return $res;
    }
    
    /*
     * Function to create Helper Form
     */

    public function getform($field_form, $languages, $title, $show_cancel_button, $field_value, $id, $action)
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->fields_value = $field_value;
        $helper->name_controller = $this->name;
        $helper->languages = $languages;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')
            ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->default_form_language = $this->context->language->id;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->title = $title;
        if ($id == 'general') {
            $helper->show_toolbar = true;
        } else {
            $helper->show_toolbar = false;
        }
        $helper->table = $id;
        $helper->firstCall = true;
        $helper->toolbar_scroll = true;
        $helper->show_cancel_button = $show_cancel_button;
        $helper->submit_action = $action . 'submit' . $this->name;
        return $helper->generateForm(array('form' => $field_form));
    }
    
    private function getModuleDirUrl()
    {
        $module_dir = '';
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        return $module_dir;
    }
    
    /*
     * Function for checking SSL
     */
    private function checkSecureUrl()
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
    
    /*
     * Function for returning the URL of PrestaShop Root Modules Directory
     */
    protected function getSiteUrl()
    {
        $module_dir = '';
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__;
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__;
        }
        return $module_dir;
    }
    
    /*
     * function for Returning the Base URL of the store
     */
    protected function getBaseUrl()
    {
        $module_dir = '';
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ ;
        } else {
            $module_dir = _PS_BASE_URL_ ;
        }
        return $module_dir;
    }
    
    public function hookActionCartSave()
    {
        $config = Tools::jsonDecode(Configuration::get('KB_BOOKING_CALENDAR_GENERAL_SETTING'), true);
        $sql = "SELECT count(*) as total FROM `" . _DB_PREFIX_ . "kb_booking_discount_rules` WHERE `active` = 1";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        if ($config['enable'] == 1 && $result['total'] > 0) {
            if (!empty($this->context->cart)) {
                $cart_obj = new Cart($this->context->cart->id);
                $cart_rules = $cart_obj->getCartRules();
                $cart_data = $cart_obj->getProducts();
                $kb_count = count($cart_rules);
                if ($kb_count > 0) {
                    foreach ($cart_rules as $key => $value) {
                        if ($value['obj']->description == 'BOOKING') {
                            return;
                        }
                    }
                }
                $rules_product = array();
                $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'kb_booking_discount_rules where active=1 order by position';
                $rules = Db::getInstance()->executeS($sql);

                $rules_final = array();
                foreach ($rules as $key => $rule) {
                    $shop_ids = explode(",", $rule['shop_id']);
                    foreach ($shop_ids as $value) {
                        if ($value == (int) Context::getContext()->shop->id) {
                            $rules_final[] = $rule;
                        }
                    }
                }

                foreach ($rules_final as $rule) {
                    $rule_values = Tools::jsonDecode($rule['value']);
                    if ($rule_values->order_discount == 0) {
                        $check_amount = 0;
                        $product_restriction = 0;
                        if ($rule['rule_type'] == 'cart') {
                            $check_amount = $cart_obj->getOrderTotal(true);
                        } else {
                            $product_restriction = 1;
                            $sql_cust = 'SELECT id_product,id_customization,price,qty FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart where id_cart=' . (int) $cart_obj->id;
                            $booking_data = Db::getInstance()->executeS($sql_cust);
                            foreach ($booking_data as $product) {
                                foreach ($cart_data as $cartpro) {
                                    if ($cartpro['id_product'] == $product['id_product']) {
                                        $Price = Tools::ps_round(
                                            Product::getPriceStaticBookingProduct(
                                                (int) $product['id_customization'],
                                                (int) $product['id_product'],
                                                true,
                                                0,
                                                6,
                                                null,
                                                false,
                                                true,
                                                $product['qty']
                                            ),
                                            (int) Configuration::get('PS_PRICE_DISPLAY_PRECISION')
                                        );
                                        $check_amount = $check_amount + $Price;
                                    }
                                }
                            }
                        }
                        if ($check_amount >= $rule_values->min_amount) {
                            $data = array();
                            $settings = array();
                            $data['code'] = $this->geraHash(10) . '_' . $rule['id_rules'];
                            $coupon_name = $config['kb_coupon_name'];
                            $description = 'BOOKING';
                            if ($rule['discount_type'] == 'fixed') {
                                $fixed_reduction = $rule_values->fix_amount;
                                $percent_reduction = 0;
                            } else {
                                $fixed_reduction = 0;
                                $percent_reduction = $rule_values->percent_amount;
                            }
                            $is_used_partial = 0;
                            $kb_minimum_amount = $rule_values->min_amount;
                            $settings['expire_in_days'] = $rule_values->validity;
                            $settings['free_shiping'] = 0;


                            $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'cart_rule  SET
                                id_customer = ' . (int) $this->context->customer->id . ',
                                date_from = "' . pSQL(date('Y-m-d H:i:s', time())) . '",
                                date_to = "' . pSQL(date('Y-m-d 23:23:59', strtotime($settings['expire_in_days'] . ' day'))) . '",
                                description = "' . pSQL(strip_tags($description)) . '",
                                quantity = 1, quantity_per_user = 1, priority = 1, partial_use = ' . (int) $is_used_partial . ',
                                code = "' . pSQL($data['code']) . '", minimum_amount = ' . $kb_minimum_amount . ', minimum_amount_tax = 1, 
                                minimum_amount_currency = 0, minimum_amount_shipping = 1,
                                country_restriction = 0, carrier_restriction = 0, group_restriction = 0, cart_rule_restriction = 0, 
                                product_restriction = ' . (int) $product_restriction . ', shop_restriction = 0, 
                                free_shipping = ' . (int) $settings['free_shiping'] . ',
                                reduction_percent = ' . (float) $percent_reduction . ', reduction_amount = '
                                                . (float) $fixed_reduction . ',
                                reduction_tax = 1, reduction_currency = ' . (int) $cart_obj->id_currency . ',
                                reduction_product = 0, gift_product = 0, gift_product_attribute = 0,
                                highlight = 0, active = 1,
                                date_add = "' . pSQL(date('Y-m-d H:i:s', time()))
                                    . '", date_upd = "' . pSQL(date('Y-m-d H:i:s', time())) . '"';

                            Db::getInstance()->execute($sql);
                            $cart_rule_id = Db::getInstance()->Insert_ID();

                            Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_shop
                            set id_cart_rule = ' . (int) $cart_rule_id . ', id_shop = ' . (int) $this->context->shop->id);

                            foreach (Language::getLanguages(true) as $lang) {
                                Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_lang
                            set id_cart_rule = ' . (int) $cart_rule_id . ', id_lang = ' . (int) $lang['id_lang'] . ', 
                            name = "' . strip_tags($coupon_name) . '"');
                            }

                            if ($product_restriction == 1) {
                                Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_product_rule_group
                                set id_cart_rule = ' . (int) $cart_rule_id . ', quantity = 1');
                                $id_product_rule_group = Db::getInstance()->Insert_ID();

                                Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_product_rule
                                set id_product_rule_group = ' . (int) $id_product_rule_group . ', type = "products"');
                                $id_product_rule = Db::getInstance()->Insert_ID();

                                $sql_cust = 'SELECT DISTINCT(id_product) FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart where id_cart=' . (int) $cart_obj->id;
                                $booking_data = Db::getInstance()->executeS($sql_cust);
                                foreach ($booking_data as $product) {
                                    Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_product_rule_value
                                    set id_product_rule= ' . (int) $id_product_rule . ', id_item =' . (int) $product['id_product']);
                                }
                            }
                            $cart_obj->addCartRule($cart_rule_id);
                            break;
                        }
                    }
                }
            }
        }
    }
    
    public function geraHash($qtd)
    {
        $Caracteres = 'ABCDEFGHIJKLMOPQRSTUVXWYZ0123456789';
        $QuantidadeCaracteres = Tools::strlen($Caracteres);
        $QuantidadeCaracteres--;

        $Hash = null;
        for ($x = 1; $x <= $qtd; $x++) {
            $Posicao = rand(0, $QuantidadeCaracteres);
            $Hash .= Tools::substr($Caracteres, $Posicao, 1);
        }
        return $Hash;
    }
    
    public function hookDisplayOrderConfirmation($params)
    {
        $config = Tools::jsonDecode(Configuration::get('KB_BOOKING_CALENDAR_GENERAL_SETTING'), true);
        $sql = "SELECT count(*) as total FROM `" . _DB_PREFIX_ . "kb_booking_discount_rules` WHERE `active` = 1";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        if ($config['enable'] == 1 && $result['total'] > 0) {
            $order_id = Tools::getvalue('id_order');
            $order = new Order($order_id);
            $cart_obj = new Cart((int) $order->id_cart);

            $rules_product = array();
            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'kb_booking_discount_rules where active=1 order by position';
            $rules = Db::getInstance()->executeS($sql);

            $rules_final = array();
            foreach ($rules as $key => $rule) {
                $shop_ids = explode(",", $rule['shop_id']);
                foreach ($shop_ids as $value) {
                    if ($value == (int) Context::getContext()->shop->id) {
                        $rules_final[] = $rule;
                    }
                }
            }

            foreach ($rules_final as $rule) {
                $rule_values = Tools::jsonDecode($rule['value']);
                if ($rule_values->order_discount == 1) {
                    $check_amount = 0;
                    $product_restriction = 0;
                    if ($rule['rule_type'] == 'cart') {
                        $check_amount = $cart_obj->getOrderTotal(true);
                    } else {
                        $sql_cust = 'SELECT id_product,id_customization,price,qty FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart where id_cart=' . (int) $cart_obj->id;
                        $booking_data = Db::getInstance()->executeS($sql_cust);
                        foreach ($booking_data as $product) {
                            $Price = Tools::ps_round(
                                Product::getPriceStaticBookingProduct(
                                    (int) $product['id_customization'],
                                    (int) $product['id_product'],
                                    true,
                                    0,
                                    6,
                                    null,
                                    false,
                                    true,
                                    $product['qty']
                                ),
                                (int) Configuration::get('PS_PRICE_DISPLAY_PRECISION')
                            );
                            $check_amount = $check_amount + $Price;
                        }
                    }
                    if ($check_amount >= $rule_values->min_amount) {
                        $data = array();
                        $settings = array();
                        $data['code'] = $this->geraHash(10) . '_' . $rule['id_rules'];
                        $coupon_name = $config['kb_coupon_name'];
                        $description = 'BOOKING';
                        if ($rule['discount_type'] == 'fixed') {
                            $fixed_reduction = $rule_values->fix_amount;
                            $percent_reduction = 0;
                            $this->context->smarty->assign('discount', Tools::displayPrice($fixed_reduction));
                        } else {
                            $fixed_reduction = 0;
                            $percent_reduction = $rule_values->percent_amount;
                            $this->context->smarty->assign('discount_percentage', $percent_reduction);
                        }
                        $is_used_partial = 0;
                        $kb_minimum_amount = $rule_values->min_amount;
                        $settings['expire_in_days'] = $rule_values->validity;
                        $settings['free_shiping'] = 0;

                        $sql = "UPDATE " . _DB_PREFIX_ . "cart_rule SET active = 0 WHERE id_customer =" . (int) $this->context->customer->id;
                        Db::getInstance()->execute($sql);
                        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'cart_rule  SET
                            id_customer = ' . (int) $this->context->customer->id . ',
                            date_from = "' . pSQL(date('Y-m-d H:i:s', time())) . '",
                            date_to = "' . pSQL(date('Y-m-d 23:23:59', strtotime($settings['expire_in_days'] . ' day'))) . '",
                            description = "' . pSQL(strip_tags($description)) . '",
                            quantity = 1, quantity_per_user = 1, priority = 1, partial_use = ' . (int) $is_used_partial . ',
                            code = "' . pSQL($data['code']) . '", minimum_amount = ' . $kb_minimum_amount . ', minimum_amount_tax = 1, 
                            minimum_amount_currency = 0, minimum_amount_shipping = 1,
                            country_restriction = 0, carrier_restriction = 0, group_restriction = 0, cart_rule_restriction = 0, 
                            product_restriction = ' . (int) $product_restriction . ', shop_restriction = 0, 
                            free_shipping = ' . (int) $settings['free_shiping'] . ',
                            reduction_percent = ' . (float) $percent_reduction . ', reduction_amount = '
                                . (float) $fixed_reduction . ',
                            reduction_tax = 1, reduction_currency = ' . (int) $cart_obj->id_currency . ',
                            reduction_product = 0, gift_product = 0, gift_product_attribute = 0,
                            highlight = 0, active = 1,
                            date_add = "' . pSQL(date('Y-m-d H:i:s', time()))
                                . '", date_upd = "' . pSQL(date('Y-m-d H:i:s', time())) . '"';

                        Db::getInstance()->execute($sql);
                        $cart_rule_id = Db::getInstance()->Insert_ID();

                        Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_shop
                            set id_cart_rule = ' . (int) $cart_rule_id . ', id_shop = ' . (int) $this->context->shop->id);

                        foreach (Language::getLanguages(true) as $lang) {
                            Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_lang
                            set id_cart_rule = ' . (int) $cart_rule_id . ', id_lang = ' . (int) $lang['id_lang'] . ', 
                            name = "' . strip_tags($coupon_name) . '"');
                        }
                        $this->context->smarty->assign('discount_code', $data['code']);
                        $offer_valid_date = Tools::displayDate(date('Y-m-d', strtotime($settings['expire_in_days'] . ' day')));
                        $this->context->smarty->assign('offer_valid_date', $offer_valid_date);
                        return $this->display(__FILE__, 'views/templates/hook/discount_order_conf.tpl');
                        break;
                    }
                }
            }
        }
    }
}
