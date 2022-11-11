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
 * @copyright 2016-2021 Musaffar Patel
 * @license   LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class LRPConfigModel
{
    /** @var int  */
    const DISCOUNT_MODE_DISCOUNT = 1;

    /** @var int  */
    const DISCOUNT_MODE_VOUCHER  = 2;

    private $discount_mode = self::DISCOUNT_MODE_DISCOUNT;

    /** @var float Ratio */
    private $ratio;

    /** @var float Point Value */
    private $point_value;

    /** @var integer Order State for Validation */
    private $id_order_state_validation;

    /** @var integer Order State for Cancelled */
    private $id_order_state_cancel;

    /** @var integer boolean referral enabled */
    private $referral_enabled;

    /** @var float Points for referring a friend */
    private $referral_points;

    /** @var float points rewarded to referred friend */
    private $referral_friend_points;

    /** @var float points rewarded on customer birthday */
    private $birthday_points;

    /** @var float Minimum Cart Value Required for redemption */
    private $min_cart_value;

    /** @var integer Number of days after which points will expire */
    private $points_expire_days;

    /** @var integer Max redemption of points as a percentage of the cart total */
    private $max_redemption_limit_percentage;

    /** @var float Minimum Cart Value Required for redemption */
    private $min_points_redemption;

    /** @var integer Number of days after last order to send points reminder */
    private $send_point_reminder_emails;

    /** @var integer Number of days after last order to send points reminder */
    private $points_reminder_email_trigger_days_1;

    /** @var integer Number of days after last order to send points reminder */
    private $points_reminder_email_trigger_days_2;

    /** @var integer Number of days after last order to send points reminder */
    private $points_reminder_email_trigger_days_3;

    /** @var array reminder email subject */
    private $points_reminder_email_subject_1;

    /** @var array reminder email subject */
    private $points_reminder_email_subject_2;

    /** @var array reminder email subject */
    private $points_reminder_email_subject_3;

    /** @var Currency to load configuration for */
    private $currency_iso;

    /** @var integer Customer Group ID */
    private $id_group;

    /** @var integer Customer Shop ID */
    private $id_shop;

    /** @var Currency points settings based on default currency */
    private $automatic_currency_settings = false;

    /** @var bool  */
    private $discount_combinable = true;

    /**
     * LRPConfigModel constructor.
     * @param string $currency_iso
     * @param int $id_group Customer Group
     */
    public function __construct($currency_iso = '', $id_group = 0, $id_shop = 0)
    {
        if ($currency_iso == '') {
            if (Validate::isLoadedObject(Context::getContext()->currency)) {
                $currency_iso = Context::getContext()->currency->iso_code;
            } else {
                $currency_iso = Currency::getDefaultCurrency()->iso_code;
            }
        }

        if ($id_shop == 0) {
            $id_shop = Context::getContext()->shop->id;
        }

        $this->currency_iso = $currency_iso;
        $this->id_group = (int)$id_group;
        $this->id_shop = (int)$id_shop;

        $this->setDiscountMode(Configuration::get($this->getKey('lrp_discount_mode', false), null, null, $this->id_shop));
        $this->setIdOrderStateValidation(Configuration::get($this->getKey('lrp_id_order_state_validation', false), null, null, $this->id_shop));
        $this->setIdOrderStateCancel(Configuration::get($this->getKey('lrp_id_order_state_cancel', false), null, null, $this->id_shop));
        $this->setReferralEnabled(Configuration::get($this->getKey('lrp_referral_enabled', false), null, null, $this->id_shop));
        $this->setPointsExpireDays(Configuration::get($this->getKey('lrp_points_expire_days', false), null, null, $this->id_shop));
        $this->setMaxRedemptionLimitPercentage(Configuration::get($this->getKey('lrp_max_redemption_limit_percentage', false), null, null, $this->id_shop));
        $this->setSendPointReminderEmails(Configuration::get($this->getKey('lrp_send_point_reminder_emails', false), null, null, $this->id_shop));
        $this->setPointsReminderEmailTriggerDays1(Configuration::get($this->getKey('lrp_points_reminder_email_trigger_days_1', false), null, null, $this->id_shop));
        $this->setPointsReminderEmailTriggerDays2(Configuration::get($this->getKey('lrp_points_reminder_email_trigger_days_2', false), null, null, $this->id_shop));
        $this->setPointsReminderEmailTriggerDays3(Configuration::get($this->getKey('lrp_points_reminder_email_trigger_days_3', false), null, null, $this->id_shop));

        $this->setRatio(Configuration::get($this->getKey('lrp_ratio', true), null, null, $this->id_shop));
        $this->setPointValue(Configuration::get($this->getKey('lrp_point_value', true), null, null, $this->id_shop));
        $this->setReferralPoints(Configuration::get($this->getKey('lrp_referral_points', true), null, null, $this->id_shop));
        $this->setReferralFriendPoints(Configuration::get($this->getKey('lrp_referral_friend_points', true), null, null, $this->id_shop));
        $this->setBirthdayPoints(Configuration::get($this->getKey('lrp_birthday_points', true), null, null, $this->id_shop));
        $this->setMinCartValue(Configuration::get($this->getKey('lrp_min_cart_value', true), null, null, $this->id_shop));
        $this->setMinPointsRedemption(Configuration::get($this->getKey('lrp_min_points_redemption', true), null, null, $this->id_shop));
        $this->setAutomaticCurrencySettings(Configuration::get($this->getKey('lrp_automatic_currency', false), null, null, $this->id_shop));
        $this->setDiscountCombinable(Configuration::get($this->getKey('lrp_discount_combinable', false), null, null, $this->id_shop));
    }

    /**
     * Get the configuration key for the Customer Group and Currency ISO Code
     * @param $key
     * @return string
     */
    private function getKey($key, $use_currency)
    {
        if ($use_currency) {
            return $key . '_' . $this->id_group . '_' . $this->id_shop . '_' . $this->currency_iso;
        } else {
            return $key . '_' . $this->id_group . '_' . $this->id_shop;
        }
    }

    /**
     * @return int
     */
    public function getDiscountMode()
    {
        return $this->discount_mode;
    }

    /**
     * @param $discount_mode
     * @return $this
     */
    public function setDiscountMode($discount_mode)
    {
        $this->discount_mode = $discount_mode;
        return $this;
    }

    /**
     * @return int
     */
    public function getRatio()
    {
        return $this->ratio;
    }

    /**
     * @param int $ratio
     * @return LRPConfigModel
     */
    public function setRatio($ratio)
    {
        $this->ratio = $ratio;
        return $this;
    }

    /**
     * @return int
     */
    public function getPointValue()
    {
        return $this->point_value;
    }

    /**
     * @param int $point_value
     * @return LRPConfigModel
     */
    public function setPointValue($point_value)
    {
        $this->point_value = $point_value;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdOrderStateValidation()
    {
        return $this->id_order_state_validation;
    }

    /**
     * @param string $id_order_state_validation
     * @return LRPConfigModel
     */
    public function setIdOrderStateValidation($id_order_state_validation)
    {
        $this->id_order_state_validation = $id_order_state_validation;
        return $this;
    }

    /**
     * @return integer
     */
    public function getIdOrderStateCancel()
    {
        return (int)$this->id_order_state_cancel;
    }

    /**
     * @param string $id_order_state_cancel
     * @return LRPConfigModel
     */
    public function setIdOrderStateCancel($id_order_state_cancel)
    {
        $this->id_order_state_cancel = $id_order_state_cancel;
        return $this;
    }

    /**
     * @return integer
     */
    public function getReferralEnabled()
    {
        return (int)$this->referral_enabled;
    }

    /**
     * @param string $referral_enabled
     * @return LRPConfigModel
     */
    public function setReferralEnabled($referral_enabled)
    {
        $this->referral_enabled = $referral_enabled;
        return $this;
    }

    /**
     * @return string
     */
    public function getReferralPoints()
    {
        return $this->referral_points;
    }

    /**
     * @param string $referral_points
     * @return LRPConfigModel
     */
    public function setReferralPoints($referral_points)
    {
        $this->referral_points = $referral_points;
        return $this;
    }

    /**
     * @return string
     */
    public function getReferralFriendPoints()
    {
        return $this->referral_friend_points;
    }

    /**
     * @param string $referral_friend_points
     * @return LRPConfigModel
     */
    public function setReferralFriendPoints($referral_friend_points)
    {
        $this->referral_friend_points = $referral_friend_points;
        return $this;
    }

    /**
     * Get points to be rewarded on customer birthday
     * @return mixed
     */
    public function getBirthdayPoints()
    {
        return $this->birthday_points;
    }

    /**
     * @param integer $birthday_points
     * @return LRPConfigModel
     */
    public function setBirthdayPoints($birthday_points)
    {
        $this->birthday_points = $birthday_points;
        return $this;
    }


    /**
     * @return string
     */
    public function getMinCartValue()
    {
        return $this->min_cart_value;
    }

    /**
     * @return float
     */
    public function getMinPointsRedemption()
    {
        return (float)$this->min_points_redemption;
    }

    /**
     * @param string $min_cart_value
     * @return LRPConfigModel
     */
    public function setMinCartValue($min_cart_value)
    {
        $this->min_cart_value = $min_cart_value;
        return $this;
    }

    /**
     * @param $min_points_redemption
     * @return $this
     */
    public function setMinPointsRedemption($min_points_redemption)
    {
        $this->min_points_redemption = $min_points_redemption;
        return $this;
    }

    /**
     * @return integer
     */
    public function getPointsExpireDays()
    {
        return (int)$this->points_expire_days;
    }

    /**
     * Set POints expiry days value
     * @param $value
     * @return $this
     */
    public function setPointsExpireDays($value)
    {
        $this->points_expire_days = (int)$value;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxRedemptionLimitPercentage()
    {
        return (int)$this->max_redemption_limit_percentage;
    }

    /**
     * set max redemption limit percentage
     * @param $value
     * @return $this
     */
    public function setMaxRedemptionLimitPercentage($value)
    {
        $this->max_redemption_limit_percentage = (int)$value;
        return $this;
    }

    /**
     * @return integer
     */
    public function getSendPointReminderEmails()
    {
        return (int)$this->send_point_reminder_emails;
    }

    /**
     * Set POints expiry days value
     * @param $value
     * @return $this
     */
    public function setSendPointReminderEmails($value)
    {
        $this->send_point_reminder_emails = (int)$value;
        return $this;
    }

    /**
     * @return integer
     */
    public function getPointsReminderEmailTriggerDays1()
    {
        return (int)$this->points_reminder_email_trigger_days_1;
    }

    /**
     * Set Points expiry days value
     * @param $value
     * @return $this
     */
    public function setPointsReminderEmailTriggerDays1($value)
    {
        $this->points_reminder_email_trigger_days_1 = (int)$value;
        return $this;
    }

    /**
     * @return integer
     */
    public function getPointsReminderEmailTriggerDays2()
    {
        return (int)$this->points_reminder_email_trigger_days_2;
    }

    /**
     * Set Points expiry days value
     * @param $value
     * @return $this
     */
    public function setPointsReminderEmailTriggerDays2($value)
    {
        $this->points_reminder_email_trigger_days_2 = (int)$value;
        return $this;
    }

    /**
     * @return integer
     */
    public function getPointsReminderEmailTriggerDays3()
    {
        return (int)$this->points_reminder_email_trigger_days_3;
    }

    /**
     * Set Points expiry days value
     * @param $value
     * @return $this
     */
    public function setPointsReminderEmailTriggerDays3($value)
    {
        $this->points_reminder_email_trigger_days_3 = (int)$value;
        return $this;
    }

    /**
     * @param $id_lang
     * @return mixed
     */
    public function getPointsReminderEmailSubject1($id_lang)
    {
        return Configuration::get($this->getKey('lrp_points_reminder_email_subject_1_' . $id_lang, false), null, null, $this->id_shop);
    }

    /**
     * @param $id_lang
     * @return mixed
     */
    public function getPointsReminderEmailSubject2($id_lang)
    {
        return Configuration::get($this->getKey('lrp_points_reminder_email_subject_2_' . $id_lang, false), null, null, $this->id_shop);
    }

    /**
     * @param $id_lang
     * @return mixed
     */
    public function getPointsReminderEmailSubject3($id_lang)
    {
        return Configuration::get($this->getKey('lrp_points_reminder_email_subject_3_' . $id_lang, false), null, null, $this->id_shop);
    }

    /**
     * Set points reminder email subject
     * @param $id_lang
     * @param $value
     * @return $this
     */
    public function setPointsReminderEmailSubject1($id_lang, $value)
    {
        $this->points_reminder_email_subject_1[$id_lang] = $value;
        return $this;
    }

    /**
     * Set points reminder email subject
     * @param $id_lang
     * @param $value
     * @return $this
     */
    public function setPointsReminderEmailSubject2($id_lang, $value)
    {
        $this->points_reminder_email_subject_2[$id_lang] = $value;
        return $this;
    }

    /**
     * Set points reminder email subject
     * @param $id_lang
     * @param $value
     * @return $this
     */
    public function setPointsReminderEmailSubject3($id_lang, $value)
    {
        $this->points_reminder_email_subject_3[$id_lang] = $value;
        return $this;
    }

    /**
     * Get automatic_currency_settings
     * @return Currency
     */
    public function getAutomaticCurrencySettings()
    {
        return $this->automatic_currency_settings;
    }

    /**
     * Get automatic_currency_settings
     * @param $automatic_currency_settings
     * @return $this
     */
    public function setAutomaticCurrencySettings($automatic_currency_settings)
    {
        $this->automatic_currency_settings = $automatic_currency_settings;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDiscountCombinable()
    {
        return $this->discount_combinable;
    }

    /**
     * @param $value
     */
    public function setDiscountCombinable($value)
    {
        $this->discount_combinable = $value;
    }

    /**
     * Save a configuration value to storage
     * @param $key
     * @param $value
     */
    public function update($key, $value, $use_currency, $id_shop)
    {
        $key = $this->getKey($key, $use_currency);
        Configuration::updateValue($key, $value, false, null, (int)$id_shop);
    }
}
