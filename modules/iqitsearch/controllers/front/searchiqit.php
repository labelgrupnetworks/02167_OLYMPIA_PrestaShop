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



 require_once _PS_ROOT_DIR_ . '/modules/iqitsearch/classes/IqitSearchProvider.php';


 class IqitSearchSearchiqitModuleFrontController extends SearchControllerCore
 {
     public $extender;
     public $iqitsearch_type = 1;
     public $php_self = null;//'module-iqitsearch-searchiqit';

     //public $php_self = 'searchiqit';

     /**
      * Assign template vars related to page content.
      *
      * @see FrontController::initContent()
      */
     public function init()
         {

             $this->page_name = 'module-iqitsearch-searchiqit';
          
             parent::init();
             $this->php_self = 'module-iqitsearch-searchiqit';

             $this->search_string = Tools::getValue('s');
             if (!$this->search_string) {
                 $this->search_string = Tools::getValue('search_query');
             }
             $this->search_tag = Tools::getValue('tag');

             $this->module = Module::getInstanceByName('iqitsearch');
             $this->extender = new IqitSearchProvider($this->context, $this->module, $this->search_string);
             $this->iqitsearch_type =  Configuration::get('iqitsearch_type');


             if(!$this->ajax){
                 $posts = array();
                 $brands = array();

                 switch ($this->iqitsearch_type) {
                     case 1:
                         $posts = $this->extender->getPosts();
                         $brands = $this->extender->getManufacturers();
                         break;
                     case 2:
                         $brands = $this->extender->getManufacturers();
                         break;
                     case 3:
                         $posts = $this->extender->getPosts();
                         break;
                 }

                 $this->context->smarty->assign(
                     array(
                         'search_string' => $this->search_string,
                         'search_tag'    => $this->search_tag,
                         'brands'         => $brands,
                         'posts'         => $posts,
                         'blogLayout' => 'grid',
                         'columns' =>  Configuration::get('PH_BLOG_GRID_COLUMNS'),
                         'is_category' =>  false
                     )
                 );

             }
         }



     protected function getAjaxProductSearchVariables()
     {

         $search = $this->getProductSearchVariables();

         if($search['products']) {

             $rendered_products_top = $this->render('catalog/_partials/products-top', array('listing' => $search));
             $rendered_products = $this->render('catalog/_partials/products', array('listing' => $search));
             $rendered_products_bottom = $this->render('catalog/_partials/products-bottom', array('listing' => $search));

             $data = array_merge(
                 array(
                     'rendered_products_top' => $rendered_products_top,
                     'rendered_products' => $rendered_products,
                     'rendered_products_bottom' => $rendered_products_bottom,
                 ),
                 $search
             );
         }


         if (!empty($data['products']) && is_array($data['products'])) {
             $data['products'] = $this->prepareProductArrayForAjaxReturn($data['products']);
         } else{
             $data['products'] = [];
         }


         $posts = array();
         $brands = array();

         switch ($this->iqitsearch_type) {
             case 1:
                 $posts = $this->extender->getPosts();
                 $brands = $this->extender->getManufacturers();
                 break;
             case 2:
                 $brands = $this->extender->getManufacturers();
                 break;
             case 3:
                 $posts = $this->extender->getPosts();
                 break;
         }

         $data['blogposts'] = $posts;
         $data['brands'] = $brands;

         return $data;
     }



 }
