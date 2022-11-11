<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Musaffar Patel
 * @copyright 2016-2021 Musaffar Patel
 * @license   LICENSE.txt
 */

class LRPProductHelper
{
    /**
     * Get Product info such as price, attribute price based on Product ID and attributes array (group)
     * @param $id_product
     * @param $group
     * @param int $id_product_attribute
     */
    public static function getProductInfo($id_product, $group, $id_product_attribute = 0)
    {
        if (!empty($group)) {
            $id_product_attribute = Product::getIdProductAttributesByIdAttributes((int)$id_product, $group);
        }

        $product_obj = new Product($id_product);
        $product = array();
        $product['id_product'] = $id_product;
        $product['id_product_attribute'] = $id_product_attribute;
        $product['out_of_stock'] = $product_obj->out_of_stock;
        $product['id_category_default'] = $product_obj->id_category_default;
        $product['link_rewrite'] = ''; //$product_obj->link_rewrite;
        $product['ean13'] = $product_obj->ean13;
        $product['minimal_quantity'] = $product_obj->minimal_quantity;
        $product['unit_price_ratio'] = $product_obj->unit_price_ratio;
        $product['price_display'] = (int)Product::getTaxCalculationMethod(Context::getContext()->cookie->id_customer);
        $product['quantity_wanted'] = 1;

        $product_properties = Product::getProductProperties(Context::getContext()->language->id, $product, null);

        $product['id_product_attribute'] = 0;
        $product_properties_tmp = Product::getProductProperties(Context::getContext()->language->id, $product, null);

        //$product_properties['base_price_exc_tax'] = $product_obj->price;
        $base_price = $product_properties_tmp['price_without_reduction'];
        $product_properties['base_price_exc_tax'] = $base_price / (1 + ($product_properties['rate'] / 100));
        return $product_properties;
    }

    public static function isProductOnDiscount(int $id_product, int $id_product_attribute): bool
    {
        return false;
        $product = LRPProductHelper::getProductInfo($id_product, array(), $id_product_attribute);
        if (!empty($product['specific_prices'])) {
            return true;
        }
        if (LRPCartRuleHelper::productHasActiveCartRule($id_product)) {
            return true;
        }
        return false;
    }
}
