<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Musaffar Patel
 * @copyright 2016-2017 Musaffar Patel
 * @license   LICENSE.txt
 */

use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use Defuse\Crypto\Key;
use phpseclib\Crypt\Rijndael;

class LRPCronController extends LRPControllerCore
{
    /** @var array module instance */
    protected $sibling;

    /** @var array cipher tool instance */
    protected $cipherTool;

    public function __construct(&$sibling = null)
    {
        parent::__construct($sibling);
        if ($sibling !== null) {
            $this->sibling = &$sibling;
        }

        $asciiSafeString = \Defuse\Crypto\Encoding::saveBytesToChecksummedAsciiSafeString(Key::KEY_CURRENT_VERSION, str_pad('reminderemail', Key::KEY_BYTE_SIZE, __FILE__));
        $this->cipherTool = new PhpEncryption($asciiSafeString);
    }

    /**
     * Send email to birthday customer and reward them with points
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    protected function getBirthdayCustomers()
    {
        $context = Context::getContext();
        $customers = LRPCustomerHelper::getCustomersByBirthDate(date('Y-m-d'));
        $id_lang = (int)$context->language->id;
        $id_shop = (int)$context->shop->id;
        $currency = Currency::getDefaultCurrency();

        $configuration = Configuration::getMultiple(
            array('PS_SHOP_EMAIL',
                'PS_MAIL_METHOD',
                'PS_MAIL_SERVER',
                'PS_MAIL_USER',
                'PS_MAIL_PASSWD',
                'PS_SHOP_NAME',
                'PS_MAIL_COLOR',
            ),
            $id_lang,
            null,
            $id_shop
        );

        $priceFormatter = new PriceFormatter();

        foreach ($customers as $customer) {
            $language = new Language($customer['id_lang']);

            $dir_mail = false;
            if (file_exists(dirname($this->sibling->module_file) . '/mails/' . $language->iso_code . '/birthday_points.txt') &&
                file_exists(dirname($this->sibling->module_file) . '/mails/' . $language->iso_code . '/birthday_points.html')) {
                $dir_mail = dirname($this->sibling->module_file) . '/mails/';
            }

            if (file_exists(_PS_MAIL_DIR_ . $language->iso_code . '/birthday_points.txt') &&
                file_exists(_PS_MAIL_DIR_ . $language->iso_code . '/birthday_points.html')) {
                $dir_mail = _PS_MAIL_DIR_;
            }

            $lrp_config_model = new LRPConfigModel(0, $customer['id_default_group'], $customer['id_shop']);

            $reward_points = $lrp_config_model->getBirthdayPoints();
            $reward_points_value = LRPDiscountHelper::getPointsMoneyValue($reward_points, $lrp_config_model->getPointValue(), $currency->id);
            $reward_points_value = $priceFormatter->convertAndFormat($reward_points_value);

            $has_been_rewarded = LRPHistoryHelper::hasBirthdayPoints($customer['id_customer'], date('Y'));

            // Filling-in vars for email
            $template_vars = array(
                '{firstname}' => $customer['firstname'],
                '{lastname}' => $customer['lastname'],
                '{email}' => $customer['email'],
                '{shop_name}' => $configuration['PS_SHOP_NAME'],
                '{points}' => $reward_points,
                '{points_value}' => $reward_points_value,
            );

            if (!$has_been_rewarded && $dir_mail && $reward_points > 0) {
                LRPHistoryHelper::rewardPoints(0, $customer['id_customer'], $reward_points, 'birthday', $currency);
                Mail::Send(
                    $customer['id_lang'],
                    'birthday_points',
                    sprintf(Mail::l('Happy Birthday : %s', $customer['id_lang']), $customer['firstname']),
                    $template_vars,
                    $customer['email'],
                    null,
                    $configuration['PS_SHOP_EMAIL'],
                    $configuration['PS_SHOP_NAME'],
                    null,
                    null,
                    $dir_mail,
                    null,
                    $id_shop
                );
            }
        }
    }

    /**
     * send reminder emails
     */
    protected function sendReminderEmails()
    {
        $context = Context::getContext();
        $id_lang = (int)$context->language->id;
        $id_shop = (int)$context->shop->id;
        $languages = Language::getLanguages();
        $configuration = Configuration::getMultiple(
            array(
                'PS_SHOP_EMAIL',
                'PS_MAIL_METHOD',
                'PS_MAIL_SERVER',
                'PS_MAIL_USER',
                'PS_MAIL_PASSWD',
                'PS_SHOP_NAME',
                'PS_MAIL_COLOR',
            ),
            $id_lang,
            null,
            $id_shop
        );

        $lrp_config_global = new LRPConfigModel(0, 0, $id_shop);

        if ($lrp_config_global->getSendPointReminderEmails() == 0) {
            return false;
        }

        $days_index = array(1, 2, 3);

        foreach ($days_index as $day) {
            $email_subjects = [];
            foreach ($languages as $language) {
                switch ($day) {
                    case 1:
                        $days = $lrp_config_global->getPointsReminderEmailTriggerDays1();
                        $email_subjects[$language['id_lang']] = $lrp_config_global->getPointsReminderEmailSubject1($language['id_lang']);
                        break;

                    case 2:
                        $days = $lrp_config_global->getPointsReminderEmailTriggerDays2();
                        $email_subjects[$language['id_lang']] = $lrp_config_global->getPointsReminderEmailSubject2($language['id_lang']);
                        break;

                    case 3:
                        $days = $lrp_config_global->getPointsReminderEmailTriggerDays3();
                        $email_subjects[$language['id_lang']] = $lrp_config_global->getPointsReminderEmailSubject3($language['id_lang']);
                        break;
                }
            }

            $day_triggers[$day] = array(
                'days' => $days,
                'email_subjects' => $email_subjects
            );
        }

        $emails_sent = array();

        foreach ($day_triggers as $id_mail => $day_trigger) {
            if ((int)$day_trigger['days'] == 0) {
                continue;
            }

            $orders = LRPOrderHelper::getOrdersBetweenDayTriggers($id_mail, $day_trigger['days']);

            foreach ($orders as $order) {
                if (in_array($order['email'], $emails_sent)) {
                    continue;
                }

                $language = new Language($order['id_lang']);
                $dir_mail = false;
                $template_name = 'points_reminder_' . $id_mail;

                if (file_exists(dirname($this->sibling->module_file) . '/mails/' . $language->iso_code . "/$template_name.txt") &&
                    file_exists(dirname($this->sibling->module_file) . '/mails/' . $language->iso_code . "/$template_name.html")) {
                    $dir_mail = dirname($this->sibling->module_file) . '/mails/';
                }

                if (file_exists(_PS_MAIL_DIR_ . $language->iso_code . "/$template_name.txt") &&
                    file_exists(_PS_MAIL_DIR_ . $language->iso_code . "/$template_name.html")) {
                    $dir_mail = _PS_MAIL_DIR_;
                }

                $email = $order['email'];
                $email_encrypted = urlencode($this->cipherTool->encrypt($email));
                $unsubscribe_link = Context::getContext()->link->getModuleLink('loyaltyrewardpoints', 'unsubscribe', array('sc' => $email_encrypted, 'i' => $order['id_customer']));

                $points_available = LRPCustomerHelper::getTotalPointsAvailable($order['id_customer'], null);

                if ($points_available == 0) {
                    continue;
                }

                $template_vars = array(
                    '{firstname}' => $order['firstname'],
                    '{lastname}' => $order['lastname'],
                    '{email}' => $order['email'],
                    '{shop_name}' => $configuration['PS_SHOP_NAME'],
                    '{points_available}' => $points_available,
                    '{unsubscribe_link}' => $unsubscribe_link,
                    '{referral_link}' => LRPReferralHelper::getCustomerReferralLink($order['id_customer'])
                );

                $subject = $day_trigger['email_subjects'][$order['id_lang']];

                Mail::Send(
                    $order['id_lang'],
                    $template_name,
                    sprintf($subject),
                    $template_vars,
                    $order['email'],
                    null,
                    $configuration['PS_SHOP_EMAIL'],
                    $configuration['PS_SHOP_NAME'],
                    null,
                    null,
                    $dir_mail,
                    null,
                    $id_shop
                );

                // Add log entry after mail is sent
                $lrp_mail_log = new LRPMailLogModel();
                $lrp_mail_log->id_customer = (int)$order['id_customer'];
                $lrp_mail_log->email = pSQL($order['email']);
                $lrp_mail_log->id_mail = (int)$id_mail;
                $lrp_mail_log->date_sent = pSQL(date('Y-m-d'));
                $lrp_mail_log->add();
            }
        }
    }

    /**
     * @param $i
     * @param $sc
     */
    public function unsubscribe($i, $sc)
    {
        $email = $this->cipherTool->decrypt(urldecode($sc));
        Db::getInstance()->update('customer', array('optin' => 0), 'id_customer=' . (int)$i . ' AND email LIKE "' . pSQL($email) . '"');
    }

    public function run()
    {
        $this->getBirthdayCustomers();
        $this->sendReminderEmails();
    }
}
