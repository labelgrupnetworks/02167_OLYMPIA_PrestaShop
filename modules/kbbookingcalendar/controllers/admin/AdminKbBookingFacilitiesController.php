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
require_once _PS_MODULE_DIR_.'kbbookingcalendar/classes/KbBookingFacilities.php';

class AdminKbBookingFacilitiesController extends AdminKbBookingCoreController
{
    protected $ps_shop = array();
//    protected $max_image_size = null;
    
    public function __construct()
    {
        $this->table = 'kb_booking_facilities';
        $this->className = 'KbBookingFacilities';
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->identifier    = 'id_facilities';
        parent::__construct();
        $this->toolbar_title = $this->module->l('Facilities', 'AdminKbBookingFacilitiesController');
        foreach (Shop::getShops(false) as $shop) {
            $this->ps_shop[$shop['id_shop']] = $shop['name'];
        }
//        $this->max_image_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
        
        $this->fields_list = array(
            'id_facilities' => array(
                'title' => $this->module->l('ID', 'AdminKbBookingFacilitiesController'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->module->l('Name', 'AdminKbBookingFacilitiesController'),
            ),
            'upload_image' => array(
                'title' => $this->module->l('Image', 'AdminKbBookingFacilitiesController'),
                'callback' => 'displayFacilityImage',
                'search' => false,
//                'filter_key' => 'pl!name'
            ),
            'type' => array(
                'title' => $this->module->l('Type', 'AdminKbBookingFacilitiesController'),
//                'filter_key' => 'pl!name'
            ),
            'active' => array(
                'title' => $this->module->l('Status', 'AdminKbBookingFacilitiesController'),
                'align' => 'text-center',
                'active' => 'active',
                'type' => 'bool',
                'order_key' => 'active',
            ),
             'id_shop' => array(
                'title' => $this->module->l('Shop', 'AdminKbBookingFacilitiesController'),
                'align' => 'center',
                'type' => 'select',
                'list' => $this->ps_shop,
                'filter_key' => 'a!id_shop',
                'callback' => 'psShopList',
//                'search' => false
            ),
            'date_upd' => array(
                'title' => $this->module->l('Updated On', 'AdminKbBookingFacilitiesController'),
                'type' => 'datetime'
            )
        );
        
        $this->_select = 'l.name,s.id_shop';
        $this->_join = ' INNER JOIN `' . _DB_PREFIX_ . $this->table . '_lang` l on (a.id_facilities=l.id_facilities AND l.id_lang='
                .(int)Context::getContext()->language->id.' AND l.id_shop='.(int)Context::getContext()->shop->id.') ';
        $this->_join .= ' INNER JOIN `' . _DB_PREFIX_ . $this->table . '_shop` s on (a.id_facilities=s.id_facilities) ';
        $this->_where = ' AND s.id_shop IN ('.(int)Context::getContext()->shop->id.')';
        $this->addRowAction('edit');
        $this->addRowAction('delete');
    }
    
    public function initContent()
    {
        parent::initContent();
    }
    
    public function psShopList($echo, $tr)
    {
        unset($tr);
        return $this->ps_shop[$echo];
    }
    
    public function displayFacilityImage($echo, $tr)
    {
        unset($echo);
        if ($tr['image_type'] == 'upload') {
            return ImageManagerCore::thumbnail($tr['upload_image_path'], 'facility_mini_' . $tr['id_facilities'] . '_' . $this->context->shop->id . '.' . $this->imageType, 60, $this->imageType, true, true);
        } else {
            return '<i class="fa fa-3x '.$tr['font_awesome_icon'].'"></i>';
        }
    }
    
    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('active'.$this->table)) {
            $id = Tools::getValue('id_facilities');
            $object = new $this->className((int) $id);
            if ($object->active == 1) {
                $object->active = 0;
            } else {
                $object->active = 1;
            }
            $object->update();
            $this->context->cookie->__set(
                'kb_redirect_success',
                $this->module->l('The status has been successfully updated.', 'AdminKbBookingFacilitiesController')
            );
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBookingFacilities', true));
        }
        if (Tools::isSubmit('submitBulkenableSelection' . $this->table)) {
            $this->processBulkEnableSelection();
            $this->context->cookie->__set('kb_redirect_success', $this->module->l('The status has been successfully updated.', 'AdminKbBookingFacilitiesController'));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBookingFacilities', true));
        }
        
