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

use Doctrine\ORM\EntityRepository;

class FieldRepository extends EntityRepository
{
    public function findByLangAndShop($langId, $shopId)
    {
        $qb = $this->createQueryBuilder('f')
            ->addSelect('f.name, f.type, f.size, f.typeControl, f.isCustom, f.capitalize')
            ->addSelect('fs.row, fs.col, fs.required, fs.active')
            ->addSelect('fl.description, fl.label')
            ->join('f.fieldShops', 'fs')
            ->join('f.fieldLangs', 'fl')
            ->where('fl.idLang = :idLang')
            ->andWhere('fl.idShop = :idShop')
            ->andWhere('fs.idShop = :idShop')
            ->andWhere('f.id = :idField')
            ->setParameters(array(
                'idShop' => $shopId,
                'idLang' => $langId,
            ));

        return $qb->getQuery()->getResult();
    }

    public function getDefaultValueByFieldName($shopId, $object, $fieldName)
    {
        $qb = $this->createQueryBuilder('f')
            ->addSelect('fs.defaultValue')
            ->join('f.fieldShops', 'fs')
            ->andWhere('fs.idShop = :idShop')
            ->andWhere('f.object = :object')
            ->andWhere('f.name = :name')
            ->setParameters(array(
                'idShop' => $shopId,
                'object' => $object,
                'name' => $fieldName,
            ));

        return $qb->getQuery()->getSingleResult();
    }

    public function getDefaultValueCountry($shopId, $object)
    {
        $qb = $this->createQueryBuilder('f')
            ->addSelect('fs.defaultValue')
            ->join('f.fieldShops', 'fs')
            ->andWhere('fs.idShop = :idShop')
            ->andWhere('f.object = :object')
            ->andWhere('f.name = :name')
            ->setParameters(array(
                'idShop' => $shopId,
                'object' => $object,
                'name' => 'id_country',
            ));

        return $qb->getQuery()->getSingleResult();
    }

    public function getDefaultValueNewsletter($shopId)
    {
        $qb = $this->createQueryBuilder('f')
            ->addSelect('fs.defaultValue')
            ->join('f.fieldShops', 'fs')
            ->andWhere('fs.idShop = :idShop')
            ->andWhere('f.object = :object')
            ->andWhere('f.name = :name')
            ->setParameters(array(
                'idShop' => $shopId,
                'object' => 'customer',
                'name' => 'newsletter',
            ));

        return $qb->getQuery()->getSingleResult();
    }

    public function getCustomFieldByCustomer($paramListSQL)
    {
        $qb = $this->createQueryBuilder('f')
            ->addSelect('f.name, f.type, fc.value, fo.value as valueOption, fc.idOption')
            ->join('f.fieldCustomer', 'fc')
            ->leftJoin('f.fieldOptions', 'fo', 'WITH', 'fo.id = fc.idOption');

        if (array_key_exists('object', $paramListSQL)) {
            $qb->andWhere('fc.object = :object');
        }
        if (array_key_exists('idCustomer', $paramListSQL)) {
            $qb->andWhere('fc.idCustomer = :idCustomer');
        }

        if (array_key_exists('idAddress', $paramListSQL)) {
            $qb->andWhere('fc.idAddress = :idAddress');
        }

        $qb->setParameters($paramListSQL);

        return $qb->getQuery()->getResult();
    }
}
