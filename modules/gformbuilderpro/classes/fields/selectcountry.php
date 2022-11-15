<?php
/**
* The file is Field Config of module. Do not modify the file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright  2017 Globo., Jsc
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/

class Selectcountryfield
{
    public  function getInfo()
	{
		return array(
                'label' => $this->l('Select Country'),
                'icon' => '../modules/gformbuilderpro/views/img/select.png',
            'desc' =>$this->l(''),
                'possition' =>3
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
        $activecountry = array(
            array('value' => '0','name' => 'All Country'),
            array('value' => '1','name' => 'Only Active Country'),
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
                'required'=>true,
				'label' => $this->l('Label'),
                'class'=>'gvalidate  gvalidate_isRequired',
			),
			array(
				'type' => 'text',
				'name' => 'name',
                'required'=>true,
				'label' => $this->l('Field Name'),
				'class'=>'gvalidate gvalidate_isName gvalidate_isRequired',
			),
            /*
            array(
				'type' => 'text',
				'name' => 'placeholder',
				'label' => $this->l('Placeholder'),
                'lang'=>true,
				'default' => '',
			),
            
            array(
				'type' => 'multival',
				'name' => 'value',
                'id' => 'value',
                'lang' => true,
				'label' => $this->l('Value'),
                'class'=>'select_value gvalidate  gvalidate_isRequired',
                'required' => true,
                'desc' => $this->l('Value or option of select. Enter to add new value.'),
			),*/
            array(
                'type' => 'select',
                'label' => $this->l('Country Option'),
                'name' => 'extra',
                'required' => false,
                'lang' => false,
                'options' => array(
                    'query' => $activecountry,
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
                'label' => $this->l('Multi select'),
                'name' => 'multi',
                'required' => false,
                'is_bool' => true,
                'desc' => $this->l('If on, then front-end user can select multi value.'),
                'values' => array(array(
                        'id' => 'multi_on',
                        'value' => 1,
                        'label' => $this->l('Yes')), array(
                        'id' => 'multi_off',
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
				'label' => $this->l('Id'),
                'class'=>'gvalidate gvalidate_isId',
                'desc' => $this->l('Add custom id so you can custom css'),
			),
            array(
				'type' => 'text',
				'name' => 'classatt',
				'label' => $this->l('Class'),
                'class'=>'gvalidate gvalidate_isClass',
                'desc' => $this->l('Add custom class so you can custom css'),
			),
		);
		return $inputs;
	}
    public function l($string, $specific = false)
	{
		return Translate::getModuleTranslation('gformbuilderpro', $string, ($specific) ? $specific : 'gformbuilderpro');
	}
}