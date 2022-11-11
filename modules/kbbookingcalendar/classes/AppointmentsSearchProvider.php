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

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrderFactory;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use Symfony\Component\Translation\TranslatorInterface;

class AppointmentsSearchProvider implements ProductSearchProviderInterface
{
    private $translator;
    private $sortOrderFactory;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
        $this->sortOrderFactory = new SortOrderFactory($this->translator);
    }

    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        if (!$products = self::getProduct(
            $context->getIdLang(),
            $query->getPage(),
            $query->getResultsPerPage(),
            false,
            $query->getSortOrder()->toLegacyOrderBy(),
            $query->getSortOrder()->toLegacyOrderWay()
        )) {
            $products = array();
        }

        $count = (int)self::getNbAppointmentProduct();

        $result = new ProductSearchResult();

        if (!empty($products)) {
            $result
                ->setProducts($products)
                ->setTotalProductsCount($count);

            $result->setAvailableSortOrders(
                array(
                    (new SortOrder('product', 'name', 'asc'))->setLabel(
                        $this->translator->trans('Name, A to Z', array(), 'Shop.Theme.Catalog')
                    ),
                    (new SortOrder('product', 'name', 'desc'))->setLabel(
                        $this->translator->trans('Name, Z to A', array(), 'Shop.Theme.Catalog')
                    ),
                    (new SortOrder('product', 'price', 'asc'))->setLabel(
                        $this->translator->trans('Price, low to high', array(), 'Shop.Theme.Catalog')
                    ),
                    (new SortOrder('product', 'price', 'desc'))->setLabel(
                        $this->translator->trans('Price, high to low', array(), 'Shop.Theme.Catalog')
                    ),
                )
            );
        }
        return $result;
    }
    
    /**
     * Get required informations on best sales products
     *
     * @param int $idLang     Language id
     * @param int $pageNumber Start from (optional)
     * @param int $nbProducts Number of products to return (optional)
     *
     * @return array|bool from Product::getProductProperties
     *                    `false` if failure
     */
    public static function getProduct($idLang, $pageNumber = 0, $nbProducts = 10, $count = false, $order_by = null, $order_way = null, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        if ($pageNumber < 1) {
            $pageNumber = 1;
        }
        if ($nbProducts < 1) {
            $nbProducts = 10;
        }
        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'date_add';
        }
        if (empty($order_way)) {
            $order_way = 'DESC';
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'p';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        } elseif ($order_by == 'position') {
            $order_by_prefix = 'c';
        }
        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }
        $interval = Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20;

        // no group by needed : there's only one attribute with default_on=1 for a given id_product + shop
        // same for image with cover=1
        $sql = 'SELECT p.*,  stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity,	
            pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
            pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`,
            m.`name` AS manufacturer_name, p.`id_manufacturer` as id_manufacturer,
            image_shop.`id_image` id_image, il.`legend`,
             t.`rate`, pl.`meta_keywords`, pl.`meta_title`, pl.`meta_description`,
            DATEDIFF(p.`date_add`, DATE_SUB("'.date('Y-m-d').' 00:00:00",
            INTERVAL '.(int) $interval.' DAY)) > 0 AS new'
            .' FROM '._DB_PREFIX_.'kb_booking_product b 
            INNER JOIN `'._DB_PREFIX_.'product` p ON (b.`id_product` = p.`id_product` AND b.product_type="appointment")
            '.Shop::addSqlAssociation('product', 'p', false);

        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
                ON p.`id_product` = pl.`id_product`
                AND pl.`id_lang` = '.(int) $idLang.Shop::addSqlRestrictionOnLang('pl').'
                LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
                ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int) $context->shop->id.')
                LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int) $idLang.')
                LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
                LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`)
                AND tr.`id_country` = '.(int) $context->country->id.'
                AND tr.`id_state` = 0
                LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
                '.Product::sqlStock('p', 0);

        $sql .= 'WHERE product_shop.`active` = 1
                AND product_shop.`visibility` != \'none\'';

        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql .= ' AND EXISTS(SELECT 1 FROM `'._DB_PREFIX_.'category_product` cp
                    JOIN `'._DB_PREFIX_.'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1').')
                    WHERE cp.`id_product` = p.`id_product`)';
        }

        $sql .= ' ORDER BY ' . (isset($order_by_prefix) ? pSQL($order_by_prefix) . '.' : '') . '`' .pSQL($order_by).'` '.pSQL($order_way).'
                LIMIT ' . (int) (($pageNumber - 1) * $nbProducts) . ', ' . (int) $nbProducts;

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if ($order_by == 'price') {
            Tools::orderbyPrice($result, $order_way);
            $result = array_slice($result, (int) (($pageNumber-1) * $nbProducts), (int) $nbProducts);
        }
        if (!$result) {
            return false;
        }

        return Product::getProductsProperties($idLang, $result);
    }
    
    /**
     * Get number of actives products sold
     *
     * @return int number of actives products listed in product_sales
     */
    public static function getNbAppointmentProduct()
    {
        $sql = 'SELECT COUNT(ps.`id_product`) AS nb
				FROM `'._DB_PREFIX_.'kb_booking_product` ps
				LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = ps.`id_product` AND ps.product_type="appointment")
				'.Shop::addSqlAssociation('product', 'p', false).'
				WHERE product_shop.`active` = 1';

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }
}
