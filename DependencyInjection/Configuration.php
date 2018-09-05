<?php

namespace PE\Bundle\OAuth2ClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('pe_oauth2_client');

        $drivers = ['orm', 'mongodb', 'custom'];

        $rootNode
            ->children()
                ->scalarNode('driver')
                    ->validate()
                        ->ifNotInArray($drivers)
                        ->thenInvalid('The driver %s is not supported. Please choose one of ' . json_encode($drivers))
                    ->end()
                    ->cannotBeOverwritten()
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('object_manager_name')->defaultNull()->end()
                ->arrayNode('class')
                    ->isRequired()
                    ->children()
                        ->scalarNode('social_account')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('service')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('repository_social_account')->defaultValue('pe_oauth2_client.repository.social_account.default')->end()
                    ->end()
                ->end()
                ->scalarNode('target_path')->defaultValue('/')->end()
                ->arrayNode('provider')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->isRequired()->cannotBeEmpty()->end()
                            ->arrayNode('options')
                                ->addDefaultsIfNotSet()
                                ->ignoreExtraKeys(false)
                                ->children()
                                    ->scalarNode('clientId')->isRequired()->cannotBeEmpty()->end()
                                    ->scalarNode('clientSecret')->isRequired()->cannotBeEmpty()->end()
                                    ->scalarNode('state')->defaultNull()->end()
                                ->end()
                            ->end()
                            ->arrayNode('collaborators')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('grantFactory')->defaultNull()->end()
                                    ->scalarNode('requestFactory')->defaultNull()->end()
                                    ->scalarNode('httpClient')->defaultNull()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()

            ->validate()
                ->ifTrue(function ($v) {
                    return 'custom' === $v['driver']
                        && (
                            'pe_oauth2_client.repository.social_account.default' === $v['service']['repository_social_account']
                        );
                })
                ->thenInvalid('You need to specify your own services when using the "custom" driver.')
            ->end()
        ;

        return $treeBuilder;
    }
}