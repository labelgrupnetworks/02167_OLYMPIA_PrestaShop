<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innovadeluxe SL
 * @copyright 2019 Innovadeluxe SL

 * @license   INNOVADELUXE
 */

use IdxrcookiesCookie as Cookie;
use IdxrcookiesCookieType as CookieType;
use IdxrCookiesConfiguracion as Configuracion;

trait IdxrcookiesFunctions
{
    private $file;

    private $dir;

    private $disabledModules;

    public function getModuleDir()
    {
        return $this->dir;
    }
    public function setModuleDir($dir)
    {
        $this->dir = $dir;
    }

    public function getModuleFile()
    {
        return $this->file;
    }

    public function setModuleFile($file)
    {
        $this->file = $file;
    }

    //mnw init
    public function checktables()
    {
        $table = $this->name;
        $colums_to_check = array(
            $table => array(
                array(
                    'name' => 'id_shop',
                    'type' => 'int(11) unsigned NOT NULL DEFAULT 0',
                    'update' => (int)Context::getContext()->shop->id
                )
            ),
        );

        foreach ($colums_to_check as $table => $columns) {
            foreach ($columns as $column) {
                $exist = Db::getInstance()->executeS('SHOW COLUMNS FROM ' . _DB_PREFIX_ . pSQL($table) . ' where Field = "' . pSQL($column['name']) .'" ;');
                if (!$exist) {
                    $updateRun=false;
                    $add_sql = 'ALTER TABLE ' . _DB_PREFIX_ . pSQL($table) . ' ADD '.  pSQL($column['name']) . ' ' . pSQL($column['type']) . ';';
                    if (isset($column['update'])) {
                        $update_sql = 'UPDATE '._DB_PREFIX_. pSQL($table) . ' set ' . pSQL($column['name']) . ' = ' . pSQL($column['update']);
                        $updateRun=true;
                    }
                    Db::getInstance()->execute($add_sql);
                    if ($updateRun) {
                        Db::getInstance()->execute($update_sql);
                    }
                }
            }
        }
    }

    protected function registerHooks()
    {
        $hooks = array(
            'displayHeader',
            'backOfficeHeader',
            'displayTop',
        );
        if ($this->es17) {
            $hooks[] = 'displayBeforeBodyClosingTag';
            $hooks[] = 'displayAfterBodyOpeningTag';
        } else {
            $hooks[] = 'displayFooter';
        }
        $return = true;
        foreach ($hooks as $hook) {
            $return &= $this->registerHook($hook);
        }
        return $return;
    }


    //mnw end

    public function installFixtures()
    {
        $cookieTypesDefault = CookieType::getDefaults();
        foreach ($cookieTypesDefault as $key => $type) {
            $cookieType = CookieType::getInstanceByKey($key);
            $cookieType->imperative = (bool)$type['imperative'];
            if (!Validate::isLoadedObject($cookieType)) {
                $cookieType->imperative = (bool) $type['imperative'];
                foreach ($type as $lang => $data_lang) {
                    if ($lang != 'imperative') {
                        $id_lang = Language::getIdByIso($lang);
                        if ($id_lang) {
                            $cookieType->name[$id_lang] = $data_lang['name'];
                            $cookieType->description[$id_lang] = $data_lang['description'];
                        }
                    }
                }
                try {
                    $cookieType->save();
                    $cookieType->setKey($key);
                } catch (Exception $e) {
                    $this->logError($e->getMessage());
                }
            }
        }
        $configDefault = Configuracion::getValoresIniciales($this);
        foreach ($configDefault as $key => $value) {
            if (in_array($key, array('TEXT', 'INFOTEXT', 'DELETECOOKIESTEXT'))) {
                $html = true;
            } else {
                $html = false;
            }
            Configuration::updateValue($this->prefix.$key, $value, $html);
        }
        return true;
    }

    public function uninstallFixtures()
    {
        $config = Configuracion::getVariablesConfiguracion($this);
        foreach ($config as $key => $item) {
            Configuration::deleteByName($this->prefix.$key);
        }
        return true;
    }

