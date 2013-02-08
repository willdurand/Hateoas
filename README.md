Hateoas
=======

[![Build Status](https://secure.travis-ci.org/willdurand/Hateoas.png)](http://travis-ci.org/willdurand/Hateoas)

A PHP library to support implementing representations for HATEOAS REST web services.


Installation
------------

Using [Composer](http://getcomposer.org), just require the `willdurand/hateoas`
package:

``` javascript
{
    "require": {
        "willdurand/hateoas": "@stable"
    }
}
```

Otherwise, install the library and setup the autoloader yourself.


Usage
-----

This library is under heavy development but basically it's a wrapper to add
hypermedia links to a resource or a collection of resources.

First of all, you have to wrap your data in a `Resource` object:

``` php
<?php

$resource = new Resource(array('foo' => 'bar'));
// or
$resource = new Resource($user);
```

Now, you are able to add links to this resource:

``` php
<?php

$resource->addLink(new Link('http://example.com/users/999', Link::REL_SELF));
$resource->addLink(new Link('http://example.com/users/999/friends', 'friends', 'application/vnd.acme.user'));
```


### LinkBuilder, CallableLinkBuilder

This library also provides a `LinkBuilder` which relies on a `RouterInterface`
instance under the hood. In Symfony2, you could use the `router` service as
shown in the following example, but **this library is not tied to Symfony2**.

``` php
<?php

// if you want to use the Symfony2 router in a Symfony2 project
$linkBuilder = new LinkBuilder($this->get('router'));

// Generate a "self" link
$selfLink = $linkBuilder->create('user_get', array('id' => $user->getId()), Link::REL_SELF);
$resource->addLink($selfLink);
```

The `LinkBuilder` has been described above. This builder uses the Symfony2
Router, but what if you don't use it? `CallableLinkBuilder` to the rescue!
This builder takes a `callable` as argument:

``` php
<?php

use Hateoas\Builder\CallableLinkBuilder;

$linkBuilder = new CallableLinkBuilder(function ($route, $parameters) use ($myRouter) {
    return $myRouter->generateRoute($route, $parameters);
});
```


### Note About The Serializer

Hateoas uses the [JMS Serializer](https://github.com/schmittjoh/serializer), and
hooks into it to provide nice features, **but** that also means you need to use
a configured serializer. Hateoas provides a configured serializer through
`Hateoas::getSerializer()`.

If you don't want to use this configured serializer, be sure to enable
annotations support, and to register the `Handler` bundled with Hateoas.

Also, be sure to enable annotations as Hateoas uses them.
The fastest way to activate annotations is to add the following line somewhere
near your autoloader configuration:

``` php
<?php

Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
```


### Using Factories and Builders

Hateoas provides factories and builders to generate `Resource` and `Link`
instances. A Factory takes a `ConfigInterface` object as argument.
That means you can use XML, YAML, annotations, etc. even if it's not yet
implemented in the library itself.


### Config

At the moment, Hateoas is bundled with an `ArrayConfig` and a `YamlConfig`
which relies on the Symfony2 Yaml component under the hood.

##### ArrayConfig

The `ArrayConfig` class takes two arrays as arguments.

##### YamlConfig

The `YamlConfig` class takes either a filename or a string containing a YAML
configuration as described below:

``` yaml
hateoas:
    resources:
        Acme\Entity\Location:
            - { route: 'location.get',          parameters: [ 'id' ], rel: 'self',      type: 'application/vnd.acme.location' }
            - { route: 'location.get_comments', parameters: [ 'id' ], rel: 'comments',  type: 'application/vnd.acme.comment' }

        Acme\Entity\Comment:
            - { route: 'comment.get',           parameters: [ 'id' ], rel: 'self',      type: 'application/vnd.acme.comment' }

    collections:
        Acme\Entity\Location:
            links:
                - { route: 'location.all', rel: 'self', type: 'application/vnd.acme.location' }

        Acme\Entity\Comment:
            links:
                - { route: 'comment.all', rel: 'self', type: 'application/vnd.acme.comment' }
```


### LinkDefinition, RouteLinkDefinition

In order to describe a `Link`, you need to define a `rel` attribute (and
optionally a `type`). If you are using Symfony2, you can describe a
`RouteLinkDefinition` so that you can define a `route` and its `parameters`:

``` php
<?php

$linkDefinition = array(
    'route'      => 'acme_demo.user_get',
    'parameters' => array('id'),
    'rel'        => Link::REL_SELF,
    'type'       => null
);
// or
$linkDefinition = new RouteLinkDefinition('acme_demo.user_get', array('id'), Link::REL_SELF);
```

> **Note:** you can use the `RouteLinkDefinition` even if you don't use the
> Symfony2 Router. It can be useful if your own router relies on the same
> principle (a route and its parameters).


### Factory, RouteAwareFactory

Now, you need a factory. Symfony2 users will be interested in the
`RouteAwareFactory`, others can implement their own _Factory_:

``` php
<?php

use Hateoas\Factory\RouteAwareFactory;

$factory = new RouteAwareFactory(
    new ArrayConfig(
        array(
            'Acme\DemoBundle\Model\User' => array(
                $linkDefinition,
                array(
                    'route'      => 'acme_demo.friend_get',
                    'parameters' => array('id'),
                    'rel'        => 'friends',
                    'type'       => 'application/vnd.acme.user'
                ),
            ),
        )
    )
);
```

This factory allows to create a `ResourceDefinition` by taking either an
instance or a classname. This definition contains a class name and a set of
`LinkDefinition`. The `RouteAwareFactory` described above allows to create
`RouteLinkDefinition` instances.


### ResourceBuilder

Now, you probably want to create resources using your configuration. Thanks to
the `ResourceBuilder` it's super easy. A `ResourceBuilder` needs a `LinkBuilder`
and a `Factory`:

``` php
<?php

use Hateoas\Builder\ResourceBuilder;

$resourceBuilder = new ResourceBuilder($factory, $linkBuilder);
```

Now, you can create a resource for a given object:

``` php
<?php

$resource = $resourceBuilder->create($user);
```

`$resource` is an instance of `Resource` and contains two `Link` (`self` and
`friends`).


### Playing With Collections

You need to pass a configuration array for your collections as second argument
of your `Factory`:

``` php
<?php

use Hateoas\Factory\RouteAwareFactory;

$factory = new RouteAwareFactory(
    new ArrayConfig(
        // single resource
        array(
            'Acme\DemoBundle\Model\User' => array(
                $linkDefinition,
                array(
                    'route'      => 'acme_demo.friend_get',
                    'parameters' => array('id'),
                    'rel'        => 'friends',
                    'type'       => 'application/vnd.acme.user'
                ),
            ),
        ),
        // collection
        array(
            'Acme\DemoBundle\Model\User' => array(
                array(
                    'route'      => 'acme_demo.user_all',
                    'rel'        => Link::REL_SELF,
                    'type'       => 'application/vnd.acme.users'
                ),
                array(
                    'route'      => 'acme_demo.user_all',
                    'parameters' => array('page'),
                    'rel'        => Link::REL_NEXT
                ),
            ),
        )
    )
);
```

Then, you just have to call the `createCollection()` method on the
`ResourceBuilder`:

```php
<?php

$collection = $resourceBuilder->createCollection(
    array($user1, $user2, ...),
    'Acme\DemoBundle\Model\User'
);
```


### Dealing With Child Properties

Both methods `create()` and `createCollection()` accept an optional parameter to
define child properties to iterate over. For example you have a Post with a
`author` property.

With the following code it adds also hyperlinks to the `author` object:

``` php
<?php

$resource = $resourceBuilder->create($user, array('objectProperties' => array('author')));
```


Examples
--------

### Example With A (Propel) Pager

Let's say you have a pager like the [Propel
Pager](http://www.propelorm.org/documentation/03-basic-crud.html#query_termination_methods),
you can configure a set of links for your collection:


``` php
$factory = new RouteAwareFactory(
    new ArrayConfig(
        // single resource
        array(
            'Acme\DemoBundle\Model\User' => array(
                $linkDefinition,
                array(
                    'route'      => 'acme_demo.friend_get',
                    'parameters' => array('id'),
                    'rel'        => 'friends',
                    'type'       => 'application/vnd.acme.user'
                ),
            ),
        ),
        // collection
        array(
            'Acme\DemoBundle\Model\User' => array(
                'links' => array(
                    array(
                        'route'      => 'acme_demo.user_all',
                        'parameters' => array('page'),
                        'rel'        => Link::REL_SELF,
                        'type'       => 'application/vnd.acme.user'
                    ),
                    array(
                        'route'      => 'acme_demo.user_all',
                        'parameters' => array('page' => 'firstPage'),
                        'rel'        => Link::REL_FIRST,
                        'type'       => 'application/vnd.acme.user'
                    ),
                    array(
                        'route'      => 'acme_demo.user_all',
                        'parameters' => array('page' => 'lastPage'),
                        'rel'        => Link::REL_LAST,
                        'type'       => 'application/vnd.acme.user'
                    ),
                    array(
                        'route'      => 'acme_demo.user_all',
                        'parameters' => array('page' => 'nextPage'),
                        'rel'        => Link::REL_NEXT,
                        'type'       => 'application/vnd.acme.user'
                    ),
                    array(
                        'route'      => 'acme_demo.user_all',
                        'parameters' => array('page' => 'previousPage'),
                        'rel'        => Link::REL_PREVIOUS,
                        'type'       => 'application/vnd.acme.user'
                    ),
                ),
                'attributes' => array(
                    'page'  => 'page',
                    'limit' => 'maxPerPage',
                    'total' => 'nbResults',
                )
            ),
        )
    )
);
```

Then, do:

```php
<?php

$collection = $resourceBuilder->createCollection(
    UserQuery::create()->paginate(), // returns an instance of ModelPager
    'Acme\DemoBundle\Model\User'
);
```

You will get the following output:

``` json
{
  "total": 1000,
  "page": 1,
  "limit": 10,
  "resources": [
    {
      "id": 999,
      "username": "xxxx",
      "email": "xxx@example.org",
      "_links": [
        {
          "href": "http://example.com/users/999",
          "rel": "self"
        }
      ]
    },
    // ...
  ],
  "_links": [
    {
      "href": "http://example.com/users?page=1",
      "rel": "self",
      "type": "application/vnd.acme.user"
    },
    {
      "href": "http://example.com/users?page=1",
      "rel": "previous",
      "type":"application/vnd.acme.user"
    },
    {
      "href": "http://example.com/users?page=2",
      "rel": "next",
      "type":"application/vnd.acme.user"
    },
    {
      "href": "http://example.com/users?page=1",
      "rel": "first",
      "type":"application/vnd.acme.user"
    },
    {
      "href": "http://example.com/users?page=100",
      "rel": "last",
      "type":"application/vnd.acme.user"
    }
  ]
}
```


License
-------

Hateoas is released under the MIT License. See the bundled LICENSE file for details.
