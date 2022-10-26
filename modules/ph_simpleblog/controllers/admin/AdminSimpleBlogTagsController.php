<?php
/**
 * Blog for PrestaShop module by PrestaHome Team.
 *
 * @author    PrestaHome Team <support@prestahome.com>
 * @copyright Copyright (c) 2011-2021 PrestaHome Team - www.PrestaHome.com
 * @license   You only can use module, nothing more!
 */
require_once _PS_MODULE_DIR_ . 'ph_simpleblog/ph_simpleblog.php';
class AdminSimpleBlogTagsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'simpleblog_tag';
        $this->className = 'SimpleBlogTag';

        $this->bootstrap = true;

        parent::__construct();

        $this->fields_list = [
            'id_simpleblog_tag' => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 25,
            ],
            'lang' => [
                'title' => $this->l('Language'),
                'filter_key' => 'l!name',
                'width' => 100,
            ],
            'name' => [
                'title' => $this->l('Name'),
                'width' => 'auto',
                'filter_key' => 'a!name',
            ],
            'posts' => [
                'title' => $this->l('Posts:'),
                'align' => 'center',
                'width' => 50,
                'havingFilter' => true,
            ],
        ];

        $this->bulk_actions = ['delete' => ['text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')]];
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->_select = 'l.name as lang, COUNT(pt.id_simpleblog_post) as posts';
        $this->_join = '
            LEFT JOIN `' . _DB_PREFIX_ . 'simpleblog_post_tag` pt
                ON (a.`id_simpleblog_tag` = pt.`id_simpleblog_tag`)
            LEFT JOIN `' . _DB_PREFIX_ . 'lang` l
                ON (l.`id_lang` = a.`id_lang`)';
        $this->_group = 'GROUP BY a.name, a.id_lang';

        return parent::renderList();
    }

    public function postProcess()
    {
        if (isset($this->tabAccess['edit']) && $this->tabAccess['edit'] === '1' && Tools::getValue('submitAdd' . $this->table)) {
            if (($id = (int) Tools::getValue($this->identifier)) && ($obj = new $this->className($id)) && Validate::isLoadedObject($obj)) {
                $previousPosts = $obj->getPosts();
                $removedPosts = [];

                foreach ($previousPosts as $post) {
                    if (!in_array($post['id_simpleblog_post'], $_POST['posts'])) {
                        $removedPosts[] = $post['id_simpleblog_post'];
                    }
                }

                $obj->setPosts($_POST['posts']);
            }
        }

        return parent::postProcess();
    }

    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Tag'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Name:'),
                    'name' => 'name',
                    'required' => true,
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Language:'),
                    'name' => 'id_lang',
                    'required' => true,
                    'options' => [
                        'query' => Language::getLanguages(false),
                        'id' => 'id_lang',
                        'name' => 'name',
                    ],
                ],
            ],
            'selects' => [
                'posts' => $obj->getPosts(true),
                'posts_unselected' => $obj->getPosts(false),
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'button',
            ],
        ];

        return parent::renderForm();
    }
}
