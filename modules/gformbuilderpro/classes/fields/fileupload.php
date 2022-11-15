<?php
/**
* The file is Field Config of module. Do not modify the file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright  2017 Globo., Jsc
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/

class Fileuploadfield
{
    public  function getInfo()
	{
		return array(
                'label' => $this->l('File'),
                'icon' => '../modules/gformbuilderpro/views/img/fileupload.png',
                'desc' => $this->l(''),
                'possition' =>8
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
				'type' => 'tags',
				'name' => 'extra',
                'id' => 'extra',
                'required'=>true,
				'label' => $this->l('Allowed extensions'),
                'class'=>'select_value gvalidate gvalidate_isRequired',
                'desc'=>$this->l('List of allowed extensions(ex. jpg,jpeg,png,gif)')
			),
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
                'label' => $this->l('Multi file'),
                'name' => 'multi',
                'desc' => $this->l('Yes, if you allow user upload multi files'),
                'required' => false,
                'is_bool' => true,
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