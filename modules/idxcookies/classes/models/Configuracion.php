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

class IdxrCookiesConfiguracion extends IdxrConfiguration_2_0
{
    public static function getValoresIniciales($module)
    {
        $lang_examples = Language::getLanguages(false);
        $cookies_url_example = array();
        $cookies_url_title_example = array();
        $text_cookies_example = array();
        $text_delete_example = array();

        foreach ($lang_examples as $lang_example) {
            $cookies_url_example[$lang_example['id_lang']] = '#';
            $cookies_url_title_example[$lang_example['id_lang']] = 'Ver política de cookies';
            $text_cookies_example[$lang_example['id_lang']] = '
                Utilizamos Cookies propias y de terceros para recopilar información para mejorar nuestros servicios y para análisis de tus hábitos de navegación. Si continuas navegando, supone la aceptación de la instalación de las mismas. Puedes configurar tu navegador para impedir su instalación.
            ';
            $text_delete_example[$lang_example['id_lang']] = '
                <p>Se informa al usuario de que tiene la posibilidad de configurar su navegador de modo que se le informe de la recepción de cookies, pudiendo, si así lo desea, impedir que sean instaladas en su disco duro.</p>
<p>A continuación le proporcionamos los enlaces de diversos navegadores, a través de los cuales podrá realizar dicha configuración:</p>
<p><strong><em>Firefox desde aquí:</em></strong> <a target="_blank" href="https://support.mozilla.org/t5/Cookies-y-cach%C3%A9/Habilitar-y-deshabilitar-cookies-que-los-sitios-web-utilizan/ta-p/13811">http://support.mozilla.org/es/kb/habilitar-y-deshabilitar-cookies-que-los-sitios-web</a></p>
<p><strong><em>Chrome desde aquí:</em></strong> <a target="_blank" href="https://support.google.com/chrome/answer/95647?hl=es">https://support.google.com/chrome/answer/95647?hl=es</a></p>
<p><strong><em>Explorer desde aquí:</em></strong><span> <a target="_blank" href="https://support.microsoft.com/es-es/help/17442/windows-internet-explorer-delete-manage-cookies">https://support.microsoft.com/es-es/help/17442/windows-internet-explorer-delete-manage-cookies</a></span></p>
<p><strong><em>Safari desde aquí: </em></strong><a target="_blank" href="https://support.apple.com/kb/ph5042?locale=es_ES"><span>http://support.apple.com/kb/ph5042</span></a></p>
<p><strong><em>Opera desde aquí:</em></strong><a target="_blank" href="http://help.opera.com/Windows/11.50/es-ES/cookies.html"><span>http://help.opera.com/Windows/11.50/es-ES/cookies.html</span></a></p>
            ';
        }
        return array(
            'DIV_POSITION' => 'top',
            'TEXT_COLOR' => '#ffffff',
            'DIV_COLOR' => '#383838',
            'COOKIES_URL' => $cookies_url_example,
            'COOKIES_URL_TITLE' => $cookies_url_title_example,
            'TEXT' => $text_cookies_example,
            'INFOTEXT' => $text_cookies_example,
            'DELETECOOKIESTEXT' => $text_delete_example,
            'FIXED_BUTTON' => 0,
            'BUTTON_POSITION' => 'right',
            'RELOAD' => 1,
        );
    }

