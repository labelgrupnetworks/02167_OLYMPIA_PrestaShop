<?php
/**
* The file is Model of module. Do not modify the file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright  2017 Globo., Jsc
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/

include_once(_PS_MODULE_DIR_ . 'gformbuilderpro/classes/gformbuilderproModel.php');
class gformbuilderprofieldsModel extends ObjectModel
{
    public $id_gformbuilderprofields;
    public $name;
    public $type;
    public $idatt;
    public $classatt;
    public $labelpos = 0;
    public $required = 0;
    public $validate;
    public $extra;
    public $label;
    public $value;
    public $placeholder;
    public $description;
    public $multi = 0;
    public $dynamicval;
    public $extra_option;
    public $condition;
    public $condition_display;
    public $condition_must_match;
    public $condition_listoptions;
    public static $definition = array(
        'table' => 'gformbuilderprofields',
        'primary' => 'id_gformbuilderprofields',
        'multilang' => true,
        'fields' => array(
            //Fields
            'required' =>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'labelpos' =>  array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'name'  =>  array('type' => self::TYPE_STRING,'required' => true, 'validate' => 'isString'),
            'type'  =>  array('type' => self::TYPE_STRING,'validate' => 'isString'),
            'idatt'  =>  array('type' => self::TYPE_STRING,'size' => 255,'validate' => 'isString'),
            'classatt'  =>  array('type' => self::TYPE_STRING,'size' => 255,'validate' => 'isString'),
            'validate'  =>  array('type' => self::TYPE_STRING,'size' => 255,'validate' => 'isString'),
            'extra'  =>  array('type' => self::TYPE_STRING,'validate' => 'isString'),
            'multi' =>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'dynamicval'  =>  array('type' => self::TYPE_STRING,'size' => 255,'validate' => 'isString'),
            
            //lang = true
            'label'       =>  array('type' => self::TYPE_STRING, 'lang' => true,'required' => true, 'validate' => 'isGenericName','size' => 255),
            'value' =>  array('type' => self::TYPE_STRING, 'lang' => true,'validate' => 'isString'),
            'placeholder' => 	array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'),
            'description' => 	array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            //new version 1.3.6
            
            'extra_option'  =>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'condition'     =>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'condition_display'     =>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'condition_must_match'  =>  array('type' => self::TYPE_INT, 'validate' => 'isBool'),
            'condition_listoptions' =>  array('type' => self::TYPE_STRING),
        )
    );

    public function __construct($id_gformbuilderprofields = null, $id_lang = null, $id_shop = null)
    {
        if(version_compare(_PS_VERSION_,'1.5.3') != -1)
            Shop::addTableAssociation('gformbuilderprofields', array('type' => 'shop'));
        parent::__construct($id_gformbuilderprofields, $id_lang, $id_shop);

    }
    public static function getField($id_gformbuilderprofields,$id_lang,&$hasupload,$id_shop){
        $fieldhtml = '';
        if($id_gformbuilderprofields){
            $fieldObj = new gformbuilderprofieldsModel((int)$id_gformbuilderprofields,(int)$id_lang, (int)$id_shop);
            if(Validate::isLoadedObject($fieldObj))
            {
                $fields_value = array();
                $fields_value['type'] = $fieldObj->type;
                $fields_value['id_gformbuilderprofields'] = (int)$fieldObj->id;
                $fields_value['name'] = $fieldObj->name;
                $fields_value['labelpos'] = $fieldObj->labelpos;
                $fields_value['validate'] = $fieldObj->validate;
                $fields_value['idatt'] = $fieldObj->idatt;
                $fields_value['classatt'] = $fieldObj->classatt;
                $fields_value['required'] = (bool)$fieldObj->required;
                $fields_value['multi'] = (bool)$fieldObj->multi;
                $fields_value['dynamicval'] = '';
                    if(isset($fieldObj->dynamicval)) $fields_value['dynamicval'] = $fieldObj->dynamicval;
                if($fieldObj->type == 'slider' || $fieldObj->type == 'spinner'){
                    $_values = explode(';',$fieldObj->extra);
                    $fields_value['extra'] = array(
                            '0'=>isset($_values[0]) ? (int)$_values[0] : 0,
                            '1'=>isset($_values[1]) ? (int)$_values[1] : 100,
                            '2'=>isset($_values[2]) ? (int)$_values[2] : 1,
                            '3'=>isset($_values[3]) ? (int)$_values[3] : Tools::floorf(($fields_value['extra']['3']+$fields_value['extra']['1'])/2),
                    );
                    if(isset($_values['4']) && (int)$_values['4'] >= $fields_value['extra']['3']){
                        $fields_value['extra']['4'] = (int)$_values['4'];
                    }else{
                        $fields_value['extra']['4'] = (int)$fields_value['extra']['1'];
                    }
                }elseif($fieldObj->type == 'colorchoose' || $fieldObj->type == 'imagethumb'){
                    $fields_value['extra'] = explode(',',$fieldObj->extra);
                }else
                    $fields_value['extra'] = $fieldObj->extra;
                $fields_value['label'] = $fieldObj->label;
                if($fieldObj->type == 'survey'){
                    $values = $fieldObj->description;
                    $fields_value['description'] = preg_split("/(\r\n|\n|\r)/", $values);//explode(',',$values);
                }elseif($fieldObj->type == 'googlemap'){
                    $fields_value['description'] = Tools::htmlentitiesUTF8(gformbuilderproModel::minifyHtml($fieldObj->description));
                }else
                    $fields_value['description'] = $fieldObj->description;
                if($fieldObj->type == 'checkbox' || $fieldObj->type == 'radio' || $fieldObj->type == 'select' || $fieldObj->type == 'survey'){
                    $values = $fieldObj->value;
                    $fields_value['value'] = preg_split("/(\r\n|\n|\r)/", $values);//explode(',',$values);
                }elseif($fieldObj->type == 'selectcountry'){
                    $fields_value['value'] = array();
                    $countries =  Country::getCountries((int)$id_lang,(bool)$fieldObj->extra);
                    if($countries)
                        foreach($countries as $country){
                            $fields_value['value'][] = $country['country'];
                        }
                }else
                    $fields_value['value'] = $fieldObj->value;
                /*new version 1.3.6*/
                

                $fields_value['placeholder'] = $fieldObj->placeholder;
                $fields_value['isoldpsversion'] = 0;
                if(version_compare(_PS_VERSION_,'1.6') == -1){
                    $fields_value['isoldpsversion'] = 1;
                }
                Context::getContext()->smarty->assign($fields_value);
                $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/front/fields/'.$fieldObj->type.'.tpl';
                if($fieldObj->type == 'selectcountry'){
                    $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/front/fields/select.tpl';
                }
                if($fieldObj->type == 'fileupload' || $fieldObj->type == 'multifileupload') $hasupload = true;
                return Context::getContext()->smarty->fetch($tpl);
            }
        }
        return $fieldhtml;
    }
    public static function gValidateField($data,$validate){
        if($validate){
            $validateList = array(
                 "isAddress",
                 "isEmail",
                 "isBool",
                 "isColor",
                 "isCityName",
                 "isDate",
                 "isGenericName",
                 "isName",
                 "isCleanHtml",
                 "isUrl",
                 "isUrlOrEmpty",
                 "isInt",
                 "isUnsignedInt",
                 "isPercentage",
                 "isFloat",
                 "isUnsignedFloat",
                 "isPrice",
                 "isPasswd",
                 "isBirthDate",
                 "isPostCode",
                 "isString",
                 "isPhoneNumber"
            );
            if(in_array($validate,$validateList))
                return Validate::$validate($data);
            else
                return false;       
        }
        return true;
    }
    public static function getAllFields($fields='',$id_lang=0,$id_shop=0){
        $res = array();
        if($fields !=''){
            $fields = implode(',', array_map('intval', explode(',', $fields)));
            if($fields !=''){
                if(!$id_lang) $id_lang = (int)Context::getContext()->language->id;
                if(!$id_shop) $id_shop = (int)Context::getContext()->shop->id;
                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                    SELECT a.*,b.label
                    FROM '._DB_PREFIX_.'gformbuilderprofields as a,
                         '._DB_PREFIX_.'gformbuilderprofields_lang as b,
                         '._DB_PREFIX_.'gformbuilderprofields_shop as c
                    WHERE a.id_gformbuilderprofields = b.id_gformbuilderprofields
                    AND a.id_gformbuilderprofields = c.id_gformbuilderprofields
                    AND c.id_shop = '.(int)$id_shop.'
                    AND b.id_lang = '.(int)$id_lang.'
                    AND a.id_gformbuilderprofields IN('.pSQL($fields).')'
                );
            }
        }
        return $res;
    }
    public static function getAllFieldDatas($fields='',$id_shop=0){
        $res = array();
        if($fields !=''){
            $fields = implode(',', array_map('intval', explode(',', $fields)));
            if($fields !=''){
                if(!$id_shop) $id_shop = (int)Context::getContext()->shop->id;
                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                    SELECT * 
                    FROM '._DB_PREFIX_.'gformbuilderprofields as a,
                         '._DB_PREFIX_.'gformbuilderprofields_lang as b,
                         '._DB_PREFIX_.'gformbuilderprofields_shop as c
                    WHERE a.id_gformbuilderprofields = b.id_gformbuilderprofields
                    AND a.id_gformbuilderprofields = c.id_gformbuilderprofields
                    AND c.id_shop = '.(int)$id_shop.' 
                    AND a.id_gformbuilderprofields IN('.pSQL($fields).')'
                );
            }
        }
        return $res;
    }
    public static function getAllFieldType() {
        $fieldtype = array();
        $shortcodes = Tools::scandir(_PS_MODULE_DIR_.'gformbuilderpro/classes/fields', 'php'); 
		foreach ($shortcodes as $file)
		{
			$fileName = basename($file, '.php');
            if ($fileName == 'index') continue;
            require_once(_PS_MODULE_DIR_.'gformbuilderpro/classes/fields/'.$file);
            $objname = Tools::ucfirst($fileName).'field';
            $fieldObj = new $objname();
            $fieldtype[$fileName] = $fieldObj->getInfo();
            $fieldtype[$fileName]['config'] = $fieldObj->getConfig();
		}
        uasort($fieldtype, function($a, $b) {
            return $a['possition'] - $b['possition'];
        });
        return $fieldtype;
    }
    public static function duplicateField($id_gformbuilderprofield){
        $result = array(
            'error'=>'1',
            'warrning'=>''
        );
        if($id_gformbuilderprofield > 0){
            $gformcmsfieldsObj = new gformbuilderprofieldsModel((int)$id_gformbuilderprofield);
            if(Validate::isLoadedObject($gformcmsfieldsObj) && $gformcmsfieldsObj->type !='wholesale'){
                $field_name = $gformcmsfieldsObj->type.'_'.rand(1,99999);
                $gformcmsfieldsObjNew = $gformcmsfieldsObj->duplicateObject();
                $gformcmsfieldsObjNew->name = $field_name;
                if($gformcmsfieldsObjNew->name == $gformcmsfieldsObjNew->classatt){
                    $gformcmsfieldsObjNew->classatt = $field_name;
                }
                $gformcmsfieldsObjNew->idatt = $field_name;
                $gformcmsfieldsObjNew->save();
                $result['id_old'] = (int)$id_gformbuilderprofield;
                $result['new_shortcode'] = '[gformbuilderpro:'.(int)$gformcmsfieldsObjNew->id.']';
                $result['new_id'] = 'gformbuilderpro_'.(int)$gformcmsfieldsObjNew->id;
                $result['id'] = (int)$gformcmsfieldsObjNew->id;
                $result['field_name'] = '{'.$field_name.'}';
                $result['error'] = '0';
            } else {
                $result = array(
                    'error'=>'1',
                    'warrning'=>'wholesale'
                );
            }
        }
        return $result;
    }
}