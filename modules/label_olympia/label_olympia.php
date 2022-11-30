<?php
/**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Label_olympia extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'label_olympia';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Labelgrup Networks S.L.';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Olympia tools');
        $this->description = $this->l('Olympia web tool module');

        $this->confirmUninstall = $this->l('Are you sure you want to unistall Olympia tools module?');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayHome');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookDisplayHome()
    {
        $sqlFilter = '
            AND c.level_depth = 2 
        ';
        $limit = 'LIMIT 9';
        $categories = $this->getCategories(Context::getContext()->language->id, true, $sqlFilter, $limit);
        $this->smarty->assign([
            'categories' => $categories
        ]);
  
        return $this->fetch('module:label_olympia/views/templates/hook/categories.tpl');
    }

    private function getCategories($idLang = false, $active = true, $sqlFilter = '', $limit = '')
    {
        $context = Context::getContext();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT c.id_category, cl.name, cl.link_rewrite
			FROM `' . _DB_PREFIX_ . 'category` c
			' . Shop::addSqlAssociation('category', 'c') . '
			LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . '
			WHERE 1 ' . $sqlFilter . ' ' . ($idLang ? 'AND `id_lang` = ' . (int) $idLang : '') . '
			' . ($active ? 'AND `active` = 1' : '') . '
			ORDER BY c.`level_depth` ASC, category_shop.`position` ASC
			' . ($limit != '' ? $limit : '')
        );

        foreach ($result as &$row) {
            if(file_exists(_PS_CAT_IMG_DIR_ . (int)$row['id_category'].'_thumb.jpg')){
                $row['image_url'] = '/img/c/' . (int)$row['id_category'].'_thumb.jpg';
            }
            $row['url'] = $context->link->getCategoryLink((int) $row['id_category']);
        }
        return $result;
    }
    
    public function sendReminderEmail($remainingDays){
        // Comprobamos que el número de días no supere el año
        if($remainingDays > 365){
            return false;
        }
        // Comprobamos si está activo el módulo de reservas
        if (!Module::isEnabled('kbbookingcalendar')){
            return false;
        }
        if(!$this->context){
            $this->context = Context::getContext();
        }
        // Obtenemos los emails de las reservas desde current_date hasta los días parametrizados
        $sql = '
            SELECT c.email, c.firstname, c.lastname, kpo.*, o.reference, o.date_add, kpc.check_in
            FROM ' . _DB_PREFIX_ . 'customer c 
            INNER JOIN ' . _DB_PREFIX_ . 'orders o ON c.id_customer = o.id_customer
            INNER JOIN ' . _DB_PREFIX_ . 'kb_booking_product_order kpo ON o.id_order = kpo.id_order
            INNER JOIN ' . _DB_PREFIX_ . 'kb_booking_product_cart kpc ON kpo.id_cart = kpc.id_cart
            WHERE 
                DATEDIFF(kpc.check_in, CURRENT_TIMESTAMP()) = ' . (int)$remainingDays . ' AND 
                kpo.is_cancelled = 0
            ORDER BY kpc.check_in
        ';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        // Enviamos un email al cliente con la información de la reserva
        foreach ($result as $element) {
            $templateVars = [
                '{email}' => $element['email'],
                '{firstname}' => $element['firstname'],
                '{lastname}' => $element['lastname'],
                '{order_name}' => $element['reference'],
                '{date}' => $element['date_add'],
                '{check_in}' => $element['check_in'],
            ];
            Mail::Send(
                (int) $this->context->language->id,
                'reservation_reminder',
                Context::getContext()->getTranslator()->trans(
                    'Your reservation is close',
                    [],
                    'Emails.Subject',
                ),
                $templateVars,
                $element['email'],
                $element['firstname'] . ' ' . $element['lastname'],
                null,
                null,
                null,
                null,
                _PS_MAIL_DIR_,
                false,
                (int) $this->context->shop->id
            );
        }
    }
}
