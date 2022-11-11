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
class OpcFieldLang
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="OnePageCheckoutPS\Entity\OpcField", inversedBy="fieldLangs")
     * @ORM\JoinColumn(name="id_field", referencedColumnName="id_field", nullable=false)
     */
    private $field;

    /**
     * @ORM\Id
     * @ORM\Column(name="id_lang", type="integer", length=10)
     */
    private $idLang;

    /**
     * @ORM\Id
     * @ORM\Column(name="id_shop", type="integer", length=10)
     */
    private $idShop;

    /**
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    public function getField()
    {
        return $this->field;
    }

    public function setField(OpcField $field)
    {
        $this->field = $field;
    }

    public function getIdLang()
    {
        return (int) $this->idLang;
    }

    public function setIdLang($idLang)
    {
        $this->idLang = (int) $idLang;
    }

    public function getIdShop()
    {
        return (int) $this->idShop;
    }

    public function setIdShop($idShop)
    {
        $this->idShop = (int) $idShop;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function toArray()
    {
        return array(
            'idLang' => $this->getIdLang(),
            'idShop' => $this->getIdShop(),
            'description' => $this->getDescription(),
            'label' => $this->getLabel(),
        );
    }

    public function toArrayLegacy()
    {
        return array(
            'id_lang' => $this->getIdLang(),
            'id_shop' => $this->getIdShop(),
            'description' => $this->getDescription(),
            'label' => $this->getLabel(),
        );
    }
}
