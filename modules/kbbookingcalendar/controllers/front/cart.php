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

require_once _PS_MODULE_DIR_ . 'kbbookingcalendar/classes/KbBookingProduct.php';

class KbBookingCalendarCartModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $display_column_left = false;
    public $is_display_tax_excl_price = false;
    
    public function __construct()
    {
        parent::__construct();
        $this->display_column_left = true;
        $this->display_column_right = false;
        if ((bool) Context::getContext()->customer->isLogged()) {
            $customer = new Customer((int) Context::getContext()->customer->id);
            $customer_group = $customer->getGroups();
        } else {
            $customer_group = array(Configuration::get('PS_UNIDENTIFIED_GROUP'));
        }
        foreach ($customer_group as $key => $group_id) {
            $grp_obj = new group($group_id);
            $this->is_display_tax_excl_price = (int) $grp_obj->price_display_method;
        }
    }
    
    /**
     * Initializes controller.
     *
     * @see FrontController::init()
     *
     * @throws PrestaShopException
     */
    public function init()
    {
        parent::init();
    }
    
    public function postProcess()
    {
        parent::postProcess();
        if (Tools::getValue('ajax')) {
            if (Tools::getValue('validateCheckInDate')) {
                $data = $this->validateCheckInDate();
                echo json_encode($data);
                die;
            }
            
            if (Tools::getValue('displayTimeSlots')) {
                $data = $this->displayTimeSlots();
                echo json_encode($data);
                die;
            }
            
            if (Tools::getValue('validateQuantity')) {
//                $data = $this->validateQuantity();
                $data = $this->validateCheckInDate();
                 echo json_encode($data);
                die;
            }
            
            if (Tools::getValue('addCart')) {
                $this->addProductInCart();
            }
        }
    }
    
    public function validateQuantity()
    {
        $data = array();
        $id_booking_product = Tools::getValue('id_booking_product');
        $id_product = Tools::getValue('id_product');
        $id_hotel_room = Tools::getValue('id_hotel_room');
        $qty = Tools::getValue('qty');
        $kb_checkin_selected = Tools::getValue('kb_checkin_selected');
        $kb_checkout_selected = Tools::getValue('kb_checkout_selected');
        $booking_product_details = KbBookingProduct::getProductDetailsByID($id_product);
        $data = array();
        if (!empty($booking_product_details)) {
            $room_details = '';
            if ($booking_product_details['product_type'] == 'hotel_booking' && !empty($id_hotel_room)) {
                $room_details = Db::getInstance()->getRow('SELECT r.* FROM ' . _DB_PREFIX_ . 'kb_booking_product_room_facilities_mapping r INNER JOIN ' . _DB_PREFIX_ . 'kb_booking_product p on (p.id_booking_product=r.id_booking_product) WHERE r.id_booking_product=' . (int) $id_booking_product . ' AND id_booking_room_facilities_map=' . (int) $id_hotel_room);
            }
            $date_details = Tools::jsonDecode($booking_product_details['date_details'], true);
            if (!empty($date_details)) {
                foreach ($date_details as $rec_date) {
                    $from_date = strtotime($rec_date['from_date']);
                    $to_date = strtotime($rec_date['to_date']);
                    $kb_checkin_date = date('Y-m-d H:i:s', mktime($kb_checkin_selected['hours'], $kb_checkin_selected['minutes'], $kb_checkin_selected['seconds'], $kb_checkin_selected['months'] + 1, $kb_checkin_selected['date'], $kb_checkin_selected['year']));
                    if ($booking_product_details['period_type'] == 'date_time') {
                        if (strtotime($kb_checkin_date) >= $from_date && strtotime($kb_checkin_date) <= $to_date) {
                            if ($booking_product_details['product_type'] == 'appointment' || $booking_product_details['product_type'] == 'daily_rental') {
                                $available_qty = $this->getAvailableQuantityByProduct($id_product, $kb_checkin_date, $kb_checkin_date, $qty);
                                if (!$available_qty) {
                                    $data['error'] = $this->module->l('The item is not available in this quantity. Kindly adjusted the quantity.', 'cart');
                                }
                            }
                        }
                    } else {
                        if ($booking_product_details['product_type'] == 'daily_rental' ||
                                $booking_product_details['product_type'] == 'hotel_booking' ||
                                $booking_product_details['product_type'] == 'appointment' ||
                                $booking_product_details['product_type'] == 'hourly_rental') {
                            $kb_checkin_date = date('Y-m-d', mktime($kb_checkin_selected['hours'], $kb_checkin_selected['minutes'], $kb_checkin_selected['seconds'], $kb_checkin_selected['months'] + 1, $kb_checkin_selected['date'], $kb_checkin_selected['year']));
                            if ($booking_product_details['product_type'] == 'appointment') {
                                if (!empty($kb_checkin_date)) {
                                    if (strtotime($kb_checkin_date) >= $from_date && strtotime($kb_checkin_date) <= $to_date) {
                                        if (!empty($qty)) {
                                            $available_qty = $this->getAvailableQuantityByProduct($id_product, $kb_checkin_date, $kb_checkin_date, $qty);
                                            if (!$available_qty) {
                                                $data['error'] = $this->module->l('The item is not available in this quantity. Kindly adjusted the quantity.', 'cart');
                                            } elseif ($available_qty) {
                                                $data['success'] = true;
                                                $price = $this->applyKbRule($id_product, $kb_checkin_date, $kb_checkin_date, $rec_date['price']);
                                                // changes by rishabh jain
                                                $price_with_tax = Tools::ps_round(
                                                    Product::getPriceStaticBookingProductPrice(
                                                        (float) $price,
                                                        (int)$id_product,
                                                        true,
                                                        null,
                                                        6,
                                                        null,
                                                        false,
                                                        true,
                                                        1
                                                    ),
                                                    (int) Configuration::get('PS_PRICE_DISPLAY_PRECISION')
                                                );
                                                
                                                
                                                // changes over
                                                $data['price'] = Tools::convertPrice($price);
                                                if ($this->is_display_tax_excl_price) {
                                                    $data['display_price'] = Tools::displayPrice(Tools::convertPrice($price));
                                                } else {
                                                    $data['display_price'] = Tools::displayPrice(Tools::convertPrice($price_with_tax));
                                                }
                                                break;
                                            }
                                        }
                                    }
                                }
                            } else {
                                if ($booking_product_details['product_type'] == 'hourly_rental') {
                                    $kb_checkin_date = date('Y-m-d H:i', mktime($kb_checkin_selected['hours'], $kb_checkin_selected['minutes'], $kb_checkin_selected['seconds'], $kb_checkin_selected['months'] + 1, $kb_checkin_selected['date'], $kb_checkin_selected['year']));
                                    $kb_checkout_date = date('Y-m-d H:i', mktime($kb_checkout_selected['hours'], $kb_checkout_selected['minutes'], $kb_checkout_selected['seconds'], $kb_checkout_selected['months'] + 1, $kb_checkout_selected['date'], $kb_checkout_selected['year']));
                                } else {
                                    $kb_checkout_date = date('Y-m-d 00:00:00', mktime($kb_checkout_selected['hours'], $kb_checkout_selected['minutes'], $kb_checkout_selected['seconds'], $kb_checkout_selected['months'] + 1, $kb_checkout_selected['date'], $kb_checkout_selected['year']));
                                }
                                if (!empty($kb_checkout_date) && !empty($kb_checkin_date)) {
                                    if ((strtotime($kb_checkin_date) >= $from_date && strtotime($kb_checkin_date) <= $to_date) &&
                                            (strtotime($kb_checkout_date) <= $to_date && strtotime($kb_checkout_date) >= $from_date)) {
                                        if (!empty($qty)) {
                                            if ($booking_product_details['product_type'] == 'hotel_booking') {
                                                $available_qty = $this->getAvailableQuantityByProduct($id_product, $kb_checkin_date, $kb_checkout_date, $qty, $id_hotel_room);
                                            } else {
                                                $available_qty = $this->getAvailableQuantityByProduct($id_product, $kb_checkin_date, $kb_checkout_date, $qty);
                                            }
                                            if (!$available_qty) {
                                                $data['error'] = $this->module->l('The item is not available in this quantity. Kindly adjusted the quantity.', 'cart');
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if (!isset($data['error'])) {
            $data['success'] = true;
        }

        return $data;
    }
    
    public function displayTimeSlots()
    {
        $data = array();
        $id_booking_product = Tools::getValue('id_booking_product');
        $id_product = Tools::getValue('id_product');
        $product_type = Tools::getValue('product_type');
        $period_type = Tools::getValue('period_type');
        $id_hotel_room = Tools::getValue('id_hotel_room');
        $qty = Tools::getValue('qty');
        $time_slots_data = array();
        $booking_product_details = KbBookingProduct::getProductDetailsByID($id_product);
        if (!empty($booking_product_details)) {
            $product_type = $booking_product_details['product_type'];
            $room_details = '';
            if ($product_type == 'hotel_booking' && !empty($id_hotel_room)) {
                $room_details = Db::getInstance()->getRow('SELECT r.* FROM ' . _DB_PREFIX_ . 'kb_booking_product_room_facilities_mapping r INNER JOIN ' . _DB_PREFIX_ . 'kb_booking_product p on (p.id_booking_product=r.id_booking_product) WHERE r.id_booking_product=' . (int) $id_booking_product . ' AND id_booking_room_facilities_map=' . (int) $id_hotel_room);
            }
            $date_details = Tools::jsonDecode($booking_product_details['date_details'], true);
            if (!empty($date_details)) {
                foreach ($date_details as $rec_date) {
                    $from_date = strtotime($rec_date['from_date']);
                    $to_date = strtotime($rec_date['to_date']);
                    if ($product_type == 'appointment' || $product_type == 'daily_rental' || $product_type == 'hourly_rental') {
                        $kb_date = Tools::getValue('date');
                        $kbselected_date = Tools::getValue('kb_checkin_selected');
                        $kb_date = date('Y-m-d', mktime($kbselected_date['hours'], $kbselected_date['minutes'], $kbselected_date['seconds'], $kbselected_date['months'] + 1, $kbselected_date['date'], $kbselected_date['year']));
                        $selected_date = strtotime($kb_date);
                        if ($selected_date >= $from_date && $selected_date <= $to_date) {
                            if ($period_type == 'date_time') {
                                if (!empty($qty)) {
                                    if (isset($rec_date['time']) && !empty($rec_date['time'])) {
                                        foreach ($rec_date['time'] as $date_time_key => &$date_time_data) {
                                            $time_slot = '';
                                            $time_slot = $date_time_data['from_time'].' - '.$date_time_data['to_time'];
                                            $from_time_array = array();
                                            $from_time_array = explode(":", $date_time_data['from_time']);
                                            $from_slot_minutes = 60 * $from_time_array[0] +$from_time_array[1];
                                            $from_slot_time = strtotime($kb_date.' + '.$from_slot_minutes.' minute');
                                            if ($from_slot_time > time()) {
                                                $available_qty = $this->getAvailableQuantityByProductAndTimeSlots($id_product, $kb_date, $kb_date, $qty, null, $time_slot);
                                                if (!$available_qty) {
                                                } else {
                                                    // changes by rishabh jain
                                                    $slot_price_with_tax = Tools::ps_round(
                                                        Product::getPriceStaticBookingProductPrice(
                                                            (float) $date_time_data['price'],
                                                            (int)$id_product,
                                                            true,
                                                            null,
                                                            2,
                                                            null,
                                                            false,
                                                            true,
                                                            1
                                                        ),
                                                        (int) Configuration::get('PS_PRICE_DISPLAY_PRECISION')
                                                    );
                                                    if ($this->is_display_tax_excl_price == 0) {
                                                        $date_time_data['price_with_tax'] = $slot_price_with_tax;
                                                    }
                                                    $time_slots_data[] = $date_time_data;
                                                }
                                            }
                                        }
                                    } else {
                                        $data['error'] = $this->module->l('The item is not available in this quantity. Kindly adjusted the quantity.', 'cart');
                                        break;
                                    }
                                } else {
                                    if (isset($rec_date['time']) && !empty($rec_date['time'])) {
                                        if ($this->is_display_tax_excl_price == 0) {
                                            foreach ($rec_date['time'] as $time_key => &$time_data) {
                                                $slot_price_with_tax = Tools::ps_round(
                                                    Product::getPriceStaticBookingProductPrice(
                                                        (float) $time_data['price'],
                                                        (int)$id_product,
                                                        true,
                                                        null,
                                                        2,
                                                        null,
                                                        false,
                                                        true,
                                                        1
                                                    ),
                                                    (int) Configuration::get('PS_PRICE_DISPLAY_PRECISION')
                                                );
                                                $time_data['price_with_tax'] = $slot_price_with_tax;
                                            }
                                        }
                                        $this->context->smarty->assign(array(
                                            'kb_time_slot' => $rec_date['time'],
                                        ));
                                        $data['content'] = $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/display_time_slot.tpl');
                                        $data['success'] = true;
                                        return $data;
                                    }
                                }
                                if (isset($time_slots_data) && !empty($time_slots_data)) {
                                    $this->context->smarty->assign(array(
                                        'kb_time_slot' => $time_slots_data,
                                    ));
                                    $data['content'] = $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/display_time_slot.tpl');
                                    $data['success'] = true;
                                    return $data;
                                }
                            }
                        }

                    }
                }
            }
        }

        if (!isset($data['error'])) {
            if (!isset($data['success'])) {
                $data['error'] = $this->module->l('The booking is not available on the selected date', 'cart');
            }
        }

        return $data;
    }
    
    public function validateHotelCheckIn()
    {
        $data = array();
        $id_hotel_room = Tools::getValue('id_hotel_room');
        $qty = Tools::getValue('qty');
        $id_booking_product = Tools::getValue('id_booking_product');
        $id_product = Tools::getValue('id_product');
        $product_type = Tools::getValue('product_type');
        $kb_checkin_selected = Tools::getValue('kb_checkin_selected');
        $kb_checkout_selected = Tools::getValue('kb_checkout_selected');
        $kb_checkin_date = date('Y-m-d', mktime($kb_checkin_selected['hours'], $kb_checkin_selected['minutes'], $kb_checkin_selected['seconds'], $kb_checkin_selected['months'] + 1, $kb_checkin_selected['date'], $kb_checkin_selected['year']));
        $kb_checkout_date = date('Y-m-d', mktime($kb_checkout_selected['hours'], $kb_checkout_selected['minutes'], $kb_checkout_selected['seconds'], $kb_checkout_selected['months'] + 1, $kb_checkout_selected['date'], $kb_checkout_selected['year']));
        $room_details = '';
        $booking_product_details = KbBookingProduct::getProductDetailsByID($id_product);
        if ($product_type == 'hotel_booking' && !empty($id_hotel_room)) {
            $room_details = Db::getInstance()->getRow('SELECT r.* FROM ' . _DB_PREFIX_ . 'kb_booking_product_room_facilities_mapping r INNER JOIN ' . _DB_PREFIX_ . 'kb_booking_product p on (p.id_booking_product=r.id_booking_product) WHERE r.id_booking_product=' . (int) $id_booking_product . ' AND id_booking_room_facilities_map=' . (int) $id_hotel_room);
        }
        if (!empty($booking_product_details)) {
            $datediff = strtotime($kb_checkout_date) - strtotime($kb_checkin_date);
            $total_days = round($datediff / (60 * 60 * 24));
            $min_days = $booking_product_details['min_days'];
            $max_days = $booking_product_details['max_days'];
            $days_exceed = true;
            if (!empty($min_days) && !empty($max_days)) {
                if (((int) $total_days >= (int) $min_days) &&
                        ((int) $total_days <= (int) $max_days)) {
                    $days_exceed = false;
                }
                if ($days_exceed) {
                    $data['error'] = $this->module->l('Booking is not allowed as Min Days should be ', 'cart') . ' ' . $min_days . ' ' . $this->module->l('and Max. days should be ', 'cart') . ' ' . $max_days;
                    return $data;
                }
            }

            $date_details = Tools::jsonDecode($booking_product_details['date_details'], true);
            $kb_date_rang = $this->kbDateRange($kb_checkin_date, $kb_checkout_date);
            $hotel_price = 0;
            if (empty($date_details)) {
                $data['error'] = $this->module->l('The booking is not available on the selected date', 'cart');
                return $data;
            }
            if (!empty($kb_date_rang)) {
                foreach ($kb_date_rang as $date_selected) {
                    $kb_date_exist = 0;

                    foreach ($date_details as $rec_date) {
                        $from_date = strtotime($rec_date['from_date']);
                        $to_date = strtotime($rec_date['to_date']);

                        if (strtotime($date_selected) >= $from_date && strtotime($date_selected) <= $to_date) {
                            //additinal price included
                            $kb_date_exist = 1;
                            $kb_price = $this->applyKbRule($id_product, $kb_checkin_date, $kb_checkin_date, $rec_date['price'] + $room_details['price']);
                            $hotel_price += $kb_price;
                        }
                    }

                    if (!$kb_date_exist) {
                        $kb_price = $this->applyKbRule($id_product, $kb_checkin_date, $kb_checkin_date, $room_details['price']);
                        $hotel_price += $kb_price;
                    }
                }
            }
            if (!empty($room_details) && !empty($qty)) {
                $available_qty = $this->getAvailableQuantityByProduct($id_product, $kb_checkin_date, $kb_checkout_date, (int) $qty, $id_hotel_room);
                if (!$available_qty) {
                    $data['error'] = $this->module->l('The item is not available in this quantity. Kindly adjusted the quantity.', 'cart');
                } else {
                    $price = $hotel_price;
                    $data['success'] = true;
                    // changes by rishabh jain
                    $price_with_tax = Tools::ps_round(
                        Product::getPriceStaticBookingProductPrice(
                            (float) $price,
                            (int)$id_product,
                            true,
                            null,
                            6,
                            null,
                            false,
                            true,
                            1
                        ),
                        (int) Configuration::get('PS_PRICE_DISPLAY_PRECISION')
                    );


                    // changes over
                    $data['price'] = Tools::convertPrice($price);
                    if ($this->is_display_tax_excl_price) {
                        $data['display_price'] = Tools::displayPrice(Tools::convertPrice($price));
                    } else {
                        $data['display_price'] = Tools::displayPrice(Tools::convertPrice($price_with_tax));
                    }
                }
            }
        }

        if (!isset($data['error'])) {
            if (!isset($data['success'])) {
                $data['error'] = $this->module->l('The booking is not available on the selected date', 'cart');
            }
        }
        return $data;
    }
    public function validateHourlyRentalCheckIn()
    {
        $data = array();
        $id_hotel_room = Tools::getValue('id_hotel_room');
        $qty = Tools::getValue('qty');
        $id_booking_product = Tools::getValue('id_booking_product');
        $id_product = Tools::getValue('id_product');
        $product_type = Tools::getValue('product_type');
        $kb_checkin_selected = Tools::getValue('kb_checkin_selected');
        $kb_checkout_selected = Tools::getValue('kb_checkout_selected');
        $kb_checkin_date = date('Y-m-d', mktime($kb_checkin_selected['hours'], $kb_checkin_selected['minutes'], $kb_checkin_selected['seconds'], $kb_checkin_selected['months'] + 1, $kb_checkin_selected['date'], $kb_checkin_selected['year']));
        $kb_checkout_date = date('Y-m-d', mktime($kb_checkout_selected['hours'], $kb_checkout_selected['minutes'], $kb_checkout_selected['seconds'], $kb_checkout_selected['months'] + 1, $kb_checkout_selected['date'], $kb_checkout_selected['year']));
        $booking_product_details = KbBookingProduct::getProductDetailsByID($id_product);
        if (!empty($booking_product_details)) {
            // chnages for hourly rental
            $kb_hour_checkin = date('Y-m-d H:i:s', mktime($kb_checkin_selected['hours'], $kb_checkin_selected['minutes'], $kb_checkin_selected['seconds'], $kb_checkin_selected['months'] + 1, $kb_checkin_selected['date'], $kb_checkin_selected['year']));
            $kb_hour_checkout = date('Y-m-d H:i:s', mktime($kb_checkout_selected['hours'], $kb_checkout_selected['minutes'], $kb_checkout_selected['seconds'], $kb_checkout_selected['months'] + 1, $kb_checkout_selected['date'], $kb_checkout_selected['year']));
            $datediff = strtotime($kb_hour_checkout) - strtotime($kb_hour_checkin);
            $total_hours = round($datediff / (60 * 60));
            $min_hours = $booking_product_details['min_hours'];
            $max_hours = $booking_product_details['max_hours'];
            $hrs_exceed = true;
            if (!empty($min_hours) && !empty($max_hours)) {
                if (((int) $total_hours >= (int) $min_hours) &&
                        ((int) $total_hours <= (int) $max_hours)) {
                    $hrs_exceed = false;
                }
                if ($hrs_exceed) {
                    $data['error'] = $this->module->l('Booking is not allowed as Min hours should be ', 'cart') . ' ' . $min_hours . ' ' . $this->module->l('and Max. hours should be ', 'cart') . ' ' . $max_hours;
                    return $data;
                }
            }
            $kb_date_rang = $this->kbDateRange($kb_hour_checkin, $kb_hour_checkout, '+1 hours', 'Y-m-d H:i');
            $kb_price = 0;
            // changes over
            $date_details = Tools::jsonDecode($booking_product_details['date_details'], true);
            $sort_from_date = array_column($date_details, 'from_date');
            array_multisort($sort_from_date, SORT_ASC, $date_details);
            if (empty($date_details)) {
                $data['error'] = $this->module->l('The booking is not available on the selected date', 'cart');
                return $data;
            }
            if (!empty($kb_date_rang)) {
                foreach ($kb_date_rang as $kb_date_rec) {
                    $kb_date_exist = 0;
                    foreach ($date_details as $rec_date) {
                        $from_date = strtotime($rec_date['from_date']);
//                        $to_date = strtotime($rec_date['to_date']);
                        $to_date = strtotime($rec_date['to_date'].' 23:59:59');
                        if (strtotime($kb_date_rec) >= $from_date && strtotime($kb_date_rec) <= $to_date) {
                            $kb_date_exist = 1;
                            $kb_price += $this->applyKbRule($id_product, $kb_date_rec, $kb_date_rec, $rec_date['price']);
                        }
                    }
                    if (!$kb_date_exist) {
                        $data['error'] = $this->module->l('The booking is not available on the selected date and time.', 'cart');
                    }
                }
            }
            if (!empty($qty)) {
                $available_qty = $this->getAvailableQuantityByProduct($id_product, $kb_hour_checkin, $kb_hour_checkout, (int) $qty, $id_hotel_room);
                if (!$available_qty) {
                    $data['error'] = $this->module->l('The item is not available in this quantity. Kindly adjusted the quantity.', 'cart');
                } else {
                    $price = $kb_price;
                    // changes by rishabh jain
                                                $price_with_tax = Tools::ps_round(
                                                    Product::getPriceStaticBookingProductPrice(
                                                        (float) $price,
                                                        (int)$id_product,
                                                        true,
                                                        null,
                                                        6,
                                                        null,
                                                        false,
                                                        true,
                                                        1
                                                    ),
                                                    (int) Configuration::get('PS_PRICE_DISPLAY_PRECISION')
                                                );
                                                
                                                
                                                // changes over
                    $data['success'] = true;
                    $data['price'] = Tools::convertPrice($price);
                    if ($this->is_display_tax_excl_price) {
                        $data['display_price'] = Tools::displayPrice(Tools::convertPrice($price));
                    } else {
                        $data['display_price'] = Tools::displayPrice(Tools::convertPrice($price_with_tax));
                    }
                }
            }
        }

        if (!isset($data['error'])) {
            if (!isset($data['success'])) {
                $data['error'] = $this->module->l('The booking is not available on the selected date', 'cart');
            }
        }
        return $data;
    }


    public function validateCheckInDate()
    {
        $data = array();
        $id_product = Tools::getValue('id_product');
        $product_type = Tools::getValue('product_type');
        $qty = (int) Tools::getValue('qty');
        $kbselected_date = Tools::getValue('kb_checkin_selected');
        $time_slot = Tools::getValue('time_slot');
        $booking_product_details = KbBookingProduct::getProductDetailsByID($id_product);
        if (!empty($booking_product_details)) {
            if ($booking_product_details['product_type'] == 'hotel_booking') {
                $data = $this->validateHotelCheckIn();
                return $data;
            } else if ($booking_product_details['product_type'] == 'hourly_rental') {
                $data = $this->validateHourlyRentalCheckIn();
                return $data;
            }
            $date_details = Tools::jsonDecode($booking_product_details['date_details'], true);
            if (!empty($date_details)) {
                foreach ($date_details as $rec_date) {
                    $from_date = strtotime($rec_date['from_date']);
                    $to_date = strtotime($rec_date['to_date']);
                    $kb_date = date('Y-m-d', mktime($kbselected_date['hours'], $kbselected_date['minutes'], $kbselected_date['seconds'], $kbselected_date['months'] + 1, $kbselected_date['date'], $kbselected_date['year']));
                    if ($booking_product_details['period_type'] == 'date_time') {
                        if (strtotime($kb_date) >= $from_date && strtotime($kb_date) <= $to_date) {
                            if (!empty($qty)) {
                                if (empty($time_slot)) {
                                    $data['error'] = $this->module->l('Kindly select the time slot.', 'cart');
                                    break;
                                }
                                if ($product_type == 'daily_rental') {
                                    $kbtime_slot = explode(' - ', $time_slot['time']);
                                    $kb_date_checkin = date('Y-m-d ' . $kbtime_slot[0], strtotime($kb_date));
                                    $kb_date_checkout = date('Y-m-d ' . $kbtime_slot[1], strtotime($kb_date));
                                    $available_qty = $this->getAvailableQuantityByProductAndTimeSlots($id_product, $kb_date_checkin, $kb_date_checkout, $qty, null, $time_slot['time']);
//                                    $available_qty = $this->getAvailableQuantityByProduct($id_product, $kb_date_checkin, $kb_date_checkout, $qty);
                                } else if ($product_type == 'appointment') {
                                    $available_qty = $this->getAvailableQuantityByProductAndTimeSlots($id_product, $kb_date, $kb_date, $qty, null, $time_slot['time']);
                                } else {
                                    $available_qty = $this->getAvailableQuantityByProductAndTimeSlots($id_product, $kb_date, $kb_date, $qty, null, $time_slot['time']);
//                                    $available_qty = $this->getAvailableQuantityByProduct($id_product, $kb_date, $kb_date, $qty);
                                }
                                // changes by rishabh jain
                                if (!$available_qty) {
                                    $data['error'] = $this->module->l('The item is not available in this quantity. Kindly adjusted the quantity.', 'cart');
                                    break;
                                } elseif ($available_qty) {
                                    $data['success'] = true;
                                    $price = $this->applyKbRule($id_product, $kb_date, $kb_date, $time_slot['price']);
                                    // changes by rishabh jain
                                    $price_with_tax = Tools::ps_round(
                                        Product::getPriceStaticBookingProductPrice(
                                            (float) $price,
                                            (int)$id_product,
                                            true,
                                            null,
                                            6,
                                            null,
                                            false,
                                            true,
                                            1
                                        ),
                                        (int) Configuration::get('PS_PRICE_DISPLAY_PRECISION')
                                    );
                                    // changes over
                                    $data['price'] = Tools::convertPrice($price);
                                    if ($this->is_display_tax_excl_price) {
                                        $data['display_price'] = Tools::displayPrice(Tools::convertPrice($price));
                                    } else {
                                        $data['display_price'] = Tools::displayPrice(Tools::convertPrice($price_with_tax));
                                    }
                                    break;
                                }
                            }
                        }
                    } else {
                        if ($booking_product_details['product_type'] == 'appointment') {
                            if (strtotime($kb_date) >= $from_date && strtotime($kb_date) <= $to_date) {
                                if (!empty($qty)) {
                                    $available_qty = $this->getAvailableQuantityByProduct($id_product, $kb_date, $kb_date, $qty);
                                    if (!$available_qty) {
                                        $data['error'] = $this->module->l('The item is not available in this quantity. Kindly adjusted the quantity.', 'cart');
                                        break;
                                    } elseif ($available_qty) {
                                        $data['success'] = true;
                                        $price = $this->applyKbRule($id_product, $kb_date, $kb_date, (float) $rec_date['price']);
                                        // changes by rishabh jain
                                                $price_with_tax = Tools::ps_round(
                                                    Product::getPriceStaticBookingProductPrice(
                                                        (float) $price,
                                                        (int)$id_product,
                                                        true,
                                                        null,
                                                        6,
                                                        null,
                                                        false,
                                                        true,
                                                        1
                                                    ),
                                                    (int) Configuration::get('PS_PRICE_DISPLAY_PRECISION')
                                                );
                                                
                                                
                                                // changes over
                                        $data['price'] = Tools::convertPrice($price);
                                        if ($this->is_display_tax_excl_price) {
                                            $data['display_price'] = Tools::displayPrice(Tools::convertPrice($price));
                                        } else {
                                            $data['display_price'] = Tools::displayPrice(Tools::convertPrice($price_with_tax));
                                        }
                                        break;
                                    }
                                }
                            }
                        } elseif ($product_type == 'daily_rental' ||
                                $product_type == 'hourly_rental') {
                            $kb_checkin_selected = Tools::getValue('kb_checkin_selected');
                            $kb_checkout_selected = Tools::getValue('kb_checkout_selected');
                            $kb_checkin_date = date('Y-m-d', mktime($kb_checkin_selected['hours'], $kb_checkin_selected['minutes'], $kb_checkin_selected['seconds'], $kb_checkin_selected['months'] + 1, $kb_checkin_selected['date'], $kb_checkin_selected['year']));
                            $kb_checkout_date = date('Y-m-d', mktime($kb_checkout_selected['hours'], $kb_checkout_selected['minutes'], $kb_checkout_selected['seconds'], $kb_checkout_selected['months'] + 1, $kb_checkout_selected['date'], $kb_checkout_selected['year']));
                            if (!empty($kb_checkout_date) && !empty($kb_checkin_date)) {
                                if ((strtotime($kb_checkin_date) >= $from_date && strtotime($kb_checkin_date) <= $to_date) &&
                                        (strtotime($kb_checkout_date) <= $to_date && strtotime($kb_checkout_date) >= $from_date)) {
                                    if ($product_type == 'daily_rental') {
                                        $datediff = strtotime($kb_checkout_date) - strtotime($kb_checkin_date);
                                        $total_days = round($datediff / (60 * 60 * 24)) + 1;
                                        $min_days = $booking_product_details['min_days'];
                                        $max_days = $booking_product_details['max_days'];
                                        $days_exceed = true;
                                        if (!empty($min_days) && !empty($max_days)) {
                                            if (((int) $total_days >= (int) $min_days) &&
                                                    ((int) $total_days <= (int) $max_days)) {
                                                $days_exceed = false;
                                            }
                                            if ($days_exceed) {
                                                $data['error'] = $this->module->l('Booking is not allowed as Min Days should be ', 'cart') . $min_days . $this->module->l(' and Max. days should be ', 'cart') . $max_days;
                                                break;
                                            }
                                        }

                                        if (!empty($qty)) {
                                            $available_qty = $this->getAvailableQuantityByProduct($id_product, $kb_checkin_date, $kb_checkout_date, $qty);
                                            if (!$available_qty) {
                                                $data['error'] = $this->module->l('The item is not available in this quantity.', 'cart');
                                                break;
                                            } elseif ($available_qty) {
                                                $data['success'] = true;
                                                $price = $this->applyKbRule($id_product, $kb_checkin_date, $kb_checkout_date, $rec_date['price']);
                                                // changes by rishabh jain
                                                $price_with_tax = Tools::ps_round(
                                                    Product::getPriceStaticBookingProductPrice(
                                                        (float) $price,
                                                        (int)$id_product,
                                                        true,
                                                        null,
                                                        6,
                                                        null,
                                                        false,
                                                        true,
                                                        1
                                                    ),
                                                    (int) Configuration::get('PS_PRICE_DISPLAY_PRECISION')
                                                );
                                                
                                                
                                                // changes over
                                                $data['price'] = Tools::convertPrice($price);
                                                if ($this->is_display_tax_excl_price) {
                                                    $data['display_price'] = Tools::displayPrice(Tools::convertPrice($price));
                                                } else {
                                                    $data['display_price'] = Tools::displayPrice(Tools::convertPrice($price_with_tax));
                                                }
                                                break;
                                            }
                                        }
                                    } elseif ($product_type == 'hourly_rental') {
                                        $kb_hour_checkin = date('Y-m-d H:i:s', mktime($kb_checkin_selected['hours'], $kb_checkin_selected['minutes'], $kb_checkin_selected['seconds'], $kb_checkin_selected['months'] + 1, $kb_checkin_selected['date'], $kb_checkin_selected['year']));
                                        $kb_hour_checkout = date('Y-m-d H:i:s', mktime($kb_checkout_selected['hours'], $kb_checkout_selected['minutes'], $kb_checkout_selected['seconds'], $kb_checkout_selected['months'] + 1, $kb_checkout_selected['date'], $kb_checkout_selected['year']));
                                        $datediff = strtotime($kb_hour_checkout) - strtotime($kb_hour_checkin);
                                        $total_hours = round($datediff / (60 * 60));
                                        $min_hours = $booking_product_details['min_hours'];
                                        $max_hours = $booking_product_details['max_hours'];
                                        $hrs_exceed = true;
                                        if (!empty($min_hours) && !empty($max_hours)) {
                                            if (((int) $total_hours >= (int) $min_hours) &&
                                                    ((int) $total_hours <= (int) $max_hours)) {
                                                $hrs_exceed = false;
                                            }
                                            if ($hrs_exceed) {
                                                $data['error'] = $this->module->l('Booking is not allowed as Min Hours should be ', 'cart') . $min_hours . $this->module->l(' and Max. Hours should be ', 'cart') . $max_hours;
                                                break;
                                            }
                                        }
                                        if (!empty($qty)) {
                                            $available_qty = $this->getAvailableQuantityByProduct($id_product, $kb_hour_checkin, $kb_hour_checkout, $qty);
                                            if (!$available_qty) {
                                                $data['error'] = $this->module->l('The item is not available in this quantity.', 'cart');
                                                break;
                                            } else {
                                                $kb_date_rang = $this->kbDateRange($kb_hour_checkin, $kb_hour_checkout, '+1 hours', 'Y-m-d H:i');
                                                $kb_price = 0;
                                                if (!empty($kb_date_rang)) {
                                                    foreach ($kb_date_rang as $kb_date_rec) {
                                                        $kb_price += $this->applyKbRule($id_product, $kb_date_rec, $kb_date_rec, $rec_date['price']);
                                                    }
                                                }
//                                                print_r($kb_price);
//                                                die;
                                                if ($product_type == 'hourly_rental') {
                                                    $price = $kb_price;
                                                }
                                                $data['success'] = true;
                                                // changes by rishabh jain
                                                $price_with_tax = Tools::ps_round(
                                                    Product::getPriceStaticBookingProductPrice(
                                                        (float) $price,
                                                        (int)$id_product,
                                                        true,
                                                        null,
                                                        6,
                                                        null,
                                                        false,
                                                        true,
                                                        1
                                                    ),
                                                    (int) Configuration::get('PS_PRICE_DISPLAY_PRECISION')
                                                );
                                                
                                                
                                                // changes over
                                                $data['price'] = Tools::convertPrice($price);
                                                if ($this->is_display_tax_excl_price) {
                                                    $data['display_price'] = Tools::displayPrice(Tools::convertPrice($price));
                                                } else {
                                                    $data['display_price'] = Tools::displayPrice(Tools::convertPrice($price_with_tax));
                                                }
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!isset($data['error'])) {
            if (!isset($data['success'])) {
                $data['error'] = $this->module->l('The booking is not available on the selected date', 'cart');
            }
        }

        return $data;
    }
    
    public function getAvailableQuantityByProduct($id_product, $checkin, $checkout, $qty, $id_room = null)
    {
        $used_qty = 0;
        $available_qty = 0;
        $id_cart = Context::getContext()->cart->id;
        $product_details = KbBookingProduct::getProductDetailsByID($id_product);
        if (!empty($product_details)) {
            $product_type = $product_details['product_type'];
            $product_qty = $product_details['quantity'];
            $period_type = $product_details['period_type'];
            //for appointment
            if ($product_type == 'appointment') {
                $checkin = date('Y-m-d 00:00:00', strtotime($checkin));
                $checkout = date('Y-m-d 23:59:59', strtotime($checkout));
                $order_placed = Db::getInstance()->executeS('SELECT c.*,d.is_cancelled FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart c INNER JOIN ' . _DB_PREFIX_ . 'kb_booking_product_order d on (d.id_cart=c.id_cart AND c.id_product=d.`id_product` AND c.id_customization=d.id_customization) WHERE c.id_product=' . (int) $id_product . ' AND c.check_in >= "' . pSQL($checkin) . '" AND c.check_out <="' . pSQL($checkout) . '"');
                if (!empty($order_placed)) {
                    foreach ($order_placed as $order) {
                        if ((int)$order['is_cancelled'] == 0) {
                            $used_qty += $order['qty'];
                        }
                    }
                }
                $cart_details = Db::getInstance()->executeS('SELECT c.* FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart c INNER JOIN ' . _DB_PREFIX_ . 'cart_product co on (co.id_cart=c.id_cart AND co.id_product=c.id_product) WHERE c.id_product=' . (int) $id_product . ' AND  c.check_in >= "' . pSQL($checkin) . '" AND c.check_out <="' . pSQL($checkout) . '" GROUP BY c.id_customization');
                if (!empty($cart_details)) {
                    foreach ($cart_details as $cart_key => $cart_data) {
                        $is_exist = false;
                        if (!empty($order_placed)) {
                            foreach ($order_placed as $order_key => $order_data) {
                                if ($order_data['id_cart'] == $cart_data['id_cart']) {
                                    $is_exist = true;
                                    break;
                                }
                            }
                        }
                        if (!$is_exist) {
                            $used_qty += $cart_data['qty'];
                        }
                    }
                }
//                if (!empty($cart_details)) {
//                    foreach ($cart_details as $cart_detail) {
//                        $used_qty += $cart_detail['qty'];
//                    }
                    
//                }
                $available_qty = $product_qty - $used_qty;
                if ($available_qty < 0) {
                    $available_qty = 0;
                }
                if ($available_qty <= 0) {
                    return false;
                } elseif ($qty > $available_qty) {
                    return false;
                }

                return true;
            } elseif ($product_type == 'daily_rental') {
                $checkin = date('Y-m-d', strtotime($checkin));
                $checkout = date('Y-m-d', strtotime($checkout));
                $date_range = $this->kbDateRange($checkin, $checkout, '+1 day', 'Y-m-d', 'hourly');
                $datediff = strtotime($checkout) - strtotime($checkin);
                $total_days = (int) round($datediff / (60 * 60 * 24)) + 1;
                $flag_days = $total_days;
                if (!empty($date_range)) {
                    foreach ($date_range as $range) {
                        $used_qty = 0;
                        $kb_range_checkin = date('Y-m-d', strtotime($range));
                        $order_placed = Db::getInstance()->executeS('SELECT c.*,d.is_cancelled FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart c INNER JOIN ' . _DB_PREFIX_ . 'kb_booking_product_order d on (d.id_cart=c.id_cart AND c.id_product=d.`id_product` AND c.id_customization=d.id_customization) WHERE c.id_product=' . (int) $id_product . ' AND ("' . pSQL($kb_range_checkin) . '" between c.check_in and c.check_out)');
                        if (!empty($order_placed)) {
                            foreach ($order_placed as $order) {
                                if ((int)$order['is_cancelled'] == 0) {
                                    $used_qty += $order['qty'];
                                }
                            }
                        }
//                        if (!empty($id_cart)) {
                        $cart_details = Db::getInstance()->executeS('SELECT c.* FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart c INNER JOIN ' . _DB_PREFIX_ . 'cart_product co on (co.id_cart=c.id_cart AND co.id_product=c.id_product) WHERE c.id_product=' . (int) $id_product . ' AND  ("' . pSQL($kb_range_checkin) . '" between c.check_in and c.check_out) GROUP BY c.id_customization');
                        if (!empty($cart_details)) {
                            foreach ($cart_details as $cart_key => $cart_data) {
                                $is_exist = false;
                                if (!empty($order_placed)) {
                                    foreach ($order_placed as $order_key => $order_data) {
                                        if ($order_data['id_cart'] == $cart_data['id_cart']) {
                                            $is_exist = true;
                                            break;
                                        }
                                    }
                                }
                                if (!$is_exist) {
                                    $used_qty += $cart_data['qty'];
                                }
                            }
                        }
//                        if (!empty($cart_details)) {
//                            foreach ($cart_details as $cart_detail) {
//                                $used_qty += $cart_detail['qty'];
//                            }
//                        }
//                        }
                        $available_qty = (int) $product_qty - (int) $used_qty;
                        if ((int) $available_qty < 0) {
                            $available_qty = 0;
                        }
                        if ((int) $available_qty < (int) $qty) {
                            $flag_days--;
                        }
                    }
                }
                if ($flag_days < $total_days) {
                    return false;
                }
                return true;
            } elseif ($product_type == 'hourly_rental') {
                $date_range = $this->kbDateRange($checkin, $checkout, '+1 hours', 'Y-m-d H:i', 'hourly');
                $datediff = strtotime($checkout) - strtotime($checkin);
                $total_hours = (int) round($datediff / (60 * 60 ));
                $flag_hours = $total_hours;
                if (!empty($date_range)) {
                    foreach ($date_range as $range) {
                        $used_qty = 0;
//                        $kb_range_checkin = date('Y-m-d H:i', strtotime($range));
                        $kb_range_checkin = date('Y-m-d H:i', strtotime($range));
                        
                        $order_placed = Db::getInstance()->executeS('SELECT c.*,d.is_cancelled FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart c INNER JOIN ' . _DB_PREFIX_ . 'kb_booking_product_order d on (d.id_cart=c.id_cart AND c.id_product=d.`id_product` AND c.id_customization=d.id_customization) WHERE c.id_product=' . (int) $id_product . ' AND ("' . pSQL($kb_range_checkin) . '" between c.check_in and c.check_out)');
                        if (!empty($order_placed)) {
                            foreach ($order_placed as $order) {
                                if ((int)$order['is_cancelled'] == 0) {
                                    $used_qty += $order['qty'];
                                }
                            }
                        }
                        $cart_details = Db::getInstance()->executeS('SELECT c.* FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart c INNER JOIN ' . _DB_PREFIX_ . 'cart_product co on (co.id_cart=c.id_cart AND co.id_product=c.id_product) WHERE c.id_product=' . (int) $id_product . ' AND  ("' . pSQL($kb_range_checkin) . '" between c.check_in and c.check_out) GROUP BY c.id_customization');
//                        if (!empty($cart_details)) {
//                            foreach ($cart_details as $cart_detail) {
//                                $used_qty += $cart_detail['qty'];
//                            }
//                        }
                        if (!empty($cart_details)) {
                            foreach ($cart_details as $cart_key => $cart_data) {
                                $is_exist = false;
                                if (!empty($order_placed)) {
                                    foreach ($order_placed as $order_key => $order_data) {
                                        if ($order_data['id_cart'] == $cart_data['id_cart']) {
                                            $is_exist = true;
                                            break;
                                        }
                                    }
                                }
                                if (!$is_exist) {
                                    $used_qty += $cart_data['qty'];
                                }
                            }
                        }
                        $available_qty = (int) $product_qty - (int) $used_qty;
                        if ((int) $available_qty < 0) {
                            $available_qty = 0;
                        }
                        if ((int) $available_qty < (int) $qty) {
                            $flag_hours--;
                        }
                    }
                }

                if ($flag_hours < $total_hours) {
                    return false;
                }
                return true;
            } elseif ($product_type == 'hotel_booking') {
                $product_qty = 0;
                if (!empty($id_room)) {
                    $room_details = KbBookingProduct::getHotelProductRoomsByID($product_details['id_booking_product'], $id_room);
                    if (!empty($room_details)) {
                        $product_qty = (int) $room_details[0]['room_quantity'];
                    }
                }
                $date_range = $this->kbDateRange($checkin, $checkout);
                $datediff = strtotime($checkout) - strtotime($checkin);
                $total_days = (int) round($datediff / (60 * 60 * 24));
                $flag_days = $total_days;
                if (!empty($date_range)) {
                    foreach ($date_range as $key => $range) {
                        $used_qty = 0;
                        if ($key == 0) {
                            $kb_range_checkin = date('Y-m-d 01:00:00', strtotime($range));
                        } else {
                            $kb_range_checkin = date('Y-m-d  23:59:59', strtotime($range));
                        }
                        $order_placed = Db::getInstance()->executeS('SELECT c.*,d.is_cancelled FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart c INNER JOIN ' . _DB_PREFIX_ . 'kb_booking_product_order d on (d.id_cart=c.id_cart AND c.id_product=d.`id_product` AND c.id_customization=d.id_customization) WHERE c.id_product=' . (int) $id_product . ' AND c.id_room=' . (int) $id_room . ' AND ("' . pSQL($kb_range_checkin) . '" between c.check_in and c.check_out)');
                        if (!empty($order_placed)) {
                            foreach ($order_placed as $order) {
                                if ((int)$order['is_cancelled'] == 0) {
                                    $used_qty += $order['qty'];
                                }
                            }
                        }
                        $cart_details = Db::getInstance()->executeS('SELECT c.* FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart c INNER JOIN ' . _DB_PREFIX_ . 'cart_product co on (co.id_cart=c.id_cart AND co.id_product=c.id_product) WHERE c.id_product=' . (int) $id_product . ' AND  c.id_room=' . (int) $id_room . ' AND ("' . pSQL($kb_range_checkin) . '" between c.check_in and c.check_out) GROUP BY c.id_customization');
                        if (!empty($cart_details)) {
                            foreach ($cart_details as $cart_key => $cart_data) {
                                $is_exist = false;
                                if (!empty($order_placed)) {
                                    foreach ($order_placed as $order_key => $order_data) {
                                        if ($order_data['id_cart'] == $cart_data['id_cart']) {
                                            $is_exist = true;
                                            break;
                                        }
                                    }
                                }
                                if (!$is_exist) {
                                    $used_qty += $cart_data['qty'];
                                }
                            }
                        }
                        $available_qty = (int) $product_qty - (int) $used_qty;
                        if ((int) $available_qty < 0) {
                            $available_qty = 0;
                        }
                        if ((int) $available_qty < (int) $qty) {
                            $flag_days--;
                        }
                    }
                }
                if ($flag_days < $total_days) {
                    return false;
                }
                return true;
            }
        }
        if (!empty($id_cart)) {
            if (!empty($product_details)) {
                if ($product_details['id_product'] == $id_product) {
                    foreach ($cart_details as $cart) {
                        if (!empty($id_room)) {
                            if ($id_room != $cart['id_room']) {
                                continue;
                            }
                        }
                        $cart_checkout = strtotime($cart['check_out']);
                        if (strtotime($checkin) <= $cart_checkout) {
                            $used_qty += $cart['quantity'];
                        }
                    }
                }
            }
        }
        if (!empty($product_details)) {
            if ($product_details['product_type'] == 'hotel_booking') {
                if (!empty($id_room)) {
                    $room_details = KbBookingProduct::getHotelProductRoomsByID($product_details['id_booking_product'], $id_room);
                    if (!empty($room_details)) {
                        $available_qty = $room_details[0]['room_quantity'] - $used_qty;
                    }
                }
            } else {
                $available_qty = $product_details['quantity'] - $used_qty;
            }
        }
        if ($available_qty < 0) {
            $available_qty = 0;
        }
        return $available_qty;
    }
    public function getAvailableQuantityByProductAndTimeSlots($id_product, $checkin, $checkout, $qty, $id_room = null, $time_slot = null)
    {
        $used_qty = 0;
        $available_qty = 0;
        $id_cart = Context::getContext()->cart->id;
        $product_details = KbBookingProduct::getProductDetailsByID($id_product);
        if (!empty($product_details)) {
            $product_type = $product_details['product_type'];
            $product_qty = $product_details['quantity'];
            $period_type = $product_details['period_type'];
            //for appointment
            if ($product_type == 'appointment') {
                $time_slot_filter = '';
                if (!empty($time_slot)) {
                    $time_slot_filter = 'and c.time_slot = "'.$time_slot.'"';
                }
                $checkin = date('Y-m-d 00:00:00', strtotime($checkin));
                $checkout = date('Y-m-d 23:59:59', strtotime($checkout));
                $order_placed = Db::getInstance()->executeS('SELECT c.*,d.is_cancelled FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart c INNER JOIN ' . _DB_PREFIX_ . 'kb_booking_product_order d on (d.id_cart=c.id_cart AND c.id_product=d.`id_product` AND c.id_customization=d.id_customization) WHERE c.id_product=' . (int) $id_product . ' AND c.check_in >= "' . pSQL($checkin) . '" AND c.check_out <="' . pSQL($checkout) . '"'.$time_slot_filter);
                if (!empty($order_placed)) {
                    foreach ($order_placed as $order) {
                        if ((int)$order['is_cancelled'] == 0) {
                            $used_qty += $order['qty'];
                        }
                    }
                }
                $sqlCarts='
                    SELECT c.* 
                    FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart c 
                    INNER JOIN ' . _DB_PREFIX_ . 'cart_product co on (co.id_cart=c.id_cart AND co.id_product=c.id_product) 
                    WHERE 
                        c.id_product=' . (int) $id_product . ' AND  
                        c.check_in >= "' . pSQL($checkin) . '" AND 
                        c.check_out <="' . pSQL($checkout) . '"'.$time_slot_filter.' AND 
                        co.date_add >= NOW() - INTERVAL 1 DAY
                    GROUP BY c.id_customization';
                $cart_details = Db::getInstance()->executeS($sqlCarts);
                if (!empty($cart_details)) {
                    foreach ($cart_details as $cart_key => $cart_data) {
                        $is_exist = false;
                        if (!empty($order_placed)) {
                            foreach ($order_placed as $order_key => $order_data) {
                                if ($order_data['id_cart'] == $cart_data['id_cart']) {
                                    $is_exist = true;
                                    break;
                                }
                            }
                        }
                        if (!$is_exist) {
                            $used_qty += $cart_data['qty'];
                        }
                    }
                }
                $available_qty = $product_qty - $used_qty;
                if ($available_qty < 0) {
                    $available_qty = 0;
                }
                if ($available_qty <= 0) {
                    return false;
                } elseif ($qty > $available_qty) {
                    return false;
                }

                return true;
            } elseif ($product_type == 'daily_rental') {
                $time_slot_filter = '';
                if (!empty($time_slot)) {
                    $time_slot_filter = 'and c.time_slot = "'.$time_slot.'"';
                }
                $checkin = date('Y-m-d', strtotime($checkin));
                $checkout = date('Y-m-d', strtotime($checkout));
                $date_range = $this->kbDateRange($checkin, $checkout, '+1 day', 'Y-m-d', 'hourly');
                $datediff = strtotime($checkout) - strtotime($checkin);
                $total_days = (int) round($datediff / (60 * 60 * 24)) + 1;
                $flag_days = $total_days;
                if (!empty($date_range)) {
                    foreach ($date_range as $range) {
                        $used_qty = 0;
                        $kb_range_checkin = date('Y-m-d', strtotime($range));
                        $order_placed = Db::getInstance()->executeS('SELECT c.*,d.is_cancelled FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart c INNER JOIN ' . _DB_PREFIX_ . 'kb_booking_product_order d on (d.id_cart=c.id_cart AND c.id_product=d.`id_product` AND c.id_customization=d.id_customization) WHERE c.id_product=' . (int) $id_product . ' AND ("' . pSQL($kb_range_checkin) . '" between c.check_in and c.check_out)'.$time_slot_filter);
                        if (!empty($order_placed)) {
                            foreach ($order_placed as $order) {
                                if ((int)$order['is_cancelled'] == 0) {
                                    $used_qty += $order['qty'];
                                }
                            }
                        }
                        $cart_details = Db::getInstance()->executeS('SELECT c.* FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart c INNER JOIN ' . _DB_PREFIX_ . 'cart_product co on (co.id_cart=c.id_cart AND co.id_product=c.id_product) WHERE c.id_product=' . (int) $id_product . ' AND  ("' . pSQL($kb_range_checkin) . '" between c.check_in and c.check_out)'.$time_slot_filter.' GROUP BY c.id_customization');
                        if (!empty($cart_details)) {
                            foreach ($cart_details as $cart_key => $cart_data) {
                                $is_exist = false;
                                if (!empty($order_placed)) {
                                    foreach ($order_placed as $order_key => $order_data) {
                                        if ($order_data['id_cart'] == $cart_data['id_cart']) {
                                            $is_exist = true;
                                            break;
                                        }
                                    }
                                }
                                if (!$is_exist) {
                                    $used_qty += $cart_data['qty'];
                                }
                            }
                        }
                    //    }
                        $available_qty = (int) $product_qty - (int) $used_qty;
                        if ((int) $available_qty < 0) {
                            $available_qty = 0;
                        }
                        if ((int) $available_qty < (int) $qty) {
                            $flag_days--;
                        }
                    }
                }
                if ($flag_days < $total_days) {
                    return false;
                }
                return true;
            } elseif ($product_type == 'hourly_rental') {
                $date_range = $this->kbDateRange($checkin, $checkout, '+1 hours', 'Y-m-d H:i', 'hourly');
                $datediff = strtotime($checkout) - strtotime($checkin);
                $total_hours = (int) round($datediff / (60 * 60 ));
                $flag_hours = $total_hours;
                if (!empty($date_range)) {
                    foreach ($date_range as $range) {
                        $used_qty = 0;
                        $kb_range_checkin = date('Y-m-d H:i', strtotime($range));
                        $order_placed = Db::getInstance()->executeS('SELECT c.*,d.is_cancelled FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart c INNER JOIN ' . _DB_PREFIX_ . 'kb_booking_product_order d on (d.id_cart=c.id_cart AND c.id_product=d.`id_product` AND c.id_customization=d.id_customization) WHERE c.id_product=' . (int) $id_product . ' AND ("' . pSQL($kb_range_checkin) . '" between c.check_in and c.check_out)');
                        if (!empty($order_placed)) {
                            foreach ($order_placed as $order) {
                                if ((int)$order['is_cancelled'] == 0) {
                                    $used_qty += $order['qty'];
                                }
                            }
                        }
                    //    if (!empty($id_cart)) {
                        $cart_details = Db::getInstance()->executeS('SELECT c.* FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart c INNER JOIN ' . _DB_PREFIX_ . 'cart_product co on (co.id_cart=c.id_cart AND co.id_product=c.id_product) WHERE c.id_product=' . (int) $id_product . ' AND  ("' . pSQL($kb_range_checkin) . '" between c.check_in and c.check_out) GROUP BY c.id_customization');
                        if (!empty($cart_details)) {
                            foreach ($cart_details as $cart_key => $cart_data) {
                                $is_exist = false;
                                if (!empty($order_placed)) {
                                    foreach ($order_placed as $order_key => $order_data) {
                                        if ($order_data['id_cart'] == $cart_data['id_cart']) {
                                            $is_exist = true;
                                            break;
                                        }
                                    }
                                }
                                if (!$is_exist) {
                                    $used_qty += $cart_data['qty'];
                                }
                            }
                        }
                    //    }
                        $available_qty = (int) $product_qty - (int) $used_qty;
                        if ((int) $available_qty < 0) {
                            $available_qty = 0;
                        }
                        if ((int) $available_qty < (int) $qty) {
                            $flag_hours--;
                        }
                    }
                }

                if ($flag_hours < $total_hours) {
                    return false;
                }
                return true;
            } elseif ($product_type == 'hotel_booking') {
                $product_qty = 0;
                if (!empty($id_room)) {
                    $room_details = KbBookingProduct::getHotelProductRoomsByID($product_details['id_booking_product'], $id_room);
                    if (!empty($room_details)) {
                        $product_qty = (int) $room_details[0]['room_quantity'];
                    }
                }
                $date_range = $this->kbDateRange($checkin, $checkout);
                $datediff = strtotime($checkout) - strtotime($checkin);
                $total_days = (int) round($datediff / (60 * 60 * 24));
                $flag_days = $total_days;
                if (!empty($date_range)) {
                    foreach ($date_range as $key => $range) {
                        $used_qty = 0;
                        if ($key == 0) {
                            $kb_range_checkin = date('Y-m-d 01:00:00', strtotime($range));
                        } else {
                            $kb_range_checkin = date('Y-m-d  23:59:59', strtotime($range));
                        }
                        $order_placed = Db::getInstance()->executeS('SELECT c.*,d.is_cancelled FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart c INNER JOIN ' . _DB_PREFIX_ . 'kb_booking_product_order d on (d.id_cart=c.id_cart AND c.id_product=d.`id_product` AND c.id_customization=d.id_customization) WHERE c.id_product=' . (int) $id_product . ' AND c.id_room=' . (int) $id_room . ' AND ("' . pSQL($kb_range_checkin) . '" between c.check_in and c.check_out)');
                        if (!empty($order_placed)) {
                            foreach ($order_placed as $order) {
                                if ((int)$order['is_cancelled'] == 0) {
                                    $used_qty += $order['qty'];
                                }
                            }
                        }
                        $cart_details = Db::getInstance()->executeS('SELECT c.* FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart c INNER JOIN ' . _DB_PREFIX_ . 'cart_product co on (co.id_cart=c.id_cart AND co.id_product=c.id_product) WHERE c.id_product=' . (int) $id_product . ' AND  c.id_room=' . (int) $id_room . ' AND ("' . pSQL($kb_range_checkin) . '" between c.check_in and c.check_out) GROUP BY c.id_customization');
                        if (!empty($cart_details)) {
                            foreach ($cart_details as $cart_key => $cart_data) {
                                $is_exist = false;
                                if (!empty($order_placed)) {
                                    foreach ($order_placed as $order_key => $order_data) {
                                        if ($order_data['id_cart'] == $cart_data['id_cart']) {
                                            $is_exist = true;
                                            break;
                                        }
                                    }
                                }
                                if (!$is_exist) {
                                    $used_qty += $cart_data['qty'];
                                }
                            }
                        }
                        $available_qty = (int) $product_qty - (int) $used_qty;
                        if ((int) $available_qty < 0) {
                            $available_qty = 0;
                        }
                        if ((int) $available_qty < (int) $qty) {
                            $flag_days--;
                        }
                    }
                }
                if ($flag_days < $total_days) {
                    return false;
                }
                return true;
            }
        }
        if (!empty($id_cart)) {
            if (!empty($product_details)) {
                if ($product_details['id_product'] == $id_product) {
                    foreach ($cart_details as $cart) {
                        if (!empty($id_room)) {
                            if ($id_room != $cart['id_room']) {
                                continue;
                            }
                        }
                        $cart_checkout = strtotime($cart['check_out']);
                        if (strtotime($checkin) <= $cart_checkout) {
                            $used_qty += $cart['quantity'];
                        }
                    }
                }
            }
        }
        if (!empty($product_details)) {
            if ($product_details['product_type'] == 'hotel_booking') {
                if (!empty($id_room)) {
                    $room_details = KbBookingProduct::getHotelProductRoomsByID($product_details['id_booking_product'], $id_room);
                    if (!empty($room_details)) {
                        $available_qty = $room_details[0]['room_quantity'] - $used_qty;
                    }
                }
            } else {
                $available_qty = $product_details['quantity'] - $used_qty;
            }
        }
        if ($available_qty < 0) {
            $available_qty = 0;
        }
        return $available_qty;
    }


    private function checkRuleisApplied($id_product, $check_date)
    {
        $price_rules = KbBookingPriceRule::isPriceRuleApplicable($id_product);
        if (!empty($price_rules)) {
            foreach ($price_rules as $rule) {
                if ($rule['date_selection'] == 'date_range') {
                    if (strtotime($check_date) >= strtotime($rule['start_date'])
                        && strtotime($check_date) <= strtotime($rule['end_date'])) {
                        return $rule;
                    }
                } else {
                    if (strtotime($check_date) == strtotime($rule['particular_date'])) {
                        return $rule;
                    }
                }
            }
        }

        return false;
    }
    
    public function applyKbRule($id_product, $start = null, $end = null, $price = null)
    {
        $kb_price = 0.0;
        if (!empty($start) && !empty($end)) {
            if (Tools::getValue('product_type') == 'daily_rental') {
                $kb_date_rang = $this->kbDateRange($start, $end, '+1 day', 'Y-m-d', 'hourly');
            } else {
                $kb_date_rang = $this->kbDateRange($start, $end);
            }
            if (!empty($kb_date_rang)) {
                foreach ($kb_date_rang as $kb_rang) {
                    $rule_applied = $this->checkRuleisApplied($id_product, $kb_rang);
                    if (!empty($rule_applied)) {
                        $reduction = $rule_applied['reduction'];
                        $kb_reduce_price = $price;
                        if ($rule_applied['reduction_type'] == 'percentage') {
                            $kb_reduce_price = $price - ($reduction / 100 * $price);
                        } else {
                            $kb_reduce_price = $price - $reduction;
                        }
                        if ($kb_reduce_price < 0) {
                            $kb_reduce_price = 0;
                        }
                        $kb_price += $kb_reduce_price;
                    } else {
                        $kb_price += $price;
                    }
                }
            }
        }
        return $kb_price;
    }
    
    private function kbDateRange($first, $last, $step = '+1 day', $output_format = 'Y-m-d', $type = null)
    {
        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);
        if ((Tools::getValue('period_type') == 'date_time') || (!empty($type) && $type == 'hourly')) {
            while ($current <= $last) {
                $dates[] = date($output_format, $current);
                $current = strtotime($step, $current);
            }
        } else {
            if ($current == $last) {
                $dates[] = date($output_format, $current);
                return $dates;
            }
            while ($current < $last) {
                $dates[] = date($output_format, $current);
                $current = strtotime($step, $current);
            }
        }
        return $dates;
    }
    
    public function addProductInCart()
    {
        $id_product = Tools::getValue('id_product');
        $id_booking_product = Tools::getValue('id_booking_product');
        $product_type = Tools::getValue('product_type');
        $period_type = Tools::getValue('period_type');
        $service_type = Tools::getValue('service_type');
        $price = Tools::getValue('price');
        $qty = Tools::getValue('qty');
        $time_slot_array = array();
        $time_slot = '';
        if (Tools::getIsset('time_slot')) {
            $time_slot_array = Tools::getValue('time_slot');
            if (isset($time_slot_array['time'])) {
                $time_slot = $time_slot_array['time'];
            }
        }
        $kb_checkin_selected = Tools::getValue('kb_checkin_selected');
        $kb_checkout_selected = Tools::getValue('kb_checkout_selected');
        $room = Tools::getValue('id_hotel_room');
        $rec_data = Tools::getValue('time_slot');
        $data = array();
        if (empty($room)) {
            $room = '';
        }

        if (!empty($id_product) && !empty($id_booking_product)) {
            $validate = $this->validateCheckInDate();
            if (isset($validate['success'])) {
                $check_in = '';
                $check_out = '';
                $price = $validate['price'];
                $check_in = date('Y-m-d', mktime($kb_checkin_selected['hours'], $kb_checkin_selected['minutes'], $kb_checkin_selected['seconds'], $kb_checkin_selected['months'] + 1, $kb_checkin_selected['date'], $kb_checkin_selected['year']));
                if ($product_type != 'appointment') {
                    $check_out = date('Y-m-d', mktime($kb_checkout_selected['hours'], $kb_checkout_selected['minutes'], $kb_checkout_selected['seconds'], $kb_checkout_selected['months'] + 1, $kb_checkout_selected['date'], $kb_checkout_selected['year']));
                }

                if ($product_type == 'hourly_rental') {
                    if ($period_type == 'date') {
                        $check_in = date('Y-m-d H:i:s', mktime($kb_checkin_selected['hours'], $kb_checkin_selected['minutes'], $kb_checkin_selected['seconds'], $kb_checkin_selected['months'] + 1, $kb_checkin_selected['date'], $kb_checkin_selected['year']));
                        $check_out = date('Y-m-d H:i:s', mktime($kb_checkout_selected['hours'], $kb_checkout_selected['minutes'], $kb_checkout_selected['seconds'], $kb_checkout_selected['months'] + 1, $kb_checkout_selected['date'], $kb_checkout_selected['year']));
                    }
                }
                $response = array();
                $pro_obj = new Product((int) $id_product);
                if (!$this->context->cart->id && isset($_COOKIE[$this->context->cookie->getName()])) {
                    $this->context->cart->add();
                    $this->context->cookie->id_cart = (int) $this->context->cart->id;
                }

                if (!$field_ids = $pro_obj->getCustomizationFieldIds()) {
                    $response['error'] = $this->module->l('Unable to add product to cart, please try again.', 'cart');
                    echo json_encode($response);
                    die;
                }
                $authorized_text_fields = array();
                foreach ($field_ids as $field_id) {
                    if ($field_id['type'] == Product::CUSTOMIZE_TEXTFIELD) {
                        $authorized_text_fields[(int) $field_id['id_customization_field']] = 'textField' . (int) $field_id['id_customization_field'];
                    }
                }

                $indexes = array_flip($authorized_text_fields);
                ksort($indexes);
                $counter = 1;
                $is_added = false;
                $id_customization = 0;
                $kbcheck_in = $check_in;
                $kbcheck_out = $check_out;
                if ($product_type == 'daily_rental') {
                    if ($period_type == 'date') {
                        $check_out = date('Y-m-d 23:59:59', strtotime($check_out));
                    } else {
                        $check_out = date('Y-m-d 23:59:59', strtotime($check_in));
                    }
                }
                foreach ($indexes as $field) {
                    $value = '';
                    if ($period_type != 'date') {
                        $kbcheck_in = date('d/m/Y', strtotime($check_in)) . ' ' . str_replace(' - ', '-', $rec_data)['time'];
                    }
                    if ($product_type == 'appointment') {
                        $check_out = date('Y-m-d 23:59:59', strtotime($check_out));
                        if ($counter == 1) {
                            $value = $kbcheck_in;
                            $check_out = $kbcheck_in;
                        } elseif ($counter == 2) {
                            $service = '';
                            if ($service_type == 'home_service') {
                                $service = $this->module->l('Home', 'cart');
                            } elseif ($service_type == 'branch') {
                                $service = $this->module->l('Branch', 'cart');
                            }
                            $value = $service;
                        }
                    } elseif ($product_type == 'daily_rental') {
                        if ($counter == 1) {
                            $value = $kbcheck_in;
                        } elseif ($counter == 2) {
                            if ($period_type == 'date') {
                                $value = $kbcheck_out;
                            } else {
                                $value = '';
                            }
                        } elseif ($counter == 3) {
                            if ($period_type == 'date') {
                                $datediff = strtotime($kbcheck_out) - strtotime($check_in);
                                $total_days = round($datediff / (60 * 60 * 24));
                                $value = $total_days + 1;
                            } else {
                                $value = 1;
                            }
                        }
                    } elseif ($product_type == 'hourly_rental') {
                        if ($counter == 1) {
                            $value = $kbcheck_in;
                        } elseif ($counter == 2) {
                            if ($period_type == 'date') {
                                $value = $check_out;
                            } else {
                                $value = '';
                            }
                        } elseif ($counter == 3) {
                            if ($period_type == 'date') {
                                $datediff = strtotime($check_out) - strtotime($check_in);
                                $total_hours = round($datediff / (60 * 60));
                                $value = $total_hours;
                            } else {
                                $time_slot = Tools::getValue('time_slot');
                                if (!empty($time_slot)) {
                                    $time = $time_slot['time'];
                                    $time_array = explode(' - ', $time);
                                    if (isset($time_array[0]) && isset($time_array[1])) {
                                        $datediff = strtotime($time_array[1]) - strtotime($time_array[0]);
                                        $total_hours = round($datediff / (60 * 60));
                                        $value = $total_hours;
                                    }
                                }
                            }
                        }
                    } elseif ($product_type == 'hotel_booking') {
                        $room_info = KbBookingProduct::getHotelProductRoomsByID($id_booking_product, $room);
                        $room_type = '';
                        $room_category = '';
                        if (!empty($room_info)) {
                            $room_type = KbBookingRoomType::getAvailableRoomTypeByID($room_info[0]['id_room_type']);
                            $room_category = KbBookingRoomCategory::getRoomCategoryNameByID($room_info[0]['id_room_category']);
                        }
                        if ($counter == 1) {
                            $value = $check_in;
                        } elseif ($counter == 2) {
                            $value = $check_out;
                        } elseif ($counter == 3) {
                            $value = (!empty($room_type)) ? $room_type['room_name'] : '';
                        } elseif ($counter == 4) {
                            $value = $room_category;
                        } elseif ($counter == 5) {
                            $datediff = strtotime($check_out) - strtotime($check_in);
                            $total_hours = round($datediff / (60 * 60 * 24));
                            $value = $total_hours;
                        }
                    }
                    $is_added = $this->context->cart->addTextFieldToProduct($pro_obj->id, $field, Product::CUSTOMIZE_TEXTFIELD, $value);
                    $id_customization = Db::getInstance()->getValue('SELECT id_customization FROM ' . _DB_PREFIX_ . 'customized_data ORDER BY id_customization DESC');
                    $counter++;
                }
                if ($is_added) {
                    if (!empty($id_customization)) {
                        Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'kb_booking_product_cart set id_room=' . (int) $room . ',check_out="' . pSQL($check_out) . '",check_in="' . pSQL($check_in) . '", id_customization=' . (int) $id_customization . ',id_cart=' . (int) $this->context->cart->id . ',id_product=' . (int) $id_product . ', price="' . pSQL($price) . '",qty=' . (int) $qty . ', date_add=now(),date_upd=now(), time_slot="' . pSQL($time_slot).'"');
                        $response['id_customization'] = $id_customization;
                    }
                    $response['success'] = true;
                    echo json_encode($response);
                    die;
                }
            } else {
                $response['error'] = (isset($validate['error'])) ? $validate['error'] : $this->module->l('The booking is not available on the selected date', 'cart');
                echo json_encode($response);
                die;
            }
        }
    }
}
