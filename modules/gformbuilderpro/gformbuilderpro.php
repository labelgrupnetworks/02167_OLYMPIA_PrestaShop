<?php
/**
* This is main class of module.
*
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright  2021 Globo., Jsc
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/

if (!defined("_PS_VERSION_"))
    exit;
include_once(_PS_MODULE_DIR_ . 'gformbuilderpro/classes/gformbuilderproModel.php');
include_once(_PS_MODULE_DIR_ . 'gformbuilderpro/classes/gformbuilderprofieldsModel.php');
include_once(_PS_MODULE_DIR_ . 'gformbuilderpro/classes/gformrequestModel.php');
class Gformbuilderpro extends Module
{
    public function __construct()
    {
        $this->name = "gformbuilderpro";
        $this->tab = "content_management";
        $this->version = "2.0.1";
        $this->author = "Globo Jsc";
        $this->need_instance = 1;
        $this->bootstrap = 1;
        $this->module_key = '0852f50ec236e316fc6931ebac6a4145';
        parent::__construct();
        $this->displayName = $this->l('Form Builder Pro - Customizable any kind of Form');
        $this->description = $this->l('Allow you to create any kind of forms for your website with Bootstrap & Responsive.');

        if (version_compare(_PS_VERSION_, '1.6.0.0 ', '>='))
            $this->ps_versions_compliancy = array('min' => '1.6.0.0', 'max' => _PS_VERSION_);
        else    $this->ps_versions_compliancy = array('min' => '1.5.0.0', 'max' => '1.5.99.99'); /** Fix bug install in ps v1.5 */
    }
    public function install()
    {
        if (Shop::isFeatureActive()){
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        return parent::install()
            && $this->_createTables()
            && $this->_createTab()
            && $this->installConfigData()
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('actionAdminControllerSetMedia')
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayGform')
            && $this->registerHook('moduleRoutes')
            /* update GDPR Compliance */
            && $this->registerHook('registerGDPRConsent')
            && $this->registerHook('actionDeleteGDPRCustomer')
            && $this->registerHook('actionExportGDPRData')
            && $this->registerHook('actionMailAlterMessageBeforeSend')

            && $this->registerHook('displayGorderreference') /** from vs 1.3.2 */

            /* #update GDPR Compliance */
            && Configuration::updateValue('GF_PRODUCT_TYPE',ImageType::getFormatedName('home'));
    }
    public function uninstall()
    {
        return parent::uninstall()
            && $this->_deleteTables()
            && $this->_deleteTab()
            && $this->unregisterHook("displayBackOfficeHeader")
            && $this->unregisterHook("actionAdminControllerSetMedia")
            && $this->unregisterHook("displayHeader")
            && $this->unregisterHook("displayGform")
            && $this->unregisterHook("moduleRoutes")
            /* update GDPR Compliance */
            && $this->unregisterHook("registerGDPRConsent")
            && $this->unregisterHook("actionDeleteGDPRCustomer")
            && $this->unregisterHook("actionExportGDPRData")
            && $this->unregisterHook('actionMailAlterMessageBeforeSend')
            && $this->unregisterHook('displayGorderreference') /** from vs 1.3.2 */
            /* #update GDPR Compliance */
            ;
    }
    public function _createTables()
    {
        $res = (bool) Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gformbuilderpro` (
                `id_gformbuilderpro` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `active` tinyint(1) unsigned NOT NULL,
                `sendtosender` tinyint(1) unsigned NOT NULL,
                `usingajax` tinyint(1) unsigned NOT NULL,
                `saveemail` tinyint(1) unsigned NOT NULL,
                `requiredlogin` tinyint(1) unsigned NOT NULL,
                `hooks` text NULL,
                `formtemplate` MEDIUMTEXT NULL,
                `fields` text NULL,
                `sendto` text NULL,
                `sender`  text NULL,
                `sender_name` text NULL DEFAULT "",
                `autoredirect` TINYINT(1) NULL DEFAULT  "0",
                `timedelay` INT(10) NULL DEFAULT  "0",
                `redirect_link` TEXT NULL,
                `ispopup` TINYINT(1) NULL DEFAULT  "0",
                `using_condition` TINYINT(1) NULL DEFAULT  "0",
                `condition_configs` MEDIUMTEXT NULL,
                `autostar` TINYINT(1) NULL DEFAULT  "0",
                `admin_attachfiles` text NULL DEFAULT "",
                `sender_attachfiles` text NULL DEFAULT "",
                `mailchimp` TINYINT(1) NULL DEFAULT  "0",
                `klaviyo` TINYINT(1) NULL DEFAULT  "0",
                `zapier` TINYINT(1) NULL DEFAULT  "0",
                `customcss`  text NULL,
                PRIMARY KEY (`id_gformbuilderpro`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
        $res &= (bool) Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gformbuilderpro_lang` (
                    `id_gformbuilderpro` int(10) unsigned NOT NULL,
                    `id_lang` int(10) unsigned NOT NULL,
                    `title` varchar(255) NOT NULL,
                    `rewrite` varchar(255)  NULL,
                    `metakeywords` varchar(255)  NULL,
                    `metadescription` text  NULL,
                    `subject` text  NULL,
                    `subjectsender` text  NULL,
                    `emailtemplate` MEDIUMTEXT  NULL,
                    `emailtemplatesender` MEDIUMTEXT  NULL,
                    `success_message` text  NULL,
                    `error_message` text  NULL,
                    `popup_label` text  NULL,
                    `replysubject` text  NULL,
                    `replyemailtemplate` MEDIUMTEXT  NULL,
                    `redirect_link_lang` TEXT NULL,
                    PRIMARY KEY (`id_gformbuilderpro`,`id_lang`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
            ');
        $res &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gformbuilderpro_shop` (
                `id_gformbuilderpro` int(10) unsigned NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_gformbuilderpro`,`id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
        $res &= (bool) Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gformbuilderprofields` (
                `id_gformbuilderprofields` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `labelpos` tinyint(2) unsigned NOT NULL,
                `type` varchar(255) NOT NULL,
                `name` varchar(255) NOT NULL,
                `idatt` varchar(255) NULL,
                `classatt` varchar(255) NULL,
                `required` tinyint(1) unsigned NOT NULL,
                `validate` varchar(255) NULL,
                `extra` text NULL,
                `multi` tinyint(1) unsigned NOT NULL,
                `dynamicval` VARCHAR(255) NULL DEFAULT "",
                `extra_option` tinyint(1) unsigned NOT NULL,
                `condition` tinyint(1) unsigned NOT NULL,
                `condition_display` tinyint(1) unsigned NOT NULL,
                `condition_must_match` tinyint(1) unsigned NOT NULL,
                `condition_listoptions` text NULL,
                PRIMARY KEY (`id_gformbuilderprofields`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
        $res &= (bool) Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gformbuilderprofields_lang` (
                    `id_gformbuilderprofields` int(10) unsigned NOT NULL,
                    `id_lang` int(10) unsigned NOT NULL,
                    `label` varchar(255) NOT NULL,
                    `value` text  NULL,
                    `placeholder` text  NULL,
                    `description` text  NULL,
                    PRIMARY KEY (`id_gformbuilderprofields`,`id_lang`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
            ');
        $res &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gformbuilderprofields_shop` (
                `id_gformbuilderprofields` int(10) unsigned NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_gformbuilderprofields`,`id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
        $res &= (bool) Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gformrequest` (
                `id_gformrequest` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_gformbuilderpro` int(10) unsigned NOT NULL,
                `user_ip` varchar(255) NULL,
                `sendto` text NULL,
                `user_email` varchar(255) NULL,
                `sender` text NULL,
                `sender_name` text NULL DEFAULT "",
                `id_lang` int(10) unsigned NULL,
                `subject` text NULL,
                `request` MEDIUMTEXT  NULL,
                `attachfiles` text  NULL,
                `jsonrequest` MEDIUMTEXT  NULL,
                `date_add` datetime DEFAULT NULL,
                `star` TINYINT(1) NULL DEFAULT  "0",
                `viewed` TINYINT(1) NULL DEFAULT  "0",
                `status` int(10) NULL DEFAULT  "0",
                PRIMARY KEY (`id_gformrequest`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
        /* From version 1.2.0 */
        $res &= (bool) Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gformrequest_reply` (
                `id_gformrequest_reply` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_gformrequest` int(10) unsigned NOT NULL,
                `replyemail` text NULL,
                `subject` text NULL,
                `request` MEDIUMTEXT  NULL,
                `date_add` datetime DEFAULT NULL,
                PRIMARY KEY (`id_gformrequest_reply`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
        $res &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gformrequest_shop` (
                `id_gformrequest` int(10) unsigned NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_gformrequest`,`id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
        /* from version 1.3.0 */
        $res &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gform_analytics` (
                `id_gform_analytics` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_gformbuilderpro` int(10) unsigned NOT NULL,
                `ip_address` varchar(255) DEFAULT NULL,
                `browser` varchar(255) DEFAULT NULL,
                `browser_version` varchar(255) DEFAULT NULL,
                `user_agent` text DEFAULT NULL,
                `platform` varchar(255) DEFAULT NULL,
                `id_customer` int(10) NULL DEFAULT  "0",
                `date_add` datetime NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_gform_analytics`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
        /* from version 1.3.2 */
        $res &= Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gform_mailchimp_klaviyo_map` (
                    `id_gformbuilderprofields` int(10) unsigned NOT NULL,
                    `mailchimp_tag` text DEFAULT NULL,
                    `klaviyo_label` text DEFAULT NULL,
                    PRIMARY KEY (`id_gformbuilderprofields`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
            ');
        $res &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gform_integration_map` (
                `id_gformbuilderpro` int(10) unsigned NOT NULL,
                `mailchimp_list` text DEFAULT NULL,
                `klaviyo_list` text DEFAULT NULL,
                `webhook_url` text DEFAULT NULL,
                PRIMARY KEY (`id_gformbuilderpro`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
        return $res;
    }
    public function _deleteTables()
    {
        return Db::getInstance()->execute('
                DROP TABLE IF EXISTS    `' . _DB_PREFIX_ . 'gformbuilderpro`,
                                        `' . _DB_PREFIX_ . 'gformbuilderpro_lang`,
                                        `' . _DB_PREFIX_ . 'gformbuilderpro_shop`,
                                        `' . _DB_PREFIX_ . 'gformbuilderprofields`,
                                        `' . _DB_PREFIX_ . 'gformbuilderprofields_lang`,
                                        `' . _DB_PREFIX_ . 'gformbuilderprofields_shop`,
                                        `' . _DB_PREFIX_ . 'gformrequest`,
                                        `' . _DB_PREFIX_ . 'gformrequest_reply`,
                                        `' . _DB_PREFIX_ . 'gformrequest_shop`,
                                        `' . _DB_PREFIX_ . 'gform_analytics`,
                                        `' . _DB_PREFIX_ . 'gform_mailchimp_klaviyo_map`,
                                        `' . _DB_PREFIX_ . 'gform_integration_map`;
        ');
    }
    public function _createTab()
    {
        $res = true;
        $tabparent = "AdminGformbuilderpro";
        $id_parent = Tab::getIdFromClassName($tabparent);
        if(!$id_parent){
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = "AdminGformbuilderpro";
            $tab->name = array();
            foreach (Language::getLanguages() as $lang){
                $tab->name[$lang["id_lang"]] = $this->l('Form Builder Pro');
            }
            $tab->id_parent = 0;
            $tab->module = $this->name;
            $res &= $tab->add();
            $id_parent = $tab->id;
        }
        $subtabs = array(
            array(
                'class'=>'AdminGformdashboard',
                'name'=>$this->l('Dashboard')
            ),
            array(
                'class'=>'AdminGformconfig',
                'name'=>$this->l('Settings')
            ),
            array(
                'class'=>'AdminGformmanager',
                'name'=>$this->l('Forms')
            ),
            array(
                'class'=>'AdminGformrequest',
                'name'=>$this->l('Received Data')
            ),
            array(
                'class'=>'AdminGformrequestexport',
                'name'=>$this->l('CSV Export')
            ),
            array(
                'class'=>'AdminGformanalytics',
                'name'=>$this->l('Analytics')
            ),
        );
        foreach($subtabs as $subtab){
            $idtab = Tab::getIdFromClassName($subtab['class']);
            if(!$idtab){
                $tab = new Tab();
                $tab->active = 1;
                $tab->class_name = $subtab['class'];
                $tab->name = array();
                foreach (Language::getLanguages() as $lang){
                    $tab->name[$lang["id_lang"]] = $subtab['name'];
                }
                $tab->id_parent = $id_parent;
                $tab->module = $this->name;
                $res &= $tab->add();
            }
        }
        return $res;
    }
    public function _deleteTab()
    {
        $id_tabs = array('AdminGformconfig','AdminGformmanager','AdminGformrequest','AdminGformrequestexport','AdminGformdashboard','AdminGformanalytics','AdminGformbuilderpro');
        foreach($id_tabs as $id_tab){
            $idtab = Tab::getIdFromClassName($id_tab);
            $tab = new Tab((int)$idtab);
            $parentTabID = $tab->id_parent;
            $tab->delete();
            $tabCount = Tab::getNbTabs((int)$parentTabID);
            if ($tabCount == 0){
                $parentTab = new Tab((int)$parentTabID);
                $parentTab->delete();
            }
        }
        return true;
    }
    public function installConfigData(){
        $res = true;
        $shop_groups_list = array();
		$shops = Shop::getContextListShopID();
        $shop_context = Shop::getContext();
        $res &= Configuration::updateValue('GF_FIELD_WIDTH_DEFAULT', 12);
        $res &= Configuration::updateValue('GF_FIELD_WIDTH_MOBILE_DEFAULT', 12);
        $res &= Configuration::updateValue('GF_FIELD_WIDTH_TABLET_DEFAULT', 12);
		$res &= Configuration::updateValue('GF_GROUP_WIDTH_DEFAULT', 12);
		foreach ($shops as $shop_id)
		{
			$shop_group_id = (int)Shop::getGroupFromShop((int)$shop_id, true);
			if (!in_array($shop_group_id, $shop_groups_list))
				$shop_groups_list[] = (int)$shop_group_id;
			$res &= Configuration::updateValue('GF_FIELD_WIDTH_DEFAULT', 12, false, (int)$shop_group_id, (int)$shop_id);
            $res &= Configuration::updateValue('GF_FIELD_WIDTH_MOBILE_DEFAULT', 12, false, (int)$shop_group_id, (int)$shop_id);
            $res &= Configuration::updateValue('GF_FIELD_WIDTH_TABLET_DEFAULT', 12, false, (int)$shop_group_id, (int)$shop_id);
			$res &= Configuration::updateValue('GF_GROUP_WIDTH_DEFAULT', 12, false, (int)$shop_group_id, (int)$shop_id);
        }
		/* Update global shop context if needed*/
		switch ($shop_context)
		{
			case Shop::CONTEXT_ALL:
				$res &= Configuration::updateValue('GF_FIELD_WIDTH_DEFAULT', 12);
                $res &= Configuration::updateValue('GF_FIELD_WIDTH_MOBILE_DEFAULT', 12);
                $res &= Configuration::updateValue('GF_FIELD_WIDTH_TABLET_DEFAULT', 12);
				$res &= Configuration::updateValue('GF_GROUP_WIDTH_DEFAULT', 12);
                if (count($shop_groups_list))
				{
					foreach ($shop_groups_list as $shop_group_id)
					{
						$res &= Configuration::updateValue('GF_FIELD_WIDTH_DEFAULT', 12, false, (int)$shop_group_id);
                        $res &= Configuration::updateValue('GF_FIELD_WIDTH_MOBILE_DEFAULT', 12, false, (int)$shop_group_id);
                        $res &= Configuration::updateValue('GF_FIELD_WIDTH_TABLET_DEFAULT', 12, false, (int)$shop_group_id);
						$res &= Configuration::updateValue('GF_GROUP_WIDTH_DEFAULT', 12, false, (int)$shop_group_id);
                    }
				}
				break;
			case Shop::CONTEXT_GROUP:
				if (count($shop_groups_list))
				{
					foreach ($shop_groups_list as $shop_group_id)
					{
						$res &= Configuration::updateValue('GF_FIELD_WIDTH_DEFAULT', 12, false, (int)$shop_group_id);
                        $res &= Configuration::updateValue('GF_FIELD_WIDTH_MOBILE_DEFAULT', 12, false, (int)$shop_group_id);
                        $res &= Configuration::updateValue('GF_FIELD_WIDTH_TABLET_DEFAULT', 12, false, (int)$shop_group_id);
						$res &= Configuration::updateValue('GF_GROUP_WIDTH_DEFAULT', 12, false, (int)$shop_group_id);
                    }
				}
				break;
		}
        return $res;
    }
    public function setBrowserAnalytics($id_form,$ip_address,$id_customer=0,$id_shop = 0){
        /* check is exist */
        if($id_form > 0){
            if($id_shop == 0) $id_shop = (int)Context::getContext()->shop->id;
            include_once(_PS_MODULE_DIR_ . 'gformbuilderpro/classes/BrowserLib.php');
            $BrowserLib = new BrowserLib();
            $browser = $BrowserLib->getBrowser();
            $platform = $BrowserLib->getPlatform();
            $sql = 'SELECT id_gform_analytics
                    FROM `' . _DB_PREFIX_ . 'gform_analytics`
                    WHERE id_gformbuilderpro = '.(int)$id_form.' AND
                        ip_address = "'.pSql($ip_address).'" AND
                        id_customer = '.(int)$id_customer.' AND
                        browser = "'.pSql($browser).'" AND
                        platform = "'.pSql($platform).'" AND
                        DAY(date_add) ="'.pSql(date('d')).'" AND
                        MONTH(date_add) ="'.pSql(date('m')).'" AND
                        YEAR(date_add) ="'.pSql(date('Y')).'"';
            $nbr = Db::getInstance()->getValue($sql);
            if(!$nbr){
                $browser_version = '';
                $user_agent = '';
                if($browser != ''){
                    $browser_version = $BrowserLib->getVersion();
                    $user_agent = $BrowserLib->getUserAgent();
                }else{
                    $browser = $this->l('Unknown');
                }
                $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'gform_analytics`(id_gformbuilderpro,ip_address,id_customer,browser,browser_version,user_agent,platform,date_add,id_shop) VALUES
                ('.(int)$id_form.',"'.pSql($ip_address).'",'.(int)$id_customer.',"'.pSql($browser).'","'.pSql($browser_version).'","'.pSql($user_agent).'","'.pSql($platform).'","'.pSql(date('Y-m-d H:i:s')).'",'.(int)$id_shop.')';
                Db::getInstance()->execute($sql);
            }
        }
    }
    public function getContent()
	{
        if (Tools::isSubmit('duplicateField')){
            $result = array(
                'error'=>'1',
                'warrning'=>''
            );
            $id_gformbuilderprofield = (int)Tools::getValue('id_gformbuilderprofield');
            if($id_gformbuilderprofield > 0){
                $result = gformbuilderprofieldsModel::duplicateField($id_gformbuilderprofield);
                $result['object']= new gformbuilderprofieldsModel($result['id'],null,(int)Context::getContext()->shop->id);
                if (isset($result['object']->condition_listoptions)) {
                    $result['object']->condition_listoptions = Tools::jsonDecode($result['object']->condition_listoptions, true);
                }
            }
            die(Tools::jsonEncode($result));
        }
        elseif (Tools::isSubmit('duplicateGroupField')) {
            $result = array(
                'error' => '1',
                'warrning' => '',
                'duplicatedatas' => array()
            );
            $id_gformbuilderprofields = Tools::getValue('id_gformbuilderprofields');
            $res = 0;
            if($id_gformbuilderprofields !=''){
                $IDs = array_map('intval', explode('_', $id_gformbuilderprofields));
                if($IDs){
                    foreach ($IDs as $id_field){
                        if(!$res && $id_field > 0){
                            $duplicatedata = gformbuilderprofieldsModel::duplicateField((int)$id_field);
                            if($duplicatedata['error'] == '1'){
                                $res = 1;
                                $result['warrning'] = $duplicatedata['warrning'];
                                if($result['warrning'] == '') $result['warrning'] = $this->l('Something wrong,please try again.');
                                break;
                            }else{
                                $duplicatedata['object']= new gformbuilderprofieldsModel($duplicatedata['id'],null,(int)Context::getContext()->shop->id);
                                $result['duplicatedatas'][(int)$id_field] = $duplicatedata;
                            }
                        }

                    }
                    $result['error'] = $res;
                }
            }else $result['warrning'] = $this->l('Can not load field id.');
            die(Tools::jsonEncode($result));
        }
	   elseif (Tools::isSubmit('getThumb')){
	        $extension = array('png','gif','jpg','jpeg','bmp','svg');
	        $listthumbs = array();
            $thumbsdir = opendir(_PS_MODULE_DIR_.'gformbuilderpro/views/img/thumbs/');
    		while (($file = readdir($thumbsdir)) !== false) {
    			if(in_array(Tools::strtolower(Tools::substr($file, -3)), $extension) || in_array(Tools::strtolower(Tools::substr($file, -4)), $extension)){
    			     $listthumbs[] = $file;
    			}
    		}
    		closedir($thumbsdir);
            die(implode(',',$listthumbs));
	   }
	   elseif (Tools::isSubmit('addThumb')){
            $thumbs = array();
           $extension = array('png','gif','jpg','jpeg','bmp','svg');
            if (isset($_FILES['file']['name']) && !empty($_FILES['file']['name']) && !empty($_FILES['file']['tmp_name']))
            {
                foreach(array_keys($_FILES['file']['name']) as $key){
                    if($_FILES['file']['name'][$key]){
                        if(in_array(Tools::strtolower(Tools::substr($_FILES['file']['name'][$key], -3)), $extension) || in_array(Tools::strtolower(Tools::substr($_FILES['file']['name'][$key], -4)), $extension)){
                	        $file_attachment = null;
                			$file_attachment['rename'] = uniqid(). Tools::strtolower(Tools::substr($_FILES['file']['name'][$key], -5));
                			$file_attachment['tmp_name'] = $_FILES['file']['tmp_name'][$key];
                			$file_attachment['name'] = $_FILES['file']['name'][$key];
                            if (isset($file_attachment['rename']) && !empty($file_attachment['rename']) && rename($file_attachment['tmp_name'], _PS_MODULE_DIR_.'gformbuilderpro/views/img/thumbs/'.basename($file_attachment['rename']))) {
                                @chmod(_PS_MODULE_DIR_.'gformbuilderpro/views/img/thumbs/'.basename($file_attachment['rename']), 0664);
                                $thumbs[] = $file_attachment['rename'];
                            }
                        }
                    }
                }
            }
           die(implode(',',$thumbs));
        }elseif (Tools::isSubmit('getFormTypeConfig')){
	       $typefield = Tools::getValue('typefield');
           $id_gformbuilderprofields = (int)Tools::getValue('id_gformbuilderprofields',0);
           echo $this->hookConfigFieldAjax(array('typefield' => $typefield,'id'=>$id_gformbuilderprofields));
	       die();
        }elseif (Tools::isSubmit('addShortcode')){
           $id_field = (int)Tools::getValue('id_gformbuilderprofields',0);
           if($id_field){
                $fieldObj = new gformbuilderprofieldsModel($id_field);
           }else{
                $fieldObj = new gformbuilderprofieldsModel();
           }
           $fieldObj->name = Tools::getValue('name','');
           $fieldObj->required = (int)Tools::getValue('required',0);
           $fieldObj->labelpos = (int)Tools::getValue('labelpos',1);
           $fieldObj->idatt = Tools::getValue('idatt','');
           $fieldObj->classatt = Tools::getValue('classatt','');
           $fieldObj->validate = Tools::getValue('validate','');
           $fieldObj->type = Tools::getValue('type','');
           $fieldObj->extra = Tools::getValue('extra','');
           $fieldObj->multi = (bool)Tools::getValue('multi','0');
           $fieldObj->dynamicval = Tools::getValue('dynamicval','');
           /*new version 1.3.6*/

           $fieldObj->extra_option = Tools::getValue('extra_option', 0);
           $fieldObj->condition    = Tools::getValue('condition',0);
           $fieldObj->condition_display = Tools::getValue('condition_display',0);
           $fieldObj->condition_must_match = Tools::getValue('condition_must_match',0);
           $fieldObj->condition_listoptions = Tools::jsonEncode(Tools::getValue('listoptions', array()));
           $languages = Language::getLanguages(false);
            if ($fieldObj->type == 'wholesale') {
                $option_whosales  = array();
                $gform_idproducts = Tools::getValue('gform_idproducts',array());
                $gform_attribute =  Tools::getValue('gform_attribute',array());
                $gform_products_voucher =  Tools::getValue('gform_products_voucher',array());
                if ($gform_idproducts) {
                    foreach($gform_idproducts as $id_product) {
                        $option_whosales[$id_product]['gform_attribute'] = isset($gform_attribute[$id_product]) ? $gform_attribute[$id_product] : array();
                        $option_whosales[$id_product]['gform_products_voucher'] = isset($gform_products_voucher[$id_product]) ? Tools::jsonDecode($gform_products_voucher[$id_product], true) : array();
                    }
                }
                $fieldObj->extra = Tools::jsonEncode($option_whosales);
            }
            foreach ($languages as $lang)
            {
                    $fieldObj->label[(int)$lang['id_lang']] = Tools::getValue('label_'.(int)$lang['id_lang'],'');
                    $fieldObj->value[(int)$lang['id_lang']] = Tools::getValue('value_'.(int)$lang['id_lang'],'');
                    $fieldObj->description[(int)$lang['id_lang']] = Tools::getValue('description_'.(int)$lang['id_lang'],'');
                    $fieldObj->placeholder[(int)$lang['id_lang']] = Tools::getValue('placeholder_'.(int)$lang['id_lang'],'');
            }
            if($id_field){
                if($fieldObj->update()){
                    $fieldObj->condition_listoptions = Tools::getValue('listoptions', array());
                    if ($fieldObj->type == 'wholesale') {
                        $fieldObj->extra = $option_whosales;
                    }
                    die(Tools::jsonEncode($fieldObj));
                }
            }else{
                if($fieldObj->save()){
                    $fieldObj->id_gformbuilderprofields = (int)$fieldObj->id;
                    $fieldObj->condition_listoptions    = Tools::getValue('listoptions', array());
                    
                    if ($fieldObj->type == 'wholesale') {
                        $fieldObj->extra  = $option_whosales;
                    }
                    die(Tools::jsonEncode($fieldObj));
                }
            }
           die(Tools::jsonEncode(array('id_gformbuilderprofields'=>0)));
        }else
		  Tools::redirectAdmin($this->context->link->getAdminLink('AdminGformdashboard'));
	}
    public function hookConfigFieldAjax($params){
        $useSSL = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? true : false;
        $protocol_content = ($useSSL) ? 'https://' : 'http://';
        $base_uri = $protocol_content.Tools::getHttpHost().__PS_BASE_URI__;
        $result = '';
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;
        if($params['typefield']){
            $typefield = basename($params['typefield'], '.php');
            if(isset($params['id']) && $params['id']){
                $fieldObj = new gformbuilderprofieldsModel($params['id']);
                $typefield = $fieldObj->type;
            }
            $shortcode_dir = _PS_MODULE_DIR_.'gformbuilderpro/classes/fields/';
            if(file_exists($shortcode_dir.$typefield.'.php')){
                $fields_value = array();
                if(version_compare(_PS_VERSION_,'1.6') == -1){
                    $fields_value['psoldversion15'] = -1;
                }else $fields_value['psoldversion15'] = 0;
                $fields_value['base_uri'] = $base_uri;
                require_once($shortcode_dir.$typefield.'.php');
                $Objname = Tools::ucfirst($typefield.'field');
	            $obj = new $Objname;
                $inputs = $obj->getConfig();
                $info = $obj->getInfo();
                $inputs[]= array(
        				'type' => 'hidden',
        				'name' => 'id_gformbuilderprofields'
        			);
                $inputs[]= array(
        				'type' => 'hidden',
        				'name' => 'type'
        			);


                $fields_form = array(
        			'form' => array(
        				'legend' => array(
        					'title' => (isset($info['label']) ? $info['label'] : '').' '.$this->l('Settings'),
        					'icon' => 'icon-cogs'
        				),
        				'input' => $inputs,
        				'submit' => array(
        					'title' => $this->l('Save change'),
        				),
                        'buttons' => array(
                            'cancel' => array(
            					'name' => 'cancelShortcode',
            					'type' => 'submit',
            					'title' => $this->l('Cancel'),
            					'class' => 'btn btn-default pull-left',
            					'icon' => 'process-icon-cancel'
            				),
            			)
        			),
        		);
                $fields_value['type'] = $typefield;
                $languages = Language::getLanguages(false);
                foreach ($languages as $lang)
        		{
        		      $fields_value['placeholder'][(int)$lang['id_lang']] = '';
                      $fields_value['description'][(int)$lang['id_lang']] = '';
                      $fields_value['value'][(int)$lang['id_lang']]  = '';
                }
                $fields_value['labelpos'] = 0;
                $fields_value['validate'] = '';
                $fields_value['id_gformbuilderprofields'] = '';
                $fields_value['multi'] = false;
                $fields_value['required'] = false;
                $fields_value['dynamicval'] = '';

                if(isset($params['id']) && $params['id']){
                    $fields_value['id_gformbuilderprofields'] = $fieldObj->id;
                    $fields_value['labelpos'] = $fieldObj->labelpos;
                    $fields_value['name'] = $fieldObj->name;
                    $fields_value['idatt'] = $fieldObj->idatt;
                    $fields_value['classatt'] = $fieldObj->classatt;
                    $fields_value['required'] = $fieldObj->required;
                    $fields_value['validate'] = $fieldObj->validate;
                    $fields_value['multi'] = (bool)$fieldObj->multi;

                    
                    $fields_value['dynamicval'] = $fieldObj->dynamicval;


                    $fields_value['extra'] = '';
                    if($typefield == 'product'){
                        $fields_value['extra'] = array();
                        $extra = $fieldObj->extra;
                        $producthtml = array();
                        if($extra !=''){
                            $products = explode(',',$extra);
                            foreach($products as $productid){
                                if($productid !=''){
                                    $cover = Product::getCover((int)$productid);
                                    $id_image = 0;
                                    if(isset($cover['id_image'])) $id_image = (int)$cover['id_image'];
                                    $productObj = new Product((int)$productid,false,(int)$id_lang,(int)$id_shop);
                                    $producthtml[(int)$productid] =array(
                                        'id'=>(int)$productid,
                                        'name'=>Product::getProductName((int)$productid,null,(int)$id_lang),
                                        'image_link' =>$this->context->link->getImageLink($productObj->link_rewrite,$id_image,Configuration::get('GF_PRODUCT_TYPE'))
                                    );
                                }
                            }
                        }

                        $fields_value['extra']['products'] = $extra;
                        $fields_value['extra']['html'] = $producthtml;
                    }elseif($typefield == 'colorchoose'){
                        $extra = $fieldObj->extra;
                        $colors = explode(',',$extra);
                        $fields_value['extra'] = array('value'=>$extra,'colors'=>$colors);
                    }elseif($typefield == 'slider' || $typefield == 'spinner'){
                        $extra = $fieldObj->extra;
                        $colors = explode(';',$extra);
                        $fields_value['extra'] = array('value'=>$extra,'extraval'=>$colors);
                    }
                    elseif($typefield == 'imagethumb'){
                        $extra = $fieldObj->extra;
                        $thumbs = explode(',',$extra);
                        $_thumbs = array();
                        if($thumbs)
                            foreach($thumbs as $thumb)
                                if(file_exists(_PS_MODULE_DIR_.'gformbuilderpro/views/img/thumbs/'.$thumb))
                                    $_thumbs[] = $thumb;
                        $fields_value['extra'] = array('value'=>$extra,'thumbs'=>$_thumbs);
                    }else
                        $fields_value['extra'] = $fieldObj->extra;
            		foreach ($languages as $lang)
            		{
            		      $fields_value['label'][(int)$lang['id_lang']] = isset($fieldObj->label[(int)$lang['id_lang']]) ? $fieldObj->label[(int)$lang['id_lang']] : Tools::ucfirst($typefield);
            		      if($typefield == 'checkbox' || $typefield == 'select' || $typefield == 'radio' || $typefield == 'survey'){
            		          $fields_value['value'][(int)$lang['id_lang']] = (isset($fieldObj->value[(int)$lang['id_lang']]) && $fieldObj->value[(int)$lang['id_lang']] !='') ? explode(',',$fieldObj->value[(int)$lang['id_lang']]) : array();
            		      }else
                            $fields_value['value'][(int)$lang['id_lang']] = isset($fieldObj->value[(int)$lang['id_lang']]) ? $fieldObj->value[(int)$lang['id_lang']] : '';

                          $fields_value['placeholder'][(int)$lang['id_lang']] = isset($fieldObj->placeholder[(int)$lang['id_lang']]) ? $fieldObj->placeholder[(int)$lang['id_lang']] : '';

                          if($typefield == 'survey'){
                            $fields_value['description'][(int)$lang['id_lang']] = (isset($fieldObj->description[(int)$lang['id_lang']]) && $fieldObj->description[(int)$lang['id_lang']] !='') ? explode(',',$fieldObj->description[(int)$lang['id_lang']]) : array();
                          }else
                            $fields_value['description'][(int)$lang['id_lang']] = isset($fieldObj->description[(int)$lang['id_lang']]) ? $fieldObj->description[(int)$lang['id_lang']] : '';
                    }
                }else{

                    $fields_value['extra'] = '';
                    if($typefield == 'product'){
                        $fields_value['extra'] = array();
                        $fields_value['extra']['products'] = '';
                        $fields_value['extra']['html'] = array();
                    }elseif($typefield == 'colorchoose'){
                        $fields_value['extra'] = array('value'=>'','colors'=>array());
                    }elseif($typefield == 'slider' || $typefield == 'spinner'){
                        $fields_value['extra'] = array('value'=>'','extraval'=>array());
                    }
                    elseif($typefield == 'imagethumb'){
                        $fields_value['extra'] = array('value'=>'','thumbs'=>array());
                    }elseif($typefield == 'fileupload'){
                        $fields_value['extra'] = 'png,jpg,gif,jpeg,doc,docx,xls,xlsx';
                    }
                    $fields_value['name'] = $typefield.'_'.time();
                    $fields_value['idatt'] = $typefield.'_'.time();
                    $fields_value['classatt'] = $typefield.'_'.time();
                    foreach ($languages as $lang)
            		{
            		      $fields_value['label'][(int)$lang['id_lang']] = Tools::ucfirst($typefield);
            		}
                }
                $fields_value['ajaxaction'] = $this->context->link->getAdminLink('AdminGformmanager');
                $fields_value['loadjqueryselect2'] = 1;
                if(version_compare(_PS_VERSION_,'1.6.0.7') == -1){
                    $fields_value['loadjqueryselect2'] = 0;
                }
        		$helper = new HelperForm();
                $helper->module = new $this->name();
        		$helper->submit_action = 'addShortcode';
                $helper->show_toolbar = false;
        		$helper->table = $this->table;
        		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        		$helper->default_form_language = $lang->id;
        		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        		$this->fields_form = array();

        		$helper->identifier = $this->identifier;
        		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        		$helper->token = Tools::getAdminTokenLite('AdminModules');
        		$helper->tpl_vars = array(
                    'fields_value' => $fields_value,
        			'languages' => $this->context->controller->getLanguages(),
        			'id_language' => $this->context->language->id
        		);
                $html_extra='';
                if(version_compare(_PS_VERSION_,'1.6') == -1){
                    $html_extra_tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/admin/button15.tpl';
                    $html_extra = Context::getContext()->smarty->fetch($html_extra_tpl);
                }
                return $helper->generateForm(array($fields_form)).$html_extra;
            }

        }
        return $result;
    }
    public function hookModuleRoutes($route = '', $detail = array())
	{
	    $routes = array();
	    $use_routes = (bool)Configuration::get('PS_REWRITING_SETTINGS');
        if($use_routes){
            $id_shop = (int)Context::getContext()->shop->id;
            $id_shop_group = (int)Shop::getGroupFromShop($id_shop);
            $routes_url = Configuration::get('GF_FRIENDLY_URL', null, $id_shop_group, $id_shop);
            if($routes_url == '') $routes_url = 'form/{rewrite}-g{id}.html';

    		$routes['module-gformbuilderpro-form'] = array(
    			'controller' => 'form',
    			'rule' => $routes_url,
    			'keywords' => array(
    				'id' => array('regexp' => '[0-9]+', 'param' => 'id'),
    				'rewrite' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
    			),
    			'params' => array(
    				'fc' => 'module',
    				'module' => 'gformbuilderpro',
    			)
    		);
        }
		return $routes;

	}
    public function hookDisplayHeader($params){
        $this->context->controller->addCSS(_MODULE_DIR_.$this->name.'/views/css/front/jquery.minicolors.css');
        $this->context->controller->addCSS(_MODULE_DIR_.$this->name.'/views/css/front/gformbuilderpro.css');
        
        $css_files = Tools::scandir(_PS_MODULE_DIR_.$this->name.'/views/css/front/customcss', 'css');
        if (!empty($css_files)) {
            foreach ($css_files as $css_file) {
                $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/front/customcss/'.$css_file);
            }
        }

        $this->context->controller->addJqueryUI('ui.datepicker');
        $this->context->controller->addJqueryUI('ui.slider');
        $this->context->controller->addJqueryPlugin('fancybox');
        if (version_compare(_PS_VERSION_, '1.6.0.0', '<')){
            $this->context->controller->addJS(_MODULE_DIR_.$this->name.'/views/js/front/gformbuilderpro_oldversion.js');
        }
        $this->context->controller->addJS(_MODULE_DIR_.$this->name.'/views/js/front/tinymce/tinymce.min.js');
        $this->context->controller->addJS(_MODULE_DIR_.$this->name.'/views/js/front/jquery.minicolors.js');
        $this->context->controller->addJS(_MODULE_DIR_.$this->name.'/views/js/front/gformbuilderpro.js');
    }
    public function hookDisplayGform($params){
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;
        $id_form = (int)$params['id'];
        $module = Tools::getValue('module','');
        $id = (int)Tools::getValue('id');
        if($id_form > 0)
            if(($module == 'gformbuilderpro' && $id != $id_form) || ($module != 'gformbuilderpro'))
                return $this->getForm($id_form,$id_lang,$id_shop);
            else
                return '';
        else
            return '';
    }
    public function dynamicHook($name,$id_form=0){


        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;
        $module = Tools::getValue('module','');
        $id = (int)Tools::getValue('id');
        if($id_form > 0){
            if(($module == 'gformbuilderpro' && $id != $id_form) || ($module != 'gformbuilderpro'))
                return $this->getForm((int)$id_form,(int)$id_lang,(int)$id_shop,$name);
            else
                return '';
        }else{
            //get all form in hook
            $html = '';
            $forms = gformbuilderproModel::getAllFormInHook($name);
            if($forms){
                foreach($forms as $form){
                    if(($module == 'gformbuilderpro' && $id != (int)$form['id_gformbuilderpro']) || ($module != 'gformbuilderpro'))
                        $html.=$this->getForm((int)$form['id_gformbuilderpro'],(int)$id_lang,(int)$id_shop,$name);
                }

            }
            return $html;
        };
    }
    public function __call($name, $arguments)
    {
        if (!Validate::isHookName($name))
            return false;
        $hook_name = str_replace('hook', '', $name);
        if (method_exists($this, 'hook'.Tools::ucfirst($hook_name))){
            return call_user_func(array($this, 'hook'.Tools::ucfirst($name)), $arguments);
        }
        else{
            if(isset($arguments[0]['id']) && $arguments[0]['id'] > 0){
                return $this->dynamicHook($hook_name,(int)$arguments[0]['id']);
            } else return $this->dynamicHook($hook_name);
        }

    }
    public function hookDisplayBackOfficeHeader($params){
        $controller_admin = Tools::getValue('controller');
        if(Tools::strtolower($controller_admin)  == 'admingformmanager')
            $this->context->controller->addCss($this->_path.'/views/css/admin/'.$this->name.'.css');
    }
    public function setFormUrl($id,$rewrite,$id_lang=null,$id_shop=null){
        if($id > 0 && $rewrite !=''){
            $params = array(
    			'id' => (int)$id,
    			'rewrite' => $rewrite,
    		);
    	   return Dispatcher::getInstance()->createUrl('module-gformbuilderpro-form', $id_lang, $params,false,'',$id_shop);
        }
    }
    public function getForm($id_form,$id_lang,$id_shop,$hookname=''){
        $isps17 = version_compare(_PS_VERSION_, '1.7', '>=');
        $useSSL = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? true : false;
        $protocol_content = ($useSSL) ? 'https://' : 'http://';
        $base_uri = $protocol_content.Tools::getHttpHost().__PS_BASE_URI__;
         $formObj = new gformbuilderproModel((int)$id_form,(int)$id_lang,(int)$id_shop);
         if(Validate::isLoadedObject($formObj) && (bool)$formObj->active && $formObj->id_gformbuilderpro == (int)$id_form){
            if((bool)$formObj->requiredlogin && !$this->context->customer->isLogged()){
                return '';
            }else{
                $module_dir = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/front/formtemplates/';
                $hooks = explode(',',$formObj->hooks);
                if(in_array($hookname,$hooks) || $hookname==''){

                    /* from version 1.3.0 */
                    $ip_address = Tools::getRemoteAddr();
                    $id_customer = ((isset(Context::getContext()->customer) && Validate::isLoadedObject(Context::getContext()->customer)) ? (int)Context::getContext()->customer->id : 0);
                    $this->setBrowserAnalytics((int)$id_form,$ip_address,(int)$id_customer,(int)$id_shop);
                    /* # from version 1.3.0 */

                    $id_shop_group = (int)Shop::getContextShopGroupID();
                    $url_rewrite = Context::getContext()->link->getModuleLink('gformbuilderpro','form',array('id'=>$id_form,'rewrite'=>$formObj->rewrite));
                    if (!strpos($url_rewrite, 'index.php')){
                        // fix in version 1.5
                        $url_rewrite = str_replace('?module=gformbuilderpro&controller=form','',$url_rewrite);
                    }
                    $customer_address = '';
                    $customer_postcode = '';
                    $customer_city = '';
                    $customer_phone = '';
                    $Conditions = array();
                    if($this->context->customer->isLogged()){
                        $customeraddress = $this->context->customer->getAddresses((int)$id_lang);
                        if($customeraddress){
                            foreach($customeraddress as $customeradd){
                                $customer_address = $customeradd['address1'].' '.$customeradd['address2'];
                                $customer_postcode = $customeradd['postcode'];
                                $customer_city = $customeradd['city'];
                                $customer_phone = (isset($customeradd['phone_mobile']) && $customeradd['phone_mobile'] !='') ? $customeradd['phone_mobile'] : $customeradd['phone'];
                                break;
                            }
                        }
                    }
                    $this->context->smarty->assign(array(
                        'sitekey'=>Configuration::get('GF_RECAPTCHA', null, $id_shop_group, $id_shop),
                        'gmap_key'=>Configuration::get('GF_GMAP_API_KEY', null, $id_shop_group, $id_shop),
                        'customerid'=>($this->context->customer->isLogged()) ? $this->context->customer->id : '0',
                        'customerfirstname'=>($this->context->customer->isLogged()) ? $this->context->customer->firstname : '',
                        'customerlastname'=>($this->context->customer->isLogged()) ? $this->context->customer->lastname : '',
                        'customername'=>($this->context->customer->isLogged()) ? $this->context->customer->firstname.' '.$this->context->customer->lastname : '',
                        'customeremail'=>($this->context->customer->isLogged()) ? $this->context->customer->email : '',
                        'productid'=>(Tools::getValue('id_product')) ? (int)Tools::getValue('id_product') : '0',
                        'productname'=>(Tools::getValue('id_product')) ? Product::getProductName((int)Tools::getValue('id_product'),null,$this->context->language->id) : '',
                        'customercompany'=>($this->context->customer->isLogged() && isset($this->context->customer->company)) ? $this->context->customer->company : '',
                        'customer_address'=>$customer_address,
                        'customer_postcode'=>$customer_postcode,
                        'customer_city'=>$customer_city,
                        'customer_phone'=>$customer_phone,
                        'shopname'=>$this->context->shop->name,
                        'currencyname'=>$this->context->currency->name,
                        'languagename'=>$this->context->language->name,
                        'base_uri'=>$base_uri,
                        'actionUrl'=>$url_rewrite,
                        'required_warrning'=>$this->l('Please fill out this field.'),

                        'button_upload_text'=>$this->l('Choose File'),

                        'formsaveemail'=>$formObj->saveemail,
                        'id_module_gformbuilderpro'=> (int)Module::getModuleIdByName('gformbuilderpro'),

                        'ispopup'=>((isset($formObj->ispopup) && $formObj->ispopup) ? 1 : 0),
                        'popup_label'=>((isset($formObj->ispopup) && $formObj->ispopup) ? $formObj->popup_label : 0),

                    ));
                    $using_v3= (int)Configuration::get('GF_RECAPTCHA_V3', null, $id_shop_group, $id_shop);
                    if($using_v3){
                        $this->context->smarty->assign(array('using_v3'=>1));
                    }
                    //get product data
                    $fields = $formObj->fields;
                    $fieldsData = gformbuilderprofieldsModel::getAllFields($fields,$id_lang,$id_shop);
                    if($fieldsData){
                        foreach($fieldsData as $field){
                            if($field['type'] == 'product'){
                                $productids = explode(',',$field['extra']);
                                if($productids){
                                    $productData = array();
                                    foreach($productids as $productid){
                                        if((int)$productid){
                                            $cover = Product::getCover((int)$productid);
                                            $id_image = 0;
                                            if(isset($cover['id_image'])) $id_image = (int)$cover['id_image'];
                                            $productObj = new Product((int)$productid,false,(int)$id_lang,(int)$id_shop);
                                            if(Validate::isLoadedObject($productObj)){
                                                $productData[(int)$productid] =array(
                                                    'id'=>(int)$productid,
                                                    'name'=>Product::getProductName((int)$productid,null,(int)$id_lang),
                                                    'link'=>$this->context->link->getProductLink($productid,null,null,null,(int)$id_lang,(int)$id_shop),
                                                    'image_link' =>$this->context->link->getImageLink($productObj->link_rewrite,$id_image,Configuration::get('GF_PRODUCT_TYPE'))
                                                );
                                            }
                                        }
                                    }
                                    $this->context->smarty->assign(array(
                                            $field['name'].'product'=>$productData
                                        )
                                    );
                                }

                            } elseif ($field['type'] == 'wholesale')  {
                                if ($field['extra']) {
                                    $productDatas = array();
                                    $wholesale_productids = Tools::jsonDecode($field['extra'], true);
                                    foreach($wholesale_productids as $id_product => $wholesale_product){
                                        if ($id_product) {
                                            $cover = Product::getCover((int)$id_product);
                                            $id_image = 0;
                                            if(isset($cover['id_image'])) $id_image = (int)$cover['id_image'];
                                            $productObj = new Product((int)$id_product,false,(int)$id_lang,(int)$id_shop);
                                            if(Validate::isLoadedObject($productObj)){
                                                $price_product  = 0;
                                                $combinations   = array();
                                                $combination_options   = $wholesale_product['gform_attribute'];
                                                $attributes = $productObj->getAttributesGroups((int)$this->context->language->id);
                                                $vouchers     = $wholesale_product['gform_products_voucher'];
                                                if ($combination_options) {
                                                    foreach ($attributes as $attribute)
                                                    {
                                                        if ($combination_options) {
                                                            if (in_array($attribute['id_product_attribute'], $combination_options)) {
                                                                $combinations[$attribute['id_product_attribute']]['id_product_attribute'] = $attribute['id_product_attribute'];
                                                                if (!isset($combinations[$attribute['id_product_attribute']]['attributes']))
                                                                {
                                                                    $combinations[$attribute['id_product_attribute']]['attributes'] = '';
                                                                }
                                                                $combinations[$attribute['id_product_attribute']]['attributes'] .= $attribute['group_name'].' : '.$attribute['attribute_name'].',';
                                                                $priceDisplay   = Product::getTaxCalculationMethod((int)$this->context->cookie->id_customer);
                                                                if(!$priceDisplay || $priceDisplay == 2) {
                                                                    $price_combin = Product::getPriceStatic((int)$id_product, true, 0, 6, null, false, true, 1);
                                                                    $combinations[$attribute['id_product_attribute']]['price'] =  Tools::displayPrice(Tools::convertPriceFull($price_combin));
                                                                    $combinations[$attribute['id_product_attribute']]['number_price'] =  $price_combin;
                                                                } elseif($priceDisplay == 1) {
                                                                    $price_combin = Product::getPriceStatic((int)$id_product, false, 0, 6, null, false, true, 1);
                                                                    $combinations[$attribute['id_product_attribute']]['price'] =  Tools::displayPrice(Tools::convertPriceFull($price_combin));
                                                                    $combinations[$attribute['id_product_attribute']]['number_price'] =  $price_combin;
                                                                }
                                                            }
                                                        } else {
                                                            $combinations[0]['attributes'] = Product::getProductName((int)$id_product,null,(int)$id_lang);
                                                            $priceDisplay   = Product::getTaxCalculationMethod((int)$this->context->cookie->id_customer);
                                                            if(!$priceDisplay || $priceDisplay == 2) {
                                                                $price_combin = Product::getPriceStatic((int)$id_product, true, 0, 6, null, false, true, 1);
                                                                $combinations[0]['price']        =  Tools::displayPrice(Tools::convertPriceFull($price_combin));
                                                                $combinations[0]['number_price'] =  $price_combin;
                                                            } elseif($priceDisplay == 1) {
                                                                $price_combin = Product::getPriceStatic((int)$id_product, false, 0, 6, null, false, true, 1);
                                                                $combinations[0]['price'] =  Tools::displayPrice(Tools::convertPriceFull($price_combin));
                                                                $combinations[0]['number_price'] =  $price_combin;
                                                            }
                                                        }
                                                    }
                                                }  else {
                                                    $combinations[0]['attributes'] = Product::getProductName((int)$id_product,null,(int)$id_lang);
                                                    $priceDisplay   = Product::getTaxCalculationMethod((int)$this->context->cookie->id_customer);
                                                    if(!$priceDisplay || $priceDisplay == 2) {
                                                        $price_combin = Product::getPriceStatic((int)$id_product, true, 0, 6, null, false, true, 1);
                                                        $combinations[0]['price']        =  Tools::displayPrice(Tools::convertPriceFull($price_combin));
                                                        $combinations[0]['number_price'] =  $price_combin;
                                                    } elseif($priceDisplay == 1) {
                                                        $price_combin = Product::getPriceStatic((int)$id_product, false, 0, 6, null, false, true, 1);
                                                        $combinations[0]['price'] =  Tools::displayPrice(Tools::convertPriceFull($price_combin));
                                                        $combinations[0]['number_price'] =  $price_combin;
                                                    }
                                                }
                                                if ($vouchers) {
                                                    foreach ($vouchers as &$voucher) {
                                                        if ($voucher['type']) {
                                                            $oldCurrency = new Currency((int)$voucher['currency']);
                                                            $voucher['value'] = Tools::convertPriceFull((float)$voucher['value'], $oldCurrency, $this->context->currency);
                                                        }
                                                    }
                                                }
                                                $productDatas[(int)$id_product] =array(
                                                    'id'   => (int)$id_product,
                                                    'price'=> Tools::displayPrice(Tools::convertPriceFull($price_product)),
                                                    'name' => Product::getProductName((int)$id_product,null,(int)$id_lang),
                                                    'link' => $this->context->link->getProductLink($id_product,null,null,null,(int)$id_lang,(int)$id_shop),
                                                    'image_link'  => $this->context->link->getImageLink($productObj->link_rewrite,$id_image,Configuration::get('GF_PRODUCT_TYPE')),
                                                    'combinations'=> $combinations,
                                                    'vouchers'    => $vouchers,
                                                );
                                            }
                                        }
                                    }
                                    $this->context->smarty->assign(array(
                                            $field['name'].'wholesale'=>$productDatas
                                        )
                                    );
                                }
                            }
                            /*new version 1.3.6*/
                            $Conditions[(int)$field['id_gformbuilderprofields']] = array(
                                'condition'             => $field['condition'],
                                'condition_display'     => $field['condition_display'],
                                'condition_must_match'  => $field['condition_must_match'],
                                'condition_listoptions' => $field['condition_listoptions'] !='' ? Tools::jsonDecode($field['condition_listoptions'], true) : array(),
                            );
                        }
                    }

                    $this->context->smarty->assign(array('Conditions'=>Tools::jsonEncode($Conditions)));
                    if($isps17){
                        if(file_exists($module_dir.(int)$formObj->id_gformbuilderpro.'/'.(int)$id_lang.'/'.(int)$id_shop.'_form_codehook.tpl')){
                            return $this->fetch($module_dir.(int)$formObj->id_gformbuilderpro.'/'.(int)$id_lang.'/'.(int)$id_shop.'_form_codehook.tpl');
                        }else{
                            $formObj->parseTpl((int)$id_lang,(int)$id_shop);
                            return $this->fetch($module_dir.(int)$formObj->id_gformbuilderpro.'/'.(int)$id_lang.'/'.(int)$id_shop.'_form_codehook.tpl');
                        }
                    }else{
                        if(file_exists($module_dir.(int)$formObj->id_gformbuilderpro.'/'.(int)$id_lang.'/'.(int)$id_shop.'_form.tpl')){
                            return $this->display(__FILE__, 'views/templates/front/formtemplates/'.(int)$formObj->id_gformbuilderpro.'/'.(int)$id_lang.'/'.(int)$id_shop.'_form.tpl');
                        }else{
                            $formObj->parseTpl((int)$id_lang,(int)$id_shop);
                            return $this->display(__FILE__, 'views/templates/front/formtemplates/'.(int)$formObj->id_gformbuilderpro.'/'.(int)$id_lang.'/'.(int)$id_shop.'_form.tpl');
                        }
                    }
                }else return false;
            }
         }else
            return '';

    }
    public function getFormByShortCode($html=''){

        preg_match_all('/\{(gformbuilderpro:)(.*?)\}/', $html, $matches);


        $customShortCodes = array();

        if(isset($matches[0]) && $matches[0]){
            foreach($matches[0] as $key=>$content)
            {
                $matchNoBrackets = str_replace(array('{','}'),'',$content);
                $shortCodeExploded = explode(':', $matchNoBrackets);
                $customShortCodes['gformbuilderpro'][$key] = $shortCodeExploded[1];

            }

            foreach($customShortCodes as $shortCodeKey=>$shortCode)
            {
                if($shortCodeKey == 'gformbuilderpro')
                {
                    foreach($shortCode as $show)
                    {
                        $testingReplacementText = $this->getForm($show,$this->context->language->id,$this->context->shop->id);
                        $originalShortCode = "{gformbuilderpro:$show}";

                        $html = str_replace($originalShortCode,$testingReplacementText,$html);

                    }
                }
            }
        }
        return $html;
    }
    public function hookActionDeleteGDPRCustomer ($customer)
   {
       if (!empty($customer['email']) && Validate::isEmail($customer['email'])) {
            $sql = "DELETE FROM "._DB_PREFIX_."gformrequest_shop
                    WHERE id_gformrequest IN (
                        SELECT id_gformrequest
                        FROM "._DB_PREFIX_."gformrequest
                        WHERE user_email = '".pSQL($customer['email'])."'
                    )";
            $res = Db::getInstance()->execute($sql);
            $sql = "DELETE  FROM "._DB_PREFIX_."gformrequest
                    WHERE user_email = '".pSQL($customer['email'])."'";
            $res &= Db::getInstance()->execute($sql);
            if ($res) {
                return Tools::jsonEncode(true);
            }
            return Tools::jsonEncode($this->l('Unable to delete customer datas using email.'));
        }
   }
   public function hookActionExportGDPRData ($customer)
   {

       if (!Tools::isEmpty($customer['email']) && Validate::isEmail($customer['email'])) {
           $sql = "SELECT gfr.request,gfr.date_add
                  FROM "._DB_PREFIX_."gformrequest gfr
                  LEFT JOIN  "._DB_PREFIX_."gformrequest_shop gfrs ON (gfr.id_gformrequest = gfrs.id_gformrequest)
                  WHERE gfr.user_email = '".pSQL($customer['email'])."' AND gfrs.id_shop = ".(int)$customer['id_shop'];
           $requests = Db::getInstance()->ExecuteS($sql);
           if($requests)
                return Tools::jsonEncode($requests);
           else
                return Tools::jsonEncode($this->l('No datas.'));
       }
   }
   public function importGform($zipfile = ''){
        $result       = array();
        $id_extraform = 0;
        $filepath = _PS_MODULE_DIR_ . 'gformbuilderpro/exports/';
        if($zipfile =='' || !file_exists($filepath.$zipfile)){
            $result = array('error'=>1,'warrning'=>$this->l('Zip file does not exist'));
        }else{
            /*remove old file before archive */
            if(file_exists($filepath.'/extracts/gformbuilderpro.xml'))
                @unlink($filepath.'/extracts/gformbuilderpro.xml');
            /* unzip file */
            $zip = new ZipArchive();
            if($zip->open($filepath.$zipfile))
            {
                /* check file data */
                if ($zip->locateName('gformbuilderpro.xml') === false)
                {
                    $result = array('error'=>1,'warrning'=> $this->l('Zip file is invalid'));
                }else{
                    /* Extract */
                    if(!Tools::ZipExtract($filepath.$zipfile, $filepath.'/extracts/')){
                        $result = array('error'=>1,'warrning'=> $this->l('Cannot extract zip file'));
                    }else{
                        if(Tools::getValue('delete_old_form')){
                            $allform = gformbuilderproModel::getAllBlock();
                            if($allform){
                                foreach($allform as $form){
                                    $formObj = new gformbuilderproModel((int)$form['id_gformbuilderpro']);
                                    if(Validate::isLoadedObject($formObj))
                                        $formObj->delete();
                                }
                            }
                        }
                        $langs = Language::getLanguages();
                        $langs_id = array();
                        if($langs)
                            foreach($langs as $lang){
                                $langs_id[$lang['iso_code']] = (int)$lang['id_lang'];
                            }
                        $definition_field = ObjectModel::getDefinition('gformbuilderprofieldsModel');
                        $definition_form = ObjectModel::getDefinition('gformbuilderproModel');
                        /* read xml file */
                        $datas = @simplexml_load_file($filepath.'/extracts/gformbuilderpro.xml','SimpleXMLElement', LIBXML_NOCDATA);
                        $module_version = (string)$datas->attributes();
                        $needchange_old_val = false;
                        if($module_version == '') $needchange_old_val = true;
                        if($datas->gform)
                            foreach($datas->gform as $gform){
                                $formtemplate = (string)$gform->formtemplate;
                                preg_match_all('/\[(gformbuilderpro:)(.*?)\]/', $formtemplate, $matches);
                                $customShortCodes = array();$fields = array();
                                if(isset($matches[0]) && $matches[0]){
                                    foreach($matches[0] as $key=>$content)
                                    {
                                        $matchNoBrackets = str_replace(array('[',']'),'',$content);
                                        $shortCodeExploded = explode(':', $matchNoBrackets);
                                        $customShortCodes['gformbuilderpro'][$key] = $shortCodeExploded[1];
                                    }
                                    if(count($customShortCodes) > 0)
                                        foreach($customShortCodes as $shortCodeKey=>$shortCode)
                                        {
                                            if($shortCodeKey == 'gformbuilderpro')
                                            {
                                                foreach($shortCode as $show)
                                                {
                                                    $gformcmsfieldsObj = new gformbuilderprofieldsModel();
                                                    /* get data field from xml */
                                                    $default_datas = array();
                                                    $xmlfield = $gform->datafields->xpath('field[@id="'.(int)$show.'"]')[0];
                                                    if($xmlfield){
                                                        $type_field = (string)$xmlfield->type;
                                                        foreach($xmlfield as $field_key=> $xmlfielddata){
                                                            if(isset($definition_field['fields']) && isset($definition_field['fields'][$field_key])){
                                                                $lang = '';
                                                                if(isset($xmlfielddata['lang'])){
                                                                    $lang = (string)$xmlfielddata['lang'];
                                                                    if($lang !='' && isset($langs_id[$lang])){
                                                                        if($needchange_old_val && in_array($type_field,array("checkbox","radio","select","survey"))){
                                                                            $gformcmsfieldsObj->{$field_key}[(int)$langs_id[$lang]] = str_replace(",","\n",(string)$xmlfielddata);
                                                                        }else
                                                                            $gformcmsfieldsObj->{$field_key}[(int)$langs_id[$lang]] = (string)$xmlfielddata;
                                                                    }
                                                                    if(!isset($default_datas[$field_key]) || $default_datas[$field_key] == '')
                                                                        if($needchange_old_val && in_array($type_field,array("checkbox","radio","select","survey"))){
                                                                            $default_datas[$field_key] = str_replace(",","\n",(string)$xmlfielddata);
                                                                        }else
                                                                            $default_datas[$field_key] = (string)$xmlfielddata;
                                                                }else{
                                                                    if($needchange_old_val && in_array($type_field,array("checkbox","radio","select","survey"))){
                                                                        $gformcmsfieldsObj->{$field_key} = str_replace(",","\n",(string)$xmlfielddata);
                                                                    }else
                                                                        $gformcmsfieldsObj->{$field_key} = (string)$xmlfielddata;
                                                                }
                                                            }
                                                        }
                                                    }
                                                    /* Fix empty lang field */
                                                    $gformcmsfieldsObj = $this->fixEmptyLangField($gformcmsfieldsObj,$definition_field['fields'],$default_datas);
                                                    $gformcmsfieldsObj->save();
                                                    $originalShortCode = "[gformbuilderpro:$show]";
                                                    $testingReplacementText = "[gformbuilderpro:$gformcmsfieldsObj->id]";
                                                    $fields[] = (int)$gformcmsfieldsObj->id;
                                                    $formtemplate = str_replace($originalShortCode,$testingReplacementText,$formtemplate);
                                                    $formtemplate = str_replace('"gformbuilderpro_'.(int)$show.'"','"gformbuilderpro_'.(int)$gformcmsfieldsObj->id.'"',$formtemplate);
                                                }
                                            }
                                        }
                                }
                                $formnewObj = new gformbuilderproModel();
                                if(Tools::getValue('override_old_form')){
                                    $_formnewObj = new gformbuilderproModel((int)$gform['id']);
                                    if(Validate::isLoadedObject($_formnewObj)){
                                        $formnewObj = $this->emptyObjData($_formnewObj,$definition_form);
                                    }
                                }
                                $default_datas = array();
                                foreach($gform as $field_key=>$gform_data)
                                {
                                    if( $field_key !='datafields')
                                    {
                                        if(isset($definition_form['fields']) && isset($definition_form['fields'][$field_key]))
                                        {
                                            $lang = '';
                                            if(isset($gform_data['lang'])){
                                                $lang = (string)$gform_data['lang'];
                                                if($lang !='' && isset($langs_id[$lang])){
                                                    $formnewObj->{$field_key}[(int)$langs_id[$lang]] = (string)$gform_data;
                                                }
                                                if(!isset($default_datas[$field_key]) || $default_datas[$field_key] == '') $default_datas[$field_key] = (string)$gform_data;
                                            }else{
                                                $formnewObj->{$field_key} = (string)$gform_data;
                                            }
                                        }
                                    }
                                }
                                $formnewObj->formtemplate = $formtemplate;
                                $formnewObj->fields = implode(',',$fields);
                                $formnewObj = $this->fixEmptyLangField($formnewObj,$definition_form['fields'],$default_datas);
                                $formnewObj->save();
                                $id_extraform = (int)$formnewObj->id;
                                $allshop = Shop::getShops(true,null,true);
                                foreach (Language::getLanguages() as $lang){
                                    if($allshop){
                                        $this->parseEmailAndTpl((int)$formnewObj->id,$lang,$allshop);
                                        foreach($allshop as $id_shop){
                                            $this->setFormUrl($formnewObj->id,$formnewObj->rewrite[$lang["id_lang"]],$lang["id_lang"],$id_shop);
                                        }
                                    }else{
                                        $id_shop = $this->context->shop->id;
                                        $this->parseEmailAndTpl((int)$formnewObj->id,$lang);
                                        $this->setFormUrl($formnewObj->id,$formnewObj->rewrite[$lang["id_lang"]],$lang["id_lang"],$id_shop);
                                    }
                                }
                            }
                        $result = array('error'=>0,'warrning'=> '', 'id_extraform' => (int)$id_extraform);
                    }
                    if(file_exists($filepath.'/extracts/gformbuilderpro.xml'))
                        @unlink($filepath.'/extracts/gformbuilderpro.xml');
                }
                $zip->close();
            }else $result = array('error'=>1,'warrning'=> $this->l('Can not open zip file.'));
            @unlink($filepath.$zipfile);
        }
        return $result;
   }
   /* Fix empty lang field */
   public function fixEmptyLangField($obj,$fields,$default_datas){
        $langs = Language::getLanguages();
        if(isset($fields))
            foreach($fields as $field_key=>$field){
                if(isset($field['lang']) && $field['lang'] == 1){
                    foreach($langs as $lang){
                        if((!isset($obj->{$field_key}[(int)$lang['id_lang']]) || $obj->{$field_key}[(int)$lang['id_lang']] == '') && isset($default_datas[$field_key]))
                        {
                            $obj->{$field_key}[(int)$lang['id_lang']] = $default_datas[$field_key];
                        }
                    }
                }
            }
        return $obj;
   }
   public function emptyObjData($obj,$fields){
        $langs = Language::getLanguages();
        if(isset($fields['fields']))
            foreach($fields['fields'] as $field_key=>$field){
                if($field_key !='id' && $field_key != $fields['primary'])
                    if(isset($field['lang']) && $field['lang'] == 1){
                        foreach($langs as $lang){
                            $obj->{$field_key}[(int)$lang['id_lang']] = '';
                        }
                    }else $obj->{$field_key} = '';
            }
        return $obj;
   }
   public function downloadExportForm($xmldatas = array()){
        /* Download zip file */
        $filename = 'gformbuilderpro_'.date('d-m-Y H_i_s');
        $filepath = _PS_MODULE_DIR_ . 'gformbuilderpro/exports/';
        $zip = new ZipArchive();
        if ($zip->open($filepath.$filename.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE){
            if($xmldatas)
                foreach($xmldatas as $xmldata)
                    $zip->addFromString($xmldata['filename'], $xmldata['data']);
            $zip->close();
            if (ob_get_length() > 0) ob_end_clean();
            ob_start();
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"".$filename.'.zip'."\"");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: ".filesize($filepath.$filename.'.zip'));
            ob_end_flush();
            @readfile($filepath.$filename.'.zip');
            @unlink($filepath.$filename.'.zip');
        }else{
            die($this->l('Failed to create archive'));
        }
        die();
   }
   public function exportDataToXml($id_forms = '',$type = 'form'){
        $isps17 = version_compare(_PS_VERSION_, '1.7', '>=');
        Context::getContext()->smarty->assign(array('isps17' => $isps17));
        $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/admin/extrahtml.tpl';
        $gforms = array();
        $gformIDs = array_map('intval', explode(',', $id_forms));
        $definition = array();
        if($type == 'field')
            $definition = ObjectModel::getDefinition('gformbuilderprofieldsModel');
        else
            $definition = ObjectModel::getDefinition('gformbuilderproModel');
        $fields = $definition['fields'];
        $langs = Language::getLanguages();
        $langs_iso = array();
        if($langs)
            foreach($langs as $lang){
                $langs_iso[(int)$lang['id_lang']] = $lang['iso_code'];
            }
        if($gformIDs){
            foreach($gformIDs as $gformID){
                if($type == 'field'){
                    $formObj = new gformbuilderprofieldsModel($gformID);
                    if(Validate::isLoadedObject($formObj)){
                        $gforms[(int)$formObj->id] = (array)$formObj;
                    }
                }else{
                    $formObj = new gformbuilderproModel($gformID);
                    if(Validate::isLoadedObject($formObj)){
                        $gforms[(int)$formObj->id] = (array)$formObj;
                        $xml_field = $this->exportDataToXml($formObj->fields,'field');
                        $gforms[(int)$formObj->id]['datafields'] = $xml_field;
                    }
                }
            }
        }
        Context::getContext()->smarty->assign(
            array(
                'action'=>'exportFormsToXml',
                'gforms'=>$gforms,
                'fields'=>$fields,
                'type'=>$type,
                'langs_iso'=>$langs_iso,
                'module_version'=>$this->version
            )
        );
        return trim(Context::getContext()->smarty->fetch($tpl));
   }
   public function parseEmailAndTpl($id_form,$lang,$id_shops=null){
        $formObj = new gformbuilderproModel((int)$id_form);
        if($formObj->id_gformbuilderpro == (int)$id_form){
            if($id_shops && is_array($id_shops)){
                foreach($id_shops as $id_shop){
                    $formObj->parseTpl($lang["id_lang"],$id_shop);
                    $formObj->parseEmail($lang,'form_'.$id_form.'_'.$id_shop);
                    if($formObj->sendtosender) $formObj->parseEmail($lang,'sender_'.$id_form.'_'.$id_shop,true);
                }
            }else{
                $id_shop = $id_shops;
                if($id_shop == null)
                    $id_shop = $this->context->shop->id;
                $formObj->parseTpl($lang["id_lang"],$id_shop);
                $formObj->parseEmail($lang,'form_'.$id_form.'_'.$id_shop);
                if($formObj->sendtosender) $formObj->parseEmail($lang,'sender_'.$id_form.'_'.$id_shop,true);
            }
        }
    }
    public function hookActionAdminControllerSetMedia($params)
    {
        $this->context->controller->addCSS(_MODULE_DIR_.$this->name.'/views/css/admin/tab_style.css');
        
        if(version_compare(_PS_VERSION_,'1.6') != -1){
            Media::addJsDef(array(
                'gformbuilderpro_module_url' => $this->context->link->getAdminLink('AdminGformrequest', true),
                'ad'=>'',
                'iso'=>$this->context->language->iso_code,
                'psversion15'=>version_compare(_PS_VERSION_,'1.6'),
                'gdefault_language'=>(int)Context::getContext()->language->id,
                'gtitleform'=>$this->l('Form Title'),
                'copyToClipboard_success'=>$this->l('Copy to clipboard successfully'),
            ));
        }else{
            $this->context->controller->addCSS(_MODULE_DIR_.$this->name.'/views/css/front/bootstrap_grid.css');
        }
        $this->context->controller->addJS(_MODULE_DIR_.$this->name.'/views/js/admin/unreadreceived.js');
        $controller = Tools::strtolower(Tools::getValue('controller'));
        if($controller == 'admingformconfig'){
            if(version_compare(_PS_VERSION_,'1.6') == -1)
                $this->context->controller->addJqueryUI('ui.mouse');
            $this->context->controller->addJqueryPlugin('tagify');
        }elseif($controller == 'admingformanalytics' || $controller == 'admingformdashboard'){
            $this->context->controller->addCSS(_MODULE_DIR_.$this->name.'/views/css/front/nv.d3.css');
            $this->context->controller->addJS(_MODULE_DIR_.$this->name.'/views/js/admin/d3.v3.min.js');
            $this->context->controller->addJS(_MODULE_DIR_.$this->name.'/views/js/admin/nv.d3.min.js');

        }elseif($controller == 'admingformmanager'){
            $this->context->controller->addJqueryUI('ui.sortable');
            $this->context->controller->addJqueryUI('ui.draggable');
            $this->context->controller->addJqueryUI('ui.droppable');
            $this->context->controller->addJqueryPlugin('tagify');
            //fix version ps < 1.6.0.7 mising jquery plugin select2
            if(version_compare(_PS_VERSION_,'1.6.0.7') == -1){
                $this->context->controller->addJqueryPlugin('autocomplete');
            }else
                $this->context->controller->addJqueryPlugin('select2');
            $this->context->controller->addJqueryPlugin('colorpicker');
            $this->context->controller->addJqueryPlugin('fancybox');
            $this->context->controller->addJS(_MODULE_DIR_.$this->name.'/views/js/admin/validate.js');
        }elseif($controller == 'admingformrequest'){
            $this->context->controller->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
            $this->context->controller->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
        }
        if($controller == 'admingformconfig' ||
            $controller == 'admingformanalytics' ||
            $controller == 'admingformmanager' ||
            $controller == 'admingformdashboard' ||
            $controller == 'admingformrequestexport' ||
            $controller == 'admingformrequest'
        ){
            if (version_compare(_PS_VERSION_, '1.7.8.0 ', '>='))
                $this->context->controller->addCSS(_MODULE_DIR_.$this->name.'/views/css/admin/fixStyle.css');
            $this->context->controller->addCSS(_MODULE_DIR_.$this->name.'/views/css/admin/gformbuilderpro.css');
            $this->context->controller->addJS(_MODULE_DIR_.$this->name.'/views/js/admin/gformbuilderpro.js');
        }

    }
    public function htmlEntityDecode($str){
        if(is_array($str)){
            foreach($str as &$_str)
                $_str = $this->htmlEntityDecode($_str);
        }else $str = html_entity_decode($str);
        return $str;
    }
    public function getAllView($id_gformbuilderpro,$date_from,$date_to,$granularity = false,$with_format = false){
        $sql = '';
        if ($granularity == 'day') {
            $sql = 'SELECT LEFT(`date_add`, 10) AS date, COUNT(id_gform_analytics) as total
                    FROM `' . _DB_PREFIX_ . 'gform_analytics`
                    WHERE date_add BETWEEN "' . pSQL($date_from) . ' 00:00:00" AND "' . pSQL($date_to) . ' 23:59:59"
                    '. ($id_gformbuilderpro > 0 ? ' AND id_gformbuilderpro='.(int)$id_gformbuilderpro : '').'
                    GROUP BY LEFT(`date_add`, 10) ';
        } elseif ($granularity == 'month') {
            $sql = 'SELECT LEFT(`date_add`, 7) AS date, COUNT(id_gform_analytics)   as total
                    FROM `' . _DB_PREFIX_ . 'gform_analytics`
                    WHERE date_add BETWEEN "' . pSQL($date_from) . ' 00:00:00" AND "' . pSQL($date_to) . ' 23:59:59"
                    '. ($id_gformbuilderpro > 0 ? ' AND id_gformbuilderpro='.(int)$id_gformbuilderpro : '').'
                    GROUP BY LEFT(`date_add`, 7)';
        } else {
            $sql = 'SELECT LEFT(`date_add`, 10) AS date, COUNT(id_gform_analytics)  as total
                    FROM `' . _DB_PREFIX_ . 'gform_analytics`
                    '. ($id_gformbuilderpro > 0 ? ' WHERE id_gformbuilderpro='.(int)$id_gformbuilderpro : '').'
                    GROUP BY LEFT(`date_add`, 10)';
        }
        $datas =  Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if(!$with_format){
            return $datas;
        }else {
            if ($granularity != 'month') $granularity = 'day';
            return $this->formatChartData($datas, $date_from, $date_to, $granularity);
        }
    }
    public function getAllSubmit($id_gformbuilderpro,$date_from,$date_to,$granularity = false,$with_format = false){
        $sql = '';
        if ($granularity == 'day') {
            $sql = 'SELECT LEFT(`date_add`, 10) AS date, COUNT(id_gformrequest) as total
                    FROM `' . _DB_PREFIX_ . 'gformrequest`
                    WHERE date_add BETWEEN "' . pSQL($date_from) . ' 00:00:00" AND "' . pSQL($date_to) . ' 23:59:59"
                    '. ($id_gformbuilderpro > 0 ? ' AND id_gformbuilderpro='.(int)$id_gformbuilderpro : '').'
                    GROUP BY LEFT(`date_add`, 10) ';
        } elseif ($granularity == 'month') {
            $sql = 'SELECT LEFT(`date_add`, 7) AS date, COUNT(id_gformrequest)   as total
                    FROM `' . _DB_PREFIX_ . 'gformrequest`
                    WHERE date_add BETWEEN "' . pSQL($date_from) . ' 00:00:00" AND "' . pSQL($date_to) . ' 23:59:59"
                    '. ($id_gformbuilderpro > 0 ? ' AND id_gformbuilderpro='.(int)$id_gformbuilderpro : '').'
                    GROUP BY LEFT(`date_add`, 7)';
        } else {
            $sql = 'SELECT LEFT(`date_add`, 10) AS date, COUNT(id_gformrequest)  as total
                    FROM `' . _DB_PREFIX_ . 'gformrequest`
                    '. ($id_gformbuilderpro > 0 ? ' WHERE id_gformbuilderpro='.(int)$id_gformbuilderpro : '').'
                    GROUP BY LEFT(`date_add`, 10)';
        }
        $datas = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if(!$with_format){
            return $datas;
        }else {
            if ($granularity != 'month') $granularity = 'day';
            return $this->formatChartData($datas, $date_from, $date_to, $granularity);
        }
    }
    public function getAllReply($id_gformbuilderpro,$date_from,$date_to,$granularity = false,$with_format = false){
        $sql = '';
        if ($granularity == 'day') {
            $sql = 'SELECT LEFT(grr.`date_add`, 10) AS date, COUNT(id_gformrequest_reply) as total
                    FROM `' . _DB_PREFIX_ . 'gformrequest_reply`  grr
                    '. ($id_gformbuilderpro > 0 ? ' LEFT JOIN `' . _DB_PREFIX_ . 'gformrequest` gr ON (grr.id_gformrequest = gr.id_gformrequest) ' : '').'
                    WHERE grr.date_add BETWEEN "' . pSQL($date_from) . ' 00:00:00" AND "' . pSQL($date_to) . ' 23:59:59"
                    '. ($id_gformbuilderpro > 0 ? ' AND gr.id_gformbuilderpro='.(int)$id_gformbuilderpro : '').'
                    GROUP BY LEFT(`date_add`, 10) ';
        } elseif ($granularity == 'month') {
            $sql = 'SELECT LEFT(grr.`date_add`, 7) AS date, COUNT(id_gformrequest_reply)   as total
                    FROM `' . _DB_PREFIX_ . 'gformrequest_reply` grr
                    '. ($id_gformbuilderpro > 0 ? ' LEFT JOIN `' . _DB_PREFIX_ . 'gformrequest` gr ON (grr.id_gformrequest = gr.id_gformrequest) ' : '').'
                    WHERE grr.date_add BETWEEN "' . pSQL($date_from) . ' 00:00:00" AND "' . pSQL($date_to) . ' 23:59:59"
                    '. ($id_gformbuilderpro > 0 ? ' AND gr.id_gformbuilderpro='.(int)$id_gformbuilderpro : '').'
                    GROUP BY LEFT(`date_add`, 7)';
        } else {
            $sql = 'SELECT LEFT(grr.`date_add`, 10) AS date, COUNT(id_gformrequest_reply)  as total
                    FROM `' . _DB_PREFIX_ . 'gformrequest_reply` grr
                    '. ($id_gformbuilderpro > 0 ? ' LEFT JOIN `' . _DB_PREFIX_ . 'gformrequest` gr ON (grr.id_gformrequest = gr.id_gformrequest) ' : '').'
                    '. ($id_gformbuilderpro > 0 ? ' WHERE gr.id_gformbuilderpro='.(int)$id_gformbuilderpro : '').'
                    GROUP BY LEFT(`date_add`, 10)';
        }
        $datas = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if(!$with_format){
            return $datas;
        }else {
            if ($granularity != 'month') $granularity = 'day';
            return $this->formatChartData($datas, $date_from, $date_to, $granularity);
        }
    }
    public function formatChartData($datas,$date_from,$date_to,$type = 'day'){
        $_view_values = array();
        foreach($datas as $view){
            $view_time = strtotime($view['date']);
            $_view_values[$view_time] = array(
                'key'=>$view_time,
                'y'=>(int)$view['total']);
        }
        $viewsdatas = array();
        if($type == 'month'){
            for ($date = strtotime($date_from); $date <= strtotime($date_to); $date = strtotime('+1 month', $date)) {
                if (isset($_view_values[$date]))
                    $viewsdatas[] = $_view_values[$date];
                else
                    $viewsdatas[] = array(
                        'key' => $date,
                        'y' => 0);
            }
        }else {
            for ($date = strtotime($date_from); $date <= strtotime($date_to); $date = strtotime('+1 day', $date)) {
                if (isset($_view_values[$date]))
                    $viewsdatas[] = $_view_values[$date];
                else
                    $viewsdatas[] = array(
                        'key' => $date,
                        'y' => 0);
            }
        }
        return $viewsdatas;
    }
    public function hookActionMailAlterMessageBeforeSend($params) {
        $id_shop = (int)Context::getContext()->shop->id;
        $id_shop_group = (int)Shop::getGroupFromShop((int)$id_shop);
        $remove_shopname = (int)Configuration::get('GF_REMOVER_SHOPNAME_IN_SUBJECT', null, $id_shop_group, $id_shop);
        if($remove_shopname){
            $message = $params['message'];
            $subject = $message->getSubject();
            /* remove [shop_name] */
            $prefix = '[' . Configuration::get('PS_SHOP_NAME', null, null, $id_shop) . ']';
            $new_subject = str_replace($prefix, '', $subject);
            $message->setSubject(trim($new_subject));
        }
    }
    public function hookDisplayGorderreference($params){ /** from vs 1.3.2 */
        $html = '';
        if(isset($params['id']) && $params['id'] > 0 && isset($this->context->customer) && $this->context->customer->isLogged()){
            $fieldObj = new gformbuilderprofieldsModel((int)$params['id'],(int)$this->context->language->id, (int)$this->context->shop->id);
            if(Validate::isLoadedObject($fieldObj))
            {
                $customer_orders = Order::getCustomerOrders($this->context->customer->id);
                if($customer_orders){
                    $orders = array();
                    foreach($customer_orders as $customer_order){
                        $orders[(int)$customer_order['id_order']] = $customer_order['reference'];

                    }
                    $fields_value = array();
                    $fields_value['id_gformbuilderprofields'] = (int)$fieldObj->id;
                    $fields_value['name'] = $fieldObj->name;
                    $fields_value['labelpos'] = $fieldObj->labelpos;
                    $fields_value['idatt'] = $fieldObj->idatt;
                    $fields_value['classatt'] = $fieldObj->classatt;
                    $fields_value['required'] = (bool)$fieldObj->required;
                    $fields_value['label'] = $fieldObj->label;
                    $fields_value['required'] = (bool)$fieldObj->required;
                    $fields_value['multi'] = (bool)$fieldObj->multi;
                    $fields_value['description'] = $fieldObj->description;
                    $fields_value['placeholder'] = $fieldObj->placeholder;
                    $fields_value['value'] = $orders;
                    $this->context->smarty->assign($fields_value);
                    return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/front/fields/hook_orderreference.tpl');
                }
            }

        }
        return $html;
    }


    /** from 03/06/2020 */
    public function getMailChimpList()
    {
        $lists = array();
        $id_shop_group = Shop::getContextShopGroupID();
        $id_shop = Shop::getContextShopID();
        $apiKey = Configuration::get('GF_MAILCHIMP_API_KEY', null, $id_shop_group, $id_shop);
        if($apiKey !=''){
            $dataCenter = Tools::substr($apiKey,Tools::strpos($apiKey,'-')+1);
            $url = 'https:/'.'/' . $dataCenter . '.api.mailchimp.com/3.0/lists';
            $response = $this->rudr_mailchimp_curl_connect($url,'GET',$apiKey);
            if($response){
                $mailchimp_lists = Tools::jsonDecode($response,true);
                if(isset($mailchimp_lists['lists']) && $mailchimp_lists['lists']){
                    foreach($mailchimp_lists['lists'] as $_list){
                        $lists[]  = array(
                            'id'=>$_list['id'],
                            'name'=>$_list['name']
                        );
                    }
                }
            }
        }
        return $lists;
    }
    public function rudr_mailchimp_curl_connect( $url, $request_type, $api_key, $data= array()) {
        if (function_exists('curl_init')) {
            if( $request_type == 'GET' )
                $url .= '?' . http_build_query($data);
            $mch = curl_init();

            $headers = array(
                'Content-Type: application/json',
                'Authorization: Basic '.base64_encode( 'user:'. $api_key ) /** MailChimp API: Need base64_encode to encode user api key */
            );

            curl_setopt($mch, CURLOPT_URL, $url );
            curl_setopt($mch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($mch, CURLOPT_RETURNTRANSFER, true); // do not echo the result, write it into variable
            curl_setopt($mch, CURLOPT_CUSTOMREQUEST, $request_type); // according to MailChimp API: POST/GET/PATCH/PUT/DELETE
            curl_setopt($mch, CURLOPT_TIMEOUT, 10);
            curl_setopt($mch, CURLOPT_SSL_VERIFYPEER, false); // certificate verification for TLS/SSL connection

            if( $request_type == 'POST' ) {
                curl_setopt($mch, CURLOPT_POST, true);
                curl_setopt($mch, CURLOPT_POSTFIELDS, Tools::jsonEncode($data) ); // send data in json
            }
            if( $request_type == 'PUT' ) {
                curl_setopt($mch, CURLOPT_CUSTOMREQUEST , 'PUT');
                curl_setopt($mch, CURLOPT_POSTFIELDS, Tools::jsonEncode($data) ); // send data in json
            }
            return curl_exec($mch);
        }else return false;
    }
    public function rudr_klaviyo_curl_connect( $url, $request_type, $data= array()) {
        if (function_exists('curl_init')) {
            if( $request_type == 'GET' )
                $url .= '?' . http_build_query($data);
            $mch = curl_init();
            curl_setopt($mch, CURLOPT_URL, $url );
            curl_setopt($mch, CURLOPT_RETURNTRANSFER, true); // do not echo the result, write it into variable
            curl_setopt($mch, CURLOPT_CUSTOMREQUEST, $request_type); // according to klaviyo API: POST/GET/PATCH/PUT/DELETE
            curl_setopt($mch, CURLOPT_TIMEOUT, 10);
            curl_setopt($mch, CURLOPT_SSL_VERIFYPEER, false); // certificate verification for TLS/SSL connection
            if( $request_type == 'POST' ) {
                $postdata = Tools::jsonEncode($data);
                curl_setopt($mch, CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type:application/json',
                        'Content-Length: ' . Tools::strlen($postdata)
                    )
                );

                curl_setopt($mch, CURLOPT_POST, true);
                curl_setopt($mch, CURLOPT_POSTFIELDS,  $postdata); // send data in json
            }
            if( $request_type == 'PUT' ) {
                curl_setopt($mch, CURLOPT_CUSTOMREQUEST , 'PUT');
                curl_setopt($mch, CURLOPT_POSTFIELDS, Tools::jsonEncode($data) ); // send data in json
            }
            return curl_exec($mch);
        }else return false;
    }
    public function rudr_zapier_curl_connect( $webhook_url, $data= array()) {
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $webhook_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $data
            ));
            $response = curl_exec($curl);
            if($response){

            }
            curl_close($curl);
        }
        return false;
    }
    public function getKlaviyoList(){
        $lists = array();
        $id_shop_group = Shop::getContextShopGroupID();
        $id_shop = Shop::getContextShopID();
        $apiKey = Configuration::get('GF_KLAVIYO_API_KEY', null, $id_shop_group, $id_shop);
        if($apiKey !=''){
            $url = 'https://a.klaviyo.com/api/v2/lists';
            $data = array('api_key'=>$apiKey);
            $response =  $this->rudr_klaviyo_curl_connect($url,'GET',$data);
            if($response){
                $klaviyo_lists = Tools::jsonDecode($response,true);
                if(isset($klaviyo_lists['message'])){
                    return array(
                        'error'=>1,
                        'message'=>$klaviyo_lists['message']
                    );
                }else
                    if($klaviyo_lists){
                        foreach($klaviyo_lists as $_list){
                            $lists[]  = array(
                                'id'=>$_list['list_id'],
                                'name'=>$_list['list_name']
                            );
                        }
                    }
            }
        }
        return $lists;
    }
    public function createDiscount($id_product, $ipa, $discount, $context) {
        if ($discount && $this->checkProductDiscount($this->context->cart->id,$id_product) == 0 && $this->checkProductDiscount($this->context->cart->id,$ipa, 'attributes') == 0) {
            $productObj = new Product((int)$id_product);$context;
            if ((int)$discount['value'] < 0) return true;
            $validtimes = 1;
            $coupon = new CartRule();
            $coupon->quantity = 1;
            $coupon->quantity_per_user = 1;
            $coupon->id_discount_type = 2;
            $coupon->product_restriction = 1;
            $coupon->reduction_product = (int)$id_product;
            $coupon->minimum_amount_tax = $discount['tax'];
            $coupon->minimum_amount_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
            if(!$discount['type']){
                $reduction_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
                if($reduction_currency <=0) $reduction_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
                if(isset($coupon->value)) $coupon->value =  $discount['value'];
                $coupon->reduction_percent = 0;
                $coupon->reduction_amount =  $discount['value'];
                $coupon->reduction_currency =  (int)Configuration::get('PS_CURRENCY_DEFAULT');
                $coupon->reduction_tax = (int) $discount['tax'];
            }else{
                if(isset($coupon->value))
                    $coupon->value =  $discount['value'];
                $coupon->reduction_percent =  $discount['value'];
                $coupon->reduction_amount = 0;
                $coupon->reduction_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
            }
            $coupon->free_gift = 0;
            $coupon->apply_discount_to = 'specific';
            $start_date = date('Y-m-d H:i:s');
            $coupon->date_from = $start_date;
            $end_date = date('Y-m-d', strtotime('+'.(int)$validtimes.' day', strtotime($start_date)));
            $coupon->date_to = $end_date;
            $gen_pass = Tools::strtoupper(Tools::passwdGen(8));
            $vouchercode = 'wholesale';
            $name_v = $vouchercode.'-'.$gen_pass;
            $coupon->code = $name_v;
            $coupon->active = 1;
            $coupon->description = '';
            $coupon->highlight = 0;
            foreach (Language::getLanguages() as $lang){
                $discountname = '';
                if($discountname == '') $discountname = $this->l('Wholesale');
                    $coupon->name[$lang['id_lang']] = $discountname.' '.$this->l('for').' '.$productObj->name[(int)$lang['id_lang']];
            }
            $coupon->add();
            $cartRuleId = $coupon->id ;
            $this->context->cart->addCartRule( (int) $cartRuleId);
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`)
            VALUES ('.(int)$cartRuleId.', "1")');
            $id_product_rule_group = Db::getInstance()->Insert_ID();
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule` (`id_product_rule_group`, `type`)
            VALUES ('.(int)$id_product_rule_group.', "products")');
            $id_product_rule = Db::getInstance()->Insert_ID();
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_value` (`id_product_rule`, `id_item`) VALUES ('.(int)$id_product_rule.','.(int)$id_product.')');
            if ((int)$ipa >= 0) {
                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule` (`id_product_rule_group`, `type`)
                VALUES ('.(int)$id_product_rule_group.', "attributes")');
                $id_product_rule = Db::getInstance()->Insert_ID();
                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_value` (`id_product_rule`, `id_item`) VALUES ('.(int)$id_product_rule.','.(int)$ipa.')');
            }
        }
        return true;
    }
    public function checkProductDiscount($id_cart , $id_product = 0, $type='products') {
        if ($type =='attributes' && $id_product == 0) {
            return 0;
        }
        $sql = 'SELECT count(id_item)
                FROM '._DB_PREFIX_.'cart_rule_product_rule_value
                WHERE id_product_rule IN(
                    SELECT id_product_rule
                    FROM '._DB_PREFIX_.'cart_rule_product_rule
                    WHERE id_product_rule_group IN(
                        SELECT DISTINCT id_product_rule_group
                        FROM '._DB_PREFIX_.'cart_rule_product_rule_group
                        WHERE id_cart_rule IN (
                            SELECT DISTINCT id_cart_rule
                                FROM '._DB_PREFIX_.'cart_cart_rule
                                WHERE id_cart = '.(int)$id_cart.'
                        )
                    )
                    AND type = "'.pSQL($type).'"
                )
                AND id_item = '.(int)$id_product.'
                ';
        return (int)Db::getInstance()->getValue($sql);
    }
}