    protected function displayTop()
    {
        if ($this->active) {
            $bot =  new WebBotChecker();
            if ((bool)$bot->isThatBot() == true) {
                return false;
            }
            $cookie_path = trim(__PS_BASE_URI__, '/\\').'/';
            if ($cookie_path[0] != '/') {
                $cookie_path = '/'.$cookie_path;
            }
            $cookie_path = rawurlencode($cookie_path);
            $cookie_path = str_replace('%2F', '/', $cookie_path);
            $cookie_path = str_replace('%7E', '~', $cookie_path);
            $active_lang = $this->context->language->id;
            $variables = array(
                'idxrcookiesConf' => array(
                    'rejectButton' => (bool)Configuration::get($this->prefix.'REJECT_BUTTON', null, $this->contextShopGroup, $this->contextShop),
                    'fixedButton' => (bool)Configuration::get($this->prefix.'FIXED_BUTTON', null, $this->contextShopGroup, $this->contextShop),
                    'buttonPosition' => Configuration::get($this->prefix.'BUTTON_POSITION', null, $this->contextShopGroup, $this->contextShop),
                    'cookiesTabs' => $this->getCookiesTabs(),
                    'audit' => Tools::getValue('audit'),
                    'cookiesUrl' => Configuration::get(
                        $this->prefix.'COOKIES_URL',
                        $active_lang,
                        $this->contextShopGroup,
                        $this->contextShop
                    ),
                    'cookiesUrlTitle' => Configuration::get(
                        $this->prefix.'COOKIES_URL_TITLE',
                        $active_lang,
                        $this->contextShopGroup,
                        $this->contextShop
                    )
                )
            );
            return $this->displayTemplate('front/'.$this->name.'.tpl', $variables);
        }
    }

    protected function displayTemplates($hook, $banned = null)
    {
        try {
            $templates = IdxrcookiesTemplate::listado(array(
                'id_shop' => (int)$this->contextShop,
                'id_lang' => (int)$this->context->language->id,
                'hook' => pSQL($hook),
                'active' => 1
            ), array());
            if (is_array($templates) && !empty($templates)) {
                $where = " AND (id_cookie_type > 0 AND id_cookie_type NOT IN (select id_cookie_type FROM "._DB_PREFIX_.$this->name."_type WHERE imperative = 1)) AND id_template > 0";
                if (isset($_COOKIE[$this->cookieName])) {
                    $cookieInfo = Tools::jsonDecode($_COOKIE[$this->cookieName]);
                } elseif (is_array($banned)) {
                    $cookieInfo = new StdClass;
                    $cookieInfo->banned = $banned;
                } else {
                    return '';
                }
                foreach ($templates as $index => $template) {
                    if (isset($cookieInfo->date) && Validate::isDate($cookieInfo->date)) {
                        $sql = "SELECT id_cookie FROM "._DB_PREFIX_.$this->name." WHERE  (id_template = ".(int)$template['id_template'].$where.')';
                        if (isset($cookieInfo->banned) && !empty($cookieInfo->banned)) {
                            $sql .= ' OR date_upd > "'.pSQL($cookieInfo->date).'"';
                        } elseif (isset($cookieInfo->banned) && empty($cookieInfo->banned)) {
                            $sql = "SELECT id_cookie FROM "._DB_PREFIX_.$this->name." WHERE  (id_template = ".(int)$template['id_template'].' AND date_upd > "'.pSQL($cookieInfo->date).'")';
                        }
                    } else {
                        $date = pSQL($this->releaseDate);
                        $sql = "SELECT id_cookie FROM "._DB_PREFIX_.$this->name." WHERE  (id_template = ".(int)$template['id_template'].$where.') AND date_upd > "'.$date.'"';
                    }
                    if (isset($cookieInfo->banned) && is_array($cookieInfo->banned)) {
                        $ids = Db::getInstance()->executeS($sql);
                        if (is_array($ids)) {
                            foreach ($ids as $item) {
                                if ($item['id_cookie'] > 0 && in_array($item['id_cookie'], $cookieInfo->banned)) {
                                    unset($templates[$index]);
                                }
                            }
                        }
                    } elseif (!isset($cookieInfo->banned)) {
                        $ids = Db::getInstance()->executeS($sql);
                        if (is_array($ids)) {
                            foreach ($ids as $item) {
                                if ($item['id_cookie'] > 0) {
                                    unset($templates[$index]);
                                }
                            }
                        }
                    } else {
                        $id_cookie = (int)Db::getInstance()->getValue($sql);
                        if ($id_cookie > 0) {
                            unset($templates[$index]);
                        }
                    }
                }
            }
            return $this->displayTemplate('hook/header.tpl', array('idxrcookiesTemplates' => array_values($templates)));
        } catch (Exception $e) {
            $this->logError($e->getMessage(), array('severity' => 3));
            return '';
        }
    }

