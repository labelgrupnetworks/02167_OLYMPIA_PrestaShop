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
class OpcFieldShop
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="OnePageCheckoutPS\Entity\OpcField", inversedBy="fieldShops")
     * @ORM\JoinColumn(name="id_field", referencedColumnName="id_field", nullable=false)
     */
    private $field;

    /**
     * @ORM\Id
     * @ORM\Column(name="id_shop", type="integer", length=10)
     */
    private $idShop;

    /**
     * @ORM\Column(name="default_value", type="string", length=255)
     */
    private $defaultValue;

    /**
     * @ORM\Column(name="`group`", type="string", length=20)
     */
    private $group;

    /**
     * @ORM\Column(name="row", type="integer", length=10)
     */
    private $row;

    /**
     * @ORM\Column(name="col", type="integer", length=10)
     */
    private $col;

    /**
     * @ORM\Column(name="required", type="integer", length=1)
     */
    private $required;

    /**
     * @ORM\Column(name="active", type="integer", length=1)
     */
    private $active;

    public function getField()
    {
        return $this->field;
    }

    public function setField(OpcField $field)
    {
        $this->field = $field;
    }

    public function getIdShop()
    {
        return (int) $this->idShop;
    }

    public function setIdShop($idShop)
    {
        $this->idShop = (int) $idShop;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function setGroup($group)
    {
        $this->group = $group;
    }

    public function getRow()
    {
        return (int) $this->row;
    }

    public function setRow($row)
    {
        $this->row = (int) $row;
    }

    public function getCol()
    {
        return (int) $this->col;
    }

    public function setCol($col)
    {
        $this->col = (int) $col;
    }

    public function getRequired()
    {
        return (bool) $this->required;
    }

    public function setRequired($required)
    {
        $this->required = (bool) $required;
    }

    public function getActive()
    {
        return (bool) $this->active;
    }

    public function setActive($active)
    {
        $this->active = (bool) $active;
    }

    public function toArray()
    {
        return array(
            'idShop' => $this->getIdShop(),
            'defaultValue' => $this->getDefaultValue(),
            'group' => $this->getGroup(),
            'row' => $this->getRow(),
            'col' => $this->getCol(),
            'required' => $this->getRequired(),
            'active' => $this->getActive(),
        );
    }

    public function toArrayLegacy()
    {
        return array(
            'id_shop' => $this->getIdShop(),
            'default_value' => $this->getDefaultValue(),
            'group' => $this->getGroup(),
            'row' => $this->getRow(),
            'col' => $this->getCol(),
            'required' => $this->getRequired(),
            'active' => $this->getActive(),
        );
    }
}
