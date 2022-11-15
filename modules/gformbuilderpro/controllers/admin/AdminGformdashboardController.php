<?php
/**
 * The file is controller. Do not modify the file if you want to upgrade the module in future
 *
 * @author    Globo Software Solution JSC <contact@globosoftware.net>
 * @copyright  2017 Globo., Jsc
 * @license   please read license in file license.txt
 * @link	     http://www.globosoftware.net
 */

class AdminGformdashboardController extends ModuleAdminController
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
        $this->toolbar_title[] = $this->l('Dashboard');
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
        parent::postProcess();
    }
    public function renderView() {
        $date_from = date('Y-m-').'01';
        $date_to = date('Y-m-d',strtotime('+1 day', strtotime(date('Y-m-d'))));
        $viewsdatas = $this->module->getAllView(0, $date_from, $date_to, 'day', true);
        $submitdatas = $this->module->getAllSubmit(0, $date_from, $date_to, 'day', true);
        $replydatas = $this->module->getAllReply(0, $date_from, $date_to, 'day', true);
        if(version_compare(_PS_VERSION_, '1.6', '>=')){
            $calendar_helper = new HelperCalendar();
            $calendar_helper->setDateFrom($date_from);
            $calendar_helper->setDateTo($date_to);
            $calendar_helper->setCompareDateFrom(null);
            $calendar_helper->setCompareDateTo(null);
            $calendar_helper->setCompareOption(0);
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
                'mainchartdatas' => $mainchartdatas,
                'gchart_date_format' => Context::getContext()->language->date_format_lite
            ));
        }
        $this->context->smarty->assign(array(
            'isps15'=>version_compare(_PS_VERSION_, '1.6'),
            'totalform'=>(int)$this->getTotalForms(),
            'totalsubmited'=>(int)$this->getTotalSubmitted(),
            'totalreply'=>(int)$this->getTotalSubmittedReply(),
            'totalunread'=>(int)$this->getTotalUnreadSubmitted(),
            'totalstar'=>(int)$this->getTotalSubmitedStar(),
            'totalbanip'=>(int)$this->getTotalBannedIp(),
            'lastestsubmitted'=>$this->getLastestSubmitted(),
            'request_link'=>$this->context->link->getAdminLink('AdminGformrequest',true),
            'analytics_link'=>$this->context->link->getAdminLink('AdminGformanalytics',true),
            'formmanager_link'=>$this->context->link->getAdminLink('AdminGformmanager',true),
            'csv_link'=>$this->context->link->getAdminLink('AdminGformmanager',true),
            'setting_link'=>$this->context->link->getAdminLink('AdminGformconfig',true)
        ));
        $this->base_tpl_view = 'dashboard.tpl';
        return parent::renderView();
    }
    public function getTotalForms(){
        $sql ='SELECT COUNT(id_gformbuilderpro) 
               FROM `' . _DB_PREFIX_ . 'gformbuilderpro`';
        return (int)Db::getInstance()->getValue($sql);
    }
    public function getTotalSubmitted(){
        $sql ='SELECT COUNT(id_gformrequest) 
               FROM `' . _DB_PREFIX_ . 'gformrequest`';
        return (int)Db::getInstance()->getValue($sql);
    }
    public function getTotalSubmittedReply(){
        $sql ='SELECT COUNT(id_gformrequest_reply) 
               FROM `' . _DB_PREFIX_ . 'gformrequest_reply` 
               GROUP BY id_gformrequest';
        return (int)Db::getInstance()->getValue($sql);
    }
    public function getTotalUnreadSubmitted(){
        $sql ='SELECT COUNT(id_gformrequest) 
               FROM `' . _DB_PREFIX_ . 'gformrequest` 
               WHERE viewed = 0';
        return (int)Db::getInstance()->getValue($sql);
    }
    public function getTotalSubmitedStar(){
        $sql ='SELECT COUNT(id_gformrequest) 
               FROM `' . _DB_PREFIX_ . 'gformrequest` 
               WHERE star = 1';
        return (int)Db::getInstance()->getValue($sql);
    }
    public function getTotalBannedIp(){
        $id_shop_group = Shop::getContextShopGroupID();
        $id_shop = Shop::getContextShopID();
        $blacklisted_ips = Configuration::get('GF_BLACKLISTED_IP', null, $id_shop_group, $id_shop);
        if($blacklisted_ips != ''){
            $blacklistedips = explode(',',$blacklisted_ips);
            if(is_array($blacklistedips) && $blacklistedips)
                return (int)count($blacklistedips);
        }
        return 0;
    }
    public function getLastestSubmitted(){
        $sql ='SELECT *
               FROM `' . _DB_PREFIX_ . 'gformrequest` 
               ORDER BY date_add DESC 
               LIMIT 5';
        return Db::getInstance()->executeS($sql);
    }
}