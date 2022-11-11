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
require_once _PS_MODULE_DIR_.'kbbookingcalendar/classes/KbBookingPriceRule.php';

class AdminKbBookingPriceRuleController extends AdminKbBookingCoreController
{
    protected $ps_shop = array();
//    protected $max_image_size = null;
    
    public function __construct()
    {
        $this->table = 'kb_booking_price_rule';
        $this->className = 'KbBookingPriceRule';
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->identifier    = 'id_booking_price_rule';
        parent::__construct();
        $this->toolbar_title = $this->module->l('Booking Price Rule', 'AdminKbBookingPriceRuleController');
        foreach (Shop::getShops(false) as $shop) {
            $this->ps_shop[$shop['id_shop']] = $shop['name'];
        }
//        $this->max_image_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
        
        $this->fields_list = array(
            'id_booking_price_rule' => array(
                'title' => $this->module->l('ID', 'AdminKbBookingPriceRuleController'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'rule_name' => array(
                'title' => $this->module->l('Price Rule', 'AdminKbBookingPriceRuleController'),
                'filter_key' => 'l!name'
            ),
            'product_name' => array(
                'title' => $this->module->l('Product Name', 'AdminKbBookingPriceRuleController'),
                'filter_key' => 'pl!name'
            ),
            'start_date' => array(
                'title' => $this->module->l('Start Date', 'AdminKbBookingPriceRuleController'),
                'type' => 'date',
                 'search' => false,
                'callback' => 'endDateDisplay'
            ),
            'end_date' => array(
                'title' => $this->module->l('End Date', 'AdminKbBookingPriceRuleController'),
                'type' => 'date',
                  'search' => false,
                'callback' => 'endDateDisplay'
            ),
            'particular_date' => array(
                'title' => $this->module->l('Specific Date', 'AdminKbBookingPriceRuleController'),
                'type' => 'date',
                'search' => false,
                'callback' => 'particularDateDisplay'
            ),
//            'reduction_type' => array(
//                'title' => $this->module->l('Reduction Type', 'AdminKbBookingPriceRuleController'),
////                'filter_key' => 'pl!name'
//            ),
//            'reduction_tax' => array(
//                'title' => $this->module->l('Reduction Tax', 'AdminKbBookingPriceRuleController'),
////                'filter_key' => 'pl!name'
//            ),
            'active' => array(
                'title' => $this->module->l('Status', 'AdminKbBookingPriceRuleController'),
                'align' => 'text-center',
                'active' => 'active',
                'type' => 'bool',
                'order_key' => 'active',
            ),
            'date_upd' => array(
                'title' => $this->module->l('Updated On', 'AdminKbBookingPriceRuleController'),
                'type' => 'datetime'
            )
        );
        
        $id_lang = $this->context->language->id;
        $this->_select = 'l.name as rule_name,s.id_shop,pl.name as product_name';
        $this->_join = ' INNER JOIN `' . _DB_PREFIX_ . $this->table . '_lang` l on (a.id_booking_price_rule=l.id_booking_price_rule AND l.id_lang='
                .(int)Context::getContext()->language->id.' AND l.id_shop='.(int)Context::getContext()->shop->id.') ';
        $this->_join .= ' INNER JOIN `' . _DB_PREFIX_ . $this->table . '_shop` s on (a.id_booking_price_rule=s.id_booking_price_rule) ';
        $this->_join .= ' INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl on (a.id_product=pl.id_product AND pl.id_lang='.(int)$id_lang.') ';
        $this->_where = ' AND s.id_shop IN ('.(int)Context::getContext()->shop->id.')';
        
        $this->addRowAction('edit');
        $this->addRowAction('delete');
    }
    
    public function initContent()
    {
        parent::initContent();
    }
    
    public function endDateDisplay($echo, $tr)
    {
        if ($tr['date_selection'] != 'date_range') {
            return '--';
        } else {
            return $echo;
        }
    }
    
    public function particularDateDisplay($echo, $tr)
    {
//        unset($echo);
        if ($tr['date_selection'] == 'date_range') {
            return '--';
        } else {
            return $echo;
        }
    }
    
    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('active'.$this->table)) {
            $id = Tools::getValue('id_booking_price_rule');
            $object = new $this->className((int) $id);
            if ($object->active == 1) {
                $object->active = 0;
            } else {
                $object->active = 1;
            }
            $object->update();
            $this->context->cookie->__set(
                'kb_redirect_success',
                $this->module->l('The status has been successfully updated.', 'AdminKbBookingPriceRuleController')
            );
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBookingPriceRule', true));
        }
        if (Tools::isSubmit('submitBulkenableSelection' . $this->table)) {
            $this->processBulkEnableSelection();
            $this->context->cookie->__set('kb_redirect_success', $this->module->l('The status has been successfully updated.', 'AdminKbBookingPriceRuleController'));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBookingPriceRule', true));
        }
        
        if (Tools::isSubmit('submitBulkdisableSelection' . $this->table)) {
            $this->processBulkDisableSelection();
            $this->context->cookie->__set('kb_redirect_success', $this->module->l('The status has been successfully updated.', 'AdminKbBookingPriceRuleController'));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbBookingPriceRule', true));
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
            $name = array();
            foreach ($this->all_languages as $lang) {
                $name[$lang['id_lang']] = trim(Tools::getValue('name_' . $lang['id_lang']));
            }
            $id_product= Tools::getValue('id_product');
            $date_selection = Tools::getValue('date_selection');
            $start_date = trim(Tools::getValue('start_date'));
            $end_date = trim(Tools::getValue('end_date'));
            $particular_date = trim(Tools::getValue('particular_date'));
            $reduction = trim(Tools::getValue('reduction'));
            $reduction_type = Tools::getValue('reduction_type');
//            $reduction_tax = Tools::getValue('reduction_tax');
            $active= Tools::getValue('active');
            if ($date_selection == 'date_range') {
                $compare_start = $start_date;
                $compare_end = $end_date;
                $particular_date = date('Y-m-d H:i:s', strtotime('1970-01-01 00:00:00'));
            } else {
                $compare_start = date('Y-m-d H:i:s', strtotime($particular_date.' 00:00:00'));
                $compare_end = date('Y-m-d H:i:s', strtotime($particular_date . ' 23:59:00'));
                $start_date = date('Y-m-d H:i:s', strtotime('1970-01-01 00:00:00'));
                $end_date = date('Y-m-d H:i:s', strtotime('1970-01-01 23:59:59'));
            }
            if ($this->checkRangeExist($id_product, $compare_start, $compare_end)) {
                $this->context->cookie->__set('kb_redirect_error', $this->module->l('Rule cannot be created as range is already exist.', 'AdminKbBookingPriceRuleController'));
                Tools::redirectAdmin($this->context->link->getAdminlink('AdminKbBookingPriceRule', true));
            }

            $kbBookingRule = new KbBookingPriceRule();
            $kbBookingRule->name = $name;
            $kbBookingRule->id_product = $id_product;
            $kbBookingRule->date_selection = $date_selection;
            $kbBookingRule->start_date = $start_date;
            $kbBookingRule->end_date = $end_date;
            $kbBookingRule->active = $active;
            $kbBookingRule->particular_date = $particular_date;
//            $kbBookingRule->reduction_tax = $reduction_tax;
            $kbBookingRule->reduction_type = $reduction_type;
            $kbBookingRule->reduction = $reduction;
            if ($kbBookingRule->add()) {
                $this->context->cookie->__set('kb_redirect_success', $this->module->l('Rule successfully created.', 'AdminKbBookingPriceRuleController'));
                Tools::redirectAdmin($this->context->link->getAdminlink('AdminKbBookingPriceRule', true));
            }
        }
    }
    
