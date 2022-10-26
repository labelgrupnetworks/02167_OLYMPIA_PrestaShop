<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'revsliderprestashop/rev-loader.php';
class RevsliderPrestashop extends Module
{
    public static $Sliders_arr = array(),$rev_current_hook;
    public function __construct()
    {
        $this->name = "revsliderprestashop";
        $this->tab = 'administration';
        $this->version = '6.2.22.0';
        $this->author = 'classydevs';
        $this->need_instance = 0;
        
        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;
        $this->displayName = $this->l('Slider Revolution');
        $this->description = $this->l('Slider Revolution - Premium responsive Prestashop slider');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

        $this->confirmUninstall = $this->l('Uninstall the module?'); 
         parent::__construct();


     }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */

    public function uninstall() {
        if ( parent::uninstall() ) {
            include dirname( __FILE__ ) . '/sql/uninstall_tables.php';
            // include dirname(__FILE__) . '/sql/uninstall_tab.php';
            // $this->moduleControllerUnRegistration();
            return true;
        }

    }
    public static function getIsset($var)
    {
        return RevLoader::getIsset($var);
    }
    public function moduleControllerRegistration()
    {
        $tabvalue = array();
        include_once dirname( __FILE__ ) . '/sql/install_tab.php';
        $languages = Language::getLanguages(true);
        if (@RevsliderPrestashop::getIsset($tabvalue) && !empty($tabvalue)) {
            foreach ($tabvalue as $index => $class) {

                $tabexists = Tab::getIdFromClassName($class['class_name']);

                if ($tabexists) {
                    continue;
                }

                $tab = new Tab();
                $tab->class_name = $class['class_name'];

                if (is_string($class['id_parent']) && !empty($class['id_parent'])) {
                    $id_parent = Tab::getIdFromClassName($class['id_parent']);
                    $tab->id_parent = $id_parent;
                } else {
                    $tab->id_parent = $class['id_parent'];
                }
                $tab->module = $class['module'];

                foreach ($languages as $lang) {
                    $tab->name[$lang['id_lang']] = $class['name'];
                }

                $tab->active = $class['active'];
                $tab->add();
                if (!$tab->id) {
                    return false;
                }
            }
        }
        return true;
    }
    public static function generateSliderFromShortcode($alias =null){


        if ($alias != null) {

            $rev_slider_front = new RevSliderFront();
            RevLoader::loadAllAddons();
            $content_sliders = '';

            ob_start();
            RevLoader::do_action( 'wp_head' );
            RevLoader::do_action( 'wp_enqueue_scripts' );
            RevLoader::rev_front_print_styles();

            RevLoader::rev_front_print_head_scripts();

            RevLoader::do_action('revslider_slider_init_by_data_post',array());
            $output = new RevSliderOutput();

            $output->add_slider_to_stage($alias);
            RevLoader::do_action( 'wp_footer' );
            RevLoader::rev_front_print_footer_scripts();

            $content_sliders = ob_get_contents();

            ob_get_clean();
            return $content_sliders;

        }
    }
    public function hookOverrideLayoutTemplate( $params ) {

        $controller = Tools::getValue( 'controller' );

        if ( $controller == 'cms' ) {
            if ( isset( $this->context->smarty->tpl_vars['cms']->value['id'] ) ) {
                $id_cms  = $this->context->smarty->tpl_vars['cms']->value['id'];
                $content = &$this->context->smarty->tpl_vars['cms']->value['content'];

                $content = self::handleShortcodes( $content );
            } elseif ( isset( $this->context->controller->cms->id ) ) {
                $id_cms  = $this->context->controller->cms->id;
                $content = &$this->context->controller->cms->content;

                $content = self::handleShortcodes( $content );
            }
        }
        if ( $controller == 'product' ) {
            if ( isset( $this->context->smarty->tpl_vars['product']->value['id'] ) ) {
                $id_product        = $this->context->smarty->tpl_vars['product']->value['id'];
                $product_var       = $this->context->smarty->tpl_vars['product'];
                $product_var_place = &$this->context->smarty->tpl_vars['product'];


                $descriptionChange                 = self::handleShortcodes( $product_var->value['description'] );
                $product_var->value['description'] = $descriptionChange;
                $product_var_place                 = $product_var;

            } elseif ( isset( $this->context->controller->product->id ) ) {
                $id_product        = $this->context->smarty->tpl_vars->product->value['id'];
                $product_var       = $this->context->smarty->tpl_vars->product;
                $product_var_place = &$this->context->smarty->tpl_vars->product;


                $descriptionChange               = self::handleShortcodes( $product_var->value->description );
                $product_var->value->description = $descriptionChange;
                $product_var_place               = $product_var;
            }
        }
        // below all is for category page---------------------------------------------
        if ( $controller == 'category' ) {
            if ( isset( $this->context->smarty->tpl_vars['category']->value['id'] ) ) {
                $id_category        = $this->context->smarty->tpl_vars['category']->value['id'];
                $category_var       = $this->context->smarty->tpl_vars['category'];
                $category_var_place = &$this->context->smarty->tpl_vars['category'];


                $descriptionChange                  = self::handleShortcodes( $category_var->value['description'] );
                $category_var->value['description'] = $descriptionChange;
                $category_var_place                 = $category_var;

            } elseif ( isset( $this->context->controller->category->id ) ) {
                $id_category        = $this->context->smarty->tpl_vars->category->value['id'];
                $category_var       = $this->context->smarty->tpl_vars->category;
                $category_var_place = &$this->context->smarty->tpl_vars->category;


                $descriptionChange                = self::handleShortcodes( $category_var->value->description );
                $category_var->value->description = $descriptionChange;
                $category_var_place               = $category_var;
            }
        }

        // below all is for manufacturer page---------------------------------------------

        if ( $controller == 'manufacturer' ) {
            if ( isset( $this->context->smarty->tpl_vars['manufacturer']->value['id'] ) ) {
                $id_manufacturer        = $this->context->smarty->tpl_vars['manufacturer']->value['id'];
                $manufacturer_var       = $this->context->smarty->tpl_vars['manufacturer'];
                $manufacturer_var_place = &$this->context->smarty->tpl_vars['manufacturer'];


                $descriptionChange                      = self::handleShortcodes( $manufacturer_var->value['description'] );
                $manufacturer_var->value['description'] = $descriptionChange;
                $manufacturer_var_place                 = $manufacturer_var;

            } elseif ( isset( $this->context->controller->manufacturer->id ) ) {
                $id_manufacturer        = $this->context->smarty->tpl_vars->manufacturer->value['id'];
                $manufacturer_var       = $this->context->smarty->tpl_vars->manufacturer;
                $manufacturer_var_place = &$this->context->smarty->tpl_vars->manufacturer;


                $descriptionChange                    = self::handleShortcodes( $manufacturer_var->value->description );
                $manufacturer_var->value->description = $descriptionChange;
                $manufacturer_var_place               = $manufacturer_var;
            }
        }
        if ( $controller == 'supplier' ) {
            // below all is for supplier single page---------------------------------------------

            if ( isset( $this->context->smarty->tpl_vars['supplier']->value['id'] ) ) {
                $id_supplier        = $this->context->smarty->tpl_vars['supplier']->value['id'];
                $supplier_var       = $this->context->smarty->tpl_vars['supplier'];
                $supplier_var_place = &$this->context->smarty->tpl_vars['supplier'];


                $descriptionChange                  = self::handleShortcodes( $supplier_var->value['description'] );
                $supplier_var->value['description'] = $descriptionChange;
                $supplier_var_place                 = $supplier_var;

            } elseif ( isset( $this->context->controller->supplier->id ) ) {
                $id_supplier        = $this->context->smarty->tpl_vars->supplier->value['id'];
                $supplier_var       = $this->context->smarty->tpl_vars->supplier;
                $supplier_var_place = &$this->context->smarty->tpl_vars->supplier;


                $descriptionChange                = self::handleShortcodes( $supplier_var->value->description );
                $supplier_var->value->description = $descriptionChange;
                $supplier_var_place               = $supplier_var;
            }

            // below all is for supplier list page page---------------------------------------------

            if ( isset( $this->context->smarty->tpl_vars['brands']->value ) ) {
                $supplier_list       = $this->context->smarty->tpl_vars['brands'];
                $supplier_list_place = &$this->context->smarty->tpl_vars['brands'];
                if ( isset( $supplier_list->value[0] ) ) {
                    $index_number = 0;
                } else {
                    $index_number = 1;
                }

                $new_supplier_list = array();
                foreach ( $supplier_list->value as $supplier ) {
                    $id_supplier = $this->context->smarty->tpl_vars['brands']->value[ $index_number ]['id_supplier'];

                    $supplier_var = $this->context->smarty->tpl_vars['brands']->value[ $index_number ];


                    $descriptionChange = self::handleShortcodes( $supplier_var['description'] );

                    $supplier_var['description']        = $descriptionChange;
                    $new_supplier_list[ $index_number ] = $supplier_var;
                    $index_number++;
                }
                $supplier_list->value = $new_supplier_list;

                $supplier_list_place = $supplier_list;
            }
        }

    }

