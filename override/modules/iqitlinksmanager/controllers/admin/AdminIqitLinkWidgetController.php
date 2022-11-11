<?php
/**
 * 2017 IQIT-COMMERCE.COM
 *
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement
 *
 *  @author    IQIT-COMMERCE.COM <support@iqit-commerce.com>
 *  @copyright 2017 IQIT-COMMERCE.COM
 *  @license   Commercial license (You can not resell or redistribute this software.)
 *
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminIqitLinkWidgetControllerOverride extends AdminIqitLinkWidgetController
{   
    public function renderForm()
    {
        $block = new IqitLinkBlock((int) Tools::getValue('id_iqit_link_block'));

        $this->fields_form[0]['form'] = array(
            'tinymce' => true,
            'legend' => array(
                'title' => isset($block->id) ? $this->l('Edit the link block.') : $this->l('New link block'),
                'icon' => isset($block->id) ? 'icon-edit' : 'icon-plus-square',
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_iqit_link_block',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'id_shop',
                    'value' => (int) $this->context->shop->id,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Name of the link block'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Hook'),
                    'name' => 'id_hook',
                    'class' => 'input-lg',
                    'options' => array(
                        'query' => $this->getDisplayHooksForHelper(),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'repository_links',
                    'label' => $this->l('Links repository'),
                    'name' => 'repository_links',
                ),
                array(
                    'type' => 'selected_links',
                    'label' => $this->l('Selected links'),
                    'name' => 'selected_links[]',
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
                'title' => $this->l('Save'),
            ),
        );

        if ($id_hook = Tools::getValue('id_hook')) {
            $block->id_hook = (int) $id_hook;
        }

        if (Tools::getValue('name')) {
            $block->name = Tools::getValue('name');
        }

        $block->id_shop = (int) $this->context->shop->id;

        $helper = $this->buildHelper();
        if (isset($block->id)) {
            $helper->currentIndex = AdminController::$currentIndex . '&id_iqit_link_block=' . $block->id;
            $helper->submit_action = 'submitEdit' . $this->className;
        } else {
            $helper->submit_action = 'submitAdd' . $this->className;
        }

        $helper->fields_value = (array) $block;

        $helper->tpl_vars = array(
            'category_tree' => $this->repository->getCategories(),
            'cms_tree' => $this->repository->getCmsPages(),
            'static_pages' => $this->repository->getStaticPages(),
            'selected_links' => $this->presenter->makeLinks($block->content)
        );

        return $helper->generateForm($this->fields_form);
    }

    private function getDisplayHooksForHelper(){
        $usableHooks = ['displayFooter', 'displayFooterBefore', 'displayCheckoutFooter', 'displayFooterAFter', 'displayLeftColumn', 'displayRightColumn', 'displayReassurance', 'displayRightColumnProduct', 'displayNav1' , 'displayNav2', 'displayNavCenter'];

        $sql = "
            SELECT h.id_hook as id, h.name as name
                FROM `" . _DB_PREFIX_ . "hook` h
                WHERE (lower(h.`name`) LIKE 'display%')
                ORDER BY h.name ASC
        ";
        $hooks = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        foreach ($hooks as $key => $hook) {
            if (preg_match('/admin/i', $hook['name'])
                || preg_match('/backoffice/i', $hook['name'])) {
                    unset($hooks[$key]);
            } else{
                if (!in_array($hook['name'], $usableHooks)){
                    unset($hooks[$key]);
                }
            }
        }
        return $hooks;
    }
}
