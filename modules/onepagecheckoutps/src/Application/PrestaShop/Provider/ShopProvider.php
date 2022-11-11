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

namespace OnePageCheckoutPS\Application\PrestaShop\Provider;

use Configuration;
use Shop;
use Tools;

class ShopProvider
{
    private $prestashopContext;

    public const SERVICE_NAME = 'onepagecheckoutps.prestashop.provider.shop';

    public function __construct(ContextProvider $prestashopContext)
    {
        $this->prestashopContext = $prestashopContext;
    }

    public function getIdentifier()
    {
        return (int) $this->prestashopContext->getShopId();
    }

    public function getGroupIdentifier()
    {
        return (int) $this->prestashopContext->getShopGroupId();
    }

    public function getShops()
    {
        return Shop::getShops();
    }

    public function getContextShopID()
    {
        return (int) Shop::getContextShopID(true);
    }

    public function getContextShopGroupID()
    {
        return (int) Shop::getContextShopGroupID(true);
    }

    public function getShopUrl($shopId)
    {
        return (new Shop($shopId))->getBaseURL();
    }

    public function isMultistoreActive()
    {
        return (bool) Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE');
    }

    public function getShopsUrl()
    {
        $shopList = Shop::getShops();
        $protocol = $this->getShopsProtocolInformations();
        $urlList = array();

        foreach ($shopList as $shop) {
            $urlList[] = array(
                'id_shop' => $shop['id_shop'],
                'url' => $protocol['protocol'] . $shop[$protocol['domain_type']] . $shop['uri'],
            );
        }

        return $urlList;
    }

    protected function getShopsProtocolInformations()
    {
        if (true === Tools::usingSecureMode()) {
            return array(
                'domain_type' => 'domain_ssl',
                'protocol' => 'https://',
            );
        }

        return array(
            'domain_type' => 'domain',
            'protocol' => 'http://',
        );
    }
}
