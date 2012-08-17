Hateoas
=======

A PHP 5.3 library to support implementing representations for HATEOAS REST web services.


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
$resource->addLink(new Link('http://example.com/users/999/friends', 'friends', 'application/vnd.acme.user+xml'));
```

This library also provides a `LinkBuilder` which relies on a `RouterInterface`
instance under the wood. In Symfony2, you could use the `router` service as
shown in the following example, but this library is not tied to Symfony2, just
to the Routing component by now.

``` php
<?php

$linkBuilder = new LinkBuilder($this->get('router'));

// Generate a "self" link
$selfLink = $linkBuilder->create('user_get', array('id' => $user->getId()), Link::REL_SELF);
$resource->addLink($selfLink);
```


Using Hateoas with FOSRestBundle
--------------------------------

Basically, instead of serializing your `$user` or your `array`, you just have to
serialize this `$resource` object. The only drawback is that you will get a
`data` structure. Thanks to the
[JMSSerializerBundle](https://github.com/schmittjoh/JMSSerializerBundle), you
can fix this issue:

``` yaml
# app/config/config.yml
jms_serializer:
    metadata:
        directories:
            hateoas:
                namespace_prefix: 'Hateoas'
                path: %kernel.root_dir%/config/serializer
```

``` yaml
# app/config/serializer/Resource.yml
Hateoas\Resource:
    properties:
        data:
            inline: true
```

Now, it will generate the following outputs according to previous examples:

``` xml
<user>
    <id>999</id>
    <username>xxxx</username>
    <email>xxxx@example.org</email>

    <link href="http://example.com/users/999" rel="self" />

    <link rel="friends"
          type="application/vnd.acme.user+xml"
          href="http://example.com/users/999/friends" />
</user>
```

``` json
{
  "user": {
    "id": 999,
      "username": "xxxx",
      "email": "xxx@example.org",
      "links": [
      {
        "href": "http://example.com/users/999",
        "rel": "self"
      },
      {
        "href": "http://example.com/users/999/friends",
        "rel": "friends",
        "type": "application/vnd.acme.user+xml"
      }
    ]
  }
}
```


Using Factories and Builders
----------------------------

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
    'Acme\DemoBundle\Model\User' => array($linkDefinition),
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

$resourceBuilder = ResourceBuilder($factory, $linkBuilder);
```

Now, you can create a resource for a given object:

``` php
<?php

$resource = $resourceBuilder->create($user);
```

`$resource` is an instance of `Resource` and contains a `Link` (the `self` one).


License
-------

Hateoas is released under the MIT License. See the bundled LICENSE file for details.
