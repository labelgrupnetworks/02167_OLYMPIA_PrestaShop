<?php

use PrestaShop\PrestaShop\Adapter\ServiceLocator;
class Cart extends CartCore
{
    
    /*
    * module: loyaltyrewardpoints
    * date: 2022-11-10 11:23:49
    * version: 2.2.0
    */
    public function removeCartRule($id_cart_rule, bool $useOrderPrices = false)
    {
        include_once(_PS_MODULE_DIR_ . '/loyaltyrewardpoints/lib/bootstrap.php');
        LRPDiscountHelper::setPointsRedeem(0);
        return parent::removeCartRule($id_cart_rule);
    }
    /*
    * module: onepagecheckoutps
    * date: 2022-11-10 12:21:35
    * version: 4.1.5
    */
    public function getTotalShippingCost($delivery_option = null, $use_tax = true, Country $default_country = null)
    {
        if (version_compare(_PS_VERSION_, '1.7.4.0') < 0) {
            static $_total_shipping;
            $opc = Module::getInstanceByName('onepagecheckoutps');
            if (Validate::isLoadedObject($opc)
                && Context::getContext()->customer->isLogged()
                && (int) Context::getContext()->customer->id === (int) $opc->config_vars['OPC_ID_CUSTOMER']
            ) {
                $_total_shipping = null;
            }
            if (null === $_total_shipping) {
                if (isset(Context::getContext()->cookie->id_country)) {
                    $default_country = new Country(Context::getContext()->cookie->id_country);
                }
                if (is_null($delivery_option)) {
                    $delivery_option = $this->getDeliveryOption($default_country, false, false);
                }
                $_total_shipping = array(
                    'with_tax' => 0,
                    'without_tax' => 0,
                );
                $delivery_option_list = $this->getDeliveryOptionList($default_country);
                foreach ($delivery_option as $id_address => $key) {
                    if (!isset($delivery_option_list[$id_address])
                        || !isset($delivery_option_list[$id_address][$key])
                    ) {
                        continue;
                    }
                    $_total_shipping['with_tax'] += $delivery_option_list[$id_address][$key]['total_price_with_tax'];
                    $_total_shipping['without_tax'] += $delivery_option_list[$id_address][$key]['total_price_without_tax'];
                }
            }
            return ($use_tax) ? $_total_shipping['with_tax'] : $_total_shipping['without_tax'];
        }
        return parent::getTotalShippingCost($delivery_option, $use_tax, $default_country);
    }
    /*
    * module: onepagecheckoutps
    * date: 2022-11-10 12:21:35
    * version: 4.1.5
    */
    public function getDeliveryOptionList(Country $default_country = null, $flush = false)
    {
        if (version_compare(_PS_VERSION_, '1.7.4.0') < 0) {
            $opc = Module::getInstanceByName('onepagecheckoutps');
            if (Validate::isLoadedObject($opc)
                && Context::getContext()->customer->isLogged()
                && (int) Context::getContext()->customer->id === (int) $opc->config_vars['OPC_ID_CUSTOMER']
            ) {
                $flush = true;
            }
        }
        return parent::getDeliveryOptionList($default_country, $flush);
    }
   
