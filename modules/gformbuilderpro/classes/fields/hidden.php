<?php
/**
* The file is Field Config of module. Do not modify the file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright  2017 Globo., Jsc
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/

class Hiddenfield
{
    public  function getInfo()
	{
		return array(
                'label' => $this->l('Hidden Field'),
                'icon' =>'../modules/gformbuilderpro/views/img/hidden.png',
                'desc' => $this->l(''),
                'possition' =>10
                );
	}
    public function getConfig()
	{
	   $validatearray = array(
            array('value' => '','name' => ''),
            array('value' => 'customerid','name' => 'Customer Id'),
            array('value' => 'customername','name' => 'Custommer Name'),
            array('value' => 'customeremail','name' => 'Custommer Email'),
            array('value' => 'productid','name' => 'Product Id'),
            array('value' => 'productname','name' => 'Product Name'),
            array('value' => 'productatt','name' => 'Product Combination'),
            array('value' => 'shopname','name' => 'Shop Name'),
            array('value' => 'currencyname','name' => 'Currency'),
            array('value' => 'languagename','name' => 'Language'),
        );
		$inputs = array(
            array(
				'type' => 'hidden',
				'name' => 'type',
			),
            array(
				'type' => 'text',
				'name' => 'label',
                'lang'=>true,
				'label' => $this->l('Label'),
                'required'=>true,
                'class'=>'gvalidate  gvalidate_isRequired',
			),
			array(
				'type' => 'text',
				'name' => 'name',
				'label' => $this->l('Name'),
                'required'=>true,
				'class'=>'gvalidate gvalidate_isName gvalidate_isRequired',
			),
            array(
				'type' => 'text',
				'name' => 'value',
                'id' => 'value',
                'lang' => true,
				'label' => $this->l('Value'),
                'required' => false,
                'desc' => $this->l('Your custom value. If you add the value, then dont\' select Dynamic value bellow.'),
                'class'=>'select_value gvalidate_isRequired2',
			),
            array(
                'type' => 'select',
                'label' => $this->l('Or Dynamic value'),
                'name' => 'extra',
                'required' => false,
                'desc' => $this->l('Width dynamic value, add product id, customer id automatic to the form.'),
                'lang' => false,
                'options' => array(
                    'query' => $validatearray,
                    'id' => 'value',
                    'name' => 'name'
                )),
			array(
				'type' => 'text',
				'name' => 'idatt',
				'label' => $this->l('Html Id'),
                'class'=>'gvalidate gvalidate_isId',
			),
            array(
				'type' => 'text',
				'name' => 'classatt',
				'label' => $this->l('Html Class'),
                'class'=>'gvalidate gvalidate_isClass',
			),
		);
		return $inputs;
	}
    public function l($string, $specific = false)
	{
		return Translate::getModuleTranslation('gformbuilderpro', $string, ($specific) ? $specific : 'gformbuilderpro');
	}
}