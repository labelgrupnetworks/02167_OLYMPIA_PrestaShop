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

class MyAccountException extends OPCException
{
    public const CUSTOMER_EMAIL_EMPTY = 1;
    public const CUSTOMER_EMAIL_INVALID = 2;
    public const CUSTOMER_PASSWORD_EMPTY = 3;
    public const CUSTOMER_PASSWORD_INVALID = 4;
    public const CUSTOMER_DISABLED = 5;
    public const CUSTOMER_ACCESS_INCORRECT = 6;
    public const CUSTOMER_ALREADY_LOGGED_IN = 7;
    public const CUSTOMER_EMAIL_ALREADY_USED = 8;
    public const CUSTOMER_EMAIL_MUST_MATCH = 9;
    public const CUSTOMER_PASSWORD_MUST_MATCH = 10;
    public const CUSTOMER_FIELD_REQUIRED = 11;
    public const CUSTOMER_SESSION_NOT_GUEST = 12;
    public const CUSTOMER_CAPTCHA_NOT_VALID = 13;

    public const SOCIAL_NETWORK_NOT_SUPPORTED = 20;
}
