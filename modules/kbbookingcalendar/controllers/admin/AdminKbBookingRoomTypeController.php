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

require_once dirname(__FILE__).'/AdminKbBookingCoreController.php';
require_once _PS_MODULE_DIR_.'kbbookingcalendar/classes/KbBookingRoomType.php';
require_once _PS_MODULE_DIR_.'kbbookingcalendar/classes/KbBookingRoomCategory.php';

class AdminKbBookingRoomTypeController extends AdminKbBookingCoreController
{
    protected $ps_shop = array();
    
    public function __construct()
    {
        $this->table = 'kb_booking_room_type';
        $this->className = 'KbBookingRoomType';
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->identifier    = 'id_room_type';
        parent::__construct();
        $this->toolbar_title = $this->module->l('Room Type', 'AdminKbBookingRoomTypeController');
        foreach (Shop::getShops(false) as $shop) {
            $this->ps_shop[$shop['id_shop']] = $shop['name'];
        }
        
        $this->fields_list = array(
            'id_room_type' => array(
                'title' => $this->module->l('ID', 'AdminKbBookingRoomTypeController'),
                'align' => 'center',
//                'filter_key' => 'ps.id_product',
                'class' => 'fixed-width-xs'
            ),
            'room_name' => array(
                'title' => $this->module->l('Room Type', 'AdminKbBookingRoomTypeController'),
//                'filter_key' => 'pl!name'
            ),
            'room_category' => array(
                'title' => $this->module->l('Room Category', 'AdminKbBookingRoomTypeController'),
                'search' => false,
                'callback' => 'roomcategoryList'
//                'filter_key' => 'pl!name'
            ),
            'active' => array(
                'title' => $this->module->l('Status', 'AdminKbBookingRoomTypeController'),
                'align' => 'text-center',
                'active' => 'active',
                'type' => 'bool',
                'order_key' => 'active',
            ),
            'date_upd' => array(
                'title' => $this->module->l('Updated On', 'AdminKbBookingRoomTypeController'),
                'type' => 'datetime'
            )
        );
        
        $this->_select = 'l.room_name,s.id_shop';
        $this->_join = ' INNER JOIN `' . _DB_PREFIX_ . $this->table . '_lang` l on (a.id_room_type=l.id_room_type AND l.id_lang='
                .(int)Context::getContext()->language->id.' AND l.id_shop='.(int)Context::getContext()->shop->id.') ';
        $this->_join .= ' INNER JOIN `' . _DB_PREFIX_ . $this->table . '_shop` s on (a.id_room_type=s.id_room_type) ';
        $this->_where = ' AND s.id_shop IN ('.(int)Context::getContext()->shop->id.')';
        
        $this->addRowAction('edit');
        $this->addRowAction('delete');
    }
    
    public function initContent()
    {
        parent::initContent();
    }
    
    public function postProcess()
    {
        parent::postProcess();
        
        if (Tools::getValue('ajax')) {
            if (Tools::getValue('fetchRoomType')) {
                $room_type_arr = array();
                $id_room_category = Tools::getValue('id_category');
                if (!empty($id_room_category)) {
                    $available_room_type = KbBookingRoomType::getAvailableRoomType();
                    if (!empty($available_room_type)) {
                        foreach ($available_room_type as $room_type) {
                            $room_type_category = explode(',', $room_type['room_category']);
                            if (in_array($id_room_category, $room_type_category)) {
                                $room_type_arr[] = array(
                                    'id_room_type' => $room_type['id_room_type'],
                                    'room_name' => $room_type['room_name'],
                                );
                            }
                        }
                    }
                }
                echo Tools::jsonEncode($room_type_arr);
                die;
            }
        }
        
        if (Tools::isSubmit('active'.$this->table)) {
            $id = Tools::getValue('id_room_type');
            $object = new $this->className((int) $id);
            if ($object->active == 1) {
                $object->active = 0;
            } else {
                $object->active = 1;
            }
            $object->update();
            $this->context->cookie->__set(
                'kb_redirect_success',
                $this->module->l('The status has been successfully updated.', 'AdminKbBookingRoomTypeController')
            );
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBookingRoomType', true));
        }
        if (Tools::isSubmit('submitBulkenableSelection' . $this->table)) {
            $this->processBulkEnableSelection();
            $this->context->cookie->__set('kb_redirect_success', $this->module->l('The status has been successfully updated.', 'AdminKbBookingRoomTypeController'));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBookingRoomType', true));
        }
        
        if (Tools::isSubmit('submitBulkdisableSelection' . $this->table)) {
            $this->processBulkDisableSelection();
            $this->context->cookie->__set('kb_redirect_success', $this->module->l('The status has been successfully updated.', 'AdminKbBookingRoomTypeController'));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBookingRoomType', true));
        }
    }
    
    protected function processBulkEnableSelection()
    {
        return $this->processBulkStatusSelection(1);
    }

    protected function processBulkDisableSelection()
    {
        return $this->processBulkStatusSelection(0);
    }
    
