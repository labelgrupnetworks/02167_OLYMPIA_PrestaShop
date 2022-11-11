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

namespace OnePageCheckoutPS\Hook;

abstract class AbstractHook
{
    private $parameters;

    protected $allowControllerList = array();

    abstract protected function executeRun();

    public function run(array $parameters = array())
    {
        if (empty($this->allowControllerList)
            || !isset($parameters['context'])
            || in_array($parameters['context']->controller->php_self, $this->allowControllerList)
        ) {
            $this->setParameters($parameters);

            return $this->executeRun();
        }
    }

    public function getParameters()
    {
        if (is_array($this->parameters)) {
            return $this->parameters;
        }

        return array();
    }

    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }
}
