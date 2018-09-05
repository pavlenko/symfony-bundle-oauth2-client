<?php

namespace PE\Bundle\OAuth2ClientBundle\Exception;

use League\OAuth2\Client\Provider\AbstractProvider;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class NoAuthCodeAuthenticationException extends AuthenticationException
{
    /**
     * @var AbstractProvider
     */
    private $provider;

    /**
     * @inheritDoc
     *
     * @param AbstractProvider $provider
     */
    public function __construct(AbstractProvider $provider, string $message = '', int $code = 0, \Throwable $previous = null)
    {
        $this->provider = $provider;
        parent::__construct($message ?: 'No "code" parameter was found (usually this is a query parameter)!');
    }

    /**
     * @return AbstractProvider
     */
    public function getProvider()
    {
        return $this->provider;
    }
}