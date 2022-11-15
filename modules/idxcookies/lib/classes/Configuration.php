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

class IdxrConfiguration_2_0 extends Configuration
{
    public static function getFormAndFields($module, $options = array())
    {
        $defaults = array(
            'submitId' => 'submit'.$module->name.'guardarConfiguracionBasica',
            'show_toolbar' => true,
            'index' => AdminController::$currentIndex.'&configure='.$module->name,
            'token' => Tools::getAdminTokenLite('AdminModules'),
            'toolbar' => array(),
            'show_legend' => true,
            'legend' => $module->l('General configuration', 'Configuration')
        );

        $options = array_merge($defaults, $options);
        $fields = static::getVariablesConfiguracion($module);
        $prefix = isset($module->prefix) && !empty($module->prefix) ? $module->prefix : Tools::strtoupper($module->name).'_';
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $inputs = array();
        foreach ($fields as $key => $variable) {
            $input = $variable;

            $input['name'] = $prefix.$key;
            switch ($variable['type']) {
                case 'select':
                    if (isset($variable['function']) && is_callable(array(get_called_class(), $variable['function']))) {
                        $input['options'] = array(
                            'query' => call_user_func(array(get_called_class(), $variable['function']), $module),
                            'id' => 'id_option',
                            'name' => 'name'
                        );
                    }
                    break;
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
                case 'categories':
                    if (isset($variable['function']) && is_callable(array(get_called_class(), $variable['function']))) {
                        $input['tree'] = array(
                            'id' => 'categories',
                            'use_checkbox' => true,
                            'selected_categories' => call_user_func(array(get_called_class(), $variable['function']), $module),
                        );
                    }
                    break;
                case 'arbol_categorias':
                    if (isset($variable['function']) && is_callable(array(get_called_class(), $variable['function']))) {
                        $input['arbolCategorias'] = call_user_func(array(get_called_class(), $variable['function']), $module);
                    }
                    break;
            }
            $inputs[] = $input;
        }

        $fields_form = array();
        $fields_form[0]['form'] = array(
            'input' => $inputs,
            'submit' => array(
                'title' => $module->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );
        if ($options['show_legend']) {
            $fields_form[0]['form']['legend'] = array(
                'title' => $options['legend'],
            );
        }
        $helper = new HelperForm();

        $helper->module = $module;
        $helper->name_controller = $module->name;
        $helper->token = $options['token'];
        $helper->currentIndex = $options['index'];

        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        $helper->title = $module->displayName;
        $helper->show_toolbar = $options['show_toolbar'];
        $helper->toolbar_scroll = 'yes';
        $helper->submit_action = $options['submitId'];
        $helper->toolbar_btn = $options['toolbar'];

        $helper->tpl_vars = array(
            'fields_value' => static::getConfigValues($prefix, $module),
            'languages' => Context::getContext()->controller->getLanguages(),
            'id_language' => Context::getContext()->language->id
        );

        return $helper->generateForm($fields_form);
    }

    public static function getConfigValues($prefix, $module)
    {
        $fields = array();
        $languages = Language::getLanguages(false);
        $variables_configuracion = static::getVariablesConfiguracion($module);
        foreach ($variables_configuracion as $key => $variable) {
            if (isset($variable['lang']) && $variable['lang'] === true) {
                foreach ($languages as $idioma) {
                    $fields[$prefix.$key][$idioma['id_lang']] =
                        Tools::getValue(
                            $prefix.$key."_".$idioma['id_lang'],
                            self::get($prefix.$key, $idioma['id_lang'], $module->contextShopGroup, $module->contextShop)
                        );
                }
            } else {
                $fields[$prefix.$key] =
                    Tools::getValue(
                        $prefix.$key,
                        self::get($prefix.$key, null, $module->contextShopGroup, $module->contextShop)
                    );
            }
        }
        return $fields;
    }

    public static function processForm($module)
    {
        $prefix = isset($module->prefix) && !empty($module->prefix) ? $module->prefix : Tools::strtoupper($module->name).'_';
        $result = true;
        $variables_configuracion = static::getVariablesConfiguracion($module);
        foreach ($variables_configuracion as $key => $variable) {
            if (in_array($variable['type'], array('html', 'heading','alert'))) {
                continue;
            }
            if (!isset($variable['validate'])) {
                throw new Exception(str_replace("%s", $variable['label'], $module->l("Field %s has no validation method", 'Configuration')));
            }
            $html = isset($variable['html']) ? $variable['html'] : false;
            if ($variable['type'] == 'categories' || $variable['type'] == 'arbol_categorias') {
                if (is_array(Tools::getValue($prefix.$key))) {
                    $value = !empty(Tools::getValue($prefix.$key)) ? implode(",", Tools::getValue($prefix.$key)) : null;
                } else {
                    $value = Tools::getValue($prefix.$key);
                }
                if (count(Tools::getValue($prefix.$key)) == 1) {
                    $value.= ',';
                }
                $result &= self::updateValue($prefix.$key, $value, $html, $module->contextShopGroup, $module->contextShop);
            } elseif (isset($variable['lang']) && $variable['lang'] == true) {
                $valores = array();
                $unIdiomaIntroducido = false;
                foreach (Language::getLanguages() as $idioma) {
                    $val = Tools::getValue($prefix.$key."_".$idioma['id_lang']);
                    if (is_callable(array('Validate', $variable['validate']))) {
                        if (!call_user_func(array('Validate', $variable['validate']), $val)) {
                            throw new Exception(str_replace("%s", $variable['label'], $module->l("Field %s has invalid value", 'Configuration')));
                        }
                    } else {
                        throw new Exception(str_replace("%s", $variable['label'], $module->l("Field %s has no validation method", 'Configuration')));
                    }
                    $valores[$idioma['id_lang']] = $val;
                    if ($val != '') {
                        $unIdiomaIntroducido = true;
                    }
                }
                if ($unIdiomaIntroducido === false && isset($variable['required']) && $variable['required'] === true) {
                    throw new Exception(str_replace("%s", $variable['label'], $module->l("Field %s is mandatory in one language at least", 'Configuration')));
                } else {
                    Configuration::updateValue($prefix.$key, $valores, $html, $module->contextShopGroup, $module->contextShop);
                }
            } else {
                $value = Tools::getValue($prefix.$key);
                if ($value) {
                    if (is_callable(array('Validate', $variable['validate']))) {
                        if (!call_user_func(array('Validate', $variable['validate']), $value)) {
                            throw new Exception(str_replace("%s", $variable['label'], $module->l("Field %s has invalid value", 'Configuration')));
                        }
                    } else {
                        throw new Exception(str_replace("%s", $variable['label'], $module->l("Field %s has no validation method", 'Configuration')));
                    }
                }
                $result &= self::updateValue($prefix.$key, $value, $html, $module->contextShopGroup, $module->contextShop);
            }
        }
        return $module->displayConfirmation($module->l('Updated configuration', 'Configuration'));
    }
}
