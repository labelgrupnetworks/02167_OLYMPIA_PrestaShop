<?php
/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innovadeluxe SL
* @copyright 2018 Innovadeluxe SL
* @license   INNOVADELUXE
*/

if (!class_exists('WebBotChecker')) {
    require_once "libraries/webbotcheccker.php";
}

if (!class_exists('InnovaTools_2_0_0')) {
    require_once(_PS_ROOT_DIR_ . '/modules/idxcookies/libraries/innovatools_2_0_0.php');
}

require_once dirname(__FILE__).'/vendor/autoload.php';

class Idxcookies extends Module
{
    use IdxrcookiesConfig;
    use IdxrcookiesFunctions;
    use IdxrcookiesLib;

    public $contextShop;

    public $contextShopGroup;

    protected $optionsModulos = array();

    protected $optionsTemplates = array();

    private $releaseDate = '2019-10-17';

    protected $cookieName = 'idxcookiesWarningCheck';

    public $prefix;

    public $es17 = true;

    private static $rootTab = 0;

    public function __construct()
    {
        $this->name = 'idxcookies';
        $this->tab = 'front_office_features';
        $this->version = '4.8.2';
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->author_address = '0x899FC2b81CbbB0326d695248838e80102D2B4c53';
        $this->innovatabs = "";
        $this->doclink = $this->name."/doc/readme_en.pdf";
        $this->author = 'innovadeluxe';
        $this->module_key = 'c145b1e813675311c6484cd9f6fe40ef';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Deluxe Cookies Warning');
        $this->description = $this->l('Configurable warning message about using cookies in your site');
        $this->contextShop = Context::getContext()->shop->getContextShopID();
        $this->contextShopGroup = Context::getContext()->shop->getContextShopGroupID();
        $this->setModuleFile(__FILE__);
        $this->setModuleDir(dirname($this->file));
        $this->prefix = Tools::strtoupper($this->name).'_';
    }

    public function install()
    {
        include(dirname(__FILE__) . '/sql/install.php');

        return parent::install() & $this->registerHooks() && $this->installFixtures();
    }

    public function uninstall()
    {
        include(dirname(__FILE__) . '/sql/uninstall.php');

        return $this->uninstallFixtures() && parent::uninstall();
    }

    public function getContent()
    {
        $output = $this->innovaTitle();
        $output .= $this->postProcess() . $this->renderForm();
        return $output;
    }

    public function postProcess()
    {
        $this->checktables(); //mnw
        return $this->processConfig();
    }

    public function renderForm()
    {
        return InnovaTools_2_0_0::adminTabWrap($this);
    }

    public function hookDisplayHeader($params)
    {
        if (!$this->active) {
            return '';
        }
        $this->setMediaJsFront();
        $this->rcpTagManagerCompatibility();
        $this->context->controller->addJqueryPlugin('fancybox');
        $this->context->controller->addJS($this->_path.'views/js/js.cookie.js', 'all');
        $this->context->controller->addCSS($this->_path.'views/css/'.$this->name.'-1.0.css', 'all');
        $this->context->controller->addJS($this->_path.'views/js/idxrcookies.js', 'all');
        return $this->displayTemplates('displayHeader');
    }

    public function hookDisplayBeforeBodyClosingTag($params)
    {
        return $this->displayTemplates('displayBeforeBodyClosingTag');
    }

    public function hookDisplayAfterBodyOpeningTag($params)
    {
        return $this->displayTemplates('displayAfterBodyOpeningTag').$this->displayTop();
    }

