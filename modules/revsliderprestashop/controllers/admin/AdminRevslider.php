<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminRevsliderController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = false;
        $this->lang = false;
        parent::__construct();

    }

     public function setMedia($isNewTheme = false)
    {
        parent::setMedia();
    }
    public function initContent(){
      
        $this->content = $this->displayHeader();
        $this->content .=  $this->overview();
        $this->content .=   $this->displayfooter();
      
        parent::initContent();
    }
    public function overview() {
        
        ob_start(); 
        //$productAdmin = new RevSliderAdmin();
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
 
}
