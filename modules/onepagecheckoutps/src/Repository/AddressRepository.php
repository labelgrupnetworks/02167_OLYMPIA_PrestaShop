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

namespace OnePageCheckoutPS\Repository;

use OnePageCheckoutPS;
use PDO;

class AddressRepository
{
    private $connection;
    private $dbPrefix;

    public function __construct(OnePageCheckoutPS $module)
    {
        $this->connection = $module->getDbalConnection();
        $this->dbPrefix = _DB_PREFIX_;
    }

    public function findDniAddressUsed($customerId, $addressDni)
    {
        $qb = $this->connection->createQueryBuilder();

        $qb
            ->select('id_address')
            ->from($this->dbPrefix . 'address')
            ->where('`id_customer` != :customer_id')
            ->andWhere('`dni` = :address_dni');

        $qb->setParameter('customer_id', $customerId);
        $qb->setParameter('address_dni', $addressDni);
        //$qb->getSQL(); trae el SQL para pintarlo

        return $qb->execute()->fetch(PDO::FETCH_COLUMN);
    }
}
