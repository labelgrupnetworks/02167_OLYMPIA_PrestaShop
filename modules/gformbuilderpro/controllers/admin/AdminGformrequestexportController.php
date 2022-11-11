<?php
/**
* The file is controller. Do not modify the file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright  2017 Globo., Jsc
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/

include_once(_PS_MODULE_DIR_ . 'gformbuilderpro/classes/gformbuilderproModel.php');
include_once(_PS_MODULE_DIR_ . 'gformbuilderpro/classes/gformrequestModel.php');
class AdminGformrequestexportController extends ModuleAdminController
{
    public function __construct()
	{
		$this->bootstrap = true;
		$this->display = 'edit';
        parent::__construct();
		$this->meta_title = $this->l('Advanced Form Builder');
	}
    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('Advanced Form Builder');
        $this->toolbar_title[] = $this->l('Request Export');
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
        if(version_compare(_PS_VERSION_,'1.5.4') == -1){
            return true;
        }else return parent::initTabModuleList();
    }
    public function initPageHeaderToolbar()
	{
        if(version_compare(_PS_VERSION_,'1.6') == 1){
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
		      parent::initPageHeaderToolbar();
        }else return true;
	}
    public function renderForm(){
        $forms = gformbuilderproModel::getAllBlock();
        $shops = Shop::getShops(true);
        $formsoption = array();
        $shopsoption = array();
       if($forms)
        foreach($forms as $form){
            $formsoption[] = array(
    				'value' => $form['id_gformbuilderpro'],
    				'name' => $form['title']
    			);
        }
        if($shops)
        foreach($shops as $shop){
            $shopsoption[] = array(
    				'value' => $shop['id_shop'],
    				'name' => $shop['name']
    			);
        }
        $allStatus = array(
            array('value' => 0,'name' => $this->l('Submitted')),
            array('value' => 1,'name' => $this->l('Pending')),
            array('value' => 2,'name' => $this->l('Closed')),
        );
        $this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Export Csv'),
				'icon' => 'icon-export'
			),
			'input' => array(
				array(
					'type' => (version_compare(_PS_VERSION_,'1.6') == -1) ? 'date' : 'datetime',
					'label' => $this->l('From'),
					'name' => 'gfromexportfrom',
                    'size'=>255
				),
                array(
					'type' => (version_compare(_PS_VERSION_,'1.6') == -1) ? 'date' : 'datetime',
					'label' => $this->l('To'),
					'name' => 'gfromexportto',
                    'size'=>255
				),
                array(
					'type' => 'select',
					'label' => $this->l('Form'),
					'name' => 'gfromexport',
					'options' => array(
						'query' => $formsoption,
						'id' => 'value',
						'name' => 'name'
					)
                ),
                array(
					'type' => 'select',
					'label' => $this->l('Shop'),
					'name' => 'gfromexportshop',
					'options' => array(
						'query' => $shopsoption,
						'id' => 'value',
						'name' => 'name'
					)
                ),
                array(
					'type' => 'checkbox',
					'label' => $this->l('Request Status'),
					'name' => 'status',
					'values' => array(
						'query' => $allStatus,
						'id' => 'value',
						'name' => 'name'
					)
                ),
			),
			'submit' => array(
				'title' => $this->l('Export'),
				'id' => 'submitExport',
				'icon' => 'process-icon-export',
                'name'=>'submitExport'
			)
		);
		$this->show_toolbar = false;
		$this->show_form_cancel_button = false;
		$this->toolbar_title = $this->l('Export');
		return parent::renderForm();
    }
    public function postProcess()
	{
        if (Tools::isSubmit('submitExport'))
        {
            $allStatus = array(
                array('value' => 0,'name' => $this->l('Submitted')),
                array('value' => 1,'name' => $this->l('Pending')),
                array('value' => 2,'name' => $this->l('Closed')),
            );
            $allStatus_submit = array();
            foreach($allStatus as $status){
                $status_select = Tools::getValue('status_'.$status['value']);
                if($status_select == 'on' || $status_select == 1) $allStatus_submit[$status['value']] = (int)$status['value']; 
            }
            
            if(count($allStatus_submit) < 1){
                $this->errors[] = $this->l('Please select status.');
            }else{
                $filename = time().'.csv';
                
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename='.$filename);
                $gfromexport = (int)Tools::getValue('gfromexport');
                $gfromexportshop = (int)Tools::getValue('gfromexportshop');
                $gfromexportfrom = Tools::getValue('gfromexportfrom');
                $gfromexportto = Tools::getValue('gfromexportto');
                $id_lang = (int)Context::getContext()->language->id;
                $results = gformrequestModel::getExportData($gfromexport,$gfromexportshop,$gfromexportfrom,$gfromexportto,$allStatus_submit);
                $fp = fopen('php://output','w');
                $formObj = new gformbuilderproModel((int)$gfromexport,(int)$id_lang,(int)$gfromexportshop);
                $fields = $formObj->fields;
                $fieldsData = gformbuilderprofieldsModel::getAllFields($fields,(int)$id_lang,(int)$gfromexportshop);
                $titles = array();
                $titles['gid'] = $this->l('Id');
                if($fieldsData){
                    foreach($fieldsData as $fieldData){
                        if($fieldData['type'] !='captcha' && $fieldData['type'] !='html' && $fieldData['type'] !='html' )
                            $titles[$fieldData['name']]=$fieldData['label'];
                    }
                }
                $titles['gdate_add'] = $this->l('Date Add');
                fputcsv($fp, $titles);
                if($results){
                    foreach($results as $result){
                        $row = array();
                        $row['gid'] = $result['id_gformrequest'];
                        $data = Tools::jsonDecode($result['jsonrequest'],true);
                        
                        
                
                        if($fieldsData){
                            foreach($fieldsData as $fieldData){
                                if($fieldData['type'] !='captcha' && $fieldData['type'] !='html' && $fieldData['type'] !='html' ){
                                    $key = $fieldData['name'];
                                    if(isset($data['{'.$key.'}'])){
                                        if($fieldData['type'] == 'htmlinput')
                                            $row[$key] = str_replace("\"","'",$data['{'.$key.'}']);
                                        else{
                                            if(is_array($data['{'.$key.'}'])) $row[$key] = implode(',',$data['{'.$key.'}']);
                                            else $row[$key] = $data['{'.$key.'}'];
                                        }
                                    }
                                    else $row[$key]='';
                                }
                            }
                        }
                        $row['gdate_add'] = $result['date_add'];
                        fputcsv($fp, $row);
                    }
                }
                exit();
            }
        }
        parent::postProcess();
    }
}
?>