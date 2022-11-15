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

class LRPMigrationPSLoyalty extends LRPMigrationCore
{
    protected $source = 'migration|psloyaltyrewards';

    /**
     * @var array
     */
    public $reward_states = array();

    /**
     * @var array
     */
    public $redeem_states = array();

    private function getTransactions()
    {
        $merged_states = array_merge($this->reward_states, $this->redeem_states);
        $merged_states = implode(',', $merged_states);

        $sql = new DbQuery();
        $sql->select('l.id_loyalty, l.id_customer, l.id_order, l.points, l.date_add, l.id_loyalty_state, o.id_currency, c.id_default_group');
        $sql->from('loyalty', 'l');
        $sql->innerJoin('loyalty_history', 'lh', 'l.id_loyalty = lh.id_loyalty');
        $sql->innerJoin('orders', 'o', 'l.id_order = o.id_order');
        $sql->innerJoin('customer', 'c', 'l.id_customer = c.id_customer');
        $sql->where('l.id_loyalty_state IN ('.$merged_states.')');
        $sql->where('lh.points > 0');
        $sql->groupBy('l.id_order');
        $sql->orderBy('l.date_add ASC');
        $results = DB::getInstance()->executeS($sql);
        return $results;
    }

    /**
     * Migrate the points
     * @return int
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function migrate()
    {
        $count = 1;
        $added = 0;
        $transactions = $this->getTransactions();

        foreach ($transactions as $transaction) {
            $currency = new Currency($transaction['id_currency']);
            $lrp_config = new LRPConfigModel($currency->iso_code, $transaction['id_default_group']);

            if (empty($lrp_config->getPointValue())) {
                continue;
            }
            $added++;

            if (in_array($transaction['id_loyalty_state'], $this->reward_states)) {
                if (!$this->migrationEntryExists($transaction['id_order'], $transaction['id_customer'], $this->source, LRPHistoryModel::TYPE_REWARDED)) {
                    LRPHistoryHelper::rewardPoints($transaction['id_order'], $transaction['id_customer'], $transaction['points'], $this->source, $currency);
                    $added++;
                }
            }

            if (in_array($transaction['id_loyalty_state'], $this->redeem_states)) {
                if (!$this->migrationEntryExists($transaction['id_order'], $transaction['id_customer'], $this->source, LRPHistoryModel::TYPE_REDEEMED)) {
                    LRPHistoryHelper::redeemPoints($transaction['id_order'], $transaction['id_customer'], $transaction['points'], $this->source, $currency);
                    $added++;
                }
            }
            $count++;
        }

        $customers_migrated = $this->getCustomersMigrated($this->source);

        foreach ($customers_migrated as $customer) {
            LRPCustomerHelper::calculateAndUpdatePointsTotal($customer['id_customer']);
        }
        return $added;
    }

    /**
     * Get States
     * @param $id_lang
     * @return array|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public static function getStates($id_lang)
    {
        $sql = new DbQuery();
        $sql->select('id_loyalty_state, name');
        $sql->from('loyalty_state_lang');
        $sql->where('id_lang = '.(int)$id_lang);
        $result = DB::getInstance()->executeS($sql);

        if (!empty($result)) {
            return $result;
        } else {
            return array();
        }
    }
}
