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


### Resource

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

To sum up, a `Resource` contains **data** and **links** related to the data.


### RouteAwareLinkBuilder, CallableLinkBuilder

This library also provides a `RouteAwareLinkBuilder` which relies on a
`UrlGeneratorInterface` instance under the hood. In Symfony2, you could use the
`router` service as shown in the following example, but **this library is not
tied to Symfony2**.

``` php
<?php

// if you want to use the Symfony2 router in a Symfony2 project
$linkBuilder = new RouteAwareLinkBuilder($this->get('router'));

// in a Silex project
$linkBuilder = new RouteAwareLinkBuilder($app['url_generator']);

// Generate a "self" link
$selfLink = $linkBuilder->create('user_get', array('id' => $user->getId()), Link::REL_SELF);
$resource->addLink($selfLink);
```

The `RouteAwareLinkBuilder` has been described above. This builder uses the
Symfony2 Router, but what if you don't use it? `CallableLinkBuilder` to the
rescue! This builder takes a `callable` as argument:

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
            links:
                - { route: 'location.get',          parameters: [ 'id' ], rel: 'self',      type: 'application/vnd.acme.location' }
                - { route: 'location.get_comments', parameters: [ 'id' ], rel: 'comments',  type: 'application/vnd.acme.comment' }

        Acme\Entity\Comment:
            links:
                - { route: 'comment.get',           parameters: [ 'id' ], rel: 'self',      type: 'application/vnd.acme.comment' }

    collections:
        Acme\Entity\Location:
            rootName: locations
            links:
                - { route: 'location.all', rel: 'self', type: 'application/vnd.acme.location' }

        Acme\Entity\Comment:
            rootName: comments
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


### EmbedDefinition

You can load `embedded` resources from within a defined resource. You need to 
define a `name` for the collection you are embedding, and optionally an 
`accessor` to load it from the parent resource.

``` php
<?php

$embedDefinition = array(
    'name' => 'comments',
    'accessor' => 'TopComments'
);
//or
$embedDefinition = new EmbedDefinition('comments', 'TopComments');
```

