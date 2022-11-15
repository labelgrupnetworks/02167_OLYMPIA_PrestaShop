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
include_once(_PS_MODULE_DIR_ . 'gformbuilderpro/classes/gformbuilderprofieldsModel.php');
include_once(_PS_MODULE_DIR_ . 'gformbuilderpro/classes/gformrequestModel.php');

class AdminGformmanagerController extends ModuleAdminController
{
    public function __construct()
    {
        $this->className = 'gformbuilderproModel';
        $this->table = 'gformbuilderpro';
        parent::__construct();
        $this->meta_title = $this->l('Form builder pro');
        $this->deleted = false;
        $this->explicitSelect = true;
        $this->context = Context::getContext();
        $this->lang = true;
        $this->bootstrap = true;
        $this->_defaultOrderBy = 'id_gformbuilderpro';
        $this->_select = ' a.id_gformbuilderpro as shortcode, a.id_gformbuilderpro as smartyhook, a.id_gformbuilderpro as frontlink,b.rewrite ';
        $this->filter = true;
        if (Shop::isFeatureActive()) {
            Shop::addTableAssociation($this->table, array('type' => 'shop'));
        }
        $this->bulk_actions = array(
			'delete' => array(
				'text' => $this->l('Delete selected'),
				'confirm' => $this->l('Delete selected items?'),
				'icon' => 'icon-trash'
			)
		);
        $this->position_identifier = 'id_gformbuilderpro';
        $this->addRowAction('edit');
        $this->addRowAction('duplicate');
        $this->addRowAction('delete');
        
        
        $this->fields_list = array(
            'id_gformbuilderpro' => array(
                'title' => $this->l('ID'),
                'type' => 'int',
                'width' => 'auto',
                'orderby' => false,
                'class' => 'fixed-width-xs'),
            'title' => array(
                'title' => $this->l('Title'),
                'width' => 'auto',
                'orderby' => false),
            'shortcode' => array(
                'title' => $this->l('Shortcode'),
                'width' => 'auto',
                'orderby' => false,
                'search'=>false,
                'callback' => 'printShortcode',
                'remove_onclick'=>true
                ),
            'smartyhook' => array(
                'title' => $this->l('Smarty hook'),
                'width' => 'auto',
                'orderby' => false,
                'search'=>false,
                'callback' => 'printSmartyhook',
                'remove_onclick'=>true
            ),
            'frontlink' => array(
                'title' => $this->l('Url'),
                'width' => 'auto',
                'orderby' => false,
                'callback' => 'printFrontlink',
                'search'=>false,
                'remove_onclick'=>true
                ),
            'requiredlogin' => array(
                'title' => $this->l('Required Login'),
                'width' => 'auto',
                'active' => 'requiredlogin',
                'type' => 'bool',
                'orderby' => false),
            'saveemail' => array(
                'title' => $this->l('Save to Database'),
                'width' => 'auto',
                'active' => 'saveemail',
                'type' => 'bool',
                'orderby' => false),
            
            'active' => array(
                'title' => $this->l('Status'),
                'width' => 'auto',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false),
            );
        $this->toolbar_btn['exportgform'] = array(
			'href' => 'submitExportgformr',
			'desc' => $this->l('Export forms'),
		);
        Context::getContext()->smarty->assign(array('psversion15'=>version_compare(_PS_VERSION_,'1.6')));
    }
    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('Form builder pro');
        $this->toolbar_title[] = $this->l('Forms');
    }
    public function printShortcode($value, $form){
        if($value !=''){
            $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/admin/extrahtml.tpl';
            Context::getContext()->smarty->assign(
                array(
                    'action'=>'printShortcode',
                    'shortcode'=>'{gformbuilderpro:'.$form['id_gformbuilderpro'].'}'
                )
            );
            return Context::getContext()->smarty->fetch($tpl);
        }
    }
    public function printSmartyhook($value, $form){
        if($value !=''){
            $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/admin/extrahtml.tpl';
            Context::getContext()->smarty->assign(
                array(
                    'action'=>'printSmartyhook',
                    'shortcode'=>"{hook h='displayGform' id='".(int)$form['id_gformbuilderpro']."'}"
                )
            );
            return Context::getContext()->smarty->fetch($tpl);
        }
    }
    public function printFrontlink($value, $form){
        $url_rewrite = Context::getContext()->link->getModuleLink('gformbuilderpro','form',array('id'=>(int)$form['id_gformbuilderpro'],'rewrite'=>$form['rewrite']));
        if (!strpos($url_rewrite, 'index.php')){
            $url_rewrite = str_replace('?module=gformbuilderpro&controller=form','',$url_rewrite);
        }
        // fix friendly url ps 1.5
        if(Configuration::get('PS_REWRITING_SETTINGS') && version_compare(_PS_VERSION_,'1.6','<'))
        {
            $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            $shop = Context::getContext()->shop;
            $base = (($force_ssl) ? 'https://'.$shop->domain_ssl : 'http://'.$shop->domain);
            $url_rewrite =  $base.$shop->getBaseURI().'form/'.$form['rewrite'].'-g'.(int)$form['id_gformbuilderpro'].'.html';
        }
        //# fix friendly url ps 1.5
        if($value !=''){
            $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/admin/extrahtml.tpl';
            Context::getContext()->smarty->assign(
                array(
                    'action'=>'printFrontlink',
                    'url_rewrite'=>$url_rewrite
                )
            );
            return Context::getContext()->smarty->fetch($tpl);
        }
        
    }
    public function initPageHeaderToolbar()
    {
        $id_gformbuilderpro = (int)Tools::getValue('id_gformbuilderpro');
        if($id_gformbuilderpro > 0){
            $this->page_header_toolbar_btn = array(
    
                'cogs' => array(
    
                    'href' => $this->context->link->getAdminLink('AdminGformmanager').'&submitDuplicateGformbuilderpro=1&id_gformbuilderpro='.(int)$id_gformbuilderpro,
    
                    'desc' => $this->l('Duplicate', null, null, false),
    
                    'icon' => 'process-icon-duplicate'));
        }
        parent::initPageHeaderToolbar();
    }
    public function initProcess()
    {
        parent::initProcess();
        if (Tools::isSubmit('requiredlogin'.$this->table) && Tools::getValue($this->identifier)) {
            $this->action = 'requiredlogin';
        }elseif (Tools::isSubmit('saveemail'.$this->table) && Tools::getValue($this->identifier)) {
            $this->action = 'saveemail';
        }
    }
    public function processRequiredlogin(){
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            $object->requiredlogin = !$object->requiredlogin;
            $object->update(false);
        }
    }
    public function processSaveemail(){
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            $object->saveemail = !$object->saveemail;
            $object->update(false);
        }
    }
    public function replateOldData($htmls,$replates = array())
    {
        if(is_array($htmls)){
            if($htmls)
                foreach($htmls as &$html)
                    if($replates)
                        foreach($replates as $data){
                            $html = str_replace($data['old_name'],$data['new_name'],$html);
                        }
        }else{
            if($replates)
                foreach($replates as $data){
                    $htmls = str_replace($data['old_name'],$data['new_name'],$htmls);
                }
        }
        return $htmls;
    }
    public function postProcess()
	{
        if(Tools::isSubmit('getTypeField')){
            $result = array();
            $ids = Tools::getValue('ids');
            $fieldIDs = array_map('intval', explode('_', $ids));
            if($fieldIDs) {
                $sql = 'SELECT id_gformbuilderprofields,type 
                    FROM `' . _DB_PREFIX_ . 'gformbuilderprofields` 
                    WHERE id_gformbuilderprofields IN(' . pSql(implode(',',$fieldIDs)) . ')';
                $result =  Db::getInstance()->executeS($sql);
            }
            die(Tools::jsonEncode($result));
        }
	   elseif(Tools::isSubmit('submitGexport_form'))
       {
	       $gid_forms = Tools::getValue('gid_forms');
           if($gid_forms !=''){
                $xmldata = $this->module->exportDataToXml($gid_forms,'form');
                $xmldatas = array(
                    array('filename'=>'gformbuilderpro.xml','data'=>$xmldata) 
                ); 
                echo $this->module->downloadExportForm($xmldatas);
           }
           die();
	   }elseif (Tools::isSubmit('submitGimport_form')){
	       
            if (isset($_FILES['zipfile']['name']) && !empty($_FILES['zipfile']['name']) && !empty($_FILES['zipfile']['tmp_name']))
    		{
    		   $filepath = _PS_MODULE_DIR_ . 'gformbuilderpro/exports/';
    		   if(
                    Tools::strtolower(pathinfo($_FILES['zipfile']['name'], PATHINFO_EXTENSION)) == 'zip' &&
                    in_array($_FILES['zipfile']['type'],array('application/zip','application/x-zip','application/x-zip-compressed'))
                )
               {
                    $temp_name = tempnam($filepath,'gform');
                    @move_uploaded_file($_FILES['zipfile']['tmp_name'], $temp_name);
                    /* try to open a zip file to check if it's valid */
                    if (!Tools::ZipTest($temp_name)){
                        $this->errors[] = $this->l('Zip file is invalid or broken.');
                        @unlink($temp_name);
                    }else{
                        $salt = sha1(microtime());
                        @rename($temp_name,$filepath.$salt.'.zip');
                        $result = $this->module->importGform($salt.'.zip');
                        if(isset($result['error']) && $result['error'] != 0){
                            $this->errors[] = $result['warrning'];
                        }else{
                            Tools::redirectAdmin($this->context->link->getAdminLink('AdminGformmanager', true,array(),array('conf'=>4)));
                        }
                    }
               }else{
                    $this->errors[] = $this->l('An error occurred during the file-upload process.');
               }
            }else{
                $this->errors[] = $this->l('Please select a zip file');
            }
           
	   }elseif (Tools::isSubmit('submitDuplicateGformbuilderpro') || Tools::isSubmit('duplicategformbuilderpro')){
            $id_gformbuilderpro = (int)Tools::getValue('id_gformbuilderpro');
            $formObj = new gformbuilderproModel($id_gformbuilderpro);
            if(Validate::isLoadedObject($formObj)){
                $formtemplate = $formObj->formtemplate;
                $replates = array();
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
                                        $gformcmsfieldsObj = new gformbuilderprofieldsModel((int)$show);
                                        if(Validate::isLoadedObject($gformcmsfieldsObj)){
                                            $field_name = $gformcmsfieldsObj->type.'_'.rand(1,999999).'_'.time();
                                            $old_name = $gformcmsfieldsObj->name;
                                            $gformcmsfieldsObjNew = $gformcmsfieldsObj->duplicateObject();
                                            $gformcmsfieldsObjNew->name = $field_name;
                                            if($gformcmsfieldsObjNew->name == $gformcmsfieldsObjNew->classatt){
                                                $gformcmsfieldsObjNew->classatt = $field_name;
                                            }
                                            $gformcmsfieldsObjNew->idatt = $field_name;
                                            $gformcmsfieldsObjNew->save();
                                            $originalShortCode = "[gformbuilderpro:$show]";
                                            $testingReplacementText = "[gformbuilderpro:$gformcmsfieldsObjNew->id]";
                                            $fields[] = (int)$gformcmsfieldsObjNew->id;
                                            $formtemplate = str_replace($originalShortCode,$testingReplacementText,$formtemplate);
                                            $formtemplate = str_replace('"gformbuilderpro_'.(int)$show.'"','"gformbuilderpro_'.(int)$gformcmsfieldsObjNew->id.'"',$formtemplate);
                                            $replates[] = array(
                                                'old_name'=>$old_name,
                                                'new_name'=>$field_name
                                            );
                                        }
                                    }
                                }
                            }
                }
                $formnewObj = $formObj->duplicateObject();
                if($replates){
                    $formnewObj->formtemplate = $this->replateOldData($formtemplate,$replates);
                    $formnewObj->title = $this->replateOldData($formnewObj->title,$replates);
                    $formnewObj->sender = $this->replateOldData($formnewObj->sender,$replates);
                    $formnewObj->subject = $this->replateOldData($formnewObj->subject,$replates);
                    $formnewObj->subjectsender = $this->replateOldData($formnewObj->subjectsender,$replates);
                    $formnewObj->emailtemplate = $this->replateOldData($formnewObj->emailtemplate,$replates);
                    $formnewObj->emailtemplatesender = $this->replateOldData($formnewObj->emailtemplatesender,$replates);
                    $formnewObj->success_message = $this->replateOldData($formnewObj->success_message,$replates);
                    $formnewObj->error_message = $this->replateOldData($formnewObj->error_message,$replates);

                    if(isset($formnewObj->sender_name) && $formnewObj->sender_name !='')
                        $formnewObj->sender_name = $this->replateOldData($formnewObj->sender_name,$replates);


                    /* From version 1.2.0 */
                    $formnewObj->replysubject = $this->replateOldData($formnewObj->replysubject,$replates);
                    $formnewObj->replyemailtemplate = $this->replateOldData($formnewObj->replyemailtemplate,$replates);
                }
                $languages = Language::getLanguages(false);
                foreach($languages as $lang){
                    $formnewObj->title[(int)$lang['id_lang']] .= '-'.$this->l('Copy');
                    $formnewObj->rewrite[(int)$lang['id_lang']] .= '-'.$this->l('copy');
                }
                
                $formnewObj->fields = implode(',',$fields);
                $formnewObj->save();
                $allshop = Shop::getShops(true,null,true);
                foreach ($languages as $lang){
                    if($allshop){
                        $this->parseEmailAndTpl((int)$formnewObj->id,$lang,$allshop);
                        foreach($allshop as $id_shop){
                            $this->module->setFormUrl($formnewObj->id,$formnewObj->rewrite[$lang["id_lang"]],$lang["id_lang"],$id_shop);
                        }
                    }else{
                        $id_shop = $this->context->shop->id;
                        $this->parseEmailAndTpl((int)$formnewObj->id,$lang);
                        $this->module->setFormUrl($formnewObj->id,$formnewObj->rewrite[$lang["id_lang"]],$lang["id_lang"],$id_shop);
                    }
                }
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminGformmanager').'&updategformbuilderpro&id_gformbuilderpro='.(int)$formnewObj->id);
            }
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminGformmanager').'&updategformbuilderpro&id_gformbuilderpro='.(int)$id_gformbuilderpro);
        }elseif (Tools::isSubmit('gfromloaddefault')){
	       $fields = Tools::getValue('fields');
           $results = array(
                'errors'=>'1',
                'datas'=>array(),
                'datastext'=>array()
            );
           if($fields){
                $languages = Language::getLanguages(false);
                $id_shop = $this->context->shop->id;
                $logo = $this->context->link->getMediaLink(_PS_IMG_.Configuration::get('PS_LOGO'));
                
                
                $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/front/emaildefault.tpl';
                $template = Tools::getValue('template');
                if($template !='' && file_exists(_PS_MODULE_DIR_.'gformbuilderpro/views/templates/front/emailtemplates/'.$template.'.tpl'))
                    $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/front/emailtemplates/'.$template.'.tpl';
                $shopname=Tools::safeOutput(Configuration::get('PS_SHOP_NAME', null, null, $id_shop));
                foreach($languages as $language){
                    $fieldsData = gformbuilderprofieldsModel::getAllFields($fields,(int)$language['id_lang'],$id_shop);
                    Context::getContext()->smarty->assign(array(
                                        'shop_logo'=>$logo,
                                        'shopname'=>$shopname,
                                        'shopurl'=>Context::getContext()->link->getPageLink('index', true, $language['id_lang'], null, false, $id_shop),
                            	        'fieldsData' => $fieldsData,
                                        'datassender'=>false,
                                        'datasreply'=>0
                                    ));
                                                                        
                    $results['datas'][$language['id_lang']] = Context::getContext()->smarty->fetch($tpl);
                    Context::getContext()->smarty->assign(array(
                                        'shop_logo'=>$logo,
                                        'fieldsData' => null,
                                        'shopname'=>$shopname,
                                        'shopurl'=>Context::getContext()->link->getPageLink('index', true, $language['id_lang'], null, false, $id_shop),
                                        'datassender'=>true,
                                        'datasreply'=>0
                                    ));
                    $results['subject'][$language['id_lang']] = Tools::getValue('title_'.$language['id_lang']).' - '.$this->l('New message');
                    $results['datassender'][$language['id_lang']] = Context::getContext()->smarty->fetch($tpl);
                    $results['datassendersubject'][$language['id_lang']] = $this->l('Your message has been successfully sent');
                    
                    /* from version 1.2.0 */
                    Context::getContext()->smarty->assign(array(
                        'datasreply'=>1
                    ));
                    $results['replysubject'][$language['id_lang']] = $this->l('Reply').': '.Tools::getValue('title_'.$language['id_lang']);
                    $results['replyemailtemplate'][$language['id_lang']] = Context::getContext()->smarty->fetch($tpl);
                    
                }
                $results['errors'] = 0;
           }
	       die(Tools::jsonEncode($results));
	   }elseif (Tools::isSubmit('gfromloadshortcode')){
	       $fields = Tools::getValue('fields');
           $results = array(
                'errors'=>'1',
                'datas'=>array()
            );
           if($fields !='' && $fields !=','){
                $languages = Language::getLanguages(false);
                $id_shop = $this->context->shop->id;
                $logo = $this->context->link->getMediaLink(_PS_IMG_.Configuration::get('PS_LOGO'));
                $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/front/emaildefault.tpl';
                $shopname=Tools::safeOutput(Configuration::get('PS_SHOP_NAME', null, null, $id_shop));
                $file_fields = array();$get_file_field = true;
                foreach($languages as $language){
                    $fieldsData = gformbuilderprofieldsModel::getAllFields($fields,(int)$language['id_lang'],$id_shop);
                    if($get_file_field) {
                        $get_file_field = false;
                        if ($fieldsData) {
                            foreach ($fieldsData as $field) {
                                if ($field['type'] == 'fileupload') {
                                    $file_fields[] = $field['name'];
                                }
                            }
                        }
                    }
                    Context::getContext()->smarty->assign(array(
                                        'shop_logo'=>$logo,
                            	        'fieldsData' => $fieldsData,
                                        'shopname'=>$shopname,
                                        'shopurl'=>Context::getContext()->link->getPageLink('index', true, $language['id_lang'], null, false, $id_shop),
                                    ));
                    foreach($fieldsData as $field)
                        if($field['type'] !='html' && $field['type'] !='submit' && $field['type'] !='captcha' && $field['type'] !='googlemap' && $field['type'] !='privacy')
                            $results['datas'][$language['id_lang']][] = array(
                                'id_gformbuilderprofields'=>$field['id_gformbuilderprofields'],
                                'label'=>$field['label'],
                                'shortcode'=>' {'.$field['name'].'}'
                            );
                    $results['datas'][$language['id_lang']][] = array(
                                'label'=>$this->l('Ip Address'),
                                'shortcode'=>' {user_ip}'
                            );
                    $results['datas'][$language['id_lang']][] = array(
                                'label'=>$this->l('Date add'),
                                'shortcode'=>'{date_add}'
                            );
                }
                $results['file_fields'] = $file_fields;
                $results['errors'] = 0;
           }
	       die(Tools::jsonEncode($results));
	   }elseif (Tools::isSubmit('gformgetproduct')){
	       $query = Tools::getValue('q', false);
            if (!$query or $query == '' or Tools::strlen($query) < 1) {
                die();
            }
            if ($pos = strpos($query, ' (ref:')) {
                $query = Tools::substr($query, 0, $pos);
            }
            $excludeIds = Tools::getValue('excludeIds', false);
            if ($excludeIds && $excludeIds != 'NaN') {
                $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
            } else {
                $excludeIds = '';
            }
            $context = Context::getContext();
            $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, image_shop.`id_image` id_image, il.`legend`, p.`cache_default_attribute`
            		FROM `'._DB_PREFIX_.'product` p
            		'.Shop::addSqlAssociation('product', 'p').'
            		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = '.(int)$context->language->id.Shop::addSqlRestrictionOnLang('pl').')
            		LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
            			ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')
            		LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$context->language->id.')
            		WHERE (pl.name LIKE \'%'.pSQL($query).'%\' OR p.reference LIKE \'%'.pSQL($query).'%\') 
                    '.(!empty($excludeIds) ? ' AND p.id_product NOT IN ('.$excludeIds.') ' : ' ').
                    ' GROUP BY p.id_product';
            if(version_compare(_PS_VERSION_,'1.6.0.12') == -1){
                $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, MAX(image_shop.`id_image`) id_image, il.`legend`
        		FROM `'._DB_PREFIX_.'product` p
        		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = '.(int)$context->language->id.Shop::addSqlRestrictionOnLang('pl').')
        		LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
        		Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
        		LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$context->language->id.')
        		WHERE (pl.name LIKE \'%'.pSQL($query).'%\' OR p.reference LIKE \'%'.pSQL($query).'%\')'.
        		(!empty($excludeIds) ? ' AND p.id_product NOT IN ('.$excludeIds.') ' : ' ').
        		' GROUP BY p.id_product';
            }elseif(version_compare(_PS_VERSION_,'1.6.1.0') == -1){
                $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, MAX(image_shop.`id_image`) id_image, il.`legend`, p.`cache_default_attribute`
        		FROM `'._DB_PREFIX_.'product` p
        		'.Shop::addSqlAssociation('product', 'p').'
        		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = '.(int)$context->language->id.Shop::addSqlRestrictionOnLang('pl').')
        		LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
        		Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
        		LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$context->language->id.')
        		WHERE (pl.name LIKE \'%'.pSQL($query).'%\' OR p.reference LIKE \'%'.pSQL($query).'%\')'.
        		(!empty($excludeIds) ? ' AND p.id_product NOT IN ('.$excludeIds.') ' : ' ').
        		' GROUP BY p.id_product';
            }
            $items = Db::getInstance()->executeS($sql);
            $results = array();
            if ($items) {
                foreach ($items as $item) {
                    $product = array(
                        'id' => (int)($item['id_product']),
                        'name' => $item['name'],
                        'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                        'image' => str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $item['id_image'], Configuration::get('GF_PRODUCT_TYPE'))),
                    );
                array_push($results, $product);
                }
            }
            $results = array_values($results);
            echo Tools::jsonEncode($results);
            die();
        }elseif (Tools::isSubmit('gformgetproductwhosale')){
            $query = Tools::getValue('q', false);
            $limit = Tools::getValue('search_number', false);
            $excludeIds = Tools::getValue('excludeids', false);
            $includeids = Tools::getValue('includeids', false);
            $combins  = Tools::jsonDecode(Tools::getValue('combins'), true);
            $results = $this->GformgetProductwhosale($query, $limit, $excludeIds, $includeids, $combins);
            echo Tools::jsonEncode($results);
            die();
        } elseif (Tools::isSubmit('getProductwhosaleConfig')) {
            $products = Tools::getValue('products');
            $combins  = Tools::getValue('combins');
            $results  = array();
            if($products) {
                $combins = Tools::jsonDecode($combins, true);
                $results = $this->GformgetProductwhosale('', 0, '', $products, $combins);
            }
            echo Tools::jsonEncode($results);
            die();
        }else{
    	   if (Tools::isSubmit('deletefields')){
    	       $deletefields = Tools::getValue('deletefields');
               if($deletefields !=''){
                    $deletefields_array = explode('_',$deletefields);
                    foreach($deletefields_array as $deletefield){
                        $fieldObj = new gformbuilderprofieldsModel((int)$deletefield);
                        if($fieldObj->id_gformbuilderprofields == (int)$deletefield && $fieldObj->id_gformbuilderprofields){
                            $fieldObj->delete();
                        }
                    }
               }
    	   }
           $savenew = false;
           $id_gformbuilderpro = (int)Tools::getValue('id_gformbuilderpro');
           if($id_gformbuilderpro<=0){ $savenew = true;}
           //minify html code before save to database (remove space in HTML file)
           //I can't use Tools:getValue here.
           if(Tools::getIsset('formtemplate')){
                $_POST['formtemplate'] = gformbuilderproModel::minifyHtml(Tools::getValue('formtemplate'));
           }
           //#end minify
           $return = parent::postProcess(true);
           if (Tools::isSubmit('submitAddgformbuilderpro')){
               if(is_object($return) && get_class($return) == 'gformbuilderproModel'){
                    $id_gformbuilderpro = (int)Tools::getValue('id_gformbuilderpro');
                    if($id_gformbuilderpro<=0){ $id_gformbuilderpro = $return->id;}
                    $formObj = Module::getInstanceByName('gformbuilderpro');
                    $hooks = Tools::getValue('hooks');
                    if($hooks){
                        $hooks_array = explode(',',$hooks);
                        foreach ($hooks_array as $hook)
                        {
                            if (Validate::isHookName($hook) && !$formObj->isRegisteredInHook($hook))
                            {
                                $formObj->registerHook($hook);
                            }
                        }
                    }
                    $shopsactive = Tools::getValue('checkBoxShopAsso_gformbuilderpro');
                    $this->parseMyCss($id_gformbuilderpro);
                    if($savenew){
                        $formModelObj = new gformbuilderproModel($id_gformbuilderpro);
                        $languages = Language::getLanguages(false);
                        $id_shop = $this->context->shop->id;
                        $logo = $this->context->link->getMediaLink(_PS_IMG_.Configuration::get('PS_LOGO'));
                        $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/front/emaildefault.tpl';
                        $shopname=Tools::safeOutput(Configuration::get('PS_SHOP_NAME', null, null, $id_shop));
                        foreach($languages as $language){
                            if(isset($formModelObj->emailtemplate) && 
                                isset($formModelObj->emailtemplatesender) && 
                                (
                                $formModelObj->emailtemplate[(int)$language['id_lang']] == '' || 
                                $formModelObj->emailtemplatesender[(int)$language['id_lang']] == '' || 
                                $formModelObj->replyemailtemplate[(int)$language['id_lang']] == ''
                                )
                            ){
                                $fieldsData = gformbuilderprofieldsModel::getAllFields($formModelObj->fields,(int)$language['id_lang'],$id_shop);
                                Context::getContext()->smarty->assign(array(
                                                    'shop_logo'=>$logo,
                                        	        'fieldsData' => $fieldsData,
                                                    'shopname'=>$shopname,
                                                    'shopurl'=>Context::getContext()->link->getPageLink('index', true, $language['id_lang'], null, false, $id_shop),
                                                ));
                                if($formModelObj->emailtemplate[(int)$language['id_lang']] == ''){
                                    $formModelObj->emailtemplate[(int)$language['id_lang']] = Context::getContext()->smarty->fetch($tpl);
                                }
                                if($formModelObj->replyemailtemplate[(int)$language['id_lang']] == ''){
                                    Context::getContext()->smarty->assign(array(
                                        'datasreply'=>1
                                    ));
                                    $formModelObj->replyemailtemplate[(int)$language['id_lang']] = Context::getContext()->smarty->fetch($tpl);
                                    Context::getContext()->smarty->assign(array(
                                        'datasreply'=>0
                                    ));
                                }
                            }
                        }
                        $formModelObj->update();
                    }
                    foreach (Language::getLanguages() as $lang){
                        if($shopsactive){
                            $this->parseEmailAndTpl((int)$id_gformbuilderpro,$lang,$shopsactive);
                            foreach($shopsactive as $id_shop){
                                $formObj->setFormUrl($id_gformbuilderpro,$return->rewrite[$lang["id_lang"]],$lang["id_lang"],$id_shop);
                            }
                        }else{
                            $id_shop = $this->context->shop->id;
                            $this->parseEmailAndTpl((int)$id_gformbuilderpro,$lang);
                            $formObj->setFormUrl($id_gformbuilderpro,$return->rewrite[$lang["id_lang"]],$lang["id_lang"],$id_shop);
                        }
                    }



                    /** save integration config */
                    $mailchimplist = Tools::getValue('mailchimplist');
                    $mailchimpmap = Tools::getValue('mailchimpmap');
                    $klaviyolist = Tools::getValue('klaviyolist');
                    $klaviyomap = Tools::getValue('klaviyomap');
                    $zapierwebhook = Tools::getValue('zapierwebhook');
                    $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'gform_integration_map`(`id_gformbuilderpro`,`mailchimp_list`,`klaviyo_list`,`webhook_url`) 
                            VALUES('.(int)$id_gformbuilderpro.',"'.pSql($mailchimplist).'","'.pSql($klaviyolist).'","'.pSql($zapierwebhook).'") 
                            ON DUPLICATE KEY UPDATE `mailchimp_list` = VALUES(mailchimp_list),`klaviyo_list` = VALUES(klaviyo_list),`webhook_url` = VALUES(webhook_url)';
                    Db::getInstance()->execute( $sql);
                    if($mailchimpmap && $klaviyomap){
                        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'gform_mailchimp_klaviyo_map`(`id_gformbuilderprofields`,`mailchimp_tag`,`klaviyo_label`) VALUES';
                        foreach($mailchimpmap as $key=>$map){
                            $sql .= '('.(int)$key.',"'.pSql($map).'","'.(isset($klaviyomap[$key]) ? pSql($klaviyomap[$key]) : '').'"),';
                        }
                        $sql = rtrim($sql,',');/** remove last comma */
                        $sql .= ' ON DUPLICATE KEY UPDATE `mailchimp_tag` = VALUES(mailchimp_tag),`klaviyo_label` = VALUES(klaviyo_label)';
                        Db::getInstance()->execute($sql);
                    }
               }
           }
       }
	}
    
    /*css my font*/
    public function parseMyCss($id_gformbuilderpro){
        $formModelObj = new gformbuilderproModel($id_gformbuilderpro);
        $filename = 'mycss_'.$formModelObj->id;
        $useSSL = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
        $protocol_content = ($useSSL) ? 'https:/'.'/' : 'http:/'.'/';$protocol_content;

        $css_dir  = _PS_MODULE_DIR_.'/gformbuilderpro/views/css/front/customcss/';
        if (!is_dir($css_dir)){
            @mkdir($css_dir, 0755);
        }
        if(!file_exists($css_dir.'/index.php'))
            @copy(_PS_MODULE_DIR_.'gformbuilderpro/index.php', $css_dir.'index.php');
        $file = $css_dir.$filename.'.css';
        $handle  = fopen($file, 'w+');
        fwrite($handle, self::minify_css($formModelObj->customcss));
        fclose($handle);
    }
    
    public static function  minify_css($input) {
        if(method_exists('Tools','minifyCSS')){
            return Tools::minifyCSS($input);
        }else{
            if(trim($input) === "") return $input;
            return preg_replace(
                array(
                    // Remove comment(s)
                    '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
                    // Remove unused white-space(s)
                    '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~+]|\s*+-(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
                    // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
                    '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
                    // Replace `:0 0 0 0` with `:0`
                    '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
                    // Replace `background-position:0` with `background-position:0 0`
                    '#(background-position):0(?=[;\}])#si',
                    // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
                    '#(?<=[\s:,\-])0+\.(\d+)#s',
                    // Minify string value
                    '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
                    '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
                    // Minify HEX color code
                    '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
                    // Replace `(border|outline):none` with `(border|outline):0`
                    '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
                    // Remove empty selector(s)
                    '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
                ),
                array(
                    '$1',
                    '$1$2$3$4$5$6$7',
                    '$1',
                    ':0',
                    '$1:0 0',
                    '.$1',
                    '$1$3',
                    '$1$2$4$5',
                    '$1$2$3',
                    '$1:0',
                    '$1$2'
                ),
            $input);
        }
    }
    public function GformgetProductwhosale ($query, $limit, $excludeIds, $includeIds, $combies=array()) {
        $context = Context::getContext();
        $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, image_shop.`id_image` id_image, il.`legend`, p.`cache_default_attribute`
                FROM `'._DB_PREFIX_.'product` p
                '.Shop::addSqlAssociation('product', 'p').'
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = '.(int)$context->language->id.Shop::addSqlRestrictionOnLang('pl').')
                LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
                    ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')
                LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$context->language->id.') WHERE';
        if(version_compare(_PS_VERSION_,'1.6.0.12') == -1){
            $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, MAX(image_shop.`id_image`) id_image, il.`legend`
            FROM `'._DB_PREFIX_.'product` p
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = '.(int)$context->language->id.Shop::addSqlRestrictionOnLang('pl').')
            LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
            Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
            LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$context->language->id.') WHERE';
        }elseif(version_compare(_PS_VERSION_,'1.6.1.0') == -1){
            $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, MAX(image_shop.`id_image`) id_image, il.`legend`, p.`cache_default_attribute`
            FROM `'._DB_PREFIX_.'product` p
            '.Shop::addSqlAssociation('product', 'p').'
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = '.(int)$context->language->id.Shop::addSqlRestrictionOnLang('pl').')
            LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
            Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
            LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$context->language->id.') WHERE';
        }
        if (!$query or $query == '' or  Tools::strlen($query) < 1) {
            $sql .=' p.active = 1 AND p.`id_product`'. (!empty($excludeIds) ? ' AND p.`id_product` NOT IN ('.$excludeIds.') ' : ' ') . (!empty($includeIds) ? ' AND p.`id_product` IN ('.$includeIds.') ' : ' ');
        } else {
            $sql .=' (pl.`name` LIKE \'%'.pSQL($query).'%\' OR p.`reference` LIKE \'%'.pSQL($query).'%\' OR  p.`id_product`='.(int)$query.') AND p.active = 1'. (!empty($excludeIds) ? ' AND p.`id_product` NOT IN ('.$excludeIds.') ' : ' ') . (!empty($includeIds) ? ' AND p.`id_product` IN ('.$includeIds.') ' : ' ');
        }
        if (empty($includeIds)) {
            $sql .=' GROUP BY p.id_product limit '.(int)$limit.','.((int)$limit + 15);
        } else {
            $sql .=' GROUP BY p.id_product';
        }
        $items = Db::getInstance()->executeS($sql);
        $results = array();
        if ($items) {
            foreach ($items as $item) {
                $product = array(
                    'id' => (int)($item['id_product']),
                    'name' => $item['name'],
                    'conbination' => $this->Productcombiehtml((int)$item['id_product'], $combies),
                    'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                    'image' => str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $item['id_image'], Configuration::get('GF_PRODUCT_TYPE'))),
                );
            array_push($results, $product);
            }
        }
        return array_values($results);
    }
    public function Productcombiehtml($id_product , $combies= array())
    {
        $product = new Product($id_product);
        $attributes = $product->getAttributesGroups((int)$this->context->language->id);
        $combinations = array();
        foreach ($attributes as $attribute)
        {
            $combinations[$attribute['id_product_attribute']]['id_product_attribute'] = $attribute['id_product_attribute'];
            if (!isset($combinations[$attribute['id_product_attribute']]['attributes']))
            {
                $combinations[$attribute['id_product_attribute']]['attributes'] = '';
            }
            $combinations[$attribute['id_product_attribute']]['attributes'] .= $attribute['attribute_name'].' - ';
        }
        foreach ($combinations as &$combination)
        {
            $checked = '';
            if (isset($combies[$id_product]) && in_array((int)$combination['id_product_attribute'], $combies[$id_product])) {

                $checked = 'checked="checked"';
            }
            $combination['attributes'] = rtrim($combination['attributes'], ' - ');
            $combination['checked'] =  $checked;
            $combination['combination_price'] = Tools::disPlayprice($product->getPriceStatic((int)$product->id,true,(int)$combination['id_product_attribute']));
        }
        $_html = '';
        if(isset($combinations) && !empty($combinations))
        {
            $this->context->smarty->assign(array(
                'combinations'     => $combinations,
                'combination_html' => 'check_box',
                'id_product'       => (int)$product->id,
            ));
            $_html = $this->context->smarty->fetch(_PS_MODULE_DIR_ . "gformbuilderpro/views/templates/admin/gformmanager/product_conbination.tpl");
        }
        return $_html;
    }
    public function renderList()
    {
        $importexportform = '';
        $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/admin/extrahtml.tpl';
        Context::getContext()->smarty->assign(
            array(
                'action'=>'import_export_form',
                'url_controller' => $this->context->link->getAdminLink('AdminGformmanager'),
            )
        );
        $importexportform = Context::getContext()->smarty->fetch($tpl);
        return parent::renderList().$importexportform;
    }
    
    public function renderForm()
    {
        $isps17 = version_compare(_PS_VERSION_, '1.7', '>=');
        Context::getContext()->smarty->assign(array('isps17' => $isps17));
        $id_gformbuilderpro = (int)Tools::getValue('id_gformbuilderpro');
        $gformdefault = (int)Tools::getValue('gformdefault');
        if ($gformdefault > 0 && $id_gformbuilderpro < 1) {
            $result = $this->module->importGform('defaultform/gformbuilderpro_'.$gformdefault.'.zip');
            if ($result && isset($result['id_extraform'])) {
                $id_gformbuilderpro = (int)$result['id_extraform'];
            }
        }
        $allfieldstype = gformbuilderprofieldsModel::getAllFieldType();
        $types = array();
        foreach($allfieldstype as $fieldtype){
            foreach($fieldtype['config'] as $config){
                if(!isset($config['name']))  $config['name'] = '';
                if(!isset($types[$config['name']])) $types[$config['name']] = array();
                $types[$config['name']][$config['type']] = $config['type'];
            }
                
        }
        if(version_compare(_PS_VERSION_,'1.6') != -1){
            Media::addJsDef(array('allfieldstype'=>$allfieldstype));
        }
        $languages = Language::getLanguages(false);
        $fielddatas = array();
        $condition_fielddatas = array();
        if($id_gformbuilderpro>0){
            $formObj = new gformbuilderproModel((int)$id_gformbuilderpro);
            $datas = array();
            $fields = $formObj->fields;
            $file_fields = array();
            if($fields){
                $id_shop = $this->context->shop->id;
                $_fielddatas = gformbuilderprofieldsModel::getAllFieldDatas($fields,$id_shop);
                foreach($languages as $language){
                    $datas[$language['id_lang']] = array();
                    $datas[$language['id_lang']][] = array(
                            'id_gformbuilderprofields'=> 'user_ip',
                            'label'=>$this->l('Ip Address'),
                            'shortcode'=>'{user_ip}'
                        );
                    $datas[$language['id_lang']][] = array(
                                'id_gformbuilderprofields'=> 'date_add',
                                'label'=>$this->l('Date add'),
                                'shortcode'=>'{date_add}'
                            );
                }
                if($_fielddatas){
                    foreach($_fielddatas as $fielddata){
                        if($fielddata['type'] !='html' && $fielddata['type'] !='captcha' && $fielddata['type'] !='googlemap' && $fielddata['type'] !='submit') {
                            $datas[$fielddata['id_lang']][] = array(
                                'id_gformbuilderprofields'=>$fielddata['id_gformbuilderprofields'],
                                'label'=>$fielddata['label'],
                                'shortcode'=>' {'.$fielddata['name'].'}'
                            );
                            /* update new version 1.3.6*/
                            if(!isset($condition_fielddatas[(int)$fielddata['id_gformbuilderprofields']])){
                                $condition_fielddatas[(int)$fielddata['id_gformbuilderprofields']] = $fielddata;
                                $condition_fielddatas[(int)$fielddata['id_gformbuilderprofields']]['label'] = array();
                                $condition_fielddatas[(int)$fielddata['id_gformbuilderprofields']]['value'] = array();
                                $condition_fielddatas[(int)$fielddata['id_gformbuilderprofields']]['placeholder'] = array();
                                $condition_fielddatas[(int)$fielddata['id_gformbuilderprofields']]['description'] = array();
                            }
                            $condition_fielddatas[(int)$fielddata['id_gformbuilderprofields']]['label'][(int)$fielddata['id_lang']] = $fielddata['label'];
                            $condition_fielddatas[(int)$fielddata['id_gformbuilderprofields']]['value'][(int)$fielddata['id_lang']] = $fielddata['value'];
                            $condition_fielddatas[(int)$fielddata['id_gformbuilderprofields']]['placeholder'][(int)$fielddata['id_lang']] = $fielddata['placeholder'];
                            $condition_fielddatas[(int)$fielddata['id_gformbuilderprofields']]['description'][(int)$fielddata['id_lang']] = $fielddata['description'];
                            /*end*/
                        }
                        if ($fielddata['type'] == 'fileupload') {
                            $file_fields[$fielddata['name']] = $fielddata['name'];
                        }
                        if(!isset($fielddatas[(int)$fielddata['id_gformbuilderprofields']])){
                            $fielddatas[(int)$fielddata['id_gformbuilderprofields']] = $fielddata;
                            $fielddatas[(int)$fielddata['id_gformbuilderprofields']]['label'] = array();
                            $fielddatas[(int)$fielddata['id_gformbuilderprofields']]['value'] = array();
                            $fielddatas[(int)$fielddata['id_gformbuilderprofields']]['placeholder'] = array();
                            $fielddatas[(int)$fielddata['id_gformbuilderprofields']]['description'] = array();
                        }
                        $fielddatas[(int)$fielddata['id_gformbuilderprofields']]['condition_listoptions'] = $fielddata['condition_listoptions'] !='' ? Tools::jsonDecode($fielddata['condition_listoptions'], true) : array();
                        $fielddatas[(int)$fielddata['id_gformbuilderprofields']]['number_listcondition'] = $fielddata['condition_listoptions'] !='' ? count(Tools::jsonDecode($fielddata['condition_listoptions'], true)) : 0;
                        $fielddatas[(int)$fielddata['id_gformbuilderprofields']]['label'][(int)$fielddata['id_lang']] = $fielddata['label'];
                        $fielddatas[(int)$fielddata['id_gformbuilderprofields']]['value'][(int)$fielddata['id_lang']] = $fielddata['value'];
                        $fielddatas[(int)$fielddata['id_gformbuilderprofields']]['placeholder'][(int)$fielddata['id_lang']] = $fielddata['placeholder'];
                        $fielddatas[(int)$fielddata['id_gformbuilderprofields']]['description'][(int)$fielddata['id_lang']] = $fielddata['description'];
                    }
                }                
                
           }
           $admin_attachfiles = explode(',',$formObj->admin_attachfiles);
           $sender_attachfiles = explode(',',$formObj->sender_attachfiles);
           $filefields = array(
               'admin_attachfiles'=>array(),
               'sender_attachfiles'=>array(),
           );
           if($file_fields){
               foreach ($file_fields as $file_field){
                   $filefields['admin_attachfiles'][] = array('name'=>$file_field,'checked'=>in_array($file_field,$admin_attachfiles));
                   $filefields['sender_attachfiles'][] = array('name'=>$file_field,'checked'=>in_array($file_field,$sender_attachfiles));
               }
           }


           $this->fields_value = array(
                'formtemplate'=>Tools::getValue('formtemplate',$formObj->formtemplate),
                'allfieldstype'=>$allfieldstype,
                'shortcodes'=>$datas,
                'autoredirect'=>(bool)$formObj->autoredirect,
                'timedelay'=>(int)$formObj->timedelay,
                'redirect_link'=>$formObj->redirect_link,
                'using_condition'=>(int)$formObj->using_condition,
                'admin_attachfiles'=>$formObj->admin_attachfiles,
                'sender_attachfiles'=>$formObj->sender_attachfiles,
                'filefields'=>$filefields,
                'redirect_link_lang'=>$formObj->redirect_link_lang,
            );
            $condition_configs = $formObj->condition_configs;
            $this->fields_value['condition_configs_json'] = $formObj->condition_configs;
            if($condition_configs !=''){
                $this->fields_value['condition_configs'] = $this->module->htmlEntityDecode(Tools::jsonDecode($condition_configs,true));
            }
            foreach($languages as $language){
                $this->fields_value['popup_label'][$language['id_lang']] = $formObj->popup_label[(int)$language['id_lang']];
            }
            
            
        }else{
            $this->fields_value = array(
                'formtemplate'=>Tools::getValue('formtemplate',''),
                'allfieldstype'=>$allfieldstype,
                'sendto'=>Configuration::get('PS_SHOP_EMAIL'),
                'autoredirect'=>0,
                'timedelay'=>0,
                'redirect_link'=>'',
                'redirect_link_lang'=>array()
            );
            
            foreach($languages as $language){
                $this->fields_value['subject'][$language['id_lang']] = '';
                $this->fields_value['popup_label'][$language['id_lang']] = $this->l('Open form');
            } 
        }
        $this->fields_value['blank_img'] = '../modules/gformbuilderpro/views/img/black_img.png';
        
        $id_shop_group = Shop::getContextShopGroupID();
		$id_shop = Shop::getContextShopID();
        $this->fields_value['idlang_default'] = (int)$this->context->language->id;
        $this->fields_value['field_width_default'] = (int)Configuration::get('GF_FIELD_WIDTH_DEFAULT', null, $id_shop_group, $id_shop);
        $this->fields_value['field_width_tablet_default'] = (int)Configuration::get('GF_FIELD_WIDTH_TABLET_DEFAULT', null, $id_shop_group, $id_shop);
        $this->fields_value['group_width_mobile_default'] = (int)Configuration::get('GF_FIELD_WIDTH_MOBILE_DEFAULT', null, $id_shop_group, $id_shop);
        $this->fields_value['group_width_default'] = (int)Configuration::get('GF_GROUP_WIDTH_DEFAULT', null, $id_shop_group, $id_shop);
        if($this->fields_value['field_width_default'] == 0){$this->fields_value['field_width_default'] = 12;}
        if($this->fields_value['field_width_tablet_default'] == 0){$this->fields_value['field_width_tablet_default'] = 12;}
        if($this->fields_value['group_width_mobile_default'] == 0){$this->fields_value['group_width_mobile_default'] = 12;}
        if($this->fields_value['group_width_default'] == 0){
            $this->fields_value['group_width_default'] = 12;
        }
        $this->fields_value['loadjqueryselect2'] = 1;
        if(version_compare(_PS_VERSION_,'1.6.0.7') == -1){
            $this->fields_value['loadjqueryselect2'] = 0;
        }
        if(version_compare(_PS_VERSION_,'1.6') == -1){
            $this->fields_value['psoldversion15'] = -1;
        }else $this->fields_value['psoldversion15'] = 0;
        
        
        $input = array();
        $input[] = array(
            'type' => 'formbuildertabopen',
            'name' => 'tabmain',
            'class' =>'activetab'
            );
        $input[] = array(
            'type' => 'text',
            'label' => $this->l('Form Title'),
            'hint' => $this->l('Invalid characters') . ' &lt;&gt;;=#{}',
            'name' => 'title',
            'size' => 255,
            'required' => true,
            'lang' => true);
        $input[] = array(
            'type' => 'tags',
            'label' => $this->l('Meta Keywords'),
            'name' => 'metakeywords',
            'lang' => true,
            'hint' => array($this->l('Invalid characters:') . ' &lt;&gt;;=#{}', $this->l('To add "Meta keywords" click in the field, write something, and then press "Enter."')));
        $input[] = array(
            'type' => 'textarea',
            'label' => $this->l('Meta Description'),
            'hint' => $this->l('Invalid characters') . ' &lt;&gt;;=#{}',
            'name' => 'metadescription',
            'lang' => true,
            'cols'=>50,
            'rows'=>5
            
            );
        $input[] = array(
            'type' => 'text',
            'label' => $this->l('Friendly Url Rewrite'),
            'name' => 'rewrite',
            'hint' => $this->l('Only letters and the hyphen (-) character are allowed.'),
            'size' => 255,
            'class'=>'rewrite_url',
            'lang' => true);
        $input[] = array(
            'type' => ($this->fields_value['psoldversion15'] > -1 ? 'customtags' : 'tags'),
            'label' => $this->l('Hooks to'),
            'hint' => $this->l('It mean, the module can display anywhere by hook. So if you want to display the module in left bar. Then enter "displayLeftColumn".'),
            'desc' => $this->l('To add "Hook" click in the field, write hook name(ex: displayHome), and then press "Enter".Learn more about Prestashop Front-office hook: ').'<a target="_blank" href="https://devdocs.prestashop.com/1.7/modules/concepts/hooks/list-of-hooks/">https://devdocs.prestashop.com/1.7/modules/concepts/hooks/list-of-hooks/</a>',
            'name' => 'hooks');
        $input[] = array(
            'type' => (version_compare(_PS_VERSION_,'1.6') == -1) ? 'radio' : 'switch',
            'label' => $this->l('Status'),
            'name' => 'active',
            'required' => false,
            'is_bool' => true,
            'class'=>'switch_radio',
            'values' => array(array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Active')), 
                    array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('Inactive'))));
        $input[] = array(
            'type' => (version_compare(_PS_VERSION_,'1.6') == -1) ? 'radio' : 'switch',
            'label' => $this->l('Save Submited Form Data to Database'),
            'hint' => $this->l('Yes if you want to collect and manage the submissions form data.'),
            'name' => 'saveemail',
            'required' => false,
            'is_bool' => true,
            'class'=>'switch_radio',
            'values' => array(array(
                    'id' => 'saveemail_on',
                    'value' => 1,
                    'label' => $this->l('Active')),
                    array(
                    'id' => 'saveemail_off',
                    'value' => 0,
                    'label' => $this->l('Inactive'))));
        $input[] = array(
            'type' => (version_compare(_PS_VERSION_,'1.6') == -1) ? 'radio' : 'switch',
            'label' => $this->l('Submit Form by Ajax?'),
            'hint' => $this->l('Yes, if you do not want reload form when customer click to submit button'),
            'name' => 'usingajax',
            'required' => false,
            'is_bool' => true,
            'class'=>'switch_radio',
            'values' => array(array(
                    'id' => 'usingajax_on',
                    'value' => 1,
                    'label' => $this->l('YES')),
                     array(
                    'id' => 'usingajax_off',
                    'value' => 0,
                    'label' => $this->l('NO'))));
        $input[] = array(
            'type' => (version_compare(_PS_VERSION_,'1.6') == -1) ? 'radio' : 'switch',
            'label' => $this->l('Required Login'),
            'hint' => $this->l('If Yes, then customer have to login before view form.'),
            'name' => 'requiredlogin',
            'required' => false,
            'is_bool' => true,
            'class'=>'switch_radio',
            'values' => array(array(
                    'id' => 'requiredlogin_on',
                    'value' => 1,
                    'label' => $this->l('Yes')),
                     array(
                    'id' => 'requiredlogin_off',
                    'value' => 0,
                    'label' => $this->l('No'))));

        
        // new field in v1.2.0            
        $input[] = array(
            'type' => 'openviapopup',
            'label' => $this->l('Open the form via a popup'),
            'name' => 'ispopup');              
        //# new field in v1.2.0 
        /*new field version 2021-Jun*/
        
        $input[] = array(
            'type'  => 'textarea',
            'label' => $this->l('Custom CSS'),
            'name'  => 'customcss',
            'class' => 'col-lg-8',
            'cols'  =>50,
            'rows'  =>10
            
            );

        if (Shop::isFeatureActive()) {
            $input[] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
                );
        }

        $input[] = array(
            'type' => 'formbuildertabclose',
            'name' => 'closetab1',
            );
        $input[] = array(
            'type' => 'formbuildertabopen',
            'name' => 'tabtemplate',
            );
        $input[] = array('type' => 'formbuilder', 'name' => 'formbuilder');
        /*
        $input[] = array(
            'type' => 'text',
            'label' => $this->l('Submit button title'),
            'name' => 'submittitle',
            'size' => 255,
            'required' => true,
            'lang' => true);
        */
        $input[] = array(
            'type' => 'textarea',
            'name' => 'fields',
            'class' => 'hidden',
            'cols'=>50,
            'rows'=>5
            );
        $input[] = array(
            'type' => 'formbuildertabclose',
            'name' => 'closetab2',
            );
        $input[] = array(
            'type' => 'formbuildertabopen',
            'name' => 'tabintegration',
            );
            $input[] = array(
                'type' => 'formbuildertabopen2',
                'name' => 'mailchimp',
            );
            $input[] = array(
                'type' => (version_compare(_PS_VERSION_,'1.6') == -1) ? 'radio' : 'switch',
                'label' => $this->l('Mailchimp'),
                'name' => 'mailchimp',
                'required' => false,
                'is_bool' => true,
                'class'=>'switch_radio',
                'values' => array(array(
                        'id' => 'mailchimp_on',
                        'value' => 1,
                        'label' => $this->l('Active')), 
                        array(
                        'id' => 'mailchimp_off',
                        'value' => 0,
                        'label' => $this->l('Inactive'))));
            $input[] = array(
                'type' => 'mailchimpmap',
                'name' => 'mailchimpmap',
            );
            $input[] = array(
                'type' => 'formbuildertabclose',
                'name' => 'closetabemail4',
            );
    
    
            $input[] = array(
                'type' => 'formbuildertabopen2',
                'name' => 'klaviyo',
            );
            $input[] = array(
                'type' => (version_compare(_PS_VERSION_,'1.6') == -1) ? 'radio' : 'switch',
                'label' => $this->l('Klaviyo'),
                'name' => 'klaviyo',
                'required' => false,
                'is_bool' => true,
                'class'=>'switch_radio',
                'values' => array(array(
                        'id' => 'klaviyo_on',
                        'value' => 1,
                        'label' => $this->l('Active')), 
                        array(
                        'id' => 'klaviyo_off',
                        'value' => 0,
                        'label' => $this->l('Inactive'))));
            $input[] = array(
                'type' => 'klaviyomap',
                'name' => 'klaviyomap',
            );
            $input[] = array(
                'type' => 'formbuildertabclose',
                'name' => 'closetabemail5',
            );
            $input[] = array(
                'type' => 'formbuildertabopen2',
                'name' => 'zapier',
            );
            $input[] = array(
                'type' => (version_compare(_PS_VERSION_,'1.6') == -1) ? 'radio' : 'switch',
                'label' => $this->l('Zapier'),
                'name' => 'zapier',
                'required' => false,
                'is_bool' => true,
                'class'=>'switch_radio',
                'values' => array(array(
                        'id' => 'zapier_on',
                        'value' => 1,
                        'label' => $this->l('Active')), 
                        array(
                        'id' => 'zapier_off',
                        'value' => 0,
                        'label' => $this->l('Inactive'))));
            $input[] = array(
                'type' => 'zapiermap',
                'name' => 'zapiermap',
            );
            $input[] = array(
                'type' => 'formbuildertabclose',
                'name' => 'closetabemail6',
            );
        /*
        $input[] = array(
            'type' => 'formbuildertabclose',
            'name' => 'closetab1',
            );*/
        $input[] = array(
            'type' => 'formbuildertabclose',
            'name' => 'closetab3',
            );
        
        $input[] = array(
            'type' => 'formbuildertabopen',
            'name' => 'tabemail',
            );
        $input[] = array(
            'type' => 'formbuildertabopen2',
            'name' => 'adminemail',
        );
        $input[] = array(
            'type' => 'tags',
            'label' => $this->l('Admin Email Address'),
            'desc' => $this->l('To add "Email" click in the field, write email(ex: demo@demo.com), and then press "Enter."'),
            'name' => 'sendto',
            'required' => true,
            );
        $input[] = array(
            'type' => 'using_condition',
            'name' => 'using_condition',
            'label' => '');

        $input[] = array(
            'type' => 'textarea',
            'label' => $this->l('Admin Subject'),
            'name' => 'subject',
            'lang' => true,
            'desc' => $this->l('You can use variables. You can see list of variables above. Example:').'<code>{input_1459352107}</code>',
            'required' => true,
            'cols'=>50,
            'rows'=>5);
        $input[] = array(
            'type' => 'using_email_template',
            'name' => 'using_email_template',
            'label' => '');
        $input[] = array(
            'type' => 'textarea',
            'label' => $this->l('Admin Message'),
            'name' => 'emailtemplate',
            'autoload_rte' => true,
            'lang' => true,
            'desc' => $this->l('You can use variables. You can see list of variables above. Example:').'<code>{input_1459352107}</code>',
            'required' => true,
            'cols'=>50,
            'rows'=>5);
        $input[] = array(
            'type' => 'attachfiles',
            'name' => 'admin_attachfiles',
            'label' => $this->l('File attachments'));
        $input[] = array(
            'type' => (version_compare(_PS_VERSION_,'1.6') == -1) ? 'radio' : 'switch',
            'label' => $this->l('Starred Message'),
            'name' => 'autostar',
            'required' => false,
            'is_bool' => true,
            'class'=>'switch_radio',
            'values' => array(array(
                    'id' => 'autostar_on',
                    'value' => 1,
                    'label' => $this->l('Active')), 
                    array(
                    'id' => 'autostar_off',
                    'value' => 0,
                    'label' => $this->l('Inactive'))));
        $input[] = array(
            'type' => 'formbuildertabclose',
            'name' => 'closetabemail1',
        );
        $input[] = array(
            'type' => 'formbuildertabopen2',
            'name' => 'senderemail',
        );
        $input[] = array(
            'type' => (version_compare(_PS_VERSION_,'1.6') == -1) ? 'radio' : 'switch',
            'label' => $this->l('Send email to Sender'),
            'name' => 'sendtosender',
            'required' => false,
            'desc' => $this->l('Yes if you want send message email to sender who submit email'),
            'is_bool' => true,
            'class'=>'switch_radio',
            'values' => array(array(
                    'id' => 'sendtosender_on',
                    'value' => 1,
                    'label' => $this->l('Yes')),
                     array(
                    'id' => 'sendtosender_off',
                    'value' => 0,
                    'label' => $this->l('No'))));
        $input[] = array(
            'type' => 'text',
            'label' => $this->l('Sender name'),
            'desc' => $this->l('Sender name will be get from form data. So you have to enter variable to this field. Example there is a field name in this form. Then you have to enter variable of the field. Example:').'<code>{input_1459352107}</code>',
            'name' => 'sender_name',
            'required' => false);
        $input[] = array(
            'type' => 'text',
            'label' => $this->l('Sender email'),
            'desc' => $this->l('Sender email will be get from form data. So you have to enter variable to this field. Example there is a field EMAIL in this form. Then you have to enter variable of the field. Example:').'<code>{input_1459352107}</code>',
            'name' => 'sender',
            'required' => false);
        $input[] = array(
            'type' => 'textarea',
            'label' => $this->l('Sender Subject'),
            'desc' => $this->l('You can use variables. You can see list of variables above. Example:').'<code>{input_1459352107}</code>',
            'name' => 'subjectsender',
            'lang' => true,
            'required' => false,
            'cols'=>50,
            'rows'=>5);
        $input[] = array(
            'type' => 'textarea',
            'label' => $this->l('Sender Message'),
            'desc' => $this->l('You can use variables. You can see list of variables above. Example:').'<code>{input_1459352107}</code>',
            'name' => 'emailtemplatesender',
            'autoload_rte' => true,
            'lang' => true,
            'cols'=>50,
            'rows'=>5);
        $input[] = array(
            'type' => 'attachfiles',
            'name' => 'sender_attachfiles',
            'label' => $this->l('File attachments'));
        $input[] = array(
            'type' => 'formbuildertabclose',
            'name' => 'closetabemail2',
        );
        $input[] = array(
            'type' => 'formbuildertabopen2',
            'name' => 'replyemail',
        );
        $input[] = array(
            'type' => 'textarea',
            'label' => $this->l('Reply Subject'),
            'desc' => $this->l('You can use variables. You can see list of variables above. Example:').'<code>{input_1459352107}</code>',
            'name' => 'replysubject',
            'lang' => true,
            'required' => false,
            'cols'=>50,
            'rows'=>5);
        $input[] = array(
            'type' => 'textarea',
            'label' => $this->l('Reply Message'),
            'desc' => '<code>{reply_message}</code>'.$this->l('is required').'.'.$this->l('You can use variables. You can see list of variables above. Example:').'<code>{input_1459352107}</code>',
            'name' => 'replyemailtemplate',
            'autoload_rte' => true,
            'lang' => true,
            'cols'=>50,
            'rows'=>5);
        $input[] = array(
            'type' => 'formbuildertabclose',
            'name' => 'closetabemail3',
        );
        $input[] = array(
            'type' => 'formbuildertabclose',
            'name' => 'closetab3',
            );
        $input[] = array(
            'type' => 'formbuildertabopen',
            'name' => 'tabmessage',
            );
        $input[] = array(
            'type' => 'textarea',
            'label' => $this->l('Sender\'s message was sent successfully'),
            'name' => 'success_message',
            'autoload_rte' => true,
            'desc' => $this->l('You can use variables. You can see list of variables in MAIL tab. Example:').'<code>{input_1459352107}</code>',
            'lang' => true,
            'cols'=>50,
            'rows'=>5);
        $input[] = array(
            'type' => 'textarea',
            'label' => $this->l('Sender\'s message failed to send'),
            'name' => 'error_message',
            'autoload_rte' => true,
            'lang' => true,
            'cols'=>50,
            'rows'=>5);
        // new field in v1.0.5
        $input[] = array(
            'type' => 'autoredirect',
            'label' => $this->l('Redirect after submit'),
            'name' => 'autoredirect');
        //# new field in v1.0.5
        $input[] = array(
            'type' => 'formbuildertabclose',
            'name' => 'closetab4',
            );
        if($id_gformbuilderpro) {
            $input[] = array(
                'type' => 'formbuildertabopen',
                'name' => 'publish',
            );
            $input[] = array(
                'type' => 'publish',
                'name' => 'publish',
            );
            $input[] = array(
                'type' => 'formbuildertabclose',
                'name' => 'closetab5',
            );
        }
        $this->fields_form = array(
            //'legend' => array('title' => $this->l('Form Config'), 'icon' => 'icon-cogs'),
            'input' => $input,
            'submit' => array(
                            'title' => $this->l('Save'),
                            'name' =>'submitAddgformbuilderpro'
                            ),
            'buttons' => array(
                'save_and_stay' => array(
                    'name' => 'submitAddgformbuilderproAndStay',
                    'type' => 'submit',
                    'title' => $this->l('Save and Stay'),
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save'
                ),
            )
        );
        $fields = '';
        if($id_gformbuilderpro){
            $formObj = new gformbuilderproModel((int)$id_gformbuilderpro,(int)Context::getContext()->language->id,(int)Context::getContext()->shop->id);
            $url_rewrite = Context::getContext()->link->getModuleLink('gformbuilderpro','form',array('id'=>(int)$id_gformbuilderpro,'rewrite'=>$formObj->rewrite));
            if (!strpos($url_rewrite, 'index.php')){
                $url_rewrite = str_replace('?module=gformbuilderpro&controller=form','',$url_rewrite);
            }
            // fix friendly url ps 1.5
            if(Configuration::get('PS_REWRITING_SETTINGS') && version_compare(_PS_VERSION_,'1.6','<'))
            {
                $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
                $shop = Context::getContext()->shop;
                $base = (($force_ssl) ? 'https://'.$shop->domain_ssl : 'http://'.$shop->domain);
                $url_rewrite =  $base.$shop->getBaseURI().'form/'.$formObj->rewrite.'-g'.(int)$id_gformbuilderpro.'.html';
            }
            //# fix friendly url ps 1.5
            Context::getContext()->smarty->assign(array(
                'gshortcode'=>'{gformbuilderpro:'.(int)$id_gformbuilderpro.'}',
                'smartycode'=>'{hook h=\'displayGform\' id=\''.(int)$id_gformbuilderpro.'\'}',
                'formlink' => $url_rewrite,
            ));
            $fields = trim($formObj->fields);
        }

        /** from 03/06/2020 */
        $mailchimp_lists = $this->module->getMailChimpList();
        $klaviyo_lists = $this->module->getKlaviyoList();
        if(isset($mailchimp_lists['error'])) 
            $this->fields_value['mailchimp_lists'] = array();
        else
            $this->fields_value['mailchimp_lists'] = $mailchimp_lists;
        if(isset($klaviyo_lists['error'])) 
            $this->fields_value['klaviyo_lists'] = array();
        else
            $this->fields_value['klaviyo_lists'] = $klaviyo_lists;
        $mailchimp_list = '';$webhook_url = '';$klaviyo_list = '';
        $mailchimp_maps = array();$klaviyo_label = array();
        if($id_gformbuilderpro > 0){
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'gform_integration_map` WHERE id_gformbuilderpro = '.(int)$id_gformbuilderpro;
            $integration_map = Db::getInstance()->getRow($sql);
            if($integration_map && isset($integration_map['mailchimp_list']) && $integration_map['mailchimp_list'] != ''){
                $mailchimp_list = $integration_map['mailchimp_list'];
            }
            if($integration_map && isset($integration_map['klaviyo_list']) && $integration_map['klaviyo_list'] != ''){
                $klaviyo_list = $integration_map['klaviyo_list'];
            }
            if($integration_map && isset($integration_map['webhook_url']) && $integration_map['webhook_url'] != ''){
                $webhook_url = $integration_map['webhook_url'];
            }
            if($fields !=''){
                $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'gform_mailchimp_klaviyo_map` 
                        WHERE id_gformbuilderprofields IN('.pSql($fields).')';
                $label_maps = Db::getInstance()->executeS($sql);
                if($label_maps){
                    foreach($label_maps as $label_map){
                        $mailchimp_maps[(int)$label_map['id_gformbuilderprofields']] = $label_map['mailchimp_tag'];
                        $klaviyo_label[(int)$label_map['id_gformbuilderprofields']] = $label_map['klaviyo_label'];
                    }
                }
            }


        }
        $this->fields_value['webhook_url'] = $webhook_url;
        $this->fields_value['klaviyo_list'] = $klaviyo_list;
        $this->fields_value['mailchimp_list'] = $mailchimp_list;
        $this->fields_value['mailchimp_tag'] = $mailchimp_maps;
        $this->fields_value['klaviyo_label'] = $klaviyo_label;

        $mailchimp_apiKey = Configuration::get('GF_MAILCHIMP_API_KEY', null, $id_shop_group, $id_shop);
        $klaviyo_apiKey = Configuration::get('GF_KLAVIYO_API_KEY', null, $id_shop_group, $id_shop);
        if($mailchimp_apiKey == ''){
            $this->fields_value['mailchimp_apikey_empty'] = 1;
        }
        if($klaviyo_apiKey == ''){
            $this->fields_value['klaviyo_apikey_empty'] = 1;
        }
        $this->fields_value['config_link'] = $this->context->link->getAdminLink('AdminGformconfig');
            $formrequest_link = '';$nbr_received = 0;
            if($id_gformbuilderpro){
                $formrequest_link = Context::getContext()->link->getAdminLink('AdminGformrequest');
                $nbr_received = gformrequestModel::getUnReadRequest((int)$id_gformbuilderpro);
            }
            $is_left_tab = (int)Configuration::get('GF_LEFT_TAB_BAR');
            if($is_left_tab != 1) $is_left_tab = 0;

            $row_layouts = array(
                array(
                    'title'=>$this->l('2 columns'),
                    'layouts' => array('6_6','5_7','7_5','4_8','8_4','3_9','9_3','2_10','10_2')
                ),
                array(
                    'title'=>$this->l('3 columns'),
                    'layouts' => array('4_4_4','3_6_3','3_3_6','6_3_3','6_4_2','2_4_6','2_6_4','4_6_2')
                ),
                array(
                    'title'=>$this->l('4 columns'),
                    'layouts' => array('3_3_3_3','2_2_4_4','4_4_2_2','4_2_2_4')
                ),
                array(
                    'title'=>$this->l('5 columns'),
                    'layouts' => array('2_2_2_2_4','4_2_2_2_2')
                )
            );
            $useSSL = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? true : false;
            $protocol_content = ($useSSL) ? 'https://' : 'http://';
            $base_uri = $protocol_content.Tools::getHttpHost().__PS_BASE_URI__;
            $loadjqueryselect2 = 1;
            if(version_compare(_PS_VERSION_,'1.6.0.7') == -1){
                $loadjqueryselect2 = 0;
            }
            Context::getContext()->smarty->assign(array(
                    'psversion15'=>version_compare(_PS_VERSION_,'1.6'),
                    'gdefault_language'=>(int)Context::getContext()->language->id,
                    'formrequest_link'=> $formrequest_link,
                    'nbr_received'=>$nbr_received,
                    'formrequest_id'=>(int)$id_gformbuilderpro,
                    'export_link'=>Context::getContext()->link->getAdminLink('AdminGformmanager',true).'&submitGexport_form=1&gid_forms='.(int)$id_gformbuilderpro,
                    'is_left_tab'=>$is_left_tab,
                    'row_layouts'=>$row_layouts,
                    'gformbuilderpro_submit_link'=>Context::getContext()->link->getAdminLink('AdminModules', true).'&configure='.$this->module->name.'&tab_module='.$this->module->tab.'&module_name='.$this->module->name,
                    'languages'=>Language::getLanguages(false),
                    'defaultFormLanguage'=>(int)Context::getContext()->language->id,
                    'base_uri'=>$base_uri,
                    'ajaxaction' => $this->context->link->getAdminLink('AdminGformmanager'),
                    'loadjqueryselect2'=>$loadjqueryselect2,
                    'fielddatas'=>$fielddatas,
                    'condition_fielddatas'=>$condition_fielddatas,
                    'Currencies'=> Tools::jsonEncode(Currency::getCurrencies(false, true, true)),
                    'id_currency_default' => $this->context->currency->id,
                ));
            if(version_compare(_PS_VERSION_,'1.6') == -1){
                Context::getContext()->smarty->assign(array('allfieldstype'=>Tools::jsonEncode($allfieldstype)));
            }
            $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/admin/gformmanager/tabs.tpl';
            $tpl2 = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/admin/gformmanager/popup_form.tpl';
            $formhtml= Context::getContext()->smarty->fetch($tpl).parent::renderForm().Context::getContext()->smarty->fetch($tpl2);
            if($is_left_tab)
            {
                Context::getContext()->smarty->assign(array('is_close_tab'=>1));
                $formhtml .= Context::getContext()->smarty->fetch($tpl);
                Context::getContext()->smarty->assign(array('is_close_tab'=>0));
            }
            return $formhtml;
    }
    public function parseEmailAndTpl($id_form,$lang,$id_shops=null){
        $this->module->parseEmailAndTpl($id_form,$lang,$id_shops);
    }
}
?>