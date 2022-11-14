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

class KbbookingcalendarOverride extends Kbbookingcalendar
{
    public function hookDisplayReserva($params)
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
}