        if (Tools::isSubmit('submitBulkdisableSelection' . $this->table)) {
            $this->processBulkDisableSelection();
            $this->context->cookie->__set('kb_redirect_success', $this->module->l('The status has been successfully updated.', 'AdminKbBookingFacilitiesController'));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBookingFacilities', true));
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
    public function processUpdate()
    {
        if (Tools::isSubmit('submitAdd' . $this->table)) {
            $name = array();
            $id_facilities = Tools::getValue('id_facilities');
            foreach ($this->all_languages as $lang) {
                $name[$lang['id_lang']] = trim(Tools::getValue('name_' . $lang['id_lang']));
            }
            $type = Tools::getValue('type');
            $image_type = Tools::getValue('image_type');
            $font_awesome_icon = trim(Tools::getValue('font_awesome_icon'));
            $active = Tools::getValue('active');
            $image_path = '';
            $image_upload = '';
            $is_img_upload = false;
            if ($image_type == 'upload') {
                $font_awesome_icon = '';
                if (!empty($_FILES)) {
                    if ($_FILES['image_upload']['error'] == 0 && $_FILES['image_upload']['name'] != '' && $_FILES['image_upload']['size'] > 0) {
                        $file_extension = pathinfo($_FILES['image_upload']['name'], PATHINFO_EXTENSION);
                        $time = time();
                        $path = _PS_MODULE_DIR_ . $this->module->name . '/views/img/' . 'facilities_' . $time . '.' . $file_extension;
                        $upload = move_uploaded_file($_FILES['image_upload']['tmp_name'], $path);
                        chmod($path, 0777);
                        if ($upload) {
                            $image_path = $path;
                            $image_upload = $this->getModuleDirUrl() . $this->module->name . '/views/img/' . 'facilities_' . $time . '.' . $file_extension;
                            $is_img_upload = true;
                        }
                    }
                }
            }

            $kbFacilities = new KbBookingFacilities($id_facilities);
            $kbFacilities->type = $type;
            $kbFacilities->image_type = $image_type;
            if ($is_img_upload) {
                $kbFacilities->upload_image_path = $image_path;
                $kbFacilities->upload_image = $image_upload;
            }
            $kbFacilities->font_awesome_icon = $font_awesome_icon;
            $kbFacilities->active = $active;
            $kbFacilities->name = $name;
            if ($kbFacilities->update()) {
                $this->context->cookie->__set('kb_redirect_success', $this->module->l('Facility successfully updated.', 'AdminKbBookingFacilitiesController'));
                Tools::redirectAdmin($this->context->link->getAdminlink('AdminKbBookingFacilities', true));
            }
        }
    }
    
    public function processAdd()
    {
        if (Tools::isSubmit('submitAdd'.$this->table)) {
             $name = array();
            foreach ($this->all_languages as $lang) {
                $name[$lang['id_lang']] = trim(Tools::getValue('name_' . $lang['id_lang']));
            }
            $type = Tools::getValue('type');
            $image_type = Tools::getValue('image_type');
            $font_awesome_icon = trim(Tools::getValue('font_awesome_icon'));
            $active = Tools::getValue('active');
            $image_path = '';
            $image_upload = '';
            if ($image_type == 'upload') {
                $font_awesome_icon = '';
                if (!empty($_FILES)) {
                    if ($_FILES['image_upload']['error'] == 0 && $_FILES['image_upload']['name'] != '' && $_FILES['image_upload']['size'] > 0) {
                        $file_extension = pathinfo($_FILES['image_upload']['name'], PATHINFO_EXTENSION);
                        $time = time();
                        $path = _PS_MODULE_DIR_ . $this->module->name . '/views/img/' . 'facilities_' . $time . '.' . $file_extension;
                        $upload = move_uploaded_file($_FILES['image_upload']['tmp_name'], $path);
                        chmod($path, 0777);
                        if ($upload) {
                            $is_feature_img_upload = true;
                            $image_path = $path;
                            $image_upload = $this->getModuleDirUrl() . $this->module->name . '/views/img/' . 'facilities_' . $time . '.' . $file_extension;
                        }
                    }
                }
            }
            
            $kbFacilities = new KbBookingFacilities();
            $kbFacilities->type = $type;
            $kbFacilities->image_type = $image_type;
            $kbFacilities->upload_image_path = $image_path;
            $kbFacilities->upload_image = $image_upload;
            $kbFacilities->font_awesome_icon = $font_awesome_icon;
            $kbFacilities->active = $active;
            $kbFacilities->name = $name;
            if ($kbFacilities->add()) {
                $this->context->cookie->__set('kb_redirect_success', $this->module->l('Facility successfully created.', 'AdminKbBookingFacilitiesController'));
                Tools::redirectAdmin($this->context->link->getAdminlink('AdminKbBookingFacilities', true));
            }
        }
    }
    
    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }
        $time = time();

