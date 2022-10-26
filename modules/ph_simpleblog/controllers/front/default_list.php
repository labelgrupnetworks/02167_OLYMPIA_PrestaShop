<?php

use PrestaShop\PrestaShop\Core\Product\Search\Pagination;

class DefaultListBlogForPrestaShopController extends ModuleFrontController
{
    public $posts_per_page;
    public $n;
    public $p;

    protected $blogCategory = null;
    protected $blogAuthor = null;
    protected $blogPost = null;

    /** @var ph_simpleblog $module */
    public $module;

    public function init()
    {
        parent::init();

        $this->posts_per_page = Configuration::get('PH_BLOG_POSTS_PER_PAGE');
        $this->p = (int) Tools::getValue('p', 1);
    }

    public function initContent()
    {
        parent::initContent();

        $this->assignGeneralPurposesVariables();
    }

    /**
     * CSS, JS and other assets for this page.
     */
    protected function addModulePageAssets()
    {
    }

    public function assignGeneralPurposesVariables()
    {
        $gridType = Configuration::get('PH_BLOG_COLUMNS');
        $gridColumns = Configuration::get('PH_BLOG_GRID_COLUMNS');
        $blogLayout = Configuration::get('PH_BLOG_LIST_LAYOUT');

        $this->context->smarty->assign([
            'categories' => SimpleBlogCategory::getCategories((int) $this->context->language->id),
            'blogMainTitle' => Configuration::get('PH_BLOG_MAIN_TITLE', (int) $this->context->language->id),
            'grid' => Configuration::get('PH_BLOG_COLUMNS'),
            'columns' => $gridColumns,
            'blogLayout' => $blogLayout,
            'useMasonry' => Configuration::get('PH_BLOG_MASONRY_LAYOUT'),
            'module_dir' => _MODULE_DIR_ . 'ph_simpleblog/',
            'tpl_path' => _PS_MODULE_DIR_ . 'ph_simpleblog/views/templates/front/',
            'gallery_dir' => _MODULE_DIR_ . 'ph_simpleblog/galleries/',
            'is_category' => Validate::isLoadedObject($this->blogCategory),
            'is_search' => false,
            'isWarehouse' => $this->module->isWarehouse,
        ]);
    }

    public function assignMetas()
    {
        $pageVariables = $this->getTemplateVarPage();
        $defaultMetaTitleForBlog = Configuration::get('PH_BLOG_MAIN_TITLE', $this->context->language->id);
        $defaultMetaDescriptionForBlog = Configuration::get('PH_BLOG_MAIN_META_DESCRIPTION', $this->context->language->id);

        if (Validate::isLoadedObject($this->blogCategory)) {
            $meta_title = $this->blogCategory->name . ' - ' . $pageVariables['meta']['title'];
            if (!empty($this->blogCategory->meta_title)) {
                $meta_title = $this->blogCategory->meta_title . ' - ' . $pageVariables['meta']['title'];
            }
        } else {
            if (empty($defaultMetaTitleForBlog)) {
                $meta_title = $pageVariables['meta']['title'] . ' ' . $this->l('Blog', 'default_list');
            } else {
                $meta_title = $defaultMetaTitleForBlog;
            }
        }

        if (Validate::isLoadedObject($this->blogCategory)) {
            if (!empty($this->blogCategory->meta_description)) {
                $meta_description = $this->blogCategory->meta_description;
            } else {
                $meta_description = $pageVariables['meta']['description'];
            }
        } else {
            $meta_description = empty($defaultMetaDescriptionForBlog) ? $pageVariables['meta']['description'] : $defaultMetaDescriptionForBlog;
        }

        if ($this->p > 1) {
            $meta_title .= ' (' . $this->p . ')';
        }

        $this->context->smarty->assign('meta_title', $meta_title);
        $this->context->smarty->assign('meta_description', strip_tags($meta_description));
    }

    /**
     * Get all informations about paginated results
     * @return array pagination data
     */
    public function getTemplateVarPagination()
    {
        $pagination = new Pagination();
        $pagination
            ->setPage($this->p)
            ->setPagesCount(
                (int) ceil(sizeof($this->posts) / $this->posts_per_page)
            )
        ;

        $totalItems = sizeof($this->posts);
        $itemsShownFrom = ($this->posts_per_page * ($this->p - 1)) + 1;
        $itemsShownTo = $this->posts_per_page * $this->p;

        $type = 'list';
        $rewrite = false;

        if (Validate::isLoadedObject($this->blogCategory)) {
            $rewrite = $this->blogCategory->link_rewrite;
            $type = 'category';
        }

        if (Validate::isLoadedObject($this->blogAuthor)) {
            $rewrite = $this->blogAuthor->link_rewrite;
            $type = 'author';
        }

        $pages = array_map(function ($link) use ($type, $rewrite) {
            $link['url'] = SimpleBlogPost::getPageLink($link['page'], $type, $rewrite);

            return $link;
        }, $pagination->buildLinks());

        //Filter next/previous link on first/last page
        $pages = array_filter($pages, function ($page) use ($pagination) {
            if ('previous' === $page['type'] && 1 === $pagination->getPage()) {
                return false;
            }
            if ('next' === $page['type'] && $pagination->getPagesCount() === $pagination->getPage()) {
                return false;
            }

            return true;
        });

        return [
            'total_items' => $totalItems,
            'items_shown_from' => $itemsShownFrom,
            'items_shown_to' => ($itemsShownTo <= $totalItems) ? $itemsShownTo : $totalItems,
            'current_page' => $pagination->getPage(),
            'pages_count' => $pagination->getPagesCount(),
            'pages' => $pages,
            // Compare to 3 because there are the next and previous links
            'should_be_displayed' => (count($pagination->buildLinks()) > 3),
        ];
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->l('Blog', 'default_list'),
            'url' => $this->context->link->getModuleLink('ph_simpleblog', 'list'),
        ];

        return $breadcrumb;
    }
}
