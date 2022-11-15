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

class IdxrcookiesCookieType extends ObjectModel
{
    public $name;

    public $description;

    public $imperative;

    public static $definition = array(
        'table' => 'idxcookies_type',
        'primary' => 'id_cookie_type',
        'multilang' => true,
        'helper_form_identifier' => 'CookieType',
        'fields' => array(
            'imperative' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'name' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
                'lang' => true,
                'size' => 64,
            ),
            'description' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'lang' => true,
            ),
        ),
    );

    public function getHelperFormInputs($module)
    {
        $inputs = array(
            array(
                'type' => 'hidden',
                'name' => 'id'
            ),
            array(
                'type' => 'text',
                'name' => 'name',
                'label' => $module->l('Name', 'CookieType'),
                'required' => true,
                'lang' => true,
                'desc' => $module->l('The name for cookie type', 'CookieType')
            ),
            array(
                'type' => 'text',
                'name' => 'description',
                'label' => $module->l('Description', 'CookieType'),
                'required' => true,
                'lang' => true,
                'desc' => $module->l('Text to show in the cookies warning contaniner about this kind of cookies', 'CookieType'),
                'autoload_rte' => true,
                'cols' => 60,
                'rows' => 30
            ),
            array(
                'type' => 'switch',
                'label' => $module->l('Imperative', 'CookieType'),
                'name' => 'imperative',
                'desc' => $module->l('This kinds of cookies are imperatives for normal work of the web?', 'CookieType'),
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $module->l('Enabled', 'CookieType')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $module->l('Disabled', 'CookieType')
                    )
                ),
            ),
        );
        return $inputs;
    }

    protected static function getHelperListFields($module, $filtros = array())
    {
        $fields_list = array(
            'id_cookie_type' => array(
                'title' => 'Id',
                'align' => 'left',
                'rows' => 50,
                'search' => false,
                'order' => false,
            ),
            'name' => array(
                'title' => $module->l('Name', 'CookieType'),
                'align' => 'left',
                'search' => false,
                'order' => false,
            ),
            'description' => array(
                'title' => $module->l('Description', 'CookieType'),
                'align' => 'left',
                'search' => false,
                'order' => false,
            ),
            'imperative' => array(
                'title' => $module->l('Imperative', 'CookieType'),
                'align' => 'left',
                'search' => false,
                'order' => false,
            ),

        );
        return $fields_list;
    }

    public static function getInstanceByKey($key)
    {
        $exist_q = 'Select id_cookie_type from ' . _DB_PREFIX_ . self::$definition['table'].' where name = "' . pSQL($key) .'"';
        $cookie_type_id = Db::getInstance()->getValue($exist_q);
        return new self((int)$cookie_type_id);
    }

    public function setKey($key)
    {
        $datos = array(
            'name' => pSQL($key)
        );
        return Db::getInstance()->update(
            self::$definition['table'],
            $datos,
            'id_cookie_type='.(int)$this->id
        );
    }

    public static function getDefaults()
    {
        return array(
            'required' => array(
                'imperative' => true,
                'es' => array(
                    'name' => 'Cookies necesarias',
                    'description' => 'Estas cookies son extrictamente necesarias para el funcionamiento de la página, las puede desactivar cambiando la configuración de su navegador pero no podrá usar la página con normalidad.'
                ),
                'en' => array(
                    'name' => 'Required cookies',
                    'description' => 'These cookies are strictly necessary for the operation of the site, you can disable them by changing the settings of your browser but you will not be able to use the site normally.'
                ),
                'it' => array(
                    'name' => 'Richiesti',
                    'description' => 'Questi cookie sono necessari per il funzionamento del sito, è possibile disabilitarli modificando le impostazioni del browser ma non sarà possibile utilizzare il sito normalmente.'
                ),
                'de' => array(
                    'name' => 'Required cookies',
                    'description' => 'These cookies are strictly necessary for the operation of the site, you can disable them by changing the settings of your browser but you will not be able to use the site normally.'
                ),
                'fr' => array(
                    'name' => 'Cookies nécessaires',
                    'description' => 'Ces cookies sont strictement nécessaires au fonctionnement du site, vous pouvez les désactiver en modifiant les paramètres de votre navigateur mais vous ne pourrez pas utiliser le site normalement.'
                )
            ),
            'functional' => array(
                'imperative' => false,
                'es' => array(
                    'name' => 'Cookies funcionales',
                    'description' => 'Estas cookies proveen información necesarias a aplicaciones de la propia web o integradas de terceros, si las inhabilita puede que encuentre algunos problemas de funcionarmiento en la página.'
                ),
                'en' => array(
                    'name' => 'Functional cookies',
                    'description' => 'These cookies provide necessary information to applications of the website itself or integrated by third parties, if you disable them you may find some problems in the operation of the page.'
                ),
                'it' => array(
                    'name' => 'Funzionali',
                    'description' => 'Questi cookie forniscono le informazioni necessarie alle applicazioni del sito Web stesso o integrate da terze parti, se le disabiliti potresti riscontrare alcuni problemi nel funzionamento della pagina.'
                ),
                'de' => array(
                    'name' => 'Functional cookies',
                    'description' => 'These cookies provide necessary information to applications of the website itself or integrated by third parties, if you disable them you may find some problems in the operation of the page.'
                ),
                'fr' => array(
                    'name' => 'Cookies fonctionnels',
                    'description' => 'Ces cookies fournissent les informations nécessaires aux applications du site lui-même ou intégrées par des tiers, si vous les désactivez, vous pouvez rencontrer des problèmes dans le fonctionnement de la page.'
                )
            ),
            'performance' => array(
                'imperative' => false,
                'es' => array(
                    'name' => 'Cookies de rendimiento',
                    'description' => 'Estas cookies se usan para analizar el trafico y comportamiento de los clientes en la página, nos ayudan a entender y conocer como se interactua con la web con el objetivo de mejorar el funcionamiento.'
                ),
                'en' => array(
                    'name' => 'Performance cookies',
                    'description' => 'These cookies are used to analyze the traffic and behavior of customers on the site, help us understand and understand how you interact with the site in order to improve performance.'
                ),
                'it' => array(
                    'name' => 'Prestazioni',
                    'description' => 'Questi cookie vengono utilizzati per analizzare il traffico e il comportamento dei clienti sul sito, aiutarci a capire e comprendere come interagisci con il sito al fine di migliorare le prestazioni.'
                ),
                'de' => array(
                    'name' => 'Performance cookies',
                    'description' => 'These cookies are used to analyze the traffic and behavior of customers on the site, help us understand and understand how you interact with the site in order to improve performance.'
                ),
                'fr' => array(
                    'name' => 'Cookies de performance',
                    'description' => 'Ces cookies sont utilisés pour analyser le trafic et le comportement des clients sur le site, nous aider à comprendre et comprendre comment vous interagissez avec le site afin d\'améliorer les performances.'
                )
            ),
            'guided' => array(
                'imperative' => false,
                'es' => array(
                    'name' => 'Cookies dirigidas',
                    'description' => 'Estas cookies pueden ser del propio sitio o de terceros, nos ayudan a crear un perfil de sus intereses y ofrecerle una publicidad dirigida a sus gustos e intereses.'
                ),
                'en' => array(
                    'name' => 'Guided cookies',
                    'description' => 'These cookies can be from the site itself or from third parties, they help us to create a profile of your interests and to offer you advertising aimed at your preferences and interests.'
                ),
                'it' => array(
                    'name' => 'Guidati',
                    'description' => 'Questi cookie possono provenire dal sito stesso o da terze parti, ci aiutano a creare un profilo dei tuoi interessi e a offrirti pubblicità mirata alle tue preferenze e interessi.'
                ),
                'de' => array(
                    'name' => 'Guided cookies',
                    'description' => 'These cookies can be from the site itself or from third parties, they help us to create a profile of your interests and to offer you advertising aimed at your preferences and interests.'
                ),
                'fr' => array(
                    'name' => 'Cookies guidés',
                    'description' => 'Ces cookies peuvent provenir du site lui-même ou de tiers, ils nous aident à créer un profil de vos intérêts et à vous proposer de la publicité en fonction de vos préférences et intérêts.'
                )
            )
        );
    }

    public static function getCookieTypes($id_cookie_type = false, $lang_id = false)
    {
        $sql = 'Select * from ' . _DB_PREFIX_ . self::$definition['table'].' ct '
            . 'left join ' ._DB_PREFIX_ .self::$definition['table'].'_lang ctl on ct.id_cookie_type = ctl.id_cookie_type ';

        if ($lang_id) {
            $sql .= 'where ctl.id_lang = ' . (int)$lang_id;
        } else {
            $sql .= 'where ctl.id_lang = ' . (int)Context::getContext()->language->id;
        }

        if ($id_cookie_type) {
            $sql .= ' and ct.id_cookie_type = ' . (int)$id_cookie_type;
        }

        $result = Db::getInstance()->executeS($sql);
        if ($lang_id) {
            foreach ($result as $keyRes => $valRes) {
                $result[$keyRes]['name'][$lang_id] = Tools::stripslashes($valRes['name'][$lang_id]);
                $result[$keyRes]['description'][$lang_id] = Tools::stripslashes($valRes['description'][$lang_id]);
            }
            return $result;
        } else {
            $total_expected = Db::getInstance()->getValue('Select count(*) from '._DB_PREFIX_.self::$definition['table']);
            if (count($result) == $total_expected) {
                foreach ($result as $keyRes => $valRes) {
                    foreach (Language::getLanguages(true) as $lang) {
                        $result[$keyRes]['name'][$lang['id_lang']] = Tools::stripslashes($valRes['name'][$lang['id_lang']]);
                        $result[$keyRes]['description'][$lang['id_lang']] = Tools::stripslashes($valRes['description'][$lang['id_lang']]);
                    }
                }
                return $result;
            } else {
                $types = Db::getInstance()->executeS('Select * from ' . _DB_PREFIX_ . self::$definition['table']);
                foreach ($types as $type) {
                    $exist = false;
                    foreach ($result as $value) {
                        if ($type['id_cookie_type'] == $value['id_cookie_type']) {
                            $exist = true;
                            break;
                        }
                    }
                    if (!$exist) {
                        $sql = $sql = 'Select * from ' . _DB_PREFIX_ . self::$definition['table']." ct "
                            . ' left join ' ._DB_PREFIX_ .self::$definition['table'].'_lang ctl on ct.id_cookie_type = ctl.id_cookie_type where ct.id_cookie_type = '.(int)$type['id_cookie_type'];
                        $result[] = Db::getInstance()->getRow($sql);
                    }
                }
            }
            foreach ($result as $keyRes => $valRes) {
                foreach (Language::getLanguages(true) as $lang) {
                    $result[$keyRes]['name'][$lang['id_lang']] = Tools::stripslashes($valRes['name'][$lang['id_lang']]);
                    $result[$keyRes]['description'][$lang['id_lang']] = Tools::stripslashes($valRes['description'][$lang['id_lang']]);
                }
            }
            return $result;
        }
    }

    public function getFormValues()
    {
        $values = parent::getFormValues();
        foreach (Language::getLanguages(true) as $lang) {
            $values['name'][$lang['id_lang']] = Tools::stripslashes($values['name'][$lang['id_lang']]);
            $values['description'][$lang['id_lang']] = Tools::stripslashes($values['description'][$lang['id_lang']]);
        }
        return $values;
    }

    public static function listado($filtros = array(), $limite = array(), $num_only = false, $orderBy = null, $orderDireccion = null)
    {
        $list = parent::listado($filtros, $limite, $num_only, $orderBy, $orderDireccion);
        if ($num_only) {
            return $list;
        }
        foreach ($list as $keyRes => $valRes) {
            foreach (Language::getLanguages(true) as $lang) {
                $list[$keyRes]['name'][$lang['id_lang']] = Tools::stripslashes($valRes['name'][$lang['id_lang']]);
                $list[$keyRes]['description'][$lang['id_lang']] = Tools::stripslashes($valRes['description'][$lang['id_lang']]);
            }
        }

        return $list;
    }
}
