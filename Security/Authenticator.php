<?php

namespace PE\Bundle\OAuth2ClientBundle\Security;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use PE\Bundle\OAuth2ClientBundle\Event\UserEvent;
use PE\Bundle\OAuth2ClientBundle\Exception\InvalidStateAuthenticationException;
use PE\Bundle\OAuth2ClientBundle\Exception\NoAuthCodeAuthenticationException;
use PE\Bundle\OAuth2ClientBundle\Repository\SocialAccountRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class Authenticator extends AbstractGuardAuthenticator
{
    const OAUTH2_SESSION_STATE_KEY = 'pe_oauth2_client_state';
    const OAUTH2_SESSION_USER_KEY  = 'pe_oauth2_client_user';

    /**
     * @var ProviderRegistry
     */
    private $providerRegistry;

    /**
     * @var SocialAccountRepositoryInterface
     */
    private $socialAccountRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var string
     */
    private $targetPath;

    /**
     * @param ProviderRegistry                 $providerRegistry
     * @param EventDispatcherInterface         $dispatcher
     * @param SocialAccountRepositoryInterface $socialAccountRepository
     * @param string                           $targetPath
     */
    public function __construct(
        ProviderRegistry $providerRegistry,
        EventDispatcherInterface $dispatcher,
        SocialAccountRepositoryInterface $socialAccountRepository,
        $targetPath
    ) {
        $this->providerRegistry        = $providerRegistry;
        $this->dispatcher              = $dispatcher;
        $this->socialAccountRepository = $socialAccountRepository;
        $this->targetPath              = $targetPath;
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        // This method is not applicable
        return null;
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'pe_oauth2_client__authenticate';
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        try {
            if (!($session = $request->getSession())) {
                throw new \RuntimeException('Session is required');
            }

            $providerName = $request->attributes->get('provider');
            $provider     = $this->providerRegistry->get($providerName);

            $code  = $request->get('code');
            $state = $request->get('state');

            if (!$code) {
                $session->set(self::OAUTH2_SESSION_STATE_KEY, $provider->getState());
                throw new NoAuthCodeAuthenticationException($provider);
            }

            if (empty($state) || $state !== $session->get(self::OAUTH2_SESSION_STATE_KEY)) {
                $session->remove(self::OAUTH2_SESSION_STATE_KEY);
                throw new InvalidStateAuthenticationException();
            }

            return [$providerName, $provider->getAccessToken('authorization_code', ['code' => $code])];
        } catch (IdentityProviderException $e) {
            throw new AuthenticationException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /* @var $provider string */
        /* @var $token AccessToken */
        list($providerName, $token) = $credentials;

        $oauthUser = $this->providerRegistry->get($providerName)->getResourceOwner($token);

        // Check social account exists
        $socialAccount = $this->socialAccountRepository->findAccountBySocial($providerName, $oauthUser->getId());
        if (!$socialAccount) {
            // Not exists - create new
            $socialAccount = $this->socialAccountRepository->createAccount();
            $socialAccount->setSocialID($oauthUser->getId());
            $socialAccount->setSocialType($providerName);
        }

        $event = new UserEvent($socialAccount);

        if ($socialAccount->getUserID()) {
            // If account has referenced user - got it
            $this->dispatcher->dispatch(UserEvent::USER_GET, $event);

            if (!$event->getUser()) {
                // Remove invalid account reference
                $this->socialAccountRepository->removeAccount($socialAccount);
            }
        } else {
            // Bind account to exists user or create new user
            $this->dispatcher->dispatch(UserEvent::USER_BIND, $event);

            // Update account after changes
            $this->socialAccountRepository->updateAccount($socialAccount);
        }

        // Return user
        return $event->getUser();
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        // Nothing to check
        return true;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($exception instanceof NoAuthCodeAuthenticationException) {
            return new RedirectResponse($exception->getProvider()->getAuthorizationUrl());
        }

        return new Response($exception->getMessage());
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $providerName = $request->attributes->get('provider');
        $targetPath   = $this->providerRegistry->getOption($providerName, 'targetPath') ?: $this->targetPath;

        return new RedirectResponse($targetPath);
    }

    /**
     * @inheritDoc
     */
    public function supportsRememberMe()
    {
        return true;
    }
}