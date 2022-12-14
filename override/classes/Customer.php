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
 * @copyright 2016-2017 Musaffar Patel
 * @license   LICENSE.txt
 */
class Customer extends CustomerCore
{
    public $id_customer_provis;

    /*
    * module: loyaltyrewardpoints
    * date: 2022-11-10 11:23:49
    * version: 2.2.0
    */
    public function __construct($id = null)
    {
        // New fields
        $new_fields = array(
            'id_customer_provis' => array('type' => self::TYPE_STRING)
        );
        parent::$definition['fields'] = array_merge(parent::$definition['fields'], $new_fields);

        parent::__construct($id);
        if (Module::isEnabled('LoyaltyRewardPoints')) {
            include_once(_PS_MODULE_DIR_ . '/loyaltyrewardpoints/lib/bootstrap.php');
            self::$definition['fields']['points'] = array('type' => self::TYPE_STRING);
            $points = LRPCustomerHelper::getTotalPointsAvailable($id, null);
            $this->points = "$points";
            $this->webserviceParameters['fields']['points'] = array();
        }
    }
}