    public function getCmsSelectLinks($lang = null)
    {
        if (!$lang) {
            $lang = $this->context->language->id;
        }
        $id_shop = Context::getContext()->shop->id;
        $cms_pages = CMS::getCMSPages($lang, null, null, $id_shop);
        $cms_options = array();
        foreach ($cms_pages as $cms) {
            $option = array();
            $option['name'] = $cms['meta_title'];
            $option['id'] = 'cms_'.$cms['id_cms'];
            $link_id_array = explode('_', $option['id'], 2);
            $final_link = $this->context->link->getCMSLink($link_id_array[1]);
            $option['id'] = $final_link;
            $cms_options[] = $option;
        }

        $cms_options[] = array("name"=> $this->l("Select CMS"), "id"=>0 );

        return $cms_options;
    }

    public function getCookies($id_cookie_type = false)
    {
        return Cookie::getCookies($id_cookie_type);
    }

    public function saveCookies($cookies)
    {
        return Cookie::saveCookies($cookies);
    }

    public function getCookieTypes($id_cookie_type = false, $lang_id = false)
    {
        return CookieType::getCookieTypes($id_cookie_type, $lang_id);
    }

    public function getCookiesTabs()
    {
        $cookies_types = $this->getCookieTypes();
        foreach ($cookies_types as &$type) {
            //$type['description']
            $type['cookies'] = $this->getCookies($type['id_cookie_type']);
        }

        $infotext = urldecode(Configuration::get(
            Tools::strtoupper($this->name.'_INFOTEXT'),
            $this->context->language->id
        ));

        $deleteCookiesText = urldecode(Configuration::get(
            Tools::strtoupper($this->name.'_DELETECOOKIESTEXT'),
            $this->context->language->id
        ));

        $this->smarty->assign(array(
            'cookiesSelected' => Configuration::get($this->prefix.'COOKIES_SELECTED', null, $this->contextShopGroup, $this->contextShop),
            "cookies_types" => $cookies_types,
            'idxrcookies_CookiesInfoText' => $infotext,
            'idxrcookies_CookiesDeleteCookiesText' => $deleteCookiesText,
        ));
        return $this->display(__FILE__, 'views/templates/front/'.$this->name.'tabs.tpl');
    }

    protected function getDomain()
    {
        $r = '!(?:(\w+)://)?(?:(\w+)\:(\w+)@)?([^/:]+)?(?:\:(\d*))?([^#?]+)?(?:\?([^#]+))?(?:#(.+$))?!i';
        preg_match($r, Tools::getHttpHost(false, false), $out);
        if (preg_match(
            '/^(((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]{1}[0-9]|[1-9]).)'.
            '{1}((25[0-5]|2[0-4][0-9]|[1]{1}[0-9]{2}|[1-9]{1}[0-9]|[0-9]).)'.
            '{2}((25[0-5]|2[0-4][0-9]|[1]{1}[0-9]{2}|[1-9]{1}[0-9]|[0-9]){1}))$/',
            $out[4]
        )
           ) {
            return false;
        }
        if (!strstr(Tools::getHttpHost(false, false), '.')) {
            return false;
        }
        $domain = $out[4];

        return $domain;
    }

