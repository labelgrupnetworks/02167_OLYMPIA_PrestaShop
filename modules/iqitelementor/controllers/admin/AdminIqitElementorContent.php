<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminIqitElementorContentController extends ModuleAdminController
{
    public $name;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'IqitElementorContent';
        $this->table = 'iqit_elementor_content';

        $hookId = Hook::getIdByName('displayManufacturerElementor');

        $this->_where = 'AND a.`hook` != '.(int) $hookId;

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        parent::__construct();

        $this->_orderBy = 'id_elementor';
        $this->identifier = 'id_elementor';
        $test = array();
        $test[0] = array(
            'id' => 0,
            'name' => $this->l('No results were found for your search.')
        );


        $this->fields_list = array(
            'id_elementor' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'title' => array('title' => $this->l('Name'), 'width' => 'auto'),
            'hook' => array('title' => $this->l('Hook'), 'width' => 'auto', 'callback' => 'formatHook'),
            'active' => array('title' => $this->l('Active'), 'width' => 'auto', 'align' => 'center', 'type' => 'bool', 'class' => 'fixed-width-xs'),
        );

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
        $this->name = 'IqitElementorContent';
    }


    public static function formatHook($idHook)
    {
        return Hook::getNameById($idHook);
    }

    public function init()
    {

        if (Tools::isSubmit('edit' . $this->className)) {
            $this->display = 'edit';
        } elseif (Tools::isSubmit('addiqit_elementor_landing')) {
            $this->display = 'add';
        }


        parent::init();
    }



    public function initContent()
    {
        if (!$this->viewAccess()) {
            $this->errors[] = Tools::displayError('You do not have permission to view this.');
            return;
        }

        if (Shop::getContext() == Shop::CONTEXT_GROUP || Shop::getContext() == Shop::CONTEXT_ALL) {
            $this->context->smarty->assign(array(
                'content' => $this->getWarningMultishopHtml()
            ));
            return;
        }

        parent::initContent();
    }



    public function postProcess()
    {
        if (Tools::isSubmit('submit' . $this->className)) {
            if(Tools::getValue(   'submitEdit' . $this->className)){
                if (Validate::isLoadedObject($object = $this->loadObject())) {
                    $this->module->clearHookCache($object->hook);
                }
            }
            $returnObject = $this->processSave();
            if (!$returnObject) {
                return false;
            }
            $idHook = (int) Tools::getValue('hook');
            $hook_name = Hook::getNameById($idHook);
            if (!Hook::isModuleRegisteredOnHook($this->module, $hook_name, $this->context->shop->id)) {
                Hook::registerHook($this->module, $hook_name);
            }

            $this->module->clearHookCache($idHook);

            Tools::redirectAdmin($this->context->link->getAdminLink('Admin'.$this->name) . '&id_elementor='.$returnObject->id .'&updateiqit_elementor_content');
        }

        
        return parent::postProcess();
    }

    public function processDelete(){


        if (Validate::isLoadedObject($object = $this->loadObject())) {

            if (IqitElementorContent::getCountByIdHook((int) $object->hook) <= 1) {
                Hook::unregisterHook($this->module, Hook::getNameById((int) $object->hook));
            }
            $this->module->clearHookCache($object->hook);
        }
        return parent::processDelete();

    }


    public function renderForm()
    {

        $landing = new IqitElementorContent((int) Tools::getValue('id_elementor'));

        if ($landing->id){
            $url = $this->context->link->getAdminLink('IqitElementorEditor').'&pageType=content&pageId=' . $landing->id;
        }
        else{
            $url = false;
            $landing->active = 1;
        }

        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => isset($landing->id) ? $this->l('Edit layout.') : $this->l('New layout'),
                'icon' => isset($landing->id) ? 'icon-edit' : 'icon-plus-square',
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_elementor',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Title of layout'),
                    'name' => 'title',
                    'required' => true,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Hook'),
                    'name' => 'hook',
                    'options' => array(
                        'query' => IqitElementorContent::getSelectableHooks(),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Status'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('Disabled'),
                        ),
                    ),
                ),
                array(
                    'type' => 'elementor_trigger',
                    'label' => $this->l('Title of layout'),
                    'url'  => $url,
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'id_shop',
                ),
            ),
            'buttons' => array(
                'cancelBlock' => array(
                    'title' => $this->l('Cancel'),
                    'href' => (Tools::safeOutput(Tools::getValue('back', false)))
                        ?: $this->context->link->getAdminLink('Admin' . $this->name),
                    'icon' => 'process-icon-cancel',
                ),
            ),
            'submit' => array(
                'name' => 'submit' . $this->className,
                'title' => $this->l('Save and stay'),
            ),
        );


        if (Tools::getValue('title')) {
            $landing->title = Tools::getValue('title');
        }

        $helper = $this->buildHelper();
        if (isset($landing->id)) {
            $helper->currentIndex = AdminController::$currentIndex . '&id_elementor=' . $landing->id;
            $helper->submit_action = 'submitEdit' . $this->className;
        } else {
            $helper->submit_action = 'submitAdd' . $this->className;
        }

        $helper->fields_value = (array) $landing;
        $helper->fields_value['id_shop'] = $this->context->shop->id;
        if ($landing->id) {
            $helper->fields_value['id_elementor'] = $landing->id;
        }
        return $helper->generateForm($this->fields_form);
    }

    protected function buildHelper()
    {
        $helper = new HelperForm();

        $helper->module = $this->module;
        $helper->override_folder = 'iqitelementor/';
        $helper->identifier = $this->className;
        $helper->token = Tools::getAdminTokenLite('Admin' . $this->name);
        $helper->languages = $this->_languages;
        $helper->currentIndex = $this->context->link->getAdminLink('Admin' . $this->name);
        $helper->default_form_language = $this->default_form_language;
        $helper->allow_employee_form_lang = $this->allow_employee_form_lang;
        $helper->toolbar_scroll = true;
        $helper->toolbar_btn = $this->initToolbar();

        return $helper;
    }


    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('Content on hooks');
    }

    protected function getWarningMultishopHtml()
    {
        if (Shop::getContext() == Shop::CONTEXT_GROUP || Shop::getContext() == Shop::CONTEXT_ALL) {
            return '<p class="alert alert-warning">' .
            $this->l('You cannot manage module from a "All Shops" or a "Group Shop" context, select directly the shop you want to edit') .
            '</p>';
        } else {
            return '';
        }
    }
}
