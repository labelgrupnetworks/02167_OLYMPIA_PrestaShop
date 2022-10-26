<?php
/**
 * Blog for PrestaShop module by PrestaHome Team.
 *
 * @author    PrestaHome Team <support@prestahome.com>
 * @copyright Copyright (c) 2011-2021 PrestaHome Team - www.PrestaHome.com
 * @license   You only can use module, nothing more!
 */
class PH_SimpleBlogAuthorsListModuleFrontController extends ModuleFrontController
{
    protected $blogAuthors;

    public function init()
    {
        parent::init();

        $this->blogAuthors = SimpleBlogPostAuthor::getAll();

        // Assign meta tags
        $this->assignMetas();
    }

    public function initContent()
    {
        // Assign JS and CSS for single post page
        $this->addModulePageAssets();

        parent::initContent();

        $this->context->smarty->assign('authors', $this->blogAuthors);

        $this->setTemplate('authors.tpl');
    }

    /**
     * Assign meta tags to single post page.
     */
    protected function assignMetas()
    {
        $defaultMetaTitleForBlog = Configuration::get('PH_BLOG_MAIN_TITLE', $this->context->language->id);
        $this->context->smarty->assign('meta_title', $defaultMetaTitleForBlog . ' - ' . $this->module->l('Authors', 'authorslist-v16'));
    }

    /**
     * CSS, JS and other assets for this page.
     */
    protected function addModulePageAssets()
    {
    }
}