    /**
    * Function used to update the bulk action selection
    */
    protected function processBulkStatusSelection($status)
    {
        $boxes = Tools::getValue($this->table.'Box');
        $result = true;
        if (is_array($boxes) && !empty($boxes)) {
            foreach ($boxes as $id) {
                $object = new $this->className((int) $id);
                $object->active = (int) $status;
                $result &= $object->update();
            }
        }
        return $result;
    }
    public function processAdd()
    {
        if (Tools::isSubmit('submitAdd'.$this->table)) {
            $room_name = array();
            foreach ($this->all_languages as $lang) {
                $room_name[$lang['id_lang']] = trim(Tools::getValue('room_name_'.$lang['id_lang']));
            }
            $max_allowed_child = trim(Tools::getValue('max_allowed_child'));
            $max_allowed_adult = trim(Tools::getValue('max_allowed_adult'));
            $booking_category = implode(',', Tools::getValue('booking_category'));
            $KbBookingRoomType = new KbBookingRoomType();
            $KbBookingRoomType->room_name = $room_name;
            $KbBookingRoomType->max_allowed_adult = $max_allowed_adult;
            $KbBookingRoomType->max_allowed_child = $max_allowed_child;
            $KbBookingRoomType->room_category = $booking_category;
            $KbBookingRoomType->active = Tools::getValue('active');
            if ($KbBookingRoomType->add()) {
                $this->context->cookie->__set('kb_redirect_success', $this->module->l('Room Type successfully created.', 'AdminKbBookingRoomTypeController'));
                Tools::redirectAdmin($this->context->link->getAdminlink('AdminKbBookingRoomType', true));
            }
        }
    }
    
    public function processUpdate()
    {
        if (Tools::isSubmit('submitAdd'.$this->table)) {
            $room_name = array();
            $id_room_type = Tools::getValue('id_room_type');
            foreach ($this->all_languages as $lang) {
                $room_name[$lang['id_lang']] = trim(Tools::getValue('room_name_'.$lang['id_lang']));
            }
            $max_allowed_child = trim(Tools::getValue('max_allowed_child'));
            $max_allowed_adult = trim(Tools::getValue('max_allowed_adult'));
            $booking_category = implode(',', Tools::getValue('booking_category'));
            $KbBookingRoomType = new KbBookingRoomType($id_room_type);
            $KbBookingRoomType->room_name = $room_name;
            $KbBookingRoomType->max_allowed_adult = $max_allowed_adult;
            $KbBookingRoomType->max_allowed_child = $max_allowed_child;
            $KbBookingRoomType->room_category = $booking_category;
            $KbBookingRoomType->active = Tools::getValue('active');
            if ($KbBookingRoomType->update()) {
                $this->context->cookie->__set('kb_redirect_success', $this->module->l('Room Type successfully updated.', 'AdminKbBookingRoomTypeController'));
                Tools::redirectAdmin($this->context->link->getAdminlink('AdminKbBookingRoomType', true));
            }
        }
    }
    
    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }
        $booking_category = KbBookingRoomCategory::getAvailableRoomCategory();
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->module->l('Room Type', 'AdminKbBookingRoomTypeController'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Name', 'AdminKbBookingRoomTypeController'),
                    'name' => 'room_name',
                    'lang' => true,
                    'required' => true,
                    'col' => '5',
                    'hint' => $this->module->l('Enter the name of room type', 'AdminKbBookingRoomTypeController')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Max Allowed Child', 'AdminKbBookingRoomTypeController'),
                    'name' => 'max_allowed_child',
                    'required' => true,
                    'col' => '2',
                    'hint' => $this->module->l('Enter the maximum number of allowed Children', 'AdminKbBookingRoomTypeController')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Max Allowed Adult', 'AdminKbBookingRoomTypeController'),
                    'name' => 'max_allowed_adult',
                    'required' => true,
                    'col' => '2',
                    'hint' => $this->module->l('Enter the maximum number of allowed Adult', 'AdminKbBookingRoomTypeController')
                ),
                array(
                    'type' => 'select',
                    'multiple' => true,
                    'required' => true,
                    'name' => 'booking_category[]',
                    'label' => $this->module->l('Select Room Category', 'AdminKbBookingRoomTypeController'),
                    'options' => array(
                        'query' => $booking_category,
                        'id' => 'id_booking_category',
                        'name' => 'name'
                    ),
                    'class' => 'kb_booking_room_category_select'
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Active', 'AdminKbBookingRoomTypeController'),
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
            ),
            'submit' => array(
                'class' => 'btn btn-default pull-right',
                'title' => $this->module->l('Save', 'AdminKbBookingRoomTypeController')
            )
        );
        $this->fields_value['booking_category[]'] = explode(',', $obj->room_category);
        
        $tpl = $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/velovalidation.tpl');
        return $tpl . parent::renderForm();
    }
    
    public function psShopList($echo, $tr)
    {
        unset($tr);
        return $this->ps_shop[$echo];
    }
    
    public function roomcategoryList($echo, $tr)
    {
        unset($tr);
        $booking_category = explode(',', $echo);
        $str = array();
        if (!empty($booking_category)) {
            foreach ($booking_category as $cat) {
                $str[] = KbBookingRoomCategory::getRoomCategoryNameByID($cat);
            }
        }
        return implode(', ', $str);
    }
    
    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['back_url'] = array(
            'href' => 'javascript: window.history.back();',
            'desc' => $this->module->l('Back', 'AdminKbBookingRoomTypeController'),
            'icon' => 'process-icon-back'
        );
        if (!Tools::getValue('id_room_type') && !Tools::isSubmit('add'.$this->table)) {
            $this->page_header_toolbar_btn['new_template'] = array(
                'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                'desc' => $this->module->l('Add new room type', 'AdminKbBookingRoomTypeController'),
                'icon' => 'process-icon-new'
            );
        }
        parent::initPageHeaderToolbar();
    }
    
    public function setMedia($newTheme = false)
    {
        parent::setMedia($newTheme);
        $this->addJQueryPlugin('select2');
    }
}
