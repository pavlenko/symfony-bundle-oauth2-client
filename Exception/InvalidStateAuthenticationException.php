<?php

namespace PE\Bundle\OAuth2ClientBundle\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class InvalidStateAuthenticationException extends AuthenticationException
{
    /**
     * @inheritDoc
     */
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message ?: 'Invalid state', $code, $previous);
    }
}