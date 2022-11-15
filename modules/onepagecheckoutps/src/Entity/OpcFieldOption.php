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
class OpcFieldOption
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id_field_option", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="OnePageCheckoutPS\Entity\OpcField", inversedBy="fieldOption")
     * @ORM\JoinColumn(name="id_field", referencedColumnName="id_field", nullable=false)
     */
    private $field;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;

    /**
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @ORM\OneToMany(targetEntity="OnePageCheckoutPS\Entity\OpcFieldOptionLang", cascade={"persist", "remove"}, mappedBy="fieldOption")
     */
    private $fieldLangs;

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

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getPosition()
    {
        return (int) $this->position;
    }

    public function setPosition($position)
    {
        $this->position = (int) $position;
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

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'value' => $this->getValue(),
            'position' => $this->getPosition(),
        );
    }

    public function toArrayLegacy()
    {
        return array(
            'id' => $this->getId(),
            'value' => $this->getValue(),
            'position' => $this->getPosition(),
        );
    }
}
