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

class LRPAdminOrderController extends LRPControllerCore
{
    protected $sibling;

    private $route = 'lrpadminordercontroller';

    public function __construct(&$sibling = null)
    {
        parent::__construct($sibling);
        if ($sibling !== null) {
            $this->sibling = &$sibling;
        }
    }

    public function setMedia()
    {
        if (Tools::getValue('controller') == 'AdminOrders') {
            Context::getContext()->controller->addCSS($this->sibling->_path . 'views/css/admin/admin.css');
        }
    }

    public function render()
    {
        $lrp_history_redeemed = new LRPHistoryModel();
        $lrp_history_redeemed->loadByOrder(Tools::getValue('id_order'), LRPHistoryModel::TYPE_REDEEMED);

        $lrp_history_rewarded = new LRPHistoryModel();
        $lrp_history_rewarded->loadByOrder(Tools::getValue('id_order'), LRPHistoryModel::TYPE_REWARDED);

        Context::getContext()->smarty->assign(array(
            'redeemed_points' => (float)$lrp_history_redeemed->points,
            'redeemed_point_value' => (float)$lrp_history_redeemed->point_value,
            'redeemed_value' => Tools::displayPrice(LRPDiscountHelper::getPointsMoneyValue($lrp_history_redeemed->points, $lrp_history_redeemed->point_value)),
            'rewarded_points' => (float)$lrp_history_rewarded->points,
            'rewarded_point_value' => (float)$lrp_history_rewarded->point_value,
            'rewarded_value' => Tools::displayPrice(LRPDiscountHelper::getPointsMoneyValue($lrp_history_rewarded->points, $lrp_history_rewarded->point_value)),
            'module_config_url' => $this->module_config_url,
            'baseDir' => __PS_BASE_URI__
        ));

        if (version_compare(_PS_VERSION_, '1.7.7', '<') === true) {
            return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/order/summary.tpl');
        } else {
            return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/order/summary_1777.tpl');
        }
    }

    public function process()
    {
    }

    public function route()
    {
        switch (Tools::getValue('action')) {
            case 'process':
                die($this->process());
            default:
                die($this->render());
        }
    }
}
