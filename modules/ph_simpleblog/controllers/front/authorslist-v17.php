<?php
/**
 * Blog for PrestaShop module by PrestaHome Team.
 *
 * @author    PrestaHome Team <support@prestahome.com>
 * @copyright Copyright (c) 2011-2021 PrestaHome Team - www.PrestaHome.com
 * @license   You only can use module, nothing more!
 */
class PH_SimpleBlogAuthorsListModuleFrontController extends DefaultListBlogForPrestaShopController
{
    protected $SimpleBlogAuthor;

    protected $blogAuthors = [];

    public function init()
    {
        parent::init();

        $this->blogAuthors = SimpleBlogPostAuthor::getAuthors();

        $this->assignMetas();

        $this->canonicalRedirection();
    }

    public function canonicalRedirection($canonical_url = '')
    {
        $this->module->canonicalRedirection($this->context->link->getModuleLink('ph_simpleblog', 'authorslist'));
    }

    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign('authors', $this->blogAuthors);

        $this->setTemplate('module:ph_simpleblog/views/templates/front/1.7/authors.tpl');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();


        $breadcrumb['links'][] = [
            'title' => $this->l('Authors', 'author-v17'),
            'url' => $this->context->link->getModuleLink('ph_simpleblog', 'authorslist'),
        ];

        return $breadcrumb;
    }

    /**
     * Assign meta tags to single post page.
     */
    public function assignMetas()
    {
        $this->context->smarty->assign('meta_title', $this->module->l('Authors', 'authorslist-v17'));
    }
}
