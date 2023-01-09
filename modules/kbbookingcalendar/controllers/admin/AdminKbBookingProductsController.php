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
 * @copyright 2019 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 *
*/

require_once dirname(__FILE__) . '/AdminKbBookingCoreController.php';
require_once _PS_MODULE_DIR_ . 'kbbookingcalendar/classes/KbBookingProduct.php';
require_once _PS_MODULE_DIR_ . 'kbbookingcalendar/classes/KbBookingFacilities.php';
require_once _PS_MODULE_DIR_ . 'kbbookingcalendar/classes/KbBookingRoomCategory.php';
require_once _PS_MODULE_DIR_ . 'kbbookingcalendar/classes/KbBookingRoomType.php';

class AdminKbBookingProductsController extends AdminKbBookingCoreController
{
    protected $ps_shop = array();
    protected $product_type_arr = array();
    protected $max_image_size = null;
    protected $error_flag = false;

    public function __construct()
    {
        $this->table = 'kb_booking_product';
        $this->className = 'KbBookingProduct';
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->identifier = 'id_booking_product';
        parent::__construct();
        $this->toolbar_title = $this->module->l('Products', 'AdminKbBookingProductsController');
        foreach (Shop::getShops(false) as $shop) {
            $this->ps_shop[$shop['id_shop']] = $shop['name'];
        }
        $this->product_type_arr['appointment'] = $this->module->l('Appointment', 'AdminKbBookingProductsController');
        $this->product_type_arr['hotel_booking'] = $this->module->l('Hotel Booking', 'AdminKbBookingProductsController');
        $this->product_type_arr['daily_rental'] = $this->module->l('Daily Rental', 'AdminKbBookingProductsController');
        $this->product_type_arr['hourly_rental'] = $this->module->l('Hourly Rental', 'AdminKbBookingProductsController');
        $this->max_image_size = (int) Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');

        $this->fields_list = array(
            'id_product' => array(
                'title' => $this->module->l('ID', 'AdminKbBookingProductsController'),
                'align' => 'center',
                'filter_key' => 'ps!id_product',
                'class' => 'fixed-width-xs'
            ),
            'image' => array(
                'title' => $this->module->l('Image', 'AdminKbBookingProductsController'),
                'align' => 'center',
                'orderby' => false,
                'filter' => false,
                'search' => false,
                'callback' => 'showCoverImage'
            ),
            'name' => array(
                'title' => $this->module->l('Name', 'AdminKbBookingProductsController'),
                'filter_key' => 'pl!name'
            ),
            'product_type' => array(
                'title' => $this->module->l('Booking Type', 'AdminKbBookingProductsController'),
                'filter_key' => 'a!product_type',
                'type' => 'select',
                'list' => $this->product_type_arr,
                'align' => 'center',
                'callback' => 'displayProductTypeArr'
            ),
//            'id_shop' => array(
//                'title' => $this->module->l('Shop', 'AdminKbBookingProductsController'),
//                'align' => 'center',
//                'type' => 'select',
//                'list' => $this->ps_shop,
//                'filter_key' => 's!id_shop',
//                'callback' => 'psShopList',
////                'search' => false
//            ),
            'active' => array(
                'title' => $this->module->l('Status', 'AdminKbBookingProductsController'),
                'align' => 'text-center',
                'active' => 'active',
                'type' => 'bool',
                'order_key' => 'active',
            ),
            'date_upd' => array(
                'title' => $this->module->l('Created On', 'AdminKbBookingProductsController'),
                'type' => 'datetime',
                'filter_key' => 'a!date_upd',
            )
        );
        $this->_select .= 'ps.id_product, pl.`name`, i.`id_image` as image,s.id_shop,a.*';
        $this->_join .= ' INNER JOIN `' . _DB_PREFIX_ . $this->table . '_shop` s on (a.id_booking_product=s.id_booking_product) ';
        $this->_join .= ' JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (a.`id_product` = pl.`id_product`) AND id_lang = ' . (int) $this->context->language->id;
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'product_shop` ps ON (a.`id_product` = ps.`id_product`) AND ps.id_shop= ' . (int) $this->context->shop->id;
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` ims ON (a.`id_product` = ims.`id_product` AND ims.`cover` = 1 AND ims.id_shop = ' . (int) $this->context->shop->id . ')';
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (ims.`id_image` = i.`id_image`)';
        //changes done by tarun to resolve the multi shop booking products list issue
//        $this->_where = ' AND s.id_shop IN ('.(int)Context::getContext()->shop->id.')';
        $this->_where = ' AND s.id_shop IN ('.(int)Context::getContext()->shop->id.') AND pl.id_shop IN ('.(int)Context::getContext()->shop->id.')';
        //changes over
        $this->addRowAction('edit');
        $this->addRowAction('delete');
    }
    
    public function renderList()
    {
        if (!Tools::getIsset('manageProductRoom')) {
            return parent::renderList();
        }
    }

    public function initContent()
    {
        if (Tools::getIsset('manageProductRoom')) {
            if (Tools::getValue('id_booking_product') && Tools::getValue('addProductRoom')) {
                $this->content .= $this->addUpdateProductRoom('add');
            } elseif (Tools::getValue('id_booking_room_facilities_map') && Tools::getValue('updateProductRoom') && Tools::getValue('id_booking_product')) {
                $this->content .= $this->addUpdateProductRoom('update', Tools::getValue('id_booking_room_facilities_map'));
            }
        }
        parent::initContent();
    }
    
    public function psShopList($echo, $tr)
    {
        unset($tr);
        return $this->ps_shop[$echo];
    }
    
    public function displayProductTypeArr($echo, $tr)
    {
        unset($tr);
        if (isset($this->product_type_arr[$echo])) {
            return $this->product_type_arr[$echo];
        }
    }
    
    public function addUpdateProductRoom($action = 'add', $id_product_room = null)
    {
        $available_room_category = KbBookingRoomCategory::getAvailableRoomCategory();
        $available_room_type = KbBookingRoomType::getAvailableRoomType();
        $available_facilities = KbBookingFacilities::getAvailableFacilitiesByType('room');

        array_unshift($available_room_category, array('id_booking_category' => null, 'name' => $this->module->l('Select Category', 'AdminKbBookingProductsController')));
        array_unshift($available_room_type, array('id_room_type' => null, 'room_name' => $this->module->l('Select Room Type', 'AdminKbBookingProductsController')));
        
        $db_value = array();
        if ($action == 'update' && !empty($id_product_room)) {
            $db_value = Db::getInstance()->getRow('SELECT a.* FROM '._DB_PREFIX_.'kb_booking_product_room_facilities_mapping a WHERE id_booking_room_facilities_map='.(int)$id_product_room);
        }
        $image_en_start_url = false;
        if (!empty($db_value)) {
            $image_upload = Tools::jsonDecode($db_value['upload_images'], true);
            if (!empty($image_upload)) {
                foreach ($image_upload as $key => $img) {
                    $image_en_start = $img['path'];
                    $image_en_start_url .= ImageManager::thumbnail($image_en_start, 'productroom_' . (int) $key . '.jpg', 50, $this->imageType, true, true);
                }
            }
        }

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->module->l('Add Room', 'AdminKbBookingProductsController'),
                    'icon' => 'icon-room'
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id_booking_product'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->module->l('Enable', 'AdminKbBookingProductsController'),
                        'name' => 'enable',
                        'values' => array(
                            array(
                                'id' => 'enable_on',
                                'value' => 1
                            ),
                            array(
                                'id' => 'enable_off',
                                'value' => 0
                            )
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->module->l('Room Category', 'AdminKbBookingProductsController'),
                        'name' => 'room_category',
                        'id' => 'product_add_room_category',
                        'required' => true,
                        'hint' => $this->module->l('Select Room Category', 'AdminKbBookingProductsController'),
                        'options' =>array(
                            'query' => $available_room_category,
                            'id' => 'id_booking_category',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->module->l('Room Type', 'AdminKbBookingProductsController'),
                        'name' => 'room_type',
                        'id' => 'product_add_room_type',
                        'required' => true,
                        'hint' => $this->module->l('Select Room Type', 'AdminKbBookingProductsController'),
                        'options' =>array(
                            'query' => $available_room_type,
                            'id' => 'id_room_type',
                            'name' => 'room_name'
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->module->l('Additional Price', 'AdminKbBookingProductsController'),
                        'name' => 'room_price',
                        'prefix' => $this->context->currency->sign,
                        'required' => true,
                        'col' => 3,
                        'desc' => $this->module->l('The additional price will be added in the actual price.', 'AdminKbBookingProductsController'),
                        'hint' => $this->module->l('Enter the additional price', 'AdminKbBookingProductsController'),
                    ),
                     array(
                        'type' => 'text',
                        'label' => $this->module->l('Quantity', 'AdminKbBookingProductsController'),
                        'name' => 'room_quantity',
                        'suffix' => $this->module->l('/day', 'AdminKbBookingProductsController'),
                        'required' => true,
                        'col' => 2,
                        'hint' => $this->module->l('Enter the Quantity', 'AdminKbBookingProductsController'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->module->l('Check-In Time', 'AdminKbBookingProductsController'),
                        'name' => 'start_time',
                        'class' => 'kb_time_from',
                        'required' => true,
                        'col' => 3,
                        'desc' => $this->module->l('This information will be displayed to the customer', 'AdminKbBookingProductsController'),
                        'hint' => $this->module->l('Enter the check-in time', 'AdminKbBookingProductsController'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->module->l('Check-Out Time', 'AdminKbBookingProductsController'),
                        'name' => 'end_time',
                        'class' => 'kb_time_to',
                        'desc' => $this->module->l('This information will be displayed to the customer', 'AdminKbBookingProductsController'),
                        'required' => true,
                        'col' => 3,
                        'hint' => $this->module->l('Enter the check-out time', 'AdminKbBookingProductsController'),
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->module->l('Upload Image(s)', 'AdminKbBookingProductsController'),
                        'desc' =>  $this->module->l('You can upload multiple images', 'AdminKbBookingProductsController'),
                        'name'=>'product_room_images',
                        'multiple' => true,
                        'display_image' => true,
                         'image' => $image_en_start_url ? $image_en_start_url : false
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->module->l('Select Facilities', 'AdminKbBookingProductsController'),
                        'name' => 'avail_facilities[]',
                        'multiple' => true,
                        'options' => array(
                            'query' =>$available_facilities,
                            'id' => 'id_facilities',
                            'name'=> 'name',
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->module->l('Save', 'AdminKbBookingProductsController'),
                    'class' => 'btn btn-default pull-right form_kb_add_product_room'
                ),
            ),
        );
        
        $languages = Language::getLanguages();
        foreach ($languages as $k => $language) {
            $languages[$k]['is_default'] = ((int) ($language['id_lang'] == $this->context->language->id));
        }
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $language = Language::getLanguages(false);
        $helper->default_form_language = $lang->id;
        $helper->languages = $languages;
        $helper->submit_action = 'kb_submit_product_room_form';
        $helper->token = Tools::getAdminTokenLite('AdminKbBookingProducts');
        $helper->currentIndex = $this->context->link->getAdminLink('AdminKbBookingProducts', true);
        
        if ($action == 'update' && !empty($id_product_room)) {
            $fields_form['form']['input'][] = array(
                'type' => 'hidden',
                'name' => 'submitupdateProductRoom',
            );
            $fields_form['form']['input'][] = array(
                'type' => 'hidden',
                'name' => 'id_booking_room_facilities_map',
            );
        }
        $helper->fields_value = array(
            'id_booking_product' => Tools::getValue('id_booking_product'),
            'room_quantity' => !empty($db_value) ? $db_value['room_quantity'] : '',
            'start_time' => !empty($db_value) ? $db_value['start_time'] : '',
            'end_time' => !empty($db_value) ? $db_value['end_time'] : '',
            'room_price' => !empty($db_value) ? $db_value['price'] : '',
            'room_category' => !empty($db_value) ? $db_value['id_room_category'] : '',
            'room_type' => !empty($db_value) ? $db_value['id_room_type'] : '',
            'enable' => !empty($db_value) ? $db_value['active'] : '',
            'avail_facilities[]' => !empty($db_value) ? explode(',', $db_value['id_facilities']) : '',
        );
        if ($action == 'update' && !empty($id_product_room)) {
            $helper->fields_value['submitupdateProductRoom'] = true;
            $helper->fields_value['id_booking_room_facilities_map'] = $id_product_room;
        }
        $form = $helper->generateForm(array($fields_form));
        $this->context->smarty->assign(array(
            'kb_product_room_form' => $form,
            'kb_room_type_field_value' =>  !empty($db_value) ? $db_value['id_room_type'] : '',
            'admin_check_room_type_url' => $this->context->link->getAdminLink('AdminKbBookingRoomType', true),
        ));
        
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/velovalidation.tpl');
    }

    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('activebooking_product_room_list')) {
            $id = Tools::getValue('id_booking_room_facilities_map');
            $rec = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'kb_booking_product_room_facilities_mapping WHERE id_booking_room_facilities_map='.(int)$id);
            $active = 0;
            if (!empty($rec)) {
                if ($rec['active'] == 1) {
                    $active = 0;
                } else {
                    $active = 1;
                }
                Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'kb_booking_product_room_facilities_mapping set active=' . (int) $active . ' WHERE id_booking_room_facilities_map=' . (int) $id);
                $this->context->cookie->__set(
                    'kb_redirect_success',
                    $this->module->l('The status has been successfully updated.', 'AdminKbBookingProductsController')
                );
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBookingProducts', true) . '&id_booking_product=' . (int) $rec['id_booking_product'] . '&updatekb_booking_product');
            } else {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBookingProducts', true));
            }
        }
        
        if (Tools::isSubmit('active'.$this->table)) {
            $id = Tools::getValue('id_booking_product');
            $object = new $this->className((int) $id);
            $active = 0;
            if ($object->active == 1) {
                $active = 0;
            } else {
                $active = 1;
            }
                $object->active = $active;
            if ($object->update()) {
                $pro = new Product($object->id_product);
                $pro->active = $active;
                $pro->update();
            }
            $this->context->cookie->__set(
                'kb_redirect_success',
                $this->module->l('The status has been successfully updated.', 'AdminKbBookingProductsController')
            );
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBookingProducts', true));
        }
        
        if (Tools::isSubmit('kb_submit_product_room_form')) {
            $this->processSubmitProductRoom();
        }
        if (Tools::isSubmit('deleteProductRoom')) {
            $id_booking_room_facilities_map = Tools::getValue('id_booking_room_facilities_map');
            $id_booking_product = Tools::getValue('id_booking_product');
            if (!empty($id_booking_room_facilities_map) && !empty($id_booking_product)) {
                $rec = Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'kb_booking_product_room_facilities_mapping WHERE id_booking_room_facilities_map='.(int)$id_booking_room_facilities_map);
                if ($rec) {
                    $this->context->cookie->__set('kb_redirect_success', $this->module->l('Room successfully deleted.', 'AdminKbBookingProductsController'));
                    Tools::redirectAdmin($this->context->link->getAdminlink('AdminKbBookingProducts', true) . '&id_booking_product=' . $id_booking_product . '&updatekb_booking_product');
                }
            }
        }
        if (Tools::isSubmit('ajax')) {
            if (Tools::getValue('action') == 'addProductImage') {
                $this->addKbProductImages();
                die;
            }
        }

        if (Tools::isSubmit('add_product_type_submit_btn')) {
            $product_type = Tools::getValue('product_type');
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBookingProducts', true) . '&addkb_booking_product&product_type=' . $product_type);
        }

        if (Tools::getValue('addDateTimeRow')) {
            $counter = Tools::getValue('counter');
             $obj = new $this->className((int)  Tools::getValue('id_booking_product'));
            $this->context->smarty->assign(
                array(
                    'product_type' => (isset($obj->product_type))?$obj->product_type:'',
                    'kbajaxcounter' => $counter,
                    'currency' => $this->context->currency,
                    'id_booking_product' => Tools::getValue('id_booking_product')
                )
            );

            echo json_encode($this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/date_time_block_product.tpl'));
            die;
        }
        if (Tools::getValue('addTimeRow')) {
            $counter = Tools::getValue('counter');
            $datetime_block = Tools::getValue('datetime_block');
            $obj = new $this->className((int)  Tools::getValue('id_booking_product'));
            $this->context->smarty->assign(
                array(
                    'kbajaxcounter' => $counter,
                    'product_type' => (isset($obj->product_type))?$obj->product_type:'',
                    'currency' => $this->context->currency,
                    'is_time_ajax' => true,
                    'datetime_block' => $datetime_block,
                    'id_booking_product' => Tools::getValue('id_booking_product')
                )
            );

            echo json_encode($this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/date_time_block_product.tpl'));
            die;
        }

        if (Tools::isSubmit('addProductFacilities')) {
            if (Tools::getValue('ajax')) {
                $selected_facilities = Tools::getValue('selected_facilities');
                $id_booking_product = Tools::getValue('id_booking_product');
                if (!empty($selected_facilities) && !empty($id_booking_product)) {
                    $selected_facilities = explode(',', $selected_facilities);
                    foreach ($selected_facilities as $id_facilities) {
                        Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'kb_booking_product_facilities_mapping WHERE id_booking_product=' . (int) $id_booking_product . ' AND id_facilities=' . (int) $id_facilities);
                        Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'kb_booking_product_facilities_mapping set id_booking_product=' . (int) $id_booking_product . ',id_facilities=' . (int) $id_facilities);
                    }
                }

                echo json_encode(array('success' => true));
                die;
            }
        }

        if (Tools::isSubmit('removeProductFacilities')) {
            if (Tools::getValue('ajax')) {
                $id_booking_product = Tools::getValue('id_booking_product');
                $id_facilities = Tools::getValue('id_facilities');
                if (!empty($id_facilities) && !empty($id_booking_product)) {
                    $rec = Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'kb_booking_product_facilities_mapping WHERE id_booking_product=' . (int) $id_booking_product . ' AND id_facilities=' . (int) $id_facilities);
                    if ($rec) {
                        echo json_encode(array('success' => true));
                    }
                }
            }
            die;
        }
        if (Tools::isSubmit('submitBulkenableSelection' . $this->table)) {
            $this->processBulkEnableSelection();
            $this->context->cookie->__set('kb_redirect_success', $this->module->l('The status has been successfully updated.', 'AdminKbBookingProductsController'));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBookingProducts', true));
        }
        
        if (Tools::isSubmit('submitBulkdisableSelection' . $this->table)) {
            $this->processBulkDisableSelection();
            $this->context->cookie->__set('kb_redirect_success', $this->module->l('The status has been successfully updated.', 'AdminKbBookingProductsController'));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBookingProducts', true));
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
                $pro_obj = new Product((int) $object->id_product);
                $pro_obj->active = (int) $status;
                $pro_obj->save();
            }
        }
        return $result;
    }
    public function processDelete()
    {
        if (Tools::isSubmit('delete'.$this->table)) {
            $id = Tools::getValue('id_booking_product');
            $object = new $this->className((int) $id);
            $pro = new Product($object->id_product);
            $pro->delete();
        }
        parent::processDelete();
    }
    
    public function displayAddBookingProductForm($obj)
    {
        $kb_product_type = '';
        if ($obj->id) {
            $kb_product_type = $obj->product_type;
        } else if (Tools::getValue('product_type')) {
            $kb_product_type = Tools::getValue('product_type');
        }
        $kb_product = '';
        $selected_cat = array();
        if ($obj->id) {
            $id_product = $obj->id_product;
            $kb_product = new Product($id_product);
            $selected_cat = Product::getProductCategoriesFull($kb_product->id, $this->context->language->id);
        }
        $categoryTreeSelection = array();
        if (!empty($kb_product)) {
            $categoryTreeSelection = $kb_product->getCategories();
        }
        $root = Category::getRootCategory();

        //Generating the tree for the first column
        $tree = new HelperTreeCategories('associated-categories-tree', 'Associated categories'); //The string in param is the ID used by the generated tree
        $tree->setTemplate('tree_associated_categories.tpl')
                ->setHeaderTemplate('tree_associated_header.tpl')
                ->setUseCheckBox(true)
                ->setRootCategory((int) $root->id)
                ->setSelectedCategories($categoryTreeSelection)
                ->setInputName('categoryBox')
                ->setUseCheckBox(true)
                ->setUseSearch(true);

        $categoryTreePresta = $tree->render();
        
        $kb_per_price_string = $this->module->l('/day', 'AdminKbBookingProductsController');
        if ($kb_product_type == 'hourly_rental') {
            $kb_per_price_string = $this->module->l('/hrs', 'AdminKbBookingProductsController');
        } elseif ($kb_product_type == 'appointment') {
            $kb_per_price_string = $this->module->l('/Appointment', 'AdminKbBookingProductsController');
        }
        
        // changes by rishabh jain
        $quantity_suffix = '';
        if ($kb_product_type == 'hourly_rental') {
            $quantity_suffix = $this->module->l('Qty', 'AdminKbBookingProductsController');
        } else if (isset($obj->period_type) && $obj->period_type == 'date_time') {
            $quantity_suffix = $this->module->l('/slot/Day', 'AdminKbBookingProductsController');
        } else {
            $quantity_suffix = $this->module->l('/Day', 'AdminKbBookingProductsController');
        }
        // changes for tax rule
        $tax_rules_groups = array();
        $tax_grp = array();
        $tax_rules_groups = TaxRulesGroup::getTaxRulesGroups(true);
        $tax_grp[0]['id_tax_rules_group'] = 0;
        $tax_grp[0]['name'] = $this->module->l('No Tax', 'AdminKbBookingProductsController');
        $i = 1;
        foreach ($tax_rules_groups as $key => $tax_data) {
            $tax_grp[$i]['id_tax_rules_group'] = $tax_data['id_tax_rules_group'];
            $tax_grp[$i]['name'] = $tax_data['name'];
            $i++;
        }
        // changes over
        $fields_form = array(
            'general' => array(
                'form' => array(
                    'input' => array(
                        array(
                            'type' => 'switch',
                            'name' => 'enable',
                            'label' => $this->module->l('Enable Product', 'AdminKbBookingProductsController'),
                            'hint' => $this->module->l('Enable/disable the product', 'AdminKbBookingProductsController'),
                            'values' => array(
                                array(
                                    'id' => 'enable_on',
                                    'value' => 1
                                ),
                                array(
                                    'id' => 'enable_off',
                                    'value' => 0
                                )
                            ),
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->module->l('Product Name', 'AdminKbBookingProductsController'),
                            'name' => 'product_name',
                            'required' => true,
                            'lang' => true,
                            'col' => 5,
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->module->l('Product Reference', 'AdminKbBookingProductsController'),
                            'name' => 'product_reference',
                            'required' => true,
                            'col' => 3,
                        ),
                        array(
                            'type' => 'textarea',
                            'label' => $this->module->l('Short Description', 'AdminKbBookingProductsController'),
                            'name' => 'short_description',
                            'autoload_rte' => true,
                            'lang' => true,
                            'desc' => $this->module->l('Enter the short description', 'AdminKbBookingProductsController'),
                        ),
                        array(
                            'type' => 'textarea',
                            'label' => $this->module->l('Description', 'AdminKbBookingProductsController'),
                            'name' => 'description',
                            'autoload_rte' => true,
                            'lang' => true,
                            'desc' => $this->module->l('Enter the description', 'AdminKbBookingProductsController'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->module->l('Condition', 'AdminKbBookingProductsController'),
                            'name' => 'condition',
                            'options' => array(
                                'query' => array(
                                    array(
                                        'id' => 'new',
                                        'name' => $this->module->l('New', 'AdminKbBookingProductsController'),
                                    ),
                                    array(
                                        'id' => 'refurbished',
                                        'name' => $this->module->l('Refurbished', 'AdminKbBookingProductsController'),
                                    ),
                                    array(
                                        'id' => 'used',
                                        'name' => $this->module->l('Used', 'AdminKbBookingProductsController'),
                                    ),
                                ),
                                'id' => 'id',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'categories_select',
                            'label' => $this->module->l('Category', 'AdminKbBookingProductsController'),
                            'desc' => $this->module->l('Select Category', 'AdminKbBookingProductsController'),
                            'name' => 'categoryBox',
                            'required' => true,
                            'category_tree' => $categoryTreePresta
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->module->l('Default Category', 'AdminKbBookingProductsController'),
                            'name' => 'id_category_default',
                            'required' => true,
                            'options' => array(
                                'query' => $selected_cat,
                                'id' => 'id_category',
                                'name' => 'name'
                            )
                        ),
                    ),
                ),
            ),
            'price' => array(
                'form' => array(
                    'input' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->module->l('Service Type', 'AdminKbBookingProductsController'),
                            'name' => 'service_type',
                            'required' => true,
                            'options' => array(
                                'query' => array(
                                    array(
                                        'id' => 'branch',
                                        'name' => $this->module->l('Branch', 'AdminKbBookingProductsController'),
                                    ),
                                    array(
                                        'id' => 'home_service',
                                        'name' => $this->module->l('Home Service', 'AdminKbBookingProductsController'),
                                    ),
                                ),
                                'id' => 'id',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'name' => 'period_type',
                            'label' => $this->module->l('Period Type', 'AdminKbBookingProductsController'),
                            'hint' => $this->module->l('Select the type of period', 'AdminKbBookingProductsController'),
                            'options' => array(
                                'query' => array(
                                    array(
                                        'id' => 'date',
                                        'name' => $this->module->l('Date', 'AdminKbBookingProductsController'),
                                    ),
                                    array(
                                        'id' => 'date_time',
                                        'name' => $this->module->l('Date & Time', 'AdminKbBookingProductsController'),
                                    ),
                                ),
                                'id' => 'id',
                                'name' => 'name'
                            ),
                            'required' => true,
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->module->l('Quantity', 'AdminKbBookingProductsController'),
                            'name' => 'quantity',
                            'suffix' => $quantity_suffix,
                            'required' => true,
                            'col' => 2,
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->module->l('Price', 'AdminKbBookingProductsController'),
                            'name' => 'price',
                            'suffix' => $kb_per_price_string,
                            'prefix' => $this->context->currency->sign,
                            'required' => true,
                            'desc' => $this->module->l('This price is only for the initial mapping. This price is not the final price of the product', 'AdminKbBookingProductsController'),
                            'col' => 3,
                        ),
                        // changes to be done for tax
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select Tax Rule'),
                            'name' => 'id_tax_rules_group',
                            'hint' => $this->module->l('Select the tax rule to be mapped with the product.', 'adminkbcategorywiseCommissioncontroller'),
                            'desc' => $this->module->l('Select the tax rule to be mapped with the product.', 'adminkbcategorywiseCommissioncontroller'),
                            'id' => version_compare(_PS_VERSION_, '1.6.0.7', '>') ? 'multiple-select-pages' : 'multiple-select-chosen-pages',
                            'class' => 'chosen',
                            'col' => 3,
                            'options' => array(
                                'query' => $tax_grp,
                                'id' => 'id_tax_rules_group',
                                'name' => 'name'
                            ),
                        ),
                        // changes over
                        array(
                            'type' => 'text',
                            'label' => $this->module->l('Min Hours', 'AdminKbBookingProductsController'),
                            'name' => 'min_hours',
                            'col' => 3,
                            'hint' => $this->module->l('Enter the minimum hours for booking', 'AdminKbBookingProductsController'),
                            'required' => true,
                            'suffix' => $this->module->l('hrs', 'AdminKbBookingProductsController'),
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->module->l('Max Hours', 'AdminKbBookingProductsController'),
                            'name' => 'max_hours',
                            'col' => 3,
                            'hint' => $this->module->l('Enter the maximum hours for booking', 'AdminKbBookingProductsController'),
                            'required' => true,
                            'suffix' => $this->module->l('hrs', 'AdminKbBookingProductsController'),
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->module->l('Min Days', 'AdminKbBookingProductsController'),
                            'name' => 'min_days',
                            'col' => 3,
                            'hint' => $this->module->l('Enter the minimum days for booking', 'AdminKbBookingProductsController'),
                            'required' => true,
                            'suffix' => $this->module->l('Days', 'AdminKbBookingProductsController'),
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->module->l('Max Days', 'AdminKbBookingProductsController'),
                            'name' => 'max_days',
                            'hint' => $this->module->l('Enter the maximum days for booking', 'AdminKbBookingProductsController'),
                            'required' => true,
                            'col' => 3,
                            'suffix' => $this->module->l('Days', 'AdminKbBookingProductsController'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->module->l('Star Rating', 'AdminKbBookingProductsController'),
                            'name' => 'star_rating',
                            'required' => true,
                            'hint' => $this->module->l('Select the star rating of product', 'AdminKbBookingProductsController'),
                            'options' => array(
                                'query' => array(
                                    array(
                                        'id' => null,
                                        'name' => $this->module->l('Select Rating', 'AdminKbBookingProductsController'),
                                    ),
                                    array(
                                        'id' => 1,
                                        'name' => $this->module->l('1 Star', 'AdminKbBookingProductsController'),
                                    ),
                                    array(
                                        'id' => 2,
                                        'name' => $this->module->l('2 Star', 'AdminKbBookingProductsController'),
                                    ),
                                    array(
                                        'id' => 3,
                                        'name' => $this->module->l('3 Star', 'AdminKbBookingProductsController'),
                                    ),
                                    array(
                                        'id' => 4,
                                        'name' => $this->module->l('4 Star', 'AdminKbBookingProductsController'),
                                    ),
                                    array(
                                        'id' => 5,
                                        'name' => $this->module->l('5 Star', 'AdminKbBookingProductsController'),
                                    ),
                                ),
                                'id' => 'id',
                                'name' => 'name'
                            ),
                        ),
                    ),
                ),
            ),
            'location' => array(
                'form' => array(
                    'input' => array(
                        array(
                            'type' => 'switch',
                            'name' => 'enable_product_map',
                            'label' => $this->module->l('Show Map', 'AdminKbBookingProductsController'),
                            'hint' => $this->module->l('Enable/disable the map', 'AdminKbBookingProductsController'),
                            'values' => array(
                                array(
                                    'id' => 'enable_product_map_on',
                                    'value' => 1
                                ),
                                array(
                                    'id' => 'enable_product_map_off',
                                    'value' => 0
                                )
                            ),
                        ),
                        array(
                            'type' => 'text',
                            'name' => 'address',
                            'label' => $this->module->l('Address', 'AdminKbBookingProductsController'),
                            'hint' => $this->module->l('Enter the address', 'AdminKbBookingProductsController'),
                            'required' => true,
                            'col' => 5,
                        ),
                        array(
                            'type' => 'text',
                            'name' => 'longitude',
                            'label' => $this->module->l('Longitude', 'AdminKbBookingProductsController'),
                            'hint' => $this->module->l('Enter the longitude', 'AdminKbBookingProductsController'),
                            'required' => true,
                            'col' => 5,
                        ),
                        array(
                            'type' => 'text',
                            'name' => 'latitude',
                            'label' => $this->module->l('Latitude', 'AdminKbBookingProductsController'),
                            'hint' => $this->module->l('Enter the latitude', 'AdminKbBookingProductsController'),
                            'required' => true,
                            'col' => 5,
                        ),
                    ),
                ),
            ),
            'dates' => array(
                'form' => array(
                    'input' => array(
                        array(
                            'type' => 'checkbox',
                            'name' => 'disable_days',
                            'class'=>'product_disable_days',
                            'label' => $this->module->l('Disable Days', 'AdminKbBookingProductsController'),
                            'hint' => $this->module->l('Enable/disable the day of week', 'AdminKbBookingProductsController'),
                            'values' => array(
                                'query' => array(
                                    array(
                                        'id' => '1',
                                        'name' => $this->module->l('Monday', 'AdminKbBookingProductsController'),
                                    ),
                                    array(
                                        'id' => '2',
                                        'name' => $this->module->l('Tuesday', 'AdminKbBookingProductsController'),
                                    ),
                                    array(
                                        'id' => '3',
                                        'name' => $this->module->l('Wednesday', 'AdminKbBookingProductsController'),
                                    ),
                                    array(
                                        'id' => '4',
                                        'name' => $this->module->l('Thursday', 'AdminKbBookingProductsController'),
                                    ),
                                    array(
                                        'id' => '5',
                                        'name' => $this->module->l('Friday', 'AdminKbBookingProductsController'),
                                    ),
                                    array(
                                        'id' => '6',
                                        'name' => $this->module->l('Saturday', 'AdminKbBookingProductsController'),
                                    ),
                                    array(
                                        'id' => '0',
                                        'name' => $this->module->l('Sunday', 'AdminKbBookingProductsController'),
                                    ),
                                ),
                                'id' => 'id',
                                'name' => 'name'
                            ),
                        ),
                        // Anulación de desarrollo de configuración de precio por día de la semana
                        /*array(
                            'type' => 'switch',
                            'name' => 'is_weekday_price_active',
                            'label' => $this->module->l('Enable weekday prices', 'AdminKbBookingProductsController'),
                            'hint' => $this->module->l('Enable weekday prices', 'AdminKbBookingProductsController'),
                            'values' => array(
                                array(
                                    'id' => 'is_weekday_price_active_on',
                                    'value' => 1
                                ),
                                array(
                                    'id' => 'is_weekday_price_active_off',
                                    'value' => 0
                                )
                            ),
                        ),*/
                    ),
                ),
            ),
        );
        
        if ($obj->id) {
            $fields_form['general']['form']['input'][] = array(
                'type' => 'hidden',
                'name' => 'id_product',
                'default_value' => $obj->id_product,
            );
        }
        $fields_form['general']['form']['input'][] = array(
            'type' => 'hidden',
            'name' => 'kb_product_type',
            'default_value' => ($obj->id) ? $obj->product_type : Tools::getValue('product_type'),
        );
        
        foreach ($fields_form as $key => &$form) {
            foreach ($form['form']['input'] as $key1 => $form1) {
                if (!(bool) $obj->id && isset($form1['name'])) {
                    if ($form1['name'] == 'disable_days') {
                        unset($form['form']['input'][$key1]);
                    }
                }
                if (Tools::getIsset('product_type') || !empty($obj->product_type)) {
                    $product_type = '';
                    if (!empty($obj->product_type)) {
                        $product_type = $obj->product_type;
                    } else {
                        $product_type = Tools::getValue('product_type');
                    }
                    if ($key == 'price') {
                        if ($product_type == 'appointment') {
                            if ($form1['name'] == 'min_hours' ||
                                    $form1['name'] == 'max_hours' ||
                                    $form1['name'] == 'min_days' ||
                                    $form1['name'] == 'star_rating' ||
                                    $form1['name'] == 'max_days') {
                                unset($form['form']['input'][$key1]);
                            }
                        } elseif ($product_type == 'daily_rental') {
                            if ($form1['name'] == 'service_type' ||
                                    $form1['name'] == 'max_hours' ||
                                    $form1['name'] == 'min_hours' ||
                                    $form1['name'] == 'star_rating') {
                                unset($form['form']['input'][$key1]);
                            }
                            
                            if ($form1['name'] == 'period_type') {
                                $form['form']['input'][$key1]['options'] = array(
                                    'query' => array(
                                        array(
                                            'id' => 'date',
                                            'name' => $this->module->l('Date'),
                                        ),
                                        array(
                                            'id' => 'date_time',
                                            'name' => $this->module->l('Date & Time'),
                                        ),
                                    ),
                                    'id' => 'id',
                                    'name' => 'name'
                                );
                            }
                        } elseif ($product_type == 'hourly_rental') {
                            if ($form1['name'] == 'service_type' ||
                                    $form1['name'] == 'min_days' ||
                                    $form1['name'] == 'max_days' ||
                                    $form1['name'] == 'star_rating') {
                                unset($form['form']['input'][$key1]);
                            }

                            if ($form1['name'] == 'period_type') {
                                $form['form']['input'][$key1]['options'] = array(
                                    'query' => array(
                                        array(
                                            'id' => 'date',
                                            'name' => $this->module->l('Date'),
                                        ),
                                    ),
                                    'id' => 'id',
                                    'name' => 'name'
                                );
                            }
                        } elseif ($product_type == 'hotel_booking') {
                            if ($form1['name'] == 'service_type' ||
                                    $form1['name'] == 'min_hours' ||
                                    $form1['name'] == 'quantity' ||
                                    $form1['name'] == 'max_hours') {
                                unset($form['form']['input'][$key1]);
                            }
                            if ($form1['name'] == 'period_type') {
                                $form['form']['input'][$key1]['options'] = array(
                                    'query' => array(
                                        array(
                                            'id' => 'date',
                                            'name' => $this->module->l('Date'),
                                        ),
                                    ),
                                    'id' => 'id',
                                    'name' => 'name'
                                );
                            }
                        }
                    }
                }
            }
        }
        
//        print_r($obj);
//        die;
        $fields_value = array(
            'enable' => $obj->active,
            'id_product' => $obj->id_product,
            'condition' => (!empty($kb_product)) ? $kb_product->condition : '',
            'price' => $obj->price,
//            'tax_rate' => (!empty($kb_product)) ? $kb_product->id_tax_rules_group : '',
            'quantity' => $obj->quantity,
            'id_category_default' => (!empty($kb_product)) ? $kb_product->id_category_default : '',
            'categoryBox' => '',
            // changes for tax rule option
            'id_tax_rules_group' => (!empty($kb_product)) ? $kb_product->id_tax_rules_group : 0,
            // changes over
//            'stock_status' => $obj->stock_status,
            'service_type' => $obj->service_type,
            'period_type' => $obj->period_type,
            'disable_days' => Tools::jsonDecode($obj->disable_days, true),
            // Anulación de desarrollo de configuración de precio por día de la semana
            // 'weekday_price_details' => Tools::jsonDecode($obj->weekday_price_details, true),
            'period_type' => $obj->period_type,
            'latitude' => $obj->latitude,
            'longitude' => $obj->longitude,
            'address' => $obj->address,
            'enable_product_map' => $obj->enable_product_map,
            // Anulación de desarrollo de configuración de precio por día de la semana
            //'is_weekday_price_active' => $obj->is_weekday_price_active,
            'min_hours' => $obj->min_hours,
            'max_hours' => $obj->max_hours,
            'max_days' => $obj->max_days,
            'min_days' => $obj->min_days,
            'star_rating' => $obj->star_rating,
            'product_reference' => (!empty($kb_product)) ? $kb_product->reference : '',
            'kb_product_type' => (!empty($obj->product_type)) ? $obj->product_type : Tools::getValue('product_type'),
//            'map_rooms' => '',
//            'map_facilities' => '',
        );
        $db_disable_days = Tools::jsonDecode($obj->disable_days, true);
        if (!empty($db_disable_days)) {
            foreach ($db_disable_days as $key => $key) {
                $fields_value[$key] = $key;
            }
        }
        /*$weekday_price_details = Tools::jsonDecode($obj->weekday_price_details, true);
        if (!empty($weekday_price_details)) {
            foreach ($weekday_price_details as $key => $price) {
                $fields_value[$key] = $price;
            }
        }*/
        foreach ($this->all_languages as $language) {
            $fields_value['product_name'][$language['id_lang']] = (!empty($kb_product)) ? $kb_product->name[$language['id_lang']] : '';
            $fields_value['short_description'][$language['id_lang']] = (!empty($kb_product)) ? $kb_product->description_short[$language['id_lang']] : '';
            $fields_value['description'][$language['id_lang']] = (!empty($kb_product)) ? $kb_product->description[$language['id_lang']] : '';
        }

        $helper = new HelperForm();
        $this->setHelperDisplay($helper);
        $helper->fields_value = $fields_value;
        $helper->submit_action = $this->submit_action;
        $helper->show_cancel_button = (isset($this->show_form_cancel_button)) ? $this->show_form_cancel_button : ($this->display == 'add' || $this->display == 'edit');
        return $helper->generateForm($fields_form);
    }

    public function renderForm()
    {
        if ((isset($this->tabAccess['edit']) && !$this->tabAccess['edit'] && Tools::getValue('id_booking_product')) || (isset($this->tabAccess['add']) && !$this->tabAccess['add'] && !Tools::getValue('id_booking_product'))) {
            $this->errors[] = $this->module->l('You do not have permission to use this form.', 'AdminKbBookingProductsController');
            return false;
        }
        if (!($obj = $this->loadObject(true))) {
            return;
        }
        $kb_form = $this->displayAddBookingProductForm($obj);
        $obj = $this->object;
        if ((bool) $obj->id) {
            $id_product = $obj->id_product;
            $count_images = Db::getInstance()->getValue(
                'SELECT COUNT(id_product)
                FROM ' . _DB_PREFIX_ . 'image
                WHERE id_product = ' . (int) $id_product
            );
            $images = Image::getImages($this->context->language->id, $id_product);
            foreach ($images as $k => $image) {
                $images[$k] = new Image($image['id_image']);
            }
            $languages = Language::getLanguages(true);
            if ($this->context->shop->getContext() == Shop::CONTEXT_SHOP) {
                $current_shop_id = (int) $this->context->shop->id;
            } else {
                $current_shop_id = 0;
            }

            $image_uploader = new HelperImageUploader('file');
            $image_uploader->setMultiple(true)
                    ->setTemplateDirectory(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin')
                    ->setTemplate('ajaximage.tpl')
                    ->setMaxFiles(4)
                    ->setUseAjax(true)->setUrl(
                        Context::getContext()->link->getAdminLink('AdminKbBookingProducts') . '&ajax=1&id_product=' . (int) $id_product. '&action=addProductImage'
                    );
            $category = new Category();
            $type = ImageType::getByNameNType('%', 'products', 'height');

            $shops = false;
            if (Shop::isFeatureActive()) {
                $shops = Shop::getShops();
            }

            if ($shops) {
                foreach ($shops as $key => $shop) {
                    if (!$obj->isAssociatedToShop($shop['id_shop'])) {
                        unset($shops[$key]);
                    }
                }
            }

            $this->context->smarty->assign(array(
                'countImages' => $count_images,
                'id_product' => (int) $id_product,
                'images' => $images,
                'iso_lang' => $languages[0]['iso_code'],
                'id_category_default' => (int) $category->id,
                'imageType' => (isset($type['name'])) ? $type['name'] : ImageType::getFormatedName('small'),
                'shops' => $shops,
                'current_shop_id' => $current_shop_id,
                'product' => new Product($id_product),
                'max_image_size' => $this->max_image_size / 1024 / 1024,
                'up_filename' => (string) Tools::getValue('virtual_product_filename_attribute'),
                'admin_product_token' => Tools::getAdminTokenLite('AdminProducts'),
                'image_uploader' => $image_uploader->render(),
                'id_lang' => $this->context->language->id,
               
                'image_tpl' => _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/product_images.tpl',
            ));
        }
        $availibleFacilities = array();
        $kb_product_type = '';
        if ($obj->id) {
            $kb_product_type = $obj->product_type;
        } else if (Tools::getValue('product_type')) {
            $kb_product_type = Tools::getValue('product_type');
        }
        $feature_type = 'hotel';
        if ($kb_product_type == 'hourly_rental' || $kb_product_type == 'daily_rental') {
            $feature_type = 'rent';
        }
        $availibleFacilities = KbBookingFacilities::getAvailableFacilitiesByType($feature_type);
        foreach ($availibleFacilities as $key => &$facility) {
            $facility['upload_image'] = '';
            $facility['upload_image_path'] = '';
        }
        $mappedFacilities = array();
        if ($obj->id) {
            $mappedFacilities = KbBookingFacilities::getFacilitiesMappedwithProduct($obj->id);
        }
//        print_r(Tools::jsonDecode($obj->date_details, true));
//        die;
        $room_listing = $this->getProductRoomListing($obj->id);
//        Tools::dieObject(Tools::jsonDecode($obj->date_details, true), true);
        $this->context->smarty->assign(
            array(
                'kb_form' => $kb_form,
                'KbcurrentToken' => $this->token,
                'id_lang' => $this->context->language->id,
                'table' => $this->table,
                'product_type' => $obj->product_type,
                'id_booking_product' => $obj->id,
                'currency' => $this->context->currency,
                'languages' => $this->_languages,
                'room_listing_table' => $room_listing,
                'mapped_facilities_product' => $mappedFacilities,
                'kb_date_data' => Tools::jsonDecode($obj->date_details, true),
                'default_language' => (int) Configuration::get('PS_LANG_DEFAULT'),
                'availibleFacilities' => $availibleFacilities,
                'datetime_tpl' => _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/date_time_block_product.tpl',
                // Anulación de desarrollo de configuración de precio por día de la semana
                // 'weekday_data' => Tools::jsonDecode($obj->weekday_price_details, true),
                'weekday_tpl' => _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/weekday_block_product.tpl',
                'rooms_tpl' => _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/room_product.tpl',
                'facilities_tpl' => _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/facilities_product.tpl',
                'form_url' => $this->context->link->getAdminLink('AdminKbBookingProducts', true),
                'facilities_admin_controller' => $this->context->link->getAdminLink('AdminKbBookingFacilities', true),
            )
        );
        $velovalidation_tpl = $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/velovalidation.tpl');
        $tpl = $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/product_add_steps_form.tpl');
//        $tpl = '';
        return $velovalidation_tpl . $tpl . parent::renderForm();
    }
    
    public function getProductRoomListing($id_booking_product)
    {
        $fields_list = array();
        $fields_list['id_booking_room_facilities_map'] = array(
            'title' => $this->module->l('ID', 'AdminKbBookingProductsController'),
            'align' => 'center td-vss',
            'search' => false,
//            'class' => '',
//            'filter_key' => 'c!id_customer',
        );
        $fields_list['room_name'] = array(
            'title' => $this->module->l('Room', 'AdminKbBookingProductsController'),
            'align' => 'center td-vss',
            'search' => false,
//            'class' => '',
//            'filter_key' => 'c!firstname',
        );
        $fields_list['category_name'] = array(
            'title' => $this->module->l('Room Category', 'AdminKbBookingProductsController'),
            'align' => 'center',
            'search' => false,
            
//            'filter_key' => 'c!lastname',
//            'class' => '',
        );
        $fields_list['price'] = array(
            'title' => $this->module->l('Additional Price', 'AdminKbBookingProductsController'),
            'align' => 'center',
            'search' => false,
//            'filter_key' => 'c!email',
//            'class' => '',
        );
        $fields_list['active'] = array(
            'title' => $this->module->l('Status', 'AdminKbBookingProductsController'),
            'align' => 'center',
            'search' => false,
            'active' => 'active',
                'type' => 'bool',
            'order_key' => 'active',
//            'filter_key' => 'c!email',
//            'class' => '',
        );
        $id_shop = $this->context->shop->id;
        $id_lang = $this->context->language->id;
        
        $_list = Db::getInstance()->executeS(
            'SELECT a.*,rl.room_name,cl.name as category_name FROM '._DB_PREFIX_.'kb_booking_product_room_facilities_mapping a INNER JOIN '._DB_PREFIX_.'kb_booking_room_type_lang rl on (rl.id_room_type=a.id_room_type AND rl.id_lang='.(int)$id_lang.' AND rl.id_shop='.(int)$id_shop.') INNER JOIN '._DB_PREFIX_.'kb_booking_category_lang cl on (cl.id_booking_category=a.id_room_category AND cl.id_lang='.(int)$id_lang.' AND cl.id_shop='.(int)$id_shop.') where a.id_booking_product = ' . (int) $id_booking_product
        );
        
        $list_id = 'booking_product_room_list';
        $helper = new HelperList();
        $helper->table = $list_id;
        $helper->row_hover = false;
        $helper->title = $this->l('Manage Rooms');
        $helper->identifier = 'id_booking_room_facilities_map';
        $helper->table_id = 'id_booking_room_facilities_map';
        $helper->name_controller = 'AdminKbBookingProducts';
        $helper->list_class = 'form_bookingproductroom';
        $helper->list_id = 'bookingproductroom';
//        $helper->actions = array('mapping');
        $helper->no_link = true;
        $helper->bulk_actions = true;
        $helper->currentIndex = AdminKbBookingProductsController::$currentIndex;
        $helper->token = Tools::getAdminTokenLite('AdminKbBookingProducts');
        $helper->shopLinkType = false;
        $helper->actions = array('editRoom', 'deleteRoom');
         $filter_str = '';
        $start = 0;
        $limit = 50;
//
//        if ($helper->_default_pagination) {
//            $limit = $helper->_default_pagination;
//        }
//        if (Tools::getIsset('page') && (int) Tools::getValue('page') > 1) {
//            $page_number = (int) Tools::getValue('page');
//            $start = (($page_number - 1) * $limit);
//        }
//        $filter_str = '';
//        if (Tools::isSubmit('submitFilter' . $list_id) && Tools::getValue('submitFilter' . $list_id) == 1) {
//            $filter_str = $this->getCustomerFilters();
//        } elseif (Tools::isSubmit('submitReset' . $list_id)) {
//            $filter_str = '';
//            $context = Context::getContext();
//            foreach ($fields_list as $key => $param) {
//                $temp = $param;
//                unset($temp);
//                $value_key = $list_id . 'Filter_c!' . $key;
//                $context->cookie->$value_key = null;
//                unset($context->cookie->$value_key);
//                unset($_POST[$value_key]);
//            }
//
//            if (isset($context->cookie->{'submitFilter' . $list_id})) {
//                unset($context->cookie->{'submitFilter' . $list_id});
//            }
//            if (isset($context->cookie->{$list_id . 'Orderby'})) {
//                unset($context->cookie->{$list_id . 'Orderby'});
//            }
//            if (isset($context->cookie->{$list_id . 'Orderway'})) {
//                unset($context->cookie->{$list_id . 'Orderway'});
//            }
//        }

        $helper->listTotal = $this->fetchProductRoom(
            true,
            $filter_str
        );
        $_list = $this->fetchProductRoom(
            false,
            $filter_str,
            $start,
            $limit
        );
        return $helper->generateList($_list, $fields_list);
    }
    
    public function fetchProductRoom(
        $return_count = false,
        $filter_str = '',
        $start = null,
        $limit = null
    ) {
        $id_shop = $this->context->shop->id;
        $id_lang = $this->context->language->id;
        $sql ='SELECT {{COLUMN}} FROM '._DB_PREFIX_.'kb_booking_product_room_facilities_mapping a INNER JOIN '._DB_PREFIX_.'kb_booking_room_type_lang rl on (rl.id_room_type=a.id_room_type AND rl.id_lang='.(int)$id_lang.' AND rl.id_shop='.(int)$id_shop.') INNER JOIN '._DB_PREFIX_.'kb_booking_category_lang cl on (cl.id_booking_category=a.id_room_category AND cl.id_lang='.(int)$id_lang.' AND cl.id_shop='.(int)$id_shop.') WHERE a.id_booking_product='.(int)Tools::getValue('id_booking_product');

        if ($return_count) {
            $sql = Tools::str_replace_once('{{COLUMN}}', 'COUNT(*)', $sql);
            return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        } else {
            $columns = 'a.*,rl.room_name,cl.name as category_name';
            $sql = Tools::str_replace_once('{{COLUMN}}', $columns, $sql);
            if ($start !== null && $limit !== null) {
                $sql .= ' LIMIT ' . (int) $start . ', ' . (int) $limit;
            }
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
    }
    
    /**
     * Display edit action link
     */
    public function displayeditRoomLink($token, $id, $name = null)
    {
        $tpl = $this->createTemplate('helpers/list/list_action_edit.tpl');
        if (!array_key_exists('editRoom', self::$cache_lang)) {
            self::$cache_lang['editRoom'] = $this->module->l('Edit', 'AdminKbBookingProductsController');
        }

        $href = self::$currentIndex.'&id_booking_room_facilities_map='.$id.'&manageProductRoom&id_booking_product='.Tools::getValue('id_booking_product').'&updateProductRoom=true&token='.($token != null ? $token : $this->token);

        $tpl->assign(array(
            'href' => $href,
            'action' => self::$cache_lang['editRoom'],
            'id' => $id
        ));

        return $tpl->fetch();
    }
    
    /**
     * Display edit action link
     */
    public function displaydeleteRoomLink($token, $id, $name = null)
    {
        $tpl = $this->createTemplate('helpers/list/list_action_delete.tpl');
        if (!array_key_exists('deleteRoom', self::$cache_lang)) {
            self::$cache_lang['deleteRoom'] = $this->module->l('Delete', 'AdminKbBookingProductsController');
        }

        $href = self::$currentIndex.'&id_booking_room_facilities_map='.$id.'&deleteProductRoom&id_booking_product='.Tools::getValue('id_booking_product').'&token='.($token != null ? $token : $this->token);

        $tpl->assign(array(
            'href' => $href,
            'confirm' => $this->module->l('Are You sure You want to delete the selected Hotel Room?', 'AdminKbBookingProductsController'),
            'action' => self::$cache_lang['deleteRoom'],
            'id' => $id
        ));

        return $tpl->fetch();
    }
    
    public function processSubmitProductRoom()
    {
        $id_booking_product = Tools::getValue('id_booking_product');
        $enable = Tools::getValue('enable');
        $room_category = Tools::getValue('room_category');
        $room_type = Tools::getValue('room_type');
        $room_price = trim(Tools::getValue('room_price'));
        $start_time = trim(Tools::getValue('start_time'));
        $room_quantity = trim(Tools::getValue('room_quantity'));
        $end_time = trim(Tools::getValue('end_time'));
        $avail_facilities = '';
        if (!empty(Tools::getValue('avail_facilities'))) {
            $avail_facilities = implode(',', Tools::getValue('avail_facilities'));
        }
        $image_upload = array();
        if (!empty($id_booking_product)) {
            if (!empty($_FILES) && isset($_FILES['product_room_images'])) {
                foreach ($_FILES['product_room_images']["tmp_name"] as $key => $tmp_name) {
                    if ($_FILES["product_room_images"]['error'][$key] == 0 && $_FILES["product_room_images"]["name"][$key] != '' && $_FILES["product_room_images"]["size"][$key] > 0) {
                        $file_extension = pathinfo($_FILES["product_room_images"]["name"][$key], PATHINFO_EXTENSION);
                        $time = time();
                        $path = _PS_MODULE_DIR_ . $this->module->name . '/views/img/' . 'room_img_'.$key.'_' . $time . '.' . $file_extension;
                        $upload = move_uploaded_file($_FILES["product_room_images"]["tmp_name"][$key], $path);
                        chmod($path, 0777);
                        if ($upload) {
                            $image_upload[]  = array(
                                'path' => $path,
                                'link' => $this->getModuleDirUrl() . $this->module->name . '/views/img/' . 'room_img_' .$key.'_'. $time . '.' . $file_extension,
                            );
                        }
                    }
                }
            }
            if (Tools::getValue('submitupdateProductRoom') && Tools::getValue('id_booking_room_facilities_map')) {
                if (!empty($image_upload)) {
                    $image_upload = Tools::jsonEncode($image_upload);
                    Db::getInstance()->execute(
                        'UPDATE ' . _DB_PREFIX_ . 'kb_booking_product_room_facilities_mapping'
                        . ' set upload_images="' . pSQL($image_upload)
                        . '" WHERE id_booking_room_facilities_map=' . (int) Tools::getValue('id_booking_room_facilities_map')
                    );
                }
                $rec = Db::getInstance()->execute(
                    'UPDATE ' . _DB_PREFIX_ . 'kb_booking_product_room_facilities_mapping '
                    . 'set id_booking_product=' . (int) $id_booking_product . ',id_room_type=' . (int) $room_type
                    . ',id_room_category=' . (int) $room_category . ',price="' . pSQL($room_price)
                    . '",start_time="' . pSQL($start_time) . '",end_time="' .pSQL($end_time) . '", room_quantity='.(int)$room_quantity
                    . ',id_facilities="' . pSQL($avail_facilities) . '",active=' . (int) $enable
                    . ',date_upd=now() WHERE id_booking_room_facilities_map=' . (int) Tools::getValue('id_booking_room_facilities_map')
                );
                if ($rec) {
                     $this->context->cookie->__set('kb_redirect_success', $this->module->l('Room successfully udpated.', 'AdminKbBookingProductsController'));
                    Tools::redirectAdmin($this->context->link->getAdminlink('AdminKbBookingProducts', true) . '&id_booking_product=' . $id_booking_product . '&updatekb_booking_product');
                }
            } else {
                $image_upload = Tools::jsonEncode($image_upload);
                $rec = Db::getInstance()->execute(
                    'INSERT INTO ' . _DB_PREFIX_ . 'kb_booking_product_room_facilities_mapping '
                    . 'set id_booking_product=' . (int) $id_booking_product . ',id_room_type=' . (int) $room_type
                    . ',id_room_category=' . (int) $room_category . ',price="' . pSQL($room_price)
                    . '",start_time="' . pSQL($start_time) . '",end_time="' .pSQL($end_time) . '",room_quantity='.(int)$room_quantity.',upload_images="' . pSQL($image_upload) . '"'
                    . ',id_facilities="' . pSQL($avail_facilities) . '",active=' . (int) $enable
                    . ',date_add=now(),date_upd=now()'
                );
                if ($rec) {
                    $this->context->cookie->__set('kb_redirect_success', $this->module->l('Room successfully added.', 'AdminKbBookingProductsController'));
                    Tools::redirectAdmin($this->context->link->getAdminlink('AdminKbBookingProducts', true) . '&id_booking_product=' . $id_booking_product . '&updatekb_booking_product');
                }
            }
        }
    }

    
    
    public function addKbProductImages()
    {
        $product = new Product((int) Tools::getValue('id_product'));
        $legends = Tools::getValue('legend');

        if (!is_array($legends)) {
            $legends = (array) $legends;
        }

        if (!Validate::isLoadedObject($product)) {
            $files = array();
            $files[0]['error'] = $this->module->l('Cannot add image because product creation failed.', 'AdminKbBookingProductsController');
        }

        $image_uploader = new HelperImageUploader('file');
        $image_uploader->setAcceptTypes(array('jpeg', 'gif', 'png', 'jpg'))->setMaxSize($this->max_image_size)->setUseAjax(true);
        $files = $image_uploader->process();

        foreach ($files as &$file) {
            $image = new Image();
            $image->id_product = (int) ($product->id);
            $image->position = Image::getHighestPosition($product->id) + 1;

            foreach ($legends as $key => $legend) {
                if (!empty($legend)) {
                    $image->legend[(int) $key] = $legend;
                }
            }

            if (!Image::getCover($image->id_product)) {
                $image->cover = 1;
            } else {
                $image->cover = 0;
            }

            if (($validate = $image->validateFieldsLang(false, true)) !== true) {
                $file['error'] = $this->module->l($validate);
            }

            if (isset($file['error']) && (!is_numeric($file['error']) || $file['error'] != 0)) {
                continue;
            }

            if (!$image->add()) {
                $file['error'] = $this->module->l('Error while creating additional image', 'AdminKbBookingProductsController');
            } else {
                if (!$new_path = $image->getPathForCreation()) {
                    $file['error'] = $this->module->l('An error occurred during new folder creation', 'AdminKbBookingProductsController');
                    continue;
                }

                $error = 0;

                if (!ImageManager::resize($file['save_path'], $new_path . '.' . $image->image_format, null, null, 'jpg', false, $error)) {
                    switch ($error) {
                        case ImageManager::ERROR_FILE_NOT_EXIST:
                            $file['error'] = $this->module->l('An error occurred while copying image, the file does not exist anymore.', 'AdminKbBookingProductsController');
                            break;
                        case ImageManager::ERROR_FILE_WIDTH:
                            $file['error'] = $this->module->l('An error occurred while copying image, the file width is 0px.', 'AdminKbBookingProductsController');
                            break;
                        case ImageManager::ERROR_MEMORY_LIMIT:
                            $file['error'] = $this->module->l('An error occurred while copying image, check your memory limit.', 'AdminKbBookingProductsController');
                            break;
                        default:
                            $file['error'] = $this->module->l('An error occurred while copying image.', 'AdminKbBookingProductsController');
                            break;
                    }
                    continue;
                } else {
                    $imagesTypes = ImageType::getImagesTypes('products');
                    $generate_hight_dpi_images = (bool) Configuration::get('PS_HIGHT_DPI');

                    foreach ($imagesTypes as $imageType) {
                        if (!ImageManager::resize($file['save_path'], $new_path . '-' . Tools::stripslashes($imageType['name']) . '.' . $image->image_format, $imageType['width'], $imageType['height'], $image->image_format)) {
                            $file['error'] = $this->module->l('An error occurred while copying image:', 'AdminKbBookingProductsController') . ' ' . Tools::stripslashes($imageType['name']);
                            continue;
                        }

                        if ($generate_hight_dpi_images) {
                            if (!ImageManager::resize($file['save_path'], $new_path . '-' . Tools::stripslashes($imageType['name']) . '2x.' . $image->image_format, (int) $imageType['width'] * 2, (int) $imageType['height'] * 2, $image->image_format)) {
                                $file['error'] = $this->module->l('An error occurred while copying image:', 'AdminKbBookingProductsController') . ' ' . Tools::stripslashes($imageType['name']);
                                continue;
                            }
                        }
                    }
                }

                unlink($file['save_path']);
                //Necesary to prevent hacking
                unset($file['save_path']);
                Hook::exec('actionWatermark', array('id_image' => $image->id, 'id_product' => $product->id));

                if (!$image->update()) {
                    $file['error'] = $this->module->l('Error while updating status', 'AdminKbBookingProductsController');
                    continue;
                }

                // Associate image to shop from context
                $shops = Shop::getContextListShopID();
                $image->associateTo($shops);
                $json_shops = array();

                foreach ($shops as $id_shop) {
                    $json_shops[$id_shop] = true;
                }

                $file['status'] = 'ok';
                $file['id'] = $image->id;
                $file['position'] = $image->position;
                $file['cover'] = $image->cover;
                $file['legend'] = $image->legend;
                $file['path'] = $image->getExistingImgPath();
                $file['shops'] = $json_shops;

                @unlink(_PS_TMP_IMG_DIR_ . 'product_' . (int) $product->id . '.jpg');
                @unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $product->id . '_' . $this->context->shop->id . '.jpg');
            }
        }

        die(Tools::jsonEncode(array($image_uploader->getName() => $files)));
    }

    public function getModuleTranslationByLanguage($module, $string, $source, $language, $sprintf = null, $js = false)
    {
        $modules = array();
        $langadm = array();
        $translations_merged = array();
        $name = $module instanceof Module ? $module->name : $module;
        
        if (!isset($translations_merged[$name]) && isset(Context::getContext()->language)) {
            $files_by_priority = array(
                _PS_MODULE_DIR_ . $name . '/translations/' . $language . '.php'
            );
            foreach ($files_by_priority as $file) {
                if (file_exists($file)) {
                    include($file);
                    /* No need to define $_MODULE as it is defined in the above included file. */
                    $modules = $_MODULE;
                    $translations_merged[$name] = true;
                }
            }
        }

        $string = preg_replace("/\\\*'/", "\'", $string);
        $key = md5($string);
        if ($modules == null) {
            if ($sprintf !== null) {
                $string = Translate::checkAndReplaceArgs($string, $sprintf);
            }

            return str_replace('"', '&quot;', $string);
        }
        $current_key = Tools::strtolower('<{' . $name . '}' . _THEME_NAME_ . '>' . $source) . '_' . $key;
        $default_key = Tools::strtolower('<{' . $name . '}prestashop>' . $source) . '_' . $key;
        if ('controller' == Tools::substr($source, -10, 10)) {
            $file = Tools::substr($source, 0, -10);
            $current_key_file = Tools::strtolower('<{' . $name . '}' . _THEME_NAME_ . '>' . $file) . '_' . $key;
            $default_key_file = Tools::strtolower('<{' . $name . '}prestashop>' . $file) . '_' . $key;
        }

        if (isset($current_key_file) && !empty($modules[$current_key_file])) {
            $ret = Tools::stripslashes($modules[$current_key_file]);
        } elseif (isset($default_key_file) && !empty($modules[$default_key_file])) {
            $ret = Tools::stripslashes($modules[$default_key_file]);
        } elseif (!empty($modules[$current_key])) {
            $ret = Tools::stripslashes($modules[$current_key]);
        } elseif (!empty($modules[$default_key])) {
            $ret = Tools::stripslashes($modules[$default_key]);
            // if translation was not found in module, look for it in AdminController or Helpers
        } elseif (!empty($langadm)) {
            $ret = Tools::stripslashes(Translate::getGenericAdminTranslation($string, $key, $langadm));
        } else {
            $ret = Tools::stripslashes($string);
        }

        if ($sprintf !== null) {
            $ret = Translate::checkAndReplaceArgs($ret, $sprintf);
        }

        if ($js) {
            $ret = addslashes($ret);
        } else {
            $ret = htmlspecialchars($ret, ENT_COMPAT, 'UTF-8');
        }
        return $ret;
    }
    public function processAdd()
    {
        if (Tools::isSubmit('submitAddkb_booking_product')) {
            $posted_data = array();
            if (Tools::getIsset('submitandstay_kb_product_btn')) {
                $posted_data['clicked_btn'] = 'savenstay';
            } else {
                $posted_data['clicked_btn'] = 'save';
            }
            $posted_data['kb_product_type'] = Tools::getValue('kb_product_type');
            $posted_data['enable'] = Tools::getValue('enable');
//            $posted_data['stock_status'] = Tools::getValue('stock_status');
            $posted_data['service_type'] = Tools::getValue('service_type');
            $posted_data['period_type'] = Tools::getValue('period_type');
            $posted_data['quantity'] = trim(Tools::getValue('quantity'));
            $posted_data['price'] = trim(Tools::getValue('price'));
//            $posted_data['start_date'] = trim(Tools::getValue('start_date'));
//            $posted_data['end_date'] = trim(Tools::getValue('end_date'));
            $posted_data['enable_product_map'] = Tools::getValue('enable_product_map');
            // Anulación de desarrollo de configuración de precio por día de la semana
            // $posted_data['is_weekday_price_active'] = Tools::getValue('is_weekday_price_active');
            $posted_data['address'] = trim(Tools::getValue('address'));
            $posted_data['longitude'] = trim(Tools::getValue('longitude'));
            $posted_data['latitude'] = trim(Tools::getValue('latitude'));
            $posted_data['product_reference'] = trim(Tools::getValue('product_reference'));
            $posted_data['condition'] = Tools::getValue('condition');
            $posted_data['id_tax_rules_group'] = Tools::getValue('id_tax_rules_group', 0);
//            $posted_data['tax_rate'] = Tools::getValue('tax_rate');
//            $posted_data['stock_status'] = Tools::getValue('stock_status');
            $posted_data['categoryBox'] = Tools::getValue('categoryBox');
            $posted_data['id_category_default'] = Tools::getValue('id_category_default');
            $posted_data['star_rating'] = Tools::getValue('star_rating');
            $posted_data['max_days'] = trim(Tools::getValue('max_days'));
            $posted_data['min_days'] = trim(Tools::getValue('min_days'));
            $posted_data['min_hours'] = trim(Tools::getValue('min_hours'));
            $posted_data['max_hours'] = trim(Tools::getValue('max_hours'));
            foreach ($this->all_languages as $lang) {
                $posted_data['product_name'][$lang['id_lang']] = trim(Tools::getValue('product_name_' . $lang['id_lang']));
                $posted_data['short_description'][$lang['id_lang']] = trim(Tools::getValue('short_description_' . $lang['id_lang']));
                $posted_data['description'][$lang['id_lang']] = trim(Tools::getValue('description_' . $lang['id_lang']));
            }
//            Tools::dieObject(Tools::getAllValues(), true);
            if (empty(Tools::getValue('kb_product_type'))) {
                $this->context->cookie->__set('kb_redirect_error', $this->module->l('Something went wrong while adding the product', 'AdminKbBookingProductsController'));
                Tools::redirectAdmin($this->context->link->getAdminlink('AdminKbBookingProducts', true));
            }
            if (!$this->addUpdateProduct($posted_data)) {
                $this->context->cookie->__set(
                    'kb_redirect_error',
                    $this->module->l('Something went wrong while adding the product. Please try again.', 'AdminKbBookingProductsController')
                );
                $this->redirect_after = self::$currentIndex . '&token=' . $this->token;
            } else {
                $this->object = new KbBookingProduct();
                $this->object->id_product = $posted_data['generated_product_id'];
                $this->object->product_type = $posted_data['kb_product_type'];
                $this->object->active = $posted_data['enable'];
//                $this->object->stock_status = $posted_data['stock_status'];
                $this->object->period_type = $posted_data['period_type'];
                $this->object->quantity = $posted_data['quantity'];
                $this->object->price = $posted_data['price'];
//                $this->object->start_date = $posted_data['start_date'];
//                $this->object->end_date = $posted_data['end_date'];
                $this->object->enable_product_map = $posted_data['enable_product_map'];
                // Anulación de desarrollo de configuración de precio por día de la semana
                // $this->object->is_weekday_price_active = $posted_data['is_weekday_price_active'];
                $this->object->address = $posted_data['address'];
                $this->object->longitude = $posted_data['longitude'];
                $this->object->latitude = $posted_data['latitude'];
//                $this->object->min_days = $posted_data['min_days'];
//                $this->object->max_days = $posted_data['max_days'];
                if ($posted_data['kb_product_type'] == 'hotel_booking' ||
                    $posted_data['kb_product_type'] == 'daily_rental') {
                    $this->object->star_rating = $posted_data['star_rating'];
                    $this->object->min_days = $posted_data['min_days'];
                    $this->object->max_days = $posted_data['max_days'];
                } elseif ($posted_data['kb_product_type'] == 'hourly_rental') {
                    $this->object->min_hours = $posted_data['min_hours'];
                    $this->object->max_hours = $posted_data['max_hours'];
                } else {
                    $this->object->service_type = $posted_data['service_type'];
                }
//                if ($posted_data['kb_product_type'] == 'hotel_booking') {
//                    $this->object->star_rating = $posted_data['star_rating'];
//                } else {
//                    $this->object->service_type = $posted_data['service_type'];
//                }
                if ($this->object->add()) {
                    $this->context->cookie->__set('kb_redirect_success', $this->module->l('Booking product added successfully.', 'AdminKbBookingProductsController'));
                    if ($posted_data['clicked_btn'] == 'savenstay') {
                        $this->redirect_after = self::$currentIndex . '&' . $this->identifier . '=' . (int) $this->object->id
                                . '&update' . $this->table . '&type=' . $posted_data['kb_product_type'] . '&token=' . $this->token;
                    } else {
                        $this->redirect_after = self::$currentIndex . '&token=' . $this->token;
                    }
                } else {
                    $this->context->cookie->__set('kb_redirect_error', $this->module->l('Something went wrong while adding the Booking product. Please try again.', 'AdminKbBookingProductsController'));
                }
            }
        }
    }

    public function processUpdate()
    {
        if (Tools::isSubmit('submitAddproductAndStay') == 'update_legends' && Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product')))) {
            $id_image = (int)Tools::getValue('id_caption');
            $language_ids = Language::getIDs(false);
            $posted_data = Tools::getAllValues();
            foreach ($posted_data as $key => $val) {
                if (preg_match('/^legend_([0-9]+)/i', $key, $match)) {
                    foreach ($language_ids as $id_lang) {
                        if ($val && $id_lang == $match[1]) {
                            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'image_lang SET legend = "'.pSQL($val).'" WHERE '.($id_image ? 'id_image = '.(int)$id_image : 'EXISTS (SELECT 1 FROM '._DB_PREFIX_.'image WHERE '._DB_PREFIX_.'image.id_image = '._DB_PREFIX_.'image_lang.id_image AND id_product = '.(int)$product->id.')').' AND id_lang = '.(int)$id_lang);
                        }
                    }
                }
            }
            $this->context->cookie->__set(
                'kb_redirect_success',
                $this->module->l('Caption is successfully updated', 'AdminKbBookingProductsController')
            );
            $this->redirect_after = self::$currentIndex . '&' . $this->identifier . '=' . (int) $this->object->id
                    . '&update' . $this->table . '&type=' . Tools::getValue('kb_product_type') . '&token=' . $this->token;
        }
        
        if (Tools::isSubmit('submitAddkb_booking_product')) {
            $posted_data = array();
            if (Tools::getIsset('submitandstay_kb_product_btn')) {
                $posted_data['clicked_btn'] = 'savenstay';
            } else {
                $posted_data['clicked_btn'] = 'save';
            }
            $posted_data['kb_product_type'] = Tools::getValue('kb_product_type');
            $posted_data['id_product'] = Tools::getValue('id_product');
            $posted_data['enable'] = Tools::getValue('enable');
//            $posted_data['stock_status'] = Tools::getValue('stock_status');
            $posted_data['service_type'] = Tools::getValue('service_type');
            $posted_data['period_type'] = Tools::getValue('period_type');
            $posted_data['quantity'] = trim(Tools::getValue('quantity'));
            $posted_data['price'] = trim(Tools::getValue('price'));
            $disable_days = array();
            for ($i = 0; $i <= 6; $i++) {
                if (Tools::getIsset('disable_days_' . $i)) {
                    $disable_days['disable_days_'.$i] = 1;
                }
            }
            $posted_data['enable_product_map'] = Tools::getValue('enable_product_map');
            // Anulación de desarrollo de configuración de precio por día de la semana
            // $posted_data['is_weekday_price_active'] = Tools::getValue('is_weekday_price_active');
            $posted_data['address'] = trim(Tools::getValue('address'));
            $posted_data['longitude'] = trim(Tools::getValue('longitude'));
            $posted_data['latitude'] = trim(Tools::getValue('latitude'));
            $posted_data['product_reference'] = trim(Tools::getValue('product_reference'));
            $posted_data['condition'] = Tools::getValue('condition');
            $posted_data['id_tax_rules_group'] = Tools::getValue('id_tax_rules_group', 0);
//            $posted_data['stock_status'] = Tools::getValue('stock_status');
            $posted_data['categoryBox'] = Tools::getValue('categoryBox');
            $posted_data['id_category_default'] = Tools::getValue('id_category_default');
            $posted_data['star_rating'] = Tools::getValue('star_rating');
            $posted_data['max_days'] = trim(Tools::getValue('max_days'));
            $posted_data['min_days'] = trim(Tools::getValue('min_days'));
            $posted_data['min_hours'] = trim(Tools::getValue('min_hours'));
            $posted_data['max_hours'] = trim(Tools::getValue('max_hours'));
            $posted_data['disable_days'] = Tools::jsonEncode($disable_days);
            // Anulación de desarrollo de configuración de precio por día de la semana
            /*$weekday_price_details = array();
            for ($i = 0; $i <= 6; $i++) {
                if ($price = Tools::getValue('weekday_price_details_' . $i)) {
                    $weekday_price_details['weekday_price_details_'.$i] = $price;
                }
            }
            $posted_data['weekday_price_details'] = json_encode($weekday_price_details);*/
            $date_data = array();
            $kb_date_from = Tools::getValue('kb_date_from');
            $kb_date_to = Tools::getValue('kb_date_to');
            if (Tools::getValue('period_type') == 'date_time') {
                $kb_time_from = Tools::getValue('kb_time_from');
                $kb_time_to = Tools::getValue('kb_time_to');
                $kb_time_price = Tools::getValue('kb_time_price');
                if (!empty($kb_date_from)) {
                    foreach ($kb_date_from as $key => $from_date) {
                        $data = array();
                        $data['from_date'] = trim($from_date);
                        $data['to_date'] = (isset($kb_date_to) && !empty($kb_date_to[$key])) ? trim($kb_date_to[$key]) : '';
                        if (!empty($kb_time_from)) {
                            foreach ($kb_time_from[$key] as $key1 => $time_from) {
                                $data['time'][] = array(
                                    'from_time' => trim($time_from),
                                    'to_time' => trim($kb_time_to[$key][$key1]),
                                    'price' => trim($kb_time_price[$key][$key1]),
                                );
                            }
                        }
                        $date_data[] = $data;
                    }
                }
            } else {
                if (!empty($kb_date_from)) {
                    $kb_time_price = Tools::getValue('kb_time_price');
                    foreach ($kb_date_from as $key => $from_date) {
                         $date_data[] = array(
                            'from_date' => trim($from_date),
                            'to_date' => (isset($kb_date_to) && !empty($kb_date_to[$key])) ? trim($kb_date_to[$key]) : '',
                            'price' => (isset($kb_time_price[$key][1])) ? trim($kb_time_price[$key][1]):'',
                            );
                    }
                }
            }
            $date_data = Tools::jsonEncode($date_data);
            foreach ($this->all_languages as $lang) {
                $posted_data['product_name'][$lang['id_lang']] = trim(Tools::getValue('product_name_' . $lang['id_lang']));
                $posted_data['short_description'][$lang['id_lang']] = trim(Tools::getValue('short_description_' . $lang['id_lang']));
                $posted_data['description'][$lang['id_lang']] = trim(Tools::getValue('description_' . $lang['id_lang']));
            }
            if (empty(Tools::getValue('kb_product_type'))) {
                $this->context->cookie->__set('kb_redirect_error', $this->module->l('Something went wrong while updating the product', 'AdminKbBookingProductsController'));
                Tools::redirectAdmin($this->context->link->getAdminlink('AdminKbBookingProducts', true));
            }

            if (!$this->addUpdateProduct($posted_data, 'update')) {
                $this->context->cookie->__set(
                    'kb_redirect_error',
                    $this->module->l('Something went wrong while adding the product. Please try again.', 'AdminKbBookingProductsController')
                );
                $this->redirect_after = self::$currentIndex . '&token=' . $this->token;
            } else {
                $this->object = new KbBookingProduct((int) $this->object->id);
                $this->object->id_product = $posted_data['generated_product_id'];
                $this->object->product_type = $posted_data['kb_product_type'];
                $this->object->active = $posted_data['enable'];
                $this->object->period_type = $posted_data['period_type'];
                $this->object->quantity = $posted_data['quantity'];
                $this->object->price = $posted_data['price'];
                $this->object->date_details  = $date_data;
                $this->object->disable_days = $posted_data['disable_days'];
                // Anulación de desarrollo de configuración de precio por día de la semana
                // $this->object->weekday_price_details = $posted_data['weekday_price_details'];
//                $this->object->end_date = $posted_data['end_date'];
                $this->object->enable_product_map = $posted_data['enable_product_map'];
                // $this->object->is_weekday_price_active = $posted_data['is_weekday_price_active'];
                $this->object->address = $posted_data['address'];
                $this->object->longitude = $posted_data['longitude'];
                $this->object->latitude = $posted_data['latitude'];
                if ($posted_data['kb_product_type'] == 'hotel_booking'
                    || $posted_data['kb_product_type'] == 'daily_rental') {
                    $this->object->star_rating = $posted_data['star_rating'];
                    $this->object->min_days = $posted_data['min_days'];
                    $this->object->max_days = $posted_data['max_days'];
                } elseif ($posted_data['kb_product_type'] == 'hourly_rental') {
                    $this->object->min_hours = $posted_data['min_hours'];
                    $this->object->max_hours = $posted_data['max_hours'];
                } else {
                    $this->object->service_type = $posted_data['service_type'];
                }
//                Tools::dieObject($this->object, true);
                if ($this->object->update()) {
                    $this->context->cookie->__set('kb_redirect_success', $this->module->l('Booking product updated successfully.', 'AdminKbBookingProductsController'));
                    if ($posted_data['clicked_btn'] == 'savenstay') {
                        $this->redirect_after = self::$currentIndex . '&' . $this->identifier . '=' . (int) $this->object->id
                                . '&update' . $this->table . '&type=' . $posted_data['kb_product_type'] . '&token=' . $this->token;
                    } else {
                        $this->redirect_after = self::$currentIndex . '&token=' . $this->token;
                    }
                } else {
                    $this->context->cookie->__set('kb_redirect_error', $this->module->l('Something went wrong while adding the Booking product. Please try again.', 'AdminKbBookingProductsController'));
                }
            }
        }
    }

    public function addUpdateProduct(&$product_data, $action = 'add')
    {
//        print_r($product_data);
//        die;
        if ($action == 'add') {
            $pro_object = new Product();
            $pro_object->id_manufacturer = 0;
            $pro_object->name = $product_data['product_name'];
            $pro_object->description_short = $product_data['short_description'];
            $pro_object->description = $product_data['description'];
            $pro_object->id_category_default = $product_data['id_category_default'];
            foreach ($this->getAllLanguages() as $lang) {
                $pro_object->link_rewrite[$lang['id_lang']] = $this->convertProductNametoLinkRewrite($product_data['product_name'][$lang['id_lang']]);
            }
            if (!empty($product_data['quantity'])) {
                $pro_object->quantity = $product_data['quantity'];
            } else {
                $pro_object->quantity = '999999';
            }
            $pro_object->price = $product_data['price'];
            $pro_object->condition = $product_data['condition'];
            $pro_object->show_condition = true;
            $pro_object->wholesale_price = 0;
            $pro_object->addUpdateProduct = 0;
            $pro_object->unit_price = 0.00;
            $pro_object->visibility = 'both';
//            $pro_object->tax_rate = 0;
            $pro_object->id_tax_rules_group = $product_data['id_tax_rules_group'];
            $pro_object->reference = $product_data['product_reference'];
            $pro_object->active = $product_data['enable'];
            $pro_object->quantity_discount = 0;
            $pro_object->out_of_stock = 1;
            $pro_object->redirect_type = '404';
            $pro_object->depends_on_stock = false;
            $pro_object->is_virtual = 1;
            $pro_object->cache_has_attachments = false;
            if ($pro_object->add()) {
                if (!$pro_object->updateCategories($product_data['categoryBox'])) {
                    $this->error_flag = true;
                    $this->context->cookie->__set('kb_redirect_error', $this->module->l('An error occurred while linking the object Product To categories'));
                }
                StockAvailable::setQuantity(
                    $pro_object->id,
                    0,
                    (int) $product_data['quantity'],
                    (int) $this->context->shop->id
                );
                StockAvailable::setProductOutOfStock(
                    (int) $pro_object->id,
                    $pro_object->out_of_stock,
                    $this->context->shop->id
                );
                if ($product_data['kb_product_type'] == 'appointment') {
                    $pro_object->text_fields = 2;
                } elseif ($product_data['kb_product_type'] == 'daily_rental') {
                    if ($product_data['period_type'] == 'date_time') {
                        $pro_object->text_fields = 1;
                    } else {
                        $pro_object->text_fields = 3;
                    }
                } elseif ($product_data['kb_product_type'] == 'hourly_rental') {
                    $pro_object->text_fields = 3;
                } else {
                    $pro_object->text_fields = 5;
                }
                $pro_object->customizable = 1;
                if (!$pro_object->createLabels(0, (int) $pro_object->text_fields)) {
                    $this->error_flag = true;
                    $this->context->cookie->__set('kb_redirect_error', $this->module->l('An error occurred while creating customization fields.', 'AdminKbBookingProductsController'));
                } else {
                    $this->addCustomizableTextFields($pro_object, $product_data);
                }
                if (!$this->error_flag) {
                    $pro_object->save();
                    //changes done by tarun for search listing issue
                    Search::indexation(false, $pro_object->id);
                    //changes over
                    $product_data['generated_product_id'] = $pro_object->id;
                    return true;
                } else {
                    return false;
                }
            } else {
                $this->context->cookie->__set(
                    'kb_redirect_error',
                    $this->module->l('An error occurred while adding the product.', 'AdminKbBookingProductsController')
                );
                return false;
            }
        } else {
            $pro_object = new Product($product_data['id_product']);
            $pro_object->name = $product_data['product_name'];
            $pro_object->description_short = $product_data['short_description'];
            $pro_object->description = $product_data['description'];
            $pro_object->id_category_default = $product_data['id_category_default'];
            foreach ($this->getAllLanguages() as $lang) {
                $pro_object->link_rewrite[$lang['id_lang']] = $this->convertProductNametoLinkRewrite($product_data['product_name'][$lang['id_lang']]);
            }
            $pro_object->quantity = $product_data['quantity'];
            $pro_object->price = $product_data['price'];
            $pro_object->condition = $product_data['condition'];
            $pro_object->show_condition = true;
            $pro_object->id_tax_rules_group = $product_data['id_tax_rules_group'];
//            $pro_object->id_tax_rules_group = $product_data['tax_rate'];
            $pro_object->reference = $product_data['product_reference'];
            $pro_object->active = $product_data['enable'];
            $product_data['generated_product_id'] = $pro_object->id;
            StockAvailable::setQuantity(
                $pro_object->id,
                0,
                (int) $product_data['quantity'],
                (int) $this->context->shop->id
            );
            if ($pro_object->save()) {
                //changes done vy tarun for search listing issue
                Search::indexation(false, $pro_object->id);
                //changes over
                if ($product_data['kb_product_type'] == 'daily_rental') {
                    if ($product_data['period_type'] == 'date_time') {
                        $pro_object->text_fields = 1;
                    } else {
                        $pro_object->text_fields = 3;
                    }
                    $pro_object->customizable = 1;
                    $pro_object->deleteCustomization();
                    if (!$pro_object->createLabels(0, (int) $pro_object->text_fields)) {
                        $this->error_flag = true;
                        $this->context->cookie->__set('kb_redirect_error', $this->module->l('An error occurred while creating customization fields.', 'AdminKbBookingProductsController'));
                    } else {
                        $this->addCustomizableTextFields($pro_object, $product_data);
                    }
                }
                
                if (!$pro_object->updateCategories($product_data['categoryBox'])) {
                    $this->error_flag = true;
                    $this->context->cookie->__set('kb_redirect_error', $this->module->l('An error occurred while linking the object Product To categories', 'AdminKbBookingProductsController'));
                }
                if (!$this->error_flag) {
                    return true;
                } else {
                    return false;
                }
            } else {
                $this->context->cookie->__set(
                    'kb_redirect_error',
                    $this->module->l('An error occurred while updating the product.', 'AdminKbBookingProductsController')
                );
                return false;
            }
        }
    }
    
    /*
     * Function for adding customizable text fields to the customizable product in the store when a new Gift Card product is being added
     */
    private function addCustomizableTextFields($pro_object, $posted_data)
    {
        $labels = $pro_object->getCustomizationFields();
        $this->module->l('Appointment Date', 'AdminKbBookingProductsController');
        $this->module->l('Service Type', 'AdminKbBookingProductsController');
        $this->module->l('Check In', 'AdminKbBookingProductsController');
        $this->module->l('Check Out', 'AdminKbBookingProductsController');
        $this->module->l('Room', 'AdminKbBookingProductsController');
        $this->module->l('Category', 'AdminKbBookingProductsController');
        $this->module->l('Total Days', 'AdminKbBookingProductsController');
        $this->module->l('Check In', 'AdminKbBookingProductsController');
        $this->module->l('Check Out', 'AdminKbBookingProductsController');
        $this->module->l('Total Hours', 'AdminKbBookingProductsController');
        $this->module->l('Check In', 'AdminKbBookingProductsController');
        $this->module->l('Check Out', 'AdminKbBookingProductsController');
        $this->module->l('Total Days', 'AdminKbBookingProductsController');
        $this->module->l('Booked Slot', 'AdminKbBookingProductsController');
        
        if ($posted_data['kb_product_type'] == 'appointment') {
            $cust_labels = array(
                'Appointment Date',
                'Service Type',
            );
        } elseif ($posted_data['kb_product_type'] == 'daily_rental') {
            if ($posted_data['period_type'] == 'date_time') {
                $cust_labels = array(
                    'Booked Slot'
                );
            } else {
                $cust_labels = array(
                    'Check In',
                    'Check Out',
                    'Total Days',
                );
            }
        } elseif ($posted_data['kb_product_type'] == 'hourly_rental') {
            $cust_labels = array(
                'Check In',
                'Check Out',
                'Total Hours',
            );
        } elseif ($posted_data['kb_product_type'] == 'hotel_booking') {
            $cust_labels = array(
                'Check In',
                'Check Out',
                'Room',
                'Category',
                'Total Days',
            );
        }
        $gc_label_count = 0;
        foreach (array_keys($labels[Product::CUSTOMIZE_TEXTFIELD]) as $id_customization_field) {
            foreach ($this->all_languages as $lang) {
                /* We are setting the values in $_POST as the fuction that is called after ($pro_object->updateLabels()) uses the same and since it is a core function it cannot be modified. */
                $_POST['label_'.Product::CUSTOMIZE_TEXTFIELD.'_'.$id_customization_field.'_'.$lang['id_lang']] = $this->getModuleTranslationByLanguage('kbbookingcalendar', $cust_labels[$gc_label_count], 'AdminKbBookingProductsController', $lang['iso_code']);
                $_POST['require_'.Product::CUSTOMIZE_TEXTFIELD.'_'.$id_customization_field] = 1;
//                $_POST['label_'.Product::CUSTOMIZE_TEXTFIELD.'_'.$id_customization_field.'_'.$lang['id_lang']] = $cust_labels[$gc_label_count];
            }
            $gc_label_count++;
        }
        if (!$this->error_flag && !$pro_object->updateLabels()) {
            $this->context->cookie->__set(
                'kb_redirect_error',
                $this->module->l('An error occurred while updating customization fields.', 'AdminKbBookingProductsController')
            );
        }
    }
    
    public function deleteOldLabels($id_product)
    {
        Db::getInstance()->execute(
            'DELETE `'._DB_PREFIX_.'customization_field`,`'._DB_PREFIX_.'customization_field_lang`
            FROM `'._DB_PREFIX_.'customization_field` JOIN `'._DB_PREFIX_.'customization_field_lang`
            WHERE `'._DB_PREFIX_.'customization_field`.`id_product` = '.(int)$id_product.'
            AND `'._DB_PREFIX_.'customization_field_lang`.`id_customization_field` = `'._DB_PREFIX_.'customization_field`.`id_customization_field`'
        );

        $prod_custom = Db::getInstance()->executeS('SELECT id_customization FROM '._DB_PREFIX_.'customization where id_product = '.(int)$id_product);

        foreach ($prod_custom as $custom) {
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customized_data` WHERE  `id_customization` = '.(int)$custom['id_customization']);
//            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'kb_dp_customization_price_mapping` WHERE `id_customization` = '.(int)$custom['id_customization']);
            Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'cart_product where id_product = '.(int)$id_product.' and id_customization='.(int) $custom['id_customization']);
        }
    }

    /**
     * Check if a field is edited (if the checkbox is checked)
     * This method will do something only for multishop with a context all / group
     *
     * @param string $field Name of field
     * @param int $id_lang
     * @return bool
     */
    protected function isProductFieldUpdated($field, $id_lang = null)
    {
        // Cache this condition to improve performances
        static $is_activated = null;
        if (is_null($is_activated)) {
            $is_activated = Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP && $this->id_object;
        }

        if (!$is_activated) {
            return true;
        }

        $def = ObjectModel::getDefinition($this->object);
        if (!$this->object->isMultiShopField($field) && is_null($id_lang) && isset($def['fields'][$field])) {
            return true;
        }

        if (is_null($id_lang)) {
            return !empty($_POST['multishop_check'][$field]);
        } else {
            return !empty($_POST['multishop_check'][$field][$id_lang]);
        }
    }

    /*
     * Function for unaccenting the product name so that it can be converted to a URL for link_rewrite field
     */

    private function unaccentProductName($string)
    {
        if (strpos($string = htmlentities($string, ENT_QUOTES, 'UTF-8'), '&') !== false) {
            $preg_replaced = preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1', $string);
            $string = html_entity_decode($preg_replaced, ENT_QUOTES, 'UTF-8');
        }
        return $string;
    }

    /*
     * Function for converting the product name to URL for link_rewrite field
     */

    private function convertProductNametoLinkRewrite($string, $slug = '-', $extra = null)
    {
        $unaccented_name = $this->unaccentProductName($string);
        $preg_quote = preg_quote($extra, '~');
        $preg_replaced = preg_replace('~[^0-9a-z' . $preg_quote . ']+~i', $slug, $unaccented_name);
        $trimmed_name = trim($preg_replaced, $slug);
        return Tools::strtolower($trimmed_name);
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['back_url'] = array(
            'href' => 'javascript: window.history.back();',
            'desc' => $this->module->l('Back', 'AdminKbBookingProductsController'),
            'icon' => 'process-icon-back'
        );
        if (!Tools::getValue('id_booking_product') && !Tools::isSubmit('add' . $this->table)) {
            $this->page_header_toolbar_btn['new_template'] = array(
                'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                'desc' => $this->module->l('Add New Booking Product', 'AdminKbBookingProductsController'),
                'icon' => 'process-icon-new'
            );
        }
        parent::initPageHeaderToolbar();
    }

    public function showCoverImage($id_row, $row_data)
    {
        if (!empty($row_data['id_product'])) {
            $product = new Product($row_data['id_product']);
            $coverImage = $product->getCover($row_data['id_product']);

            if (!empty($coverImage)) {
                $path_to_image = _PS_IMG_DIR_ . 'p/' . Image::getImgFolderStatic($coverImage['id_image']) . (int) $coverImage['id_image'] . '.' . $this->imageType;
                return ImageManagerCore::thumbnail($path_to_image, 'product_mini_' . $row_data['id_product'] . '_' . $this->context->shop->id . '.' . $this->imageType, 45, $this->imageType);
            }
        }
    }

    public function setMedia($newTheme = false)
    {
        parent::setMedia($newTheme);
        $this->addJqueryPlugin('tablednd');
        $this->addJqueryPlugin('autocomplete');
    }
}
