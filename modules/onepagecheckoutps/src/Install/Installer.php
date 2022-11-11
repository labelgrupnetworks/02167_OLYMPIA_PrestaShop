<?php
/**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * @author    PresTeamShop SAS (Registered Trademark) <info@presteamshop.com>
 * @copyright 2011-2022 PresTeamShop SAS, All rights reserved.
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @category  PrestaShop
 * @category  Module
 */

namespace OnePageCheckoutPS\Install;

use Db;
use Language;
use Module;
use OnePageCheckoutPS\Application\PrestaShop\Configuration\Configuration;
use OnePageCheckoutPS\Application\PrestaShop\Provider\ContextProvider;
use Shop;
use Tab;
use Tools;

class Installer
{
    private $configuration;
    private $module;
    private $contextProvider;

    public const SERVICE_NAME = 'onepagecheckoutps.install.installer';

    public function __construct(Module $module, ContextProvider $contextProvider, Configuration $configuration)
    {
        $this->module = $module;
        $this->configuration = $configuration;
        $this->contextProvider = $contextProvider;
    }

    public function install()
    {
        if (!$this->registerHooks()) {
            return false;
        }

        if (!$this->installTables()) {
            return false;
        }

        if (!$this->insertQueriesByShop()) {
            return false;
        }

        if (!$this->insertQueriesByLang()) {
            return false;
        }

        if (!$this->installConfiguration()) {
            return false;
        }

        if (!$this->installTab()) {
            return false;
        }

        $this->clearSmartyCache();

        return true;
    }

    public function uninstall()
    {
        if (!$this->uninstallTables()) {
            return false;
        }

        if (!$this->uninstallConfiguration()) {
            return false;
        }

        if (!$this->uninstallTab()) {
            return false;
        }

        $this->clearSmartyCache();

        return true;
    }

    protected function clearSmartyCache()
    {
        $smarty = $this->contextProvider->getSmarty();

        Tools::clearCache($smarty);
        Tools::clearCompile($smarty);

        if (file_exists(_PS_ROOT_DIR_ . '/var/cache/dev/class_index.php')) {
            unlink(_PS_ROOT_DIR_ . '/var/cache/dev/class_index.php');
        }
        if (file_exists(_PS_ROOT_DIR_ . '/var/cache/prod/class_index.php')) {
            unlink(_PS_ROOT_DIR_ . '/var/cache/prod/class_index.php');
        }
    }

    private function installTab()
    {
        if (Tab::getIdFromClassName($this->module->tabClassName)) {
            return true;
        }

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $this->module->tabClassName;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->module->tabName;
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentModulesSf');
        $tab->module = $this->module->name;

        return (bool) $tab->add();
    }

    private function uninstallTab()
    {
        if ($id_tab = Tab::getIdFromClassName($this->module->tabClassName)) {
            return true;
        }

        $tab = new Tab($id_tab);

        return $tab->delete();
    }

    private function registerHooks()
    {
        return (bool) $this->module->registerHook($this->module->getModuleHooks());
    }

    private function installTables()
    {
        return (bool) $this->executeQueries($this->module->getModuleQueries()['install']);
    }

    private function uninstallTables()
    {
        return (bool) $this->executeQueries($this->module->getModuleQueries()['uninstall']);
    }

    private function installConfiguration()
    {
        foreach ($this->module->getModuleConfigList() as $key => $config) {
            $this->configuration->set(
                $key,
                $config['options']['default_value'],
                $config['options']
            );
        }

        return true;
    }

    private function uninstallConfiguration()
    {
        foreach (array_keys($this->module->getModuleConfigList()) as $key) {
            $this->configuration->remove($key);
        }

        return true;
    }

    public function insertQueriesByShop($shopList = null)
    {
        if (empty($shopList)) {
            $shopList = Shop::getShops();
            $shopList = array_keys($shopList);
        } elseif (is_array($shopList)) {
            $shopList = array_values($shopList);
        } else {
            $shopList = array($shopList);
        }

        $shopQueries = $this->module->getModuleQueries()['shop'];
        if ($shopQueries) {
            foreach ($shopQueries as $query) {
                foreach ($shopList as $shopId) {
                    $queryShop = str_replace('ID_SHOP', $shopId, $query);

                    $this->executeQueries($queryShop);
                }
            }
        }

        return true;
    }

    public function insertQueriesByLang($langList = null)
    {
        if (empty($langList)) {
            $langList = Language::getLanguages(false);
        } elseif (is_array($langList)) {
            foreach ($langList as $key => $langId) {
                $langList[$key] = Language::getLanguage($langId);
            }
        } else {
            $langList = array(Language::getLanguage($langList));
        }

        $shopList = Shop::getShops();
        $shopList = array_keys($shopList);
        foreach ($langList as $lang) {
            $isoCode = 'en';
            if (file_exists(dirname(__FILE__) . '/../../sql/languages/' . Tools::strtolower($lang['iso_code']) . '.sql')) {
                $isoCode = Tools::strtolower($lang['iso_code']);
            }

            $queries = Tools::file_get_contents(dirname(__FILE__) . '/../../sql/languages/' . $isoCode . '.sql');
            if ($queries) {
                $queries = str_replace('PREFIX_', _DB_PREFIX_, $queries);
                $queries = str_replace('ID_LANG', $lang['id_lang'], $queries);

                if (strpos($queries, 'ID_SHOP')) {
                    foreach ($shopList as $shopId) {
                        $queryShop = str_replace('ID_SHOP', $shopId, $queries);
                        $queryShop = preg_split("/;\s*[\r\n]+/", $queryShop);

                        $this->executeQueries($queryShop);
                    }
                } else {
                    $this->executeQueries($queries);
                }
            }
        }

        return true;
    }

    private function executeQueries($queries)
    {
        if ($queries) {
            $db = Db::getInstance(_PS_USE_SQL_SLAVE_);

            if (!is_array($queries)) {
                $queries = array($queries);
            }

            foreach ($queries as $query) {
                $result = $db->execute(trim($query));
                if ($result === false) {
                    throw new OPCException('Unable to execute the following query: ' . $query);
                }
            }
        }

        return true;
    }
}
