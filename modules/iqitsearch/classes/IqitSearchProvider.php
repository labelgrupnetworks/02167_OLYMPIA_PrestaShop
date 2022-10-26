<?php
/**
 * 2017 IQIT-COMMERCE.COM
 *
 * NOTICE OF LICENSE
 *
 *  @author    IQIT-COMMERCE.COM <support@iqit-commerce.com>
 *  @copyright 2017 IQIT-COMMERCE.COM
 *  @license   Commercial license (You can not resell or redistribute this software.)
 *
 */

/**
 * @since 1.5.0
 */

 class IqitSearchProvider
 {
     public $id_lang;
     public $id_shop;
     public $context;
     public $db;

     public $words = array();

     public function __construct($context, $module, $search_string)
     {
         $this->module = $module;
         $this->db = Db::getInstance(_PS_USE_SQL_SLAVE_);

         if (!$context) {
             $this->context = Context::getContext();
         } else {
             $this->context = $context;
         }
         $this->id_lang = (int)$this->context->language->id;
         $this->id_shop = (int)$this->context->shop->id;
         $this->words = explode(' ', Search::sanitize($search_string, $this->id_lang, false, $this->context->language->iso_code));
     }


     public function getManufacturers()
     {
       $manufacturers = array();
       $result = array();

       foreach ($this->words as $key => $word){
           if (!empty($word) && strlen($word) >= (int)Configuration::get('PS_SEARCH_MINWORDLEN'))
           {
               $sql_param_search = Search::getSearchParamFromWord($word);

               $result[]= Db::getInstance()->executeS('
                 SELECT m.*
                   FROM `'._DB_PREFIX_.'manufacturer` m
                   LEFT JOIN `'._DB_PREFIX_.'manufacturer_shop` ms
                   ON m.`id_manufacturer` = ms.`id_manufacturer`
                   WHERE m.`name` LIKE \'%'.$sql_param_search.'\'
                   AND `active` = 1
                   AND ms.`id_shop` = '. $this->id_shop .'
                   GROUP BY m.`id_manufacturer`
               ');
             }
       }

       $rewriteSettings = (int) Configuration::get('PS_REWRITING_SETTINGS');

       foreach ($result as $select)
       foreach ($select as $brand) {
              $manufacturers[$brand['id_manufacturer']]['name'] = $brand['name'];
              $manufacturers[$brand['id_manufacturer']]['link'] = Context::getContext()->link->getManufacturerLink($brand['id_manufacturer'], ($rewriteSettings ? Tools::link_rewrite($brand['name']) : 0));
              $manufacturers[$brand['id_manufacturer']]['image'] = Context::getContext()->link->getManufacturerImageLink($brand['id_manufacturer'],
                  'small_default') ;
      }

       return $manufacturers;

   }

   public function getPosts($ajax = false)
   {

     if(!Module::isEnabled('ph_simpleblog')) {
         return array();
     }

       $posts = array();
       $result = array();

     foreach ($this->words as $key => $word){
         if (!empty($word) && strlen($word) >= (int)Configuration::get('PS_SEARCH_MINWORDLEN'))
         {
             $sql_param_search = Search::getSearchParamFromWord($word);
             $result[]= Db::getInstance()->executeS('
               SELECT p.id_simpleblog_post
                 FROM `'._DB_PREFIX_.'simpleblog_post` p
                 LEFT JOIN `'._DB_PREFIX_.'simpleblog_post_lang` pl
                 ON p.`id_simpleblog_post` = pl.`id_simpleblog_post`
                 LEFT JOIN `'._DB_PREFIX_.'simpleblog_post_shop` ps
                 ON p.`id_simpleblog_post` = ps.`id_simpleblog_post`
                 LEFT OUTER JOIN `'._DB_PREFIX_.'simpleblog_post_tag` pt
                 ON p.`id_simpleblog_post` = pt.`id_simpleblog_post`
                 LEFT OUTER JOIN `'._DB_PREFIX_.'simpleblog_tag` t
                 ON pt.`id_simpleblog_tag` = t.`id_simpleblog_tag`
                 AND t.`id_lang` = '. $this->id_lang .'
                 WHERE (pl.`title` LIKE \'%'.$sql_param_search.'\'
                 OR t.`name` LIKE \'%'.$sql_param_search.'\')
                 AND `active` = 1
                 AND pl.`id_lang` = '. $this->id_lang .'
                 AND ps.`id_shop` = '. $this->id_shop .'
                 GROUP BY p.`id_simpleblog_post`
                 ORDER BY p.`date_add` DESC
             ');
           }
     }

     foreach ($result as $select){
         foreach ($select as $post){
             $posts[$post['id_simpleblog_post']] = $post['id_simpleblog_post'];
         }
     }

     if (!empty($posts)){
         $posts = SimpleBlogPost::getPosts($this->id_lang, 10, null, null, true, 'sbp.date_add', 'DESC', null, false, false, null, 'in', $posts);
     }



     return $posts;

 }
 }
