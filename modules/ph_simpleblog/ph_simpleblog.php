<?php
/**
 * Blog for PrestaShop module by PrestaHome Team.
 *
 * @author    PrestaHome Team <support@prestahome.com>
 * @copyright Copyright (c) 2011-2021 PrestaHome Team - www.PrestaHome.com
 * @license   You only can use module, nothing more!
 */
if (!defined('THUMBLIB_BASE_PATH') && file_exists(_PS_MODULE_DIR_.'ph_simpleblog/assets/phpthumb/ThumbLib.inc.php')) {
    require_once _PS_MODULE_DIR_.'ph_simpleblog/assets/phpthumb/ThumbLib.inc.php';
}

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

require_once _PS_MODULE_DIR_.'ph_simpleblog/models/SimpleBlogCategory.php';
require_once _PS_MODULE_DIR_.'ph_simpleblog/models/SimpleBlogPost.php';
require_once _PS_MODULE_DIR_.'ph_simpleblog/models/SimpleBlogPostType.php';
require_once _PS_MODULE_DIR_.'ph_simpleblog/models/SimpleBlogPostImage.php';
require_once _PS_MODULE_DIR_.'ph_simpleblog/models/SimpleBlogTag.php';
require_once _PS_MODULE_DIR_.'ph_simpleblog/models/SimpleBlogComment.php';
require_once _PS_MODULE_DIR_.'ph_simpleblog/models/SimpleBlogPostAuthor.php';
require_once _PS_MODULE_DIR_.'ph_simpleblog/classes/SimpleBlogHelper.php';
require_once _PS_MODULE_DIR_.'ph_simpleblog/classes/BlogPostsFinder.php';
require_once _PS_MODULE_DIR_.'ph_simpleblog/controllers/front/default_list.php';

define('_SIMPLEBLOG_GALLERY_DIR_', _PS_MODULE_DIR_.'ph_simpleblog/galleries/');
define('_SIMPLEBLOG_GALLERY_URL_', _MODULE_DIR_.'ph_simpleblog/galleries/');

if (!defined('_PS_VERSION_')) {
    exit;
}

class ph_simpleblog extends Module
{
    public $is_16;
    public $is_17;

    // Not used at this moment, some issues with PrestaShop 1.7.x
    public $moduleTabs = array(
        array(
            'name' => array(
                'en' => 'Blog for PrestaShop',
                'pl' => 'Blog dla PrestaShop',
            ),
            'class_name' => 'AdminBlogForPrestaShop',
            'parent_class_name' => 'IMPROVE',
        ),
        array(
            'name' => array(
                'en' => 'Posts',
                'pl' => 'Wpisy',
            ),
            'class_name' => 'AdminSimpleBlogPosts',
            'parent_class_name' => 'AdminBlogForPrestaShop',
        ),
        array(
            'name' => array(
                'en' => 'Categories',
                'pl' => 'Kategorie',
            ),
            'class_name' => 'AdminSimpleBlogCategories',
            'parent_class_name' => 'AdminBlogForPrestaShop',
        ),
        array(
            'name' => array(
                'en' => 'Comments',
                'pl' => 'Komentarze',
            ),
            'class_name' => 'AdminSimpleBlogComments',
            'parent_class_name' => 'AdminBlogForPrestaShop',
        ),
        array(
            'name' => array(
                'en' => 'Tags',
                'pl' => 'Tagi',
            ),
            'class_name' => 'AdminSimpleBlogTags',
            'parent_class_name' => 'AdminBlogForPrestaShop',
        ),
        array(
            'name' => array(
                'en' => 'Authors',
                'pl' => 'Autorzy',
            ),
            'class_name' => 'AdminSimpleBlogAuthors',
            'parent_class_name' => 'AdminBlogForPrestaShop',
        ),
        array(
            'name' => array(
                'en' => 'Settings',
                'pl' => 'Ustawienia',
            ),
            'class_name' => 'AdminSimpleBlogSettings',
            'parent_class_name' => 'AdminBlogForPrestaShop',
        ),
    );

