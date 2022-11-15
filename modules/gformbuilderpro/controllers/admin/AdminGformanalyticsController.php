<?php
/**
 * The file is controller. Do not modify the file if you want to upgrade the module in future
 *
 * @author    Globo Software Solution JSC <contact@globosoftware.net>
 * @copyright  2017 Globo., Jsc
 * @license   please read license in file license.txt
 * @link	     http://www.globosoftware.net
 */

include_once(_PS_MODULE_DIR_ . 'gformbuilderpro/classes/gformbuilderproModel.php');
class AdminGformanalyticsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'edit';
        parent::__construct();
        $this->meta_title = $this->l('Form Builder Pro');
        if (!$this->module->active)
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminDashboard'));
    }
    public function initContent()
    {
        $this->display = 'edit';
        $this->initTabModuleList();
        $this->initToolbar();
        $this->initPageHeaderToolbar();
        $this->content = $this->renderView();
        $this->context->smarty->assign(array(
            'content' => $this->content,
            'url_post' => self::$currentIndex.'&token='.$this->token,
        ));
        if(version_compare(_PS_VERSION_,'1.6') == 1){
            $this->context->smarty->assign(array(
                'show_page_header_toolbar' => $this->show_page_header_toolbar,
                'page_header_toolbar_title' => $this->page_header_toolbar_title,
                'page_header_toolbar_btn' => $this->page_header_toolbar_btn
            ));
        }
    }
    public function initTabModuleList(){
        if(version_compare(_PS_VERSION_,'1.5.4.0') == -1)
            return true;
        else
            return parent::initTabModuleList();
    }
    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('Form Builder Pro');
        $this->toolbar_title[] = $this->l('Analytics');
    }
    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn = array(
            'new' => array(
                'href' => $this->context->link->getAdminLink('AdminGformmanager'),
                'desc' => $this->l('Forms'),
                'icon' => 'process-icon-cogs'
            ),
            'about' => array(
                'href' => $this->context->link->getAdminLink('AdminGformrequest'),
                'desc' => $this->l('Data Recieved'),
                'icon' => 'process-icon-duplicate'
            ),
        );
        if(version_compare(_PS_VERSION_,'1.6') == 1){
            parent::initPageHeaderToolbar();
        }
    }
    public function postProcess()
    {
        if(Tools::isSubmit('gremoveAnalyticsAatas')){
            $id_gformbuilderpro = (int)Tools::getValue('id_gformbuilderpro');
            $date_from = Tools::getValue('date_from');
            $date_to = Tools::getValue('date_to');
            if($date_from == ''){
                $date_from = date('Y-m-d');
            }
            if($date_to == ''){
                $date_to = date('Y-m-d',strtotime('+1 day', strtotime($date_from)));
            }
            $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'gform_analytics` 
                    WHERE date_add BETWEEN "' . pSQL($date_from) . ' 00:00:00" AND "' . pSQL($date_to) . ' 23:59:59" 
                ' . ($id_gformbuilderpro > 0 ? ' AND id_gformbuilderpro=' . (int)$id_gformbuilderpro : '') . ' 
            ';
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
            die(Tools::jsonEncode(array('error' => 0, 'warrning' => $this->l('Remove history Successfully'))));
        }elseif(Tools::isSubmit('gBanIp')){
            $ip_address = Tools::getValue('ip_address');
            $idshop = (int)Tools::getValue('idshop');
            $id_shop_group = (int)Shop::getGroupFromShop($idshop);
            $blacklist_ip = Configuration::get('GF_BLACKLISTED_IP', null, (int)$id_shop_group, (int)$idshop);
            $banned = Tools::getValue('banned');
            if($banned == false || $banned == 'false' || $banned == '0') {
                if ($blacklist_ip != '') $blacklist_ip .= ',' . $ip_address; else $blacklist_ip = $ip_address;
                Configuration::updateValue('GF_BLACKLISTED_IP', $blacklist_ip, false, (int)$id_shop_group, (int)$idshop);
                die(Tools::jsonEncode(array('error' => 0, 'warrning' => $this->l('Ban Ip Successfully'))));
            }else{
                $blacklist_ips = explode(',',$blacklist_ip);
                if($blacklist_ips){
                    foreach($blacklist_ips as $key=>$ip){
                        if($ip == $ip_address)  unset($blacklist_ips[$key]);
                    }
                }
                Configuration::updateValue('GF_BLACKLISTED_IP', implode(',',$blacklist_ips), false, (int)$id_shop_group, (int)$idshop);
                die(Tools::jsonEncode(array('error' => 0, 'warrning' => $this->l('UnBan Ip Successfully'))));
            }
        }
        parent::postProcess();

    }
    public function renderView() {
        $id_gformbuilderpro = (int)Tools::getValue('id_gformbuilderpro');
        $ajax = (int)Tools::getValue('ajax');
        $preselect_date_range = Tools::getValue('preselectDateRange');
        if($preselect_date_range == '') $preselect_date_range == 'month';
        $date_from = Tools::getValue('date_from');
        $date_to = Tools::getValue('date_to');
        if($date_from == ''){
            $date_from = date('Y-m-').'01';
            $date_to = date('Y-m-d',strtotime('+1 day', strtotime(date('Y-m-d'))));
            $preselect_date_range == 'day';
        }
        if($date_to == ''){
            $date_to = date('Y-m-d',strtotime('+1 day', strtotime($date_from)));
        }
        $calendar_helper = null;
        if(!$ajax) {
            $chart_datas = array();
            $platform_datas = array();
            $allbrowser = $this->getTotalBrowser($id_gformbuilderpro, $date_from, $date_to);
            if ($allbrowser) {
                foreach ($allbrowser as $browser) {
                    $chart_datas[] = array('key' => $browser['browser'], 'y' => $browser['total']);
                }
            }
            $allplatform = $this->getTotalPlatform($id_gformbuilderpro, $date_from, $date_to);
            if ($allplatform) {
                foreach ($allplatform as $platform) {
                    $platform_datas[] = array('key' => $platform['platform'], 'y' => $platform['total']);
                }
            }
            if(version_compare(_PS_VERSION_, '1.6', '>=')){
                $calendar_helper = new HelperCalendar();
                $calendar_helper->setDateFrom($date_from);
                $calendar_helper->setDateTo($date_to);
                $calendar_helper->setCompareDateFrom(null);
                $calendar_helper->setCompareDateTo(null);
                $calendar_helper->setCompareOption(0);
            }
            $viewsdatas = array();
            $submitdatas = array();
            $replydatas = array();
            if ($preselect_date_range == 'year' || $preselect_date_range == 'prev-year') {
                $viewsdatas = $this->module->getAllView($id_gformbuilderpro, $date_from, $date_to, 'month', true);
                $submitdatas = $this->module->getAllSubmit($id_gformbuilderpro, $date_from, $date_to, 'month', true);
                $replydatas = $this->module->getAllReply($id_gformbuilderpro, $date_from, $date_to, 'month', true);
            } else {
                $viewsdatas = $this->module->getAllView($id_gformbuilderpro, $date_from, $date_to, 'day', true);
                $submitdatas = $this->module->getAllSubmit($id_gformbuilderpro, $date_from, $date_to, 'day', true);
                $replydatas = $this->module->getAllReply($id_gformbuilderpro, $date_from, $date_to, 'day', true);
            }
            $mainchartdatas = array(
                array(
                    'values' => $viewsdatas,
                    'key' => $this->l('Views'),
                    'color' => "#ff7f0e"
                ),
                array(
                    'values' => $submitdatas,
                    'key' => $this->l('Submit'),
                    'color' => "#2ca02c"
                ),
                array(
                    'values' => $replydatas,
                    'key' => $this->l('Reply'),
                    'color' => "#2222ff"
                )
            );
            if(version_compare(_PS_VERSION_, '1.6', '>=')){
                Media::addJsDef(array(
                    'brower_chart_datas' => $chart_datas,
                    'platform_chart_datas' => $platform_datas,
                    'mainchartdatas' => $mainchartdatas,
                    'gchart_date_format' => ($preselect_date_range == 'year' || $preselect_date_range == 'prev-year' ? 'm/Y' : Context::getContext()->language->date_format_lite)
                ));
            }
        }
        $forms = gformbuilderproModel::getAllBlock();
        $fields_value = array(
            'date_from'=>$date_from,
            'date_to'=>$date_to,
            'preselect_date_range'=>$preselect_date_range
        );
        $total = (int)$this->getTotalAnalyticdata($id_gformbuilderpro,$date_from,$date_to);
        $n = 20;
        $p = (int)Tools::getValue('p');
        if ($p < 1) $p = 1;
        $start = $p - 4;
        if ($start < 1) $start = 1;
        $pagination = '';

        if($total > $n){
            $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/admin/pagination.tpl';
            $pages_nb = ceil($total/$n);
            $stop = (int)($p + 4);
            if ($stop > $pages_nb) $stop = (int)$pages_nb;
            Context::getContext()->smarty->assign(array(
                'page' => $pages_nb,
                'pages_nb'=>$pages_nb,
                'prev_p' => $p != 1 ? $p - 1 : 1,
                'next_p' => (int)$p + 1  > $pages_nb ? $pages_nb : $p + 1,
                'requestPage' => $this->context->link->getAdminLink('AdminGformanalytics',true),
                'p' => $p,
                'n' => $n,
                'start' => $start,
                'stop' => $stop,
                'total' => $total,
                'has'=>$total,
                'link'=>Context::getContext()->link,
            ));
            $pagination = Context::getContext()->smarty->fetch($tpl);
        }



        /* get analytic data */
        $analytics_datas = array();
        if($total > 0) {
            $sql = 'SELECT ga.*, gl.title,c.firstname,c.lastname 
                FROM `' . _DB_PREFIX_ . 'gform_analytics` ga
                LEFT JOIN `' . _DB_PREFIX_ . 'gformbuilderpro_lang` gl ON(ga.id_gformbuilderpro = gl.id_gformbuilderpro AND gl.id_lang = ' . (int)Context::getContext()->language->id . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON(ga.id_customer = c.id_customer)
                WHERE ga.date_add BETWEEN "' . pSQL($date_from) . ' 00:00:00" AND "' . pSQL($date_to) . ' 23:59:59" 
                ' . ($id_gformbuilderpro > 0 ? ' AND ga.id_gformbuilderpro=' . (int)$id_gformbuilderpro : '') . ' 
                ORDER BY ga.date_add DESC LIMIT ' . (int)(($p - 1) * $n) . ', ' . (int)$n;
            $analytics_datas = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
        if($analytics_datas){
            $id_shop_group = (int)Shop::getGroupFromShop((int)Context::getContext()->shop->id);
            $blacklist_ip = Configuration::get('GF_BLACKLISTED_IP', null, (int)$id_shop_group, (int)Context::getContext()->shop->id);
            $blacklist_ips = explode(',',$blacklist_ip);
            foreach ($analytics_datas as &$analytics_data){
                if(in_array($analytics_data['ip_address'],$blacklist_ips)){
                    $analytics_data['banned'] = 1;
                }else{$analytics_data['banned'] = 0;}
            }
        }

        if(!$ajax) {
            Context::getContext()->smarty->assign(
                array(
                    'isps17'=>version_compare(_PS_VERSION_, '1.7', '>='),
                    'isps15'=>version_compare(_PS_VERSION_, '1.6'),
                    'calendar'=>($calendar_helper != null ? $calendar_helper->generate() : ''),
                    'fields_value'=>$fields_value,
                    'gforms'=>$forms,
                    'id_gformbuilderpro'=>(int)$id_gformbuilderpro,
                    'analytics_datas'=>$analytics_datas,
                    'pagination'=>$pagination,
                    'customer_link'=>$this->context->link->getAdminLink('AdminCustomers',true).'&viewcustomer&id_customer=',
                )
            );
            $this->base_tpl_view = 'analytics.tpl';
            return parent::renderView();
        }else{
            $result = array(
                'analytics_datas'=>$analytics_datas,
                'pagination'=>$pagination,
                'ban_title'=>$this->l('Ban Ip'),
                'gunbantitle'=>$this->l('UnBan Ip'),
                'click_to_view'=>$this->l('Click to view location'),
                'customer_link'=>$this->context->link->getAdminLink('AdminCustomers',true).'&viewcustomer&id_customer=',
                
            );
            die(Tools::jsonEncode($result));
        }

    }
    public function getTotalAnalyticdata($id_gformbuilderpro,$date_from,$date_to){
        $sql = 'SELECT COUNT(id_gform_analytics) 
                FROM `' . _DB_PREFIX_ . 'gform_analytics` ga
                WHERE ga.date_add BETWEEN "' . pSQL($date_from) . ' 00:00:00" AND "' . pSQL($date_to) . ' 23:59:59" 
                '. ($id_gformbuilderpro > 0 ? ' AND ga.id_gformbuilderpro='.(int)$id_gformbuilderpro : '');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public function getTotalBrowser($id_gformbuilderpro,$date_from,$date_to){
        $sql = 'SElECT browser,COUNT(id_gform_analytics) as total
                FROM `' . _DB_PREFIX_ . 'gform_analytics` 
                WHERE date_add BETWEEN "' . pSQL($date_from) . ' 00:00:00" AND "' . pSQL($date_to) . ' 23:59:59" 
                '. ($id_gformbuilderpro > 0 ? ' AND id_gformbuilderpro='.(int)$id_gformbuilderpro : '').' 
                GROUP BY browser';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }
    public function getTotalPlatform($id_gformbuilderpro,$date_from,$date_to){
        $sql = 'SElECT platform,COUNT(id_gform_analytics) as total
                FROM `' . _DB_PREFIX_ . 'gform_analytics` 
                WHERE date_add BETWEEN "' . pSQL($date_from) . ' 00:00:00" AND "' . pSQL($date_to) . ' 23:59:59" 
                '. ($id_gformbuilderpro > 0 ? ' AND id_gformbuilderpro='.(int)$id_gformbuilderpro : '').' 
                GROUP BY platform';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

}