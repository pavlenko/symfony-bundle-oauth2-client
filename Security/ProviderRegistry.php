<?php

namespace PE\Bundle\OAuth2ClientBundle\Security;

use League\OAuth2\Client\Provider\AbstractProvider;
use PE\Bundle\OAuth2ClientBundle\Model\Button;
use Psr\Container\ContainerInterface;

class ProviderRegistry
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string[]
     */
    private $serviceMap;

    /**
     * @var string[]
     */
    private $buttonMap;

    /**
     * @var string[]
     */
    private $names;

    /**
     * @param ContainerInterface $container
     * @param string[]           $serviceMap
     * @param string[]           $buttonMap
     * @param string[]           $names
     */
    public function __construct(ContainerInterface $container, array $serviceMap, array $buttonMap, array $names)
    {
        $this->container  = $container;
        $this->serviceMap = $serviceMap;
        $this->buttonMap  = $buttonMap;
        $this->names      = $names;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasProvider($name)
    {
        return in_array($name, $this->names);
    }

    /**
     * @param string $name
     *
     * @return AbstractProvider
     */
    public function getProvider($name)
    {
        if (!in_array($name, $this->names)) {
            throw new \InvalidArgumentException(sprintf(
                'There is no OAuth2 provider called "%s". Available are: %s',
                $name,
                implode(', ', $this->names)
            ));
        }

        return $this->container->get($this->serviceMap[$name]);
    }

    /**
     * @param string $name
     *
     * @return Button
     */
    public function getButton($name)
    {
        if (!in_array($name, $this->names)) {
            throw new \InvalidArgumentException(sprintf(
                'There is no OAuth2 provider called "%s". Available are: %s',
                $name,
                implode(', ', $this->names)
            ));
        }

        return $this->container->get($this->buttonMap[$name]);
    }

    /**
     * @return string[]
     */
    public function getNames()
    {
        return $this->names;
    }
}