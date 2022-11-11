<?php
/**
 * The file is controller. Do not modify the file if you want to upgrade the module in future
 *
 * @author    Globo Software Solution JSC <contact@globosoftware.net>
 * @copyright  2017 Globo., Jsc
 * @license   please read license in file license.txt
 * @link	     http://www.globosoftware.net
 */

class AdminGformconfigController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'edit';
        parent::__construct();
        $this->meta_title = $this->l('Form Builder Pro');
        if (!$this->module->active)
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminDashboard'));
    }
    public function initContent()
    {
        $this->display = 'edit';
        $this->initTabModuleList();
        $this->initToolbar();
        $this->initPageHeaderToolbar();
        $this->content = $this->renderForm();
        $this->context->smarty->assign(array(
            'content' => $this->content,
            'url_post' => self::$currentIndex.'&token='.$this->token,
        ));
        if(version_compare(_PS_VERSION_,'1.6') == 1){
            $this->context->smarty->assign(array(
                'show_page_header_toolbar' => $this->show_page_header_toolbar,
                'page_header_toolbar_title' => $this->page_header_toolbar_title,
                'page_header_toolbar_btn' => $this->page_header_toolbar_btn
            ));
        }
    }
    public function initTabModuleList(){
        if(version_compare(_PS_VERSION_,'1.5.4.0') == -1)
            return true;
        else
            return parent::initTabModuleList();
    }
    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('Form Builder Pro');
        $this->toolbar_title[] = $this->l('General Settings');
    }
    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn = array(
            'new' => array(
                'href' => $this->context->link->getAdminLink('AdminGformmanager'),
                'desc' => $this->l('Form'),
                'icon' => 'process-icon-cogs'
            ),
            'about' => array(
                'href' => $this->context->link->getAdminLink('AdminGformrequest'),
                'desc' => $this->l('Data Recieved'),
                'icon' => 'process-icon-duplicate'
            ),
        );
        if(version_compare(_PS_VERSION_,'1.6') == 1){
            parent::initPageHeaderToolbar();
        }
    }
    public function postProcess()
    {

        if (Tools::isSubmit('saveConfig'))
        {
            $shop_groups_list = array();
            $shops = Shop::getContextListShopID();
            $shop_context = Shop::getContext();
            $GF_REDIRECT_TO_URL = array();$GF_FORM_METAKEYWORDS=array();
            $languages = Language::getLanguages(false);
            foreach($languages as $lang){
                $GF_REDIRECT_TO_URL[(int)$lang['id_lang']] = Tools::getValue('GF_REDIRECT_TO_URL_'.(int)$lang['id_lang']);
                $GF_FORM_METAKEYWORDS[(int)$lang['id_lang']] = Tools::getValue('GF_FORM_METAKEYWORDS_'.(int)$lang['id_lang']);
            }
            foreach ($shops as $shop_id)
            {
                $shop_group_id = (int)Shop::getGroupFromShop((int)$shop_id, true);
                if (!in_array($shop_group_id, $shop_groups_list))
                    $shop_groups_list[] = (int)$shop_group_id;
                $res = Configuration::updateValue('GF_RECAPTCHA', Tools::getValue('GF_RECAPTCHA'), false, (int)$shop_group_id, (int)$shop_id);
                $res &= Configuration::updateValue('GF_SECRET_KEY', Tools::getValue('GF_SECRET_KEY'), false, (int)$shop_group_id, (int)$shop_id);
                $res &= Configuration::updateValue('GF_BLACKLISTED_IP', Tools::getValue('GF_BLACKLISTED_IP'), false, (int)$shop_group_id, (int)$shop_id);
                $res &= Configuration::updateValue('GF_GMAP_API_KEY', Tools::getValue('GF_GMAP_API_KEY'), false, (int)$shop_group_id, (int)$shop_id);
                $res &= Configuration::updateValue('GF_RECAPTCHA_V3', (int)Tools::getValue('GF_RECAPTCHA_V3'), false, (int)$shop_group_id, (int)$shop_id);
                $res &= Configuration::updateValue('GF_FRIENDLY_URL', Tools::getValue('GF_FRIENDLY_URL'), false, (int)$shop_group_id, (int)$shop_id);
                $res &= Configuration::updateValue('GF_REDIRECT_TO_URL', $GF_REDIRECT_TO_URL, false, (int)$shop_group_id, (int)$shop_id);
                $res &= Configuration::updateValue('GF_FORM_METAKEYWORDS', $GF_FORM_METAKEYWORDS, false, (int)$shop_group_id, (int)$shop_id);
                $res &= Configuration::updateValue('GF_REMOVER_SHOPNAME_IN_SUBJECT', (int)Tools::getValue('GF_REMOVER_SHOPNAME_IN_SUBJECT'), false, (int)$shop_group_id, (int)$shop_id);
            
                $res &= Configuration::updateValue('GF_MAILCHIMP_API_KEY', Tools::getValue('GF_MAILCHIMP_API_KEY'), false, (int)$shop_group_id, (int)$shop_id);
                $res &= Configuration::updateValue('GF_KLAVIYO_API_KEY', Tools::getValue('GF_KLAVIYO_API_KEY'), false, (int)$shop_group_id, (int)$shop_id);
                
    
            }
            /* Update global shop context if needed*/
            switch ($shop_context)
            {
                case Shop::CONTEXT_ALL:
                    $res = Configuration::updateValue('GF_RECAPTCHA', Tools::getValue('GF_RECAPTCHA'));
                    $res &= Configuration::updateValue('GF_SECRET_KEY', Tools::getValue('GF_SECRET_KEY'));
                    $res &= Configuration::updateValue('GF_BLACKLISTED_IP', Tools::getValue('GF_BLACKLISTED_IP'));
                    $res &= Configuration::updateValue('GF_GMAP_API_KEY', Tools::getValue('GF_GMAP_API_KEY'));
                    $res &= Configuration::updateValue('GF_RECAPTCHA_V3', (int)Tools::getValue('GF_RECAPTCHA_V3'));
                    $res &= Configuration::updateValue('GF_REMOVER_SHOPNAME_IN_SUBJECT', (int)Tools::getValue('GF_REMOVER_SHOPNAME_IN_SUBJECT'));
                    $res &= Configuration::updateValue('GF_FRIENDLY_URL', Tools::getValue('GF_FRIENDLY_URL'));
                    $res &= Configuration::updateValue('GF_REDIRECT_TO_URL', $GF_REDIRECT_TO_URL);
                    $res &= Configuration::updateValue('GF_FORM_METAKEYWORDS', $GF_FORM_METAKEYWORDS);

                    $res &= Configuration::updateValue('GF_MAILCHIMP_API_KEY', Tools::getValue('GF_MAILCHIMP_API_KEY'));
                    $res &= Configuration::updateValue('GF_KLAVIYO_API_KEY', Tools::getValue('GF_KLAVIYO_API_KEY'));

                    if (count($shop_groups_list))
                    {
                        foreach ($shop_groups_list as $shop_group_id)
                        {
                            $res = Configuration::updateValue('GF_RECAPTCHA', Tools::getValue('GF_RECAPTCHA'), false, (int)$shop_group_id);
                            $res &= Configuration::updateValue('GF_SECRET_KEY', Tools::getValue('GF_SECRET_KEY'), false, (int)$shop_group_id);
                            $res &= Configuration::updateValue('GF_BLACKLISTED_IP', Tools::getValue('GF_BLACKLISTED_IP'), false, (int)$shop_group_id);
                            $res &= Configuration::updateValue('GF_GMAP_API_KEY', Tools::getValue('GF_GMAP_API_KEY'), false, (int)$shop_group_id);
                            $res &= Configuration::updateValue('GF_RECAPTCHA_V3', (int)Tools::getValue('GF_RECAPTCHA_V3'), false, (int)$shop_group_id);
                            $res &= Configuration::updateValue('GF_REMOVER_SHOPNAME_IN_SUBJECT', (int)Tools::getValue('GF_REMOVER_SHOPNAME_IN_SUBJECT'), false, (int)$shop_group_id);
                            $res &= Configuration::updateValue('GF_FRIENDLY_URL', Tools::getValue('GF_FRIENDLY_URL'), false, (int)$shop_group_id);
                            $res &= Configuration::updateValue('GF_REDIRECT_TO_URL', $GF_REDIRECT_TO_URL, false, (int)$shop_group_id);
                            $res &= Configuration::updateValue('GF_FORM_METAKEYWORDS', $GF_FORM_METAKEYWORDS, false, (int)$shop_group_id);

                            $res &= Configuration::updateValue('GF_MAILCHIMP_API_KEY', Tools::getValue('GF_MAILCHIMP_API_KEY'), false, (int)$shop_group_id);
                            $res &= Configuration::updateValue('GF_KLAVIYO_API_KEY', Tools::getValue('GF_KLAVIYO_API_KEY'), false, (int)$shop_group_id);


                        }
                    }
                    break;
                case Shop::CONTEXT_GROUP:
                    if (count($shop_groups_list))
                    {
                        foreach ($shop_groups_list as $shop_group_id)
                        {
                            $res = Configuration::updateValue('GF_RECAPTCHA', Tools::getValue('GF_RECAPTCHA'), false, (int)$shop_group_id);
                            $res &= Configuration::updateValue('GF_SECRET_KEY', Tools::getValue('GF_SECRET_KEY'), false, (int)$shop_group_id);
                            $res &= Configuration::updateValue('GF_BLACKLISTED_IP', Tools::getValue('GF_BLACKLISTED_IP'), false, (int)$shop_group_id);
                            $res &= Configuration::updateValue('GF_GMAP_API_KEY', Tools::getValue('GF_GMAP_API_KEY'), false, (int)$shop_group_id);
                            $res &= Configuration::updateValue('GF_RECAPTCHA_V3', (int)Tools::getValue('GF_RECAPTCHA_V3'), false, (int)$shop_group_id);
                            $res &= Configuration::updateValue('GF_REMOVER_SHOPNAME_IN_SUBJECT', (int)Tools::getValue('GF_REMOVER_SHOPNAME_IN_SUBJECT'), false, (int)$shop_group_id);
                            $res &= Configuration::updateValue('GF_FRIENDLY_URL', Tools::getValue('GF_FRIENDLY_URL'), false, (int)$shop_group_id);
                            $res &= Configuration::updateValue('GF_REDIRECT_TO_URL', $GF_REDIRECT_TO_URL, false, (int)$shop_group_id);
                            $res &= Configuration::updateValue('GF_FORM_METAKEYWORDS', $GF_FORM_METAKEYWORDS, false, (int)$shop_group_id);

                            $res &= Configuration::updateValue('GF_MAILCHIMP_API_KEY', Tools::getValue('GF_MAILCHIMP_API_KEY'), false, (int)$shop_group_id);
                            $res &= Configuration::updateValue('GF_KLAVIYO_API_KEY', Tools::getValue('GF_KLAVIYO_API_KEY'), false, (int)$shop_group_id);

                        }
                    }
                    break;
            }
            if (!$res)
                $this->errors[] = $this->l('The configuration could not be updated.');
            else
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminGformconfig', true));
        }
    }
    public function renderForm() {
        $this->fields_form = array(
            'input' => array(
                array(
                    'type' => 'formbuildertabopen',
                    'name' => 'tabmain',
                    'class' =>'activetab'
                ),
                array(
                    'type' => 'text',
                    'lang' => false,
                    'label' => $this->l('Schema of URLs ( Friendly URL )'),
                    'name' => 'GF_FRIENDLY_URL',
                    'desc' => $this->l('Default is : ').'form/{rewrite}-g{id}.html'
                ),
                array(
                    'type' => 'tags',
                    'label' => $this->l('Meta Keywords'),
                    'name' => 'GF_FORM_METAKEYWORDS',
                    'lang' => true,
                    'desc'=> $this->l('Apply to all form'),
                    'hint' => array($this->l('Invalid characters:') . ' &lt;&gt;;=#{}', $this->l('To add "Meta keywords" click in the field, write something, and then press "Enter."'))),
                array(
                    'type' => 'text',
                    'lang' => true,
                    'label' => $this->l('Redirect to the canonical URL ( Form not found )'),
                    'name' => 'GF_REDIRECT_TO_URL',
                ),
                /* add new 27-06-2019 */
                array(
                    'type' => (version_compare(_PS_VERSION_,'1.6') == -1) ? 'radio' : 'switch',
                    'label' => $this->l('Remove [Shop_Name] from email subject'),
                    'name' => 'GF_REMOVER_SHOPNAME_IN_SUBJECT',
                    'required' => false,
                    'is_bool' => true,
                    'class'=>'switch_radio',
                    'desc' => $this->l('Only in prestashop version 1.7'),
                    'values' => array(array(
                        'id' => 'GF_REMOVER_SHOPNAME_on',
                        'value' => 1,
                        'label' => $this->l('Active')),
                        array(
                            'id' => 'GF_REMOVER_SHOPNAME_off',
                            'value' => 0,
                            'label' => $this->l('Inactive')))),
                /* #add new 27-06-2019 */
                array(
                    'type' => 'formbuildertabclose',
                    'name' => 'closetab1',
                ),
                array(
                    'type' => 'formbuildertabopen',
                    'name' => 'apitab',
                ),
                /* add new 21-12-2018 */
                array(
                    'type' => (version_compare(_PS_VERSION_,'1.6') == -1) ? 'radio' : 'switch',
                    'label' => $this->l('Using reCAPTCHA V3'),
                    'name' => 'GF_RECAPTCHA_V3',
                    'required' => false,
                    'is_bool' => true,
                    'class'=>'switch_radio',
                    'desc' => $this->l('If No, Using reCAPTCHA V2'),
                    'values' => array(array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Active')),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Inactive')))),
                /* #add new 21-12-2018 */
                array(
                    'type' => 'text',
                    'label' => $this->l('reCAPTCHA Site Key'),
                    'desc' => $this->l('Required if you want use Captcha for your form.You can get Site Key and Secret Key here: ').'<a target="_blank" href="https://www.google.com/recaptcha/admin">'.$this->l('Click here').'</a>',
                    'name' => 'GF_RECAPTCHA'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('reCAPTCHA Secret Key'),
                    'name' => 'GF_SECRET_KEY'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Google map API Key'),
                    'name' => 'GF_GMAP_API_KEY'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Mail chimp API Key'),
                    'name' => 'GF_MAILCHIMP_API_KEY',
                    'desc' => $this->l('Find or Generate Your API Key: ').'<a target="_blank" href="https://mailchimp.com/help/about-api-keys#Find_or_generate_your_API_key">'.$this->l('Click here').'</a>',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Klaviyo API Key'),
                    'name' => 'GF_KLAVIYO_API_KEY',
                    'desc' => $this->l('Find or Generate Your API Key: ').'<a target="_blank" href="https://help.klaviyo.com/hc/en-us/articles/115005062267-Manage-Your-Account-s-API-Keys">'.$this->l('Click here').'</a>',
                ),
                array(
                    'type' => 'formbuildertabclose',
                    'name' => 'closetab2',
                ),
                array(
                    'type' => 'formbuildertabopen',
                    'name' => 'blacklisttab',
                ),
                array(
                    'type' => 'tags',
                    'name' => 'GF_BLACKLISTED_IP',
                    'label' => $this->l('Blacklisted IP addresses'),
                ),
                array(
                    'type' => 'formbuildertabclose',
                    'name' => 'closetab3',
                )

            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'saveConfig'
            )
        );
        $is_left_tab = (int)Configuration::get('GF_LEFT_TAB_BAR');
        if($is_left_tab != 1) $is_left_tab = 0;
        Context::getContext()->smarty->assign(array(
            'psversion15'=>version_compare(_PS_VERSION_,'1.6'),
            'is_left_tab'=>$is_left_tab
        ));
        $this->fields_value = $this->getConfigFieldsValues();
        $tpl = _PS_MODULE_DIR_.'gformbuilderpro/views/templates/admin/gformconfig/tabs.tpl';
        $formhtml= Context::getContext()->smarty->fetch($tpl).parent::renderForm();
        if($is_left_tab)
        {
            Context::getContext()->smarty->assign(array('is_close_tab'=>1));
            $formhtml .= Context::getContext()->smarty->fetch($tpl);
            Context::getContext()->smarty->assign(array('is_close_tab'=>0));
        }
        return $formhtml;
    }
    
    public function getConfigFieldsValues()
    {
        $id_shop_group = Shop::getContextShopGroupID();
        $id_shop = Shop::getContextShopID();
        $result = array(
            'GF_RECAPTCHA_V3' => (int)Tools::getValue('GF_RECAPTCHA_V3', (int)Configuration::get('GF_RECAPTCHA_V3', null, $id_shop_group, $id_shop)),
            'GF_RECAPTCHA' => Tools::getValue('GF_RECAPTCHA', Configuration::get('GF_RECAPTCHA', null, $id_shop_group, $id_shop)),
            'GF_SECRET_KEY' => Tools::getValue('GF_SECRET_KEY', Configuration::get('GF_SECRET_KEY', null, $id_shop_group, $id_shop)),
            'GF_BLACKLISTED_IP' => Tools::getValue('GF_BLACKLISTED_IP', Configuration::get('GF_BLACKLISTED_IP', null, $id_shop_group, $id_shop)),
            'GF_GMAP_API_KEY' => Tools::getValue('GF_GMAP_API_KEY', Configuration::get('GF_GMAP_API_KEY', null, $id_shop_group, $id_shop)),
            'GF_FRIENDLY_URL' => Tools::getValue('GF_FRIENDLY_URL', Configuration::get('GF_FRIENDLY_URL', null, $id_shop_group, $id_shop)),
            'GF_REMOVER_SHOPNAME_IN_SUBJECT' => (int)Tools::getValue('GF_REMOVER_SHOPNAME_IN_SUBJECT', Configuration::get('GF_REMOVER_SHOPNAME_IN_SUBJECT', null, $id_shop_group, $id_shop)),
            'GF_MAILCHIMP_API_KEY'=> Tools::getValue('GF_MAILCHIMP_API_KEY', Configuration::get('GF_MAILCHIMP_API_KEY', null, $id_shop_group, $id_shop)),
            'GF_KLAVIYO_API_KEY'=> Tools::getValue('GF_KLAVIYO_API_KEY', Configuration::get('GF_KLAVIYO_API_KEY', null, $id_shop_group, $id_shop)),
        );
        $result['GF_REDIRECT_TO_URL'] = array();
        $result['GF_FORM_METAKEYWORDS']= array();
        $languages = Language::getLanguages(false);
        foreach($languages as $lang){
            $result['GF_REDIRECT_TO_URL'][(int)$lang['id_lang']] = Tools::getValue('GF_REDIRECT_TO_URL_'.(int)$lang['id_lang'], Configuration::get('GF_REDIRECT_TO_URL', (int)$lang['id_lang'], $id_shop_group, $id_shop));
            $result['GF_FORM_METAKEYWORDS'][(int)$lang['id_lang']] = Tools::getValue('GF_FORM_METAKEYWORDS_'.(int)$lang['id_lang'], Configuration::get('GF_FORM_METAKEYWORDS', (int)$lang['id_lang'], $id_shop_group, $id_shop));
        }
        return $result;
    }
}
?>