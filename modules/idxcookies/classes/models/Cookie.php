<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2019 Innova Deluxe SL

 * @license   INNOVADELUXE
 */

use IdxrObjectModel_3_0_2 as ObjectModel;
use IdxrcookiesTemplate as Template;
use IdxrTools_2_0 as Tools;
use IdxrcookiesCookieType as CookieType;

class IdxrcookiesCookie extends ObjectModel
{
    public $id_template;

    public $domain;

    public $name;

    public $id_cookie_type;

    public $id_shop;

    public $module;

    public $date_add;

    public $date_upd;

    public static $definition = array(
        'table' => 'idxcookies',
        'primary' => 'id_cookie',
        'helper_form_identifier' => 'Cookie',
        'helper_list_select' => 'a.*, IFNULL(a.module, "-") as module, IFNULL(a.id_template, "-") as id_template',
        'fields' => array(
            'id_template' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
                'required' => true,
            ),
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
            ),
            'id_cookie_type' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
            ),
            'domain' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'size' => 64,
            ),
            'name' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true,
                'size' => 64,
            ),
            'module' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isModuleName',
                'size' => 255,
            ),
            'date_add' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isDate',
                'required' => true,
                'size' => 255,
            ),
            'date_upd' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isDate',
                'required' => true,
                'size' => 255,
            ),
        ),
    );

    public function getHelperFormInputs($module)
    {
        $optionsModulos = $module->getOptionsModulo();
        $optionsTemplates = $module->getOptionsTemplates();

        $inputs = array(
            array(
                'type' => 'hidden',
                'name' => 'id'
            ),
            array(
                'type' => 'text',
                'label' => $module->l('Cookie Name', 'Cookie'),
                'name' => 'name',
                'lang' => false,
                'desc' => $module->l('The name of the cookie', 'Cookie')
            ),
            array(
                'type' => 'text',
                'label' => $module->l('Cookie domain', 'Cookie'),
                'name' => 'domain',
                'lang' => false,
                'desc' => $module->l('The domain of the cookie', 'Cookie')
            ),
            array(
                'label' => $module->l('Associated module', 'Cookie'),
                'desc' => $module->l('This module will disabled from execution until user accept the cookie', 'Cookie'),
                'name' => 'module',
                'type' => 'select',
                'class' => 'full-width',
                'options' => array(
                    'query' => $optionsModulos,
                    'id' => 'id',
                    'name' => 'name'
                ),
            ),
            array(
                'label' => $module->l('Associated template', 'Cookie'),
                'desc' => $module->l('This template will disabled from execution until user accept the cookie', 'Cookie'),
                'name' => 'id_template',
                'type' => 'select',
                'class' => 'full-width',
                'options' => array(
                    'query' => $optionsTemplates,
                    'id' => 'id',
                    'name' => 'name'
                ),
            ),
        );
        return $inputs;
    }

    protected static function getHelperListFields($module, $filtros = array())
    {
        $fields_list = array(
            'id_cookie' => array(
                'title' => $module->l('Id', 'Cookie'),
                'align' => 'left',
                'search' => true,
                'order' => true,
            ),
            'name' => array(
                'title' => $module->l('Name', 'Cookie'),
                'align' => 'left',
                'search' => true,
            ),
            'domain' => array(
                'title' => $module->l('Domain', 'Cookie'),
                'align' => 'left',
                'search' => true,
            ),
            'id_template' => array(
                'title' => $module->l('Template', 'Cookie'),
                'align' => 'left',
                'search' => false,
                'callback' => 'displaySelectTemplates',
                'callback_object' => $module,
            ),
            'module' => array(
                'title' => $module->l('Module', 'Cookie'),
                'align' => 'left',
                'search' => false,
                'callback' => 'displaySelectModules',
                'callback_object' => $module,
            ),
            'id_cookie_type' => array(
                'title' => $module->l('Cookie Type', 'Cookie'),
                'align' => 'left',
                'search' => false,
                'callback' => 'displaySelectCookiesType',
                'callback_object' => $module,
            ),
        );
        return $fields_list;
    }

    public static function getCookies($id_cookie_type = false)
    {
        $sql = 'Select * from ' . _DB_PREFIX_ . self::$definition['table'];
        if ($id_cookie_type) {
            $sql .= ' where id_cookie_type = ' .(int)$id_cookie_type;
        }
        //mnw init
        $id_shop = Context::getContext()->shop->id;

        if ($id_cookie_type) {
            $sql .= ' AND ';
        } else {
            $sql .= ' WHERE ';
        }
        if (Shop::isFeatureActive()) {
            $sql .= ' id_shop = ' .(int)$id_shop;
        } else {
            $sql .= ' id_shop IN (0, ' .(int)$id_shop. ') GROUP BY name';
        }
        //mnw end
        $sql .= ' order by domain';

        $cookies = Db::getInstance()->executeS($sql);
        foreach ($cookies as &$cookie) {
            if (!$cookie['domain']) {
                $cookie['domain'] = _PS_BASE_URL_;
            }
            $cookieType = new CookieType((int)$cookie['id_cookie_type']);
            if (Validate::isLoadedObject($cookieType)) {
                $cookie['imperative'] = (bool)$cookieType->imperative;
            } else {
                $cookie['imperative'] = false;
            }
        }

        return $cookies;
    }

    public static function saveCookies($cookies)
    {
        if (is_array($cookies)) {
            foreach ($cookies as $key => $value) {
                $exist_q = 'Select id_cookie from ' . _DB_PREFIX_ . self::$definition['table'].' where name = "' . pSQL($key) . '"';

                //mnw init
                $id_shop = Context::getContext()->shop->id;
                if ($id_shop) {
                    $exist_q .= ' AND id_shop = ' .(int)$id_shop;
                }
                $id_cookie = Db::getInstance()->getValue($exist_q);
                if (!$id_cookie) {
                    $data = array(
                        'name' => pSQL($key),
                        'id_shop' => (int)$id_shop,
                    );
                    if (isset($value['module'])) {
                        $data['module'] = pSQL($value['module']);
                    }
                    if (isset($value['id_template'])) {
                        $data['id_template'] = (int)$value['id_template'];
                    }else{
                        $data['id_template'] = 0;
                    }
                    if (isset($value['id_cookie_type'])) {
                        $data['id_cookie_type'] = (int)$value['id_cookie_type'];
                    }else{
                        $data['id_cookie_type'] = 0;
                    }
                    if (isset($value['setDomain']) && $value['setDomain'] === true) {
                        $data['domain'] = pSQL($value['domain']);
                    } else {
                        $data['domain'] = '';
                    }
                    if (isset($value['id_template'])) {
                        $data['id_template'] = (int)$value['id_template'];
                    }
                    if (!isset($data['date_add'])) {
                        $data['date_add'] = date('Y-m-d H:i:s');
                    }
                    if (!isset($data['date_upd'])) {
                        $data['date_upd'] = date('Y-m-d H:i:s');
                    }
                    Db::getInstance()->insert(self::$definition['table'], $data);
                }
                //mnw end
            }
        }
    }

    public function save($null_values = false, $auto_date = true)
    {
        if (!Validate::isDate($this->date_add)) {
            $this->date_add = date('Y-m-d H:i:s');
        }
        return parent::save($null_values, $auto_date);
    }

    public static function listadoCustomFilters($parametros)
    {
        $parametros['sql']->where('a.id_shop = '.(int)$parametros['filtros']['id_shop']);
    }
}
