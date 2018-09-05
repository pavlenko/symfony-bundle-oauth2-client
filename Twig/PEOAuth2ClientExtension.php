<?php

namespace PE\Bundle\OAuth2ClientBundle\Twig;

use PE\Bundle\OAuth2ClientBundle\Security\ProviderRegistry;

class PEOAuth2ClientExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * @var ProviderRegistry
     */
    private $providerRegistry;

    /**
     * @param \Twig_Environment $environment
     * @param ProviderRegistry  $providerRegistry
     */
    public function __construct(\Twig_Environment $environment, ProviderRegistry $providerRegistry)
    {
        $this->environment      = $environment;
        $this->providerRegistry = $providerRegistry;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('pe_oauth2_client_buttons', [$this, 'renderButtons'])
        ];
    }

    /**
     * @param array $options
     *
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderButtons(array $options = [])
    {
        $providers = $this->providerRegistry->getOptionsMap();

        if (isset($options['exclude']) && is_array($options['exclude'])) {
            $providers = array_filter($providers, function ($name) use ($options) {
                return !in_array($name, $options['exclude']);
            }, ARRAY_FILTER_USE_KEY);
        }

        return $this->environment->render('@PEOAuth2Client/buttons.html.twig', ['providers' => $providers]);
    }
}