    public static  function handleShortcodes($content){
        $shortcodes = array(
            "rev_slider" => function($data){
                $content = "";

                if(isset($data["alias"])){
                    $alias = $data["alias"];

                    $slider_content = self::generateSliderFromShortcode($alias);
                    return $slider_content;
                }

                return $content;
            }
        );


        foreach($shortcodes as $key => $function){
            $dat = array();
            preg_match_all("/\[".$key." (.+?)\]/", $content, $dat);
            if(count($dat) > 0 && $dat[0] != array() && isset($dat[1])){
                $i = 0;
                $actual_string = $dat[0];
                foreach($dat[1] as $temp){
                    $temp = explode(" ", $temp);
                    $params = array();
                    foreach ($temp as $d){
                        list($opt, $val) = explode("=", $d);
                        $params[$opt] = trim($val, '"');
                    }
                    $content = str_replace($actual_string[$i], $function($params), $content);
                    $i++;
                }
            }
        }
        return $content;
    }
    public function getHooks(){
        return array(
            '' => 'Select Hook',
            'displayBanner' => 'displayBanner',
            'displayTop' => 'displayTop',
            'displayTopColumn' => 'displayTopColumn',
            'displayHome' => 'displayHome',
            'displayFullWidthTop' => 'displayFullWidthTop',
            'displayFullWidthTop2' => 'displayFullWidthTop2',
            'displayFullWidthTop' => 'displayFullWidthTop',
            'displayLeftColumn' => 'displayLeftColumn',
            'displayRightColumn' => 'displayRightColumn',
            'displayFooter' => 'displayFooter',
            'displayLeftColumnProduct' => 'displayLeftColumnProduct',
            'displayRightColumnProduct' => 'displayRightColumnProduct',
            'displayFooterProduct' => 'displayFooterProduct',
            'displayMyAccountBlock' => 'displayMyAccountBlock',
            'displayMyAccountBlockfooter' => 'displayMyAccountBlockfooter',
            'displayProductButtons' => 'displayProductButtons',
            'displayCarrierList' => 'displayCarrierList',
            'displayBeforeCarrier' => 'displayBeforeCarrier',
            'displayPaymentTop' => 'displayPaymentTop',
            'displayPaymentReturn' => 'displayPaymentReturn',
            'displayOrderConfirmation' => 'displayOrderConfirmation',
            'displayShoppingCart' => 'displayShoppingCart',
            'displayShoppingCartFooter' => 'displayShoppingCartFooter',
            'dislayMyAccountBlock' => 'dislayMyAccountBlock',
            'displayCustomerAccountFormTop' => 'displayCustomerAccountFormTop',
            'customhookname' => 'Custom Hook Name'
        );

    }
    public function hookdisplayHeader()
    {
//return;
        $sliders = $this->hookCommonCb();
        self::$Sliders_arr = $sliders;
        $css_url = "{$this->_path}public/assets/";
        $js_url = "{$this->_path}public/assets/";
        $this->context->controller->addCSS($css_url . 'css/rs6.css');

        $this->context->controller->registerJavascript('modules-revsliderprestashop-tools', 'modules/'.$this->name.'/public/assets/js/rbtools.min.js', ['position' => 'bottom', 'priority' => 1500]);
        $this->context->controller->registerJavascript('modules-revsliderprestashop-rs6', 'modules/'.$this->name.'/public/assets/js/rs6.min.js', ['position' => 'bottom', 'priority' => 1500]);
        $this->addonAssets($sliders);
    }
    public function addonAssets($sliders){


            if (!empty($sliders)) {
                ob_start();
                foreach ($sliders as $slider){
                    $slider = (object)$slider;
                    $params = Tools::jsonDecode($slider->params,true);
                    if(isset($params['addOns'])){
                        $params = $params['addOns'];
                        if (@RevsliderPrestashop::getIsset($params->template) && $params->template != 'false') {
                            continue;
                        } else {
                            if (@RevsliderPrestashop::getIsset($params->id_shop) && $params->id_shop != Shop::getContextShopID()) {
                                continue;
                            } else {
                                self::loadAddonAssetsSpeicifically($params, $slider);
                            }
                        }
                    }

                }
            }

    }
    public static function loadAddonAssetsSpeicifically($params,$slider){

        if(isset($params['revslider-paintbrush-addon']['enable'])){
            if($params['revslider-paintbrush-addon']['enable']== true){
                Context::getContext()->controller->addCSS(RS_PLUGIN_ADDONS_URL . 'revslider-paintbrush-addon/public/assets/css/revolution.addon.paintbrush.css');
                Context::getContext()->controller->registerJavascript('addon-paintbrush', 'modules/'.'revsliderprestashop'.'/addons/revslider-paintbrush-addon/public/assets/js/revolution.addon.paintbrush.min.js', ['position' => 'bottom', 'priority' => 1500]);
            }
        }

        if(isset($params['revslider-bubblemorph-addon']['enable'])){
            if($params['revslider-bubblemorph-addon']['enable']== true){
                Context::getContext()->controller->addCSS(RS_PLUGIN_ADDONS_URL . 'revslider-bubblemorph-addon/public/assets/css/revolution.addon.bubblemorph.css');
                Context::getContext()->controller->registerJavascript('addon-bubblemorph', 'modules/'.'revsliderprestashop'.'/addons/revslider-bubblemorph-addon/public/assets/js/revolution.addon.bubblemorph.min.js', ['position' => 'bottom', 'priority' => 1500]);
            }
        }

        if(isset($params['revslider-explodinglayers-addon']['enable'])){
           if($params['revslider-explodinglayers-addon']['enable']== true){
               Context::getContext()->controller->addCSS(RS_PLUGIN_ADDONS_URL . 'revslider-explodinglayers-addon/public/assets/css/revolution.addon.explodinglayers.css');
               Context::getContext()->controller->registerJavascript('addon-explodinglayers', 'modules/'.'revsliderprestashop'.'/addons/revslider-explodinglayers-addon/public/assets/js/revolution.addon.explodinglayers.min.js', ['position' => 'bottom', 'priority' => 1500]);
           }
       }

       if(isset($params['revslider-liquideffect-addon']['enable'])){
           if($params['revslider-liquideffect-addon']['enable']== true){
               Context::getContext()->controller->addCSS(RS_PLUGIN_ADDONS_URL . 'revslider-liquideffect-addon/public/assets/css/revolution.addon.liquideffect.css');
               Context::getContext()->controller->registerJavascript('addon-liquideffect', 'modules/'.'revsliderprestashop'.'/addons/revslider-liquideffect-addon/public/assets/js/pixi.min.js', ['position' => 'bottom', 'priority' => 1500]);
               Context::getContext()->controller->registerJavascript('addon-liquideffect', 'modules/'.'revsliderprestashop'.'/addons/revslider-liquideffect-addon/public/assets/js/revolution.addon.liquideffect.min.js', ['position' => 'bottom', 'priority' => 1500]);
           }
       }

       if(isset($params['revslider-particles-addon']['enable'])){
           if($params['revslider-particles-addon']['enable']== true){
               Context::getContext()->controller->addCSS(RS_PLUGIN_ADDONS_URL . 'revslider-particles-addon/public/assets/css/revolution.addon.particles.css');
               Context::getContext()->controller->registerJavascript('addon-particles', 'modules/'.'revsliderprestashop'.'/addons/revslider-particles-addon/public/assets/js/revolution.addon.particles.min.js', ['position' => 'bottom', 'priority' => 1500]);
           }
       }

       if(isset($params['revslider-beforeafter-addon']['enable'])){
        if($params['revslider-beforeafter-addon']['enable']== true){
            Context::getContext()->controller->addCSS(RS_PLUGIN_ADDONS_URL . 'revslider-beforeafter-addon/public/assets/css/revolution.addon.beforeafter.css');
            Context::getContext()->controller->registerJavascript('addon-beforeafter', 'modules/'.'revsliderprestashop'.'/addons/revslider-beforeafter-addon/public/assets/js/revolution.addon.beforeafter.min.js', ['position' => 'bottom', 'priority' => 1500]);
        }
       }

        if(isset($params['revslider-revealer-addon']['enable'])){
           if($params['revslider-revealer-addon']['enable']== true){
               //have 1 extra js
               Context::getContext()->controller->addCSS(RS_PLUGIN_ADDONS_URL . 'revslider-revealer-addon/public/assets/css/revolution.addon.revealer.css');
               Context::getContext()->controller->registerJavascript('addon-revealer', 'modules/'.'revsliderprestashop'.'/addons/revslider-revealer-addon/public/assets/js/revolution.addon.revealer.min.js', ['position' => 'bottom', 'priority' => 1500]);
           }
       }

        if(isset($params['revslider-weather-addon']['enable'])){
           if($params['revslider-weather-addon']['enable']== true){
               //Context::getContext()->controller->addCSS(RS_PLUGIN_ADDONS_URL . 'revslider-weather-addon/public/assets/css/revolution.addon.weather.css');
               //Context::getContext()->controller->registerJavascript('addon-weather', 'modules/'.'revsliderprestashop'.'/addons/revslider-weather-addon/public/assets/js/revolution.addon.weather.min.js', ['position' => 'bottom', 'priority' => 1500]);
           }
       }

       if(isset($params['revslider-filmstrip-addon']['enable'])){
        if($params['revslider-filmstrip-addon']['enable']== true){
            Context::getContext()->controller->addCSS(RS_PLUGIN_ADDONS_URL . 'revslider-filmstrip-addon/public/assets/css/revolution.addon.filmstrip.css');
            Context::getContext()->controller->registerJavascript('addon-filmstrip', 'modules/'.'revsliderprestashop'.'/addons/revslider-filmstrip-addon/public/assets/js/revolution.addon.filmstrip.min.js', ['position' => 'bottom', 'priority' => 1500]);
        }
       }

        if(isset($params['revslider-typewriter-addon']['enable'])){
           if($params['revslider-typewriter-addon']['enable']== true){
               Context::getContext()->controller->addCSS(RS_PLUGIN_ADDONS_URL . 'revslider-typewriter-addon/public/assets/css/typewriter.css');
               Context::getContext()->controller->registerJavascript('addon-typewriter', 'modules/'.'revsliderprestashop'.'/addons/revslider-typewriter-addon/public/assets/js/revolution.addon.typewriter.min.js', ['position' => 'bottom', 'priority' => 1500]);
           }
        }
          if(isset($params['revslider-polyfold-addon']['enable'])){
           if($params['revslider-polyfold-addon']['enable']== true){
               Context::getContext()->controller->addCSS(RS_PLUGIN_ADDONS_URL . 'revslider-polyfold-addon/public/assets/css/revolution.addon.polyfold.css');
               Context::getContext()->controller->registerJavascript('addon-polyfold', 'modules/'.'revsliderprestashop'.'/addons/revslider-polyfold-addon/public/assets/js/revolution.addon.polyfold.min.js', ['position' => 'bottom', 'priority' => 1500]);
           }
       }

       if(isset($params['revslider-slicey-addon']['enable'])){
           if($params['revslider-slicey-addon']['enable']== true){
               //Context::getContext()->controller->addCSS(RS_PLUGIN_ADDONS_URL . 'revslider-slicey-addon/public/assets/css/revolution.addon.slicey.css');
               Context::getContext()->controller->registerJavascript('addon-slicey', 'modules/'.'revsliderprestashop'.'/addons/revslider-bubblemorph-addon/public/assets/js/revolution.addon.slicey.min.js', ['position' => 'bottom', 'priority' => 1500]);
           }
       }

        if(isset($params['revslider-maintenance-addon']['enable'])){
           if($params['revslider-maintenance-addon']['enable']== true){
               //Context::getContext()->controller->addCSS(RS_PLUGIN_ADDONS_URL . 'revslider-maintenance-addon/public/assets/css/revolution.addon.maintenance.css');
               //Context::getContext()->controller->registerJavascript('addon-maintenance', 'modules/'.'revsliderprestashop'.'/addons/revslider-maintenance-addon/public/assets/js/revolution.addon.maintenance.min.js', ['position' => 'bottom', 'priority' => 1500]);
           }
        }
        if(isset($params['revslider-snow-addon']['enable'])){
           if($params['revslider-snow-addon']['enable']== true){
               //Context::getContext()->controller->addCSS(RS_PLUGIN_ADDONS_URL . 'revslider-maintenance-addon/public/assets/css/revolution.addon.snow.css');
               Context::getContext()->controller->registerJavascript('addon-snow', 'modules/'.'revsliderprestashop'.'/addons/revslider-snow-addon/public/assets/js/revolution.addon.snow.min.js', ['position' => 'bottom', 'priority' => 1500]);
           }
        }

       if(isset($params['revslider-duotonefilters-addon']['enable'])){
           if($params['revslider-duotonefilters-addon']['enable']== true){
               Context::getContext()->controller->addCSS(RS_PLUGIN_ADDONS_URL . 'revslider-duotonefilters-addon/public/assets/css/revolution.addon.duotonefilters.css');
               Context::getContext()->controller->registerJavascript('addon-duotonefilters', 'modules/'.'revsliderprestashop'.'/addons/revslider-duotonefilters-addon/public/assets/js/revolution.addon.duotonefilters.min.js', ['position' => 'bottom', 'priority' => 1500]);
           }
       }

       if(isset($params['revslider-panorama-addon']['enable'])){
        if($params['revslider-panorama-addon']['enable']== true){
            //need one js file
            Context::getContext()->controller->addCSS(RS_PLUGIN_ADDONS_URL . 'revslider-panorama-addon/public/assets/css/revolution.addon.panorama.css');
            Context::getContext()->controller->registerJavascript('addon-panorama', 'modules/'.'revsliderprestashop'.'/addons/revslider-panorama-addon/public/assets/js/revolution.addon.panorama.min.js', ['position' => 'bottom', 'priority' => 1500]);
        }
       }

         if(isset($params['revslider-whiteboard-addon']['enable'])){
            if($params['revslider-whiteboard-addon']['enable']== true){
                //Context::getContext()->controller->addCSS(RS_PLUGIN_ADDONS_URL . 'revslider-whiteboard-addon/public/assets/css/revolution.addon.whiteboard.css');
                Context::getContext()->controller->registerJavascript('addon-whiteboard', 'modules/'.'revsliderprestashop'.'/addons/revslider-whiteboard-addon/public/assets/js/revolution.addon.whiteboard.min.js', ['position' => 'bottom', 'priority' => 1500]);
          }
         }
         
         if(isset($params['revslider-refresh-addon']['enable'])){
            if($params['revslider-refresh-addon']['enable']== true){
                //Context::getContext()->controller->addCSS(RS_PLUGIN_ADDONS_URL . 'revslider-refresh-addon/public/assets/css/revolution.addon.refresh.css');
                Context::getContext()->controller->registerJavascript('addon-refresh', 'modules/'.'revsliderprestashop'.'/addons/revslider-refresh-addon/public/assets/js/revolution.addon.refresh.min.js', ['position' => 'bottom', 'priority' => 1500]);
          }
         }

       }

