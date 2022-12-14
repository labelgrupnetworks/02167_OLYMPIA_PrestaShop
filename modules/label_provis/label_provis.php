<?php
/**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Label_provis extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'label_provis';
        $this->tab = 'quick_bulk_update';
        $this->version = '1.0.0';
        $this->author = 'Labelgrup Networks S.L.';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Provis API Connector');
        $this->description = $this->l('Provis API Synchronization Module');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall API Provis Connector module?');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('LABEL_PROVIS_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('LABEL_PROVIS_LIVE_MODE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitLabel_provisModule')) == true) {
            $this->postProcess();
        }

        // $this->context->smarty->assign([
        //     'module_dir' => $this->_path,
        // ]);

        // $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitLabel_provisModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'LABEL_PROVIS_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Test url'),
                        'name' => 'LABEL_PROVIS_URL_TEST',
                        'label' => $this->l('Test url'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Production url'),
                        'name' => 'LABEL_PROVIS_URL_PRODUCTION',
                        'label' => $this->l('Production url'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Demo Installation ID'),
                        'name' => 'LABEL_PROVIS_DEMO_ID_INSTALLATION',
                        'label' => $this->l('Demo Installation ID'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Demo App Key'),
                        'name' => 'LABEL_PROVIS_DEMO_APP_KEY',
                        'label' => $this->l('Demo App Key'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Demo Secret Key'),
                        'name' => 'LABEL_PROVIS_DEMO_SECRET_KEY',
                        'label' => $this->l('Demo Secret Key'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Installation ID'),
                        'name' => 'LABEL_PROVIS_ID_INSTALLATION',
                        'label' => $this->l('Installation ID'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('App Key'),
                        'name' => 'LABEL_PROVIS_APP_KEY',
                        'label' => $this->l('App Key'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Secret Key'),
                        'name' => 'LABEL_PROVIS_SECRET_KEY',
                        'label' => $this->l('Secret Key'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'LABEL_PROVIS_LIVE_MODE' => Configuration::get('LABEL_PROVIS_LIVE_MODE', true),
            'LABEL_PROVIS_URL_TEST' => Configuration::get('LABEL_PROVIS_URL_TEST', 'https://apibasebeta.provis.es'),
            'LABEL_PROVIS_URL_PRODUCTION' => Configuration::get('LABEL_PROVIS_URL_PRODUCTION', 'https://apibase.provis.es/'),
            'LABEL_PROVIS_DEMO_ID_INSTALLATION' => Configuration::get('LABEL_PROVIS_DEMO_ID_INSTALLATION', '9009 - DEMO COMPLEJO HOTELERO OLYMPIA'),
            'LABEL_PROVIS_DEMO_APP_KEY' => Configuration::get('LABEL_PROVIS_DEMO_APP_KEY', '4BEFBB4C8DFF466C92BB610F18034D95'),
            'LABEL_PROVIS_DEMO_SECRET_KEY' => Configuration::get('LABEL_PROVIS_DEMO_SECRET_KEY', '0642D05C-04BDEA1E'),
            'LABEL_PROVIS_ID_INSTALLATION' => Configuration::get('LABEL_PROVIS_ID_INSTALLATION', '5009 - COMPLEJO HOTELERO OLYMPIA'),
            'LABEL_PROVIS_APP_KEY' => Configuration::get('LABEL_PROVIS_APP_KEY', '3D5F432CE38C49958774BAB97E5310A3'),
            'LABEL_PROVIS_SECRET_KEY' => Configuration::get('LABEL_PROVIS_SECRET_KEY', '0CB8D968-0805CE8A'),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
        Media::addJsDef(array('urlCronjobController' => $this->context->link->getModuleLink($this->name, 'cronjob')));
    }

    /**
     * Obtiene una lista de los productos disponibles.
     */
    public function getEcommerceItems($productType='all'){
        
    }

    private function getLiveMode(){
        return Configuration::get('LABEL_PROVIS_LIVE_MODE');
    }

    public function getUrl($relativeUrl = ''){
        $baseUrl = '';
        if($this->getLiveMode()) {
            $baseUrl = Configuration::get('LABEL_PROVIS_URL_PRODUCTION');
        } else {
            $baseUrl = Configuration::get('LABEL_PROVIS_URL_TEST');
        }
        return $baseUrl . $relativeUrl;
    } 

    public function getIdInstallation(){
        if($this->getLiveMode()) {
            return Configuration::get('LABEL_PROVIS_ID_INSTALLATION');
        } else {
            return Configuration::get('LABEL_PROVIS_DEMO_ID_INSTALLATION');
        }
    } 

    public function getAppKey(){
        if($this->getLiveMode()) {
            return Configuration::get('LABEL_PROVIS_APP_KEY');
        } else {
            return Configuration::get('LABEL_PROVIS_DEMO_APP_KEY');
        }
    } 

    public function getSecretKey(){
        if($this->getLiveMode()) {
            return Configuration::get('LABEL_PROVIS_SECRET_KEY');
        } else {
            return Configuration::get('LABEL_PROVIS_DEMO_SECRET_KEY');
        }
    } 

    /**
     * Authorization:hmac-256 9900:appkey:Firma:Nonce:UnixTimeStamp' \
     */
    public function getAuth($url, $method = 'GET', $parameters = [], $nonce = null, $separator = ':'){
        // Configuramos los parámetros
        $uri = strtolower(urlencode($url));
        $md5 = '';
        if($method == 'POST'){
            $md5 = base64_encode(md5(json_encode($parameters), true));
        }
        $dateTime = new \DateTime('now');
        $timestamp = $dateTime->format('U');
        $idInstallation= $this->getIdInstallation();
        $appKey = $this->getAppKey();
        $secretKey = $this->getSecretKey();
        if($nonce == null){
            $nonce = uniqid();
        }
        // Formamos la cadena de firma
        $stringToHash = $appKey.$method.$uri.$timestamp.$nonce.$md5;
        // Codificamos la cadena de firma
        $firma = base64_encode(hash_hmac('sha256', $stringToHash, $secretKey, true));
        dump('url: '.$url);
        dump('uri: '.$uri);
        dump('timestamp: '.$timestamp);
        dump('nonce: '.$nonce);
        dump('jsonencode: '.json_encode($parameters));
        dump('md5: '.$md5);
        dump('encode: '.$stringToHash);
        dump('signature: '.$firma);
        dump('Authorization: hmac-256 '.$idInstallation.$separator.$appKey.$separator.$firma.$separator.$nonce.$separator.$timestamp);

        // Devolvemos la firma encriptada
        return 'Authorization: hmac-256 '.$idInstallation.$separator.$appKey.$separator.$firma.$separator.$nonce.$separator.$timestamp;
    }

    public function request($action, $parameters){
        switch ($action) {
            case 'ecommerce_list':
                $relativeUrl = 'api/ecommerce/items';
                $method = 'GET';

                // Configuramos la url
                $url = $this->getUrl().$relativeUrl.'?'.http_build_query($parameters);

                // Configuramos los parámetros y ejecutamos la llamada
                $request = [
                    'URL' => $url,
                    'METHOD' => $method,
                    'AUTHORIZATION' => $this->getAuth($url, $method, $parameters)
                ];
                $response = $this->call($request);
                break;
            case 'customer_verification':
                $relativeUrl = 'api/ecommerce/customers/verification';
                $method = 'POST';

                // Configuramos la url
                $url = $this->getUrl().$relativeUrl;

                // Configuramos los parámetros y ejecutamos la llamada
                $request = [
                    'URL' => $url,
                    'METHOD' => $method,
                    'AUTHORIZATION' => $this->getAuth($url, $method, $parameters)
                ];
                dump($request);
                $response = $this->call($request);

            default:
                break;
        }

        return $response;
    }

    public function call($request){
        try{
            dump($request);
            $curl = curl_init();
            $curl_ops = array(
                CURLOPT_URL => $request['URL'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $request['METHOD'],
                CURLOPT_POSTFIELDS => '',
                CURLOPT_HTTPHEADER => array(
                  'Content-Type: application/json', 
                  'Cache-Control: no-cache',
                  $request['AUTHORIZATION']
                ),
            );
            dump($curl_ops);
            curl_setopt_array(
                $curl,
                $curl_ops
            );

            $response = curl_exec($curl);
            $err = curl_error($curl);
            // $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            // dump('CURLINFO_HTTP_CODE: '.$httpcode);
            curl_close($curl);
            dump($response);
            return json_decode($response);

        } catch(Exception $ex){
            dump('Exception.');
            dump($ex);
        }
    }

    public function hookActionCustomerAccountAdd($params){
        dump($params['newCustomer']);
        $parameters = [ 
            "IDPersona"=> -1, 
            "IDInstalacion"=> $this->getIdInstallation(), 
            "IDTipoDeCliente"=> 1, 
            "Nif"=> "", 
            "Sexo"=> "", 
            "Email"=> $params['newCustomer']->email, 
            "Nombre"=> $params['newCustomer']->firstname, 
            "Apellidos"=> $params['newCustomer']->lastname, 
            "Nick"=> "", 
            "Movil"=> "", 
            "Telefono"=> "",
            "FechaDeNacimiento"=> $params['newCustomer']->birthday, 
            "CP"=> "", 
            "Direccion"=> "", 
            "Localidad"=> "", 
            "IDProvincia"=> 5, 
            "CodigoPais"=> 1, 
            "CodBanco"=> "", 
            "CodSucursal"=> "", 
            "Dc"=> "11", 
            "NumeroDeCuenta"=>"", 
            "PermitirNotificacionesComercialesLOPD"=> true, 
            "PermitirCompartirEnRedesSociales"=> false, 
            "PermitirCompartirDatosATerceros"=> false 
        ];
        dump($parameters);
        $response = $this->request('customer_verification', $parameters);
        dump($response);
        die();
    }
}