    public $isWarehouse = false;

    public function __construct()
    {
        $this->name = 'ph_simpleblog';
        $this->tab = 'front_office_features';
        $this->version = '1.8.0';
        $this->author = 'PrestaHome';
        $this->need_instance = 0;
        $this->is_configurable = 1;
        $this->ps_versions_compliancy['min'] = '1.6';
        $this->ps_versions_compliancy['max'] = _PS_VERSION_;
        $this->secure_key = Tools::encrypt($this->name);
        $this->is_16 = (version_compare(_PS_VERSION_, '1.6', '>=') === true && version_compare(_PS_VERSION_, '1.7', '<') === true);
        $this->is_17 = (version_compare(_PS_VERSION_, '1.7', '>=') === true);

        $this->controllers = [
            'single',
            'list',
            'category',
            'categorypage',
            'page',
            'author',
            'authorslist',
            'authorpage',
        ];

        $this->bootstrap = true;

        if (Shop::isFeatureActive()) {
            Shop::addTableAssociation('simpleblog_category', array('type' => 'shop'));
            Shop::addTableAssociation('simpleblog_post', array('type' => 'shop'));
        }

        parent::__construct();

        $this->displayName = $this->l('Blog for PrestaShop');
        $this->description = $this->l('Adds a blog to your PrestaShop store');

        $this->confirmUninstall = $this->l('Are you sure you want to delete this module?');

        if ($this->isWarehouseTheme()) {
            $this->isWarehouse = true;
        }
    }

    public function isUsingNewTranslationSystem()
    {
        return false;
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        // Hooks & Install
        return parent::install()
            && $this->registerHook('gSitemapAppendUrls')
            && $this->registerHook('moduleRoutes')
            && $this->registerHook('displaySimpleBlogPosts')
            && $this->registerHook('displaySimpleBlogCategories')
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('displayPrestaHomeBlogAfterPostContent')
            && $this->registerHook('displayLeftColumn')
            && $this->registerHook('registerGDPRConsent')
            && $this->registerHook('actionDeleteGDPRCustomer')
            && $this->registerHook('actionExportGDPRData')
            && $this->prepareModuleSettings()
        ;
    }

    /**
     * Install module tab with specified arguments.
     *
     * @param string $name
     * @param string $className
     * @param int    $id_parent
     *
     * @return object new Tab instance
     */
    public function myInstallModuleTab($name, $className, $id_parent)
    {
        $tab = new Tab();
        $tab->name = array();
        $tab->class_name = $className;

        if ($className == 'AdminBlogForPrestaShop' && $this->is_17) {
            $tab->icon = 'note';
        }

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $name;
        }
        $tab->id_parent = (int) $id_parent;
        $tab->module = $this->name;

