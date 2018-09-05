<?php

namespace PE\Bundle\OAuth2ClientBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\Expression;

class PEOAuth2ClientExtension extends Extension
{
    /**
     * @var array
     */
    private static $drivers = [
        'orm' => [
            'registry' => 'doctrine',
        ],
        'mongodb' => [
            'registry' => 'doctrine_mongodb',
        ],
    ];

    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        if ('custom' !== $config['driver']) {
            if (!isset(self::$drivers[$config['driver']])) {
                throw new \RuntimeException('Unknown driver');
            }

            // Set registry alias
            $container->setAlias(
                'pe_oauth2_client.doctrine_registry',
                new Alias(self::$drivers[$config['driver']]['registry'], false)
            );

            // Set factory to object manager
            $definition = $container->getDefinition('pe_oauth2_client.object_manager');
            $definition->setFactory([new Reference('pe_oauth2_client.doctrine_registry'), 'getManager']);

            // Set manager name to access in config
            $container->setParameter('pe_oauth2_client.object_manager_name', $config['object_manager_name']);

            // Set parameter for switch mapping
            $container->setParameter('pe_oauth2_client.backend_type.' . $config['driver'], true);

            // Set classes to use in default services
            $container->setParameter('pe_oauth2_client.class.social_account', $config['class']['social_account']);
        }

        // Set aliases to services
        $container->setAlias('pe_oauth2_client.repository.social_account', new Alias($config['service']['repository_social_account'], true));

        $container->setParameter('pe_oauth2_client.target_path', $config['target_path']);

        // Process clients
        if (!empty($config['provider'])) {
            $definition = $container->getDefinition('pe_oauth2_client.security.provider_registry');
            $serviceMap = [];
            $optionsMap = [];

            foreach ($config['provider'] as $name => $providerConfig) {
                $providerConfig['options']['redirectUri'] = new Expression(
                    "service('router').generateUrl('pe_oauth2_client__authenticate', {provider: '$name'})"
                );

                foreach ($providerConfig['collaborators'] as $key => $serviceID) {
                    $providerConfig['collaborators'][$key] = $serviceID ? new Reference($serviceID) : null;
                }

                $providerDefinition = new Definition($providerConfig['class']);
                $providerDefinition->setArguments([$providerConfig['options'], $providerConfig['collaborators']]);
                $providerDefinition->setPublic(true);

                $serviceMap[$name] = $id = 'pe_oauth2_client.provider.' . $name;
                $optionsMap[$name] = $providerConfig['options'];

                $container->setDefinition($id, $providerDefinition);
            }

            $definition->replaceArgument(1, $serviceMap);
            $definition->replaceArgument(2, $optionsMap);
        }
    }

    /**
     * @inheritDoc
     */
    public function getAlias()
    {
        return 'pe_oauth2_client';
    }
}