    protected function getCookiePath()
    {
        $cookie_path = trim(__PS_BASE_URI__, '/\\').'/';
        if ($cookie_path[0] != '/') {
            $cookie_path = '/'.$cookie_path;
        }
        $cookie_path = rawurlencode($cookie_path);
        $cookie_path = str_replace('%2F', '/', $cookie_path);
        $cookie_path = str_replace('%7E', '~', $cookie_path);
        return $cookie_path;
    }

    protected function getNextPageAudit()
    {
        //mnw init
        $active_lang = $this->context->language->id;
        $id_shop = Context::getContext()->shop->id;
        switch ($this->context->controller->php_self) {
            case 'index':
                $category = Category::getHomeCategories($active_lang)[0];
                $next_page = $this->context->link->getCategoryLink($category['id_category'], null, null, null, $id_shop).'?audit=true';
                break;
            case 'category':
                $product = Product::getProducts($active_lang, 0, 1, 'id_product', 'DESC', false, true);
                $next_page = $this->context->link->getProductLink($product[0]['id_product'], null, null, null, null, $id_shop).'?audit=true';
                break;
            case 'product':
                $cms = CMS::getCMSPages();
                $next_page = $this->context->link->getCMSLink($cms[0]['id_cms'], null, null, null, $id_shop).'?audit=true';
                break;
            case 'cms':
                $next_page = $this->context->link->getPageLink('stores', false, null, 'audit=true', false, $id_shop);
                break;
            case 'stores':
                if (Module::isEnabled('onepagecheckoutps')) {
                    $next_page = Configuration::get(Tools::strtoupper($this->name).'_return_url');
                } else {
                    $next_page = $this->context->link->getPageLink('order', false, null, 'audit=true', false, $id_shop);
                }
                break;
            case 'order':
                $next_page = Configuration::get(Tools::strtoupper($this->name).'_return_url');
                break;
            default:
                $next_page = Configuration::get(Tools::strtoupper($this->name).'_return_url');
                break;
        }
        return $next_page;
    }

    protected function setMediaJsBack()
    {
        $params = array(
            'module_path' => $this->_path,
            'urlAjax' => $this->context->link->getAdminLink('AdminIdxrcookies').'&ajax=true',
            'textoOk' => $this->l('Configuration updated correctly'),
            'textoKo' => $this->l('There was an error updating the configuration'),
            'textoError' => $this->l('There was an error while trying to do the action'),
            'urlFormTemplatesBack' => AdminController::$currentIndex . '&configure=' . $this->name.'&renderTabTemplates&token='.Tools::getAdminTokenLite('AdminModules'),
            'urlFormCookiesBack' => AdminController::$currentIndex . '&configure=' . $this->name.'&renderTabCookies&token='.Tools::getAdminTokenLite('AdminModules'),
            'urlFormCookiesTypeBack' => AdminController::$currentIndex . '&configure=' . $this->name.'&renderTabCookieTypes&token='.Tools::getAdminTokenLite('AdminModules'),
            'prefijoModulo' => $this->prefix,
        );
        $this->addJsDef($params, 'back');
    }

