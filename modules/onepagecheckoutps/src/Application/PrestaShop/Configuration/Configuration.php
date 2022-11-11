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

use Configuration as PrestaShopConfiguration;
use Language;
use OnePageCheckoutPS\Exception\OPCException;

class Configuration
{
    private $optionsResolver;

    public const SERVICE_NAME = 'onepagecheckoutps.prestashop.configuration';

    public function __construct(ConfigurationOptionsResolver $optionsResolver)
    {
        $this->optionsResolver = $optionsResolver;
    }

    public function has($key, array $options = array())
    {
        $settings = $this->optionsResolver->resolve($options);

        return (bool) PrestaShopConfiguration::hasKey(
            $key,
            $settings['id_lang'],
            $settings['id_shop_group'],
            $settings['id_shop']
        );
    }

    public function get($key, array $options = array())
    {
        $settings = $this->optionsResolver->resolve($options);

        $value = PrestaShopConfiguration::get(
            $key,
            $settings['id_lang'],
            $settings['id_shop_group'],
            $settings['id_shop']
        );

        if (empty($value)) {
            return $settings['default_value'];
        }

        return $value;
    }

    public function set($key, $value, array $options = array())
    {
        $settings = $this->optionsResolver->resolve($options);

        $success = (bool) PrestaShopConfiguration::updateValue(
            $key,
            $value,
            $settings['is_html'],
            $settings['id_shop_group'],
            $settings['id_shop']
        );

        if (false === $success) {
            throw new OPCException(sprintf('Could not set key %s in PrestaShop configuration', $key));
        }

        return $this;
    }

    public function remove($key)
    {
        $success = (bool) PrestaShopConfiguration::deleteByName($key);

        if (false === $success) {
            throw new OPCException(sprintf('Could not remove key %s from PrestaShop configuration', $key));
        }

        return $this;
    }

    public function fill(&$moduleConfigs)
    {
        if (!is_array($moduleConfigs) || empty($moduleConfigs)) {
            throw new OPCException(sprintf('Configuration is not defined'));
        }

        $languages = Language::getLanguages(false);

        foreach ($moduleConfigs as $key => &$config) {
            $settings = $this->optionsResolver->resolve($config['options']);

            if ($settings['is_bool']) {
                $config['value'] = (bool) $this->get($key);
            } else {
                $config['value'] = $this->get($key);

                if ($settings['is_lang']) {
                    $config['value'] = array();
                    foreach ($languages as $language) {
                        $config['value'][$language['id_lang']] = $this->get(
                            $key,
                            array('id_lang' => (int) $language['id_lang'])
                        );
                    }
                }
            }
        }
    }
}
