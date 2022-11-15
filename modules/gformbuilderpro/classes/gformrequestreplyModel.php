<?php
/**
* The file is Model of module. Do not modify the file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright  2017 Globo., Jsc
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/

class gformrequestreplyModel extends ObjectModel
{
    public $id_gformrequest;
    public $replyemail;
    public $subject;
    public $request;
    public $date_add;
    public static $definition = array(
        'table' => 'gformrequest_reply',
        'primary' => 'id_ps_gformrequest_reply',
        'multilang' => false,
        'multishop' => false,
        'fields' => array(
            //Fields
            'id_gformrequest' =>  array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'replyemail' =>  array('type' => self::TYPE_STRING,'validate' => 'isString'),
            'subject'  =>  array('type' => self::TYPE_STRING,'validate' => 'isString'),
            'request' => 	array('type' => self::TYPE_HTML,'validate' => 'isCleanHtml'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );
    public function __construct($id_gformrequest_reply = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_gformrequest_reply, $id_lang, $id_shop);
    }
}