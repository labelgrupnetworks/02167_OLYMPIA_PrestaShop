<?php
/**
* The file is Field Config of module. Do not modify the file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright  2017 Globo., Jsc
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/

class Inputfield
{
    public  function getInfo()
	{
		return array(
                'label' => $this->l('Text'),
                'icon' => '../modules/gformbuilderpro/views/img/input.png',
                'desc' =>$this->l('Input field'),
                'possition' =>1
                );
	}
    public function getConfig()
	{
	    $validatearray = array(
            array('value' => '','name' => ''),
            array('value' => 'isGenericName','name' => 'isGenericName'),
            array('value' => 'isAddress','name' => 'isAddress'),
            array('value' => 'isEmail','name' => 'isEmail'),
            array('value' => 'isCityName','name' => 'isCityName'),
            array('value' => 'isDate','name' => 'isDate'),
            array('value' => 'isUrl','name' => 'isUrl'),
            array('value' => 'isInt','name' => 'isInt'),
            array('value' => 'isUnsignedInt','name' => 'isUnsignedInt'),
            array('value' => 'isFloat','name' => 'isFloat'),
            array('value' => 'isUnsignedFloat','name' => 'isUnsignedFloat'),
            array('value' => 'isPercentage','name' => 'isPercentage'),
            array('value' => 'isPrice','name' => 'isPrice'),
            array('value' => 'isString','name' => 'isString'),
            array('value' => 'isPostCode','name' => 'isPostCode'),
            array('value' => 'isBirthDate','name' => 'isBirthDate'),
            array('value' => 'isPasswd','name' => 'isPasswd'),
            array('value' => 'isPhoneNumber','name' => 'isPhoneNumber')
        );
        $label_pos = array(
            array('value' => '0','name' => 'Top'),
            array('value' => '1','name' => 'Left'),
            array('value' => '2','name' => 'Right'),
            array('value' => '3','name' => 'Hidden'),
        );
        $dynamic_val = array(
            array('value' => '','name' => ''),
            array('value' => 'customerid','name' => 'Customer Id'),
            array('value' => 'customerfirstname','name' => 'Custommer First Name'),
            array('value' => 'customerlastname','name' => 'Custommer Last Name'),
            array('value' => 'customername','name' => 'Custommer Full Name'),
            array('value' => 'customeremail','name' => 'Custommer Email'),
            array('value' => 'customercompany','name' => 'Custommer Company'),
            array('value' => 'customer_address','name' => 'Custommer Address'),
            array('value' => 'customer_postcode','name' => 'Custommer Postcode'),
            array('value' => 'customer_city','name' => 'Custommer City'),
            array('value' => 'customer_phone','name' => 'Custommer Phone'),
            array('value' => 'productid','name' => 'Product Id'),
            array('value' => 'productname','name' => 'Product Name')
            
            
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
				'label' => $this->l('Field Name'),
                'class'=>'gvalidate gvalidate_isName gvalidate_isRequired',
                'required'=>true
			),
            array(
				'type' => 'text',
				'name' => 'placeholder',
				'label' => $this->l('Placeholder'),
                'lang'=>true,
				'default' => '',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Dynamic Value'),
                'name' => 'dynamicval',
                'required' => false,
                'lang' => false,
                'options' => array(
                    'query' => $dynamic_val,
                    'id' => 'value',
                    'name' => 'name'
                )),
            array(
                'type' => (version_compare(_PS_VERSION_,'1.6') == -1) ? 'radio' : 'switch',
                'label' => $this->l('Required field'),
                'name' => 'required',
                'required' => false,
                'is_bool' => true,
                'values' => array(array(
                        'id' => 'required_on',
                        'value' => 1,
                        'label' => $this->l('Yes')), array(
                        'id' => 'required_off',
                        'value' => 0,
                        'label' => $this->l('No')))),
            array(
                'type' => (version_compare(_PS_VERSION_,'1.6') == -1) ? 'radio' : 'switch',
                'label' => $this->l('Register newsletter if input value is email'),
                'name' => 'extra',
                'required' => false,
                'is_bool' => true,
                'values' => array(array(
                        'id' => 'extra_on',
                        'value' => 1,
                        'label' => $this->l('Yes')), array(
                        'id' => 'extra_off',
                        'value' => 0,
                        'label' => $this->l('No')))),
            
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
                'type' => 'select',
                'label' => $this->l('Validation type'),
                'name' => 'validate',
                'required' => false,
                'lang' => false,
                'options' => array(
                    'query' => $validatearray,
                    'id' => 'value',
                    'name' => 'name'
                )),
            array(
				'type' => 'textarea',
				'name' => 'description',
                'id' => 'description',
                'lang' => true,
				'label' => $this->l('Description'),
                'desc' => $this->l('Add description to explain to your customer about this field.'),
            'cols'=>50,
            'rows'=>5
			),
			array(
				'type' => 'text',
				'name' => 'idatt',
				'label' => $this->l('Html id'),
                'class'=>'gvalidate gvalidate_isId',
                'desc' => $this->l('Add your custom id so you can custom css for this field'),
			),
            array(
				'type' => 'text',
				'name' => 'classatt',
				'label' => $this->l('Html class'),
                'class'=>'gvalidate gvalidate_isClass',
                'desc' => $this->l('Add your custom class so you can custom css for this field'),
			),
		);
		return $inputs;
	}
    public function l($string, $specific = false)
	{
		return Translate::getModuleTranslation('gformbuilderpro', $string, ($specific) ? $specific : 'gformbuilderpro');
	}
}