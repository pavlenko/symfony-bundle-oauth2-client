<?php

namespace PE\Bundle\OAuth2ClientBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass;
use PE\Bundle\OAuth2ClientBundle\DependencyInjection\PEOAuth2ClientExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PEOAuth2ClientBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            return new PEOAuth2ClientExtension();
        }

        return $this->extension;
    }

    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container)
    {
        $this->addCompilerMappingsPass($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function addCompilerMappingsPass(ContainerBuilder $container)
    {
        $mappings = [
            realpath(__DIR__ . '/Resources/config/doctrine-mapping') => __NAMESPACE__ . '\Model',
        ];

        if (class_exists('Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass')) {
            $container->addCompilerPass(DoctrineOrmMappingsPass::createYamlMappingDriver(
                $mappings,
                ['pe_oauth2_client.model_manager_name'],
                'pe_oauth2_client.backend_type.orm'
            ));
        }

        if (class_exists('Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass')) {
            $container->addCompilerPass(DoctrineMongoDBMappingsPass::createYamlMappingDriver(
                $mappings,
                ['pe_oauth2_client.model_manager_name'],
                'pe_oauth2_client.backend_type.mongodb'
            ));
        }
    }
}