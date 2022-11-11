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

class LRPAdminCustomerController extends LRPControllerCore
{
    protected $sibling;

    const PAGE_SIZE = 20;

    private $route = 'lrpadmincustomercontroller';

    protected $request;

    public function __construct(&$sibling = null, Symfony\Component\HttpFoundation\Request $request = null)
    {
        parent::__construct($sibling);
        if ($sibling !== null) {
            $this->sibling = &$sibling;
        }
        if (!empty($request)) {
            $this->request = $request;
        }
    }

    public function setMedia()
    {
        if ((Tools::getValue('controller') == 'AdminCustomers' || Tools::getValue('tab') == 'AdminCustomers')) {
            Context::getContext()->controller->addCSS($this->sibling->_path . 'views/css/lib/tools.css');
            Context::getContext()->controller->addCSS($this->sibling->_path . 'views/css/lib/popup.css');
            Context::getContext()->controller->addCSS($this->sibling->_path . 'views/css/admin/admin.css');

            Context::getContext()->controller->addJquery();
            Context::getContext()->controller->addJS($this->sibling->_path . 'views/js/lib/popup.js');
            Context::getContext()->controller->addJS($this->sibling->_path . 'views/js/lib/tools.js');
            Context::getContext()->controller->addJS($this->sibling->_path . 'views/js/admin/customer/LRPAdminCustomerController.js');
        }
    }

    public function render()
    {
        $id_customer = (int)Tools::getValue('id_customer');
        if ($id_customer == 0) {
            $id_customer = $this->request->get('customerId');
        }

        $lrp_customer = new LRPCustomerModel();
        $lrp_customer->loadByCustomerID($id_customer);
        $points_available = LRPCustomerHelper::getTotalPointsAvailable($id_customer, null);

        $currencies = Currency::getCurrenciesByIdShop(Context::getContext()->shop->id);

        Context::getContext()->smarty->assign(array(
            'id_customer' => $id_customer,
            'points' => $points_available,
            'module_config_url' => $this->module_config_url,
            'currencies' => $currencies
        ));
        return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/customer/history.tpl');
    }

    public function renderList()
    {
        $lrp_history = new LRPHistoryModel();
        $history = $lrp_history->getByCustomerID(Tools::getValue('id_customer'), Tools::getValue('current_page'), self::PAGE_SIZE);

        foreach ($history['result'] as &$history_item) {
            if ($history_item->type == LRPHistoryModel::TYPE_REWARDED) {
                $history_item->type = $this->sibling->l('Rewarded', $this->route);
            } else {
                $history_item->type = $this->sibling->l('Redeemed', $this->route);
            }
            $order = new Order($history_item->id_order);
            $history_item->reference = $order->reference;
            $history_item->source = LRPCustomerHelper::translateSource($history_item->source, $this->sibling, '');
        }

        $pagination = array(
            'page_total' => ceil($history['total'] / self::PAGE_SIZE),
            'current_page' => (int)Tools::getValue('current_page')
        );

        Context::getContext()->smarty->assign(array(
            'id_customer' => (int)Tools::getValue('id_customer'),
            'history' => $history['result'],
            'module_config_url' => $this->module_config_url,
            'pagination' => $pagination
        ));
        return $this->sibling->display($this->sibling->module_file, 'views/templates/admin/customer/_partial_history_list.tpl');
    }

    public function processUpdatePoints()
    {
        if ((int)Tools::getValue('points') == 0 || (int)Tools::getValue('id_customer') == 0) {
            return false;
        };

        $source = LRPHistoryHelper::getEmployeeSource();
        $currency = new Currency(Tools::getValue('id_currency'));

        if (Tools::getValue('type') == 'add') {
            LRPHistoryHelper::rewardPoints('', Tools::getValue('id_customer'), Tools::getValue('points'), $source, $currency);
        }

        if (Tools::getValue('type') == 'subtract') {
            LRPHistoryHelper::redeemPoints('', Tools::getValue('id_customer'), Tools::getValue('points'), $source, $currency);
        }
    }

    public function route()
    {
        switch (Tools::getValue('action')) {
            case 'renderlist':
                die($this->renderList());

            case 'processupdatepoints':
                die($this->processUpdatePoints());

            default:
                die($this->render());
        }
    }
}
