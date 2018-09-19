# OAuth client symfony bundle
This bundle integrates with [league/oauth2-client](http://oauth2-client.thephpleague.com/).

## Installation
Install the library via [Composer](https://getcomposer.org/) by
running the following command:
```bash
composer require pe/symfony-bundle-oauth2-client
```
Then enable the bundle in your kernel:
```php
<?php
// app/AppKernel.php
class AppKernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new PE\Bundle\OAuth2ClientBundle\PEOAuth2ClientBundle(),
            // ...
        ];
    }
}
```
or for Symfony 4.0
```php
<?php
// SF 4.0 config/bundles.php

return [
    PE\Bundle\OAuth2ClientBundle\PEOAuth2ClientBundle::class => ['all' => true],
];
```
## Configuration
Add to your config with facebook provider example, all provider options match constructor options argument array keys
```yaml
pe_oauth2_client:
    driver: orm
    class:
        social_account: App\Entity\SocialAccount
    provider:
        facebook:
            class: \League\OAuth2\Client\Provider\Facebook
            options:
                clientId: 123
                clientSecret: 456
                graphApiVersion: v2.12
```
Add security authenticator
```yaml
security:
    # other security config
    firewalls:
        some_firewall:
            # other firewall options
            guard:
                authenticators:
                    - pe_oauth2_client.security.authenticator
```
Create entities
```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="oauth2_social_accounts")
 */
class SocialAccount extends \PE\Bundle\OAuth2ClientBundle\Model\SocialAccount
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="guid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     *
     * @var string
     */
    protected $id;
}
