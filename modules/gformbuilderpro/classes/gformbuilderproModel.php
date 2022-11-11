<?php
/**
* The file is Model of module. Do not modify the file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright  2017 Globo., Jsc
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/

include_once(_PS_MODULE_DIR_ . 'gformbuilderpro/classes/gformbuilderprofieldsModel.php');
include_once(_PS_MODULE_DIR_ . 'gformbuilderpro/classes/gformrequestModel.php');
class gformbuilderproModel extends ObjectModel
{
    public $id_gformbuilderpro;
    public $active = 1;
    public $sendtosender = 0;
    public $usingajax = 1;
    public $saveemail = 0;
    public $requiredlogin;
    public $formtemplate;
    public $hooks;
    public $fields;
    public $sendto;
    public $sender;
    public $rewrite;
    public $metakeywords;
    public $metadescription;
    public $title;
    public $subject;
    public $subjectsender;
    public $emailtemplate;
    public $emailtemplatesender;
    public $success_message;
    public $error_message;
    public $autoredirect;
    public $timedelay;
    public $redirect_link;
    public $redirect_link_lang;
    public $ispopup;
    public $popup_label;
    public $replysubject;
    public $replyemailtemplate;
    
    public $autostar = 0;
    public $using_condition;
    public $condition_configs;

    public $admin_attachfiles;
    public $sender_attachfiles;

    public $mailchimp;
    public $klaviyo;
    public $zapier;

    public $sender_name;
    public $customcss;
    
    public static $definition = array(
        'table' => 'gformbuilderpro',
        'primary' => 'id_gformbuilderpro',
        'multilang' => true,
        'fields' => array(
            //Fields
            'active'          =>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'sendtosender'=>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'usingajax'          =>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'saveemail' =>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'requiredlogin'          =>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'formtemplate'  =>  array('type' => self::TYPE_HTML,'validate' => 'isCleanHtml'),
            'fields'  =>  array('type' => self::TYPE_STRING,'validate' => 'isString'),
            'hooks'  =>  array('type' => self::TYPE_STRING,'validate' => 'isCleanHtml'),
            'sendto'  =>  array('type' => self::TYPE_STRING,'validate' => 'isString'),
            'sender'  =>  array('type' => self::TYPE_STRING,'validate' => 'isString'),
            'autoredirect'=>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'timedelay'=>  array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'redirect_link'  =>  array('type' => self::TYPE_STRING,'validate' => 'isCleanHtml'),
            'ispopup'=>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'using_condition'=>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'autostar' =>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'condition_configs' => 	array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
            'admin_attachfiles' =>  array('type' => self::TYPE_STRING,'validate' => 'isString'),
            'sender_attachfiles' =>  array('type' => self::TYPE_STRING,'validate' => 'isString'),
            'sender_name' =>  array('type' => self::TYPE_STRING,'validate' => 'isString'),
            'mailchimp'          =>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'klaviyo'          =>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'zapier'          =>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),

            //lang = true
            'title'       =>  array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255),
            'rewrite'       =>  array('type' => self::TYPE_STRING, 'lang' => true,'size' => 255,'validate' => 'isString'),
            'metakeywords'       =>  array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'metadescription'       =>  array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'subject' =>  array('type' => self::TYPE_STRING, 'lang' => true,'size' => 255,'validate' => 'isString'),
            'subjectsender' =>  array('type' => self::TYPE_STRING, 'lang' => true,'size' => 255,'validate' => 'isString'),
            'emailtemplate' => 	array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'emailtemplatesender' =>array('type' => self::TYPE_HTML, 'lang' => true,'validate' => 'isCleanHtml'),
            'success_message' => 	array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'error_message' => 	array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'popup_label' =>  array('type' => self::TYPE_STRING, 'lang' => true,'size' => 255,'validate' => 'isString'),
            'replysubject' =>  array('type' => self::TYPE_STRING, 'lang' => true,'size' => 255,'validate' => 'isString'),
            'replyemailtemplate' => 	array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),

            'redirect_link_lang'  =>  array('type' => self::TYPE_STRING,'lang' => true,'validate' => 'isCleanHtml'),
            'customcss'  =>  array('type' => self::TYPE_STRING),
        )
    );

    public function __construct($id_gformbuilderpro = null, $id_lang = null, $id_shop = null)
    {
        if(version_compare(_PS_VERSION_,'1.5.3') != -1)
            Shop::addTableAssociation('gformbuilderpro', array('type' => 'shop'));
        parent::__construct($id_gformbuilderpro, $id_lang, $id_shop);

    }
    public static function getAllBlock(){
        $id_shop = (int)Context::getContext()->shop->id;
        $id_lang = (int)Context::getContext()->language->id;
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT a.*,b.title
            FROM '._DB_PREFIX_.'gformbuilderpro as a,
                 '._DB_PREFIX_.'gformbuilderpro_lang as b,
                 '._DB_PREFIX_.'gformbuilderpro_shop as c
            WHERE a.id_gformbuilderpro = b.id_gformbuilderpro
            AND a.id_gformbuilderpro = c.id_gformbuilderpro
            AND c.id_shop = '.(int)$id_shop.'
            AND b.id_lang = '.(int)$id_lang.'
            AND a.active = 1'
        );
        return $res;
    }
    public static function getAllFormInHook($hook=''){
        $res = array();
        if($hook !=''){
            $id_shop = (int)Context::getContext()->shop->id;
            $id_lang = (int)Context::getContext()->language->id;
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT a.*,b.title
                FROM '._DB_PREFIX_.'gformbuilderpro as a,
                     '._DB_PREFIX_.'gformbuilderpro_lang as b,
                     '._DB_PREFIX_.'gformbuilderpro_shop as c
                WHERE a.id_gformbuilderpro = b.id_gformbuilderpro
                AND a.id_gformbuilderpro = c.id_gformbuilderpro
                AND c.id_shop = '.(int)$id_shop.'
                AND b.id_lang = '.(int)$id_lang.'
                and FIND_IN_SET("'.pSQL($hook).'",a.hooks)
                AND a.active = 1'
            );
        }
        return $res;
    }
    public function parseTpl($id_lang,$id_shop){
        
        $module_dir = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/front/formtemplates/';
        $id_form = $this->id_gformbuilderpro;
        if (!is_dir($module_dir.$id_form.'/'))
           mkdir($module_dir.$id_form.'/', 0755);
        if(!file_exists($module_dir.$id_form.'/index.php'))
            @copy(_PS_MODULE_DIR_.'gformbuilderpro/index.php',$module_dir.$id_form.'/index.php');
        if (!is_dir($module_dir.$id_form.'/'.$id_lang.'/'))
           mkdir($module_dir.$id_form.'/'.$id_lang.'/', 0755);
        if(!file_exists($module_dir.$id_form.'/'.$id_lang.'/index.php'))
            @copy(_PS_MODULE_DIR_.'gformbuilderpro/index.php',$module_dir.$id_form.'/'.$id_lang.'/index.php');
        $html = preg_replace('/<div class="field_content">.+<\/div>/siU', '',$this->formtemplate);
        $html = preg_replace("/<span class=\"shortcode\">(.*?)<\/span>/", "$1", $html);
        preg_match_all('/\[(gformbuilderpro:)(.*?)\]/', $html, $matches);
        $customShortCodes = array();
        $hasupload  = false;
        $newContent = '';
        if(isset($matches[0]) && $matches[0]){
            foreach($matches[0] as $key=>$content)
            {
                $matchNoBrackets = str_replace(array('[',']'),'',$content);
                $shortCodeExploded = explode(':', $matchNoBrackets);
                $customShortCodes['gformbuilderpro'][$key] = $shortCodeExploded[1];
            }
            $newContent = $html;
            foreach($customShortCodes as $shortCodeKey=>$shortCode)
            {
                if($shortCodeKey == 'gformbuilderpro')
                {
                    foreach($shortCode as $show)
                    {
                        $testingReplacementText = gformbuilderprofieldsModel::getField($show,$id_lang,$hasupload,$id_shop);
                        $originalShortCode = "[gformbuilderpro:$show]";
                        $newContent = str_replace($originalShortCode,$testingReplacementText,$newContent);
                    }
                }
            }
        }
        $fields_value = array(
            'hasupload' =>(int)$hasupload,
            'htmlcontent'=>$newContent,
            'idform'=>(int)$id_form,
            'id_lang'=>(int)$id_lang,
            'id_shop'=>(int)$id_shop,
            'ajax'=>(bool)$this->usingajax,
            'actionUrl'=>Context::getContext()->link->getModuleLink('gformbuilderpro','form',array('id'=>$this->id_gformbuilderpro,'rewrite'=>$this->rewrite[$id_lang])),
            'submittitle'=>''
            );
        if (version_compare(_PS_VERSION_, '1.6.0.0', '<')){
            $fields_value['old_psversion_15'] = 1;
        }
        $is_ps17 = version_compare(_PS_VERSION_, '1.7', '>=');
        Context::getContext()->smarty->assign($fields_value);
        if($is_ps17)
            Context::getContext()->smarty->assign('codehook',0);
        $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/front/templates.tpl';
        if(!$is_ps17){
            $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/front/templates_ps16.tpl';
        }
        $newContent = Context::getContext()->smarty->fetch($tpl);
        $file_name = $id_shop.'_form.tpl';   
		$file = $module_dir.$id_form.'/'.$id_lang.'/'.$file_name;
		$handle = fopen($file, 'w+');
		fwrite($handle, self::minifyHtml($newContent));
		fclose($handle);
        if($is_ps17){
            // file template for shortcode or hook
            Context::getContext()->smarty->assign('codehook',1);
            $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/front/templates.tpl';
            $newContent = Context::getContext()->smarty->fetch($tpl);
            $file_name = $id_shop.'_form_codehook.tpl';   
    		$file = $module_dir.$id_form.'/'.$id_lang.'/'.$file_name;
    		$handle = fopen($file, 'w+');
    		fwrite($handle, self::minifyHtml($newContent));
    		fclose($handle);
            $dir = _PS_THEME_DIR_.'modules/gformbuilderpro/';
            self::delTree($dir);
        }

    }
    public function fixMce($str = '')
    {
        $search  = array('%7Bshop_logo%7D', '%7Bshop_name%7D', '%7Bshop_url%7D');
        $replace = array('{shop_logo}', '{shop_name}','{shop_url}');
        $str = str_replace($search,$replace, $str);
        return $str;
    }
    public function parseEmail($lang,$filename,$sender = false){
        $isps17 = version_compare(_PS_VERSION_, '1.7', '>=');
        Context::getContext()->smarty->assign(array('isps17' => $isps17));
        $mails_dir = _PS_MODULE_DIR_.'gformbuilderpro/mails/';
        if (!is_dir($mails_dir.$lang['iso_code'].'/')){
            @mkdir($mails_dir.$lang['iso_code'].'/', 0755);
        }
        if(!file_exists($mails_dir.$lang['iso_code'].'/index.php'))
            @copy(_PS_MODULE_DIR_.'gformbuilderpro/index.php',$mails_dir.$lang['iso_code'].'/index.php');
        $file = $mails_dir.$lang['iso_code'].'/'.$filename.'.html';
        $fields_value = array();
        $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/front/emailbase.tpl';
        if($sender){
            $emailcontent = $this->fixMce($this->emailtemplatesender[$lang['id_lang']]);
            $fields_value = array(
                'subject'=>($this->subjectsender[$lang['id_lang']] !='') ? $this->subjectsender[$lang['id_lang']] : ' ',
                'emailcontent'=>$emailcontent,
            );
            Context::getContext()->smarty->assign($fields_value);
            $newemailContent = Context::getContext()->smarty->fetch($tpl);
            $handle = fopen($file, 'w+');
    		fwrite($handle,self::minifyHtml($newemailContent));
    		fclose($handle);
            $file = $mails_dir.$lang['iso_code'].'/'.$filename.'.txt';
            $handle = fopen($file, 'w+');
		    fwrite($handle, strip_tags($this->emailtemplatesender[$lang['id_lang']]));
    		fclose($handle);
        }
        else{
            $emailcontent = $this->fixMce($this->emailtemplate[$lang['id_lang']]);
            $fields_value = array(
                'subject'=>$this->subject[$lang['id_lang']],
                'emailcontent'=>$emailcontent,
            );
            Context::getContext()->smarty->assign($fields_value);
            $newemailContent = Context::getContext()->smarty->fetch($tpl);
            $handle = fopen($file, 'w+');
    		fwrite($handle,self::minifyHtml($newemailContent));
    		fclose($handle);
            $file = $mails_dir.$lang['iso_code'].'/'.$filename.'.txt';
            $handle = fopen($file, 'w+');
            fwrite($handle, strip_tags($this->emailtemplate[$lang['id_lang']]));
    		fclose($handle);
            /* reply template */
            $file = $mails_dir.$lang['iso_code'].'/reply_'.$filename.'.html';
            $emailcontent = $this->fixMce($this->replyemailtemplate[$lang['id_lang']]);
            $fields_value = array(
                'subject'=>$this->replysubject[$lang['id_lang']],
                'emailcontent'=>$emailcontent,
                'reply'=>1
            );
            Context::getContext()->smarty->assign($fields_value);
            $newemailContent = Context::getContext()->smarty->fetch($tpl);
            $handle = fopen($file, 'w+');
    		fwrite($handle,self::minifyHtml($newemailContent));
    		fclose($handle);
            $file = $mails_dir.$lang['iso_code'].'/reply_'.$filename.'.txt';
            $handle = fopen($file, 'w+');
            fwrite($handle, strip_tags($this->replyemailtemplate[$lang['id_lang']]));
    		fclose($handle);
            Context::getContext()->smarty->assign(array('reply'=>0));
        }
        /* remove theme cache */
        $dir = _PS_THEME_DIR_.'modules/gformbuilderpro/';
        self::delTree($dir);
    }
    public function delete()
    {
        $fields = $this->fields;
        $res = true;
        // delete all field in form
        if($fields !=''){
            $all_fields = explode(',',$fields);
            foreach ($all_fields as $field)
    		{
				$fieldObj = new gformbuilderprofieldsModel((int)$field);
                if(Validate::isLoadedObject($fieldObj))
                    $res&=$fieldObj->delete();
    		}
        }
        if($this->id_gformbuilderpro){
            $dir = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/front/formtemplates/'.$this->id_gformbuilderpro.'/';
            self::delTree($dir);
        }
        // delete all request from form
		$res &=gformrequestModel::removeRequestForm((int)$this->id_gformbuilderpro);
        $res &= parent::delete();
        return $res;
    }
    public static function delTree($dir = ''){
        if (is_dir($dir) && $dir !='')
		{
			$objects = scandir($dir);
			foreach ($objects as $object)
				if ($object != '.' && $object != '..')
				{
					if (filetype($dir.'/'.$object) == 'dir')
						self::delTree($dir.'/'.$object);
					else
						unlink($dir.'/'.$object);
				}
			reset($objects);
			rmdir($dir);
		}
    }
    public static function  minifyHtml($input = ''){
        $events = array('onmousedown','onmousemove','onmmouseup','onmouseover','onmouseout','onload','onunload','onfocus','onblur','onchange','onsubmit','ondblclick','onclick','onkeydown','onkeyup','onkeypress',
            'onmouseenter','onmouseleave','onerror','onselect','onreset','onabort','ondragdrop','onresize','onactivate','onafterprint',
            'onmoveend','onafterupdate','onbeforeactivate','onbeforecopy','onbeforecut','onbeforedeactivate','onbeforeeditfocus','onbeforepaste','onbeforeprint','onbeforeunload','onbeforeupdate','onmove',
            'onbounce','oncellchange','oncontextmenu','oncontrolselect','oncopy','oncut','ondataavailable','ondatasetchanged','ondatasetcomplete','ondeactivate','ondrag','ondragend','ondragenter','onmousewheel',
            'ondragleave','ondragover','ondragstart','ondrop','onerrorupdate','onfilterchange','onfinish','onfocusin','onfocusout','onhashchange','onhelp','oninput','onlosecapture','onmessage','onmouseup','onmovestart',
            'onoffline','ononline','onpaste','onpropertychange','onreadystatechange','onresizeend','onresizestart','onrowenter','onrowexit','onrowsdelete','onrowsinserted','onscroll','onsearch','onselectionchange',
            'onselectstart','onstart','onstop');
        $unsafe = array();
        foreach($events as $event){
            $unsafe[] = '/'.$event.'="(.*?)"/is';
        }
        /* Fix bug Validate::isCleanHtml */
        $input=preg_replace($unsafe, "", $input);
        if(method_exists('Media','minifyHTML')){
            return Media::minifyHTML($input);
        }elseif(method_exists('Tools','minifyHTML')){
            return Tools::minifyHTML($input);
        }else{
            if(trim($input) === "") return $input;
            // Remove extra white-space(s) between HTML attribute(s)
            $input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function($matches) {
                return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
            }, str_replace("\r", "", $input));
            // Minify inline CSS declaration(s)
            if(strpos($input, ' style=') !== false) {
                $input = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function($matches) {
                    return '<' . $matches[1] . ' style=' . $matches[2] . gformbuilderproModel::minify_css($matches[3]) . $matches[2];
                }, $input);
            }
            return preg_replace(
                array(
                    // t = text
                    // o = tag open
                    // c = tag close
                    // Keep important white-space(s) after self-closing HTML tag(s)
                    '#<(img|input)(>| .*?>)#s',
                    // Remove a line break and two or more white-space(s) between tag(s)
                    '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
                    '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
                    '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
                    '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
                    '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
                    '#<(img|input)(>| .*?>)<\/\1>#s', // reset previous fix
                    '#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
                    '#(?<=\>)(&nbsp;)(?=\<)#', // --ibid
                    // Remove HTML comment(s) except IE comment(s)
                    '#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s'
                ),
                array(
                    '<$1$2</$1>',
                    '$1$2$3',
                    '$1$2$3',
                    '$1$2$3$4$5',
                    '$1$2$3$4$5$6$7',
                    '$1$2$3',
                    '<$1$2',
                    '$1 ',
                    '$1',
                    ""
                ),
            $input);
        }
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
}