    /*
    * module: kbbookingcalendar
    * date: 2022-11-11 07:57:21
    * version: 2.0.0
    */
    public function getProducts($refresh = false, $id_product = false, $id_country = null, $fullInfos = true, bool $keepOrderPrices = false)
    {
        if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
            if (!$this->id) {
                return [];
            }
            if ($this->_products !== null && !$refresh) {
                if (is_int($id_product)) {
                    foreach ($this->_products as $product) {
                        if ($product['id_product'] == $id_product) {
                            return [$product];
                        }
                    }
                    return [];
                }
                return $this->_products;
            }
            $sql = new DbQuery();
            $sql->select('cp.`id_product_attribute`, cp.`id_product`, cp.`quantity` AS cart_quantity, cp.id_shop, cp.`id_customization`, pl.`name`, p.`is_virtual`,
                        pl.`description_short`, pl.`available_now`, pl.`available_later`, product_shop.`id_category_default`, p.`id_supplier`,
                        p.`id_manufacturer`, m.`name` AS manufacturer_name, product_shop.`on_sale`, product_shop.`ecotax`, product_shop.`additional_shipping_cost`,
                        product_shop.`available_for_order`, product_shop.`show_price`, product_shop.`price`, product_shop.`active`, product_shop.`unity`, product_shop.`unit_price_ratio`,
                        stock.`quantity` AS quantity_available, p.`width`, p.`height`, p.`depth`, stock.`out_of_stock`, p.`weight`,
                        p.`available_date`, p.`date_add`, p.`date_upd`, IFNULL(stock.quantity, 0) as quantity, pl.`link_rewrite`, cl.`link_rewrite` AS category,
                        CONCAT(LPAD(cp.`id_product`, 10, 0), LPAD(IFNULL(cp.`id_product_attribute`, 0), 10, 0), IFNULL(cp.`id_address_delivery`, 0), IFNULL(cp.`id_customization`, 0)) AS unique_id, cp.id_address_delivery,
                        product_shop.advanced_stock_management, ps.product_supplier_reference supplier_reference');
            $sql->from('cart_product', 'cp');
            $sql->leftJoin('product', 'p', 'p.`id_product` = cp.`id_product`');
            $sql->innerJoin('product_shop', 'product_shop', '(product_shop.`id_shop` = cp.`id_shop` AND product_shop.`id_product` = p.`id_product`)');
            $sql->leftJoin('product_lang', 'pl', 'p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int) $this->id_lang . Shop::addSqlRestrictionOnLang('pl', 'cp.id_shop'));
            $sql->leftJoin('category_lang', 'cl', 'product_shop.`id_category_default` = cl.`id_category` AND cl.`id_lang` = ' . (int) $this->id_lang . Shop::addSqlRestrictionOnLang('cl', 'cp.id_shop'));
            $sql->leftJoin('product_supplier', 'ps', 'ps.`id_product` = cp.`id_product` AND ps.`id_product_attribute` = cp.`id_product_attribute` AND ps.`id_supplier` = p.`id_supplier`');
            $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');
            $sql->join(Product::sqlStock('cp', 'cp'));
            $sql->where('cp.`id_cart` = ' . (int) $this->id);
            if ($id_product) {
                $sql->where('cp.`id_product` = ' . (int) $id_product);
            }
            $sql->where('p.`id_product` IS NOT NULL');
            $sql->orderBy('cp.`date_add`, cp.`id_product`, cp.`id_product_attribute` ASC');
            if (Customization::isFeatureActive()) {
                $sql->select('cu.`id_customization`, cu.`quantity` AS customization_quantity');
                $sql->leftJoin('customization', 'cu', 'p.`id_product` = cu.`id_product` AND cp.`id_product_attribute` = cu.`id_product_attribute` AND cp.`id_customization` = cu.`id_customization` AND cu.`id_cart` = ' . (int) $this->id);
                $sql->groupBy('cp.`id_product_attribute`, cp.`id_product`, cp.`id_shop`, cp.`id_customization`');
            } else {
                $sql->select('NULL AS customization_quantity, NULL AS id_customization');
            }
            if (Combination::isFeatureActive()) {
                $sql->select('
                product_attribute_shop.`price` AS price_attribute, product_attribute_shop.`ecotax` AS ecotax_attr,
                IF (IFNULL(pa.`reference`, \'\') = \'\', p.`reference`, pa.`reference`) AS reference,
                (p.`weight`+ pa.`weight`) weight_attribute,
                IF (IFNULL(pa.`ean13`, \'\') = \'\', p.`ean13`, pa.`ean13`) AS ean13,
                IF (IFNULL(pa.`isbn`, \'\') = \'\', p.`isbn`, pa.`isbn`) AS isbn,
                IF (IFNULL(pa.`upc`, \'\') = \'\', p.`upc`, pa.`upc`) AS upc,
                IF (IFNULL(pa.`mpn`, \'\') = \'\', p.`mpn`, pa.`mpn`) AS mpn,
                IFNULL(product_attribute_shop.`minimal_quantity`, product_shop.`minimal_quantity`) as minimal_quantity,
                IF(product_attribute_shop.wholesale_price > 0,  product_attribute_shop.wholesale_price, product_shop.`wholesale_price`) wholesale_price
            ');
                $sql->leftJoin('product_attribute', 'pa', 'pa.`id_product_attribute` = cp.`id_product_attribute`');
                $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', '(product_attribute_shop.`id_shop` = cp.`id_shop` AND product_attribute_shop.`id_product_attribute` = pa.`id_product_attribute`)');
            } else {
                $sql->select('p.`reference` AS reference, p.`ean13`, p.`isbn`, p.`upc` AS upc, p.`mpn` AS mpn, product_shop.`minimal_quantity` AS minimal_quantity, product_shop.`wholesale_price` wholesale_price');
            }
            $sql->select('image_shop.`id_image` id_image, il.`legend`');
            $sql->leftJoin('image_shop', 'image_shop', 'image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $this->id_shop);
            $sql->leftJoin('image_lang', 'il', 'il.`id_image` = image_shop.`id_image` AND il.`id_lang` = ' . (int) $this->id_lang);
            $result = Db::getInstance()->executeS($sql);
            $products_ids = [];
            $pa_ids = [];
            if ($result) {
                foreach ($result as $key => $row) {
                    $products_ids[] = $row['id_product'];
                    $pa_ids[] = $row['id_product_attribute'];
                    $specific_price = SpecificPrice::getSpecificPrice($row['id_product'], $this->id_shop, $this->id_currency, $id_country, $this->id_shop_group, $row['cart_quantity'], $row['id_product_attribute'], $this->id_customer, $this->id);
                    if ($specific_price) {
                        $reduction_type_row = ['reduction_type' => $specific_price['reduction_type']];
                    } else {
                        $reduction_type_row = ['reduction_type' => 0];
                    }
                    $result[$key] = array_merge($row, $reduction_type_row);
                }
            }
            Product::cacheProductsFeatures($products_ids);
            Cart::cacheSomeAttributesLists($pa_ids, $this->id_lang);
            if (empty($result)) {
                $this->_products = [];
                return [];
            }
            if ($fullInfos) {
                $cart_shop_context = Context::getContext()->cloneContext();
                $givenAwayProductsIds = [];
                if ($this->shouldSplitGiftProductsQuantity && $refresh) {
                    $gifts = $this->getCartRules(CartRule::FILTER_ACTION_GIFT, false);
                    if (count($gifts) > 0) {
                        foreach ($gifts as $gift) {
                            foreach ($result as $rowIndex => $row) {
                                if (!array_key_exists('is_gift', $result[$rowIndex])) {
                                    $result[$rowIndex]['is_gift'] = false;
                                }
                                if ($row['id_product'] == $gift['gift_product'] && $row['id_product_attribute'] == $gift['gift_product_attribute']) {
                                    $row['is_gift'] = true;
                                    $result[$rowIndex] = $row;
                                }
                            }
                            $index = $gift['gift_product'] . '-' . $gift['gift_product_attribute'];
                            if (!array_key_exists($index, $givenAwayProductsIds)) {
                                $givenAwayProductsIds[$index] = 1;
                            } else {
                                ++$givenAwayProductsIds[$index];
                            }
                        }
                    }
                }
                $this->_products = [];
                foreach ($result as &$row) {
                    if (!array_key_exists('is_gift', $row)) {
                        $row['is_gift'] = false;
                    }
                    $additionalRow = Product::getProductProperties((int) $this->id_lang, $row);
                    $row['reduction'] = $additionalRow['reduction'];
                    $row['reduction_without_tax'] = $additionalRow['reduction_without_tax'];
                    $row['price_without_reduction'] = $additionalRow['price_without_reduction'];
                    $row['specific_prices'] = $additionalRow['specific_prices'];
                    unset($additionalRow);
                    $givenAwayQuantity = 0;
                    $giftIndex = $row['id_product'] . '-' . $row['id_product_attribute'];
                    if ($row['is_gift'] && array_key_exists($giftIndex, $givenAwayProductsIds)) {
                        $givenAwayQuantity = $givenAwayProductsIds[$giftIndex];
                    }
                    if (!$row['is_gift'] || (int) $row['cart_quantity'] === $givenAwayQuantity) {
                        $row = $this->applyProductCalculations($row, $cart_shop_context, null, $keepOrderPrices);
                    } else {
                        $this->_products[] = $this->applyProductCalculations($row, $cart_shop_context, $givenAwayQuantity, $keepOrderPrices);
                        unset($row['is_gift']);
                        $row = $this->applyProductCalculations(
                            $row,
                            $cart_shop_context,
                            $row['cart_quantity'] - $givenAwayQuantity,
                            $keepOrderPrices
                        );
                    }
                    $custPrice = 0;
                    $custQty = 0;
                    $sql_cust = 'SELECT price,qty FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart where id_customization=' . (int) $row['id_customization'] . ' and id_product=' . (int) $row['id_product'];
                    $cust = Db::getInstance()->getRow($sql_cust);
                    if (!empty($cust)) {
                        $custPrice = Tools::ps_round(
                            Product::getPriceStaticBookingProduct(
                                (int) $row['id_customization'],
                                (int) $row['id_product'],
                                true,
                                (int) $row['id_product_attribute'],
                                6,
                                null,
                                false,
                                true,
                                $cust['qty']
                            ),
                            (int) Configuration::get('PS_PRICE_DISPLAY_PRECISION')
                        );
                        $custQty = $cust['qty'];
                        if ($custQty != 0) {
                            $row['quantity'] = $custQty;
                        }
                        $row['price'] = $cust['price'];
                        $row['price_without_reduction'] = ($custPrice);
                        $row['price_with_reduction'] = ($custPrice);
                        $row['price_with_reduction_without_tax'] = ($cust['price']);
                        $row['total'] = ($cust['price'] * $row['quantity']);
                        $row['total_wt'] = ($custPrice * $row['quantity']);
                        $row['price_wt'] = ($custPrice);
                    }
                    $this->_products[] = $row;
                }
            } else {
                $this->_products = $result;
            }
            return $this->_products;
        } else {
            if (!$this->id) {
                return array();
            }
            if ($this->_products !== null && !$refresh) {
                if (is_int($id_product)) {
                    foreach ($this->_products as $product) {
                        if ($product['id_product'] == $id_product) {
                            return array($product);
                        }
                    }
                    return array();
                }
                return $this->_products;
            }
            $sql = new DbQuery();
            $sql->select('cp.`id_product_attribute`, cp.`id_product`, cp.`quantity` AS cart_quantity, cp.id_shop, cp.`id_customization`, pl.`name`, p.`is_virtual`,
                        pl.`description_short`, pl.`available_now`, pl.`available_later`, product_shop.`id_category_default`, p.`id_supplier`,
                        p.`id_manufacturer`, m.`name` AS manufacturer_name, product_shop.`on_sale`, product_shop.`ecotax`, product_shop.`additional_shipping_cost`,
                        product_shop.`available_for_order`, product_shop.`show_price`, product_shop.`price`, product_shop.`active`, product_shop.`unity`, product_shop.`unit_price_ratio`,
                        stock.`quantity` AS quantity_available, p.`width`, p.`height`, p.`depth`, stock.`out_of_stock`, p.`weight`,
                        p.`available_date`, p.`date_add`, p.`date_upd`, IFNULL(stock.quantity, 0) as quantity, pl.`link_rewrite`, cl.`link_rewrite` AS category,
                        CONCAT(LPAD(cp.`id_product`, 10, 0), LPAD(IFNULL(cp.`id_product_attribute`, 0), 10, 0), IFNULL(cp.`id_address_delivery`, 0), IFNULL(cp.`id_customization`, 0)) AS unique_id, cp.id_address_delivery,
                        product_shop.advanced_stock_management, ps.product_supplier_reference supplier_reference');
            $sql->from('cart_product', 'cp');
            $sql->leftJoin('product', 'p', 'p.`id_product` = cp.`id_product`');
            $sql->innerJoin('product_shop', 'product_shop', '(product_shop.`id_shop` = cp.`id_shop` AND product_shop.`id_product` = p.`id_product`)');
            $sql->leftJoin('product_lang', 'pl', 'p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int) $this->id_lang . Shop::addSqlRestrictionOnLang('pl', 'cp.id_shop'));
            $sql->leftJoin('category_lang', 'cl', 'product_shop.`id_category_default` = cl.`id_category` AND cl.`id_lang` = ' . (int) $this->id_lang . Shop::addSqlRestrictionOnLang('cl', 'cp.id_shop'));
            $sql->leftJoin('product_supplier', 'ps', 'ps.`id_product` = cp.`id_product` AND ps.`id_product_attribute` = cp.`id_product_attribute` AND ps.`id_supplier` = p.`id_supplier`');
            $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');
            $sql->join(Product::sqlStock('cp', 'cp'));
            $sql->where('cp.`id_cart` = ' . (int) $this->id);
            if ($id_product) {
                $sql->where('cp.`id_product` = ' . (int) $id_product);
            }
            $sql->where('p.`id_product` IS NOT NULL');
            $sql->orderBy('cp.`date_add`, cp.`id_product`, cp.`id_product_attribute` ASC');
            if (Customization::isFeatureActive()) {
                $sql->select('cu.`id_customization`, cu.`quantity` AS customization_quantity');
                $sql->leftJoin('customization', 'cu', 'p.`id_product` = cu.`id_product` AND cp.`id_product_attribute` = cu.`id_product_attribute` AND cp.`id_customization` = cu.`id_customization` AND cu.`id_cart` = ' . (int) $this->id);
                $sql->groupBy('cp.`id_product_attribute`, cp.`id_product`, cp.`id_shop`, cp.`id_customization`');
            } else {
                $sql->select('NULL AS customization_quantity, NULL AS id_customization');
            }
            if (Combination::isFeatureActive()) {
                $sql->select('
                product_attribute_shop.`price` AS price_attribute, product_attribute_shop.`ecotax` AS ecotax_attr,
                IF (IFNULL(pa.`reference`, \'\') = \'\', p.`reference`, pa.`reference`) AS reference,
                (p.`weight`+ pa.`weight`) weight_attribute,
                IF (IFNULL(pa.`ean13`, \'\') = \'\', p.`ean13`, pa.`ean13`) AS ean13,
                IF (IFNULL(pa.`isbn`, \'\') = \'\', p.`isbn`, pa.`isbn`) AS isbn,
                IF (IFNULL(pa.`upc`, \'\') = \'\', p.`upc`, pa.`upc`) AS upc,
                IFNULL(product_attribute_shop.`minimal_quantity`, product_shop.`minimal_quantity`) as minimal_quantity,
                IF(product_attribute_shop.wholesale_price > 0,  product_attribute_shop.wholesale_price, product_shop.`wholesale_price`) wholesale_price
            ');
                $sql->leftJoin('product_attribute', 'pa', 'pa.`id_product_attribute` = cp.`id_product_attribute`');
                $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', '(product_attribute_shop.`id_shop` = cp.`id_shop` AND product_attribute_shop.`id_product_attribute` = pa.`id_product_attribute`)');
            } else {
                $sql->select('p.`reference` AS reference, p.`ean13`, p.`isbn`, p.`upc` AS upc, product_shop.`minimal_quantity` AS minimal_quantity, product_shop.`wholesale_price` wholesale_price');
            }
            $sql->select('image_shop.`id_image` id_image, il.`legend`');
            $sql->leftJoin('image_shop', 'image_shop', 'image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $this->id_shop);
            $sql->leftJoin('image_lang', 'il', 'il.`id_image` = image_shop.`id_image` AND il.`id_lang` = ' . (int) $this->id_lang);
            $result = Db::getInstance()->executeS($sql);
            $products_ids = array();
            $pa_ids = array();
            if ($result) {
                foreach ($result as $key => $row) {
                    $products_ids[] = $row['id_product'];
                    $pa_ids[] = $row['id_product_attribute'];
                    $specific_price = SpecificPrice::getSpecificPrice($row['id_product'], $this->id_shop, $this->id_currency, $id_country, $this->id_shop_group, $row['cart_quantity'], $row['id_product_attribute'], $this->id_customer, $this->id);
                    if ($specific_price) {
                        $reduction_type_row = array('reduction_type' => $specific_price['reduction_type']);
                    } else {
                        $reduction_type_row = array('reduction_type' => 0);
                    }
                    $result[$key] = array_merge($row, $reduction_type_row);
                }
            }
            Product::cacheProductsFeatures($products_ids);
            Cart::cacheSomeAttributesLists($pa_ids, $this->id_lang);
            $this->_products = array();
            if (empty($result)) {
                return array();
            }
            $ecotax_rate = (float) Tax::getProductEcotaxRate($this->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
            $apply_eco_tax = Product::$_taxCalculationMethod == PS_TAX_INC && (int) Configuration::get('PS_TAX');
            $cart_shop_context = Context::getContext()->cloneContext();
            $gifts = $this->getCartRules(CartRule::FILTER_ACTION_GIFT);
            $givenAwayProductsIds = array();
            if ($this->shouldSplitGiftProductsQuantity && count($gifts) > 0) {
                foreach ($gifts as $gift) {
                    foreach ($result as $rowIndex => $row) {
                        if (!array_key_exists('is_gift', $result[$rowIndex])) {
                            $result[$rowIndex]['is_gift'] = false;
                        }
                        if ($row['id_product'] == $gift['gift_product'] && $row['id_product_attribute'] == $gift['gift_product_attribute']) {
                            $row['is_gift'] = true;
                            $result[$rowIndex] = $row;
                        }
                    }
                    $index = $gift['gift_product'] . '-' . $gift['gift_product_attribute'];
                    if (!array_key_exists($index, $givenAwayProductsIds)) {
                        $givenAwayProductsIds[$index] = 1;
                    } else {
                        $givenAwayProductsIds[$index] ++;
                    }
                }
            }
            foreach ($result as &$row) {
                if (!array_key_exists('is_gift', $row)) {
                    $row['is_gift'] = false;
                }
                $additionalRow = Product::getProductProperties((int) $this->id_lang, $row);
                $row['reduction'] = $additionalRow['reduction'];
                $row['price_without_reduction'] = $additionalRow['price_without_reduction'];
                $row['specific_prices'] = $additionalRow['specific_prices'];
                unset($additionalRow);
                $givenAwayQuantity = 0;
                $giftIndex = $row['id_product'] . '-' . $row['id_product_attribute'];
                if ($row['is_gift'] && array_key_exists($giftIndex, $givenAwayProductsIds)) {
                    $givenAwayQuantity = $givenAwayProductsIds[$giftIndex];
                }
                if (!$row['is_gift'] || (int) $row['cart_quantity'] === $givenAwayQuantity) {
                    if (version_compare(_PS_VERSION_, '1.7.7.1', '>=')) {
                        $row = $this->applyProductCalculations($row, $cart_shop_context, null, $keepOrderPrices);
                    } else {
                        $row = $this->applyProductCalculations($row, $cart_shop_context);
                    }
                } else {
                    if (version_compare(_PS_VERSION_, '1.7.7.1', '>=')) {
                        $this->_products[] = $this->applyProductCalculations($row, $cart_shop_context, $givenAwayQuantity, $keepOrderPrices);
                    } else {
                        $this->_products[] = $this->applyProductCalculations($row, $cart_shop_context, $givenAwayQuantity);
                    }
                    unset($row['is_gift']);
                    if (version_compare(_PS_VERSION_, '1.7.7.1', '>=')) {
                        $row = $this->applyProductCalculations(
                            $row,
                            $cart_shop_context,
                            $row['cart_quantity'] - $givenAwayQuantity,
                            $keepOrderPrices
                        );
                    } else {
                        $row = $this->applyProductCalculations(
                            $row,
                            $cart_shop_context,
                            $row['cart_quantity'] - $givenAwayQuantity
                        );
                    }
                }
                $custPrice = 0;
                $custQty = 0;
                $sql_cust = 'SELECT price,qty FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart where id_customization=' . (int) $row['id_customization'] . ' and id_product=' . (int) $row['id_product'];
                $cust = Db::getInstance()->getRow($sql_cust);
                if (!empty($cust)) {
                    $custPrice = Tools::ps_round(
                        Product::getPriceStaticBookingProduct(
                            (int) $row['id_customization'],
                            (int) $row['id_product'],
                            true,
                            (int) $row['id_product_attribute'],
                            6,
                            null,
                            false,
                            true,
                            $cust['qty']
                        ),
                        (int) Configuration::get('PS_PRICE_DISPLAY_PRECISION')
                    );
                    $custQty = $cust['qty'];
                    if ($custQty != 0) {
                        $row['quantity'] = $custQty;
                    }
                    $row['price'] = $cust['price'];
                    $row['price_without_reduction'] = ($custPrice);
                    $row['price_with_reduction'] = ($custPrice);
                    $row['price_with_reduction_without_tax'] = ($cust['price']);
                    $row['total'] = ($cust['price'] * $row['quantity']);
                    $row['total_wt'] = ($custPrice * $row['quantity']);
                    $row['price_wt'] = ($custPrice);
                }
                $this->_products[] = $row;
            }
            return $this->_products;
        }
    }
    
    
    /*
    * module: kbbookingcalendar
    * date: 2022-11-11 07:57:21
    * version: 2.0.0
    */
    public function getOrderTotal(
        $with_taxes = true,
        $type = Cart::BOTH,
        $products = null,
        $id_carrier = null,
        $use_cache = true,
        bool $keepOrderPrices = false
    ) {
        $price_calculator = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\Product\\PriceCalculator');
        $ps_use_ecotax = $this->configuration->get('PS_USE_ECOTAX');
        $ps_round_type = $this->configuration->get('PS_ROUND_TYPE');
        $ps_ecotax_tax_rules_group_id = $this->configuration->get('PS_ECOTAX_TAX_RULES_GROUP_ID');
        $compute_precision = $this->configuration->get('_PS_PRICE_COMPUTE_PRECISION_');
        if (!$this->id) {
            return 0;
        }
        $type = (int) $type;
        $array_type = array(
            Cart::ONLY_PRODUCTS,
            Cart::ONLY_DISCOUNTS,
            Cart::BOTH,
            Cart::BOTH_WITHOUT_SHIPPING,
            Cart::ONLY_SHIPPING,
            Cart::ONLY_WRAPPING,
            Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING,
            Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING,
        );
        $virtual_context = Context::getContext()->cloneContext();
        $virtual_context->cart = $this;
        if (!in_array($type, $array_type)) {
            die(Tools::displayError());
        }
        $with_shipping = in_array($type, array(Cart::BOTH, Cart::ONLY_SHIPPING));
        if ($type == Cart::ONLY_DISCOUNTS && !CartRule::isFeatureActive()) {
            return 0;
        }
        $virtual = $this->isVirtualCart();
        if ($virtual && $type == Cart::ONLY_SHIPPING) {
            return 0;
        }
        if ($virtual && $type == Cart::BOTH) {
            $type = Cart::BOTH_WITHOUT_SHIPPING;
        }
        if ($with_shipping || $type == Cart::ONLY_DISCOUNTS) {
            if (is_null($products) && is_null($id_carrier)) {
                $shipping_fees = $this->getTotalShippingCost(null, (bool) $with_taxes);
            } else {
                $shipping_fees = $this->getPackageShippingCost((int) $id_carrier, (bool) $with_taxes, null, $products);
            }
        } else {
            $shipping_fees = 0;
        }
        if ($type == Cart::ONLY_SHIPPING) {
            return $shipping_fees;
        }
        if ($type == Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING) {
            $type = Cart::ONLY_PRODUCTS;
        }
        $param_product = true;
        if (is_null($products)) {
            $param_product = false;
            if (version_compare(_PS_VERSION_, '1.7.7.1', '>=')) {
                $products = $this->getProducts(false, false, null, true, $keepOrderPrices);
            } else {
                $products = $this->getProducts();
            }
        }
        if ($type == Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING) {
            foreach ($products as $key => $product) {
                if ($product['is_virtual']) {
                    unset($products[$key]);
                }
            }
            $type = Cart::ONLY_PRODUCTS;
        }
        $order_total = 0;
        if (Tax::excludeTaxeOption()) {
            $with_taxes = false;
        }
        $products_total = array();
        $ecotax_total = 0;
        $productLines = $this->countProductLines($products);
        foreach ($products as $product) {
            if (array_key_exists('is_gift', $product) && $product['is_gift']) {
                $productIndex = $product['id_product'] . '-' . $product['id_product_attribute'];
                if ($productLines[$productIndex] > 1) {
                    continue;
                }
            }
            if ($virtual_context->shop->id != $product['id_shop']) {
                $virtual_context->shop = new Shop((int) $product['id_shop']);
            }
            $id_address = $this->getProductAddressId($product);
            $null = null;
            $price = $price_calculator->getProductPrice(
                (int) $product['id_product'],
                $with_taxes,
                (int) $product['id_product_attribute'],
                6,
                null,
                false,
                true,
                $product['cart_quantity'],
                false,
                (int) $this->id_customer ? (int) $this->id_customer : null,
                (int) $this->id,
                $id_address,
                $null,
                $ps_use_ecotax,
                true,
                $virtual_context,
                true,
                (int) $product['id_customization']
            );
            $custPrice = 0;
            $custQty = 0;
            $sql_cust = 'SELECT price,qty FROM ' . _DB_PREFIX_ . 'kb_booking_product_cart where id_customization=' . (int) $product['id_customization'] . ' and id_product=' . (int) $product['id_product'];
            $cust = Db::getInstance()->getRow($sql_cust);
            if (!empty($cust)) {
                if ($with_taxes) {
                    $custPrice = Tools::ps_round(
                        Product::getPriceStaticBookingProduct(
                            (int) $product['id_customization'],
                            (int) $product['id_product'],
                            true,
                            (int) $product['id_product_attribute'],
                            6,
                            null,
                            false,
                            true,
                            $product['cart_quantity']
                        ),
                        (int) Configuration::get('PS_PRICE_DISPLAY_PRECISION')
                    );
                    $price = $custPrice;
                } else {
                    $price = $cust['price'];
                }
            }
            $id_tax_rules_group = $this->findTaxRulesGroupId($with_taxes, $product, $virtual_context);
            if (in_array($ps_round_type, array(Order::ROUND_ITEM, Order::ROUND_LINE))) {
                if (!isset($products_total[$id_tax_rules_group])) {
                    $products_total[$id_tax_rules_group] = 0;
                }
            } elseif (!isset($products_total[$id_tax_rules_group . '_' . $id_address])) {
                $products_total[$id_tax_rules_group . '_' . $id_address] = 0;
            }
            switch ($ps_round_type) {
                case Order::ROUND_TOTAL:
                    $products_total[$id_tax_rules_group . '_' . $id_address] += $price * (int) $product['cart_quantity'];
                    break;
                case Order::ROUND_LINE:
                    $product_price = $price * $product['cart_quantity'];
                    $products_total[$id_tax_rules_group] += Tools::ps_round($product_price, $compute_precision);
                    break;
                case Order::ROUND_ITEM:
                default:
                    $product_price = $price;
                    $products_total[$id_tax_rules_group] += Tools::ps_round($product_price, $compute_precision) * (int) $product['cart_quantity'];
                    break;
            }
        }
        foreach ($products_total as $key => $price) {
            $order_total += $price;
        }
        $order_total_products = $order_total;
        if ($type == Cart::ONLY_DISCOUNTS) {
            $order_total = 0;
        }
        $wrappingFees = $this->calculateWrappingFees($with_taxes, $type);
        if ($type == Cart::ONLY_WRAPPING) {
            return $wrappingFees;
        }
        $order_total_discount = 0;
        $order_shipping_discount = 0;
        if (!in_array($type, array(Cart::ONLY_SHIPPING, Cart::ONLY_PRODUCTS)) && CartRule::isFeatureActive()) {
            $cart_rules = $this->getTotalCalculationCartRules($type, $with_shipping);
            $package = array(
                'id_carrier' => $id_carrier,
                'id_address' => $this->getDeliveryAddressId($products),
                'products' => $products
            );
            $flag = false;
            foreach ($cart_rules as $cart_rule) {
                if (($with_shipping || $type == Cart::ONLY_DISCOUNTS) && $cart_rule['obj']->free_shipping && !$flag) {
                    $order_shipping_discount = (float) Tools::ps_round($cart_rule['obj']->getContextualValue($with_taxes, $virtual_context, CartRule::FILTER_ACTION_SHIPPING, ($param_product ? $package : null), $use_cache), $compute_precision);
                    $flag = true;
                }
                if (!$this->shouldExcludeGiftsDiscount && (int) $cart_rule['obj']->gift_product) {
                    $in_order = false;
                    if (is_null($products)) {
                        $in_order = true;
                    } else {
                        foreach ($products as $product) {
                            if ($cart_rule['obj']->gift_product == $product['id_product'] && $cart_rule['obj']->gift_product_attribute == $product['id_product_attribute']) {
                                $in_order = true;
                            }
                        }
                    }
                    if ($in_order) {
                        $order_total_discount += $cart_rule['obj']->getContextualValue($with_taxes, $virtual_context, CartRule::FILTER_ACTION_GIFT, $package, $use_cache);
                    }
                }
                if ($cart_rule['obj']->reduction_percent > 0 || $cart_rule['obj']->reduction_amount > 0) {
                    $order_total_discount += Tools::ps_round($cart_rule['obj']->getContextualValue($with_taxes, $virtual_context, CartRule::FILTER_ACTION_REDUCTION, $package, $use_cache), $compute_precision);
                }
            }
            $order_total_discount = min(Tools::ps_round($order_total_discount, 2), (float) $order_total_products) + (float) $order_shipping_discount;
            $order_total -= $order_total_discount;
        }
        if ($type == Cart::BOTH) {
            $order_total += $shipping_fees + $wrappingFees;
        }
        if ($order_total < 0 && $type != Cart::ONLY_DISCOUNTS) {
            return 0;
        }
        if ($type == Cart::ONLY_DISCOUNTS) {
            return $order_total_discount;
        }
        return Tools::ps_round((float) $order_total, $compute_precision);
    }
}
