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
class GformbuilderproFormModuleFrontController extends ModuleFrontController{
    public function __construct()
	{
		$this->context = Context::getContext();
        if(version_compare(_PS_VERSION_,'1.6') ==-1){
            $this->display_column_left = false;
            $this->display_column_right = false;
        }elseif(version_compare(_PS_VERSION_,'1.7') ==-1){
            $this->display_column_left = Context::getContext()->theme->hasLeftColumn();
            $this->display_column_right = Context::getContext()->theme->hasRightColumn();
        }
		parent::__construct();

	}
    public function initContent()
    {
        parent::initContent();
        $tpl_path = '';
        $isps17 = version_compare(_PS_VERSION_, '1.7', '>=');
        Context::getContext()->smarty->assign(array('isps17' => $isps17));
        if($isps17){
            $tpl_path = '../../../modules/'.$this->module->name;
        }else{
            $tpl_path = _PS_MODULE_DIR_.$this->module->name;
        }
        $formtemplates_dir = 'formtemplates/';
        $useSSL = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? true : false;
        $protocol_content = ($useSSL) ? 'https://' : 'http://';
        $base_uri = $protocol_content.Tools::getHttpHost().__PS_BASE_URI__;
        $idform = (int)Tools::getValue('idform');
        $id_lang = (int)Tools::getValue('id_lang');
        $id_shop = (int)Tools::getValue('id_shop');
        $usingajax = (int)Tools::getValue('usingajax');
        $id_shop_group = (int)Shop::getContextShopGroupID();
        if($id_lang<=0){ $id_lang = (int)$this->context->language->id;}
        if($id_shop<=0){ $id_shop = (int)$this->context->shop->id;}
        if($idform<=0){ $idform = (int)Tools::getValue('id');}
        $use_routes = (bool)Configuration::get('PS_REWRITING_SETTINGS');
        if($use_routes && $idform<=0){
            $routers = $this->module->hookModuleRoutes();
            $form_router = $routers['module-gformbuilderpro-form'];
            $form_rule = $form_router['rule'];
            $rewrite = '';
            $REQUEST_URI = $_SERVER['REQUEST_URI']; /* get rewrite param from REQUEST_URI if missing id form */
            if($form_rule == '{rewrite}'){
                if ($pos_get = Tools::strpos($REQUEST_URI, '?')) $REQUEST_URI = Tools::substr($REQUEST_URI, 0, $pos_get);
                $uri_params = explode('/',$REQUEST_URI);
                $rewrite = end($uri_params);
            }else {
                $form_rule = str_replace('/', '\\/', $form_rule);
                foreach ($form_router['keywords'] as $key => $keyword) {
                    $form_rule = str_replace('{' . $key . '}', '(' . $keyword['regexp'] . ')', $form_rule);
                }
                $url_array = array();
                $form_pattern = '/.*?' . $form_rule . '/'; // '/.*?form\/([_a-zA-Z0-9-\pL]*)/';
                preg_match($form_pattern, $REQUEST_URI , $url_array);
                if (isset($url_array[1]) && $url_array[1] != '')  $rewrite = $url_array[1];
            }
            if($rewrite !=''){
                 /* get form from form_rewrite */
                 $sql = 'SELECT id_gformbuilderpro
                        FROM `' . _DB_PREFIX_ . 'gformbuilderpro_lang`
                        WHERE rewrite = "'.pSql($rewrite).'" AND id_lang = '.(int)$id_lang;
                 $idform = (int)Db::getInstance()->getValue($sql);
            }
        }
        $blackip = (bool)$this->isInBlacklistIp($id_shop_group,$id_shop);
        $pagenotfound = Configuration::get('GF_REDIRECT_TO_URL', (int)$id_lang, $id_shop_group, $id_shop);
        if($blackip){
            if($usingajax){
                Context::getContext()->smarty->assign(array(
        	        '_errors' => array($this->l('You are blocked.','form')),
                ));
                $tpl = $tpl_path.'/views/templates/front/thankyou.tpl';
                if(!$isps17) $tpl = $tpl_path.'/views/templates/front/thankyou_ps16.tpl';
                $thankyou = Context::getContext()->smarty->fetch($tpl);
                $results = array(
                            'errors'=>'1',
                            'thankyou'=>$thankyou
                        );
                die(Tools::jsonEncode($results));
            }else{

                if($pagenotfound !='')
                    Tools::redirect($pagenotfound);
                else
                    Tools::redirect('index.php?controller=pagenotfound');
                exit;
            }
        }else{
            if($idform > 0){
                Context::getContext()->smarty->assign(array(
        	        'baseUri' => $base_uri
                ));
                $formObj = new gformbuilderproModel((int)$idform,(int)$id_lang,(int)$id_shop);
                if($formObj->active){

                    if((bool)$formObj->requiredlogin && !$this->context->customer->isLogged()){
                        if($usingajax){
                            Context::getContext()->smarty->assign(array(
                    	        '_errors' => array($this->l('You must login to access forms','form'))
                            ));
                            $tpl = $tpl_path.'/views/templates/front/thankyou_codehook.tpl';
                            if(!$isps17) $tpl = $tpl_path.'/views/templates/front/thankyou_ps16.tpl';
                            $thankyou = Context::getContext()->smarty->fetch($tpl);
                            $results = array(
                                        'errors'=>'1',
                                        'thankyou'=>$thankyou,
                                    );
                            die(Tools::jsonEncode($results));
                        }else{
                            Tools::redirect('index.php?controller=authentication?back=my-account');
                            exit;
                        }
                    }else{
                        $sendemail = false;
                        $relaceurlcart = false;
                        $params = array();
                        $params['{shop_name}'] = Tools::safeOutput(Configuration::get('PS_SHOP_NAME', null, null, $id_shop));
			            $params['{shop_url}'] = Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id);
                        $params['%7Bshop_url%7D'] = $params['{shop_url}'];
                        $params['%7Bshop_name%7D'] = $params['{shop_name}'];
                        $notCheckfields = array();
                        if (Tools::getValue('ConditionsHide')) {
                            $notCheckfields = explode(',', Tools::getValue('ConditionsHide') );
                        }
                        if(Tools::isSubmit('gSubmitForm')){
                            $formok = true;
                            if($formObj->id_gformbuilderpro > 0 && $formObj->active){
                                $fields = trim($formObj->fields);
                                if($fields !=''){
                                    $fieldsData = gformbuilderprofieldsModel::getAllFields($fields,$id_lang,$id_shop);
                                    $_errors = array();
                                    $attachfiles = array();
                                    $attachfiles_name = array();
                                    /* check captcha first */
                                    if($fieldsData){
                                        foreach($fieldsData as $fieldData){
                                            if($fieldData['type'] =='captcha' && $formok && !in_array($fieldData['id_gformbuilderprofields'], $notCheckfields)){
                                                $id_shop_group = (int)Shop::getContextShopGroupID();
                                                $secret = Configuration::get('GF_SECRET_KEY', null, $id_shop_group, $id_shop);
                                                $using_v3= (int)Configuration::get('GF_RECAPTCHA_V3', null, $id_shop_group, $id_shop);
                                                if($using_v3){
                                                    $grecaptcharesponse = Tools::getValue('recaptcha_response');
                                                    // Make and decode POST request:
                                                    $recaptcha_datas = Tools::file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $grecaptcharesponse);
                                                    $recaptcha = Tools::jsonDecode($recaptcha_datas,true);
                                                    if (isset($recaptcha['score']) && $recaptcha['score'] >= 0.5) {
                                                        // Verified
                                                    } else {
                                                        $formok = false;
                                                        $_errors[] = $this->l('Robot verification failed, please try again.','form');
                                                    }
                                                }else{
                                                    $grecaptcharesponse = Tools::getValue('g-recaptcha-response');
                                                    if(!empty($grecaptcharesponse)){
                                                        $verifyResponse = Tools::file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$grecaptcharesponse);
                                                        $responseData = Tools::jsonDecode($verifyResponse);
                                                        if(is_object($responseData)){
                                                            if(!$responseData->success){
                                                                $formok = false;
                                                                $_errors[] = $this->l('Robot verification failed, please try again.','form');
                                                            }
                                                        }elseif(is_array($responseData))
                                                            if(!$responseData["success"]){
                                                                $formok = false;
                                                                $_errors[] = $this->l('Robot verification failed, please try again.','form');
                                                            }
                                                    }else{
                                                        $formok = false;
                                                        $_errors[] = $this->l('Please click on the reCAPTCHA box.','form');
                                                    }
                                                }
                                            }
                                            if($fieldData['type'] == 'privacy' && $formok  && !in_array($fieldData['id_gformbuilderprofields'], $notCheckfields)){
                                                $field_data = Tools::getValue($fieldData['name']);
                                                if($field_data != 1){
                                                    $formok = false;
                                                    $_errors[] = $this->l('Privacy field is required, please try again.','form');
                                                }
                                            }
                                        }
                                    }
                                    if($fieldsData && $formok){
                                        foreach($fieldsData as $fieldData){
                                            if($fieldData['type'] !='captcha' && $fieldData['type'] !='submit' && $fieldData['type'] !='html' && $fieldData['type'] !='privacy'){
                                                $field_data = Tools::getValue($fieldData['name']);
                                                if (in_array($fieldData['id_gformbuilderprofields'], $notCheckfields) ) {
                                                    $field_data = '';
                                                }
                                                if (!in_array($fieldData['id_gformbuilderprofields'], $notCheckfields))
                                                    $formok &= (bool)$this->validateForm($fieldData,$field_data,$_errors,$id_lang,$id_shop);
                                                if($formok){
                                                    if($fieldData['type'] =='select' || $fieldData['type'] =='colorchoose' || $fieldData['type'] =='checkbox' || $fieldData['type'] =='selectcountry')
                                                    {
                                                        $params['{'.$fieldData['name'].'}'] = '';
                                                        if(is_array($field_data) && !empty($field_data)){
                                                            $params['{'.$fieldData['name'].'}'] = implode(',',$field_data);
                                                        }else{
                                                            $params['{'.$fieldData['name'].'}'] = $field_data;
                                                        }

                                                    }
                                                    elseif($fieldData['type'] =='imagethumb'){
                                                        $params['{'.$fieldData['name'].'}'] = '';
                                                        if(is_array($field_data) && !empty($field_data)){
                                                            $thumbs = array();
                                                            foreach($field_data as $thumb_data){
                                                                if($thumb_data !=''){
                                                                    $thumb = '<img style="max-width:120px;" src="'.$base_uri.'modules/gformbuilderpro/views/img/thumbs/'.$thumb_data.'" alt="'.$thumb_data.'">';
                                                                    $thumbs[] = $thumb;
                                                                }
                                                            }
                                                            $params['{'.$fieldData['name'].'}'] = implode(' ',$thumbs);
                                                        }elseif($field_data !=''){

                                                            $thumb = '<img style="max-width:120px;" src="'.$base_uri.'modules/gformbuilderpro/views/img/thumbs/'.$field_data.'" alt="'.$field_data.'">';
                                                            $params['{'.$fieldData['name'].'}'] = $thumb;
                                                        }
                                                    }
                                                    elseif($fieldData['type'] =='hidden' && isset($fieldData['extra']) && $fieldData['extra'] == 'productatt'){
                                                        $params['{'.$fieldData['name'].'}'] = '';
                                                        $productatt = explode('-',$field_data);
                                                        $id_product = 0;$id_att = 0;
                                                        if(isset($productatt[0])) $id_product = (int)$productatt[0];
                                                        if(isset($productatt[1])) $id_att = (int)$productatt[1];
                                                        if($id_product > 0){
                                                            $params['{'.$fieldData['name'].'}'] =Product::getProductName((int)$id_product,(int)$id_att,(int)$id_lang);
                                                        }
                                                    }
                                                    elseif($fieldData['type'] =='product')
                                                    {

                                                        $params['{'.$fieldData['name'].'}'] = '';
                                                        if(is_array($field_data) && !empty($field_data)){
                                                            $product_chooses = array();
                                                            foreach($field_data as $productid){
                                                                if($productid > 0){
                                                                    $product_name=Product::getProductName((int)$productid,null,(int)$id_lang);
                                                                    $product_link=$this->context->link->getProductLink($productid,null,null,null,(int)$id_lang,(int)$id_shop);
                                                                    $product_chooses[] = '<a href="'.$product_link.'" >'.$product_name.'</a>';
                                                                }
                                                            }
                                                            $params['{'.$fieldData['name'].'}'] = implode(',',$product_chooses);
                                                        }else{
                                                            if($field_data > 0){
                                                                $product_name=Product::getProductName((int)$field_data,null,(int)$id_lang);
                                                                $product_link=$this->context->link->getProductLink($field_data,null,null,null,(int)$id_lang,(int)$id_shop);
                                                                $product_choose = '<a href="'.$product_link.'" >'.$product_name.'</a>';
                                                                $params['{'.$fieldData['name'].'}'] = $product_choose;
                                                            }
                                                        }

                                                    }elseif($fieldData['type'] == 'survey')
                                                    {
                                                        $surceyObj = new gformbuilderprofieldsModel($fieldData['id_gformbuilderprofields'],$id_lang,$id_shop);
                                                        $surcey_vals =   explode(',',$surceyObj->value);
                                                        $surcey_data = array();
                                                        foreach($surcey_vals as $key=>$surcey)
                                                            $surcey_data[] = $surcey.':'.(isset($field_data[$key]) ? $field_data[$key] : '');
                                                        $params['{'.$fieldData['name'].'}'] = implode(',',$surcey_data);
                                                    } elseif ($fieldData['type'] == 'wholesale') {
                                                        $params['{'.$fieldData['name'].'}'] = '';
                                                        
                                                        if ((int)Tools::getValue($fieldData['name']) > 0) {
                                                            $id_cart  = $this->context->cart->id;
                                                            $products = $this->context->cart->getProducts(true);
                                                            if ($fieldData['extra_option']) {
                                                                $relaceurlcart = true;
                                                            }
                                                            foreach ($products as &$product) {
                                                                $product['price'] = Tools::displayPrice(Tools::convertPriceFull($product['price']));
                                                                $product['total_wt'] = Tools::displayPrice(Tools::convertPriceFull($product['total_wt']));
                                                                $product['total'] = Tools::displayPrice(Tools::convertPriceFull($product['total']));
                                                            }
                                                            Context::getContext()->smarty->assign(array(
                                                                'gproducts' => $products,
                                                                'links'     => $this->context->link,
                                                                'id_lang'   => $id_lang,
                                                                'protocol'  =>  Tools::getShopProtocol(),
                                                                'total'     =>  Cart::getTotalCart($id_cart, true)
                                                            ));
                                                            $tpl = $tpl_path.'/views/templates/front/wholesale.tpl';
                                                            $params['{'.$fieldData['name'].'}'] = Context::getContext()->smarty->fetch($tpl);
                                                        }
                                                    }elseif($fieldData['type'] == 'input'){
                                                        // register newletter
                                                        if($fieldData['extra'] == 1){
                                                            $this->newsletterAssign($field_data);
                                                        }
                                                        // #register newletter
                                                        $params['{'.$fieldData['name'].'}'] = $field_data;
                                                    }
                                                    elseif($fieldData['type'] != 'fileupload') $params['{'.$fieldData['name'].'}'] = $field_data;
                                                }
                                            }
                                        }
                                    }
                                    $params['{date_add}'] = date("Y-m-d H:i:s");
                                    $params['{user_ip}'] = Tools::getRemoteAddr();
                                    if($formok){
                                        $sender_attachfiles = explode(',',$formObj->sender_attachfiles);
                                        $admin_attachfiles = explode(',',$formObj->admin_attachfiles);
                                        $adminattachfiles = array();
                                        $senderattachfiles = array();
                                        //upload file after form is validate ok
                                        foreach($fieldsData as $fieldData){
                                            if($formok && $fieldData['type'] =='fileupload'){
                                                $file_attachments = array();
                                        		$params['{'.$fieldData['name'].'}'] = '';
                                                $multi = (bool)$fieldData['multi'];
                                                if (!in_array($fieldData['id_gformbuilderprofields'], $notCheckfields))
                                                    if($multi){
                                                        if (isset($_FILES[$fieldData['name']]['name']) && !empty($_FILES[$fieldData['name']]['name']) && !empty($_FILES[$fieldData['name']]['tmp_name']))
                                                        {
                                                            foreach(array_keys($_FILES[$fieldData['name']]['name']) as $key){
                                                                if($_FILES[$fieldData['name']]['name'][$key]){
                                                                    $file_attachment = array();
                                                                    $file_attachment['rename'] = uniqid(). Tools::strtolower(Tools::substr($_FILES[$fieldData['name']]['name'][$key], -5));
                                                                    $file_attachment['content'] = Tools::file_get_contents($_FILES[$fieldData['name']]['tmp_name'][$key]);
                                                                    $file_attachment['tmp_name'] = $_FILES[$fieldData['name']]['tmp_name'][$key];
                                                                    $file_attachment['name'] = $_FILES[$fieldData['name']]['name'][$key];
                                                                    $file_attachment['mime'] = $_FILES[$fieldData['name']]['type'][$key];
                                                                    $file_attachment['error'] = $_FILES[$fieldData['name']]['error'][$key];
                                                                    if (isset($file_attachment['rename']) && !empty($file_attachment['rename']) && rename($file_attachment['tmp_name'], _PS_UPLOAD_DIR_.basename($file_attachment['rename']))) {
                                                                        $file_attachments[]= $file_attachment['rename'];
                                                                        @chmod(_PS_UPLOAD_DIR_.basename($file_attachment['rename']), 0664);
                                                                        $file_attachment['name'] = $file_attachment['rename'];
                                                                        $attachfiles[] = $file_attachment;
                                                                        if(in_array($fieldData['name'],$admin_attachfiles)) $adminattachfiles[] = $file_attachment;
                                                                        if(in_array($fieldData['name'],$sender_attachfiles)) $senderattachfiles[] = $file_attachment;
                                                                        $attachfiles_name[] = $file_attachment['name'];
                                                                    }else{
                                                                        $_errors[] = $fieldData['label'].' '.$this->l(' An error occurred during the file-upload process.','form');
                                                                        $formok = false;
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        if($file_attachments)
                                                            $params['{'.$fieldData['name'].'}'] = implode(',',$file_attachments);
                                                    }else{
                                                        if (isset($_FILES[$fieldData['name']]['name']) && !empty($_FILES[$fieldData['name']]['name']) && !empty($_FILES[$fieldData['name']]['tmp_name']))
                                                        {
                                                            $file_attachment = array();
                                                            $file_attachment['rename'] = uniqid(). Tools::strtolower(Tools::substr($_FILES[$fieldData['name']]['name'], -5));
                                                            $file_attachment['content'] = Tools::file_get_contents($_FILES[$fieldData['name']]['tmp_name']);
                                                            $file_attachment['tmp_name'] = $_FILES[$fieldData['name']]['tmp_name'];
                                                            $file_attachment['name'] = $_FILES[$fieldData['name']]['name'];
                                                            $file_attachment['mime'] = $_FILES[$fieldData['name']]['type'];
                                                            $file_attachment['error'] = $_FILES[$fieldData['name']]['error'];
                                                            if (isset($file_attachment['rename']) && !empty($file_attachment['rename']) && rename($file_attachment['tmp_name'], _PS_UPLOAD_DIR_.basename($file_attachment['rename']))) {
                                                                $params['{'.$fieldData['name'].'}'] = $file_attachment['rename'];
                                                                @chmod(_PS_UPLOAD_DIR_.basename($file_attachment['rename']), 0664);
                                                                $file_attachment['name'] = $file_attachment['rename'];
                                                                $attachfiles[] = $file_attachment;
                                                                if(in_array($fieldData['name'],$admin_attachfiles)) $adminattachfiles[] = $file_attachment;
                                                                if(in_array($fieldData['name'],$sender_attachfiles)) $senderattachfiles[] = $file_attachment;
                                                                $attachfiles_name[] = $file_attachment['name'];
                                                            }else{
                                                                $_errors[] = $fieldData['label'].' '.$this->l(' An error occurred during the file-upload process.','form');
                                                                $formok = false;
                                                                break;
                                                            }
                                                        }
                                                    }
                                            }
                                        }
                                        //#upload file after is validate ok
                                    }
                                    $sender_name = '';
                                    if($formok){
                                        $params['{form_title}'] = $formObj->title;
                                        $params['{shop_email}'] = Configuration::get('PS_SHOP_EMAIL');
                                        $params['{shop_address}'] = Configuration::get('PS_SHOP_ADDR1').' '.Configuration::get('PS_SHOP_ADDR2');
                                        $params['{shop_phone}'] = Configuration::get('PS_SHOP_PHONE');
                                        $params['{shop_fax}'] = Configuration::get('PS_SHOP_FAX');

                                        $sendemail = true;
                                        $reply_to = null;
                                        $sender = $formObj->sender;
                                        $sender_name = $formObj->sender_name;
                                        if($sender_name !=''){
                                            if($params){
                                                foreach($params as $key=>$value){
                                                    if(is_array($value)) $value = implode(',',$value);
                                                    $sender_name = str_replace($key,$value,$sender_name);
                                                }
                                            }
                                            $params['{sender_name}'] = $sender_name;
                                        }
                                        if($sender!=''){
                                            if(!$senderattachfiles) $senderattachfiles = null;
                                            $sendersubject = $formObj->subjectsender;
                                            if($params){
                                                foreach($params as $key=>$value){
                                                    if(is_array($value)) $value = implode(',',$value);
                                                    $sender = str_replace($key,$value,$sender);
                                                    $sendersubject = str_replace($key,$value,$sendersubject);
                                                }
                                            }
                                            if($sender !='' && Validate::isEmail(trim($sender))){
                                                $reply_to = $sender;
                                                if($formObj->sendtosender){
                                                    try{
                                                        $params['{sender_email}'] = $sender;
                                                        
                                                        $this->sendEmail($params,$formObj->id_gformbuilderpro,trim($sender),$sendersubject,$id_lang,$id_shop,$senderattachfiles,true);
                                                    } catch (Exception $e) {}
                                                }
                                            }
                                        }
                                        if(($reply_to == null || $reply_to == '') && isset($this->context->customer) && $this->context->customer->isLogged()){
                                            if($this->context->customer->email !='' && Validate::isEmail($this->context->customer->email)){
                                                $reply_to = $this->context->customer->email;
                                            }
                                        }
                                        $subject = $formObj->subject;
                                        if($params){
                                            foreach($params as $key=>$value){
                                                if(is_array($value)) $value = implode(',',$value);
                                                $subject = str_replace($key,$value,$subject);
                                            }
                                        }
                                        $sendto = explode(',',$formObj->sendto);
                                        $to_emails = array();
                                        if($sendto)
                                            foreach($sendto as $toemail){
                                                $toemail = trim($toemail);
                                                if($toemail !='' && Validate::isEmail($toemail))
                                                {
                                                    $to_emails[$toemail] = $toemail;
                                                }
                                            }
                                        /* From 1.2.2 */
                                        if(isset($formObj->using_condition) && $formObj->using_condition){
                                            if(isset($formObj->condition_configs) && $formObj->condition_configs !=''){

                                                $condition_configs = $this->module->htmlEntityDecode(Tools::jsonDecode($formObj->condition_configs,true));
                                                if($condition_configs && is_array($condition_configs)){
                                                    foreach($condition_configs as $condition_config){

                                                        if(isset($condition_config['if']) && trim($condition_config['if']) !='' && isset($condition_config['state']) && $condition_config['state'] > 0)
                                                        {
                                                            $if_variable = trim($condition_config['if']);
                                                            if(isset($params[$if_variable])){
                                                                /* check data */
                                                                if(isset($condition_config['value']) && isset($condition_config['value'][(int)$id_lang])){
                                                                    $value_compare = $condition_config['value'][(int)$id_lang];
                                                                    $value_compares = array_map('trim',array_filter(explode(';',$value_compare))); /* split multi datas by ; */
                                                                    $param_compare_data = trim($params[$if_variable]);
                                                                    $is_match = false;
                                                                    switch ($condition_config['state']) {
                                                                       case 1:
                                                                            if(in_array($param_compare_data,$value_compares)){
                                                                                $is_match = true;
                                                                            }
                                                                            break;
                                                                       case 2:
                                                                            if(!in_array($param_compare_data,$value_compares)){
                                                                                $is_match = true;
                                                                            }
                                                                            break;
                                                                       case 3:
                                                                            /* greater than */
                                                                            $greater_than = true;
                                                                            foreach($value_compares as $value_compare){
                                                                                if($value_compare >= $param_compare_data){
                                                                                    $greater_than = false;
                                                                                    break;
                                                                                }
                                                                            }
                                                                            $is_match = $greater_than;
                                                                            break;
                                                                       case 4:
                                                                            /* less than */
                                                                            $less_than = true;
                                                                            foreach($value_compares as $value_compare){
                                                                                if($value_compare <= $param_compare_data){
                                                                                    $less_than = false;
                                                                                    break;
                                                                                }
                                                                            }
                                                                            $is_match = $less_than;
                                                                            break;
                                                                    }
                                                                    if($is_match)
                                                                        if(isset($condition_config['email']) && trim($condition_config['email']) !=''){
                                                                            $c_emails = array_map('trim',array_filter(explode(';',$condition_config['email']))); /* split multi datas by ; */
                                                                            if($c_emails)
                                                                                foreach($c_emails as $c_email){
                                                                                    if($c_email !='' && Validate::isEmail($c_email)){
                                                                                        $to_emails[$c_email] = $c_email;
                                                                                    }
                                                                                }
                                                                        }
                                                                }

                                                            }
                                                        }
                                                    }
                                                }

                                            }
                                        }
                                        /* #From 1.2.2 */
                                        if($to_emails){
                                            if(!$adminattachfiles) $adminattachfiles = null;
                                            foreach($to_emails as $email){
                                                try{
                                                    $this->sendEmail($params,$formObj->id_gformbuilderpro,trim($email),$subject,$id_lang,$id_shop,$adminattachfiles,false,$reply_to);
                                                } catch (Exception $e) {
                                                    $sendemail = false;
                                                }
                                            }
                                        }
                                        if($formObj->saveemail){
                                            $requestObj = new gformrequestModel(null,null,$id_shop);
                                            $requestObj->id_gformbuilderpro = $formObj->id_gformbuilderpro;
                                            $requestObj->user_ip = $params['{user_ip}'];
                                            $requestObj->subject = $subject;
                                            $requestObj->request =$this->getEmailSendData($formObj->id_gformbuilderpro,$id_lang,$id_shop,$params);
                                            $requestObj->sendto = implode(';',$to_emails);//$formObj->sendto;
                                            $requestObj->attachfiles = implode(',',$attachfiles_name);
                                            $requestObj->jsonrequest = Tools::jsonEncode($params);
                                            $requestObj->date_add = $params['{date_add}'];
                                            $requestObj->status = 0;
                                            /* From version 1.2.0 */
                                            $requestObj->sender = $reply_to;
                                            $requestObj->id_lang = (int)$this->context->language->id;
                                            if(isset($params['{sender_name}']) && $params['{sender_name}'] !='') $requestObj->sender_name = $params['{sender_name}'];

                                            if(isset($formObj->autostar) && $formObj->autostar){
                                                $requestObj->star = 1;
                                            }

                                            $customeremail = '';
                                            if(isset($this->context->customer) && Validate::isLoadedObject($this->context->customer) && $this->context->customer->isLogged()){
                                                $customeremail = $this->context->customer->email;
                                            }
                                            $requestObj->user_email = $customeremail;
                                            $requestObj->save();
                                        }

                                        /** 03/06/2020 */
                                        $this->integrationData($formObj,$fieldsData,$params);
                                    }elseif($_errors)
                                        if($usingajax){

                                            Context::getContext()->smarty->assign(array(
                                    	        '_errors' => $_errors
                                            ));
                                            $tpl = $tpl_path.'/views/templates/front/thankyou_codehook.tpl';
                                            if(!$isps17) $tpl = $tpl_path.'/views/templates/front/thankyou_ps16.tpl';
                                            $thankyou = Context::getContext()->smarty->fetch($tpl);
                                            $results = array(
                                                'errors'=>'1',
                                                'thankyou'=>$thankyou
                                            );
                                            die(Tools::jsonEncode($results));
                                        }else{
                                            $this->context->smarty->assign(array(
                                    	        '_errors' => $_errors,
                                            ));
                                        }
                                }
                                if($usingajax){
                                    if($sendemail){
                                        Context::getContext()->smarty->assign(array(
                                	        'success_message' => $this->getThankyouData($formObj->success_message,$params),
                                            'errors'=>'0'
                                        ));
                                    }else{
                                        Context::getContext()->smarty->assign(array(
                                	        'success_message' => $this->getThankyouData($formObj->error_message,$params),
                                            'errors'=>'1'
                                        ));
                                    }
                                    $tpl = $tpl_path.'/views/templates/front/thankyou_codehook.tpl';
                                    if(!$isps17) $tpl = $tpl_path.'/views/templates/front/thankyou_ps16.tpl';
                                    $thankyou = Context::getContext()->smarty->fetch($tpl);
                                    $redirect_link = trim($formObj->redirect_link);
                                    $getshopprotocol = trim(Tools::getShopProtocol(), '/');
                                    if(isset($formObj->redirect_link_lang) && trim($formObj->redirect_link_lang) !=''){
                                        $redirect_link = trim($formObj->redirect_link_lang);
                                    }
                                    if ($relaceurlcart) {
                                        $redirect_link = $this->context->link->getPageLink('order', null, $id_lang, "step=1", false, $id_shop);
                                        $formObj->autoredirect = 1;
                                        $formObj->timedelay    = 1;
                                    }

                                    $results = array(
                                        'errors'=>($sendemail ? '0' : '1'),
                                        'thankyou'=>$thankyou,
                                        'autoredirect'=>(bool)$formObj->autoredirect,
                                        'timedelay'=>(int)$formObj->timedelay,
                                        'redirect_link'=>$redirect_link,
                                    );
                                    die(Tools::jsonEncode($results));
                                }else{
                                    if($sendemail){
                                        $redirect_link = trim($formObj->redirect_link);
                                        $getshopprotocol = trim(Tools::getShopProtocol(), '/');
                                        if ($relaceurlcart) {
                                            $redirect_link = $this->context->link->getPageLink('order', null, $id_lang, "step=1", false, $id_shop);
                                            $formObj->autoredirect = 1;
                                            $formObj->timedelay    = 1;
                                        }
                                        if(isset($formObj->redirect_link_lang) &&  trim($formObj->redirect_link_lang) !=''){
                                            $redirect_link = trim($formObj->redirect_link_lang);
                                        }
                                        $this->context->smarty->assign(array(
                                	        'success_message' => $this->getThankyouData($formObj->success_message,$params),
                                            'autoredirect'=>(bool)$formObj->autoredirect,
                                            'timedelay'=>(int)$formObj->timedelay,
                                            'redirect_link'=>$redirect_link,
                                        ));
                                    }else{
                                        $this->context->smarty->assign(array(
                                	        'success_message' => $this->getThankyouData($formObj->error_message,$params),
                                        ));
                                    }
                                    $tpl = $tpl_path.'/views/templates/front/thankyou.tpl';
                                    if(!$isps17) $tpl = 'thankyou_ps16.tpl';
                                    $this->setTemplate($tpl);

                                }
                            }else{
                                if($usingajax){
                                    Context::getContext()->smarty->assign(array(
                            	        '_errors' => array($this->l('Form not found','form'))
                                    ));
                                    $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/front/thankyou_codehook.tpl';
                                    if(!$isps17) $tpl = $tpl_path.'/views/templates/front/thankyou_ps16.tpl';
                                    $thankyou = Context::getContext()->smarty->fetch($tpl);
                                    $results = array(
                                                'errors'=>'1',
                                                'thankyou'=>$thankyou
                                            );
                                    die(Tools::jsonEncode($results));
                                }else{
                                    if($pagenotfound !='')
                                        Tools::redirect($pagenotfound);
                                    else {
                                        $this->context->smarty->assign(array('path' => $this->l('Form not found', 'form')));
                                        if (!$isps17)
                                            $this->setTemplate('formtemplates/notfound_ps16.tpl');
                                        else
                                            $this->setTemplate($tpl_path . '/views/templates/front/' . $formtemplates_dir . 'notfound.tpl');
                                    }
                                }
                            }
                        } 
                        elseif (Tools::isSubmit('getPriceWhosaleProduct')) {
                            $productPrices = array();
                            $results    = array();
                            $totalprice = 0;
                            $id_field = Tools::getValue('id_field');
                            $wholesaleboxProducts = Tools::getValue('wholesaleboxProduct');
                            $wholesaleboxProductQtys = Tools::getValue('wholesaleboxProductQty');
                            $wholesaleboxProductDiscount = Tools::getValue('wholesaleboxProductDiscount');
                            if ($id_field > 0 && $wholesaleboxProducts && $wholesaleboxProductQtys) {
                                $fieldObj = new gformbuilderprofieldsModel((int)$id_field, $id_lang, $id_shop);$fieldObj;
                                if ($wholesaleboxProducts) {
                                    foreach ($wholesaleboxProducts as $id_product => $id_combinations)  {
                                        $combinations = array();
                                        if ($id_combinations)  {
                                            foreach ($id_combinations as $id_combination) {
                                                $priceDisplay   = Product::getTaxCalculationMethod((int)$this->context->cookie->id_customer);
                                                $qty            = (int)$wholesaleboxProductQtys[$id_product][$id_combination];
                                                $check_discount = 0;
                                                if(!$priceDisplay || $priceDisplay == 2) {
                                                    $price_combin = Product::getPriceStatic((int)$id_product, true, $id_combination, 6, null, false, true, $qty) * (int)$qty;
                                                    $totalprice_dc = 0;
                                                    if (isset($wholesaleboxProductDiscount[$id_product]) && $wholesaleboxProductDiscount[$id_product]['value'] != 0) {
                                                        if ((int)$wholesaleboxProductDiscount[$id_product]['type'] == 1) {
                                                            $pricediscount = Tools::convertPriceFull((float)$wholesaleboxProductDiscount[$id_product]['value']);
                                                            $totalprice_dc += $price_combin - $pricediscount;
                                                        } else {
                                                            $totalprice_dc += $price_combin - ($price_combin * ((float)$wholesaleboxProductDiscount[$id_product]['value'] / 100));
                                                        }
                                                        $check_discount = 1;
                                                    } else {
                                                        $totalprice_dc += $price_combin;
                                                    }
                                                    $totalprice    += $totalprice_dc;
                                                    $combinations[$id_combination]['old_price'] =  Tools::displayPrice(Tools::convertPriceFull($price_combin));
                                                    $combinations[$id_combination]['price']     =  Tools::displayPrice(Tools::convertPriceFull($totalprice_dc));
                                                    $combinations[$id_combination]['check_discount']     =  $check_discount;
                                                } elseif($priceDisplay == 1) {
                                                    $price_combin = Product::getPriceStatic((int)$id_product, false, $id_combination, 6, null, false, true, (int)$qty) * (int)$qty;
                                                    $totalprice_dc = 0;
                                                    if (isset($wholesaleboxProductDiscount[$id_product]) && $wholesaleboxProductDiscount[$id_product]['value'] != 0) {
                                                        if ((int)$wholesaleboxProductDiscount[$id_product]['type'] == 1) {
                                                            $pricediscount = Tools::convertPriceFull((float)$wholesaleboxProductDiscount[$id_product]['value']);
                                                            $totalprice_dc += $price_combin - $pricediscount;
                                                        } else {
                                                            $totalprice_dc += $price_combin - ($price_combin * ((float)$wholesaleboxProductDiscount[$id_product]['value'] / 100));
                                                        }
                                                        $check_discount = 1;
                                                    } else {
                                                        $totalprice_dc += $price_combin;
                                                    }
                                                    $totalprice    += $totalprice_dc;
                                                    $combinations[$id_combination]['price']     =  Tools::displayPrice(Tools::convertPriceFull($price_combin));
                                                    $combinations[$id_combination]['old_price'] =  Tools::displayPrice(Tools::convertPriceFull($totalprice_dc));
                                                    $combinations[$id_combination]['check_discount']     =  $check_discount;
                                                }
                                            }
                                            $productPrices[$id_product] =  $combinations;
                                        }
                                    }
                                }
                                $results = array(
                                    'products'   => $productPrices,
                                    'totalprice' => Tools::displayPrice(Tools::convertPriceFull($totalprice)),
                                    'id_field'   => (int)$id_field,
                                );

                            } else {
                                $results = array(
                                    'products'   => array(),
                                    'totalprice' => Tools::displayPrice(Tools::convertPriceFull(0)),
                                    'id_field'   => (int)$id_field,
                                );
                            }
                            die(Tools::jsonEncode($results));
                        } elseif (Tools::isSubmit('deleteCartModule')) {
                            $res = true;
                            if (validate::isLoadedObject($this->context->cart)) {
                                $productCarts = $this->context->cart->getProducts(true);
                                if ($productCarts) {
                                    foreach ($productCarts as $productCart) {
                                        $res &= $this->context->cart->deleteProduct((int)$productCart['id_product'], (int)$productCart['id_product_attribute']);
                                    }
                                }
                            }
                            die($res);
                        }
                        else{
                            $template = $formtemplates_dir.$idform.'/'.$id_lang.'/'.$id_shop.'_form.tpl';
                            if(!file_exists(_PS_MODULE_DIR_.'gformbuilderpro/views/templates/front/'.$template)){

                                $formObj->parseTpl((int)$id_lang,(int)$id_shop);
                            }
                            if(file_exists(_PS_MODULE_DIR_.'gformbuilderpro/views/templates/front/'.$template)){

                                /* from version 1.3.0 */
                                $ip_address = Tools::getRemoteAddr();
                                $id_customer = ((isset(Context::getContext()->customer) && Validate::isLoadedObject(Context::getContext()->customer)) ? (int)Context::getContext()->customer->id : 0);
                                $this->module->setBrowserAnalytics((int)$idform,$ip_address,(int)$id_customer,(int)$id_shop);
                                /* # from version 1.3.0 */

                                $id_shop_group = (int)Shop::getContextShopGroupID();
                                $url_rewrite = Context::getContext()->link->getModuleLink('gformbuilderpro','form',array('id'=>$idform,'rewrite'=>$formObj->rewrite));
                                if (!strpos($url_rewrite, 'index.php')){
                                    $url_rewrite = str_replace('?module=gformbuilderpro&controller=form','',$url_rewrite);
                                }
                                $meta_keywords = array();
                                $main_keyword = Configuration::get('GF_FORM_METAKEYWORDS', (int)$id_lang, $id_shop_group, $id_shop);
                                if($main_keyword !=''){
                                    $meta_keywords = explode(',',$main_keyword);
                                }
                                $form_metakeyword =  $formObj->metakeywords;
                                if($form_metakeyword !=''){
                                    $form_metakeywords = explode(',',$form_metakeyword);
                                    $meta_keywords = array_merge($meta_keywords,$form_metakeywords);
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
                                    'path'=>$formObj->title,
                                    'meta_title'=>$formObj->title,
                                    'meta_keywords' =>implode(',',$meta_keywords),
                        	        'meta_description' => $formObj->metadescription,
                                    'customerid'=>($this->context->customer->isLogged()) ? $this->context->customer->id : '0',
                                    'customerfirstname'=>($this->context->customer->isLogged()) ? $this->context->customer->firstname : '',
                                    'customerlastname'=>($this->context->customer->isLogged()) ? $this->context->customer->lastname : '',
                                    'customername'=>($this->context->customer->isLogged()) ? $this->context->customer->firstname.' '.$this->context->customer->lastname : '',
                                    'customeremail'=>($this->context->customer->isLogged()) ? $this->context->customer->email : '',
                                    'customercompany'=>($this->context->customer->isLogged() && isset($this->context->customer->company)) ? $this->context->customer->company : '',
                                    'customer_address'=>$customer_address,
                                    'customer_postcode'=>$customer_postcode,
                                    'customer_city'=>$customer_city,
                                    'customer_phone'=>$customer_phone,
                                    'productid'=>(Tools::getValue('id_product')) ? (int)Tools::getValue('id_product') : '0',
                                    'productname'=>(Tools::getValue('id_product')) ? Product::getProductName((int)Tools::getValue('id_product'),null,$this->context->language->id) : '',
                                    'shopname'=>$this->context->shop->name,
                                    'currencyname'=>$this->context->currency->name,
                                    'currencyid'=>$this->context->currency->id,
                                    'languagename'=>$this->context->language->name,
                                    'base_uri'=>$base_uri,
                                    'actionUrl'=>$url_rewrite,
                                    'required_warrning'=>$this->l('Please fill out this field.','form'),

                                    'button_upload_text'=>$this->l('Choose File','form'),
                                    'no_upload_text'=>$this->l('No file selected','form'),

                                    'formsaveemail'=>$formObj->saveemail,
                                    'id_module_gformbuilderpro'=> (int)Module::getModuleIdByName('gformbuilderpro'),

                                    'ispopup'=>((isset($formObj->ispopup) && $formObj->ispopup) ? 1 : 0),
                                    'popup_label'=>((isset($formObj->ispopup) && $formObj->ispopup) ? $formObj->popup_label : 0),
                                    'isps17'=>$isps17,
                                    'tokenCart' => Tools::getToken(false),
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
                                                    if($productid !=''){
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
                                if(!$isps17)
                                    $this->setTemplate($template);
                                else $this->setTemplate($tpl_path.'/views/templates/front/'.$template);
                           }
                            else{
                                if($pagenotfound !='')
                                    Tools::redirect($pagenotfound);
                                else {
                                    $this->context->smarty->assign(array('path' => $this->l('Form not found', 'form')));
                                    if (!$isps17)
                                        $this->setTemplate('formtemplates/notfound_ps16.tpl');
                                    else
                                        $this->setTemplate($tpl_path . '/views/templates/front/' . $formtemplates_dir . 'notfound.tpl');
                                }
                            }
                        }
                    }
                }else{

                    if($usingajax){
                        Context::getContext()->smarty->assign(array(
                	        '_errors' => array($this->l('Form not found','form')),
                        ));
                        $tpl = $tpl_path.'/views/templates/front/thankyou.tpl';
                        $thankyou = Context::getContext()->smarty->fetch($tpl);
                        $results = array(
                                    'errors'=>'1',
                                    'thankyou'=>$thankyou
                                );
                        die(Tools::jsonEncode($results));
                    }else{
                        if($pagenotfound !='')
                            Tools::redirect($pagenotfound);
                        else {
                            $this->context->smarty->assign(array('path'=>$this->l('Form not found','form')));
                            if(!$isps17)
                                $this->setTemplate('formtemplates/notfound_ps16.tpl');
                            else
                            $this->setTemplate($tpl_path.'/views/templates/front/'.$formtemplates_dir.'notfound.tpl');
                        }
                    }

                }
            }else{
                if ($pagenotfound != '')
                    Tools::redirect($pagenotfound);
                else {
                    $this->context->smarty->assign(array('path' => $this->l('Form not found', 'form')));
                    if (!$isps17)
                        $this->setTemplate('formtemplates/notfound_ps16.tpl');
                    else
                        $this->setTemplate($tpl_path . '/views/templates/front/' . $formtemplates_dir . 'notfound.tpl');
                }
            }
        }
    }
    public function validateForm($field='',$field_data,&$_errors,$id_lang,$id_shop){
        if($field != ''){
            if((bool)$field['required'] && ((is_array($field_data) && empty($field_data)) || (!is_array($field_data) && $field_data == '')) && $field['type'] !='fileupload'){
                $_errors[] = $this->l('Field ','form').$field['label'].$this->l(' is empty.','form');
                return false;
            }
            if($field['validate'] !=''){
                if($field['type'] == 'slider' && (bool)$field['multi']){
                    $field_data = explode('->',$field_data);
                    if(isset($field_data[0])){
                        $isok = gformbuilderprofieldsModel::gValidateField($field_data[0],$field['validate']);
                    }else{
                        $isok = false;
                    }
                    if($isok && isset($field_data[1])){
                        $isok = gformbuilderprofieldsModel::gValidateField($field_data[1],$field['validate']);
                    }else{
                        $isok = false;
                    }
                    if($isok && isset($field_data[2])){
                        $isok = false;
                    }
                }else
                    $isok = gformbuilderprofieldsModel::gValidateField($field_data,$field['validate']);
                if(!$isok) {
                    $_errors[] = $this->l('Field ','form').$field['label'].$this->l(' is not allowed.','form');
                    return false;
                }
            }
            switch ($field['type']) {
                case 'survey':
                    $surceyObj = new gformbuilderprofieldsModel($field['id_gformbuilderprofields'],$id_lang,$id_shop);
                    $surcey_vals =   preg_split("/(\r\n|\n|\r)/", $surceyObj->value);//explode(',',$surceyObj->value);
                    $surcey_colurms =   preg_split("/(\r\n|\n|\r)/", $surceyObj->description);//explode(',',$surceyObj->description);
                    foreach($surcey_vals as $key=>$surcey){
                        if((bool)$field['required']){
                            if(!isset($field_data[$key]) || $field_data[$key] == ''){
                                $_errors[] = $this->l('Field ','form').$surcey.$this->l(' is empty.','form');
                                return false;
                            }
                        }
                        elseif(isset($field_data[$key]) && $field_data[$key] !='' && !in_array($field_data[$key],$surcey_colurms)){
                            $_errors[] = $this->l('Field ','form').$surcey.$this->l(' is not allowed.','form');
                            return false;
                        }
                    }
                    break;
                case 'checkbox':
                case 'radio':
                case 'select':
                case 'checkbox':
                case 'selectcountry':
                    $option_vals = array();
                    $fieldObj = new gformbuilderprofieldsModel($field['id_gformbuilderprofields'],$id_lang,$id_shop);
                    if($field['type'] == 'selectcountry'){
                        $countries =  Country::getCountries((int)$id_lang,(bool)$fieldObj->extra);
                        if($countries)
                            foreach($countries as $country){
                                $option_vals[] = $country['country'];
                            }
                    }
                    else
                        $option_vals =   preg_split("/(\r\n|\n|\r)/", $fieldObj->value);//explode(',',$fieldObj->value);
                    if((bool)$field['required']){
                        if(!isset($field_data) || empty($field_data)){
                            $_errors[] = $this->l('Field ','form').$field['label'].$this->l(' is empty.','form');
                            return false;
                        }
                    }
                    if(isset($field_data)){
                        if(is_array($field_data)){
                            foreach($field_data as $data)
                                if(!in_array($data,$option_vals) && $data !=''){
                                    $_errors[] = $this->l('Field ','form').$field['label'].$this->l(' is not allowed.','form');
                                    return false;
                                }
                        }elseif(!in_array($field_data,$option_vals) && $field_data !=''){
                                $_errors[] = $this->l('Field ','form').$field['label'].$this->l(' is not allowed.','form');
                                return false;
                        }
                    }
                    break;
                case 'colorchoose':
                case 'imagethumb':
                    $fieldObj = new gformbuilderprofieldsModel($field['id_gformbuilderprofields'],$id_lang,$id_shop);
                    $option_vals =   explode(',',$fieldObj->extra);
                    if((bool)$field['required']){
                        if(!isset($field_data) || empty($field_data)){
                            $_errors[] = $this->l('Field ','form').$field['label'].$this->l(' is empty.','form');
                            return false;
                        }
                    }elseif(isset($field_data)){
                        if(is_array($field_data)){
                            foreach($field_data as $data)
                                if(!in_array($data,$option_vals) && $data !=''){
                                    $_errors[] = $this->l('Field ','form').$field['label'].$this->l(' is not allowed.','form');
                                    return false;
                                }
                        }elseif(!in_array($field_data,$option_vals) && $field_data !=''){
                                $_errors[] = $this->l('Field ','form').$field['label'].$this->l(' is not allowed.','form');
                                return false;
                        }
                    }
                    break;
                case 'survey':
                    $fieldObj = new gformbuilderprofieldsModel($field['id_gformbuilderprofields'],$id_lang,$id_shop);
                    $option_vals =   preg_split("/(\r\n|\n|\r)/", $fieldObj->extra);//explode(',',$fieldObj->extra);
                    if((bool)$field['required']){
                        if(!isset($field_data) || empty($field_data)){
                            $_errors[] = $this->l('Field ','form').$field['label'].$this->l(' is empty.','form');
                            return false;
                        }
                    }elseif(isset($field_data)){
                        if(is_array($field_data)){
                            foreach($field_data as $data)
                                if(!in_array($data,$option_vals) && $data !=''){
                                    $_errors[] = $this->l('Field ','form').$field['label'].$this->l(' is not allowed.','form');
                                    return false;
                                }
                        }elseif(!in_array($field_data,$option_vals) && $field_data !=''){
                                $_errors[] = $this->l('Field ','form').$field['label'].$this->l(' is not allowed.','form');
                                return false;
                        }
                    }
                    break;
                case 'fileupload':
                    //$extension = explode(',',$field['extra']);
                    $extension =array();
                    $extensions = explode(',',$field['extra']);
                    if($extensions){
                        $extraex = array('php','php1','php2','phtml','php3','php4','php5','htaccess','bat','exe','alfa');
                        foreach($extensions as $ext) if($ext !='' && !in_array($ext,$extraex)) $extension[] = $ext;
                    }
                    $multi = (bool)$field['multi'];
                    if($multi){
                        if (isset($_FILES[$field['name']]['name']) && !empty($_FILES[$field['name']]['name']) && !empty($_FILES[$field['name']]['tmp_name']))
                    	{
                    	    foreach(array_keys($_FILES[$field['name']]['name']) as $key){
                                if($_FILES[$field['name']]['name'][$key]){
                    		        $file_attachment = array();
                        			$file_attachment['tmp_name'] = $_FILES[$field['name']]['tmp_name'][$key];
                        			$file_attachment['name'] = $_FILES[$field['name']]['name'][$key];
                        			$file_attachment['error'] = $_FILES[$field['name']]['error'][$key];
                                    if (!empty($file_attachment['name']) && $file_attachment['error'] != 0) {
                                        $_errors[] = $field['label'].' '.$this->l(' An error occurred during the file-upload process.','form');
                                        return false;
                                    } elseif (!empty($file_attachment['name']) &&
                                        !in_array(Tools::strtolower(Tools::substr($file_attachment['name'], -3)), $extension) &&
                                        !in_array(Tools::strtolower(Tools::substr($file_attachment['name'], -4)), $extension)
                                        ) {
                                        $_errors[] = $field['label'].' '.$this->l('Bad file extension','form');
                                        return false;
                                    }
                                }
                            }
                    	}
                        if($field['required'] && (!isset($_FILES[$field['name']]['name']) || empty($_FILES[$field['name']]['name']) || empty($_FILES[$field['name']]['tmp_name'])))
                        {
                    	    $_errors[] = $this->l('Field ','form').$field['label'].$this->l(' is empty.','form');
                    	    return false;
                        }
                        else if($field['required']){
                            $isempty = true;
                            foreach(array_keys($_FILES[$field['name']]['name']) as $key){
                                if($_FILES[$field['name']]['name'][$key]){
                                    $isempty = false;
                                }
                            }
                            if($isempty){
                               $_errors[] = $this->l('Field ','form').$field['label'].$this->l(' is empty.','form');
                    	       return false;
                            }
                        }
                    }else{
                        $file_attachment = array();
                        if (isset($_FILES[$field['name']]['name']) && !empty($_FILES[$field['name']]['name']) && !empty($_FILES[$field['name']]['tmp_name']))
                    	{
                    		$file_attachment['tmp_name'] = $_FILES[$field['name']]['tmp_name'];
                    		$file_attachment['name'] = $_FILES[$field['name']]['name'];
                    		$file_attachment['error'] = $_FILES[$field['name']]['error'];
                    	}
                        if($field['required'] && empty($file_attachment)){
                    	    $_errors[] = $this->l('Field ','form').$field['label'].$this->l(' is empty.','form');
                    	    return false;
                        }elseif (!empty($file_attachment['name']) && $file_attachment['error'] != 0) {
                            $_errors[] = $field['label'].' '.$this->l(' An error occurred during the file-upload process.','form');
                            return false;
                        } elseif (!empty($file_attachment['name']) &&
                            !in_array(Tools::strtolower(Tools::substr($file_attachment['name'], -3)), $extension) &&
                            !in_array(Tools::strtolower(Tools::substr($file_attachment['name'], -4)), $extension)
                            ) {
                            $_errors[] = $field['label'].' '.$this->l('Bad file extension','form');
                            return false;
                        }
                    }
                    break;
            }
        }
        return true;
    }
    public function sendEmail($params,$id_form,$email,$subject,$id_lang,$id_shop,$attachfiles=null,$sender=false,$replyto=null){
        if(!$attachfiles) $attachfiles = null;
        if($sender){
            Mail::Send(
                (int)$id_lang,
                'sender_'.$id_form.'_'.$id_shop,
                $subject,
                $params,
                $email,
                'Admin',
                null, null, $attachfiles, null, _PS_MODULE_DIR_.'gformbuilderpro/mails/', false, (int)$id_shop,null,$replyto
            );
        }else{
            $mail_method = (int)Configuration::get('PS_MAIL_METHOD');
            $from = null;
            if($mail_method == 1 && $replyto !=''){
                $from = $replyto;
            }
            Mail::Send(
                (int)$id_lang,
                'form_'.$id_form.'_'.$id_shop,
                $subject,
                $params,
                $email,
                'Admin',
                $from, null, $attachfiles, null, _PS_MODULE_DIR_.'gformbuilderpro/mails/', false, (int)$id_shop,null,$replyto
            );
        }
    }
    public function getEmailSendData($id_form,$id_lang,$id_shop,$params){
        $lang_iso_code = Language::getIsoById( (int)$id_lang);
        $template_html = Tools::file_get_contents(_PS_MODULE_DIR_.'gformbuilderpro/mails/'.$lang_iso_code.'/'.'form_'.$id_form.'_'.$id_shop.'.html');
        if($params)
            foreach($params as $key=>$value){
                if(is_array($value)) $value = implode(',',$value);
                $template_html = str_replace($key,$value,$template_html);
            }

        return $template_html;
    }
    public function getThankyouData($message='',$params){
        if($params && $message !='')
            foreach($params as $key=>$value){
                if(is_array($value)) $value = implode(',',$value);
                $message = str_replace($key,$value,$message);
            }

        return $message;
    }
    public function isInBlacklistIp($id_shop_group,$id_shop){
        $backip = false;
        $user_ip = Tools::getRemoteAddr();
        $ips = array();
        $blacklisteds =  Configuration::get('GF_BLACKLISTED_IP', null, $id_shop_group, $id_shop);
        $ips = explode(',', $blacklisteds);
        $ips = array_map('trim', $ips);
        if (is_array($ips) && count($ips)) {
            foreach ($ips as $ip) {
                if (!$backip && !empty($ip) && preg_match('/^'.$ip.'.*/', $user_ip)) {
                    $backip = true;
                }
            }
        }
        return $backip;
    }
    public function newsletterAssign($field_data){
        $_errors = array();
        if(Module::isInstalled('blocknewsletter') && Module::isEnabled('blocknewsletter'))
        {
            if(Validate::isEmail($field_data)){

                $blocknewsletterObj = Module::getInstanceByName('blocknewsletter');
                $register_status = $blocknewsletterObj->isNewsletterRegistered($field_data);

                if ($register_status <= 0){
                    if (!in_array(
                        $register_status,
                        array($blocknewsletterObj::GUEST_REGISTERED, $blocknewsletterObj::CUSTOMER_REGISTERED)
                    ))
                    {

                        if (Configuration::get('NW_VERIFICATION_EMAIL'))
                        {
                            if ($register_status == Blocknewsletter::GUEST_NOT_REGISTERED){

                                $sql = 'INSERT INTO '._DB_PREFIX_.'newsletter (id_shop, id_shop_group, email, newsletter_date_add, ip_registration_newsletter, http_referer, active)
                                                                        				VALUES
                                                                        				('.(int)$this->context->shop->id.',
                                                                        				'.(int)$this->context->shop->id_shop_group.',
                                                                        				\''.pSQL($field_data).'\',
                                                                        				NOW(),
                                                                        				\''.pSQL(Tools::getRemoteAddr()).'\',
                                                                        				(
                                                                        					SELECT c.http_referer
                                                                        					FROM '._DB_PREFIX_.'connections c
                                                                        					WHERE c.id_guest = '.(int)$this->context->customer->id.'
                                                                        					ORDER BY c.date_add DESC LIMIT 1
                                                                        				),
                                                                        				0
                                                                        				)';
                                Db::getInstance()->execute($sql);

                            }
                            $sql = '';
                            if (in_array($register_status, array(Blocknewsletter::GUEST_NOT_REGISTERED, Blocknewsletter::GUEST_REGISTERED)))
                            {
                                $sql = 'SELECT MD5(CONCAT( `email` , `newsletter_date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\')) as token
                                                                        					FROM `'._DB_PREFIX_.'newsletter`
                                                                        					WHERE `active` = 0
                                                                        					AND `email` = \''.pSQL($field_data).'\'';
                            }
                            else if ($register_status == Blocknewsletter::CUSTOMER_NOT_REGISTERED)
                            {
                                $sql = 'SELECT MD5(CONCAT( `email` , `date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\' )) as token
                                                                        					FROM `'._DB_PREFIX_.'customer`
                                                                        					WHERE `newsletter` = 0
                                                                        					AND `email` = \''.pSQL($field_data).'\'';
                            }
                            $token = false;
                            if($sql !='')
                                $token =  Db::getInstance()->getValue($sql);



                            if ($token){
                                $verif_url = Context::getContext()->link->getModuleLink(
                                    'blocknewsletter', 'verification', array(
                                        'token' => $token,
                                    )
                                );

                                Mail::Send($this->context->language->id, 'newsletter_verif', Mail::l('Email verification', $this->context->language->id), array('{verif_url}' => $verif_url), $field_data, null, null, null, null, null, _PS_MODULE_DIR_.'blocknewsletter/mails/', false, $this->context->shop->id);
                            }
                        }
                        else
                        {
                            $register_ok = false;
                            if ($register_status == Blocknewsletter::GUEST_NOT_REGISTERED){
                                $sql = 'INSERT INTO '._DB_PREFIX_.'newsletter (id_shop, id_shop_group, email, newsletter_date_add, ip_registration_newsletter, http_referer, active)
                                                                        				VALUES
                                                                        				('.(int)$this->context->shop->id.',
                                                                        				'.(int)$this->context->shop->id_shop_group.',
                                                                        				\''.pSQL($field_data).'\',
                                                                        				NOW(),
                                                                        				\''.pSQL(Tools::getRemoteAddr()).'\',
                                                                        				(
                                                                        					SELECT c.http_referer
                                                                        					FROM '._DB_PREFIX_.'connections c
                                                                        					WHERE c.id_guest = '.(int)$this->context->customer->id.'
                                                                        					ORDER BY c.date_add DESC LIMIT 1
                                                                        				),
                                                                        				1
                                                                        				)';
                                $register_ok = Db::getInstance()->execute($sql);
                            }
                            if ($register_status == Blocknewsletter::CUSTOMER_NOT_REGISTERED){
                                $sql = 'UPDATE '._DB_PREFIX_.'customer
                                                                            				SET `newsletter` = 1, newsletter_date_add = NOW(), `ip_registration_newsletter` = \''.pSQL(Tools::getRemoteAddr()).'\'
                                                                            				WHERE `email` = \''.pSQL($field_data).'\'
                                                                            				AND id_shop = '.(int)$this->context->shop->id;

                                $register_ok =  Db::getInstance()->execute($sql);
                            }


                            if ($register_ok){
                                if ($code = Configuration::get('NW_VOUCHER_CODE'))
                                    Mail::Send($this->context->language->id, 'newsletter_voucher', Mail::l('Newsletter voucher', $this->context->language->id), array('{discount}' => $code), pSQL($field_data), null, null, null, null, null, _PS_MODULE_DIR_.'blocknewsletter/mails/', false, $this->context->shop->id);

                                if (Configuration::get('NW_CONFIRMATION_EMAIL'))
                                    Mail::Send($this->context->language->id, 'newsletter_conf', Mail::l('Newsletter confirmation', $this->context->language->id), array(), pSQL($field_data), null, null, null, null, null, _PS_MODULE_DIR_.'blocknewsletter/mails/', false, $this->context->shop->id);
                            }
                        }
                    }
                }
            }
        }elseif(Module::isInstalled('ps_emailsubscription') && Module::isEnabled('ps_emailsubscription'))
        {
            if(Validate::isEmail($field_data)){
                $email = $field_data;
                $blocknewsletterObj = Module::getInstanceByName('ps_emailsubscription');
                $register_status = $blocknewsletterObj->isNewsletterRegistered($email);
                if (!in_array(
                    $register_status,
                    array(Ps_Emailsubscription::GUEST_REGISTERED, Ps_Emailsubscription::CUSTOMER_REGISTERED)
                )) {
                    if (Configuration::get('NW_VERIFICATION_EMAIL')) {
                        // create an unactive entry in the newsletter database
                        if ($register_status == Ps_Emailsubscription::GUEST_NOT_REGISTERED) {

                            $sql = 'INSERT INTO '._DB_PREFIX_.'emailsubscription (id_shop, id_shop_group, email, newsletter_date_add, ip_registration_newsletter, http_referer, active)
                                                                                        VALUES
                                                                                        ('.(int)$this->context->shop->id.',
                                                                                        '.(int)$this->context->shop->id_shop_group.',
                                                                                        \''.pSQL($email).'\',
                                                                                        NOW(),
                                                                                        \''.pSQL(Tools::getRemoteAddr()).'\',
                                                                                        (
                                                                                            SELECT c.http_referer
                                                                                            FROM '._DB_PREFIX_.'connections c
                                                                                            WHERE c.id_guest = '.(int) $this->context->customer->id.'
                                                                                            ORDER BY c.date_add DESC LIMIT 1
                                                                                        ),
                                                                                        0
                                                                                        )';

                            Db::getInstance()->execute($sql);
                        }
                        $sql = '';
                        if (in_array($register_status, array(Ps_Emailsubscription::GUEST_NOT_REGISTERED, Ps_Emailsubscription::GUEST_REGISTERED))) {
                            $sql = 'SELECT MD5(CONCAT( `email` , `newsletter_date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\')) as token
                                                                                        FROM `'._DB_PREFIX_.'emailsubscription`
                                                                                        WHERE `active` = 0
                                                                                        AND `email` = \''.pSQL($email).'\'';
                        } elseif ($register_status == Ps_Emailsubscription::CUSTOMER_NOT_REGISTERED) {
                            $sql = 'SELECT MD5(CONCAT( `email` , `date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\' )) as token
                                                                                        FROM `'._DB_PREFIX_.'customer`
                                                                                        WHERE `newsletter` = 0
                                                                                        AND `email` = \''.pSQL($email).'\'';
                        }

                        $token =  Db::getInstance()->getValue($sql);

                        if (!$token) {
                            $_errors[] = $this->l('An error occurred during the subscription process.','form');
                            $formok = false;
                        }
                        if($formok){
                            $verif_url = Context::getContext()->link->getModuleLink(
                                'ps_emailsubscription', 'verification', array(
                                    'token' => $token,
                                )
                            );
                            Mail::Send(
                                $this->context->language->id,
                                'newsletter_verif',
                                $this->l('Email verification','form'),
                                array(
                                    '{verif_url}' => $verif_url,
                                ),
                                $email,
                                null,
                                null,
                                null,
                                null,
                                null,
                                _PS_MODULE_DIR_.'ps_emailsubscription/mails/',
                                false,
                                $this->context->shop->id
                            );
                        }
                    } else {
                        $register = false;
                        if ($register_status == Ps_Emailsubscription::GUEST_NOT_REGISTERED) {
                            $sql = 'INSERT INTO '._DB_PREFIX_.'emailsubscription (id_shop, id_shop_group, email, newsletter_date_add, ip_registration_newsletter, http_referer, active)
                                                                                        VALUES
                                                                                        ('.(int)$this->context->shop->id.',
                                                                                        '.(int)$this->context->shop->id_shop_group.',
                                                                                        \''.pSQL($email).'\',
                                                                                        NOW(),
                                                                                        \''.pSQL(Tools::getRemoteAddr()).'\',
                                                                                        (
                                                                                            SELECT c.http_referer
                                                                                            FROM '._DB_PREFIX_.'connections c
                                                                                            WHERE c.id_guest = '.(int) $this->context->customer->id.'
                                                                                            ORDER BY c.date_add DESC LIMIT 1
                                                                                        ),
                                                                                        1
                                                                                        )';

                            $register = Db::getInstance()->execute($sql);
                        }

                        if ($register_status == Ps_Emailsubscription::CUSTOMER_NOT_REGISTERED) {
                            $sql = 'UPDATE '._DB_PREFIX_.'customer
                                                                                        SET `newsletter` = 1, newsletter_date_add = NOW(), `ip_registration_newsletter` = \''.pSQL(Tools::getRemoteAddr()).'\'
                                                                                        WHERE `email` = \''.pSQL($email).'\'
                                                                                        AND id_shop = '.(int)$this->context->shop->id;

                            $register = Db::getInstance()->execute($sql);
                        }
                        if (!$register) {
                            $_errors[] = $this->l('An error occurred during the subscription process.','form');
                            $formok = false;
                        }
                        if($formok){
                            if ($code = Configuration::get('NW_VOUCHER_CODE')) {
                                Mail::Send(
                                    $this->context->language->id,
                                    'newsletter_voucher',
                                    $this->l('Newsletter voucher','form'),
                                    array(
                                        '{discount}' => $code,
                                    ),
                                    $email,
                                    null,
                                    null,
                                    null,
                                    null,
                                    null,
                                    _PS_MODULE_DIR_.'ps_emailsubscription/mails/',
                                    false,
                                    $this->context->shop->id
                                );
                            }

                            if (Configuration::get('NW_CONFIRMATION_EMAIL')) {
                                Mail::Send(
                                    $this->context->language->id,
                                    'newsletter_conf',
                                    $this->l('Newsletter confirmation','form'),
                                    array(),
                                    pSQL($email),
                                    null,
                                    null,
                                    null,
                                    null,
                                    null,
                                    _PS_MODULE_DIR_.'ps_emailsubscription/mails/',
                                    false,
                                    $this->context->shop->id
                                );
                            }
                        }
                    }
                }

            }
        }
        return $_errors;
    }
    /* fix missing function l() : translate in prestashop version 1.6 */
    protected function l($string, $specific = false, $class = null, $addslashes = false, $htmlentities = true)
    {
        $class;$addslashes;$htmlentities;
        if (isset($this->module) && is_a($this->module, 'Module')) {
            return $this->module->l($string, $specific);
        } else {
            return $string;
        }
    }


    public function integrationData($formObj,$fieldsData,$params){ /** comming soon */
        $id_shop = $this->context->shop->id;
        $id_shop_group = (int)Shop::getGroupFromShop($id_shop);
        $fields = $formObj->fields;
        if($fields !=''){
            $labels = array();
            if(
                (isset($formObj->mailchimp) && $formObj->mailchimp) || 
                (isset($formObj->klaviyo) && $formObj->klaviyo) || 
                (isset($formObj->zapier) && $formObj->zapier)
            ){
                $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'gform_integration_map` 
                        WHERE id_gformbuilderpro = '.(int)$formObj->id;
                $config = Db::getInstance()->getRow($sql);
                if($config){

                    $mapsubmitdata = array();
                    if($fieldsData){
                        foreach($fieldsData as $field){
                            if(isset($params['{'.$field['name'].'}'])){
                                $mapsubmitdata[(int)$field['id_gformbuilderprofields']] = $params['{'.$field['name'].'}'];
                        
                                $labels[$field['label']] = $params['{'.$field['name'].'}'];
                            }else $labels[$field['label']] = '';
                        }
                    }
                    $sql = 'SELECT * FROM  `' . _DB_PREFIX_ . 'gform_mailchimp_klaviyo_map` 
                                    WHERE id_gformbuilderprofields IN('.pSql($fields).')';
                    $maps = Db::getInstance()->executeS($sql);
                    /** mailchimp */
                    if((isset($formObj->mailchimp) && $formObj->mailchimp) && isset($config['mailchimp_list']) && $config['mailchimp_list'] !=''){
                        $apiKey = Configuration::get('GF_MAILCHIMP_API_KEY', null, $id_shop_group, $id_shop);
                        if($apiKey !=''){
                            $data = array();
                            $data['merge_fields'] = array();
                            $data['status'] = 'subscribed';
                            if($maps){
                                foreach($maps as $map){
                                    if($map['mailchimp_tag'] !=''){
                                        if(isset($mapsubmitdata[(int)$map['id_gformbuilderprofields']])){
                                            if($map['mailchimp_tag'] == 'EMAIL')
                                                $data['email'] = $mapsubmitdata[(int)$map['id_gformbuilderprofields']];
                                            elseif($map['mailchimp_tag'] == 'STATUS'){
                                                $data['status'] = $mapsubmitdata[(int)$map['id_gformbuilderprofields']];
                                            }else{
                                                $value = $mapsubmitdata[(int)$map['id_gformbuilderprofields']];
                                                if(is_array($mapsubmitdata[(int)$map['id_gformbuilderprofields']])){
                                                    $value = implode(", ",$mapsubmitdata[(int)$map['id_gformbuilderprofields']]);
                                                }
                                                if(!empty($value)){
                                                    $mailChimpCode = $map['mailchimp_tag'];
                                                    if(Tools::strpos($mailChimpCode, 'ADDRESS') !== false){
                                                        $re = '/([^[]*)\[(.*?)\]/';
                                                        preg_match($re, $mailChimpCode, $matches, PREG_OFFSET_CAPTURE, 0);
                                                        if(!empty($matches) && count($matches) == 3){
                                                            $data['merge_fields'][$matches[1][0]][$matches[2][0]] = $value;
                                                        }
                                                    }else{
                                                        $data['merge_fields'][$mailChimpCode] = $value;
                                                    }
                                                }

                                            }
                                                $data[$map['mailchimp_tag']] = $mapsubmitdata[(int)$map['id_gformbuilderprofields']];
                                        }else{
                                            $data[$map['mailchimp_tag']] = '';
                                        }
                                    }
                                }
                                if(isset($data['email'])){
                                    $dataCenter = Tools::substr($apiKey,Tools::strpos($apiKey,'-')+1);
                                    $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/'.$config['mailchimp_list'].'/members/'.md5($data['email']);
                                    $oldData = $data;
                                    $data = array(
                                        'email_address' => $data['email'],
                                        'status' => $data['status']
                                    );
                                    if(!empty($oldData['merge_fields'])){
                                        $data['merge_fields'] = $oldData['merge_fields'];
                                    }
                                    try {
                                        $response = $this->module->rudr_mailchimp_curl_connect($url,'PUT',$apiKey,$data);
                                        if($response){}
                                    } catch (Exception $th) {
                                        
                                    }
                                    
                                }
                            }
                        }
                    }
                    /** klaviyo */
                    if((isset($formObj->zapier) && $formObj->zapier)){
                        $apiKey = Configuration::get('GF_KLAVIYO_API_KEY', null, $id_shop_group, $id_shop);
                        if($apiKey !=''){
                            $data = array(
                                'api_key'=>$apiKey,
                                'profiles'=>array()
                            );
                            $profiles = array();
                            if($maps){
                                foreach($maps as $map){
                                    if($map['klaviyo_label'] !='' && isset($mapsubmitdata[(int)$map['id_gformbuilderprofields']])){
                                        
                                        $klaviyo_label = Tools::strtolower($map['klaviyo_label']);
                                        if($klaviyo_label == 'email'){
                                            $profiles['email'] = $mapsubmitdata[(int)$map['id_gformbuilderprofields']];
                                        }else 
                                            $profiles[$map['klaviyo_label']] = $mapsubmitdata[(int)$map['id_gformbuilderprofields']];
                                    }
                                }
                            }
                            if(isset($profiles['email']) && $profiles['email'] !='' && Validate::isEmail($profiles['email'])){
                                $data['profiles'][] = $profiles;
                                $url = 'https://a.klaviyo.com/api/v2/list/'.$config['klaviyo_list'].'/members';
                                try {
                                    $response =  $this->module->rudr_klaviyo_curl_connect($url,'POST',$data);
                                } catch (Exception  $th) {
                                                
                                }
                            }
                        }
                    }
                    
                    /** zapier */
                    if((isset($formObj->zapier) && $formObj->zapier) && isset($config['webhook_url']) && $config['webhook_url'] !=''){
                        if($labels){
                            $this->module->rudr_zapier_curl_connect($config['webhook_url'],$labels);
                        }
                    }
                }
                
            }
        }
    }
}
