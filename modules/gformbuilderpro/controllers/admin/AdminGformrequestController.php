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
include_once(_PS_MODULE_DIR_ . 'gformbuilderpro/classes/gformrequestModel.php');
include_once(_PS_MODULE_DIR_ . 'gformbuilderpro/classes/gformrequestreplyModel.php');
class AdminGformrequestController extends ModuleAdminController
{
    public $statuses_array = array();
    public function __construct()
    {
        $download = Tools::getValue('download');
        if($download!=''){
            if(file_exists(_PS_UPLOAD_DIR_.$download)){
                header('Content-Transfer-Encoding: binary');
                header('Content-Type: '.$download);
                header('Content-Length: '.filesize(_PS_UPLOAD_DIR_.$download));
                header('Content-Disposition: attachment; filename="'.utf8_decode($download).'"');
                @set_time_limit(0);
                readfile(_PS_UPLOAD_DIR_.$download);
            }
            exit;
        }
        $this->className = 'gformrequestModel';
        $this->table = 'gformrequest';
        parent::__construct();
        if (Tools::isSubmit('gfromSubmitReply')){
    	       $result = array();
               $idrequest = (int)Tools::getValue('idrequest');
               $requestObj = new gformrequestModel($idrequest);
               $id_shop = (int)Context::getContext()->shop->id;
               if(Validate::isLoadedObject($requestObj)){
                    $subject = Tools::getValue('gformbuilderpro_reply_subject');
                    $email = trim(Tools::getValue('gformbuilderpro_reply_to'));
                    $gformbuilderpro_reply = Tools::getValue('gformbuilderpro_reply');
                    $sendemail = true;$error_emails = array();$valid_emails = array();
                    if($email !=''){
                        if(!Validate::isCleanHtml($gformbuilderpro_reply)){
                            $result = array('error'=>1,'warrning'=>$this->l('Message invalid.'));
                            $sendemail = false;
                        }else{
                            $emails = explode(';',$email);
                            $params = array();
                            $id_lang = (int)$requestObj->id_lang;
                            $langObj = new Language($id_lang);
                            if(!Validate::isLoadedObject($langObj)) 
                                $id_lang = (int)Context::getContext()->language->id;
                            
                            $formObj = new gformbuilderproModel((int)$requestObj->id_gformbuilderpro,(int)$id_lang,(int)$id_shop);
                            if($requestObj->jsonrequest !=''){
                                $jsonrequest = Tools::jsonDecode($requestObj->jsonrequest,true);
                                if($jsonrequest){
                                    foreach($jsonrequest as $key=>$requestdata)
                                    {
                                        $params[$key] = $requestdata;
                                    }
                                }
                            }   
                            $params['{form_title}'] = $formObj->title;
                            $params['{shop_email}'] = Configuration::get('PS_SHOP_EMAIL');
                            $params['{shop_address}'] = Configuration::get('PS_SHOP_ADDR1').' '.Configuration::get('PS_SHOP_ADDR2');
                            $params['{shop_phone}'] = Configuration::get('PS_SHOP_PHONE');
                            $params['{shop_fax}'] = Configuration::get('PS_SHOP_FAX');
                            $params['{shop_name}'] = Tools::safeOutput(Configuration::get('PS_SHOP_NAME', null, null, $id_shop));
    			            $params['{shop_url}'] = Context::getContext()->link->getPageLink('index', true, $id_lang);
                            $params['%7Bshop_url%7D'] = $params['{shop_url}'];
                            $params['%7Bshop_name%7D'] = $params['{shop_name}'];
                            $params['{reply_message}'] = $gformbuilderpro_reply;
                            $params['%7Breply_message%7D'] = $params['{reply_message}'];
                            $params['{reply_subject}'] = $subject;
                            foreach($emails as $_email){
                                $_email = trim($_email);
                                if($_email !='' && Validate::isEmail($_email)){
                                    //$sendemail &= 
                                    $valid_emails[] = $_email;
                                    $params['{sender_email}'] = $_email;
                                    Mail::Send(
                                        (int)$id_lang,
                                        'reply_form_'.(int)$requestObj->id_gformbuilderpro.'_'.(int)$id_shop,
                                        $subject,
                                        $params,
                                        $_email,
                                        null,
                                        null, null, null, null, _PS_MODULE_DIR_.'gformbuilderpro/mails/', false, (int)$id_shop,null
                                    );
                                }else{
                                    if($_email !='')
                                        $error_emails[] = $_email;
                                }
                            }
                            if($error_emails){
                                $warrning = $this->l('Email invalid or empty.').'('.implode(',',$error_emails).')';
                                if($valid_emails){
                                    $warrning.=',   '.$this->l('Your Mail has been sent successfully').'('.implode(',',$valid_emails).')';
                                    $email = implode(';',$valid_emails);
                                }
                                $result = array(
                                    'error'=>1,
                                    'warrning'=>$warrning
                                );
                            }elseif(!$sendemail) 
                                $result = array('error'=>1,'warrning'=>$this->l('Error ! Please try again.'));
                        }
                    }else{
                        $result = array('error'=>1,'warrning'=>$this->l('Email invalid or empty.'));
                        $sendemail = false;
                    }
                    if($sendemail && $valid_emails){
                        /* Save to db */
                        $replyObj = new gformrequestreplyModel();
                        $replyObj->id_gformrequest = (int)$idrequest;
                        $replyObj->replyemail = $email;
                        $replyObj->subject = pSql($subject);
                        $replyObj->request = $gformbuilderpro_reply;
                        $replyObj->date_add = date("Y-m-d H:i:s");
                        if($replyObj->save())
                            if($error_emails){}
                            else 
                                $result = array('error'=>0,'warrning'=>$this->l('Your Mail has been sent successfully'));
                        else $result = array('error'=>1,'warrning'=>$this->l('Error ! Please try again.'));
                    }
               }else{
                    $result = array('error'=>1,'warrning'=>$this->l('Request not found'));
               }
    	       die(Tools::jsonEncode($result));
	    }elseif(Tools::isSubmit('gfromToggleStar')){
            $idrequest = (int)Tools::getValue('id_gformrequest');
            $requestObj = new gformrequestModel($idrequest);
            $result = array(
                'error'=>1,
                'warrning'=>$this->l('Error! Please try again')
            );
            if(Validate::isLoadedObject($requestObj)){
                $requestObj->star = (int)Tools::getValue('star');
                if($requestObj->save()){
                    $result = array(
                        'error'=>0,
                        'warrning'=>''
                    );
                }
            }
            die(Tools::jsonEncode($result));
        }elseif(Tools::isSubmit('getUnReadReceived')){
            $result = array(
                'error'=>1,
                'nbr'=>0
            );
            $nbr = gformrequestModel::getUnReadRequest();
            if($nbr > 0){
                $result = array(
                    'error'=>0,
                    'nbr'=>(int)$nbr
                );
            }
            die(Tools::jsonEncode($result));
	    }elseif(Tools::isSubmit('gfromViewedRequest')){
            $idrequest = (int)Tools::getValue('id_gformrequest');
            $requestObj = new gformrequestModel($idrequest);
            $result = array(
                'error'=>1,
                'warrning'=>$this->l('Error! Please try again')
            );
            if(Validate::isLoadedObject($requestObj)){
                $requestObj->viewed = 1;
                if($requestObj->save()){
                    $result = array(
                        'error'=>0,
                        'warrning'=>''
                    );
                }
            }
            die(Tools::jsonEncode($result));
	    }
        
        
        $changeStatus = Tools::getValue('changeStatus');
        if($changeStatus == '1'){
            $result = array(
                'success'=>0,
                'warrning'=>$this->l('Change status fail, please try again')
            );
            $idrequest = (int)Tools::getValue('id');
            $requestObj = new gformrequestModel($idrequest);
            if(Validate::isLoadedObject($requestObj)){
                $requestObj->status = (int)Tools::getValue('val');
                $requestObj->update();
                $result = array(
                    'success'=>1,
                    'warrning'=>$this->l('Change status successfull')
                );
            }
            echo Tools::jsonEncode($result);
            die();
        }
        $this->meta_title = $this->l('Forms Request');
        $this->deleted = false;
        $this->explicitSelect = true;
        $this->context = Context::getContext();
        $this->lang = false;
        $this->bootstrap = true;
        $this->_defaultOrderBy = 'id_gformrequest';
        $this->_defaultOrderWay = 'desc';
        $this->filter = false;
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
        $this->position_identifier = 'id_gformrequest';
        $this->addRowAction('view');
        $this->addRowAction('delete');
        $this->_select = ' fb.title as fbtitle,a.viewed ';
        $this->_join = ' LEFT JOIN `'._DB_PREFIX_.'gformbuilderpro` f ON (a.`id_gformbuilderpro` = f.`id_gformbuilderpro`) 
                        LEFT JOIN `'._DB_PREFIX_.'gformbuilderpro_lang` fb ON (a.`id_gformbuilderpro` = fb.`id_gformbuilderpro` AND fb.id_lang = '.(int)$this->context->language->id.') 
                        LEFT JOIN `'._DB_PREFIX_.'gformbuilderpro_shop` fc ON (a.`id_gformbuilderpro` = fc.`id_gformbuilderpro` AND fc.id_shop = '.(int)$this->context->shop->id.')';
        $titles_array = array();
        $forms = gformbuilderproModel::getAllBlock();        
        foreach ($forms as $form) {
            $titles_array[$form['id_gformbuilderpro']] = '#'.$form['id_gformbuilderpro'].':'.$form['title'];
        }
        $this->statuses_array = array(
            '0'=>$this->l('Submitted'),
            '1'=>$this->l('Pending'),
            '2'=>$this->l('Closed')
        );        
        $this->fields_list = array(
            'id_gformrequest' => array(
                'title' => $this->l('ID'),
                'type' => 'int',
                'width' => 'auto',
                'class' => 'fixed-width-xs',
                'orderby' => false),
            'star' => array(
                'title' => '',
                'type' => 'int',
                'width' => 'auto',
                'search' =>false,
                'orderby' => false,
                'callback' => 'printStar',
                'remove_onclick' => true
                ),
            'fbtitle' => array(
                'title' => $this->l('Form'),
                'filter_key' => 'a!id_gformbuilderpro',
                'type' => 'select',
                'list' => $titles_array,
                'filter_type' => 'int',
                'order_key' => 'fb!title'
            ),             
            'sender_name' => array(
                'title' => $this->l('Sender'),
                'type' => 'text',
                'width' => 'auto',
                'orderby' => false,
                'filter_key' => 'a!sender_name'),
            'subject' => array(
                'title' => $this->l('Mail Subject'),
                'type' => 'text',
                'width' => 'auto',
                'orderby' => false,
                'filter_key' => 'a!subject',
                'callback' => 'printSubject'
                ),
            'sendto' => array(
                'title' => $this->l('Sent To'),
                'type' => 'text',
                'width' => 'auto',
                'orderby' => false,
                'filter_key' => 'a!sendto'),
            'status' => array(
                'title' => $this->l('Status'),
                'type' => 'select',
                'color' => 'color',
                'list' => $this->statuses_array,
                'filter_key' => 'a!status',
                'filter_type' => 'int',
                'orderby' => false,
                'callback' => 'printStatus'
            ),
            'date_add' => array(
                'title' => $this->l('Date'),
                'type' => 'datetime',
                'width' => 'auto',
                'orderby' => false,
                'filter_key' => 'a!date_add'),
            'request' => array(
                'title' => $this->l('Quick view'),
                'width' => 'auto',
                'orderby' => false,
                'search' => false,
                'callback' => 'printRequest',
                'remove_onclick' => true
                ),  
            );
            
    }
    public function printStatus($status,$row){
        $row;
        return $this->statuses_array[(int)$status];
    }
    public function printStar($val,$row){
        $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/admin/extrahtml.tpl';
        Context::getContext()->smarty->assign(
            array(
                'action'=>'printStar',
                'star'=>(int)$val,
                'id_gformrequest'=>(int)$row['id_gformrequest']
            )
        );
        return Context::getContext()->smarty->fetch($tpl);
    }
    public function printSubject($val,$row){
        $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/admin/extrahtml.tpl';
        Context::getContext()->smarty->assign(
            array(
                'action'=>'printSubject',
                'viewed'=>(int)$row['viewed'],
                'subject'=>$val,
                'id_gformrequest'=>(int)$row['id_gformrequest']
            )
        );
        return Context::getContext()->smarty->fetch($tpl);
    }
    public function printRequest($val,$row){
        if($val == '') return '';
        else{
            $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/admin/extrahtml.tpl';
            Context::getContext()->smarty->assign(
                array(
                    'action'=>'printRequest',
                    'showrequest'=>1,
                    'subject'=>$row['subject'],
                    'request'=>$row['request'],
                    'id_gformrequest'=>(int)$row['id_gformrequest'],
                    'link_request'=>$this->context->link->getAdminLink('AdminGformrequest',true).'&id_gformrequest='.(int)$row['id_gformrequest'].'&reply=1&viewgformrequest=1',
                )
            );
            return Context::getContext()->smarty->fetch($tpl);
        }
    }
    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }
    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('Form');
        $this->toolbar_title[] = $this->l('Received Data');
    }
    public function renderView(){
        $isps17 = version_compare(_PS_VERSION_, '1.7', '>=');
        Context::getContext()->smarty->assign(array('isps17' => $isps17));
        $idrequest = (int)Tools::getValue('id_gformrequest');
        $extension = array('jpg','jpeg','gif','png');
        if($idrequest > 0){
            $requestObj = new gformrequestModel($idrequest);
            if($requestObj->viewed !=1){
                $requestObj->viewed = 1;
                $requestObj->save();
            }
            if(version_compare(_PS_VERSION_,'1.6') != -1)
                $this->initPageHeaderToolbar();
            $sender = ($requestObj->sender !='' && Validate::isEmail($requestObj->sender) ? $requestObj->sender : '');
            $user_email = ($requestObj->user_email !='' && Validate::isEmail($requestObj->user_email) ? $requestObj->user_email : '');
            
            $id_lang = $requestObj->id_lang;
            if($id_lang < 1){
                $id_lang = (int)$this->context->language->id;
            }else{
                $langObj = new Language($id_lang);
                if(!Validate::isLoadedObject($langObj)) $id_lang = (int)$this->context->language->id;
            }
            $this->tpl_view_vars = array(
                'id_gformbuilderpro'=>$requestObj->id_gformbuilderpro,
                'idrequest'=>$idrequest,
                'user_ip'=>$requestObj->user_ip,
                'subject'=>$requestObj->subject,
                'request'=>$requestObj->request,
                'date_add'=>$requestObj->date_add,
                'requestdownload'=>$this->context->link->getAdminLink('AdminGformrequest').'&download=',
                'backurl'=>$this->context->link->getAdminLink('AdminGformrequest'),
                'statuses_array'=>$this->statuses_array,
                'status'=>$requestObj->status,
                /* from version 1.2.0 */
                'sender'=> $sender,
                'user_email'=>$user_email,
                'gid_lang'=>(int)$id_lang,
            );
            
            $sql = 'SELECT * FROM '._DB_PREFIX_.'gformrequest_reply WHERE id_gformrequest = '.(int)$requestObj->id_gformrequest.' ORDER BY date_add ASC ';
            $replys = Db::getInstance()->executeS($sql);
            $this->tpl_view_vars['replys']= $replys;
            $formObj = new gformbuilderproModel((int)$requestObj->id_gformbuilderpro,(int)$id_lang);
            $subject = $formObj->replysubject;
            //$email_template = $formObj->replyemailtemplate;
            if($requestObj->jsonrequest !=''){
                $jsonrequest = Tools::jsonDecode($requestObj->jsonrequest,true);
                if($jsonrequest){
                    foreach($jsonrequest as $key=>$requestdata)
                    {
                        if($subject !='')
                            $subject = str_replace($key,$requestdata, $subject);
                    }
                }
            }           
            $this->tpl_view_vars['reply_subject'] = trim($subject);
            if($requestObj->attachfiles !=''){
                $attachfiles = explode(',',$requestObj->attachfiles);
                foreach($attachfiles as $file){
                    if($file !='' && file_exists(_PS_UPLOAD_DIR_.$file)){
                        if(in_array(Tools::strtolower(Tools::substr($file, -3)), $extension) || 
                            in_array(Tools::strtolower(Tools::substr($file, -4)), $extension)){
                             $this->tpl_view_vars['attachfiles'][] = array('isImage'=>true,'name'=>$file);   
                        }else{
                            $this->tpl_view_vars['attachfiles'][] = array('isImage'=>false,'name'=>$file);
                        }
                    }
                        
                }
            }
            $this->base_tpl_view = 'viewrequest.tpl';
        }
        return parent::renderView();
    }
}
?>