    //deprecated in 1.7
    public function hookDisplayTop($params)
    {
        if ($this->es17) {
            return '';
        }
        return $this->displayTop();
    }

    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('controller') == 'AdminModules' && Tools::getValue('configure') == $this->name) {
            $this->installAdminTab('AdminIdxrcookies');
            $this->setMediaJsBack();
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
            $this->context->controller->addJquery();
            $this->context->controller->addJS($this->_path.'views/js/typeahead.min.js');
            $this->context->controller->addJS($this->_path.'views/js/back-1.2.js');
        }
    }

    public function setInnovaTabs()
    {
        $isoLinks = InnovaTools_2_0_0::getIsoLinks($this);

        $tab = 'main';
        if (Tools::isSubmit('renderTabCookieTypes')) {
            $tab = 'renderTabCookieTypes';
        } elseif (Tools::isSubmit('submitNewcookie')) {
            $tab = 'renderCookieAudit';
        } elseif (Tools::isSubmit('renderTabCookies')) {
            $tab = 'renderTabCookies';
        } elseif (Tools::isSubmit('addidxcookies_template') ||
                  Tools::isSubmit('updateidxcookies_template') ||
                  Tools::isSubmit('updateidxcookies_template') ||
                  Tools::isSubmit('renderTabTemplates') ||
                  Tools::isSubmit('deleteidxcookies_template')
                 ) {
            $tab = 'renderTabTemplates';
        }

        $this->innovatabs = array();
        $this->innovatabs [] = array(
            "title" => $this->l('Configuration'),
            "icon" => "wrench",
            "link" => "renderTabConfiguracion",
            "type" => "tab",
            "show" => "both",
            "active" => $tab == 'main'?true:false,
        );
        $this->innovatabs [] = array(
            "title" => $this->l('Cookie Audit'),
            "icon" => "search",
            "link" => "renderCookieAudit",
            "type" => "tab",
            "show" => "both",
            "active" => Tools::isSubmit('renderTabCookieAudit') ? true : false,
        );
        $this->innovatabs [] = array(
            "title" => $this->l('Cookies'),
            "icon" => "asterisk",
            "link" => "renderTabCookies",
            "type" => "tab",
            "show" => "both",
            "active" =>  Tools::isSubmit('renderTabCookies') ? true : false,
        );
        $this->innovatabs [] = array(
            "title" => $this->l('Cookie Types'),
            "icon" => "list",
            "link" => "renderTabCookieTypes",
            "type" => "tab",
            "show" => "both",
            "active" => Tools::isSubmit('renderTabCookieTypes') ? true : false,
        );

        $this->innovatabs [] = array(
            "title" => $this->l('Templates'),
            "icon" => "code",
            "link" => "renderTabTemplates",
            "type" => "tab",
            "show" => "both",
            "active" =>  Tools::isSubmit('renderTabTemplates') ? true : false,
        );

        $this->innovatabs [] = array(
            "title" => $this->l('Documentation'),
            "icon" => "file",
            "link" => $this->doclink,
            "type" => "doc",
            "show" => "both",
        );
        $this->innovatabs [] = array(
            "title" => $this->l('Support'),
            "icon" => "life-saver",
            "link" => $isoLinks["support"],
            "type" => "url",
            "show" => "whmcs",
        );

        $this->innovatabs [] = array(
            "title" => $this->l('Our Modules'),
            "icon" => "cubes",
            "link" => $isoLinks["ourmodules"],
            "type" => "url",
            "show" => "both",
        );
    }

    public function innovaTitle()
    {
        //tabs version
        $innovaTabs = "";
        if (method_exists(get_class($this), "setInnovaTabs")) {
            $innovaTabs=$this->setInnovaTabs();
        }
        $this->smarty->assign(array(
            "module_dir" => $this->_path,
            "module_name" => $this->displayName,
            "module_description" => $this->description,
            "isoLinks" => InnovaTools_2_0_0::getIsoLinks($this),
            "isAddons" => InnovaTools_2_0_0::isAddons($this),
            "tabs" => InnovaTools_2_0_0::getVersionTabs($this),
        ));

        $this->context->controller->addCSS(($this->_path)."views/css/backinnova.css", "all");
        return $this->display(__FILE__, "views/templates/admin/innova-title.tpl");
    }
}
