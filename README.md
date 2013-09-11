Hateoas
=======

[![Build
Status](https://secure.travis-ci.org/willdurand/Hateoas.png)](http://travis-ci.org/willdurand/Hateoas)

A PHP library to support implementing representations for HATEOAS REST web services.

**Important:** This library is under heavy refactoring. If you are using it, you
will find the code in the `1.0` branch. The `master` branch is a work in
progress, and should not be used in production at the moment.


Usage
-----

Not yet available as the library is currently under heavy refactoring.

For those who use the `1.0` version, you can [jump to this documentation
page](https://github.com/willdurand/Hateoas/blob/1.0/README.md#readme) though.


Installation
------------

Using [Composer](http://getcomposer.org/), require the `willdurand/hateoas`
package:

``` javascript
{
    "require": {
        "willdurand/hateoas": "2.0.*@dev"
    }
}
```

Otherwise, install the library and setup the autoloader yourself.


Contributing
------------

See CONTRIBUTING file.


Running the Tests
-----------------

Install the [Composer](http://getcomposer.org/) `dev` dependencies:

    php composer.phar install --dev

Then, run the test suite using [atoum](http://www.atoum.org/):

    bin/atoum


License
-------

Hateoas is released under the MIT License. See the bundled LICENSE file for details.
