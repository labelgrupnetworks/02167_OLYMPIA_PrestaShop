<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2019 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 *
 */
require_once _PS_MODULE_DIR_ . 'kbbookingcalendar/classes/KbBookingProduct.php';
require_once _PS_MODULE_DIR_ . 'kbbookingcalendar/classes/HourlyRentalSearchProvider.php';

use PrestaShop\PrestaShop\Core\Filter\CollectionFilter;
use PrestaShop\PrestaShop\Core\Filter\HashMapWhitelistFilter;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\Pagination;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\Facet;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\FacetsRendererInterface;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

class KbBookingCalendarHourlyRentalsModuleFrontController extends ModuleFrontController {

    public $ssl = true;
    public $display_column_left = false;

    public function __construct() {
        parent::__construct();
        $this->display_column_left = true;
        $this->display_column_right = false;
    }

    /**
     * Sets default medias for this controller
     */
    public function setMedia() {
        parent::setMedia();

        if (!$this->useMobileTheme()) {
            $this->addCSS(array(
                _THEME_CSS_DIR_ . 'product_list.css' => 'all',
            ));
        }
        $this->addJS(_THEME_JS_DIR_ . 'category.js');
    }

    /**
     * Initializes controller.
     *
     * @see FrontController::init()
     *
     * @throws PrestaShopException
     */
    public function init() {
        parent::init();
    }

    public function getLayout() {
        parent::getLayout();
        return 'layouts/layout-left-column.tpl';
    }

    public function initContent() {
        parent::initContent();
        $kb_setting = Tools::jsonDecode(Configuration::get('KB_BOOKING_CALENDAR_GENERAL_SETTING'), true);
        if (!empty($kb_setting) && $kb_setting['enable']) {
            $hourly_rental_products = KbBookingProduct::getProductsByType('hourly_rental');
            if ($this->ajax) {
                ob_end_clean();
                header('Content-Type: application/json');
                $this->ajaxDie(json_encode($this->getAjaxProductSearchVariables()));
            } else {
                $variables = $this->getHourlyRentalProducts($hourly_rental_products);
                $this->context->smarty->assign(
                        array(
                            'listing' => $variables,
                        )
                );
                // Changes done by Kanishka Kannoujia on 13-04-2022 to solve RenderLogo not defined
                if (version_compare(_PS_VERSION_, '1.7.8.0', '>=')) {
                    $this->context->smarty->assign(
                            array(
                                'ps_ver' => 1,
                            )
                    );
                }
                // Changes end here
                $this->setTemplate("module:kbbookingcalendar/views/templates/front/daily_rental.tpl");
            }
        } else {
            Tools::redirect(Context::getContext()->link->getPageLink('index'));
        }
    }

    /**
     * Similar to "getProductSearchVariables" but used in AJAX queries.
     *
     * It returns an array with the HTML for the products and facets,
     * and the current URL to put it in the browser URL bar (we don't want to
     * break the back button!).
     *
     * @return array
     */
    protected function getAjaxProductSearchVariables() {
        $search = $this->getHourlyRentalProducts();

        $rendered_products_top = $this->render('catalog/_partials/products-top', array('listing' => $search));
        $rendered_products = $this->render('catalog/_partials/products', array('listing' => $search));
        $rendered_products_bottom = $this->render('catalog/_partials/products-bottom', array('listing' => $search));

        $data = array_merge(
                array(
            'rendered_products_top' => $rendered_products_top,
            'rendered_products' => $rendered_products,
            'rendered_products_bottom' => $rendered_products_bottom,
                ), $search
        );

        if (!empty($data['products']) && is_array($data['products'])) {
            $data['products'] = $this->prepareProductArrayForAjaxReturn($data['products']);
        }

        return $data;
    }

    /**
     * Cleans the products array with only whitelisted properties.
     *
     * @param array[] $products
     *
     * @return array[] Filtered product list
     */
    protected function prepareProductArrayForAjaxReturn(array $products) {
        $filter = $this->get('prestashop.core.filter.front_end_object.search_result_product_collection');

        return $filter->filter($products);
    }

