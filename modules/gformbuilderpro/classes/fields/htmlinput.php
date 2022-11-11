<?php
/**
* The file is Field Config of module. Do not modify the file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright  2017 Globo., Jsc
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/

class Htmlinputfield
{
    public  function getInfo()
	{
		return array(
                'label' => $this->l('Textarea with Editor'),
                'icon' => '../modules/gformbuilderpro/views/img/htmlinput.png',
                'desc' =>$this->l('A block of text with WYSIWYG editor'),
                'possition' =>4
                );
	}
    public function getConfig()
	{
	    $validatearray = array(
            array('value' => 'isCleanHtml','name' => 'isCleanHtml'),
        );
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
				'default' => 'textarea',
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
                'required'=>true,
				'class'=>'gvalidate gvalidate_isName gvalidate_isRequired',
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
				'label' => $this->l('HTML Id'),
                'class'=>'gvalidate gvalidate_isId',
                'desc' => $this->l('Add your custom id so you can custom css for this field'),
			),
            array(
				'type' => 'text',
				'name' => 'classatt',
				'label' => $this->l('HTML Class'),
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