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

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class OpcFieldCustomer
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="OnePageCheckoutPS\Entity\OpcField", inversedBy="fieldCustomer")
     * @ORM\JoinColumn(name="id_field", referencedColumnName="id_field", nullable=false)
     */
    private $field;

    /**
     * @ORM\Column(name="id_customer", type="integer")
     */
    private $idCustomer;

    /**
     * @ORM\Column(name="id_address", type="integer")
     */
    private $idAddress;

    /**
     * @ORM\Column(name="id_option", type="integer")
     */
    private $idOption;

    /**
     * @var string
     *
     * @ORM\Column(name="object", type="string", length=9)
     */
    private $object;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="date_upd", type="datetime")
     */
    private $dateUpd;

    public function getId()
    {
        return (int) $this->id;
    }

    public function getField()
    {
        return $this->field;
    }

    public function setField(OpcField $field)
    {
        $this->field = $field;
    }

    public function getIdCustomer()
    {
        return (int) $this->idCustomer;
    }

    public function setIdCustomer($idCustomer)
    {
        $this->idCustomer = (int) $idCustomer;
    }

    public function getIdAddress()
    {
        return (int) $this->idAddress;
    }

    public function setIdAddress($idAddress)
    {
        $this->idAddress = (int) $idAddress;
    }

    public function getIdOption()
    {
        return (int) $this->idOption;
    }

    public function setIdOption($idOption)
    {
        $this->idOption = (int) $idOption;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function setObject($object)
    {
        $this->object = $object;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getDateUpd()
    {
        return $this->dateUpd;
    }

    public function setDateUpd()
    {
        $this->dateUpd = new DateTime('NOW');
    }

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'idAddress' => $this->getIdAddress(),
            'idOption' => $this->getIdOption(),
            'object' => $this->getObject(),
            'value' => $this->getValue(),
            'dateUpd' => $this->getDateUpd(),
        );
    }

    public function toArrayLegacy()
    {
        return array(
            'id' => $this->getId(),
            'id_address' => $this->getIdAddress(),
            'id_option' => $this->getIdOption(),
            'object' => $this->getObject(),
            'value' => $this->getValue(),
            'date_upd' => $this->getDateUpd(),
        );
    }
}