    protected function setMediaJsFront()
    {
        if (Tools::isSubmit('audit')) {
            $next_page = $this->getNextPageAudit();
        } else {
            $next_page = '';
        }
        $cookie_path = $this->getCookiePath();
        $cookies = $this->getCookies();
        $active_lang = $this->context->language->id;
        Media::addJsDef(array('ajaxUrl' => $this->context->shop->getBaseURI() . 'modules/' . $this->name . '/ajax.php',));
        if (isset($_COOKIE[$this->cookieName])) {
            try {
                $cookieInfo = Tools::jsonDecode($_COOKIE[$this->cookieName]);
                if (isset($cookieInfo->date) && Validate::isDate($cookieInfo->date)) {
                    $date = pSQL($cookieInfo->date);
                } else {
                    $date = pSQL($this->releaseDate);
                }
                $sql = "SELECT count(*) FROM "._DB_PREFIX_.$this->name." WHERE 1 AND (date_upd > '".$date."') AND (id_cookie_type > 0)";
                if ((int)Db::getInstance()->getValue($sql) > 0) {
                    $forceDialog = true;
                } else {
                    $forceDialog = false;
                }
            } catch (Exception $e) {
                $this->logError($e->getMessage(), array('severity' => 3));
                $forceDialog = false;
            }
        } else {
            $forceDialog = true;
        }
        $this->context->cookie->__set($this->name.'ajaxToken', Tools::getToken());
        $php_self = null;
        if (isset($this->context->controller->php_self)) {
            $php_self = $this->context->controller->php_self;
        }
        if ($php_self === 'product') {
            $id_product = (int)Tools::getValue('id_product');
        } else {
            $id_product = null;
        }
        $params = array(
            'urlAjax' =>$this->context->link->getModuleLink($this->name, 'ajax', array(
                'ajax' => true,
                'token' => Tools::getToken()
            )),
            'forceDialog' => $forceDialog,
            'userOptions' => array(
                'date' => date('Y-m-d H:i:s'),
                'divColor' => Configuration::get($this->prefix.'DIV_COLOR', null, $this->contextShopGroup, $this->contextShop),
                'textColor' => Configuration::get($this->prefix.'TEXT_COLOR', null, $this->contextShopGroup, $this->contextShop),
                'divPosition' => Configuration::get($this->prefix.'DIV_POSITION', null, $this->contextShopGroup, $this->contextShop),
                'cookiesUrl' => urlencode(Configuration::get($this->prefix.'COOKIES_URL', $active_lang, $this->contextShopGroup, $this->contextShop)),
                'cookiesUrlTitle' => Configuration::get(
                    $this->prefix.'COOKIES_URL_TITLE',
                    $active_lang,
                    $this->contextShopGroup,
                    $this->contextShop
                ),
                'cookiesText' => Configuration::get(
                    $this->prefix.'TEXT',
                    $active_lang,
                    $this->contextShopGroup,
                    $this->contextShop
                ),
                'cookiesInfoText' => Configuration::get(
                    $this->prefix.'INFOTEXT',
                    $active_lang,
                    $this->contextShopGroup,
                    $this->contextShop
                ),
                'cookieName' => $this->cookieName,
                'cookiePath' => $cookie_path,
                'cookieDomain' => $this->getDomain(),
                'okText' => $this->l('Accept', 'Functions'),
                'koText' => $this->l('Reject', 'Functions'),
                'acceptSelectedText' => $this->l('Accept selected', 'Functions'),
                'reject_button' => (bool)Configuration::get($this->prefix.'REJECT_BUTTON', null, $this->contextShopGroup, $this->contextShop),
                'accept_selected_button' => (bool)Configuration::get($this->prefix.'ACCEPT_SELECTED_BUTTON', null, $this->contextShopGroup, $this->contextShop),
                'fixed_button' => Configuration::get($this->prefix.'FIXED_BUTTON', null, $this->contextShopGroup, $this->contextShop),
                'button_position' => Configuration::get($this->prefix.'BUTTON_POSITION', null, $this->contextShopGroup, $this->contextShop),
                'reload' => (bool)Configuration::get($this->prefix.'RELOAD', null, $this->contextShopGroup, $this->contextShop),
                'blockUserNav' => (bool)Configuration::get($this->prefix.'BLOCK_USER_NAV', null, $this->contextShopGroup, $this->contextShop)
            ),
            'audit' => Tools::isSubmit('audit') ? true : false,
            'audit_next_page' => $next_page,
            'cookies_list' => $cookies,
            'php_self' => $php_self,
            'id_product' => $id_product,
        );
        $this->addJsDef($params, 'front');
    }

