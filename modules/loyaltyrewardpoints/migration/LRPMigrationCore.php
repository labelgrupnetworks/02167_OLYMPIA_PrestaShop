<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Musaffar Patel
 * @copyright 2016-2021 Musaffar Patel
 * @license   LICENSE.txt
 */

abstract class LRPMigrationCore
{
    protected $source = 'migrated|unknown';

    abstract protected function migrate();

    /**
     * Check if a migration record already exists
     * @param $id_order
     * @param $id_customer
     * @param $source
     * @param $type
     */
    protected function migrationEntryExists($id_order, $id_customer, $source, $type)
    {
        $sql = new DbQuery();
        $sql->select('count(*) AS totalCount');
        $sql->from('lrp_history');
        $sql->where('id_order = ' . (int)$id_order);
        $sql->where('id_customer = ' . (int)$id_customer);
        $sql->where('type = ' . (int)$type);
        $sql->where('source LIKE "' . pSQL($source) . '"');
        $count = DB::getInstance()->getValue($sql);

        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get list of customers which have been migrated from a specific source
     * @param $source
     * @return array|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    protected function getCustomersMigrated($source)
    {
        $sql = new DbQuery();
        $sql->select('DISTINCT(id_customer)');
        $sql->from('lrp_history');
        $sql->where('source LIKE "' . pSQL($source) . '"');
        $result = DB::getInstance()->executeS($sql);

        if (!empty($result)) {
            return $result;
        } else {
            return array();
        }
    }
}
