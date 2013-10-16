Hateoas
=======

[![Build
Status](https://secure.travis-ci.org/willdurand/Hateoas.png)](http://travis-ci.org/willdurand/Hateoas)
[![Scrutinizer Quality
Score](https://scrutinizer-ci.com/g/willdurand/Hateoas/badges/quality-score.png)](https://scrutinizer-ci.com/g/willdurand/Hateoas/)

A PHP library to support implementing representations for HATEOAS REST web services.


Usage
-----

> **Important:** For those who use the `1.0` version, you can [jump to this
> documentation
> page](https://github.com/willdurand/Hateoas/blob/1.0/README.md#readme) though.

### Introduction

**Hateoas** leverages the [Serializer](github.com/schmittjoh/serializer) library
to provide a nice way to build HATEOAS REST web services. HATEOAS stands for
**H**ypermedia **a**s **t**he **E**ngine **o**f **A**pplication **S**tate, and
basically adds **hypermedia links** to your **representations** (ie. your API
responses). [HATEOAS is about the discoverability of actions on a
resource](http://timelessrepo.com/haters-gonna-hateoas).

For instance, let's say you have a User API that returns a **representation** of
a single _user_ as follow:

```json
{
    "user": {
        "id": 123,
        "first_name": "John",
        "last_name": "Doe"
    }
}
```

In order to tell your API consumers how to retrieve the data for this specific
user, you have to add your very first **link** to this representation, let's
call it `self` as it is the URI for this particular user:

```json
{
    "user": {
        "id": 123,
        "first_name": "John",
        "last_name": "Doe",
        "_links": {
            "self": { "href": "http://example.com/api/users/123" }
        }
    }
}
```

Generally speaking, a **resource** owns its own **actions** and `self` is a
well-known relation name. Let's dig into Hateoas now.


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

Hateoas is released under the MIT License. See the bundled LICENSE file for
details.
