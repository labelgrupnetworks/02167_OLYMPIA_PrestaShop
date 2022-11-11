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

class IdxrcookiesTemplate extends ObjectModel
{
    public $nombre;

    public $hook;

    public $contenido;

    public $active;

    public $tag_script;

    public $tag_literal;

    public $date_add;

    public $date_upd;

    public static $definition = array(
        'table' => 'idxcookies_templates',
        'primary' => 'id_template',
        'multilang' => true,
        'multilang_shop' => true,
        'helper_form_identifier' => 'Template',
        'fields' => array(
            'nombre' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 150,
            ),
            'hook' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isHookName',
                'required' => true,
                'size' => 255,
            ),
            'active' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'tag_script' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'tag_literal' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'contenido' => array(
                'type' => self::TYPE_NOTHING,
                'validate' => 'isAnything',
                'lang' => true,
                'shop' => true
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
        if ($module->es17) {
            $optionsHooks = array(
                array(
                    'id' => 'displayHeader',
                    'name' => $module->l('Header of pages', 'Template'),
                ),
                array(
                    'id' => 'displayBeforeBodyClosingTag',
                    'name' => $module->l('Very bottom of pages', 'Template'),
                ),
                array(
                    'id' => 'displayAfterBodyOpeningTag',
                    'name' => $module->l('Very top of pages', 'Template'),
                )
            );
        } else {
            $optionsHooks = array(
                array(
                    'id' => 'displayHeader',
                    'name' => $module->l('Header of pages', 'Template'),
                ),
                array(
                    'id' => 'displayFooter',
                    'name' => $module->l('Very bottom of pages', 'Template'),
                ),
            );
        }

        $inputs = array(
            array(
                'type' => 'hidden',
                'name' => 'id'
            ),
            array(
                'type' => 'text',
                'name' => 'nombre',
                'label' => $module->l('Name', 'Template'),
                'required' => true,
                'hint' => $module->l('Internal use only', 'Template')
            ),
            array(
                'type' => 'select',
                'label' => $module->l('Hook', 'Template'),
                'name' => 'hook',
                'desc' => $module->l('Position of the template', 'Template'),
                'options' => array(
                    'query' => $optionsHooks,
                    'id' => 'id',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'textarea',
                'rows' => 5,
                'name' => 'contenido',
                'label' => $module->l('Content of template', 'Template'),
                'desc' => $module->l('You can use html, javascript or smarty code.', 'Template'),
                'lang' => true,
            ),
            array(
                'type' => 'switch',
                'name' => 'tag_script',
                'label' => $module->l('Add script tag', 'Template'),
                'desc' => $module->l('Place your code between script html tags.', 'Template'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $module->l('On', 'Template')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $module->l('Off', 'Template')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'name' => 'tag_literal',
                'label' => $module->l('Add literal tag', 'Template'),
                'desc' => $module->l('Place your code between literal smarty tags. Smarty code will not be interpreted', 'Template'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $module->l('On', 'Template')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $module->l('Off', 'Template')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'name' => 'active',
                'label' => $module->l('Active', 'Template'),
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $module->l('On', 'Template')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $module->l('Off', 'Template')
                    )
                ),
            ),
        );
        return $inputs;
    }

    protected static function getHelperListFields($module, $filtros = array())
    {
        $fields_list = array(
            'id_template' => array(
                'title' => $module->l('Id', 'Template'),
                'align' => 'left',
                'search' => true,
                'order' => true,
            ),
            'nombre' => array(
                'title' => $module->l('Name', 'Template'),
                'align' => 'left',
                'search' => true,
            ),
            'hook' => array(
                'title' => $module->l('Hook', 'Template'),
                'align' => 'left',
                'search' => true,
            ),
            'active' => array(
                'title' => $module->l('Active', 'Template'),
                'align' => 'center',
                'search' => true,
                'active' => 'active',
                'type' => 'bool',
                'orderBy' => false,
            ),

        );
        return $fields_list;
    }
}
