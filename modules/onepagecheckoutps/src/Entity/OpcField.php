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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="OnePageCheckoutPS\Repository\FieldRepository")
 */
class OpcField
{
    public const GROUP_CUSTOMER = 'customer';
    public const GROUP_DELIVERY = 'delivery';
    public const GROUP_INVOICE = 'invoice';

    /**
     * @ORM\Id
     * @ORM\Column(name="id_field", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="object", type="string", length=20)
     */
    private $object;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=20)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="integer", length=10)
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="type_control", type="string", length=20)
     */
    private $typeControl;

    /**
     * @var string
     *
     * @ORM\Column(name="is_custom", type="integer", length=1)
     */
    private $isCustom;

    /**
     * @var string
     *
     * @ORM\Column(name="capitalize", type="integer", length=1)
     */
    private $capitalize;

    /**
     * @ORM\OneToMany(targetEntity="OnePageCheckoutPS\Entity\OpcFieldLang", cascade={"persist", "remove"}, mappedBy="field")
     */
    private $fieldLangs;

    /**
     * @ORM\OneToMany(targetEntity="OnePageCheckoutPS\Entity\OpcFieldShop", cascade={"persist", "remove"}, mappedBy="field")
     */
    private $fieldShops;

    /**
     * @ORM\OneToMany(targetEntity="OnePageCheckoutPS\Entity\OpcFieldOption", cascade={"persist", "remove"}, mappedBy="field")
     */
    private $fieldOptions;

    /**
     * @ORM\OneToMany(targetEntity="OnePageCheckoutPS\Entity\OpcFieldCustomer", cascade={"persist", "remove"}, mappedBy="field")
     */
    private $fieldCustomer;

    public function __construct()
    {
        $this->fieldLangs = new ArrayCollection();
        $this->fieldShops = new ArrayCollection();
        $this->fieldOptions = new ArrayCollection();
        $this->fieldCustomer = new ArrayCollection();
    }

    public function getId()
    {
        return (int) $this->id;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function setObject($object)
    {
        $this->object = $object;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getSize()
    {
        return (int) $this->size;
    }

    public function setSize($size)
    {
        $this->size = (int) $size;
    }

    public function getTypeControl()
    {
        return $this->typeControl;
    }

    public function setTypeControl($typeControl)
    {
        $this->typeControl = $typeControl;
    }

    public function getIsCustom()
    {
        return (bool) $this->isCustom;
    }

    public function setIsCustom($isCustom)
    {
        return $this->isCustom = (bool) $isCustom;
    }

    public function getCapitalize()
    {
        return (bool) $this->capitalize;
    }

    public function setCapitalize($capitalize)
    {
        $this->capitalize = (bool) $capitalize;
    }

    public function getFieldLangs()
    {
        return $this->fieldLangs;
    }

    public function getLangById($langId)
    {
        foreach ($this->fieldLangs as $lang) {
            if ($lang->getIdLang() === $langId) {
                return $lang;
            }
        }
    }

    public function setFieldLangs(OpcFieldLang $fieldLang)
    {
        $fieldLang->setField($this);

        $this->fieldLangs->add($fieldLang);
    }

    public function getFieldShops()
    {
        return $this->fieldShops;
    }

    public function getShopById($shopId)
    {
        foreach ($this->fieldShops as $shop) {
            if ($shop->getIdShop() === $shopId) {
                return $shop;
            }
        }
    }

    public function setFieldShops(OpcFieldShop $fieldShop)
    {
        $fieldShop->setField($this);

        $this->fieldShops->add($fieldShop);
    }

    public function getFieldOptions()
    {
        return $this->fieldOptions;
    }

    public function setFieldOptions(OpcFieldOption $fieldOpion)
    {
        $fieldOpion->setField($this);

        $this->fieldOptions->add($fieldOpion);
    }

    public function getFieldCustomer()
    {
        return $this->fieldCustomer;
    }

    public function getFieldCustomerBy(string $object, int $customerId, int $addressId)
    {
        foreach ($this->fieldCustomer as $field) {
            if ($field->getObject() === $object &&
                $field->getIdCustomer() === (int) $customerId &&
                $field->getIdAddress() === (int) $addressId
            ) {
                return $field;
            }
        }
    }

    public function setFieldCustomer(OpcFieldCustomer $fieldCustomer)
    {
        $fieldCustomer->setField($this);

        $this->fieldCustomer->add($fieldCustomer);
    }

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'object' => $this->getObject(),
            'name' => $this->getName(),
            'type' => $this->getType(),
            'size' => $this->getSize(),
            'typeControl' => $this->getTypeControl(),
            'isCustom' => $this->getIsCustom(),
            'capitalize' => $this->getCapitalize(),
        );
    }

    public function toArrayLegacy()
    {
        return array(
            'id' => $this->getId(),
            'object' => $this->getObject(),
            'name' => $this->getName(),
            'type' => $this->getType(),
            'size' => $this->getSize(),
            'type_control' => $this->getTypeControl(),
            'is_custom' => $this->getIsCustom(),
            'capitalize' => $this->getCapitalize(),
        );
    }
}
