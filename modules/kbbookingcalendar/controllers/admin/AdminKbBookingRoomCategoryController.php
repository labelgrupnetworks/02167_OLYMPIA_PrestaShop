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
require_once _PS_MODULE_DIR_.'kbbookingcalendar/classes/KbBookingRoomCategory.php';

class AdminKbBookingRoomCategoryController extends AdminKbBookingCoreController
{
    protected $ps_shop = array();
//    protected $max_image_size = null;
    
    public function __construct()
    {
        $this->table = 'kb_booking_category';
        $this->className = 'KbBookingRoomCategory';
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->identifier    = 'id_booking_category';
        parent::__construct();
        $this->toolbar_title = $this->module->l('Room Category', 'AdminKbBookingRoomCategoryController');
        foreach (Shop::getShops(false) as $shop) {
            $this->ps_shop[$shop['id_shop']] = $shop['name'];
        }
//        $this->max_image_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
        
        $this->fields_list = array(
            'id_booking_category' => array(
                'title' => $this->module->l('ID', 'AdminKbBookingRoomCategoryController'),
                'align' => 'center',
//                'filter_key' => 'ps.id_product',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->module->l('Name', 'AdminKbBookingRoomCategoryController'),
                 'filter_key' => 'name',
            ),
            'description' => array(
                'title' => $this->module->l('Description', 'AdminKbBookingRoomCategoryController'),
//                'filter_key' => 'pl!name'
            ),
            'active' => array(
                'title' => $this->module->l('Status', 'AdminKbBookingRoomCategoryController'),
                'align' => 'text-center',
                'active' => 'active',
                'type' => 'bool',
                'order_key' => 'active',
            ),
            'date_add' => array(
                'title' => $this->module->l('Created On', 'AdminKbBookingRoomCategoryController'),
                'type' => 'datetime'
            )
        );
        
        $this->_select = 'l.name,s.id_shop';
        $this->_join = ' INNER JOIN `' . _DB_PREFIX_ . $this->table . '_lang` l on (a.id_booking_category=l.id_booking_category AND l.id_lang='
                .(int)Context::getContext()->language->id.' AND l.id_shop='.(int)Context::getContext()->shop->id.') ';
        $this->_join .= ' INNER JOIN `' . _DB_PREFIX_ . $this->table . '_shop` s on (a.id_booking_category=s.id_booking_category) ';
        $this->_where = ' AND s.id_shop IN ('.(int)Context::getContext()->shop->id.')';
        
        $this->addRowAction('edit');
        $this->addRowAction('delete');
    }
    
    public function psShopList($echo, $tr)
    {
        unset($tr);
        return $this->ps_shop[$echo];
    }
    
    public function initContent()
    {
        parent::initContent();
    }
    
    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('active'.$this->table)) {
            $id = Tools::getValue('id_booking_category');
            $object = new $this->className((int) $id);
            if ($object->active == 1) {
                $object->active = 0;
            } else {
                $object->active = 1;
            }
            $object->update();
            $this->context->cookie->__set(
                'kb_redirect_success',
                $this->module->l('The status has been successfully updated.', 'AdminKbBookingRoomCategoryController')
            );
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBookingRoomCategory', true));
        }
        if (Tools::isSubmit('submitBulkenableSelection' . $this->table)) {
            $this->processBulkEnableSelection();
            $this->context->cookie->__set('kb_redirect_success', $this->module->l('The status has been successfully updated.', 'AdminKbBookingRoomCategoryController'));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBookingRoomCategory', true));
        }
        
        if (Tools::isSubmit('submitBulkdisableSelection' . $this->table)) {
            $this->processBulkDisableSelection();
            $this->context->cookie->__set('kb_redirect_success', $this->module->l('The status has been successfully updated.', 'AdminKbBookingRoomCategoryController'));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBookingRoomCategory', true));
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
            $names = array();
            foreach ($this->all_languages as $lang) {
                $names[$lang['id_lang']] = trim(Tools::getValue('name_'.$lang['id_lang']));
            }
            $description = trim(Tools::getValue('description'));
            $active = Tools::getValue('active');
            
            $kbcategory = new KbBookingRoomCategory();
            $kbcategory->name = $names;
            $kbcategory->active = $active;
            $kbcategory->description = $description;
            if ($kbcategory->add()) {
                $this->context->cookie->__set('kb_redirect_success', $this->module->l('Category successfully created.', 'AdminKbBookingRoomCategoryController'));
                Tools::redirectAdmin($this->context->link->getAdminlink('AdminKbBookingRoomCategory', true));
            }
        }
    }
    
    public function processUpdate()
    {
        if (Tools::isSubmit('submitAdd'.$this->table)) {
            $id_booking_category = Tools::getValue('id_booking_category');
            $names = array();
            foreach ($this->all_languages as $lang) {
                $names[$lang['id_lang']] = trim(Tools::getValue('name_'.$lang['id_lang']));
            }
            $description = trim(Tools::getValue('description'));
            $active = Tools::getValue('active');
            $kbcategory = new KbBookingRoomCategory($id_booking_category);
            $kbcategory->name = $names;
            $kbcategory->active = $active;
            $kbcategory->description = $description;
            if ($kbcategory->update()) {
                $this->context->cookie->__set('kb_redirect_success', $this->module->l('Category successfully updated.', 'AdminKbBookingRoomCategoryController'));
                Tools::redirectAdmin($this->context->link->getAdminlink('AdminKbBookingRoomCategory', true));
            }
        }
    }
    
    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }
        
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->module->l('Room Category', 'AdminKbBookingRoomCategoryController'),
//                'icon' => 'icon-envelope'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Name', 'AdminKbBookingRoomCategoryController'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'col' => '5',
                    'hint' => $this->module->l('Enter the name of category', 'AdminKbBookingRoomCategoryController')
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->module->l('Description', 'AdminKbBookingRoomCategoryController'),
                    'name' => 'description',
                     'col' => '5',
                ),
                array(
                    'type' => 'switch',
                    'name' => 'active',
                    'label' => $this->module->l('Active', 'AdminKbBookingRoomCategoryController'),
                    'hint' => $this->module->l('Enable/disable the category', 'AdminKbBookingRoomCategoryController'),
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
                'title' => $this->module->l('Save', 'AdminKbBookingRoomCategoryController')
            )
        );
         $tpl = $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/velovalidation.tpl');
        return $tpl.parent::renderForm();
    }
    
    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['back_url'] = array(
            'href' => 'javascript: window.history.back();',
            'desc' => $this->module->l('Back', 'AdminKbBookingRoomCategoryController'),
            'icon' => 'process-icon-back'
        );
        if (!Tools::getValue('id_booking_category') && !Tools::isSubmit('add'.$this->table)) {
            $this->page_header_toolbar_btn['new_template'] = array(
                'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                'desc' => $this->module->l('Add new category', 'AdminKbBookingRoomCategoryController'),
                'icon' => 'process-icon-new'
            );
        }
        parent::initPageHeaderToolbar();
    }
}