    public function install()
    {

        include_once dirname( __FILE__ ) . '/sql/install_tables.php';
        $langs    = Language::getLanguages();
        $tabvalue = array(
            array(
                'class_name' => 'AdminRevslider',
                'id_parent' => '',
                'module' => 'revsliderprestashop',
                'name' => 'Slider Revolution',
                'active' => 1,
            ),
        );
        foreach ( $tabvalue as $tab ) {
            $newtab             = new Tab();
            $newtab->class_name = $tab['class_name'];
            $newtab->module     = $tab['module'];
            $newtab->id_parent  = $tab['id_parent'];
            foreach ( $langs as $l ) {
                $newtab->name[ $l['id_lang'] ] = $this->l( $tab['name'] );
            }
            $newtab->add( true, false );
            // Db::getInstance()->execute(' UPDATE `'._DB_PREFIX_.'tab` SET `icon` = "create" WHERE `id_tab` = "'.(int)$newtab->id.'"');

        }


        $tabvalue = array(
            array(
                'class_name' => 'AdminRevsliderSliders',
                'id_parent' => Tab::getIdFromClassName('AdminRevslider'),
                'module' => 'revsliderprestashop',
                'name' => 'Slider Revolution',
                'active' => 1,
            ),
            array(
                'class_name' => 'AdminRevolutionsliderAjax',
                'id_parent' => -1,
                'module' => 'revsliderprestashop',
                'name' => 'Revolution Ajax Controller',
                'active' => 0,
            ),
            array(
                'class_name' => 'AdminRevolutionsliderFmanager',
                'id_parent' => -1,
                'module' => 'revsliderprestashop',
                'name' => 'Revolution File Manager',
                'active' => 0,
            ),
        );
        foreach ( $tabvalue as $tab ) {
            $newtab             = new Tab();
            $newtab->class_name = $tab['class_name'];
            $newtab->module     = $tab['module'];
            $newtab->id_parent  = $tab['id_parent'];
            foreach ( $langs as $l ) {
                $newtab->name[ $l['id_lang'] ] = $this->l( $tab['name'] );
            }
            $newtab->add( true, false );
            // Db::getInstance()->execute(' UPDATE `'._DB_PREFIX_.'tab` SET `icon` = "create" WHERE `id_tab` = "'.(int)$newtab->id.'"');

        }

        if (parent::install() && $this->registerHook('displayHeader') && $this->registerHook('displayBackOfficeHeader') && $this->registerHook('overrideLayoutTemplate') && $this->registerHook('displayRevSlider')  && $this->registerHook('actionShopDataDuplication')) {

            // $gethooks =  $this->getHooks();
            // foreach (array_keys($gethooks) as $hook) {
            //     if ($hook != '') {
            //         $this->registerHook($hook);
            //     }
            // }
            return true;
        }
        return false;
    }


