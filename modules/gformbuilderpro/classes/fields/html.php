<?php
/**
* The file is Field Config of module. Do not modify the file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright  2017 Globo., Jsc
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/

class Htmlfield
{
    public  function getInfo()
	{
		return array(
                'label' => $this->l('Html Block'),
                'icon' => '../modules/gformbuilderpro/views/img/html.png',
                'desc' =>$this->l(''),
                'possition' =>10
                );
	}
    public function getConfig()
	{
		$inputs = array(
            array(
				'type' => 'hidden',
				'name' => 'type',
				'default' => 'html',
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