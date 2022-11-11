<?php
/**
 * @author    Musaffar Patel <musaffar.pate@gmail.com>
 * @copyright 2016-2021 Musaffar Patel
 * @license   Commercial Single License
 */

$module_folder = 'loyaltyrewardpoints';

/* Library */
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/lib/classes/LRPAjaxResponse.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/lib/classes/LRPControllerCore.php');

/* Helpers */
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/helpers/LRPCartHelper.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/helpers/LRPCartRuleHelper.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/helpers/LRPCustomerHelper.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/helpers/LRPConfigHelper.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/helpers/LRPDiscountHelper.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/helpers/LRPInsightsHelper.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/helpers/LRPReferralHelper.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/helpers/LRPHistoryHelper.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/helpers/LRPOrderHelper.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/helpers/LRPProductHelper.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/helpers/LRPUtilityHelper.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/helpers/LRPTranslationHelper.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/helpers/LRPVoucherHelper.php');

/* Models */
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/models/LRPInstall.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/models/LRPConfigModel.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/models/LRPCustomerModel.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/models/LRPHistoryModel.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/models/LRPMailLogModel.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/models/LRPRuleModel.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/models/LRPCartModel.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/models/LRPReferralCookieModel.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/models/LRPReferralClickModel.php');

/* Controllers */
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/controllers/cron/LRPCronController.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/controllers/widget/MPProductSearchWidgetController.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/controllers/admin/config/LRPAdminConfigMainController.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/controllers/admin/config/LRPAdminConfigGroupsController.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/controllers/admin/config/LRPAdminConfigInsightsController.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/controllers/admin/config/LRPAdminConfigGeneralController.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/controllers/admin/config/LRPAdminConfigRulesController.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/controllers/admin/config/migration/LRPAdminConfigMigrationController.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/controllers/admin/config/migration/LRPAdminConfigMigrationPSLoyaltyController.php');

include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/controllers/admin/order/LRPAdminOrderController.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/controllers/admin/customer/LRPAdminCustomerController.php');

include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/controllers/front/LRPFrontCheckoutController.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/controllers/front/LRPFrontCustomerController.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/controllers/front/LRPFrontProductController.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/controllers/front/customeraccount.php');

/* Migration */
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/migration/LRPMigrationCore.php');
include_once(_PS_MODULE_DIR_ . '/' . $module_folder . '/migration/LRPMigrationPSLoyalty.php');