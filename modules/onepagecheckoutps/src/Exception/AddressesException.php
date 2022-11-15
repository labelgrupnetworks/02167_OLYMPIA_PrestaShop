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

class AddressesException extends OPCException
{
    public const ADDRESS_FIELD_REQUIRED = 1;
    public const ADDRESS_NEED_CUSTOMER_GUEST_SESSION = 2;
    public const ADDRESS_DEFER_CUSTOMER_SESSION = 3;
    public const ADDRESS_POSTCODE_FORMAT_INVALID = 4;
    public const ADDRESS_VATNUMBER_INVALID = 5;
    public const ADDRESS_DNI_INVALID = 6;
    public const ADDRESS_COULD_NOT_BE_LOADED = 7;
    public const NON_UNIQUE_DNI = 8;
}
