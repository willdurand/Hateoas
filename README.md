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
    "willdurand/hateoas": "dev-master"
  }
}
```

Otherwise, install the library and setup the autoloader yourself.


Usage
-----

This library is under heavy development but basically it's a wrapper to add
hypermedia links to a resource. First, you need to wrap your data in a
`Resource` object:

``` php
<?php

$resource = new Resource(array('foo' => 'bar'));
// or
$resource = new Resource($user);
```

Then, you will have to add links to this resource:

``` php
<?php

$resource->addLink(new Link('http://example.com/users/999', Link::REL_SELF));
$resource->addLink(new Link('http://example.com/users/999/friends', 'friends', 'application/vnd.acme.user'));
```

This library also provides a `LinkBuilder` which relies on a `RouterInterface`
instance under the hood. In Symfony2, you could use the `router` service as
shown in the following example, but this library is not tied to Symfony2. Only
builders need the Routing and the Form components by now.

``` php
<?php

$linkBuilder = new LinkBuilder($this->get('router'));

// Generate a "self" link
$selfLink = $linkBuilder->create('user_get', array('id' => $user->getId()), Link::REL_SELF);
$resource->addLink($selfLink);
```


### Using Factories and Builders

Hateoas provides factories and builders to generate `Resource` and `Link`
instances. A Factory takes a configuration as an array. That means you can use
XML, YAML, annotations, etc. even if it's not yet implemented in the library
itself.

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

Now, you can create a factory. Symfony2 users will be interested in the
`RouteAwareFactory`:

``` php
<?php

use Hateoas\Factory\RouteAwareFactory;

$factory = new RouteAwareFactory(array(
    'Acme\DemoBundle\Model\User' => array(
        $linkDefinition,
        array(
            'route'      => 'acme_demo.friend_get',
            'parameters' => array('id'),
            'rel'        => 'friends',
            'type'       => 'application/vnd.acme.user'
        ),
    ),
));
```

This factory allows to create a `ResourceDefinition` by taking either an
instance or a classname. This definition contains a class name and a set of
`LinkDefinition`. The `RouteAwareFactory` described above allows to create
`RouteLinkDefinition`.

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

You need to pass a configuration array for your collections as second argument
of your `Factory`:

``` php
<?php

use Hateoas\Factory\RouteAwareFactory;

$factory = new RouteAwareFactory(
    // Entities
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
    // Collections
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

Both methods `create()` and `createCollection()` accept a optional parameter to
define child properties to iterate over. For example you have a Post with a
`author` property.

With the following code it adds also hyperlinks to the `author` object:

``` php
<?php

$resource = $resourceBuilder->create($user, array('objectProperties' => array('author')));
```

Let's say you have a pager like the [Propel
Pager](http://www.propelorm.org/documentation/03-basic-crud.html#query_termination_methods),
you can configure a set of links for your collection:


``` php
$factory = new RouteAwareFactory(
    // Entities
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
    // Collections
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
);
```

Then, just do:

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
