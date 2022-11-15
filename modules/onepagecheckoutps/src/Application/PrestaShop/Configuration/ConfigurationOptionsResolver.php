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

namespace OnePageCheckoutPS\Application\PrestaShop\Configuration;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationOptionsResolver
{
    private $optionsResolver;

    public function __construct($shopId, $shopGroupId)
    {
        $this->optionsResolver = new OptionsResolver();
        $this->optionsResolver->setDefaults(array(
            'global' => false,
            'default_value' => '',
            'id_lang' => null,
            'is_lang' => false,
            'is_html' => false,
            'is_bool' => false,
        ));
        $this->optionsResolver->setDefault('id_shop', function (Options $options) use ($shopId) {
            if (true === $options['global']) {
                return null;
            }

            return $shopId;
        });
        $this->optionsResolver->setDefault('id_shop_group', function (Options $options) use ($shopGroupId) {
            if (true === $options['global']) {
                return null;
            }

            return $shopGroupId;
        });

        $this->optionsResolver->setAllowedTypes('global', 'bool');
        $this->optionsResolver->setAllowedTypes('id_lang', array('null', 'int'));
        $this->optionsResolver->setAllowedTypes('id_shop', array('null', 'int'));
        $this->optionsResolver->setAllowedTypes('id_shop_group', array('null', 'int'));
        $this->optionsResolver->setAllowedTypes('is_lang', 'bool');
        $this->optionsResolver->setAllowedTypes('is_bool', 'bool');
        $this->optionsResolver->setAllowedTypes('is_bool', 'bool');
    }

    public function resolve(array $options)
    {
        return $this->optionsResolver->resolve($options);
    }
}
