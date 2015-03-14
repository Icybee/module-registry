# Registry [![Build Status](https://travis-ci.org/Icybee/module-registry.svg?branch=master)](https://travis-ci.org/Icybee/module-registry)

Stores settings, options, and meta data.

```php
<?php

$registry = $app->registry;

$a = $registry['a_property'];           // null
$a = $registry->get('a_property', 123); // 123

$registry['a_property'] = 123;
$a = $registry['a_property'];           // 123

$app->user->metas['a_property'] = 123;
$app->site->metas['a_property'] = 123;
$app->models['articles']->one->metas['a_property'] = 123;
``` 





----------





## Requirement

The package requires PHP 5.4 or later.





## Installation

The recommended way to install this package is through [Composer](http://getcomposer.org/):

```
$ composer require icybee/module-registry
```

This module is part of the modules required by [Icybee](http://icybee.org).





### Cloning the repository

The package is [available on GitHub](https://github.com/Icybee/module-registry), its repository can be
cloned with the following command line:

	$ git clone https://github.com/Icybee/module-registry.git registry





## Documentation

The package is documented as part of the [Icybee](http://icybee.org/) CMS
[documentation](http://icybee.org/docs/). The documentation for the package and its
dependencies can be generated with the `make doc` command. The documentation is generated in
the `docs` directory using [ApiGen](http://apigen.org/). The package directory can later by
cleaned with the `make clean` command.





## Testing

The test suite is ran with the `make test` command. [Composer](http://getcomposer.org/) is
automatically installed as well as all the dependencies required to run the suite. The package
directory can later be cleaned with the `make clean` command.

The package is continuously tested by [Travis CI](http://about.travis-ci.org/).

[![Build Status](https://travis-ci.org/Icybee/module-registry.svg?branch=master)](https://travis-ci.org/Icybee/module-registry)





## License

The module is licensed under the New BSD License - See the [LICENSE](LICENSE) file for details.
