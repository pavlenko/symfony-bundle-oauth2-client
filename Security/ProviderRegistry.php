<?php

namespace PE\Bundle\OAuth2ClientBundle\Security;

use League\OAuth2\Client\Provider\AbstractProvider;
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
     * @var array
     */
    private $optionsMap;

    /**
     * @param ContainerInterface $container
     * @param string[]           $serviceMap
     */
    public function __construct(ContainerInterface $container, array $serviceMap, array $optionsMap)
    {
        $this->container  = $container;
        $this->serviceMap = $serviceMap;
        $this->optionsMap = $optionsMap;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->serviceMap[$name]);
    }

    /**
     * @param string $name
     *
     * @return AbstractProvider
     */
    public function get($name)
    {
        if (!isset($this->serviceMap[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'There is no OAuth2 provider called "%s". Available are: %s',
                $name,
                implode(', ', array_keys($this->serviceMap))
            ));
        }

        return $this->container->get($this->serviceMap[$name]);
    }

    /**
     * @return array
     */
    public function getOptionsMap()
    {
        return $this->optionsMap;
    }

    /**
     * @param string $name
     * @param string $option
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getOption($name, $option, $default = null)
    {
        if (isset($this->optionsMap[$name], $this->optionsMap[$name][$option])) {
            return $this->optionsMap[$name][$option];
        }

        return $default;
    }
}