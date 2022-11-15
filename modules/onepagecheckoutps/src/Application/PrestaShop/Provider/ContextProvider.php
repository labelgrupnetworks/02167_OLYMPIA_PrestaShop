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

use Context;

class ContextProvider
{
    private $context;

    public const SERVICE_NAME = 'onepagecheckoutps.prestashop.provider.context';

    public function __construct()
    {
        $this->context = Context::getContext();
    }

    public function getLanguageIsoCode()
    {
        return $this->context->language !== null ? $this->context->language->iso_code : 'en';
    }

    public function getLanguage()
    {
        return $this->context->language;
    }

    public function getLanguageId()
    {
        return $this->context->language->id;
    }

    public function getLink()
    {
        return $this->context->link;
    }

    public function getShopId()
    {
        return (int) $this->context->shop->id;
    }

    public function getShopGroupId()
    {
        return (int) $this->context->shop->id_shop_group;
    }

    public function getShopName()
    {
        return $this->context->shop->name;
    }

    public function getCurrencyIsoCode()
    {
        return $this->context->currency !== null ? $this->context->currency->iso_code : 'EUR';
    }

    public function getCurrentThemeName()
    {
        return $this->context->shop->theme_name;
    }

    public function getCustomer()
    {
        return $this->context->customer;
    }

    public function getSmarty()
    {
        return $this->context->smarty;
    }

    public function getCart()
    {
        return $this->context->cart;
    }

    public function getCountry()
    {
        return $this->context->country;
    }

    public function getContextLegacy()
    {
        return $this->context;
    }

    public function getController()
    {
        return $this->context->controller;
    }

    public function getCookie()
    {
        return $this->context->cookie;
    }

    public function isMobile()
    {
        return $this->context->isMobile();
    }

    public function isTablet()
    {
        return $this->context->isTablet();
    }

    public function getDevice()
    {
        return $this->context->getDevice();
    }

    public function isVirtualCart()
    {
        return $this->context->cart->isVirtualCart();
    }
}
