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

class MPProductSearchWidgetController
{
    public $id;

    private $sibling;

    private $module_folder = 'loyaltyrewardpoints';

    public function __construct($id, $sibling)
    {
        $this->id = $id;
        $this->sibling = $sibling;
    }

    /**
     * @return mixed
     */
    public function render($selected_products = array())
    {
        Context::getContext()->smarty->assign(array(
            'id' => $this->id,
            'selected_products' => $selected_products
        ));
        return $this->sibling->display(_PS_MODULE_DIR_ . $this->module_folder, 'views/templates/widget/mpproductsearchwidget.tpl');
    }

    public function getSearchResults($product_name, $id_lang)
    {
        $sql = new DbQuery();
        $sql->select('DISTINCT(pl.id_product), name, reference');
        $sql->from('product_lang', 'pl');
        $sql->innerJoin('product', 'p', 'pl.id_product = p.id_product AND pl.id_lang = ' . (int)$id_lang);
        $sql->where('pl.name LIKE "%' . pSQL($product_name) . '%" OR p.reference LIKE "%' . pSQL($product_name) . '%" OR p.id_product = ' . (int)$product_name);
        $results = Db::getInstance()->executeS($sql);
        return $results;
    }

    /**
     * Search products based on search string
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public function processSearch()
    {
        return $this->getSearchResults(Tools::getValue('search_string'), Context::getContext()->language->id);
    }

    /**
     * Construct array of product names and associated ID from a comma separated list of product IDs
     * @param $id_products
     * @return array
     */
    public function constructSelectedProductsArray($id_products)
    {
        $arr_products_tmp = explode(',', $id_products);
        $arr_products = array();
        if (!empty($arr_products_tmp)) {
            foreach ($arr_products_tmp as $id_product) {
                $product = new Product($id_product, false, Configuration::get('PS_LANG_DEFAULT'));
                $arr_products[] = array(
                    'name' => $product->name,
                    'id' => $product->id
                );
            }
        } else {
            return array();
        }
        return $arr_products;
    }

    public function route()
    {
        switch (Tools::getValue('action')) {
            case 'processsearch':
                return $this->processSearch();
        }
    }
}
