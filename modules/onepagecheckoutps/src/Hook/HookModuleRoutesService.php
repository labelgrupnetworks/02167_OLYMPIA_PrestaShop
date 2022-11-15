<?php
/**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * @author    PresTeamShop SAS (Registered Trademark) <info@presteamshop.com>
 * @copyright 2011-2022 PresTeamShop SAS, All rights reserved.
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @category  PrestaShop
 * @category  Module
 */

namespace OnePageCheckoutPS\Hook;

use OnePageCheckoutPS\Application\Core\CoreService;

class HookModuleRoutesService extends AbstractHook
{
    private $coreService;

    public function __construct(CoreService $coreService)
    {
        $this->coreService = $coreService;
    }

    protected function executeRun()
    {
        return array(
            'myaccount_customer_exists_by_email' => array(
                'controller' => 'myaccount',
                'rule' => 'checkout/myaccount/customerExists',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'onepagecheckoutps',
                    'controller' => 'myaccount',
                    'action' => 'customerExists',
                ),
            ),
            'myaccount_login' => array(
                'controller' => 'myaccount',
                'rule' => 'checkout/myaccount/loginCustomer',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'onepagecheckoutps',
                    'controller' => 'myaccount',
                    'action' => 'loginCustomer',
                ),
            ),
            'myaccount_login_social' => array(
                'controller' => 'myaccount',
                'rule' => 'checkout/myaccount/loginSocialCustomer',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'onepagecheckoutps',
                    'controller' => 'myaccount',
                    'action' => 'loginSocialCustomer',
                    'noajax' => true,
                    'ajax' => true, //evita problema con la redireccion canonical del FrontController,
                ),
            ),
            'myaccount_save' => array(
                'controller' => 'myaccount',
                'rule' => 'checkout/myaccount/saveCustomer',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'onepagecheckoutps',
                    'controller' => 'myaccount',
                    'action' => 'saveCustomer',
                ),
            ),
            'myaccount_convert_guest_to_customer' => array(
                'controller' => 'myaccount',
                'rule' => 'checkout/myaccount/convertGuestToCustomerUrl',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'onepagecheckoutps',
                    'controller' => 'myaccount',
                    'action' => 'convertGuestToCustomer',
                ),
            ),
            'addresses_save' => array(
                'controller' => 'addresses',
                'rule' => 'checkout/addresses/save',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'onepagecheckoutps',
                    'controller' => 'addresses',
                    'action' => 'save',
                ),
            ),
            'addresses_delete' => array(
                'controller' => 'addresses',
                'rule' => 'checkout/addresses/delete',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'onepagecheckoutps',
                    'controller' => 'addresses',
                    'action' => 'delete',
                ),
            ),
            'addresses_list' => array(
                'controller' => 'addresses',
                'rule' => 'checkout/addresses/list',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'onepagecheckoutps',
                    'controller' => 'addresses',
                    'action' => 'list',
                ),
            ),
            'addresses_valid_unique_dni' => array(
                'controller' => 'addresses',
                'rule' => 'checkout/addresses/validateUniqueDni',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'onepagecheckoutps',
                    'controller' => 'addresses',
                    'action' => 'validateUniqueDni',
                ),
            ),
            'shipping_list' => array(
                'controller' => 'shipping',
                'rule' => 'checkout/shipping/list',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'onepagecheckoutps',
                    'controller' => 'shipping',
                    'action' => 'getCarrierList',
                ),
            ),
            'shipping_update' => array(
                'controller' => 'shipping',
                'rule' => 'checkout/shipping/update',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'onepagecheckoutps',
                    'controller' => 'shipping',
                    'action' => 'update',
                ),
            ),
            'payment_list' => array(
                'controller' => 'payment',
                'rule' => 'checkout/payment/list',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'onepagecheckoutps',
                    'controller' => 'payment',
                    'action' => 'getPaymentList',
                ),
            ),
            'cart_summary' => array(
                'controller' => 'cart',
                'rule' => 'checkout/cart',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'onepagecheckoutps',
                    'controller' => 'cart',
                    'action' => 'getCartSummary',
                ),
            ),
            'cart_update_address' => array(
                'controller' => 'cart',
                'rule' => 'checkout/cart/updateAddress',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'onepagecheckoutps',
                    'controller' => 'cart',
                    'action' => 'updateAddress',
                ),
            ),
        );
    }
}