    public function checkRangeExist($id_product, $start, $end, $id = null)
    {
        $existing_rec = KbBookingPriceRule::getRulebyProductID($id_product);
        if (!empty($existing_rec)) {
            foreach ($existing_rec as $rec) {
                if (!empty($id)) {
                    if ($id == $rec['id_booking_price_rule']) {
                        continue;
                    }
                }
                if ($rec['date_selection'] == 'date_range') {
                    if ($this->dateIsBetween($start, $end, $rec['start_date'], $rec['end_date'])) {
                        return true;
                    }
                } else {
                    if (strtotime($rec['particular_date']) >= strtotime($start) and (strtotime($rec['particular_date']) <=  strtotime($end))) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    private function dateIsBetween($dbfrom1, $dbto1, $user_st, $user_et)
    {
        if (((strtotime($dbfrom1) >= strtotime($user_st)) and ( strtotime($dbfrom1) <= strtotime($user_et)))
            or ( strtotime($dbto1) >= strtotime($user_st)) and ( strtotime($dbto1) <= strtotime($user_et))) {
            return true;
        } else {
            return false;
        }
    }

    public function processupdate()
    {
        if (Tools::isSubmit('submitAdd'.$this->table)) {
            $name = array();
//            print_r(Tools::getAllValues());
//            die;
            $id_booking_price_rule = Tools::getValue('id_booking_price_rule');
            foreach ($this->all_languages as $lang) {
                $name[$lang['id_lang']] = trim(Tools::getValue('name_' . $lang['id_lang']));
            }
            $id_product= Tools::getValue('id_product');
            $date_selection = Tools::getValue('date_selection');
            $start_date = trim(Tools::getValue('start_date'));
            $end_date = trim(Tools::getValue('end_date'));
            $particular_date = trim(Tools::getValue('particular_date'));
            $reduction = trim(Tools::getValue('reduction'));
            $reduction_type = Tools::getValue('reduction_type');
//            $reduction_tax = Tools::getValue('reduction_tax');
            $active= Tools::getValue('active');
            if ($date_selection == 'date_range') {
                $compare_start = $start_date;
                $compare_end = $end_date;
                $particular_date = date('Y-m-d H:i:s', strtotime('1970-01-01 00:00:00'));
            } else {
                $compare_start = date('Y-m-d H:i:s', strtotime($particular_date . ' 00:00:00'));
                $compare_end = date('Y-m-d H:i:s', strtotime($particular_date . ' 23:59:00'));
                $start_date = date('Y-m-d H:i:s', strtotime('1970-01-01 00:00:00'));
                $end_date = date('Y-m-d H:i:s', strtotime('1970-01-01 23:59:59'));
            }
            if ($this->checkRangeExist($id_product, $compare_start, $compare_end, $id_booking_price_rule)) {
                $this->context->cookie->__set('kb_redirect_error', $this->module->l('Rule cannot be created as range is already exist.', 'AdminKbBookingPriceRuleController'));
                Tools::redirectAdmin($this->context->link->getAdminlink('AdminKbBookingPriceRule', true));
            }
            $kbBookingRule = new KbBookingPriceRule($id_booking_price_rule);
//            print_r($kbBookingRule);
//            die;
            $kbBookingRule->name = $name;
            $kbBookingRule->id_product = $id_product;
            $kbBookingRule->date_selection = $date_selection;
            
//            $kbBookingRule->start_date = $start_date;
//            $kbBookingRule->end_date = $end_date;
            $kbBookingRule->active = $active;
            if ($date_selection == 'date_range') {
                $kbBookingRule->start_date = $start_date;
                $kbBookingRule->end_date = $end_date;
                $kbBookingRule->particular_date = '1971-01-01';
            } else {
                $kbBookingRule->start_date = '1971-01-01';
                $kbBookingRule->end_date = '1971-01-01';
                $kbBookingRule->particular_date = $particular_date;
            }
//            $kbBookingRule->reduction_tax = $reduction_tax;
            $kbBookingRule->reduction_type = $reduction_type;
            $kbBookingRule->reduction = $reduction;
//            Tools::dieObject($kbBookingRule);
            if ($kbBookingRule->update()) {
                $this->context->cookie->__set('kb_redirect_success', $this->module->l('Rule successfully updated.', 'AdminKbBookingPriceRuleController'));
                Tools::redirectAdmin($this->context->link->getAdminlink('AdminKbBookingPriceRule', true));
            }
        }
    }
    
    public function renderForm()
    {
//        die( date('Y-m-d H:i:s', strtotime('2019-01-07 23:59:00')));
//        var_dump($this->dateIsBetween('2015-04-01', '2015-04-01', '2015-04-01', '2015-04-01'));
        if (!($obj = $this->loadObject(true))) {
            return;
        }
        
        $product_list = KbBookingProduct::getAllProducts($this->context->language->id, 0, 0, 'id_product', 'ASC');

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->module->l('Price Rule', 'AdminKbBookingPriceRuleController'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Title', 'AdminKbBookingPriceRuleController'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'col' => '5',
                    'hint' => $this->module->l('Enter the title of price rule', 'AdminKbBookingPriceRuleController')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->module->l('Select Product', 'AdminKbBookingPriceRuleController'),
                    'name' => 'id_product',
                    'class' => 'kb_price_rule_id_product',
                    'required' => true,
//                    'col' => '2',
                    'options' => array(
                        'query' => $product_list,
                        'id' => 'id_product',
                        'name' => 'name'
                    ),
                    'hint' => $this->module->l('Select the product to map price rule', 'AdminKbBookingPriceRuleController')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->module->l('Date Type', 'AdminKbBookingPriceRuleController'),
                    'name' => 'date_selection',
                    'required' => true,
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'date_range',
                                'name' => $this->module->l('Date Range', 'AdminKbBookingPriceRuleController')
                            ),
                            array(
                                'id' => 'particular_date',
                                'name' => $this->module->l('Specific Date', 'AdminKbBookingPriceRuleController'),
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'hint' => $this->module->l('Select the date type', 'AdminKbBookingPriceRuleController')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Start Date', 'AdminKbBookingPriceRuleController'),
                    'name' => 'start_date',
//                    'class' => 'kb_booking_start_date',
                    'prefix' => '<i class="icon-calendar"></i>',
                    'required' => true,
                    'col' => 3,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('End Date', 'AdminKbBookingPriceRuleController'),
                    'name' => 'end_date',
//                    'class' => 'kb_booking_end_date',
                    'prefix' => '<i class="icon-calendar"></i>',
                    'required' => true,
                    'col' => 3,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Specific Date', 'AdminKbBookingPriceRuleController'),
                    'name' => 'particular_date',
                    'prefix' => '<i class="icon-calendar"></i>',
                    'required' => true,
                    'col' => 3,
                ),
                array(
                    'type' => 'select',
                    'name' => 'reduction_type',
                    'label' => $this->module->l('Reduction Type', 'AdminKbBookingPriceRuleController'),
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'percentage',
                                'name' => $this->module->l('percentage', 'AdminKbBookingPriceRuleController'),
                            ),
                            array(
                                'id' => 'fixed',
                                'name' => $this->module->l('Amount', 'AdminKbBookingPriceRuleController')
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    )
                ),
                 array(
                    'type' => 'text',
                    'name' => 'reduction',
                    'required' => true,
                    'col' => 3,
//                    'prefix' => $this->context->currency->sign,
                    'label' => $this->module->l('Reduction', 'AdminKbBookingPriceRuleController'),
                ),
//                array(
//                    'type' => 'select',
//                    'name' => 'reduction_tax',
//                    'label' => $this->module->l('Reduction Tax With/Without'),
//                    'options' => array(
//                        'query' => array(
//                            array(
//                                'id' => 0,
//                                'name' => $this->module->l('Without Tax'),
//                            ),
//                            array(
//                                'id' => 1,
//                                'name' => $this->module->l('With Tax')
//                            ),
//                        ),
//                        'id' => 'id',
//                        'name' => 'name',
//                    )
//                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Active', 'AdminKbBookingPriceRuleController'),
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
                'title' => $this->module->l('Save', 'AdminKbBookingPriceRuleController')
            )
        );
        
        $tpl = $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/velovalidation.tpl');
        
        return $tpl.parent::renderForm();
    }
    
    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['back_url'] = array(
            'href' => 'javascript: window.history.back();',
            'desc' => $this->module->l('Back', 'AdminKbBookingPriceRuleController'),
            'icon' => 'process-icon-back'
        );
        if (!Tools::getValue('id_booking_price_rule') && !Tools::isSubmit('add'.$this->table)) {
            $this->page_header_toolbar_btn['new_template'] = array(
                'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                'desc' => $this->module->l('Add New Rule', 'AdminKbBookingPriceRuleController'),
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
