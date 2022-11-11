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
class OpcFieldOptionLang
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="OnePageCheckoutPS\Entity\OpcFieldOption", inversedBy="fieldOptionLangs")
     * @ORM\JoinColumn(name="id_field_option", referencedColumnName="id_field_option", nullable=false)
     */
    private $fieldOption;

    /**
     * @ORM\Id
     * @ORM\Column(name="id_lang", type="integer", length=10)
     */
    private $idLang;

    /**
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    public function getFieldOption()
    {
        return $this->fieldOption;
    }

    public function setFieldOption(OpcFieldOption $fieldOption)
    {
        $this->fieldOption = $fieldOption;
    }

    public function getIdLang()
    {
        return (int) $this->idLang;
    }

    public function setIdLang($idLang)
    {
        $this->idLang = (int) $idLang;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function toArray()
    {
        return array(
            'idLang' => $this->getIdLang(),
            'description' => $this->getDescription(),
        );
    }

    public function toArrayLegacy()
    {
        return array(
            'id_lang' => $this->getIdLang(),
            'description' => $this->getDescription(),
        );
    }
}
