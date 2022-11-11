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

namespace OnePageCheckoutPS\Application\Core\MyAccount;

use OnePageCheckoutPS;
use OnePageCheckoutPS\Entity\OpcSocialNetwork;
use OnePageCheckoutPS\Exception\SocialNetworkException;

class SocialNetwork
{
    private $module;
    private $opcSocialNetwork;

    public const SERVICE_NAME = 'onepagecheckoutps.core.social_network';

    public const SOCIAL_NETWORK_AVAILABLES = array(
        'Google' => array(
            'id' => '',
            'secret' => '',
        ),
        'Facebook' => array(
            'id' => '',
            'secret' => '',
        ),
        'Paypal' => array(
            'id' => '',
            'secret' => '',
        ),
        'Apple' => array(
            'id' => '',
            'team_id' => '',
            'key_id' => '',
            'key_file' => '',
        ),
        'Biocryptology' => array(
            'id' => '',
            'secret' => '',
        ),
    );

    public function __construct(OnePageCheckoutPS $module)
    {
        $this->module = $module;
    }

    public function fillWith(array $params)
    {
        $this->opcSocialNetwork = new OpcSocialNetwork();
        $this->opcSocialNetwork->setShopId($this->module->getContextProvider()->getShopId());

        if (array_key_exists('name', $params)) {
            $this->opcSocialNetwork->setName($params['name']);
        }
        if (array_key_exists('enabled', $params)) {
            $this->opcSocialNetwork->setEnabled($params['enabled']);
        }
        if (array_key_exists('keys', $params)) {
            $this->opcSocialNetwork->setKeys($params['keys']);
        }
    }

    public function validate()
    {
        if (empty($this->opcSocialNetwork->getName())) {
            throw new SocialNetworkException(
                'The name of the social network to be deleted is necessary.',
                SocialNetworkException::SOCIAL_NETWORK_EMPTY
            );
        }

        if (!in_array($this->opcSocialNetwork->getName(), array_keys(self::SOCIAL_NETWORK_AVAILABLES))) {
            throw new SocialNetworkException(
                'The name of the social network sent is not supported.',
                SocialNetworkException::SOCIAL_NETWORK_NOT_SUPPORTED
            );
        }
    }

    public function existSocialNetworkSaved()
    {
        $opcSocialNetworkRepository = $this->module->getEntityManager()->getRepository(OpcSocialNetwork::class);
        $opcSocialNetworkResult = $opcSocialNetworkRepository->findOneBy(array(
            'name' => $this->opcSocialNetwork->getName(),
            'shopId' => $this->opcSocialNetwork->getShopId(),
        ));

        if ($opcSocialNetworkResult) {
            return true;
        }

        return false;
    }

    public function add()
    {
        $this->validate();

        if (empty($this->opcSocialNetwork->getKeys())) {
            throw new SocialNetworkException(
                'The keys are necessary to add the social network.',
                SocialNetworkException::SOCIAL_NETWORK_KEYS_EMPTY
            );
        }

        if ($this->existSocialNetworkSaved()) {
            throw new SocialNetworkException(
                'The social network is already added, if you want to modify it, please first delete it and add it again.',
                SocialNetworkException::SOCIAL_NETWORK_DUPLICATED
            );
        }

        foreach ($this->opcSocialNetwork->getKeys() as $key => $value) {
            if (empty($value)) {
                throw new SocialNetworkException(
                    sprintf('The value of "%s" cannot be empty.', $key),
                    SocialNetworkException::SOCIAL_NETWORK_KEY_VALUE_EMPTY
                );
            }
        }

        $entityManager = $this->module->getEntityManager();
        $entityManager->persist($this->opcSocialNetwork);
        $entityManager->flush();
    }

    public function delete()
    {
        $this->validate();

        $entityManager = $this->module->getEntityManager();

        $opcSocialNetworkRepository = $entityManager->getRepository(OpcSocialNetwork::class);
        $opcSocialNetworkResult = $opcSocialNetworkRepository->findOneBy(array(
            'name' => $this->opcSocialNetwork->getName(),
            'shopId' => $this->opcSocialNetwork->getShopId(),
        ));

        if (!$opcSocialNetworkResult) {
            throw new SocialNetworkException(
                sprintf('The social network %s was not found.', $this->opcSocialNetwork->getName()),
                SocialNetworkException::SOCIAL_NETWORK_NOT_FOUND
            );
        }

        $entityManager->remove($opcSocialNetworkResult);
        $entityManager->flush();
    }

    public function getList($enabled = null)
    {
        $return = array();

        $opcSocialNetworkRepository = $this->module->getEntityManager()->getRepository(OpcSocialNetwork::class);

        $paramListSQL = array(
            'shopId' => $this->module->getContextProvider()->getShopId(),
        );

        if (!is_null($enabled)) {
            $paramListSQL['enabled'] = $enabled;
        }

        $opcSocialNetworkResult = $opcSocialNetworkRepository->findBy($paramListSQL);

        if ($opcSocialNetworkResult) {
            $baseLink = $this->module->getContextProvider()->getLink()->getBaseLink();
            $baseLink .= 'checkout/myaccount/loginSocialCustomer?provider=';

            foreach ($opcSocialNetworkResult as $opcSocialNetwork) {
                $linkToConnect = $baseLink . $opcSocialNetwork->getName();
                $keyList = $opcSocialNetwork->getKeys();

                if ($opcSocialNetwork->getName() === 'Apple') {
                    $keyList['key_file'] = _PS_ROOT_DIR_ . '/upload/apple_key/' . $keyList['key_file'];
                }

                $return[$opcSocialNetwork->getName()] = array(
                    'enabled' => $opcSocialNetwork->getEnabled(),
                    'keys' => $keyList,
                    'linkToConnect' => $linkToConnect,
                );
            }
        }

        return $return;
    }
}