        $upload_img = $this->getModuleDirUrl() . $this->module->name . '/views/img/404.gif?time=' . $time;
        if ($obj->image_type == 'upload') {
            $upload_img = $obj->upload_image;
        }
        $upload_img_url = "<img id='kbslmarker' class='img img-thumbnail'  src='" . $upload_img . "' width='100px;' height='100px;'>";
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->module->l('Facilities', 'AdminKbBookingFacilitiesController'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Title', 'AdminKbBookingFacilitiesController'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'col' => '5',
                    'hint' => $this->module->l('Enter the title of Facility', 'AdminKbBookingFacilitiesController')
                ),
                array(
                    'type' => 'select',
                    'name' => 'type',
                    'label' => $this->module->l('Type', 'AdminKbBookingFacilitiesController'),
                    'required' => true,
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'room',
                                'name' => $this->module->l('Room', 'AdminKbBookingFacilitiesController')
                            ),
                            array(
                                'id' => 'rent',
                                'name' => $this->module->l('Rent', 'AdminKbBookingFacilitiesController')
                            ),
                            array(
                                'id' => 'hotel',
                                'name' => $this->module->l('Hotel', 'AdminKbBookingFacilitiesController')
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'select',
                    'name' => 'image_type',
                    'label' => $this->module->l('Image Type', 'AdminKbBookingFacilitiesController'),
                    'required' => true,
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => null,
                                'name' => $this->module->l('Select', 'AdminKbBookingFacilitiesController')
                            ),
                            array(
                                'id' => 'upload',
                                'name' => $this->module->l('Upload Image', 'AdminKbBookingFacilitiesController')
                            ),
                            array(
                                'id' => 'font',
                                'name' => $this->module->l('Font Awesome', 'AdminKbBookingFacilitiesController')
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'file',
                    'label' => $this->module->l('Upload Image', 'AdminKbBookingFacilitiesController'),
                    'name' => 'image_upload',
                    'required' => true,
                    'image' => $upload_img_url ? $upload_img_url : false,
                    'display_image' => true,
                    'hint' => $this->module->l('Upload image', 'AdminKbBookingFacilitiesController')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Font Awesome Icon', 'AdminKbBookingFacilitiesController'),
                    'name' => 'font_awesome_icon',
                    'required' => true,
                    'desc' => $this->module->l('Example: To add wifi icon, Enter fa-wifi. It should be like fa-*'),
                    'col' => '3',
                    'hint' => $this->module->l('Enter the font awesome icon', 'AdminKbBookingFacilitiesController')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Active', 'AdminKbBookingFacilitiesController'),
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
                'title' => $this->module->l('Save', 'AdminKbBookingFacilitiesController')
            )
        );

        if ($obj->image_type == 'upload') {
            $this->fields_form['input'][] = array(
                'type' => 'hidden',
                'name' => 'is_image_uploaded'
            );
        }

        $tpl = $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/velovalidation.tpl');
        return $tpl . parent::renderForm();
    }
    
    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['back_url'] = array(
            'href' => 'javascript: window.history.back();',
            'desc' => $this->module->l('Back', 'AdminKbBookingFacilitiesController'),
            'icon' => 'process-icon-back'
        );
        if (!Tools::getValue('id_facilities') && !Tools::isSubmit('add'.$this->table)) {
            $this->page_header_toolbar_btn['new_template'] = array(
                'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                'desc' => $this->module->l('Add new Facility', 'AdminKbBookingFacilitiesController'),
                'icon' => 'process-icon-new'
            );
        }
        parent::initPageHeaderToolbar();
    }
    
    public function setMedia($newTheme = false)
    {
        parent::setMedia($newTheme);
        $this->addCSS($this->getKbModuleDir().'views/css/font-awesome.min.css');
    }
}
