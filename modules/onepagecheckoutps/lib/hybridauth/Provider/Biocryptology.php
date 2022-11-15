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
 * @category  PrestaShop
 * @category  Module
 */

namespace Hybridauth\Provider;

use Hybridauth\Adapter\OAuth2;
use Hybridauth\Exception\UnexpectedApiResponseException;
use Hybridauth\Data;
use Hybridauth\User;

class Biocryptology extends OAuth2
{
    /**
     * {@inheritdoc}
     */
    // phpcs:ignore
    protected $scope = 'openid profile email address';

    /**
     * {@inheritdoc}
     */
    protected $apiBaseUrl = 'https://is.biocryptology.com/';

    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = 'https://is.biocryptology.com/V1/auth';

    /**
     * {@inheritdoc}
     */
    protected $accessTokenUrl = 'https://is.biocryptology.com/V1/token';

    /**
     * {@inheritdoc}
     */
    protected $apiDocumentation = 'https://id.biocryptology.com/business/';

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this->AuthorizeUrlParameters += [
            'access_type' => 'offline'
        ];

        if ($this->isRefreshTokenAvailable()) {
            $this->tokenRefreshParameters += [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret
            ];
        }
    }

    /**
     * {@inheritdoc}
     *
     */
    public function getUserProfile()
    {
        $response = $this->apiRequest('V1/userinfo');

        $data = new Data\Collection($response);

        if (!$data->exists('sub')) {
            throw new UnexpectedApiResponseException('Provider API returned an unexpected response.');
        }

        $userProfile = new User\Profile();

        $userProfile->identifier = $data->get('sub');
        $userProfile->firstName = $data->get('given_name');
        $userProfile->lastName = $data->get('family_name');
        $userProfile->displayName = $data->get('name');
        $userProfile->photoURL = $data->get('picture');
        $userProfile->profileURL = $data->get('profile');
        $userProfile->gender = $data->get('gender');
        $userProfile->language = $data->get('locale');
        $userProfile->email = $data->get('email');

        $userProfile->emailVerified = $data->get('email_verified') ? $userProfile->email : '';

        if ($this->config->get('photo_size')) {
            $userProfile->photoURL .= '?sz=' . $this->config->get('photo_size');
        }

        return $userProfile;
    }
}