    public function hookCommonCb()
    {
        global $wpdb;

        $sliders = $wpdb->getResults("SELECT * FROM " . $wpdb->prefix . RevSliderGlobals::TABLE_SLIDERS_NAME);
        return $sliders;
    }
    public function generateSlider($hookPosition = 'displayHome')
    {

        if(RevLoader::is_admin()){
            return;
        }

        $cache_id = 'revslider_front_' . $hookPosition;
        if (!Cache::isStored($cache_id)) {
            $sliders = self::$Sliders_arr;


            if (!empty($sliders)) {
                $rev_slider_front = new RevSliderFront();
                RevLoader::loadAllAddons();
                $content_sliders = '';

                ob_start();
                RevLoader::do_action( 'wp_head' );
                RevLoader::do_action( 'wp_enqueue_scripts' );
                RevLoader::rev_front_print_styles();

                RevLoader::rev_front_print_head_scripts();

                RevLoader::do_action('revslider_slider_init_by_data_post',array());



                foreach ($sliders as $slider){
                    $slider = (object)$slider;
                    $params = Tools::jsonDecode($slider->params, true);

                        if (@RevsliderPrestashop::getIsset($params['layout']['id_shop']) && $params['layout']['id_shop'] != Shop::getContextShopID()) {
                            continue;
                        }

                    if (isset($params['layout']['displayhook'])) {

                        if ($params['layout']['displayhook'] === $hookPosition) {
                            $output = new RevSliderOutput();
                            $slider_alias = $slider->alias;
                            $output->add_slider_to_stage($slider_alias);
                        }
                    }
                }

                RevLoader::do_action( 'wp_footer' );
                RevLoader::rev_front_print_footer_scripts();

                $content_sliders = ob_get_contents();

                ob_get_clean();
                Cache::store($cache_id, $content_sliders);
            }
        }


        return Cache::retrieve($cache_id);

    }
    function GetInner($content,$start,$end){
        $r = explode($start, $content);
        if (isset($r[1])){
            $r = explode($end, $r[1]);
            return $r[0];
        }
        return '';
    }


    public function __call($function, $args)
    {

        $hook = Tools::substr($function, 0, 4);


        if ($hook == 'hook') {
            $hook_name = Tools::substr($function, 4);

            $hook_name = lcfirst($hook_name);
            return $this->generateSlider($hook_name);
        } else {
            return false;
        }
    }

    public function hookHeader() {
        return $this->hookDisplayHeader();
    }
   
  
} 