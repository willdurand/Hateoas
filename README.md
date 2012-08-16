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


License
-------

Hateoas is released under the MIT License. See the bundled LICENSE file for details.
