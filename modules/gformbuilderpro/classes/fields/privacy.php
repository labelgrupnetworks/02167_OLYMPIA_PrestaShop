<?php
/**
* The file is Field Config of module. Do not modify the file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright  2017 Globo., Jsc
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/

class Privacyfield
{
    public  function getInfo()
	{
		return array(
                'label' => $this->l('Privacy'),
                'icon' => '../modules/gformbuilderpro/views/img/checkbox.png',
                'desc' =>$this->l(''),
                'possition' =>10
                );
	}
    public function getConfig()
	{
	   $label_pos = array(
            array('value' => '1','name' => 'Left'),
            array('value' => '2','name' => 'Right'),
            array('value' => '3','name' => 'None'),
        );
		$inputs = array(
            array(
				'type' => 'hidden',
				'name' => 'type',
				'default' => 'privacy',
			),
            array(
				'type' => 'text',
				'name' => 'label',
                'required'=>true,
                'lang'=>true,
				'label' => $this->l('Name of this block'),
                'class'=>'gvalidate  gvalidate_isRequired',
			),
            array(
                'type' => 'select',
                'label' => $this->l('Align'),
                'name' => 'labelpos',
                'required' => false,
                'lang' => false,
                'options' => array(
                    'query' => $label_pos,
                    'id' => 'value',
                    'name' => 'name'
                )),
			array(
				'type' => 'hidden',
				'name' => 'name',
                'required'=>true,
				'label' => $this->l('Name'),
				'class'=>'gvalidate gvalidate_isName gvalidate_isRequired',
			),
            array(
				'type' => 'textarea',
				'name' => 'description',
                'id' => 'description',
                'lang' => true,
				'label' => $this->l('Content'),
                'class'=>'textareatiny',
                
            'cols'=>50,
            'rows'=>5
			)
		);
		return $inputs;
	}
    public function l($string, $specific = false)
	{
		return Translate::getModuleTranslation('gformbuilderpro', $string, ($specific) ? $specific : 'gformbuilderpro');
	}
}