<?php

namespace PE\Bundle\OAuth2ClientBundle\Event;

use PE\Bundle\OAuth2ClientBundle\Model\SocialAccountInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserEvent
{
    const USER_GET  = 'pe_oauth2_client.user.get';
    const USER_BIND = 'pe_oauth2_client.user.bind';

    /**
     * @var SocialAccountInterface
     */
    private $socialAccount;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @param SocialAccountInterface $socialAccount
     */
    public function __construct(SocialAccountInterface $socialAccount)
    {
        $this->socialAccount = $socialAccount;
    }

    /**
     * @return SocialAccountInterface
     */
    public function getSocialAccount()
    {
        return $this->socialAccount;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }
}