## Contributing

### Coding standards

Your code should follow [Symfony's coding standards](http://symfony.com/doc/current/contributing/code/standards.html) as close as possible.


### Conventions

Your code should follow [Symfony's code conventions](http://symfony.com/doc/current/contributing/code/conventions.html) as close as possible.


### Testing your code locally

Please make sure the unit tests run without failures before - and after - contributing. To do this, simply run your tests
using the phpunit version bundled with the library, like this:

    $ ./bin/phpunit


### Sharing your adapters

You want to create a new adapter for use by the TissueBundle? That's great! I've created a dedicated [how-to](https://github.com/cleentfaar/tissue/Resources/doc/how-to/creating-your-own-adapter.md)
for you to start off with. To let your users include it in their application, you only have to make sure to include the
bundle as a requirement in your package's `composer.json`:
```json
"require": {
    "cleentfaar/tissue-bundle": "~0.1"
}
```