    protected function getHourlyRentalProducts() {
        if (Configuration::get('PS_CATALOG_MODE')) {
            return false;
        }

        $searchProvider = new HourlyRentalSearchProvider(
                $this->context->getTranslator()
        );
//        Tools::dieObject($searchProvider, true);
        $context = new ProductSearchContext($this->context);

        $query = new ProductSearchQuery();

//        $nProducts = (int) 10;
        $resultsPerPage = (int) Tools::getValue('resultsPerPage');
        if ($resultsPerPage <= 0 || $resultsPerPage > 36) {
            $resultsPerPage = 10;
        }
        $query
                ->setResultsPerPage($resultsPerPage)
                ->setPage(max((int) Tools::getValue('page'), 1))
        ;
        // set the sort order if provided in the URL
        if (($encodedSortOrder = Tools::getValue('order'))) {
            $query->setSortOrder(SortOrder::newFromString(
                            $encodedSortOrder
            ));
        }

        $result = $searchProvider->runQuery(
                $context, $query
        );

        // sort order is useful for template,
        // add it if undefined - it should be the same one
        // as for the query anyway
        if (!$result->getCurrentSortOrder()) {
            $result->setCurrentSortOrder($query->getSortOrder());
        }

        $assembler = new ProductAssembler($this->context);

        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
                new ImageRetriever(
                $this->context->link
                ), $this->context->link, new PriceFormatter(), new ProductColorsRetriever(), $this->context->getTranslator()
        );

        $products_for_template = array();

        foreach ($result->getProducts() as $rawProduct) {
            $products_for_template[] = $presenter->present(
                    $presentationSettings, $assembler->assembleProduct($rawProduct), $this->context->language
            );
        }
        // prepare the sort orders
        // note that, again, the product controller is sort-orders
        // agnostic
        // a module can easily add specific sort orders that it needs
        // to support (e.g. sort by "energy efficiency")
        $sort_orders = $this->getTemplateVarSortOrders(
                $result->getAvailableSortOrders(), $query->getSortOrder()->toString()
        );

        $sort_selected = false;
        if (!empty($sort_orders)) {
            foreach ($sort_orders as $order) {
                if (isset($order['current']) && true === $order['current']) {
                    $sort_selected = $order['label'];
                    break;
                }
            }
        }

