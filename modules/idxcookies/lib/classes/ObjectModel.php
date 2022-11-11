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

use IdxrHelperList_3_0_2 as HelperList;

class IdxrObjectModel_3_0_2 extends ObjectModel
{
    public $form_title; //se usa para el título del helper form

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        if ($id) {
            $this->id = (int)$id;
        }
    }

    public static function getById($id)
    {
        $filtros = array(
            static::$definition['primary'] => $id
        );
        if ($elementos = self::listado($filtros, array(0, 1))) {
            return $elementos[0];
        }
        return null;
    }

    public static function listado($filtros = array(), $limite = array(), $num_only = false, $orderBy = null, $orderDireccion = null)
    {
        if (empty($filtros)) {
            $filtros = array();
        }
        if (!isset($filtros['id_lang'])) {
            $filtros['id_lang'] = Context::getContext()->language->id;
        }

        if (!isset($filtros['id_shop'])) {
            $filtros['id_shop'] = Context::getContext()->shop->id;
        }

        $sql = new DbQuery();
        if ($num_only) {
            $sql->select('COUNT(*)');
        } else {
            if (isset(static::$definition['helper_list_select'])) {
                $select = static::$definition['helper_list_select'];
            } else {
                $select = 'a.*';
                if (isset(static::$definition['multilang']) && static::$definition['multilang'] === true) {
                    $select.= ', al.*';
                }
            }
            $sql->select($select);
        }

        $sql->from(static::$definition['table'], 'a');
        if (isset(static::$definition['multilang']) && static::$definition['multilang'] == true) {
            if (isset(static::$definition['multilang_shop']) && static::$definition['multilang_shop'] === true) {
                $sql->innerJoin(static::$definition['table'].'_lang', 'al', '(a.'.static::$definition['primary'].'=al.'.static::$definition['primary'].' AND al.id_lang = '.(int)$filtros['id_lang'].' AND al.id_shop = '.(int)$filtros['id_shop'].')');
            } else {
                $sql->innerJoin(static::$definition['table'].'_lang', 'al', '(a.'.static::$definition['primary'].'=al.'.static::$definition['primary'].' AND al.id_lang = '.(int)$filtros['id_lang'].')');
            }
        }
        if (isset(static::$definition['multishop']) && static::$definition['multishop'] == true) {
            if (!isset($filtros['id_shop'])) {
                $filtros['id_shop'] = Context::getContext()->shop->id;
            }
            $sql->innerJoin(static::$definition['table'].'_shop', 'ashop', '(a.'.static::$definition['primary'].'=`ashop`.'.static::$definition['primary'].' AND `ashop`.id_shop = '.(int)$filtros['id_shop'].')');
        }
        $parameters = array( 'sql' => &$sql, 'filtros' => &$filtros );
        if (is_callable(array(get_called_class(), 'listadoCustomFilters'))) {
            call_user_func(array(get_called_class(), 'listadoCustomFilters'), $parameters);
        }
        foreach ($filtros as $clave => $valor) {
            if (in_array($clave, array('pagina', 'numResultados', 'id_lang', 'id_shop'))) {
                continue;
            }
            if ($clave == static::$definition['primary']) {
                $sql->where('a.'.static::$definition['primary'].'='.(int)$valor);
            } elseif (isset(static::$definition['fields'][$clave])) {
                //FIXME: validate $valor y psql o float int
                $field = static::$definition['fields'][$clave];
                $tabla = 'a.';
                if (isset($field['lang']) && $field['lang'] === true) {
                    $tabla = 'al.';
                } elseif (isset($field['shop']) && $field['shop'] === true) {
                    $tabla = '`ashop`.';
                }
                if (is_numeric($valor)) {
                    $sql->where($tabla.$clave.' = "'.pSQL($valor).'"');
                } else {
                    $sql->where($tabla.$clave.' LIKE "%'.pSQL($valor).'%"');
                }
            }
        }

        if (!empty($limite) && !$num_only) {
            $sql->limit($limite[1], $limite[0]);
        }
        if (!$orderBy) {
            $orderBy = 'a.'.static::$definition['primary'];
        }
        if (!$orderDireccion) {
            $orderDireccion = 'asc';
        }
        $sql->orderBy($orderBy.' '.$orderDireccion);
        if ($num_only) {
            return Db::getInstance()->getValue($sql);
        } else {
            return Db::getInstance()->executeS($sql);
        }
    }

    public static function resetHelperListFiltros()
    {
        $helper = new HelperList();
        $helper->key_filtros = static::$definition['table'];
        return $helper->resetFiltros();
    }

    public static function renderHelperList($module, $options)
    {
        $helper = new HelperList();
        $helper->module = $module;
        $helper->identifier = static::$definition['primary'];
        $helper->table = static::$definition['table'];
        $helper->token = $options['token'];
        if (isset($options['actions'])) {
            $helper->actions = $options['actions'];
        }
        if (isset($options['no_link'])) {
            $helper->no_link = $options['no_link'];
        }
        $helper->title = $options['title'];
        $helper->currentIndex = $options['index'];
        $helper->key_filtros = static::$definition['table'];
        $filtros = $helper->getFiltros();
        if (isset($options['filtros'])) {
            $filtros = array_merge($filtros, $options['filtros']);
        }
        $order_by = isset($filtros['orderBy']) ? $filtros['orderBy'] : null;
        $order_way = isset($filtros['orderWay']) ? $filtros['orderWay'] : null;
        $campos = static::listado($filtros, array(($filtros['pagina']-1) * $filtros['numResultados'], $filtros['numResultados']), false, $order_by, $order_way);
        $total = (int)static::listado($filtros, array(), true);
        if ($total == 0 && $filtros['pagina'] > 1) {
            $helper->resetFiltros();
        }

        $helper->listTotal = $total;
        $fields_list = static::getHelperListFields($module, $filtros);
        foreach ($fields_list as $key => $field) {
            if (isset($filtros[$key])) {
                $fields_list[$key]['value'] = $filtros[$key];
            }
        }
        if (isset($options['toolbar']) && $options['toolbar'] == 'true') {
            $helper->show_toolbar = true;
            $helper->toolbar_btn = $options['toolbar_btn'];
        }
        $helper->tpl_vars['token'] = $helper->token;
        return $helper->generateList($campos, $fields_list);
    }

    protected static function getHelperListFields($module, $filtros = array())
    {
        $fields_list = array(
            static::$definition['primary'] => array(
                'title' => $module->l('ID', 'ObjectModel'),
                'align' => 'left',
                'search' => true,
            )
        );
        foreach (static::$definition['fields'] as $clave => $field) {
            if (isset($field['helper-list'])) {
                $field['helper-list']['title'] = $module->l($field['helper-list']['title']);
                $fields_list[$clave] = $field['helper-list'];
            }
        }
        foreach ($fields_list as $clave => $valor) {
            if (isset($filtros[$clave])) {
                $fields_list[$clave]['value'] = $filtros[$clave];
            }
        }
        return $fields_list;
    }

    public function renderHelperForm($module, $options)
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper = new HelperForm();
        $helper->module = $module;
        $helper->name_controller = $module->name;
        $helper->token = $options['token'];
        $helper->currentIndex = $options['index'];

        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        if (isset($options['title'])) {
            $helper->title = $options['title'];
        }
        $helper->show_toolbar = true;
        if (isset($options['back_url'])) {
            $helper->show_cancel_button = true;
            $helper->back_url = $options['back_url'];
        }
        $helper->table = static::$definition['table'];
        $helper->submit_action = 'submit'.static::$definition['table'];

        $helper->tpl_vars = array(
            'fields_value' => $this->getFormValues(),
            'languages' => Context::getContext()->controller->getLanguages(),
            'id_language' => Context::getContext()->language->id
        );
        return $helper->generateForm($this->getFieldsForm($module));
    }

    public function getFieldsForm($module)
    {
        $inputs = $this->getHelperFormInputs($module);
        foreach ($inputs as &$input) {
            switch ($input['type']) {
                case 'switch':
                    $input['values'] = array(
                        array(
                            'id' => $input['name'].'_on',
                            'value' => 1,
                            'label' => $module->l('Enabled', 'Configuration')
                        ),
                        array(
                            'id' => $input['name'].'_off',
                            'value' => 0,
                            'label' => $module->l('Disabled', 'Configuration')
                        )
                    );
                    break;
                case 'arbol_categorias':
                    if (isset($input['function']) && is_callable(array($this, $input['function']))) {
                        $input['arbolCategorias'] = call_user_func(array($this, $input['function']), $module);
                    }
                    break;
                case 'file':
                    $esImagen = (isset($input['esImagen']) && $input['esImagen'] === true);
                    $input['files'] = $this->getFiles($module, $input['name'], $esImagen);
                    break;
            }
        }
        $fields_form = array();
        $fields_form['form'] = array(
            'form' => array(
                'id_form' => 'form-object-'.static::$definition['table'],
                'input' => $inputs,
                'submit' => array(
                    'title' => $module->l('Save', 'ObjectModel'),
                    'name' => 'submit'.$module->name.'Guardar'.static::$definition['helper_form_identifier'],
                ),
                'buttons' => array(
                    'save-and-stay' => array(
                        'title' => $module->l('Save and stay', 'ObjectModel'),
                        'name' => 'submit'.$module->name.'SaveAndStay'.static::$definition['helper_form_identifier'],
                        'type' => 'submit',
                        'value' => 1,
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-save',
                    ),
                ),
            ),
        );
        return $fields_form;
    }

    public function getFiles($module, $key, $esImage = true)
    {
        $path = $this->{$key};
        $datos = array(
            'type' => $esImage ? HelperUploader::TYPE_IMAGE: HelperUploader::TYPE_FILE,
            'delete_url' => $module->getFileDeleteLink($key, $this->id, get_called_class()),
        );
        $download_url = $module->getAdminDownloadPath($path);
        if ($esImage) {
            $datos['image'] = $module->displayAdminImagen($path);
        } else {
            if ($download_url) {
                $datos['download_url'] = $download_url;
            }
        }
        return array(
            $datos,
        );
    }

    public function getFormValues()
    {
        $values = array(
            'id' => $this->id
        );
        $languages = Language::getLanguages(false);
        foreach (static::$definition['fields'] as $clave => $field) {
            if (isset($field['lang']) && $field['lang'] === true) {
                foreach ($languages as $language) {
                    if (is_array($this->{$clave}) && isset($this->{$clave}[$language['id_lang']])) {
                        $values[$clave][$language['id_lang']] = Tools::getValue($clave.'_'.$language['id_lang'], $this->{$clave}[$language['id_lang']]);
                    } else {
                        $values[$clave][$language['id_lang']] = Tools::getValue($clave.'_'.$language['id_lang']);
                    }
                }
            } elseif (isset($field['idx_form_type'])) {
                switch ($field['idx_form_type']) {
                    case 'select_multiple':
                        $selected_items = array();
                        if ($this->{$clave}) {
                            $selected = explode(",", $this->{$clave});
                            if ($selected && !is_array($selected)) {
                                $selected = array($selected);
                            }
                            if ($selected) {
                                $selected_items = $selected;
                            }
                        }
                        $values[$clave.'[]'] = Tools::getValue($clave, $selected_items);
                        break;
                    case 'checkbox':
                        $selected_items = array();
                        if ($this->{$clave}) {
                            $selected = explode(",", $this->{$clave});
                            if ($selected && !is_array($selected)) {
                                $selected = array($selected);
                            }
                            if ($selected) {
                                $selected_items = $selected;
                            }
                        }
                        $choices = call_user_func(array($this, $field['idx_form_get_options_callback']));
                        foreach ($choices as $checkbox) {
                            $checkboxKey = $clave.'_'.$checkbox['id'];
                            $values[$checkboxKey] =
                                Tools::getValue(
                                    $checkboxKey,
                                    in_array($checkbox['id'], $selected_items) ? 'on' : false
                                );
                        }
                        break;
                }
            } else {
                $values[$clave] = Tools::getValue($clave, $this->{$clave});
            }
        }
        return $values;
    }

    public function postProcessForm($module, $values = array(), $skip_keys = array())
    {
        $errores = array();
        $idiomas = Context::getContext()->controller->getLanguages();
        $all_values = Tools::getAllValues();
        foreach (static::$definition['fields'] as $clave => $campo) {
            if (in_array($clave, $skip_keys)) {
                continue;
            }
            if ($clave === 'date_add') {
                if (!Validate::isLoadedObject($this)) {
                    $this->date_add = date('Y-m-d H:i:s');
                }
                continue;
            }
            if ($clave === 'date_upd') {
                $this->date_upd = date('Y-m-d H:i:s');
                continue;
            }
            if (isset($campo['idx_form_type'])) {
                switch ($campo['idx_form_type']) {
                    case 'autocompletar':
                        $field_value = Tools::getValue($clave);
                        if (is_array($field_value) && isset($field_value['data'])) {
                            $values = implode(",", $field_value['data']);
                            $this->{$clave} = $values;
                        } else {
                            $this->{$clave} = null;
                        }
                        break;
                    case 'arbol_categorias':
                        if (is_array(Tools::getValue($clave))) {
                            $value = !empty(Tools::getValue($clave)) ? implode(",", Tools::getValue($clave)) : null;
                        } else {
                            $value = Tools::getValue($clave);
                        }
                        if (count(Tools::getValue($clave)) == 1) {
                            $value.= ',';
                        }
                        $this->{$clave} = $value;
                        break;
                    case 'file':
                        $file = $module->processFile($clave, $campo['idx_upload_dir']);
                        if ($file) {
                            $this->{$clave} = $file;
                        }
                        break;
                    case 'checkbox':
                        $checkboxes = array();
                        $choices = call_user_func(array($this, $campo['idx_form_get_options_callback']));
                        foreach ($choices as $checkbox) {
                            $checkboxKey = $clave.'_'.$checkbox['id'];
                            if (Tools::isSubmit($checkboxKey) && Tools::getValue($checkboxKey) === 'on') {
                                $checkboxes[] = $checkbox['id'];
                            }
                        }
                        $this->{$clave} = implode(",", $checkboxes);
                        break;
                    case 'select_multiple':
                        $selectKey = str_replace("[]", "", $clave);
                        $value = Tools::getValue($selectKey);
                        if (is_array($value)) {
                            $this->{$clave} = implode(",", $value);
                        } else {
                            $this->{$clave} = null;
                        }
                        break;
                }
                continue;
            }
            if (isset($campo['required']) && $campo['required'] === true) {
                if (isset($campo['lang']) && $campo['lang'] == true) {
                    $unIdiomaIntroducido = false;
                    foreach ($idiomas as $idioma) {
                        if (Tools::isSubmit($clave."_".$idioma['id_lang']) || isset($values[$clave."_".$idioma['id_lang']])) {
                            if (isset($values[$clave."_".$idioma['id_lang']])) {
                                $value = $values[$clave."_".$idioma['id_lang']];
                            } else {
                                $value = Tools::getValue($clave."_".$idioma['id_lang']);
                            }
                            if ($campo['type'] == self::TYPE_NOTHING) {
                                $value = addslashes($value);
                            } else {
                                $value = pSQL($value);
                            }
                            if (isset($campo['validate']) && is_callable(array('Validate', $campo['validate']))) {
                                $validate = call_user_func(array('Validate', $campo['validate']), $value);
                                if (!$validate) {
                                    $value = null;
                                    $errores[] = str_replace("%s", $clave, $module->l("Field %s has invalid value for language ", 'ObjectModel').$idioma['name']);
                                } else {
                                    $this->{$clave}[$idioma['id_lang']] = $value;
                                    if (Tools::getValue($clave."_".$idioma['id_lang']) != '') {
                                        $unIdiomaIntroducido = true;
                                    }
                                }
                            } else {
                                $errores[] = str_replace("%s", $clave, $module->l("Field %s needs a validation function", 'ObjectModel'));
                            }
                        }
                    }
                    if (!$unIdiomaIntroducido) {
                        $errores[] = str_replace("%s", $clave, $module->l("Field %s is mandatory in one language at least", 'ObjectModel'));
                        continue;
                    }
                } else {
                    if (!Tools::isSubmit($clave) || (Tools::getValue($clave) == '') && (!isset($values[$clave]) || empty($values[$clave]))) {
                        $errores[] = str_replace("%s", $clave, $module->l("Field %s is required", 'ObjectModel'));
                        continue;
                    } else {
                        if (isset($values[$clave])) {
                            $value = $values[$clave];
                        } else {
                            $value = Tools::getValue($clave);
                        }
                        if ($campo['type'] == self::TYPE_NOTHING) {
                            $value = addslashes($value);
                        } else {
                            $value = pSQL($value);
                        }
                        if (isset($campo['validate']) && is_callable(array('Validate', $campo['validate']))) {
                            $validate = call_user_func(array('Validate', $campo['validate']), $value);
                            if (!$validate) {
                                $value = null;
                                $errores[] = str_replace("%s", $clave, $module->l("Field %s has invalid value"));
                            } else {
                                $this->{$clave} = $value;
                            }
                        } else {
                            $errores[] = str_replace("%s", $clave, $module->l("Field %s needs a validation function", 'ObjectModel'));
                        }
                    }
                }
            } else { //campo no obligatorio
                if (isset($campo['lang']) && $campo['lang'] == true) {
                    foreach ($idiomas as $idioma) {
                        if (isset($values[$clave."_".$idioma['id_lang']])) {
                            $value = $values[$clave."_".$idioma['id_lang']];
                        } else {
                            $value = Tools::getValue($clave."_".$idioma['id_lang']);
                        }
                        if (empty($value)) {
                            continue;
                        }
                        if ($campo['type'] == self::TYPE_NOTHING) {
                            $value = addslashes($value);
                        } else {
                            $value = pSQL($value);
                        }
                        if (isset($campo['validate']) && is_callable(array('Validate', $campo['validate']))) {
                            $validate = call_user_func(array('Validate', $campo['validate']), $value);
                            if (!$validate) {
                                $value = null;
                                $errores[] = str_replace("%s", $clave, $module->l("Field %s has invalid value for language ", 'ObjectModel').$idioma['name']);
                            } else {
                                $this->{$clave}[$idioma['id_lang']] = $value;
                            }
                        } elseif (!empty($value)) {
                            $errores[] = str_replace("%s", $clave, $module->l("Field %s needs a validation function", 'ObjectModel'));
                        }
                    }
                } else {
                    if (isset($values[$clave])) {
                        $value = $values[$clave];
                    } else {
                        $value = Tools::getValue($clave);
                    }
                    if (!$value) {
                        $this->{$clave} = null;
                        continue;
                    }
                    if ($campo['type'] == self::TYPE_NOTHING) {
                        $value = addslashes($value);
                    } else {
                        $value = pSQL($value);
                    }
                    if (isset($campo['validate']) && is_callable(array('Validate', $campo['validate']))) {
                        $validate = call_user_func(array('Validate', $campo['validate']), $value);
                        if (!$validate) {
                            $value = null;
                            $errores[] = str_replace("%s", $clave, $module->l("Field %s has invalid value", 'ObjectModel'));
                        } else {
                            $this->{$clave} = $value;
                        }
                    } else {
                        $errores[] = str_replace("%s", $clave, $module->l("Field %s needs a validation function", 'ObjectModel'));
                    }
                }
            }
        }
        if (count($errores) > 0) {
            throw new Exception(implode("\n", $errores));
        }
        return $this->save();
    }

    public function cambiarEstado($key)
    {
        if (!array_key_exists($key, $this)) {
            throw new PrestaShopException('property "'.$key.'" is missing in object ' . get_class($this));
        }

        $this->setFieldsToUpdate(array($key => true));

        $this->{$key} = !(int) $this->{$key};
        return $this->update(false);
    }
}
