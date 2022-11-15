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
class OpcSocialNetwork
{
    /**
     * @ORM\Id
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="id_shop", type="integer", length=10)
     */
    private $shopId;

    /**
     * @var int
     *
     * @ORM\Column(name="enabled", type="integer", length=1)
     */
    private $enabled = false;

    /**
     * @var text
     *
     * @ORM\Column(name="`keys`", type="text")
     */
    private $keys;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setShopId($shopId)
    {
        $this->shopId = (int) $shopId;
    }

    public function getShopId()
    {
        return (int) $this->shopId;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = (bool) $enabled;
    }

    public function getEnabled()
    {
        return (bool) $this->enabled;
    }

    public function setKeys($keys)
    {
        $this->keys = json_encode($keys);
    }

    public function getKeys()
    {
        return json_decode($this->keys, true);
    }

    public function toArray()
    {
        return array(
            'name' => $this->getName(),
            'enabled' => $this->getEnabled(),
            'keys' => $this->getKeys(),
        );
    }
}
