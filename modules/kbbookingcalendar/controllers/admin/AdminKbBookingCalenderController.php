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

class AdminKbBookingCalenderController extends AdminKbBookingCoreController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = false;
        $this->explicitSelect = true;
        parent::__construct();
    }
    
    public function setMedia($isnewtheme = false)
    {
        parent::setMedia($isnewtheme);
        $this->addCSS(_PS_MODULE_DIR_. 'kbbookingcalendar/libraries/fullcalendar/fullcalendar.min.css');
        $this->addJS(_PS_MODULE_DIR_. 'kbbookingcalendar/libraries/fullcalendar/lib/moment.min.js');
        $this->addJS(_PS_MODULE_DIR_. 'kbbookingcalendar/libraries/fullcalendar/lib/jquery.min.js');
        $this->addJS(_PS_MODULE_DIR_. 'kbbookingcalendar/libraries/fullcalendar/fullcalendar.min.js');
        $this->addJS(_PS_MODULE_DIR_. 'kbbookingcalendar/libraries/fullcalendar/locale-all.js');
        $this->addJS(_PS_MODULE_DIR_. 'kbbookingcalendar/views/js/admin/kb_admin_calender.js');
    }
    
    public function initContent()
    {
        $where = '';
        if (Tools::getIsset('kb_product_id') && !empty(Tools::getValue('kb_product_id'))) {
            $product_id = Tools::getValue('kb_product_id');
            $where = " AND kb.id_product=" . (int) $product_id;
            $this->context->smarty->assign('kb_product_id', $product_id);
        }

        if (Tools::getIsset('kb_product_type') && !empty(Tools::getValue('kb_product_type'))) {
            $product_type = Tools::getValue('kb_product_type');
            $where = $where . " AND kbp.product_type='" . $product_type . "'";
            $this->context->smarty->assign('kb_product_type', $product_type);
        }

        if (Tools::getIsset('kb_start_date') && !empty(Tools::getValue('kb_start_date'))) {
            $start_date = Tools::getValue('kb_start_date');
        } else {
            $start_date = date('Y-m-d', strtotime('-1 months'));
        }
        $this->context->smarty->assign('kb_start_date', $start_date);
        if (Tools::getIsset('kb_end_date') && !empty(Tools::getValue('kb_end_date'))) {
            $end_date = Tools::getValue('kb_end_date');
            $this->context->smarty->assign('kb_end_date', $end_date);
        } else {
            $end_date = date('Y-m-d H:i:s');
            $this->context->smarty->assign('kb_end_date', date('Y-m-d'));
        }

        $where = $where." AND o.date_add >= '" . pSQL($start_date) . "'
                    AND o.date_add <= '" . pSQL($end_date) . "'";
        
        $sql = "SELECT kb.id_order, kb.id_cart, kb.id_product , kb.id_customization "
                . "FROM " . _DB_PREFIX_ . "kb_booking_product_order kb "
                . "INNER JOIN " . _DB_PREFIX_ . "orders o "
                . "ON kb.id_order = o.id_order "
                . "INNER JOIN " . _DB_PREFIX_ . "kb_booking_product kbp "
                . "ON kb.id_product = kbp.id_product "
                . "WHERE o.id_shop = '" . (int) $this->context->shop->id . "' ".$where;
        $orders_data = Db::getInstance()->executeS($sql);
        $booking_data = array();
        foreach ($orders_data as $value) {
            $pro = array();
            $sql = "SELECT c.index, c.value "
                    . "FROM " . _DB_PREFIX_ . "customized_data c "
                    . "WHERE `value` REGEXP '-' AND c.id_customization = '" . (int) $value['id_customization'] . "'";
            $data_value = Db::getInstance()->executeS($sql);
            $pro['id_customization'] = $value['id_customization'];
            $pro['id_order'] = $value['id_order'];
            $data_value[] = $pro;
            $booking_data[] = $data_value;
        }
        $final_data = array();
        foreach ($booking_data as $key => $data) {
            $entry = array();
            foreach ($data as $key1 => $val) {
                if (isset($val['value'])) {
                    $date_data = array();
                    $date_data = explode("-", $val['value']);
                    if (count($date_data) > 1) {
                        if (Validate::isDate($val['value'])) {
                            if (!isset($entry['start'])) {
                                $entry['start'] = $val['value'];
                            } else {
                                $entry['end'] = $val['value'];
                            }
                        } else {
                            if (count($date_data) > 4) {
                                $date_new = explode(" ", $val['value']);

                                if (!isset($entry['start'])) {
                                    $entry['start'] = $date_new[0] . ' ' . $date_new[3];
                                } else {
                                    $entry['end'] = $date_new[0] . ' ' . $date_new[3];
                                }
                            }
                        }
                    }
                }

                if (isset($booking_data[$key][$key1]['id_customization'])) {
                    $entry['title'] = $this->getTitleForCalender((int) $booking_data[$key][$key1]['id_customization']);
                }
                if (isset($booking_data[$key][$key1]['id_order'])) {
                    $entry['url'] = $this->getOrderPageUrlForCalender((int) $booking_data[$key][$key1]['id_order']);
                }
            }
            if (isset($entry['start'])) {
                $entry['color'] = '#007bff';
                $final_data[] = $entry;
            }
        }

        //Booking Product data
        
        $sql_query = "SELECT ps.id_product, pl.`name` FROM `" . _DB_PREFIX_ . "kb_booking_product` a "
                . "INNER JOIN `" . _DB_PREFIX_ . "kb_booking_product_shop` s on (a.id_booking_product=s.id_booking_product) "
                . "JOIN `" . _DB_PREFIX_ . "product_lang` pl ON (a.`id_product` = pl.`id_product`) AND id_lang = ". (int) $this->context->language->id." "
                . "LEFT JOIN `" . _DB_PREFIX_ . "product_shop` ps ON (a.`id_product` = ps.`id_product`) AND ps.id_shop= ".(int) $this->context->shop->id." "
                . "WHERE  s.id_shop IN (".(int) $this->context->shop->id.") AND pl.id_shop IN (".(int) $this->context->shop->id.") ORDER BY a.id_booking_product ASC";
        $available_booking_pros = Db::getInstance()->executeS($sql_query);
        $this->context->smarty->assign('kb_available_booking_pros', $available_booking_pros);
        
        $sql_type = "SELECT a.`product_type` FROM `" . _DB_PREFIX_ . "kb_booking_product` a "
                . "INNER JOIN `" . _DB_PREFIX_ . "kb_booking_product_shop` s on (a.id_booking_product=s.id_booking_product) ";
        $available_booking_type = Db::getInstance()->executeS($sql_type);
        $this->context->smarty->assign('kb_available_booking_type', $available_booking_type);
        $product_type_arr = array();
        $this->product_type_arr['appointment'] = $this->module->l('Appointment', 'AdminKbBookingCalender');
        $this->product_type_arr['hotel_booking'] = $this->module->l('Hotel Booking', 'AdminKbBookingCalender');
        $this->product_type_arr['daily_rental'] = $this->module->l('Daily Rental', 'AdminKbBookingCalender');
        $this->product_type_arr['hourly_rental'] = $this->module->l('Hourly Rental', 'AdminKbBookingCalender');
        $this->context->smarty->assign('kb_product_type_arr', $this->product_type_arr);
        $this->context->smarty->assign('kb_admin_link', $this->context->link->getAdminLink('AdminKbBookingCalender', true).'&ajaxproductaction=true');
        $this->context->smarty->assign('current_date', date('Y-m-d'));
        $this->context->smarty->assign('lang_iso', $this->context->language->iso_code);
        $this->context->smarty->assign('calender_data', $final_data);
        $template_html = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'kbbookingcalendar/views/templates/admin/calender.tpl');
        $this->content .= $template_html;
        parent::initContent();
    }
    
    public function getTitleForCalender($id_customization)
    {
        $result = Db::getInstance()->executeS('
        SELECT c.`id_product`, cd.`value`, cfl.`name`
        FROM `' . _DB_PREFIX_ . 'customized_data` cd
        NATURAL JOIN `' . _DB_PREFIX_ . 'customization` c
        LEFT JOIN `' . _DB_PREFIX_ . 'customization_field_lang` cfl ON (cfl.id_customization_field = cd.`index` AND id_lang = ' . (int) $this->context->language->id . ')  
        WHERE cd.`value` NOT REGEXP "-" AND cd.`id_customization` = ' . (int) $id_customization);
        if (!empty($result)) {
            $title = '';
            $product_name = '';
            foreach ($result as $val) {
                if (isset($val['id_product']) && empty($product_name)) {
                    $product = new Product($val['id_product']);
                    $product_name = $product->name[$this->context->language->id];
                }
                if (empty($title)) {
                    $title = $val['name'] . ' : ' . $val['value'];
                } else {
                    $title = $val['name'] . ' : ' . $val['value'] . ' ' . $title;
                }
            }
            $title = $product_name . ' - ' . $title;
        } else {
            $title = $this->module->l('Event Name Not Found', 'AdminKbBookingCalenderController');
        }
        return $title;
    }
    
    public function getOrderPageUrlForCalender($id_order)
    {
        $new_dir = _PS_ADMIN_CONTROLLER_DIR_ . 'AdminOrdersController.php';
        if (file_exists($new_dir)) {
            $url = $this->context->link->getAdminlink('AdminOrders') . '&id_order=' . $id_order . '&vieworder';
        } else {
            $url = $this->context->link->getAdminlink('AdminOrders');
            $data_url = explode("?", $url);
            $url = $data_url[0].$id_order .'/view?'.$data_url[1];
        }
        return $url;
    }
}
