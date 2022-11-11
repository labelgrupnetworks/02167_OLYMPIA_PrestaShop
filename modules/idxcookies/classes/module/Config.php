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
use IdxrcookiesTemplate as Template;
use IdxrTools_2_0 as Tools;

trait IdxrcookiesConfig
{
    protected function processConfig()
    {
        $slug = 'submit'.$this->name;
        foreach ($_POST as $clave => $valor) {
            if (preg_match('/'.$slug.'(.*)/', $clave, $match)) {
                if (empty($match[1])) {
                    continue;
                }
                $method = 'postProcess'.Tools::dashesToCamelCase($match[1], true);
                if (is_callable(array($this, $method))) {
                    return call_user_func(array($this, $method));
                }
            }
        }
        foreach ($_GET as $clave => $valor) {
            if (preg_match('/(.*)'.$this->name.'_(.*)/', $clave, $match)) {
                if ($match[1] == 'update') {
                    return;
                } elseif ($match[1] == 'delete') {
                    if (empty($match[2])) {
                        continue;
                    }
                    $method = 'postProcessDelete'.Tools::dashesToCamelCase($match[2], true);
                    if (is_callable(array($this, $method))) {
                        return call_user_func(array($this, $method));
                    }
                }
                $method = 'postProcessToggle'.Tools::dashesToCamelCase($match[2], true);
                $params = array(
                    'field' => $match[1],
                );
                if (is_callable(array($this, $method))) {
                    return call_user_func(array($this, $method), $params);
                }
            }
        }
        if (Tools::isSubmit('delete'.$this->name)) {
            return $this->postProcessDeleteCookie();
        } elseif (Tools::isSubmit('delete'.$this->name.'_type')) {
            return $this->postProcessDeleteCookieType();
        }
        return '';
    }

    public function renderCookieAudit()
    {
        $return_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        Configuration::updateValue(Tools::strtoupper($this->name).'_return_url', $return_url);
        //mnw init
        $id_shop = Context::getContext()->shop->id;
        $this->smarty->assign(
            array(
                "index_url" => $this->context->link->getPageLink('index', null, null, 'audit=true', false, $id_shop),
            )
        );
        //mnw end
        return $this->display(__FILE__, "views/templates/admin/cookie_audit.tpl");
    }

    public function renderTabTemplates()
    {
        try {
            $output = '';
            if (Tools::isSubmit('displayConfirmation')) {
                $output.= $this->displayConfirmation($this->l('Action performed correctly', 'Config'));
            } elseif (Tools::isSubmit('displayError')) {
                $output.= $this->displayError($this->l('An error occurred while performing the action. Check the log for any annotation', 'Config'));
            }
            if (Tools::isSubmit('addTemplate') || Tools::isSubmit('viewTemplate') || Tools::isSubmit('update'.$this->name.'_templates')) {
                return $output.$this->renderTemplateForm();
            } else {
                return $output.$this->renderTemplateListado();
            }
        } catch (Exception $e) {
            $this->context->controller->errors[] = $e->getMessage();
        }
    }

    protected function renderTemplateForm()
    {
        $template = new Template(Tools::getValue('id_template'), null, $this->contextShop);
        $back_url = AdminController::$currentIndex . '&configure=' . $this->name.'&renderTabTemplates';
        if (Validate::isLoadedObject($template)) {
            $index = AdminController::$currentIndex . '&configure=' . $this->name.'&renderTabTemplates&viewTemplate&id_template='.(int)Tools::getValue('id_template');
            $title = $this->displayTemplate('admin/idx-heading.tpl', array(
                'title' => $this->l('Template ', 'Config').$template->nombre,
            ));
        } else {
            $index = $back_url.'&viewTemplate';
            $title = $this->displayTemplate('admin/idx-heading.tpl', array(
                'title' => $this->l('New template', 'Config'),
            ));
        }
        $token = Tools::getAdminTokenLite('AdminModules');
        $options = array(
            'token' => $token,
            'back_url' => $back_url,
            'index' => $index
        );
        return $title.$template->renderHelperForm($this, $options);
    }

