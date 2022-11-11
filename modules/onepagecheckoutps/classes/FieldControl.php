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
 * @category  PrestaShop
 * @category  Module
 */

class FieldControl extends FieldClass
{
    public $options;
    public $id_control;
    public $name_control;
    public $error_message;
    public $help_message;
    public $classes;
    public $value;

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);

        $this->classes       = $this->object.($this->required ? ' required' : '');
        $this->id_control    = (!empty($this->object) ? $this->object.'_' : '').$this->name;
        $this->name_control  = (!empty($this->object) ? $this->object.'_' : '').$this->name;
        $this->error_message = '';
        $this->help_message  = '';
        $this->options       = array();
        $this->value         = '';

        $context = Context::getContext();

        if ($this->name == 'id' && ($this->object == 'delivery' || $this->object == 'invoice')) {
            $this->options = array(
                'empty_option' => true
            );

            if (Validate::isLoadedObject($context->customer) && $context->customer->isLogged()) {
                $address_customer = $context->customer->getAddresses($id_lang);

                if (empty($address_customer)) {
                    $this->default_value = '';
                }

                $this->options = array(
                    'empty_option' => true,
                    'value'        => 'id_address',
                    'description'  => 'alias',
                    'data'         => $address_customer
                );
            }
        } elseif ($this->is_custom && ($this->type_control === 'radio' || $this->type_control === 'select')) {
            //search in options custom
            $data = FieldOptionClass::getOptionsByIdField($this->id, $id_lang);

            $this->options = array(
                'empty_option' => true,
                'value'        => 'value',
                'description'  => 'description',
                'data'         => $data
            );
        }
    }
}
