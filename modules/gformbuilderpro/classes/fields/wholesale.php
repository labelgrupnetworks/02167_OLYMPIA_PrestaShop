<?php
/**
* The file is Field Config of module. Do not modify the file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright  2017 Globo., Jsc
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/

class Wholesalefield
{
    public  function getInfo()
	{
		return array(
                'label' => $this->l('Wholesale'),
                'icon' => '../modules/gformbuilderpro/views/img/product.png',
                'desc' =>$this->l('Only one Wholesale widget can be added per form.'),
                'possition' =>10
                );
	}
    public function getConfig()
	{
	   $label_pos = array(
            array('value' => '0','name' => 'Top'),
            array('value' => '1','name' => 'Left'),
            array('value' => '2','name' => 'Right'),    
            array('value' => '3','name' => 'Hidden'),
        );
		$inputs = array(
            array(
				'type' => 'hidden',
				'name' => 'type',
			),
            array(
				'type' => 'extraproducts',
				'name' => 'extra',
                'id' => 'extra',
                'required'=>true,
				'label' => $this->l(' Config Product'),
                'class'=>'select_value gvalidate  gvalidate_isRequired',
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
                'class'=>'gvalidate gvalidate_isName gvalidate_isRequired',
                'required'=>true
			),
            array(
                'type' => 'select',
                'label' => $this->l('Label position'),
                'name' => 'labelpos',
                'required' => false,
                'lang' => false,
                'options' => array(
                    'query' => $label_pos,
                    'id' => 'value',
                    'name' => 'name'
                )),
            array(
				'type' => 'textarea',
				'name' => 'description',
                'id' => 'description',
                'lang' => true,
				'label' => $this->l('Description'),
            'cols'=>50,
            'rows'=>5
			),
            array(
				'type' => 'hidden',
				'name' => 'idatt',
				'label' => $this->l('Id'),
                'class'=>'gvalidate gvalidate_isId',
                'desc' => $this->l('Add custom id so you can custom css'),
			),
            array(
                'type' => (version_compare(_PS_VERSION_,'1.6') == -1) ? 'radio' : 'switch',
                'label' => $this->l('Automatically redirect'),
                'desc' => $this->l('Automatically redirect to cart after submitted.'),
                'name' => 'extra_option',
                'required' => true,
                'is_bool' => true,
                'values' => array(array(
                        'id' => 'extra_option_on',
                        'value' => 1,
                        'label' => $this->l('Yes')), array(
                        'id' => 'extra_option_off',
                        'value' => 0,
                        'label' => $this->l('No')))),
		);
		return $inputs;
	}
    public function l($string, $specific = false)
	{
		return Translate::getModuleTranslation('gformbuilderpro', $string, ($specific) ? $specific : 'gformbuilderpro');
	}
}