    protected function renderTemplateListado()
    {
        $options = array(
            'title' => $this->l('Template\s'),
            'token' => Tools::getAdminTokenLite('AdminModules'),
            'no_link' => true,
            'index' => AdminController::$currentIndex.'&configure='.$this->name.'&renderTabTemplates=true',
            'toolbar' => true,
            'toolbar_btn' => array(
                'new' => array(
                    'href' => AdminController::$currentIndex.'&configure='.$this->name.'&renderTabTemplates&addTemplate&token='.Tools::getAdminTokenLite('AdminModules'),
                    'desc' => $this->l('Add new')
                )
            )
        );
        return $this->displayInformation($this->l('In this section you can create code snippets that create cookies by javascript (like google analytics) to allow blocking until the user accepts the cookies. Remember that you can use smarty code in code snippets.', 'Config'))
            .Template::renderHelperList($this, $options);
    }

    public function postProcessSaveAndStayTemplate()
    {
        $template = $this->postProcessGuardarTemplate(false);
        if (Validate::isLoadedObject($template)) {
            Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&renderTabTemplates&viewTemplate&id_template='.(int)$template->id.'&displayConfirmation&token='.Tools::getAdminTokenLite('AdminModules'));
        } elseif (is_string($template)) {
            return $template;
        }
    }

    public function postProcessGuardarTemplate($redirect = true)
    {
        $template = new Template((int)Tools::getValue('id_template'));
        try {
            if (!$template->date_add) {
                $template->date_add = date('Y-m-d H:i:s');
            }
            $template->date_upd = date('Y-m-d H:i:s');
            $template->postProcessForm($this, array(), array('date_add', 'date_upd'));
            if (Validate::isLoadedObject($template)) {
                if ($redirect === true) {
                    Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&renderTabTemplates&displayConfirmation&token='.Tools::getAdminTokenLite('AdminModules'));
                } else {
                    return $template;
                }
            } else {
                return $this->displayError($this->l('Template could not be saved', 'Config'));
            }
        } catch (Exception $e) {
            return $this->displayError($e->getMessage());
        }
    }

    public function postProcessDeleteTemplates()
    {
        $template = new Template((int)Tools::getValue('id_template'));
        if (!Validate::isLoadedObject($template)) {
            return $this->displayError($this->l('Template object could not be founded', 'Config'));
        }
        if ($template->delete()) {
            try {
                $sql = "UPDATE "._DB_PREFIX_.$this->name." SET id_template = 0 WHERE id_template = ".(int)Tools::getValue('id_template');
                Db::getInstance()->execute($sql);
            } catch (Exception $e) {
                $this->logError($e->getMessage());
            }
            return $this->displayConfirmation($this->l('Template deleted', 'Config'));
        } else {
            return $this->displayError($this->l('Template could not be delete', 'Config'));
        }
    }

    public function postProcessToggleTemplates($params)
    {
        $template = new Template((int)Tools::getValue('id_template'));
        if (!Validate::isLoadedObject($template)) {
            return;
        }
        if ($template->cambiarEstado($params['field'])) {
            return $this->displayConfirmation($this->l('Template updated', 'Config'));
        } else {
            return $this->displayError($this->l('Template could not be saved', 'Config'));
        }
    }

    public function renderTabCookieTypes()
    {
        try {
            $output = '';
            if (Tools::isSubmit('displayConfirmation')) {
                $output.= $this->displayConfirmation($this->l('Action performed correctly', 'Config'));
            } elseif (Tools::isSubmit('displayError')) {
                $output.= $this->displayError($this->l('An error occurred while performing the action. Check the log for any annotation', 'Config'));
            }
            if (Tools::isSubmit('addCookieType') || Tools::isSubmit('viewCookieType') || Tools::isSubmit('update'.$this->name.'_type')) {
                return $output.$this->renderCookieTypeForm();
            } else {
                return $output.$this->renderCookieTypeListado();
            }
        } catch (Exception $e) {
            $this->context->controller->errors[] = $e->getMessage();
        }
    }

