<?php

namespace PE\Bundle\OAuth2ClientBundle\Repository;

use PE\Bundle\OAuth2ClientBundle\Model\SocialAccountInterface;

interface SocialAccountRepositoryInterface
{
    /**
     * @param string $userClass
     * @param string $userID
     *
     * @return SocialAccountInterface[]
     */
    public function findAccountsByUser($userClass, $userID);

    /**
     * @param string $id
     *
     * @return SocialAccountInterface|null
     */
    public function findAccountByID($id);

    /**
     * @param string $socialType
     * @param string $socialID
     *
     * @return SocialAccountInterface|null
     */
    public function findAccountBySocial($socialType, $socialID);

    /**
     * @return SocialAccountInterface
     */
    public function createAccount();

    /**
     * @param SocialAccountInterface $account
     * @param bool                   $flush
     */
    public function updateAccount(SocialAccountInterface $account, $flush = true);

    /**
     * @param SocialAccountInterface $account
     * @param bool                   $flush
     */
    public function removeAccount(SocialAccountInterface $account, $flush = true);

    /**
     * Flush changes
     */
    public function flush();
}