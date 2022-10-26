<?php
/**
 * Blog for PrestaShop module by Krystian Podemski from PrestaHome.
 *
 * @author    Krystian Podemski <krystian@prestahome.com>
 * @copyright Copyright (c) 2008-2020 Krystian Podemski - www.PrestaHome.com / www.Podemski.info
 * @license   You only can use module, nothing more!
 */
class PH_SimpleBlogAuthorModuleFrontController extends DefaultListBlogForPrestaShopController
{
    protected $blogAuthor;

    public function init()
    {
        parent::init();

        $SimpleBlogAuthor = SimpleBlogPostAuthor::getByRewrite(Tools::getValue('rewrite'));

        if (!Validate::isLoadedObject($SimpleBlogAuthor)) {
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
            Tools::redirect($this->context->link->getPageLink('404'));
        }

        $this->blogAuthor = $SimpleBlogAuthor;

        // Assign meta tags
        $this->assignMetas();
    }

    public function initContent()
    {
        parent::initContent();

        $finder = new BlogPostsFinder();
        $finder->setAuthor($this->blogAuthor->id);
        $this->posts = $finder->findPosts();

        // Assign JS and CSS for single post page
        $this->addModulePageAssets();

        $pagination = $this->getTemplateVarPagination();
        $this->context->smarty->assign('pagination', $pagination);

        $this->posts = array_splice($this->posts, $this->p ? ($this->p - 1) * $this->posts_per_page : 0, $this->posts_per_page);
        $this->context->smarty->assign('posts', $this->posts);
        $this->context->smarty->assign('author', $this->blogAuthor);

        Configuration::set('PH_BLOG_DISPLAY_CATEGORY_CHILDREN', false);

        $this->setTemplate('module:ph_simpleblog/views/templates/front/1.7/author-posts-list.tpl');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $id_lang = $this->context->language->id;

        if (Validate::isLoadedObject($this->blogAuthor)) {
            $breadcrumb['links'][] = [
                'title' => $this->l('Authors', 'author-v17'),
                'url' => $this->context->link->getModuleLink('ph_simpleblog', 'authorslist'),
            ];
            $breadcrumb['links'][] = [
                'title' => $this->blogAuthor,
                'url' => $this->blogAuthor->getUrl(),
            ];
        }

        return $breadcrumb;
    }

    /**
     * Assign meta tags to single post page.
     */
    public function assignMetas()
    {
        parent::assignMetas();

        $this->context->smarty->assign('meta_title', sprintf($this->module->l('Posts by %s', 'author-v17'), $this->blogAuthor));
        $this->context->smarty->assign('meta_description', false);
    }

    /**
     * Return SimpleBlogPost object
     *
     * @return object
     */
    public function getAuthor()
    {
        return $this->blogAuthor;
    }
}
