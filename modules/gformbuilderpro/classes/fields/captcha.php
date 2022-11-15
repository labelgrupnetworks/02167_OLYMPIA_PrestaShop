<?php
/**
* The file is Field Config of module. Do not modify the file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright  2017 Globo., Jsc
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/

class Captchafield
{
    public  function getInfo()
	{
		return array(
                'label' => $this->l('reCAPTCHA'),
                'icon' => '../modules/gformbuilderpro/views/img/recaptcha.png',
                'desc' =>$this->l(''),
                'possition' =>111
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
				'label' => $this->l('Name'),
                'required'=>true,
				'class'=>'gvalidate gvalidate_isName gvalidate_isRequired',
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
		);
        $id_shop_group = Shop::getContextShopGroupID();
		$id_shop = Shop::getContextShopID();
        $GF_RECAPTCHA = Configuration::get('GF_RECAPTCHA', null, $id_shop_group, $id_shop);
        $GF_SECRET_KEY = Configuration::get('GF_SECRET_KEY', null, $id_shop_group, $id_shop);
        if($GF_RECAPTCHA == '' || $GF_SECRET_KEY == ''){
            $inputs[] = array(
				'type' => 'free',
                'desc'=>$GF_RECAPTCHA.'<div class="alert alert-warning">'.$this->l('You must config reCAPTCHA in ').'<a target="_blank" href="'.Context::getContext()->link->getAdminLink('AdminGformconfig').'">'.$this->l(' here').'</a></div>'
			);
        }
		return $inputs;
	}
    public function l($string, $specific = false)
	{
		return Translate::getModuleTranslation('gformbuilderpro', $string, ($specific) ? $specific : 'gformbuilderpro');
	}
}