    public static function getVariablesConfiguracion($module)
    {
        $position_options = array(
            array(
                'id_option' => 'top',
                'name' => $module->l('Top', 'Configuracion')
            ),
            array(
                'id_option' => 'center',
                'name' => $module->l('Center', 'Configuracion')
            ),
            array(
                'id_option' => 'bottom',
                'name' => $module->l('Bottom', 'Configuracion')
            ),
            array(
                'id_option' => 'left',
                'name' => $module->l('Left', 'Configuracion')
            ),
            array(
                'id_option' => 'right',
                'name' => $module->l('Right', 'Configuracion')
            )
        );

        $button_position_options =  array(
            array(
                'id_option' => 'left',
                'name' => $module->l('Left', 'Configuracion')
            ),
            array(
                'id_option' => 'right',
                'name' => $module->l('Right', 'Configuracion')
            )
        );

        $cms_options=$module->getCmsSelectLinks();

        $variables = array(
            'HEADING' => array(
                'type' => 'alert',
                'text' => $module->l('The module provides the technical functionality to display the relevant notices in your online store, but all texts included in this module are sample texts, we do not guarantee that they comply with the law. You should consult a legal advisor to provide you with the appropriate texts for your store.', 'Configuracion'),
            ),
            'DIV_COLOR' => array(
                'type' => 'color',
                'label' => $module->l('Background colour', 'Configuracion'),
                'desc' => $module->l('Colour for the container background', 'Configuracion'),
                'validate' => 'isColor',
            ),
            'DIV_POSITION' => array(
                'type' => 'select',
                'label' => $module->l('Position', 'Configuracion'),
                'desc' => $module->l('Container position: top, middle, bottom', 'Configuracion'),
                'options' => array(
                    'query' => $position_options,
                    'id' => 'id_option',
                    'name' => 'name'
                ),
                'validate' => 'isString'
            ),
            'COOKIES_SELECTOR' => array(
                'type' => 'select',
                'label' => $module->l('Select Cookies URL', 'Configuracion'),
                'desc' => $module->l('Select CMS of your cookies text', 'Configuracion'),
                'options' => array(
                    'query' => $cms_options,
                    'id' => 'id',
                    'name' => 'name'
                ),
                'validate' => 'isString'
            ),
            'COOKIES_URL' => array(
                'type' => 'text',
                'label' => $module->l('Cookies URL', 'Configuracion'),
                'lang' => true,
                'class' => 'jselect',
                'desc' => $module->l('The URL to your cookies text', 'Configuracion'),
                'validate' => 'isUrl'
            ),
            'COOKIES_URL_TITLE' => array(
                'type' => 'text',
                'label' => $module->l('Cookies URL title', 'Configuracion'),
                'lang' => true,
                'validate' => 'isString',
                'desc' => $module->l('The title for your cookies link', 'Configuracion')
            ),
            'TEXT_COLOR' => array(
                'type' => 'color',
                'label' => $module->l('Text colour', 'Configuracion'),
                'desc' => $module->l('Colour for the text to show in the cookies warning contaniner', 'Configuracion'),
                'validate' => 'isColor',
            ),
            'REJECT_BUTTON' => array(
                'type' => 'switch',
                'label' => $module->l('Show `reject cookies` button', 'Configuracion'),
                'desc' => $module->l('Shows a button to reject all not funtional cookies in cookies warning container.', 'Configuracion'),
                'values' => array(
                    array(
                        'id' => 'show_on',
                        'value' => 1,
                        'label' => $module->l('Show', 'Configuracion')
                    ),
                    array(
                        'id' => 'show_off',
                        'value' => 0,
                        'label' => $module->l('Hide', 'Configuracion')
                    )
                ),
                'validate' => 'isBool',
            ),
            'ACCEPT_SELECTED_BUTTON' => array(
                'type' => 'switch',
                'label' => $module->l('Show `accept selected cookies` button', 'Configuracion'),
                'desc' => $module->l('Shows a button to accept only selected button.', 'Configuracion'),
                'values' => array(
                    array(
                        'id' => 'show_on',
                        'value' => 1,
                        'label' => $module->l('Show', 'Configuracion')
                    ),
                    array(
                        'id' => 'show_off',
                        'value' => 0,
                        'label' => $module->l('Hide', 'Configuracion')
                    )
                ),
                'validate' => 'isBool',
            ),
            'COOKIES_SELECTED' => array(
                'type' => 'switch',
                'label' => $module->l('All cookies selected', 'Configuracion'),
                'desc' => $module->l('If active, all cookies will be selected by default. If not, only necessary cookies will be selected by default', 'Configuracion'),
                'values' => array(
                    array(
                        'id' => 'show_on',
                        'value' => 1,
                        'label' => $module->l('Show', 'Configuracion')
                    ),
                    array(
                        'id' => 'show_off',
                        'value' => 0,
                        'label' => $module->l('Hide', 'Configuracion')
                    )
                ),
                'validate' => 'isBool',
            ),
            'RELOAD' => array(
                'type' => 'switch',
                'label' => $module->l('Reload after first accept?', 'Configuracion'),
                'desc' => $module->l('If active, module will reload page so all the modules and templates can insert content to the page. If not, module will insert templates into page', 'Configuracion'),
                'values' => array(
                    array(
                        'id' => 'show_on',
                        'value' => 1,
                        'label' => $module->l('Show', 'Configuracion')
                    ),
                    array(
                        'id' => 'show_off',
                        'value' => 0,
                        'label' => $module->l('Hide', 'Configuracion')
                    )
                ),
                'validate' => 'isBool',
            ),
            'BLOCK_USER_NAV' => array(
                'type' => 'switch',
                'label' => $module->l('Block user navigation?', 'Configuracion'),
                'desc' => $module->l('This option blocks user navigation until acception or rejection of cookies', 'Configuracion'),
                'values' => array(
                    array(
                        'id' => 'show_on',
                        'value' => 1,
                        'label' => $module->l('Show', 'Configuracion')
                    ),
                    array(
                        'id' => 'show_off',
                        'value' => 0,
                        'label' => $module->l('Hide', 'Configuracion')
                    )
                ),
                'validate' => 'isBool',
            ),
            'FIXED_BUTTON' => array(
                'type' => 'switch',
                'label' => $module->l('Show fixed button', 'Configuracion'),
                'desc' => $module->l('After accept cookies left a fixed button for configure again the cookies, also can add the class cookiesConfButton to any element of your page', 'Configuracion'),
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'show_on',
                        'value' => 1,
                        'label' => $module->l('Show', 'Configuracion')
                    ),
                    array(
                        'id' => 'show_off',
                        'value' => 0,
                        'label' => $module->l('Hide', 'Configuracion')
                    )
                ),
                'validate' => 'isBool',
            ),
            'BUTTON_POSITION' => array(
                'type' => 'select',
                'label' => $module->l('Fixed button position', 'Configuracion'),
                'desc' => $module->l(': top, middle, bottom', 'Configuracion'),
                'options' => array(
                    'query' => $button_position_options,
                    'id' => 'id_option',
                    'name' => 'name'
                ),
                'validate' => 'isString'
            ),
            'TEXT' => array(
                'type' => 'textarea',
                'label' => $module->l('Warning text', 'Configuracion'),
                'lang' => true,
                'autoload_rte' => true,
                'desc' => $module->l('Text to show in the cookies warning contaniner', 'Configuracion'),
                'cols' => 60,
                'rows' => 30,
                'validate' => 'isCleanHtml',
                'html' => true,
            ),
            'INFOTEXT' => array(
                'type' => 'textarea',
                'label' => $module->l('Info text', 'Configuracion'),
                'lang' => true,
                'autoload_rte' => true,
                'desc' => $module->l('Text to show in the cookies popup contaniner where the customer can customize cookie options', 'Configuracion'),
                'cols' => 60,
                'rows' => 30,
                'validate' => 'isCleanHtml',
                'html' => true,
            ),
            'DELETECOOKIESTEXT' => array(
                'type' => 'textarea',
                'label' => $module->l('How to delete cookies text', 'Configuracion'),
                'lang' => true,
                'autoload_rte' => true,
                'desc' => $module->l('Text to show in the cookies popup contaniner describing how to delete cookies', 'Configuracion'),
                'cols' => 60,
                'rows' => 30,
                'validate' => 'isCleanHtml',
                'html' => true,
            ),
        );

        return $variables;
    }
    
    public static function getConfigValues($prefix, $module)
    {
        $vars = self::getVariablesConfiguracion($module);
        foreach ($vars as $index => $var) {
            if (array_key_exists('autoload_rte', $var) && $var['autoload_rte']) {
                if (array_key_exists('lang', $var) && $var['lang']) {
                    $decodetext = array();
                    foreach (Language::getLanguages(true) as $lang) {
                        $decodetext[$lang['id_lang']] = urldecode(Configuration::get($index, $lang['id_lang']));
                    }
                } else {
                    $decodetext = urldecode(Configuration::get($index));
                }
                Configuration::updateValue($index, $decodetext);
            }
        }
        return parent::getConfigValues($prefix, $module);
    }
}
