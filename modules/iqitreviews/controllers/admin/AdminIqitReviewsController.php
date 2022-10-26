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

class AdminIqitReviewsController extends ModuleAdminController
{
    public $name;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'IqitProductReview';
        $this->table = 'iqitreviews_products';
        $this->identifier = 'id_iqitreviews_products';

        $this->_defaultOrderBy = 'date_add';
        $this->_defaultOrderWay = 'DESC';
        $this->list_no_link = true;
        $this->_pagination = array(10, 15, 100, 300, 1000);



        $this->addRowAction('delete');

        parent::__construct();

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }

        $this->bulk_actions = array(
            'approve' => array(
                'text' => $this->trans('Approve', [], 'Modules.Iqitreviews.Adminiqitreviews'),
                'confirm' => $this->trans('Approve selected items?', [], 'Modules.Iqitreviews.Adminiqitreviews'),
                'icon' => 'icon-power-off text-success'
            ),
            'disapprove' => array(
                'text' => $this->trans('Dispprove', [], 'Modules.Iqitreviews.Adminiqitreviews'),
                'confirm' => $this->trans('Disapprove selected items?', [], 'Modules.Iqitreviews.Adminiqitreviews'),
                'icon' => 'icon-power-off text-danger'
            ),
            'divider' => array(
                'text' => 'divider'
            ),
            'delete' => array(
                'text' => $this->trans('Delete selected', [], 'Modules.Iqitreviews.Adminiqitreviews'),
                'confirm' => $this->trans('Delete selected items?', [], 'Modules.Iqitreviews.Adminiqitreviews'),
                'icon' => 'icon-trash'
            ),
        );

        $this->name = 'IqitReviews';

        $this->fields_list = array(
            'id_iqitreviews_products' => array(
                'title' => $this->trans('ID', [], 'Modules.Iqitreviews.Adminiqitreviews'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'id_product' => array(
                'title' => $this->trans('Product', [], 'Modules.Iqitreviews.Adminiqitreviews'),
                'align' => 'center',
                'callback' => 'formatProduct',
                'class' => 'fixed-width-xs'
            ),
            'title' => array(
                'title' => $this->trans('Title', [], 'Modules.Iqitreviews.Adminiqitreviews'),
            ),
            'rating' => array(
                'title' => $this->trans('Rating', [], 'Modules.Iqitreviews.Adminiqitreviews'),
                'class' => 'fixed-width-xs',
                'callback' => 'formatRating',
                'align' => 'center',
            ),
            'comment' => array(
                'title' => $this->trans('Comment', [], 'Modules.Iqitreviews.Adminiqitreviews'),
                'callback' => 'getCommentClean',
                'orderby' => false
            ),
            'customer_name' => array(
                'title' => $this->trans('Author', [], 'Modules.Iqitreviews.Adminiqitreviews'),
            ),
            'date_add' => array(
                'title' => $this->trans('Date', [], 'Modules.Iqitreviews.Adminiqitreviews'),
                'type' => 'date'
            ),
            'status' => array(
                'title' => $this->trans('Published', [], 'Modules.Iqitreviews.Adminiqitreviews'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a!status',
                'class' => 'fixed-width-sm'
            )
        );

        $this->fields_options = array(
            'general' => array(
                'title' => $this->trans('General', [], 'Modules.Iqitreviews.Adminiqitreviews'),
                'icon' => 'icon-cogs',
                'fields' => array(
                    $this->module->cfgName . 'guest' => array(
                        'title' => $this->trans('Allow guest reviews', [], 'Modules.Iqitreviews.Adminiqitreviews'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    $this->module->cfgName . 'autopublish' => array(
                        'title' => $this->trans('Autopublish comments', [], 'Modules.Iqitreviews.Adminiqitreviews'),
                        'hint' => $this->trans('If disabled you will have to approve comments manually ', [], 'Modules.Iqitreviews.Adminiqitreviews'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', [], 'Modules.Iqitreviews.Adminiqitreviews'))
            ),
        );
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->trans('Themes', [], 'Modules.Iqitreviews.Adminiqitreviews');
        $this->toolbar_title[] = $this->trans('Product reviews', [], 'Modules.Iqitreviews.Adminiqitreviews');
    }


    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public static function getCommentClean($comment)
    {
        return Tools::getDescriptionClean($comment);
    }

    public static function formatRating($rating)
    {
        return $rating . '/5';
    }

    public static function formatProduct($idProduct)
    {
        $product = new Product((int)$idProduct, false, (int)Context::getContext()->language->id);
        return '<a href="' . Context::getContext()->link->getAdminLink('AdminProducts') . '&id_product=' . (int)$idProduct . '">' . $product->name . ' (id: ' . $idProduct . ')</a>';
    }


    public function postProcess()
    {
        if (Tools::isSubmit('submitBulkapprove'.$this->table)) {
            $this->processBulkApprove();
        } elseif (Tools::isSubmit('submitBulkdisapprove'.$this->table)) {
            $this->processBulkDisapprove();
        }

        parent::postProcess();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/admin.js');
        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/backoffice.css');
    }

    public function ajaxProcessStatusProductReview()
    {
        header('Content-type: application/json');
        if (!$idReview = (int)Tools::getValue('id_iqitreviews_products')) {
            die(json_encode(array('success' => false, 'error' => true, 'text' => $this->trans('Failed to update the status', [], 'Modules.Iqitreviews.Adminiqitreviews'))));
        } else {
            $review = new IqitProductReview((int)$idReview);
            if (Validate::isLoadedObject($review)) {
                $this->module->clearCache($review->id_product);
                $review->status = $review->status == 1 ? 0 : 1;
                $review->save() ?
                    die(json_encode(array('success' => true, 'text' => $this->trans('The status has been updated successfully', [], 'Modules.Iqitreviews.Adminiqitreviews')))) :
                    die(json_encode(array('success' => false, 'error' => true, 'text' => $this->trans('Failed to update the status', [], 'Modules.Iqitreviews.Adminiqitreviews'))));
            }
        }
    }

    protected function processBulkApprove()
    {
        return $this->processBulkApproveSelection(1);
    }

    protected function processBulkDisapprove()
    {
        return $this->processBulkApproveSelection(0);
    }

    protected function processBulkApproveSelection($status)
    {
        $result = true;
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id) {
                /** @var ObjectModel $object */
                $object = new $this->className((int)$id);
                $this->module->clearCache($object->id_product);
                $object->status = (int)$status;
                $result &= $object->update();
            }
        }
        return $result;
    }
}
