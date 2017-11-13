# Sylius Ceca payment gateway plugin  

## Installation

```bash
$ composer require sergiosanchezalvarez/ceca-plugin
```

Add routing to your app/config/routing.yml

```yml
ceca_payum:
    resource: "@CecaPlugin/Resources/config/routing.yml"
```

Add plugin dependencies to your AppKernel.php file:

```php
public function registerBundles()
{
    return array_merge(parent::registerBundles(), [
        ...
        
        new \Sergiosanchezalvarez\CecaPlugin\CecaPlugin(),
    ]);
}
```

Configure the comercios.ceca.es with this notification url

```html
* http://yourdomain.tld/payum/ceca/bank-notification
```
* or https

Configure and active the gateway in syliys admin zone

And it's done! :)