        if ($tab->save()) {
            return $tab;
        }
    }

    /**
     * Delete module tabs.
     */
    public function myDeleteModuleTabs()
    {
        $tabs = array(
            'AdminSimpleBlog',
            'AdminSimpleBlogCategories',
            'AdminSimpleBlogPosts',
            'AdminSimpleBlogSettings',
            'AdminSimpleBlogTags',
            'AdminSimpleBlogComments',
            'AdminSimpleBlogAuthors',
            'AdminBlogForPrestaShop',
            'AdminSimpleBlogPostsParent',
        );

        $idTabs = array();
        foreach ($tabs as $className) {
            $idTabs[] = Tab::getIdFromClassName($className);
        }

        foreach ($idTabs as $idTab) {
            if ($idTab) {
                $tab = new Tab($idTab);
                if (Validate::isLoadedObject($tab)) {
                    $tab->delete();
                }
            }
        }
    }

    /**
     * Install all module tabs.
     */
    public function createAllModuleTabs()
    {
        // Main tab
        $mainTab = $this->myInstallModuleTab(
            $this->l('Blog for PrestaShop'),
            $this->is_17 ? 'AdminBlogForPrestaShop' : 'AdminSimpleBlog',
            Tab::getIdFromClassName('IMPROVE')
        );

        // Posts
        $this->myInstallModuleTab(
            $this->l('Posts'),
            'AdminSimpleBlogPosts',
            (int) $mainTab->id
        );

        // Categories
        $this->myInstallModuleTab(
            $this->l('Categories'),
            'AdminSimpleBlogCategories',
            (int) $mainTab->id
        );

        // Comments
        $this->myInstallModuleTab(
            $this->l('Comments'),
            'AdminSimpleBlogComments',
            (int) $mainTab->id
        );

        // Tags
        $this->myInstallModuleTab(
            $this->l('Tags'),
            'AdminSimpleBlogTags',
            (int) $mainTab->id
        );

        // Authors
        $this->myInstallModuleTab(
            $this->l('Authors'),
            'AdminSimpleBlogAuthors',
            (int) $mainTab->id
        );

        // Settings
        $this->myInstallModuleTab(
            $this->l('Settings'),
            'AdminSimpleBlogSettings',
            (int) $mainTab->id
        );
    }

    /**
     * Check if the theme used in the store a Warehouse theme
     * 
     * @return bool
     */
    protected function isWarehouseTheme()
    {
        return Configuration::get('PH_BLOG_WAREHOUSE_COMPAT')
            || ($this->is_17 && isset($this->context->shop->theme_name) && (strpos($this->context->shop->theme_name, 'warehouse') !== false));
    }

    public function hasFieldInDatabaseIfNotCreateOne($table, $fieldName, $fieldSpecification)
    {
        if (empty($table) || empty($fieldName) || empty($fieldSpecification)) {
            throw new Exception('Cannot check for database integration issues');
        }

        $sql = 'SHOW COLUMNS FROM `'._DB_PREFIX_.$table.'`';
        $tableColumns = Db::getInstance()->executeS($sql);

        $createColumn = true;
        foreach ($tableColumns as $column) {
            if ($column['Field'] == $fieldName) {
                $createColumn = false;
            }
        }

        if ($createColumn == true) {
            Db::getInstance()->query('ALTER TABLE `'._DB_PREFIX_.pSQL($table).'` ADD `'.pSQL($fieldName).'` '.pSQL($fieldSpecification));
        }
    }

    /**
     * This methods check wether we need to update db schema because of
     * well known problem between ~1.6.0.4 and 1.6.0.6 where "hide_post_mode" wasn't available.
     */
    protected function checkDatabaseIntegrationProblems()
    {
        $sql = 'SHOW COLUMNS FROM `'._DB_PREFIX_.'tab`';
        $tab_columns = Db::getInstance()->executeS($sql);

        $createColumn = true;
        foreach ($tab_columns as $column) {
            if ($column['Field'] == 'hide_host_mode') {
                $createColumn = false;
            }
        }

        if ($createColumn == true) {
            Db::getInstance()->query('ALTER TABLE `'._DB_PREFIX_.'tab` ADD `hide_host_mode` tinyint(1) NOT NULL DEFAULT 0 AFTER  `active`');
        }
    }

    /**
     * Setup default content for module.
     */
    protected function setupBlogDefaultContent()
    {
        // Default category
        $simple_blog_category = new SimpleBlogCategory();

        foreach (Language::getLanguages(true) as $lang) {
            $simple_blog_category->name[$lang['id_lang']] = 'News';
        }

        foreach (Language::getLanguages(true) as $lang) {
            $simple_blog_category->link_rewrite[$lang['id_lang']] = 'news';
        }
        $simple_blog_category->add();
        $simple_blog_category->associateTo(Shop::getCompleteListOfShopsID());

        // Post Types
        $default_post_type = new SimpleBlogPostType();
        $default_post_type->name = 'Post';
        $default_post_type->slug = 'post';
        $default_post_type->description = $this->l('Default type of post');
        $default_post_type->add();

        $gallery_post_type = new SimpleBlogPostType();
        $gallery_post_type->name = 'Gallery';
        $gallery_post_type->slug = 'gallery';
        $gallery_post_type->add();

        $external_url_post_type = new SimpleBlogPostType();
        $external_url_post_type->name = $this->l('External URL');
        $external_url_post_type->slug = 'url';
        $external_url_post_type->add();

        $video_post_type = new SimpleBlogPostType();
        $video_post_type->name = $this->l('Video');
        $video_post_type->slug = 'video';
        $video_post_type->add();
    }

    public static function getLanguagesIDs()
    {
        $langs = Language::getLanguages(false);
        $ids = array();
        foreach ($langs as $lang) {
            $ids[] = $lang['id_lang'];
        }

        return $ids;
    }

    protected function getSimpleBlogSettings()
    {
        if (function_exists('date_default_timezone_get')) {
            $timezone = @date_default_timezone_get();
        } else {
            $timezone = 'Europe/Warsaw';
        }

        return array(
            'PH_BLOG_SLUG' => 'blog',
            'PH_BLOG_POSTS_PER_PAGE' => 10,
            'PH_BLOG_FB_COMMENTS' => true,
            'PH_BLOG_COLUMNS' => 'prestashop',
            'PH_BLOG_LAYOUT' => 'default',
            'PH_BLOG_LIST_LAYOUT' => 'grid',
            'PH_BLOG_GRID_COLUMNS' => 2,
            'PH_BLOG_MAIN_TITLE' => array_fill_keys(self::getLanguagesIDs(), $this->l('Blog - whats new?')),
            'PH_BLOG_LOAD_FA' => false,
            'PH_BLOG_DISPLAY_AUTHOR' => true,
            'PH_BLOG_DISPLAY_DATE' => true,
            'PH_BLOG_DISPLAY_THUMBNAIL' => true,
            'PH_BLOG_DISPLAY_CATEGORY' => true,
            'PH_BLOG_DISPLAY_SHARER' => true,
            'PH_BLOG_DISPLAY_TAGS' => true,
            'PH_BLOG_DISPLAY_DESCRIPTION' => true,
            'PH_BLOG_THUMB_METHOD' => 1,
            'PH_BLOG_THUMB_X' => 600,
            'PH_BLOG_THUMB_Y' => 300,
            'PH_BLOG_THUMB_X_WIDE' => 900,
            'PH_BLOG_THUMB_Y_WIDE' => 350,
            'PH_BLOG_DISPLAY_CAT_DESC' => true,
            'PH_BLOG_POST_BY_AUTHOR' => true,
            'PH_BLOG_FB_INIT' => true,
            'PH_BLOG_DISPLAY_FEATURED' => true,
            'PH_BLOG_DISPLAY_BREADCRUMBS' => true,
            'PH_BLOG_DISPLAY_FEATURED' => true,
            'PH_BLOG_DISPLAY_CATEGORY_IMAGE' => true,
            'PH_BLOG_DISPLAY_LIKES' => true,
            'PH_BLOG_DISPLAY_VIEWS' => true,
            'PH_CATEGORY_IMAGE_X' => 900,
            'PH_CATEGORY_IMAGE_Y' => 250,
            'PH_CATEGORY_SORTBY' => 'position',
            'PH_BLOG_DATEFORMAT' => '%B %e, %Y',
            'PH_BLOG_NATIVE_COMMENTS' => true,
            'PH_BLOG_COMMENT_NOTIFICATIONS' => true,
            'PH_BLOG_NEW_AUTHORS' => 0,
            'PH_BLOG_AUTHOR_INFO' => true,
            'PH_BLOG_COMMENT_AUTO_APPROVAL' => false,
            'PH_BLOG_COMMENT_ALLOW' => true,
            'PH_BLOG_COMMENT_STUFF_HIGHLIGHT' => true,
            'PH_BLOG_COMMENT_NOTIFY_EMAIL' => Configuration::get('PS_SHOP_EMAIL'),
            'PH_BLOG_FACEBOOK_MODERATOR' => '',
            'PH_BLOG_FACEBOOK_APP_ID' => '',
            'PH_BLOG_FACEBOOK_COLOR_SCHEME' => 'light',
            'PH_BLOG_DISPLAY_MORE' => true,
            'PH_BLOG_MASONRY_LAYOUT' => false,
            'PH_BLOG_TIMEZONE' => $timezone,
            'PH_BLOG_LOAD_FONT_AWESOME' => true,
            'PH_BLOG_LOAD_BXSLIDER' => true,
            'PH_BLOG_LOAD_MASONRY' => true,
            'PH_BLOG_LOAD_FITVIDS' => true,
            'PH_BLOG_DISPLAY_RELATED' => true,
            'PH_BLOG_COMMENT_ALLOW_GUEST' => false,
            'PH_BLOG_COMMENTS_RECAPTCHA' => false,
            'PH_BLOG_COMMENTS_RECAPTCHA_SITE_KEY' => '',
            'PH_BLOG_COMMENTS_RECAPTCHA_SECRET_KEY' => '',
            'PH_BLOG_COMMENTS_RECAPTCHA_THEME' => 'light',
            'PH_BLOG_RELATED_PRODUCTS_USE_DEFAULT_LIST' => false,
            'PH_BLOG_COMMENTS_SYSTEM' => 'native',
            'PH_BLOG_DISQUS_SHORTNAME' => 'blogforprestashop',
            'PH_BLOG_CANONICAL' => '',
            'PH_BLOG_IMAGE_FBSHARE' => 'featured',
        );
    }

    public function prepareModuleSettings()
    {
        // Database
        $sql = array();
        include_once dirname(__FILE__).'/init/install_sql.php';
        foreach ($sql as $s) {
            if (!Db::getInstance()->Execute($s)) {
                die('Error while creating DB');
            }
        }
        $this->checkDatabaseIntegrationProblems();

        // Tabs
        $this->createAllModuleTabs();

        // Default content
        $this->setupBlogDefaultContent();

        // Make sure controllers are not added to gsitemap
        $this->makeControllersAsNotConfigurable();

        // Settings
        foreach ($this->getSimpleBlogSettings() as $key => $value) {
            Configuration::updateValue($key, $value);
        }

        // For theme developers, you're welcome!
        if (file_exists(_PS_MODULE_DIR_.$this->name.'/init/my-install.php')) {
            include_once _PS_MODULE_DIR_.$this->name.'/init/my-install.php';
        }

        return true;
    }

    public function uninstall()
    {
        // Database
        $sql = array();
        include_once dirname(__FILE__).'/init/uninstall_sql.php';
        foreach ($sql as $s) {
            if (!Db::getInstance()->Execute($s)) {
                return false;
            }
        }

        // Settings
        foreach ($this->getSimpleBlogSettings() as $key => $value) {
            Configuration::deleteByName($key);
        }

        // Tabs
        $this->myDeleteModuleTabs();

        // For theme developers - you're welcome!
        if (file_exists(_PS_MODULE_DIR_.$this->name.'/init/my-uninstall.php')) {
            include_once _PS_MODULE_DIR_.$this->name.'/init/my-uninstall.php';
        }

        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }

    protected function myAssignModuleAssets()
    {
        if ($this->is_16) {
            if (Configuration::get('PH_BLOG_LOAD_BXSLIDER')) {
                $this->context->controller->addCSS($this->_path.'css/jquery.bxslider.css');
            }

            if (Configuration::get('PH_BLOG_LOAD_FONT_AWESOME')) {
                $this->context->controller->addCSS($this->_path.'css/font-awesome.css');
            }

            $this->context->controller->addCSS($this->_path.'css/ph_simpleblog.css');
            $this->context->controller->addCSS($this->_path.'css/custom.css');

            if (Configuration::get('PH_BLOG_LOAD_FITVIDS')) {
                $this->context->controller->addJS($this->_path.'js/jquery.fitvids.js');
            }

            if (Configuration::get('PH_BLOG_LOAD_BXSLIDER')) {
                $this->context->controller->addJS($this->_path.'js/jquery.bxslider.min.js');
            }

            $this->context->controller->addJS($this->_path.'js/masonry.pkgd.min.js');
            
            $this->context->controller->addJS($this->_path.'js/ph_simpleblog.js');
            $this->context->controller->addJqueryPlugin(array('bxslider', 'fancybox'));
        }

        if ($this->is_17) {
            $this->context->controller->registerStylesheet('modules-phsimpleblog', 'modules/'.$this->name.'/views/css/ph_simpleblog-17.css', array('media' => 'all', 'priority' => 150));
            $this->context->controller->registerStylesheet('modules-phsimpleblog-custom', 'modules/'.$this->name.'/css/custom.css', array('position' => 'bottom', 'priority' => 150));
            $this->context->controller->registerJavascript('modules-phsimpleblog', 'modules/'.$this->name.'/views/js/ph_simpleblog-17.js', array('position' => 'bottom', 'priority' => 150));

            if (Configuration::get('PH_BLOG_LOAD_FITVIDS')) {
                $this->context->controller->addJS($this->_path.'js/jquery.fitvids.js');
            }

            $this->context->controller->registerJavascript('modules-phsimpleblog-masonry', 'modules/'.$this->name.'/js/masonry.pkgd.min.js', array('position' => 'bottom', 'priority' => 150));
            

            $this->context->controller->addJqueryPlugin(array('fancybox'));
            $this->context->controller->requireAssets(array('font-awesome-css'));
        }
    }

    /**
     * @inheritDoc
     */
    public function hookDisplaySimpleBlogPosts()
    {
        // No implementation needed inside the main module
    }

    /**
     * @inheritDoc
     */
    public function hookRegisterGDPRConsent()
    {
        // No implementation needed inside the main module
    }

    /**
     * @inheritDoc
     */
    public function hookActionDeleteGDPRCustomer()
    {
        // No implementation needed inside the main module
    }

    /**
     * @inheritDoc
     */
    public function hookActionExportGDPRData()
    {
        // No implementation needed inside the main module
    }

    public function hookDisplayHeader($params)
    {
        $this->myAssignModuleAssets();

        if (Tools::getValue('module') && Tools::getValue('module') == 'ph_simpleblog' && Tools::getValue('controller') == 'single') {
            $post = $this->context->controller->getPost();

            $imageForFacebook = Configuration::get('PH_BLOG_IMAGE_FBSHARE', 'featured') == 'featured' ? 'featured_image' : 'banner';

            $this->context->smarty->assign(array(
                'post_url' => $post->url,
                'post_title' => $post->title,
                'post_description' => strip_tags($post->short_content),
                'post_image' => rtrim($this->context->shop->getBaseUrl(true, false), '/').$post->{$imageForFacebook},
            ));

            Media::addJsDef(
                array(
                    'ph_sharing_name' => addcslashes($post->title, "'"),
                    'ph_sharing_url' => addcslashes($post->url, "'"),
                    'ph_sharing_img' => addcslashes(rtrim($this->context->shop->getBaseUrl(true, false), '/').$post->{$imageForFacebook}, "'"),
                )
            );

            return $this->display(__FILE__, 'header.tpl');
        }
    }

    public function hookModuleRoutes($params)
    {
        $blogSlug = Configuration::get('PH_BLOG_SLUG');

        $authorsSlug = 'authors';

        $routes = array(
            // Home list
            'module-ph_simpleblog-list' => array(
                'controller' => 'list',
                'rule' => $blogSlug,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ph_simpleblog',
                ),
            ),

            // Home pagination
            'module-ph_simpleblog-page' => array(
                'controller' => 'page',
                'rule' => $blogSlug.'/page/{p}',
                'keywords' => array(
                    'p' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'p',
                    ),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ph_simpleblog',
                ),
            ),

            // Single author page
            'module-ph_simpleblog-author' => array(
                'controller' => 'author',
                'rule' => $blogSlug.'/'.$authorsSlug.'/{rewrite}',
                'keywords' => array(
                    'rewrite' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'rewrite',
                    ),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ph_simpleblog',
                ),
            ),

            // Authors pagination
            'module-ph_simpleblog-authorpage' => array(
                'controller' => 'authorpage',
                'rule' => $blogSlug.'/'.$authorsSlug.'/{rewrite}/page/{p}',
                'keywords' => array(
                    'p' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'p',
                    ),
                    'rewrite' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'rewrite',
                    ),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ph_simpleblog',
                ),
            ),

            // Authors list
            'module-ph_simpleblog-authorslist' => array(
                'controller' => 'authorslist',
                'rule' => $blogSlug.'/'.$authorsSlug,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ph_simpleblog',
                ),
            ),

            // Category list
            'module-ph_simpleblog-category' => array(
                'controller' => 'category',
                'rule' => $blogSlug.'/{sb_category}',
                'keywords' => array(
                    'sb_category' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'sb_category',
                    ),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ph_simpleblog',
                ),
            ),

            // Category pagination
            'module-ph_simpleblog-categorypage' => array(
                'controller' => 'categorypage',
                'rule' => $blogSlug.'/{sb_category}/page/{p}',
                'keywords' => array(
                    'p' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'p',
                    ),
                    'sb_category' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'sb_category',
                    ),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ph_simpleblog',
                ),
            ),

            // Single
            'module-ph_simpleblog-single' => array(
                'controller' => 'single',
                'rule' => $blogSlug.'/{sb_category}/{rewrite}',
                'keywords' => array(
                    'sb_category' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'sb_category',
                    ),
                    'rewrite' => array(
                        'regexp' => '[_a-zA-Z0-9-\pL]*',
                        'param' => 'rewrite',
                    ),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ph_simpleblog',
                ),
            ),
        );

        return $routes;
    }

    public static function myRealURL()
    {
        return Context::getContext()->shop->getBaseUrl(true);
    }

    public static function getLink()
    {
        return Context::getContext()->link->getModuleLink('ph_simpleblog', 'list');
    }

    public function prepareSimpleBlogCategories()
    {
        $this->context->smarty->assign(array(
            'categories' => SimpleBlogCategory::getCategories($this->context->language->id, true),
        ));
    }

    public function hookDisplaySimpleBlogCategories($params)
    {
        $this->prepareSimpleBlogCategories();

        if (isset($params['template'])) {
            return $this->display(__FILE__, $params['template'].'.tpl');
        } else {
            return $this->hookDisplayLeftColumn($params);
        }
    }

    public function hookDisplayLeftColumn($params)
    {
        $this->prepareSimpleBlogCategories();

        if ($this->is_17) {
            return $this->fetch('module:'.$this->name.'/views/templates/hook/1.7/left-column.tpl');
        }

        return $this->display(__FILE__, 'left-column.tpl');
    }

    public function hookDisplayRightColumn($params)
    {
        return $this->hookDisplayLeftColumn($params);
    }

    public function hookDisplayHome($params)
    {
        return $this->hookDisplayLeftColumn($params);
    }

    public function hookDisplayFooter($params)
    {
        return $this->hookDisplayLeftColumn($params);
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (method_exists($this->context->controller, 'addCSS')) {
            $this->context->controller->addCSS(($this->_path).'css/simpleblog-admin.css', 'all');
        }
    }

    public function hookDisplayPrestaHomeBlogAfterPostContent($params)
    {
        if ($this->is_17) {
            return $this->fetch('module:'.$this->name.'/views/templates/hook/1.7/after-post-content.tpl');
        }

        return $this->display(__FILE__, 'after-post-content.tpl');
    }

    public function getContent()
    {
        $this->smarty->assign('phpWarning', version_compare(phpversion(), '7.1', '<'));
        $this->smarty->assign('module_path', _MODULE_DIR_.'ph_simpleblog/');
        $this->smarty->assign('shopUrl', $this->context->shop->getBaseUrl());

        return $this->display(__FILE__, 'views/templates/admin/welcome.tpl');
    }

    /**
     * Set configurable=0 for controllers to exclude them from gsitemap
     */
    public function makeControllersAsNotConfigurable()
    {
        foreach ($this->controllers as $controller) {
            $page = 'module-' . $this->name . '-' . $controller;
            $idMeta = Db::getInstance()->getValue('SELECT `id_meta` FROM ' . _DB_PREFIX_ . 'meta WHERE page="' . pSQL($page) . '"');
            if ((int) $idMeta > 0) {
                $meta = new Meta((int) $idMeta);
                $meta->configurable = 0;
                $meta->save();
            }
        }

        return true;
    }

    public function hookgSitemapAppendUrls($params)
    {
        $lang = $params['lang'];
        $idLang = (int) $lang['id_lang'];
        $idShop = (int) $lang['id_shop'];

        $categories = SimpleBlogCategory::getCategories($idLang, true, true, $idShop);

        $blogCategoriesLinks = array();
        $blogPostLinks = array();

        foreach ($categories as $category) {
            $blogCategoriesLinks[] = array(
                'type' => 'module',
                'page' => $category['name'],
                'link' => $category['url'],
                'image' => false,
            );
        }

        $finder = new BlogPostsFinder();
        $finder->setIdShop($idShop);
        $finder->setIdLang($idLang);

        $posts = $finder->findPosts();

        foreach ($posts as $post) {
            $blogPostLinks[] = array(
                'type' => 'module',
                'page' => $post['title'],
                'link' => $post['url'],
                'image' => false,
                'lastmod' => ($post['date_upd'] == '0000-00-00 00:00:00') ? $post['date_add'] : $post['date_upd'],
            );
        }

        return array_merge($blogCategoriesLinks, $blogPostLinks);
    }

    public function canonicalRedirection($canonical_url = '')
    {
        if (!$canonical_url || !Configuration::get('PS_CANONICAL_REDIRECT') || strtoupper($_SERVER['REQUEST_METHOD']) != 'GET' || Tools::getValue('live_edit')) {
            return;
        }

        $match_url = rawurldecode(Tools::getCurrentUrlProtocolPrefix().$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        if (!preg_match('/^'.Tools::pRegexp(rawurldecode($canonical_url), '/').'([&?].*)?$/', $match_url)) {
            $params = array();
            $url_details = parse_url($canonical_url);

            if (!empty($url_details['query'])) {
                parse_str($url_details['query'], $query);
                foreach ($query as $key => $value) {
                    $params[Tools::safeOutput($key)] = Tools::safeOutput($value);
                }
            }
            $excluded_key = array(
                'module',
                'rewrite',
                'sb_category',
                'isolang',
                'id_lang',
                'controller',
                'fc',
                'id_product',
                'id_category',
                'id_manufacturer',
                'id_supplier',
                'id_cms'
            );
            foreach ($_GET as $key => $value) {
                if (!in_array($key, $excluded_key) && Validate::isUrl($key) && Validate::isUrl($value)) {
                    $params[Tools::safeOutput($key)] = Tools::safeOutput($value);
                }
            }

            $str_params = http_build_query($params, '', '&');
            if (!empty($str_params)) {
                $final_url = preg_replace('/^([^?]*)?.*$/', '$1', $canonical_url).'?'.$str_params;
            } else {
                $final_url = preg_replace('/^([^?]*)?.*$/', '$1', $canonical_url);
            }

            // Don't send any cookie
            Context::getContext()->cookie->disallowWriting();

            if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_ && $_SERVER['REQUEST_URI'] != __PS_BASE_URI__) {
                die('[Debug] This page has moved<br />Please use the following URL instead: <a href="'.$final_url.'">'.$final_url.'</a>');
            }

            $redirect_type = Configuration::get('PS_CANONICAL_REDIRECT') == 2 ? '301' : '302';
            header('HTTP/1.0 '.$redirect_type.' Moved');
            header('Cache-Control: no-cache');
            Tools::redirectLink($final_url);
        }
    }
}
