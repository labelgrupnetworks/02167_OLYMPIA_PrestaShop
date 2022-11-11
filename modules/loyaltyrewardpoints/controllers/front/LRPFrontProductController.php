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

class LRPFrontProductController extends LRPControllerCore
{
    protected $sibling;

    private $route = 'lrpfrontproductcontroller';

    public function __construct(&$sibling = null)
    {
        parent::__construct($sibling);
        if ($sibling !== null) {
            $this->sibling = &$sibling;
        }
    }

    public function setMedia()
    {
        $allowed = array('index', 'product', 'category');
        $controller = Tools::getValue('controller');

        if (in_array($controller, $allowed)) {
            Context::getContext()->controller->addJS($this->sibling->_path . 'views/js/front/LRPFrontProductController.js');
            Context::getContext()->controller->addCSS($this->sibling->_path . 'views/css/front/front.css');
        }
    }

    /**
     * Display the amount of points this product is worth
     * @param $params
     * @return string
     */
    public function hookDisplayFooterProduct($params)
    {
        $action = '';

        if (Context::getContext()->controller->php_self != 'product') {
            return false;
        }

        if (!empty($params['quickview'])) {
            $action = 'quickview';
        }

        Context::getContext()->smarty->assign(array(
            'action' => $action,
            'module_config_url' => $this->module_config_url,
            'lrp_module_ajax_url' => $this->module_ajax_url
        ));
        return $this->sibling->display($this->sibling->module_file, 'views/templates/front/product/footer.tpl');
    }

    /**
     * Render widget
     * @return string
     */
    public function renderWidget()
    {
        $query_str = parse_url('?'.Tools::getValue('query'), PHP_URL_QUERY);
        parse_str($query_str, $query_params);
        $group = null;

        if (!empty($query_params['group'])) {
            $group = $query_params['group'];
        }

        $product_info = LRPProductHelper::getProductInfo(Tools::getValue('id_product'), $group);
        $id_product_attribute = Product::getIdProductAttributesByIdAttributes((int)Tools::getValue('id_product'), $group);
        if (LRPProductHelper::isProductOnDiscount(Tools::getValue('id_product'), $id_product_attribute)) {
            return false;
        }

        if ((float)$product_info['price'] == 0) {
            return false;
        }

        if ((int)Tools::getValue('qty') == 0) {
            $qty = 1;
        } else {
            $qty = (int)Tools::getValue('qty');
        }

        $points = LRPDiscountHelper::calculatePointsFromMoney($product_info['price'], Context::getContext()->currency->iso_code, false);
        $points = LRPDiscountHelper::applyPointRules($points, $product_info['id_product'], $qty);
        $points = floor($points);
        $points_money_value = LRPDiscountHelper::getPointsMoneyValue($points);

        Context::getContext()->smarty->assign(array(
            'points' => $points,
            'points_money_value' => Tools::displayPrice($points_money_value),
        ));
        return $this->sibling->display($this->sibling->module_file, 'views/templates/front/product/widget.tpl');
    }

    public function route()
    {
        switch (Tools::getValue('action')) {
            case 'renderwidget':
                die($this->renderWidget());
        }
    }
}