        $pagination = $this->getTemplateVarPagination(
                $query, $result
        );
        $searchVariables = array(
            'label' => $this->module->l('Hourly Rental', 'hourlyrentals'),
            'products' => $products_for_template,
            'sort_orders' => $sort_orders,
            'sort_selected' => $sort_selected,
            'pagination' => $pagination,
            'js_enabled' => $this->ajax,
            'current_url' => $this->updateQueryString(array(
                'q' => $result->getEncodedFacets(),
            )),
        );
        return $searchVariables;
    }

    /**
     * Renders an array of facets.
     *
     * @param array $facets
     *
     * @return string the HTML of the facets
     */
    protected function renderFacets(ProductSearchResult $result) {
        $facetCollection = $result->getFacetCollection();
        // not all search providers generate menus
        if (empty($facetCollection)) {
            return '';
        }

        $facetsVar = array_map(
                array($this, 'prepareFacetForTemplate'), $facetCollection->getFacets()
        );

        $activeFilters = array();
        foreach ($facetsVar as $facet) {
            foreach ($facet['filters'] as $filter) {
                if ($filter['active']) {
                    $activeFilters[] = $filter;
                }
            }
        }

        return $this->render('catalog/_partials/facets', array(
                    'facets' => $facetsVar,
                    'js_enabled' => $this->ajax,
                    'activeFilters' => $activeFilters,
                    'sort_order' => $result->getCurrentSortOrder()->toString(),
                    'clear_all_link' => $this->updateQueryString(array('q' => null, 'page' => null))
        ));
    }

    /**
     * Prepares the sort-order links.
     *
     * Sort order links contain the current encoded facets if any,
     * but not the page number because normally when you change the sort order
     * you want to go back to page one.
     *
     * @param array  $sortOrders                   the available sort orders
     * @param string $currentSortOrderURLParameter used to know which of the sort orders (if any) is active
     *
     * @return array
     */
    protected function getTemplateVarSortOrders(array $sortOrders, $currentSortOrderURLParameter) {
        return array_map(function ($sortOrder) use ($currentSortOrderURLParameter) {
            $Kbhoour = new KbBookingCalendarHourlyRentalsModuleFrontController();
            $order = $sortOrder->toArray();
            $order['current'] = $order['urlParameter'] === $currentSortOrderURLParameter;
            $order['url'] = $Kbhoour->updateQueryString(array(
                'order' => $order['urlParameter'],
                'page' => null,
            ));

            return $order;
        }, $sortOrders);
    }

    /**
     * Pagination is HARD. We let the core do the heavy lifting from
     * a simple representation of the pagination.
     *
     * Generated URLs will include the page number, obviously,
     * but also the sort order and the "q" (facets) parameters.
     *
     * @param ProductSearchQuery  $query
     * @param ProductSearchResult $result
     *
     * @return an array that makes rendering the pagination very easy
     */
    protected function getTemplateVarPagination(
    ProductSearchQuery $query, ProductSearchResult $result
    ) {
        $pagination = new Pagination();
        $pagination
                ->setPage($query->getPage())
                ->setPagesCount(
                        (int) ceil($result->getTotalProductsCount() / $query->getResultsPerPage())
                )
        ;

        $totalItems = $result->getTotalProductsCount();
        $itemsShownFrom = ($query->getResultsPerPage() * ($query->getPage() - 1)) + 1;
        $itemsShownTo = $query->getResultsPerPage() * $query->getPage();

        return array(
            'total_items' => $totalItems,
            'items_shown_from' => $itemsShownFrom,
            'items_shown_to' => ($itemsShownTo <= $totalItems) ? $itemsShownTo : $totalItems,
            'pages' => array_map(function ($link) {
                        $Kbhoour = new KbBookingCalendarHourlyRentalsModuleFrontController();
                        $link['url'] = $Kbhoour->updateQueryString(array(
                            'page' => $link['page'],
                        ));

                        return $link;
                    }, $pagination->buildLinks()),
                    // Compare to 3 because there are the next and previous links
                    'should_be_displayed' => (count($pagination->buildLinks()) > 3)
                );
            }

            public function postProcess() {

                parent::postProcess();
            }

            /**
             * Generate a URL corresponding to the current page but
             * with the query string altered.
             *
             * If $extraParams is set to NULL, then all query params are stripped.
             *
             * Otherwise, params from $extraParams that have a null value are stripped,
             * and other params are added. Params not in $extraParams are unchanged.
             */
            protected function updateQueryString(array $extraParams = null) {
                $uriWithoutParams = explode('?', $_SERVER['REQUEST_URI'])[0];
                $url = Tools::getCurrentUrlProtocolPrefix() . $_SERVER['HTTP_HOST'] . $uriWithoutParams;
                $params = array();
                parse_str($_SERVER['QUERY_STRING'], $params);

                if (null !== $extraParams) {
                    foreach ($extraParams as $key => $value) {
                        if (null === $value) {
                            unset($params[$key]);
                        } else {
                            $params[$key] = $value;
                        }
                    }
                }

                ksort($params);

                if (null !== $extraParams) {
                    foreach ($params as $key => $param) {
                        if (null === $param || '' === $param) {
                            unset($params[$key]);
                        }
                    }
                } else {
                    $params = array();
                }

                $queryString = str_replace('%2F', '/', http_build_query($params, '', '&'));

                return $url . ($queryString ? "?$queryString" : '');
            }

            private function getModuleDirUrl() {
                $module_dir = '';
                if ($this->checkSecureUrl()) {
                    $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
                } else {
                    $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
                }
                return $module_dir;
            }

            private function checkSecureUrl() {
                $custom_ssl_var = 0;
                if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                    $custom_ssl_var = 1;
                }
                if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
                    return true;
                } else {
                    return false;
                }
            }

        }
        