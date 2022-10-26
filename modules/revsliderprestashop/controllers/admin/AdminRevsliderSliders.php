<?php

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once _PS_MODULE_DIR_ . 'revsliderprestashop/rev-loader.php';
class AdminRevsliderSlidersController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = false;
        $this->lang = false;
        parent::__construct();
    }

     public function setMedia($isNewTheme = false)
    {


        Media::addJsDef(array(
            'rev_ajaxurl' => RevLoader::getAjaxUrl(),
            'custom_admin_url' => RevLoader::getCustomAdminRUL(),
            'custom_base_url' => RevLoader::customBaseURL(),
            'project_url' => RevLoader::url(),
        ));
       $this->context->controller->addCSS(RS_PLUGIN_URL.'admin/assets/default/css/jquery-ui.css', 'all', null, false);
       $this->context->controller->addCSS(RS_PLUGIN_URL.'admin/assets/default/css/wp-color-picker.css', 'all', null, false);
       $this->context->controller->addCSS(RS_PLUGIN_URL.'admin/assets/default/css/thickbox.css', 'all', null, false);
       $this->context->controller->addCSS('//fonts.googleapis.com/css?family=Open+Sans:400,300,700,600,800', 'all', null, false);
       $this->context->controller->addCSS('//fonts.googleapis.com/css?family=Roboto', 'all', null, false);
       $this->context->controller->addCSS('//fonts.googleapis.com/icon?family=Material+Icons', 'all', null, false);
       $this->context->controller->addCSS(RS_PLUGIN_URL.'admin/assets/css/tp-color-picker.css', 'all', null, false);
       $this->context->controller->addCSS(RS_PLUGIN_URL.'admin/assets/css/select2RS.css', 'all', null, false);
       $this->context->controller->addCSS(RS_PLUGIN_URL.'public/assets/css/rs6.css', 'all', null, false);
       $this->context->controller->addCSS(RS_PLUGIN_URL.'public/assets/fonts/font-awesome/css/font-awesome.css', 'all', null, false);
       $this->context->controller->addCSS(RS_PLUGIN_URL.'public/assets/fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css', 'all', null, false);
       $this->context->controller->addCSS(RS_PLUGIN_URL.'admin/assets/css/basics.css', 'all', null, false);
       $this->context->controller->addCSS(RS_PLUGIN_URL.'admin/assets/css/builder.css', 'all', null, false);
//
//
//         $this->context->controller->addJS(RS_PLUGIN_URL.'admin/assets/default/js/jquery.js');
//         $this->context->controller->addJS(RS_PLUGIN_URL.'admin/assets/default/js/jquery-ui.js');
//         $this->context->controller->addJS(RS_PLUGIN_URL.'admin/assets/default/js/iris.min.js');
//         $this->context->controller->addJS(RS_PLUGIN_URL.'admin/assets/default/js/wp-color-picker.js');
//         $this->context->controller->addJS(RS_PLUGIN_URL.'admin/assets/default/js/updates.js');
//         $this->context->controller->addJS(RS_PLUGIN_URL.'admin/assets/default/js/thickbox.js');
//         $this->context->controller->addJS(RS_PLUGIN_URL.'admin/assets/default/js/media-upload.js');
//         $this->context->controller->addJS(RS_PLUGIN_URL.'public/assets/js/rbtools.min.js');
//         $this->context->controller->addJS(RS_PLUGIN_URL.'admin/assets/js/modules/admin.min.js');
//         $this->context->controller->addJS(RS_PLUGIN_URL.'admin/assets/js/plugins/utils.min.js');
//         $this->context->controller->addJS(RS_PLUGIN_URL.'admin/assets/js/modules/editor.min.js');
//         $this->context->controller->addJS(RS_PLUGIN_URL.'admin/assets/js/modules/overview.min.js');
//         $this->context->controller->addJS(RS_PLUGIN_URL.'public/assets/js/rs6.min.js');


        parent::setMedia();

    }
    public function initContent(){

        $this->content = $this->displayHeader();
        $this->content .=  $this->overview();
        $this->content .=   $this->displayfooter();
      
        parent::initContent();
    }




    public function overview() {

        $page = Tools::getValue('page');
        if(!$page){

            $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            Tools::redirectAdmin($actual_link.'&page=revslider');
        }

        
        ob_start();
        new RevSliderAdmin();
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
 
}