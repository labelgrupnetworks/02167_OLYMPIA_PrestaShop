<?php
/**
 * Blog for PrestaShop module by Krystian Podemski from PrestaHome.
 *
 * @author    Krystian Podemski <krystian@prestahome.com>
 * @copyright Copyright (c) 2008-2020 Krystian Podemski - www.PrestaHome.com / www.Podemski.info
 * @license   You only can use module, nothing more!
 */
require_once _PS_MODULE_DIR_ . 'ph_simpleblog/ph_simpleblog.php';

class AdminSimpleBlogAuthorsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'simpleblog_author';
        $this->className = 'SimpleBlogPostAuthor';
        $this->lang = true;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->bootstrap = true;

        parent::__construct();

        $this->bulk_actions = ['delete' => ['text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')]];

        $this->_select = 'IFNULL(sbp.posts, 0) as number_of_posts';
        $this->_join = 'LEFT JOIN (SELECT id_simpleblog_author, COUNT(`id_simpleblog_post`) as posts FROM ' . _DB_PREFIX_ . 'simpleblog_post GROUP BY id_simpleblog_author) sbp ON a.id_simpleblog_author = sbp.id_simpleblog_author';

        $this->fields_list = [
            'id_simpleblog_author' => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 30,
            ],
            'firstname' => [
                'title' => $this->l('Firstname'),
                'width' => 'auto',
            ],
            'lastname' => [
                'title' => $this->l('Lastname'),
                'width' => 'auto',
            ],
            'email' => [
                'title' => $this->l('E-mail'),
                'width' => 'auto',
            ],
            'number_of_posts' => [
                'title' => $this->l('Posts'),
                'width' => 'auto',
            ],
            'active' => [
                'title' => $this->l('Active'),
                'width' => 25,
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false,
            ],
        ];
    }

    public function initFormToolBar()
    {
        unset($this->toolbar_btn['back']);
        $this->toolbar_btn['save-and-stay'] = [
            'short' => 'SaveAndStay',
            'href' => '#',
            'desc' => $this->l('Save and stay'),
        ];
        $this->toolbar_btn['back'] = [
            'href' => self::$currentIndex . '&token=' . Tools::getValue('token'),
            'desc' => $this->l('Back to list'),
        ];
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();

        $this->addJS([
            _MODULE_DIR_ . 'ph_simpleblog/js/admin.js',
        ]);

        Media::addJsDef([
            'PS_ALLOW_ACCENTED_CHARS_URL' => Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')
        ]);
    }

    public function renderForm()
    {
        $this->initFormToolBar();
        if (!$this->loadObject(true)) {
            return;
        }

        $obj = $this->loadObject(true);

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Author'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Firstname:'),
                    'name' => 'firstname',
                    'lang' => false,
                ],

                [
                    'type' => 'text',
                    'label' => $this->l('Lastname:'),
                    'name' => 'lastname',
                    'lang' => false,
                ],

                [
                    'type' => 'select_image',
                    'label' => $this->l('Photo:'),
                    'name' => 'photo',
                    'lang' => false,
                    'desc' => $this->l('Module will not crop your photo, it is recommended to use something around 400x400 px'),
                ],

                [
                    'type' => 'textarea',
                    'label' => $this->l('Bio:'),
                    'name' => 'bio',
                    'lang' => true,
                    'rows' => 5,
                    'cols' => 40,
                    'autoload_rte' => true,
                ],

                // [
                //     'type' => 'textarea',
                //     'label' => $this->l('Additional info:'),
                //     'name' => 'additional_info',
                //     'lang' => true,
                //     'rows' => 5,
                //     'cols' => 40,
                //     'autoload_rte' => true,
                // ],

                [
                    'type' => 'text',
                    'label' => $this->l('E-mail:'),
                    'name' => 'email',
                    'lang' => false,
                ],

                // [
                //     'type' => 'text',
                //     'label' => $this->l('Phone:'),
                //     'name' => 'phone',
                //     'lang' => false,
                // ],

                [
                    'type' => 'text',
                    'label' => $this->l('Facebook:'),
                    'name' => 'facebook',
                    'lang' => false,
                ],

                [
                    'type' => 'text',
                    'label' => $this->l('Instagram:'),
                    'name' => 'instagram',
                    'lang' => false,
                ],

                [
                    'type' => 'text',
                    'label' => $this->l('Twitter:'),
                    'name' => 'twitter',
                    'lang' => false,
                ],

                // [
                //     'type' => 'text',
                //     'label' => $this->l('Google:'),
                //     'name' => 'google',
                //     'lang' => false,
                // ],

                [
                    'type' => 'text',
                    'label' => $this->l('LinkedIn:'),
                    'name' => 'linkedin',
                    'lang' => false,
                ],

                // [
                //     'type' => 'text',
                //     'label' => $this->l('WWW:'),
                //     'name' => 'www',
                //     'lang' => false,
                // ],

                [
                    'type' => 'text',
                    'label' => $this->l('Friendly URL:'),
                    'desc' => $this->l('for example: firstname-lastname or author-nickname'),
                    'name' => 'link_rewrite',
                    'required' => true,
                    'lang' => false,
                    'class' => 'use-str2url',
                ],

                [
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];

        return parent::renderForm();
    }
}