    protected function renderCookieTypeForm()
    {
        $cookie_type = new CookieType(Tools::getValue('id_cookie_type'), null, $this->contextShop);
        $back_url = AdminController::$currentIndex . '&configure=' . $this->name.'&renderTabCookieTypes';
        if (Validate::isLoadedObject($cookie_type)) {
            $index = AdminController::$currentIndex . '&configure=' . $this->name.'&renderTabCookieTypes&viewCookieType&id_cookie_type='.(int)Tools::getValue('id_cookie_type');
            $title = $this->displayTemplate('admin/idx-heading.tpl', array(
                'title' => $this->l('Cookie type ', 'Config').$cookie_type->name[$this->context->language->id],
            ));
        } else {
            $index = $back_url.'&viewCookieType';
            $title = $this->displayTemplate('admin/idx-heading.tpl', array(
                'title' => $this->l('New cookie type', 'Config'),
            ));
        }
        $token = Tools::getAdminTokenLite('AdminModules');
        $options = array(
            'token' => $token,
            'back_url' => $back_url,
            'index' => $index
        );
        return $title.$cookie_type->renderHelperForm($this, $options);
    }

    protected function renderCookieTypeListado()
    {
        $options = array(
            'title' => $this->l('Cookie types', 'Config'),
            'token' => Tools::getAdminTokenLite('AdminModules'),
            'no_link' => true,
            'index' => AdminController::$currentIndex.'&configure='.$this->name.'&renderTabCookieTypes=true',
            'toolbar' => true,
            'toolbar_btn' => array(
                'new' => array(
                    'href' => AdminController::$currentIndex.'&configure='.$this->name.'&renderTabCookieTypes&addCookieType&token='.Tools::getAdminTokenLite('AdminModules'),
                    'desc' => $this->l('Add new')
                )
            )
        );
        return CookieType::renderHelperList($this, $options);
    }

    public function postProcessSaveAndStayCookieType()
    {
        $cookie_type = $this->postProcessGuardarCookieType(false);
        if (Validate::isLoadedObject($cookie_type)) {
            Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&renderTabCookieTypes&viewCookieType&id_cookie_type='.(int)$cookie_type->id.'&displayConfirmation&token='.Tools::getAdminTokenLite('AdminModules'));
        } elseif (is_string($cookie_type)) {
            return $cookie_type;
        }
    }

    public function postProcessGuardarCookieType($redirect = true)
    {
        $cookie_type = new CookieType((int)Tools::getValue('id_cookie_type'));
        try {
            if (!$cookie_type->date_add) {
                $cookie_type->date_add = date('Y-m-d H:i:s');
            }
            $cookie_type->date_upd = date('Y-m-d H:i:s');
            $cookie_type->postProcessForm($this, array(), array('date_add', 'date_upd'));
            if (Validate::isLoadedObject($cookie_type)) {
                if ($redirect === true) {
                    Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&renderTabCookieTypes&displayConfirmation&token='.Tools::getAdminTokenLite('AdminModules'));
                } else {
                    return $cookie_type;
                }
            } else {
                return $this->displayError($this->l('Cookie Type could not be saved', 'Config'));
            }
        } catch (Exception $e) {
            return $this->displayError($e->getMessage());
        }
    }

    public function postProcessDeleteCookieType()
    {
        $cookie_type = new CookieType((int)Tools::getValue('id_cookie_type'));
        if (!Validate::isLoadedObject($cookie_type)) {
            return $this->displayError($this->l('Cookie Type object could not be founded', 'Config'));
        }
        if ($cookie_type->delete()) {
            return $this->displayConfirmation($this->l('Cookie Type deleted', 'Config'));
        } else {
            return $this->displayError($this->l('Cookie Type could not be delete', 'Config'));
        }
    }

