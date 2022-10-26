<?php
/**
 * Blog for PrestaShop module by PrestaHome Team.
 *
 * @author    PrestaHome Team <support@prestahome.com>
 * @copyright Copyright (c) 2011-2021 PrestaHome Team - www.PrestaHome.com
 * @license   You only can use module, nothing more!
 */
require_once _PS_MODULE_DIR_ . 'ph_simpleblog/ph_simpleblog.php';

class AdminSimpleBlogCommentsController extends ModuleAdminController
{
    public $is_16;

    public function __construct()
    {
        $this->table = 'simpleblog_comment';
        $this->className = 'SimpleBlogComment';

        $this->bootstrap = true;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->is_16 = (bool) (version_compare(_PS_VERSION_, '1.6.0', '>=') === true);

        parent::__construct();

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
            ],
            'enableSelection' => ['text' => $this->l('Enable selection')],
            'disableSelection' => ['text' => $this->l('Disable selection')],
        ];

        $this->_select = 'sbpl.title AS `post_title`';

        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'simpleblog_post_lang` sbpl ON (sbpl.`id_simpleblog_post` = a.`id_simpleblog_post` AND sbpl.`id_lang` = ' . (int) Context::getContext()->language->id . ')';

        $this->fields_list = [
            'id_simpleblog_comment' => [
                'title' => $this->l('ID'),
                'type' => 'int',
                'align' => 'center',
                'width' => 25,
            ],
            'id_simpleblog_post' => [
                'title' => $this->l('Post ID'),
                'type' => 'int',
                'align' => 'center',
                'width' => 25,
            ],
            'post_title' => [
                'title' => $this->l('Comment for'),
                'width' => 'auto',
            ],
            'name' => [
                'title' => $this->l('Name'),
            ],
            'email' => [
                'title' => $this->l('E-mail'),
            ],
            'comment' => [
                'title' => $this->l('Comment'),
                'width' => 'auto',
            ],
            'active' => [
                'title' => $this->l('Status'),
                'width' => 70,
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
            ],
        ];
    }

    public function renderForm()
    {
        $id_lang = $this->context->language->id;
        $obj = $this->loadObject(true);

        $this->fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Comment'),
            ],
            'input' => [
                [
                    'type' => 'hidden',
                    'name' => 'id_simpleblog_post',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'id_customer',
                    'label' => $this->l('Customer'),
                ],
                [
                    'type' => 'text',
                    'name' => 'id_simpleblog_post',
                    'label' => $this->l('Post ID'),
                ],
                [
                    'type' => 'text',
                    'name' => 'name',
                    'label' => $this->l('Name'),
                    'required' => false,
                    'lang' => false,
                ],
                [
                    'type' => 'text',
                    'name' => 'email',
                    'label' => $this->l('E-mail'),
                    'required' => false,
                    'lang' => false,
                ],
                [
                    'type' => 'text',
                    'name' => 'ip',
                    'label' => $this->l('IP Address'),
                    'required' => false,
                    'lang' => false,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Comment'),
                    'name' => 'comment',
                    'cols' => 75,
                    'rows' => 7,
                    'required' => false,
                    'lang' => false,
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Displayed'),
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
                'name' => 'savePostComment',
            ],
        ];

        $this->multiple_fieldsets = true;

        $SimpleBlogPost = new SimpleBlogPost($obj->id_simpleblog_post, $id_lang);

        $this->tpl_form_vars = [
            'customerLink' => $this->context->link->getAdminLink('AdminCustomers'),
            'blogPostLink' => $this->context->link->getAdminLink('AdminSimpleBlogPost'),
            'blogPostName' => $SimpleBlogPost->meta_title,
        ];

        return parent::renderForm();
    }
}