> **Note:** The embedded resources are rendered as a sequence of individual
> resources, not as a collection.


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
                'links' => array(
                    $linkDefinition,
                    array(
                        'route'      => 'acme_demo.friend_get',
                        'parameters' => array('id'),
                        'rel'        => 'friends',
                        'type'       => 'application/vnd.acme.user'
                    )
                )
            )
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
the `ResourceBuilder` it's super easy. A `ResourceBuilder` needs a
`RouteAwareLinkBuilder` and a `Factory`:

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
                'links' => array(
                    $linkDefinition,
                    array(
                        'route'      => 'acme_demo.friend_get',
                        'parameters' => array('id'),
                        'rel'        => 'friends',
                        'type'       => 'application/vnd.acme.user'
                    )
                ),
                'embedded' => array(
                    $embedDefinition,
                    array('name' => 'best_friends', 'accessor' => 'BestFriends')
                )
            )
        ),
        // collection
        array(
            'Acme\DemoBundle\Model\User' => array(
                'links' => array(
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

A `Collection` contains a set of **resources**, **links** related to these
resources, and metadata about the collection. For instance, you can set the
`rootName` that is the name of the set of resources.

If you use pagination, you can set the number of resources (`total`), the
current `page`, and the `limit`.


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

### Quick Example

Let's say you have an application that manages a set of _locations_, and each
_location_ owns a set of _comments_. According to the YAML configuration shown
above, here are the outputs:

##### GET /locations

``` xml
<?xml version="1.0" encoding="UTF-8"?>
<locations>
  <link href="http://localhost:8081/locations" rel="self" type="application/vnd.example.location"/>
  <location>
    <link href="http://localhost:8081/locations/1" rel="self" type="application/vnd.example.location"/>
    <link href="http://localhost:8081/locations/1/comments" rel="comments" type="application/vnd.example.comment"/>
    <id><![CDATA[1]]></id>
    <name><![CDATA[test]]></name>
    <created_at><![CDATA[2013-02-07T18:37:03+0100]]></created_at>
  </location>
  <location>
    <link href="http://localhost:8081/locations/4" rel="self" type="application/vnd.example.location"/>
    <link href="http://localhost:8081/locations/4/comments" rel="comments" type="application/vnd.example.comment"/>
    <id><![CDATA[4]]></id>
    <name><![CDATA[foobar]]></name>
    <created_at><![CDATA[2013-02-07T18:37:04+0100]]></created_at>
  </location>
</locations>
```

``` json
{
   "locations" : [
      {

         "_links" : {
            "self": {
               "href" : "http://localhost:8081/locations/1",
               "type" : "application/vnd.example.location"
            },
            "comments": {
               "href" : "http://localhost:8081/locations/1/comments",
               "type" : "application/vnd.example.comment"
            }
         },
         "id" : "1",
         "name" : "test",
         "created_at" : "2013-02-07T18:37:03+0100",
      },
      {

         "_links" : {
            "self": {
               "href" : "http://localhost:8081/locations/4",
               "type" : "application/vnd.example.location"
            },
            "comments": {
               "href" : "http://localhost:8081/locations/4/comments",
               "type" : "application/vnd.example.comment"
            }
         },
         "id" : "4",
         "name" : "foobar",
         "created_at" : "2013-02-07T18:37:04+0100"
      }
   ],
   "_links" : {
      "self": {
         "rel" : "self",
         "href" : "http://localhost:8081/locations",
         "type" : "application/vnd.example.location"
      }
   }
}
```

The JSON representation of links follows the [Hypertext Application Language
specification](http://stateless.co/hal_specification.html).


##### GET /locations/4

``` xml
<?xml version="1.0" encoding="UTF-8"?>
<location>
  <link href="http://localhost:8081/locations/4" rel="self" type="application/vnd.example.location"/>
  <link href="http://localhost:8081/locations/4/comments" rel="comments" type="application/vnd.example.comment"/>
  <id><![CDATA[4]]></id>
  <name><![CDATA[foobar]]></name>
  <created_at><![CDATA[2013-02-07T18:37:04+0100]]></created_at>
</location>
```

``` json
{
   "_links" : {
      "self": {
         "href" : "http://localhost:8081/locations/4",
         "type" : "application/vnd.example.location"
      },
      "comments": {
         "href" : "http://localhost:8081/locations/4/comments",
         "type" : "application/vnd.example.comment"
      }
   },
   "id" : "4",
   "name" : "foobar",
   "created_at" : "2013-02-07T18:37:04+0100"
}
```


##### GET /locations/4/comments

``` xml
<?xml version="1.0" encoding="UTF-8"?>
<comments>
  <link href="http://localhost:8081/comments" rel="self" type="application/vnd.example.comment"/>
  <comment>
    <link href="http://localhost:8081/comments/8" rel="self" type="application/vnd.example.comment"/>
    <id><![CDATA[8]]></id>
    <username><![CDATA[anonymous]]></username>
    <body><![CDATA[]]></body>
    <created_at><![CDATA[2013-02-07T19:41:14+0100]]></created_at>
  </comment>
</comments>
```

``` json
{
   "comments" : [
      {
         "_links" : {
            "self": {
               "href" : "http://localhost:8081/comments/8",
               "type" : "application/vnd.example.comment"
            }
         },
         "id" : "8",
         "username" : "anonymous",
         "body" : "",
         "created_at" : "2013-02-07T19:41:14+0100"
      },
   ],
   "_links" : {
      "self": {
         "href" : "http://localhost:8081/comments",
         "type" : "application/vnd.example.comment"
      }
   }
}
```


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
      "_links": {
        "self": {
          "href": "http://example.com/users/999"
        }
      }
    },
    // ...
  ],
  "_links": {
    "self": {
      "href": "http://example.com/users?page=1",
      "type": "application/vnd.acme.user"
    },
    "previous": {
      "href": "http://example.com/users?page=1",
      "type":"application/vnd.acme.user"
    },
    "next": {
      "href": "http://example.com/users?page=2",
      "type":"application/vnd.acme.user"
    },
    "first": {
      "href": "http://example.com/users?page=1",
      "type":"application/vnd.acme.user"
    },
    "last": {
      "href": "http://example.com/users?page=100",
      "type":"application/vnd.acme.user"
    }
  }
}
```


License
-------

Hateoas is released under the MIT License. See the bundled LICENSE file for details.