    public function renderTabCookies()
    {
        try {
            $output = '';
            if (Tools::isSubmit('displayConfirmation')) {
                $output.= $this->displayConfirmation($this->l('Action performed correctly', 'Config'));
            } elseif (Tools::isSubmit('displayError')) {
                $output.= $this->displayError($this->l('An error occurred while performing the action. Check the log for any annotation', 'Config'));
            }
            if (Tools::isSubmit('addCookie') || Tools::isSubmit('viewCookie') || Tools::isSubmit('update'.$this->name)) {
                return $output.$this->renderCookieForm();
            } else {
                return $output.$this->renderCookieListado();
            }
        } catch (Exception $e) {
            $this->context->controller->errors[] = $e->getMessage();
        }
    }

    protected function renderCookieForm()
    {
        $cookie = new Cookie(Tools::getValue('id_cookie'), null, $this->contextShop);
        $back_url = AdminController::$currentIndex . '&configure=' . $this->name.'&renderTabCookies';
        if (Validate::isLoadedObject($cookie)) {
            $index = AdminController::$currentIndex . '&configure=' . $this->name.'&renderTabCookies&viewCookie&id_cookie='.(int)Tools::getValue('id_cookie');
            $title = $this->displayTemplate('admin/idx-heading.tpl', array(
                'title' => $this->l('Cookie ', 'Config').$cookie->name,
            ));
        } else {
            $index = $back_url.'&viewCookie';
            $title = $this->displayTemplate('admin/idx-heading.tpl', array(
                'title' => $this->l('New cookie', 'Config'),
            ));
        }
        $token = Tools::getAdminTokenLite('AdminModules');
        $options = array(
            'token' => $token,
            'back_url' => $back_url,
            'index' => $index
        );
        return $title.$cookie->renderHelperForm($this, $options);
    }

    protected function renderCookieListado()
    {
        $options = array(
            'title' => $this->l('Cookie\s'),
            'token' => Tools::getAdminTokenLite('AdminModules'),
            'no_link' => true,
            'index' => AdminController::$currentIndex.'&configure='.$this->name.'&renderTabCookies=true',
            'toolbar' => true,
            'toolbar_btn' => array(
                'new' => array(
                    'href' => AdminController::$currentIndex.'&configure='.$this->name.'&renderTabCookies&addCookie&token='.Tools::getAdminTokenLite('AdminModules'),
                    'desc' => $this->l('Add new')
                )
            )
        );
        return $this->displayInformation($this->l('If you change options in this section, the advice popup will show up again to all customers', 'Config'))
            .Cookie::renderHelperList($this, $options);
    }

    public function postProcessSaveAndStayCookie()
    {
        $cookie = $this->postProcessGuardarCookie(false);
        if (Validate::isLoadedObject($cookie)) {
            Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&renderTabCookies&viewCookie&id_cookie='.(int)$cookie->id.'&displayConfirmation&token='.Tools::getAdminTokenLite('AdminModules'));
        } elseif (is_string($cookie)) {
            return $cookie;
        }
    }

    public function postProcessGuardarCookie($redirect = true)
    {
        $cookie = new Cookie((int)Tools::getValue('id_cookie'));
        try {
            if (!$cookie->date_add) {
                $cookie->date_add = date('Y-m-d H:i:s');
            }
            $cookie->id_shop = (int)$this->contextShop;
            $cookie->date_upd = date('Y-m-d H:i:s');
            $cookie->id_template = (int)Tools::getValue('id_template');
            $cookie->postProcessForm($this, array(), array('date_add', 'date_upd', 'id_template', 'id_shop'));
            if (Validate::isLoadedObject($cookie)) {
                if ($redirect === true) {
                    Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&renderTabCookies&displayConfirmation&token='.Tools::getAdminTokenLite('AdminModules'));
                } else {
                    return $cookie;
                }
            } else {
                return $this->displayError($this->l('Cookie could not be saved', 'Config'));
            }
        } catch (Exception $e) {
            return $this->displayError($e->getMessage());
        }
    }