    public function getDisabledModules()
    {
        if (isset($this->context->employee) || Tools::isSUbmit('audit')) {
            return array();
        }
        try {
            if (is_array($this->disabledModules)) {
                return $this->disabledModules;
            }
            if (isset($_COOKIE[$this->cookieName])) {
                $cookieInfo = Tools::jsonDecode($_COOKIE[$this->cookieName]);
                if (isset($cookieInfo->banned) && is_array($cookieInfo->banned)) {
                    if (empty($cookieInfo->banned)) {
                        $bannedImploded = '';
                    } else {
                        $bannedImploded = implode(",", $cookieInfo->banned);
                    }
                }
                if (isset($cookieInfo->date) && Validate::isDate($cookieInfo->date)) {
                    $date = pSQL($cookieInfo->date);
                } else {
                    $date = pSQL($this->releaseDate);
                }
                if ($bannedImploded == '') {
                    $where = " AND date_upd >= '".$date."'";
                } else {
                    $where = " AND (id_cookie IN (".pSQL($bannedImploded).") OR date_upd > '".$date."')";
                }
            } else {
                $where = '';
            }
            $sql = "SELECT module FROM "._DB_PREFIX_.$this->name." WHERE 1 ".$where." AND (id_cookie_type > 0 AND id_cookie_type NOT IN (select id_cookie_type FROM "._DB_PREFIX_.$this->name."_type WHERE imperative = 1)) AND module IS NOT null AND module != '' GROUP BY module";
            $modulos = array();
            if ($reg = Db::getInstance()->executeS($sql)) {
                foreach ($reg as $elemento) {
                    $modulos[] = $elemento['module'];
                }
            }
            $this->disabledModules = $modulos;
            return $modulos;
        } catch (Exception $e) {
            $this->logError($e->getMessage(), array('severity' => 3));
            return array();
        }
    }

    public function filterHookModuleExecList($module_list, $hook_name)
    {
        $disabled_modules = $this->getDisabledModules();
        if (empty($disabled_modules)) {
            return $module_list;
        }
        $cleanCache = false;
        foreach ($module_list as $key => $module) {
            if (in_array($module['module'], $disabled_modules) && $module['module'] != $this->name) {
                $cleanCache = true;
                unset($module_list[$key]);
            }
        }
        return array_values($module_list);
    }

    public function logError($message, $customOptions = array(), $forceCustomLogger = true)
    {
        if (empty($message)) {
            return;
        }
        if (isset($customOptions['severity'])) {
            $severity = $customOptions['severity'];
        } else {
            $severity = 1;
        }
        if (isset($customOptions['object_type'])) {
            $object_type = $customOptions['object_type'];
        } else {
            $object_type = null;
        }
        if (isset($customOptions['object_id'])) {
            $object_id = $customOptions['object_id'];
        } else {
            $object_id = null;
        }
        if (isset($customOptions['error_code'])) {
            $error_code = $customOptions['error_code'];
        } else {
            $error_code = null;
        }
        if (isset($customOptions['allowDuplicate'])) {
            $allowDuplicate = $customOptions['allowDuplicate'];
        } else {
            $allowDuplicate = false;
        }
        if (isset($customOptions['idEmployee'])) {
            $idEmployee = $customOptions['idEmployee'];
        } else {
            $idEmployee = null;
        }

        PrestaShopLogger::addLog('[MODULE] '.Tools::strtoupper($this->name).': '.$message, $severity, $error_code, $object_type, $object_id, $allowDuplicate, $idEmployee);
    }

    protected function addJsDef($variables, $context = 'front')
    {
        Media::addJsDef(array(
            'IdxrcookiesConfig'.Tools::ucfirst($context) => $variables
        ));
    }

