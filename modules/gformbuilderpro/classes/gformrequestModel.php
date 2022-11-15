<?php
/**
* The file is Model of module. Do not modify the file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright  2017 Globo., Jsc
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/

class gformrequestModel extends ObjectModel
{
    public $id_gformrequest;
    public $id_gformbuilderpro;
    public $user_ip;
    public $user_email;
    public $subject;
    public $sendto;
    public $request;
    public $attachfiles;
    public $jsonrequest;
    public $date_add;
    public $status;
    public $sender;
    public $star;
    public $viewed;
    public $id_lang;
    public $sender_name;
    public static $definition = array(
        'table' => 'gformrequest',
        'primary' => 'id_gformrequest',
        'multilang' => false,
        'fields' => array(
            //Fields
            'id_gformbuilderpro' =>  array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'user_ip'  =>  array('type' => self::TYPE_STRING,'validate' => 'isString'),
            'sendto'  =>  array('type' => self::TYPE_STRING,'validate' => 'isString'),
            'subject'  =>  array('type' => self::TYPE_STRING,'validate' => 'isString'),
            'attachfiles' =>  array('type' => self::TYPE_STRING,'validate' => 'isString'),
            'jsonrequest' =>  array('type' => self::TYPE_HTML,'validate' => 'isString'),
            'request' => 	array('type' => self::TYPE_HTML,'validate' => 'isCleanHtml'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'status' =>  array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'user_email' =>  array('type' => self::TYPE_STRING,'validate' => 'isString'),
            'sender' =>  array('type' => self::TYPE_STRING,'validate' => 'isString'),
            'sender_name' =>  array('type' => self::TYPE_STRING,'validate' => 'isString'),
            'id_lang' =>  array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'star' =>  array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'viewed' =>  array('type' => self::TYPE_INT, 'validate' => 'isInt')
        )
    );
    public function __construct($id_gformrequest = null, $id_lang = null, $id_shop = null)
    {
        if(version_compare(_PS_VERSION_,'1.5.3') != -1)
            Shop::addTableAssociation('gformrequest', array('type' => 'shop'));
        parent::__construct($id_gformrequest, $id_lang, $id_shop);

    }
    public static function getExportData($id_form=0,$id_shop=0,$from='',$to='',$status=array()){
        if(count($status) > 0){
            $_status = implode(',', array_map('intval', $status));
            if($_status == '') return array();
            $sql = 'SELECT a.*,c.id_shop FROM '._DB_PREFIX_.'gformrequest as a, '._DB_PREFIX_.'gformrequest_shop as c 
                    WHERE a.id_gformrequest = c.id_gformrequest
                        AND a.status IN ('.pSql($_status).') ';
            if($id_form > 0) $sql.=' AND a.id_gformbuilderpro = '.(int)$id_form;
            if($id_shop > 0) $sql.=' AND c.id_shop = '.(int)$id_shop;
            if($from !='') $sql.=' AND a.date_add >= \''.date('Y-m-d H:i:s',strtotime($from)).'\'';
            if($to !='') $sql.=' AND a.date_add <= \''.date('Y-m-d H:i:s',strtotime($to)).'\'';
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }else{
            return array();
        }
    }
    public static function getReceivedIds($id_form=0,$id_shop=0){
        $sql = 'SELECT GROUP_CONCAT(a.id_gformrequest)  
                FROM '._DB_PREFIX_.'gformrequest as a';
        if($id_shop > 0)
            $sql.=' LEFT JOIN  '._DB_PREFIX_.'gformrequest_shop as c ON (a.id_gformrequest = c.id_gformrequest) ';   
        $sql.=' WHERE a.id_gformbuilderpro = '.(int)$id_form;
        if($id_shop > 0) $sql.=' AND c.id_shop = '.(int)$id_shop;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }
    public static function getReceivedAttachments($id_forms = '',$id_shop=0){
        if($id_forms !=''){
            $sql = 'SELECT a.attachfiles 
                    FROM '._DB_PREFIX_.'gformrequest as a';
            if($id_shop > 0)
                $sql.=' LEFT JOIN  '._DB_PREFIX_.'gformrequest_shop as c ON (a.id_gformrequest = c.id_gformrequest) ';   
            $sql.=' WHERE a.attachfiles !="" AND a.id_gformbuilderpro IN ('.pSql(implode(',', array_map('intval', explode(',',$id_forms)))).')';
            if($id_shop > 0) $sql.=' AND c.id_shop = '.(int)$id_shop;
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
        return '';
    }
    public static function removeRequestForm($id_form,$id_shop = 0){
        if($id_shop == 0)
            $id_shop = (int)Context::getContext()->shop->id;
        $res = true;
        $requests = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT a.id_gformrequest,a.attachfiles
            FROM '._DB_PREFIX_.'gformrequest as a,
                 '._DB_PREFIX_.'gformrequest_shop as c
            WHERE a.id_gformrequest = c.id_gformrequest
            AND c.id_shop = '.(int)$id_shop.'
            AND a.id_gformbuilderpro = '.(int)$id_form
        );
        $requestsId = array();
        if($requests){
            foreach($requests as $request){
                $requestsId[] = (int)$request['id_gformrequest'];
                $attachfiles = $request['attachfiles'];
                if($attachfiles !=''){
                    $all_attachfiles = explode(',',$attachfiles);
                    foreach ($all_attachfiles as $file)
            		{
        				if ($file && file_exists(_PS_UPLOAD_DIR_.$file))
        					$res &= @unlink(_PS_UPLOAD_DIR_.$file);
            		}
                }
            }
        }
        $requestsIdString = implode(',', array_map('intval', $requestsId));
        if($requestsIdString !='' && $res){
            $res &= Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('
                DELETE FROM '._DB_PREFIX_.'gformrequest
                    WHERE  id_gformrequest IN ('.pSQL($requestsIdString).')'
            );
            $res &= Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('
                DELETE FROM '._DB_PREFIX_.'gformrequest_shop
                    WHERE  id_gformrequest IN ('.pSQL($requestsIdString).')
                    AND id_shop = '.(int)$id_shop
            );
        }
        return $res;
    }
    public function delete()
    {
        $attachfiles = $this->attachfiles;
        $res = true;
        if($attachfiles !=''){
            $all_attachfiles = explode(',',$attachfiles);
            foreach ($all_attachfiles as $file)
    		{
				if ($file && file_exists(_PS_UPLOAD_DIR_.$file))
					$res &= @unlink(_PS_UPLOAD_DIR_.$file);
    		}
        }
		
        $res &= parent::delete();
        return $res;
    }
    public static function getUnReadRequest($id_gformbuilderpro = 0){
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = 'SELECT COUNT(a.id_gformrequest) FROM '._DB_PREFIX_.'gformrequest a 
                LEFT JOIN '._DB_PREFIX_.'gformrequest_shop as c ON(a.id_gformrequest = c.id_gformrequest) 
                WHERE a.viewed !=1 AND c.id_shop = '.(int)$id_shop .($id_gformbuilderpro > 0 ? ' AND id_gformbuilderpro = '.(int)$id_gformbuilderpro : '');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }
}