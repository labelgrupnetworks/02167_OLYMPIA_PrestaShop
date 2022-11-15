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

namespace OnePageCheckoutPS\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class OpcCustomerAddress
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id_customer", type="integer", length=10)
     */
    private $customerId;

    /**
     * @ORM\Id
     * @ORM\Column(name="id_address", type="integer", length=10)
     */
    private $addressId;

    /**
     * @ORM\Id
     * @ORM\Column(name="object", type="string", length=10)
     */
    private $object;

    public function setCustomerId($customerId)
    {
        $this->customerId = (int) $customerId;
    }

    public function getCustomerId()
    {
        return (int) $this->customerId;
    }

    public function setAddressId($addressId)
    {
        $this->addressId = (int) $addressId;
    }

    public function getAddressId()
    {
        return (int) $this->addressId;
    }

    public function setObject($object)
    {
        $this->object = $object;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function toArray()
    {
        return array(
            'id_customer' => $this->getCustomerId(),
            'id_address' => $this->getAddressId(),
            'object' => $this->getObject(),
        );
    }
}