    protected function installAdminTab($clase, $title = '', $parent = -1)
    {
        $id_tab = (int)Tab::getIdFromClassName($clase);
        if ($id_tab) {
            return new Tab((int)$id_tab);
        }
        if (empty($title)) {
            $title = $clase;
        }
        if ($parent == self::$rootTab && $this->es17) {
            $parent = (int)Tab::getIdFromClassName('CONFIGURE');
        } elseif (!is_numeric($parent)) {
            $parent = (int)Tab::getIdFromClassName($parent);
        }
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $clase;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $title;
        }
        $tab->id_parent = $parent;
        $tab->module = $this->name;
        try {
            $tab->add();
            return $tab;
        } catch (Exception $e) {
            return false;
        }
    }

    protected function uninstallAdminTab($clase)
    {
        $id_tab = (int)Tab::getIdFromClassName($clase);
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        return true;
    }

    public function displayInformation($information)
    {
        return $this->fetchTemplate('admin/information.tpl', array('message' => $information));
    }

    public function ajaxProcessGetAjaxTemplates()
    {
        $banned = Tools::getValue('banned', array());
        $header = $this->displayTemplates('displayHeader', $banned).$this->displayModulesTemplates('header', $banned);
        $footer = $this->displayTemplates('displayFooter', $banned).$this->displayModulesTemplates('footer', $banned);
        if(Module::isEnabled('rc_pgtagmanager')){
            $scripts = array(
                __PS_BASE_URI__.'modules/rc_pgtagmanager/views/js/hook/RcTagManagerLib.js'
            );
        }else{
            $scripts = array();
        }
        return array(
            'header' => $header,
            'footer' => $footer,
            'scripts' => $scripts,
        );
    }

    protected function displayModulesTemplates($hook, $banned)
    {
        try {
            $cookies = $this->getCookies();
            if (!is_array($banned)) {
                $banned = array();
            }
            $php_self = Tools::getValue('php_self');
            $controller = Tools::getValue('controller');
            $content = '';
            if (is_array($cookies) && !empty($cookies)) {
                foreach ($cookies as $cookie) {
                    if (in_array($cookie['id_cookie'], $banned)) {
                        continue;
                    }
                    if ($cookie['module'] === 'ps_googleanalytics') {
                        $module = Module::getInstanceByName('ps_googleanalytics');
                        switch ($php_self) {
                           case 'index':
                           if ($hook === 'header') {
                               $content = $module->hookDisplayHeader(array()).$module->hookDisplayHome();
                           } elseif ($hook === 'footer') {
                               $content = $module->hookDisplayFooter();
                           }
                           // no break
                           case 'product':
                            if ($hook === 'header') {
                                $content = $module->hookDisplayHeader(array());
                            } elseif ($hook === 'footer') {
                                $product = new Product((int)Tools::getValue('id_product'), true, $this->context->language->id, $this->contextShop);
                                $_POST['controller'] = 'product';
                                $content = $module->hookDisplayFooter().$module->hookDisplayFooterProduct(array(
                                    'product' => $product
                                ));
                                $_POST['controller'] = $controller;
                            }
                            break;
                            default:
                            if ($hook === 'header') {
                                $content .= $module->hookDisplayHeader(array());
                            } elseif ($hook === 'footer') {
                                $content .= $module->hookDisplayFooter();
                            }
                            break;
                       }
                    }elseif($cookie['module'] === 'rc_pgtagmanager'){
                        $_GET['controller'] = $php_self;
                        $module = Module::getInstanceByName('rc_pgtagmanager');
                        if($hook == 'header'){
                            $content .= $module->hookHeader();
                        }elseif ($hook === 'footer'){
                            $data = Tools::getValue('rcpgTagManagerVars');
                            $this->context->smarty->assign($data);
                            $content .= $module->hookDisplayBeforeBodyClosingTag();
                        }
                        $_GET['controller'] = $controller;
                    }
                }
            }
            return $content;
        } catch (Exception $e) {
            return '';
        }
    }

    protected function rcpTagManagerCompatibility()
    {
        if (!Module::isEnabled('rc_pgtagmanager')) {
            return;
        }
        $controller = Tools::getValue('controller');
        $controllers_with_product_lists = array(
            'product',
            'category',
            'manufacturer',
            'supplier',
            'bestsales',
            'newproducts',
            'search'
        );
        if (in_array($controller, $controllers_with_product_lists)) {
            if ($controller === 'product') {
                // handle product var
                $data = array(
                    'product' => $this->context->smarty->getTemplateVars('product'),
                    'accesories' => $this->context->smarty->getTemplateVars('accessories')
                );
            } else {
                // rest of controllers will have listing var
                $data = array('listing' =>  $this->context->smarty->getTemplateVars('listing'));
            }
            $this->addJsDef($data, 'rcpgTagManager');
        }
    }
}
