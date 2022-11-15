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

namespace OnePageCheckoutPS\Exception;

class SocialNetworkException extends OPCException
{
    public const SOCIAL_NETWORK_EMPTY = 1;
    public const SOCIAL_NETWORK_KEYS_EMPTY = 2;
    public const SOCIAL_NETWORK_KEY_VALUE_EMPTY = 3;
    public const SOCIAL_NETWORK_NOT_SUPPORTED = 4;
    public const SOCIAL_NETWORK_NOT_FOUND = 5;
    public const SOCIAL_NETWORK_DUPLICATED = 6;
}
