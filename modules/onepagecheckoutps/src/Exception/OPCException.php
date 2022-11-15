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

use Exception;

class OPCException extends Exception
{
    protected $parameters;

    public const UNKNOWN = 0;
    public const PARAMETER_NOT_SENT = 1;

    public function __construct($message, $code, $parameters = array())
    {
        parent::__construct($message, $code);

        $this->parameters = $parameters;
    }

    public function getMessageFormatted($messageLang)
    {
        if (count($this->parameters) > 0 && !empty($messageLang)) {
            return vsprintf($messageLang, $this->parameters);
        }

        if (empty($messageLang)) {
            return parent::getMessage();
        }

        return $messageLang;
    }
}
