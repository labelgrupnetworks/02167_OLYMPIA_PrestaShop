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

class AdminKbBookingOrdersController extends AdminKbBookingCoreController
{

    protected $statuses_array = array();

    //Class Constructor
    public function __construct()
    {
//        $this->name = 'KbBookingOrders';
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->table = 'order';

        parent::__construct();
        $statuses = OrderState::getOrderStates((int) $this->context->language->id);
        foreach ($statuses as $status) {
            $this->statuses_array[$status['id_order_state']] = $status['name'];
        }

        $this->fields_list = array(
            'id_order' => array(
                'title' => $this->module->l('Order ID', 'AdminKbBookingOrdersController'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'reference' => array(
                'title' => $this->module->l('Reference', 'AdminKbBookingOrdersController')
            ),
            'customer' => array(
                'title' => $this->module->l('Customer', 'AdminKbBookingOrdersController'),
                'havingFilter' => true,
            ),
            'total_paid_tax_incl' => array(
                'title' => $this->module->l('Total', 'AdminKbBookingOrdersController'),
                'align' => 'text-right',
                'type' => 'price',
                'currency' => true,
//                'callback' => 'setOrderCurrency',
                'badge_success' => true
            ),
            'payment' => array(
                'title' => $this->module->l('Payment', 'AdminKbBookingOrdersController')
            ),
            'osname' => array(
                'title' => $this->module->l('Status', 'AdminKbBookingOrdersController'),
                'type' => 'select',
                'color' => 'color',
                'list' => $this->statuses_array,
                'filter_key' => 'os!id_order_state',
                'filter_type' => 'int',
                'order_key' => 'osname'
            ),
            'date_add' => array(
                'title' => $this->module->l('Date', 'AdminKbBookingOrdersController'),
                'align' => 'text-right',
                'type' => 'datetime',
                'filter_key' => 'a!date_add'
            )
        );

        $this->_select = 'CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`, osl.`name` AS `osname`,';
        $this->_join = '
                INNER JOIN `' . _DB_PREFIX_ . 'kb_booking_product_order` b ON (b.`id_order` = a.`id_order`)
		LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)
		LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.`id_order_state` = a.`current_state`)
		LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = ' . (int) $this->context->language->id . ')';
        $this->_orderBy = 'id_order';
        $this->_orderWay = 'DESC';
        $this->_group = 'GROUP BY b.id_order';
       //Line added to remove link from list row
        $this->list_no_link = true;
    }

    public function renderList()
    {
        $this->addRowAction('view');

        return parent::renderList();
    }

    public static function setOrderCurrency($echo, $tr)
    {
        $order = new Order($tr['id_order']);
        return Tools::displayPrice($echo, (int) $order->id_currency);
    }

    public function initToolbar()
    {
        parent::initToolbar();

        unset($this->toolbar_btn['new']);
    }

    /**
     * Display view action link
     */
    public function displayViewLink($token = null, $id = null, $name = null)
    {
        if (!array_key_exists('View', self::$cache_lang)) {
            self::$cache_lang['View'] = $this->module->l('View', 'AdminKbBookingOrdersController');
        }
        
        $new_dir = _PS_ADMIN_CONTROLLER_DIR_ . 'AdminOrdersController.php';
        if (file_exists($new_dir)) {
            $url = $this->context->link->getAdminlink('AdminOrders') . '&' . $this->identifier . '=' . $id . '&vieworder';
        } else {
            $url = $this->context->link->getAdminlink('AdminOrders');
            $data_url = explode("?", $url);
            $url = $data_url[0].$id .'/view?'.$data_url[1];
        }
        
        $this->context->smarty->assign(array(
            'href' => $url,
            'action' => self::$cache_lang['View'],
            'icon' => 'search-plus'
        ));

        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name.'/views/templates/admin/list/list_action.tpl');
    }
}