    public function postProcessDeleteCookie()
    {
        $cookie = new Cookie((int)Tools::getValue('id_cookie'));
        if (!Validate::isLoadedObject($cookie)) {
            return $this->displayError($this->l('Cookie object could not be founded', 'Config'));
        }
        if ($cookie->delete()) {
            return $this->displayConfirmation($this->l('Cookie deleted', 'Config'));
        } else {
            return $this->displayError($this->l('Cookie could not be delete', 'Config'));
        }
    }

    public function getConfigFieldsValues()
    {
    }

    public function renderTabConfiguracion()
    {
        try {
            $options = array(
                'submitId' => 'submit'.$this->name.'GuardarConfiguracion',
                'show_legend' => false,
                'index' => AdminController::$currentIndex.'&configure='.$this->name.'&renderTabConfiguracion'
            );
            return IdxrCookiesConfiguracion::getFormAndFields($this, $options);
        } catch (Exception $e) {
            return $this->displayError($e->getMessage());
        }
    }

    public function postProcessGuardarConfiguracion()
    {
        try {
            return IdxrCookiesConfiguracion::processForm($this);
        } catch (Exception $e) {
            return $this->displayError($e->getMessage());
        }
    }

    public function displaySelectTemplates($value, $tr)
    {
        $variables = array(
            'cookie' => $tr,
            'optionTemplateList' => $this->getOptionsTemplates(),

        );
        return $this->fetchTemplate('admin/cookies-select-templates.tpl', $variables);
    }

    public function displaySelectModules($value, $tr)
    {
        $variables = array(
            'cookie' => $tr,
            'optionModuleList' => $this->getOptionsModulo(),

        );
        return $this->fetchTemplate('admin/cookies-select-modules.tpl', $variables);
    }

    public function displaySelectCookiesType($value, $tr)
    {
        $variables = array(
            'cookie' => $tr,
            'cookie_types' => $this->getCookieTypes(),

        );
        return $this->fetchTemplate('admin/cookies-select-cookie-types.tpl', $variables);
    }

    public function getOptionsModulo()
    {
        $sql = 'SELECT m.* FROM `'._DB_PREFIX_.'module` m';
        if ($reg = Db::getInstance()->executeS($sql)) {
            $optionsModulos = array();
            foreach ($reg as $elemento) {
                $modulo = Module::getInstanceByName($elemento['name']);
                if (!Validate::isLoadedObject($modulo)) {
                    continue;
                }
                $optionsModulos[] = array(
                    'id' => $modulo->name,
                    'name' => $modulo->displayName.' ('.$modulo->name.')',
                );
            }
            usort($optionsModulos, function ($a, $b) {
                return strcmp($a["name"], $b["name"]);
            });
            if (empty($this->optionsModulos)) {
                $this->optionsModulos = array_merge(
                    array(array(
                        'id' => '---',
                        'name' => $this->l('None', 'Config')
                    )),
                    $optionsModulos
                );
            }
        }

        return $this->optionsModulos;
    }

    public function getOptionsTemplates()
    {
        $templates = Template::listado(array(), array(), false, 'nombre', 'asc');
        $optionsTemplates = array();
        if (is_array($templates)) {
            foreach ($templates as $elemento) {
                $template = new Template((int)$elemento['id_template']);
                if (!Validate::isLoadedObject($template)) {
                    continue;
                }
                $optionsTemplates[] = array(
                    'id' => $template->id,
                    'name' => $template->nombre,
                );
            }
            usort($optionsTemplates, function ($a, $b) {
                return strcmp($a["name"], $b["name"]);
            });
        }

        if (empty($this->optionsTemplates)) {
            $this->optionsTemplates = array_merge(
                array(array(
                    'id' => '---',
                    'name' => $this->l('None', 'Config')
                )),
                $optionsTemplates
            );
        }
        return $this->optionsTemplates;
    }
}
