Hateoas
=======

[![Build
Status](https://secure.travis-ci.org/willdurand/Hateoas.png)](http://travis-ci.org/willdurand/Hateoas)
[![Scrutinizer Quality
Score](https://scrutinizer-ci.com/g/willdurand/Hateoas/badges/quality-score.png?s=45b5a825f99de4d29c98b5103f59e060139cf354)](https://scrutinizer-ci.com/g/willdurand/Hateoas/)
[![Latest Stable
Version](https://poser.pugx.org/willdurand/hateoas/v/stable.png)](https://packagist.org/packages/willdurand/hateoas)
![PHP7 ready](https://img.shields.io/badge/PHP7-ready-green.svg)

A PHP library to support implementing representations for HATEOAS REST web
services.


* [Installation](#installation)
  - [Working With Symfony2](#working-with-symfony2)
* [Usage](#usage)
  - [Introduction](#introduction)
  - [Configuring Links](#configuring-links)
  - [Embedding Resources](#embedding-resources)
  - [Dealing With Collections](#dealing-with-collections)
  - [Representations](#representations)
    - [VndErrorRepresentation](#vnderrorrepresentation)
  - [The Expression Language](#the-expression-language)
    - [Context](#context)
    - [Adding Your Own Context Variables](#adding-your-own-context-variables)
    - [Expression Functions](#expression-functions)
  - [URL Generators](#url-generators)
  - [Helpers](#helpers)
    - [LinkHelper](#linkhelper)
  - [Twig Extensions](#twig-extensions)
    - [LinkExtension](#linkextension)
  - [Serializers & Formats](#serializers--formats)
    - [The JsonHalSerializer](#the-jsonhalserializer)
    - [The XmlSerializer](#the-xmlserializer)
    - [The XmlHalSerializer](#the-xmlhalserializer)
    - [Adding New Serializers](#adding-new-serializers)
  - [The HateoasBuilder](#the-hateoasbuilder)
    - [XML Serializer](#xml-serializer)
    - [JSON Serializer](#json-serializer)
    - [URL Generator](#url-generator)
    - [Expression Evaluator/Expression Language](#expression-evaluatorexpression-language)
    - [Relation Provider](#relation-provider)
    - [(JMS) Serializer Specific](#jms-serializer-specific)
    - [Others](#others)
  - [Configuring a Cache Directory](#configuring-a-cache-directory)
  - [Configuring Metadata Locations](#configuring-metadata-locations)
  - [Extending The Library](#extending-the-library)
* [Reference](#reference)
  - [XML](#xml)
  - [YAML](#yaml)
  - [Annotations](#annotations)
    - [@Relation](#relation)
    - [@Route](#route)
    - [@Embedded](#embedded)
    - [@Exclusion](#exclusion)
    - [@RelationProvider](#relationprovider)
* [Internals](#internals)


Installation
------------

The recommended way to install Hateoas is through
[Composer](http://getcomposer.org/). Require the `willdurand/hateoas` package
by running the following command:

```sh
composer require willdurand/hateoas
```

This will resolve the latest stable version.

Otherwise, install the library and setup the autoloader yourself.

### Working With Symfony2

There is a bundle for that! Install the
[BazingaHateoasBundle](https://github.com/willdurand/BazingaHateoasBundle), and
enjoy!


Usage
-----

> **Important:** For those who use the `1.0` version, you can [jump to this
> documentation
> page](https://github.com/willdurand/Hateoas/blob/1.0/README.md#readme) as the
> following documentation has been written for **Hateoas 2.0** and above.

### Introduction

**Hateoas** leverages the [Serializer](https://github.com/schmittjoh/serializer)
library to provide a nice way to build HATEOAS REST web services. HATEOAS stands
for **Hypermedia as the Engine of Application State**,
and adds **hypermedia links** to your **representations** (i.e. your API
responses). [HATEOAS is about the discoverability of actions on a
resource](http://timelessrepo.com/haters-gonna-hateoas).

For instance, let's say you have a User API which returns a **representation**
of a single _user_ as follow:

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

Let's dig into Hateoas now.


### Configuring Links

In Hateoas terminology, **links** are seen as **relations** added to resources.
It is worth mentioning that **relations** also refer to **embedded resources**
too, but this topic will be covered in the [Embedding
Resources](#embedding-resources) section.

A link is a relation which is identified by a `name` (e.g. `self`) and that
has an `href` parameter:

```php
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Serializer\XmlRoot("user")
 *
 * @Hateoas\Relation("self", href = "expr('/api/users/' ~ object.getId())")
 */
class User
{
    /** @Serializer\XmlAttribute */
    private $id;
    private $firstName;
    private $lastName;

    public function getId() {}
}
```

In the example above, we configure a `self` relation that is a link because of
the `href` parameter. Its value, which may look weird at first glance, will be
extensively covered in [The Expression Language](#the-expression-language)
section. This special value is used to generate a URI.

In this section, [**annotations**](#annotations) are used to configure Hateoas.
[**XML**](#xml) and [**YAML**](#yaml) formats are also supported. If you wish,
you can use plain PHP too.

**Important:** you must configure both the Serializer and Hateoas the same way. E.g.
if you use YAML for configuring Serializer, use YAML for configuring Hateoas.

The easiest way to try HATEOAS is with the `HateoasBuilder`. The builder has
numerous methods to configure the Hateoas serializer, but we won't dig into
them right now (see [The HateoasBuilder](#the-hateoasbuilder)).
Everything works fine out of the box:

```php
use Hateoas\HateoasBuilder;

$hateoas = HateoasBuilder::create()->build();

$user = new User(42, 'Adrien', 'Brault');
$json = $hateoas->serialize($user, 'json');
$xml  = $hateoas->serialize($user, 'xml');
```

The `$hateoas` object is an instance of `JMS\Serializer\SerializerInterface`,
coming from the Serializer library. Hateoas does not come with its own
serializer, it hooks into the JMS Serializer.

By default, Hateoas uses the [Hypertext Application
Language](http://stateless.co/hal_specification.html) (HAL) for JSON
serialization. This specifies the _structure_ of the response (e.g. that
"links" should live under a `_links` key):

```json
{
    "id": 42,
    "first_name": "Adrien",
    "last_name": "Brault",
    "_links": {
        "self": {
            "href": "/api/users/42"
        }
    }
}
```

For XML, [Atom Links](http://tools.ietf.org/search/rfc4287#section-4.2.7)
are used by default:

```xml
<user id="42">
    <first_name><![CDATA[Adrien]]></first_name>
    <last_name><![CDATA[Brault]]></last_name>
    <link rel="self" href="/api/users/42"/>
</user>
```

It is worth mentioning that these formats are the **default ones**, not the
only available ones. You can use [different formats through different
serializers, and even add your owns](#serializers--formats).

Now that you know how to add **links**, let's see how to add **embedded
resources**.

### Embedding Resources

Sometimes, it's more efficient to embed related resources rather than
link to them, as it prevents clients from having to make extra requests to
fetch those resources.

An **embedded resource** is a named **relation** that contains data, represented
by the `embedded` parameter.

```php
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * ...
 *
 * @Hateoas\Relation(
 *     "manager",
 *     href = "expr('/api/users/' ~ object.getManager().getId())",
 *     embedded = "expr(object.getManager())",
 *     exclusion = @Hateoas\Exclusion(excludeIf = "expr(object.getManager() === null)")
 * )
 */
class User
{
    ...

    /** @Serializer\Exclude */
    private $manager;
}
```

**Note:** You will need to exclude the manager property from the serialization,
otherwise both the serializer and Hateoas will serialize it.
You will also have to exclude the manager relation when the manager is `null`,
because otherwise an error will occur when creating the `href` link (calling
`getId()` on `null`).

**Tip:** If the manager property is an object that already has a `_self`
link, you can re-use that value for the `href` instead of repeating it here.
See [LinkHelper](#linkhelper).

```php
$hateoas = HateoasBuilder::create()->build();

$user = new User(42, 'Adrien', 'Brault', new User(23, 'Will', 'Durand'));
$json = $hateoas->serialize($user, 'json');
$xml  = $hateoas->serialize($user, 'xml');
```

For `json`, the HAL representation places these embedded relations inside
an `_embedded` key:

```json
{
    "id": 42,
    "first_name": "Adrien",
    "last_name": "Brault",
    "_links": {
        "self": {
            "href": "/api/users/42"
        },
        "manager": {
            "href": "/api/users/23"
        }
    },
    "_embedded": {
        "manager": {
            "id": 23,
            "first_name": "Will",
            "last_name": "Durand",
            "_links": {
                "self": {
                    "href": "/api/users/23"
                }
            }
        }
    }
}
```

In XML, serializing `embedded` relations will create new elements:

```xml
<user id="42">
    <first_name><![CDATA[Adrien]]></first_name>
    <last_name><![CDATA[Brault]]></last_name>
    <link rel="self" href="/api/users/42"/>
    <link rel="manager" href="/api/users/23"/>
    <manager rel="manager" id="23">
        <first_name><![CDATA[Will]]></first_name>
        <last_name><![CDATA[Durand]]></last_name>
        <link rel="self" href="/api/users/23"/>
    </manager>
</user>
```

The tag name of an embedded resource is inferred from the
[`@XmlRoot`](http://jmsyst.com/libs/serializer/master/reference/annotations#xmlroot)
annotation (`xml_root_name` in YAML, `xml-root-name` in XML) coming from the
Serializer configuration.

### Dealing With Collections

The library provides several classes in the `Hateoas\Representation\*`
namespace to help you with common tasks. These are simple classes configured
with the library's annotations.

The `PaginatedRepresentation`, `OffsetRepresentation` and `CollectionRepresentation` classes are
probably the most interesting ones. These are helpful when your resource is
actually a collection of resources (e.g. `/users` is a collection of users).
These help you represent the collection and add pagination and limits:

```php
use Hateoas\Representation\PaginatedRepresentation;
use Hateoas\Representation\CollectionRepresentation;

$paginatedCollection = new PaginatedRepresentation(
    new CollectionRepresentation(
        array($user1, $user2, ...),
        'users', // embedded rel
        'users'  // xml element name
    ),
    'user_list', // route
    array(), // route parameters
    1,       // page number
    20,      // limit
    4,       // total pages
    'page',  // page route parameter name, optional, defaults to 'page'
    'limit', // limit route parameter name, optional, defaults to 'limit'
    false,   // generate relative URIs, optional, defaults to `false`
    75       // total collection size, optional, defaults to `null`
);

$json = $hateoas->serialize($paginatedCollection, 'json');
$xml  = $hateoas->serialize($paginatedCollection, 'xml');
```

The `CollectionRepresentation` class allows you to dynamically configure the
collection resources rel, and the xml root element name.

The `PaginatedRepresentation` is designed to add `self`, `first`, and when
possible `last`, `next`, and `previous` links.

The `OffsetRepresentation` works just like `PaginatedRepresentation` but is useful
when pagination is expressed by `offset`, `limit` and `total`.

The `RouteAwareRepresentation` adds a `self` relation based on a given route.

You can generate **absolute URIs** by setting the `absolute` parameter to `true`
in both the `PaginatedRepresentation` and the `RouteAwareRepresentation`.

The Hateoas library also provides a `PagerfantaFactory` to easily build
`PaginatedRepresentation` from a
[Pagerfanta](https://github.com/whiteoctober/Pagerfanta) instance. If you use
the Pagerfanta library, this is an easier way to create the collection
representations:

```php
use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;

$pagerfantaFactory   = new PagerfantaFactory(); // you can pass the page,
                                                // and limit parameters name
$paginatedCollection = $pagerfantaFactory->createRepresentation(
    $pager,
    new Route('user_list', array())
);

$json = $hateoas->serialize($paginatedCollection, 'json');
$xml  = $hateoas->serialize($paginatedCollection, 'xml');
```

You would get the following JSON content:

```json
{
    "page": 1,
    "limit": 10,
    "pages": 1,
    "_links": {
        "self": {
            "href": "/api/users?page=1&limit=10"
        },
        "first": {
            "href": "/api/users?page=1&limit=10"
        },
        "last": {
            "href": "/api/users?page=1&limit=10"
        }
    },
    "_embedded": {
        "items": [
            { "id": 123 },
            { "id": 456 }
        ]
    }
}
```

And the following XML content:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<collection page="1" limit="10" pages="1">
    <user id="123"></user>
    <user id="456"></user>
    <link rel="self" href="/api/users?page=1&amp;limit=10" />
    <link rel="first" href="/api/users?page=1&amp;limit=10" />
    <link rel="last" href="/api/users?page=1&amp;limit=10" />
</collection>
```

If you want to customize the inlined `CollectionRepresentation`, pass one as
third argument of the `createRepresentation()` method:

```php
use Hateoas\Representation\Factory\PagerfantaFactory;

$pagerfantaFactory   = new PagerfantaFactory(); // you can pass the page and limit parameters name
$paginatedCollection = $pagerfantaFactory->createRepresentation(
    $pager,
    new Route('user_list', array()),
    new CollectionRepresentation(
        $pager->getCurrentPageResults(),
        'users',
        'users',
        new Exclusion(...)
    )
);

$json = $hateoas->serialize($paginatedCollection, 'json');
$xml  = $hateoas->serialize($paginatedCollection, 'xml');
```

If you want to change the xml root name of the collection, create a new
class with the xml root configured and use the inline mechanism:

```php
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("users")
 */
class UsersRepresentation
{
    /**
     * @Serializer\Inline
     */
    private $inline;

    public function __construct($inline)
    {
        $this->inline = $inline;
    }
}

$paginatedCollection = ...;
$paginatedCollection = new UsersRepresentation($paginatedCollection);
```

### Representations

As mentionned in the previous section, **representations** are classes configured
with the library's annotations in order to help you with common tasks. The
**collection representations** are described in [Dealing With
Collection](#dealing-with-collections).

#### VndErrorRepresentation

The `VndErrorRepresentation` allows you to describe an error response following
the [`vnd.error` specification](https://github.com/blongden/vnd.error).

```php
$error = new VndErrorRepresentation(
    'Validation failed',
    42,
    new Relation('help', 'http://.../', null, array('title' => 'Error Information')),
    new Relation('describes', 'http://.../', null, array('title' => 'Error Description'))
);
```

Serializing such a representation in XML and JSON would give you the following
outputs:

```xml
<?xml version="1.0" encoding="UTF-8"?>
    <resource logref="42">
    <message><![CDATA[Validation failed]]></message>
    <link rel="help" href="http://.../" title="Error Information"/>
    <link rel="describes" href="http://.../" title="Error Description"/>
</resource>
```

```json
{
    "message": "Validation failed",
    "logref": 42,
    "_links": {
        "help": {
            "href": "http://.../",
            "title": "Error Information"
        },
        "describes": {
            "href": "http://.../",
            "title": "Error Description"
        }
    }
}
```

**Hint:** it is recommended to create your own error classes that extend the
`VndErrorRepresentation` class.

### The Expression Language

Hateoas relies on the powerful Symfony
[ExpressionLanguage](http://symfony.com/doc/current/components/expression_language/introduction.html)
component to retrieve values such as links, ids or objects to embed.

Each time you fill in a value (e.g. a Relation `href` in annotations or YAML),
you can either pass a **hardcoded value** or an **expression**.
In order to use the Expression Language, you have to use the `expr()` notation:

```php
/**
 * @Hateoas\Relation("self", href = "expr('/api/users/' ~ object.getId())")
 */
```

You can learn more about the Expression Syntax by reading the official
documentation: [The Expression
Syntax](http://symfony.com/doc/current/components/expression_language/syntax.html).

#### Context

Natively, a special variable named `object` is available in each expression, and
represents the current object:

```
expr(object.getId())
```

We call such a variable a **context variable**.

You can add your own context variables to the Expression Language context by
adding them to the `ExpressionEvaluator`.

##### Adding Your Own Context Variables

Using the `HateoasBuilder`, call the `setExpressionContextVariable()` method to add
new context variables:

```php
use Hateoas\HateoasBuilder;

$hateoas = HateoasBuilder::create()
    ->setExpressionContextVariable('foo', new Foo())
    ->build();
```

The `foo` variable is now available:

```
expr(foo !== null)
```

##### Expression Functions

**Expression Functions** are custom functions used to extend the [Expression
Language](#the-expression-language) as explained in the [Extending the
ExpressionLanguage](http://symfony.com/doc/current/components/expression_language/extending.html),
part of the Symfony documentation.

Hateoas provides core expression functions such as the `LinkExpressionFunction`
described in [LinkHelper - The `link` Function](#the-link-function), but you can
also write your own function.
The `ExpressionFunctionInterface` is designed to represent an expression
function. Adding a new expression function is a matter of implementing this
interface and registering by calling the `registerExpressionFunction()` method
on the [HateoasBuilder](#the-hateoasbuilder).

### URL Generators

Since you can use the [Expression Language](#the-expression-language) to define
the relations links (`href` key), you can do a lot by default. However if you
are using a framework, chances are that you will want to use routes to build
links.

You will first need to configure an `UrlGenerator` on the builder. You can
either implement the `Hateoas\UrlGenerator\UrlGeneratorInterface`, or use the
`Hateoas\UrlGenerator\CallableUrlGenerator`:

```php
use Hateoas\UrlGenerator\CallableUrlGenerator;

$hateoas = HateoasBuilder::create()
    ->setUrlGenerator(
        null, // By default all links uses the generator configured with the null name
        new CallableUrlGenerator(function ($route, array $parameters, $absolute) use ($myFramework) {
            return $myFramework->generateTheUrl($route, $parameters, $absolute);
        })
    )
    ->build()
;
```

You will then be able to use the [@Route](#route) annotation:

```php
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "user_get",
 *          parameters = {
 *              "id" = "expr(object.getId())"
 *          }
 *      )
 * )
 */
class User
```

```json
{
    "id": 42,
    "first_name": "Adrien",
    "last_name": "Brault",
    "_links": {
        "self": {
            "href": "/api/users/42"
        }
    }
}
```

Note that the library comes with a `SymfonyUrlGenerator`. For example, to use it
in Silex:

```php
use Hateoas\UrlGenerator\SymfonyUrlGenerator;

$hateoas = HateoasBuilder::create()
    ->setUrlGenerator(null, new SymfonyUrlGenerator($app['url_generator']))
    ->build()
;
```

### Helpers

Hateoas provides a set of helpers to ease the process of building APIs.

#### LinkHelper

The `LinkHelper` class provides a `getLinkHref($object, $rel, $absolute = false)`
method that allows you to get the _href_ value of any object, for any given
relation name. It is able to generate a URI (either absolute or relative) from
any **link** relation:

```php
$user = new User(123, 'William', 'Durand');

$linkHelper->getLinkHref($user, 'self');
// /api/users/123

$linkHelper->getLinkHref($user, 'self', true);
// http://example.com/api/users/123
```

##### The `link` Function

The feature above is also available in your expressions (cf. [The Expression
Language](#the-expression-language)) through the `link(object, rel, absolute)`
**function**:

```php
/**
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route("post_get", parameters = {"id" = "expr(object.getId())"})
 * )
 */
class Post {}

/**
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route("user_get", parameters = {"id" = "expr(object.getId())"})
 * )
 * @Hateoas\Relation(
 *     "post",
 *     href = "expr(link(object.getPost(), 'self', true))"
 * )
 * @Hateoas\Relation(
 *     "relative",
 *     href = "expr(link(object.getRelativePost(), 'self'))"
 * )
 */
class User
{
    ...

    public function getPost()
    {
        return new Post(456);
    }

    public function getRelativePost()
    {
        return new Post(789);
    }
}
```

Pay attention to the `href` expressions for the `post` and `relative` relations,
as well as their corresponding values in the following JSON content:

```json
{
    "user": {
        "id": 123,
        "first_name": "William",
        "last_name": "Durand",
        "_links": {
            "self": { "href": "http://example.com/api/users/123" },
            "post": { "href": "http://example.com/api/posts/456" },
            "relative": { "href": "/api/posts/789" }
        }
    }
}
```

It is worth mentioning that you can **force** whether you want an absolute or
relative URI by using the third argument in both the `getLinkHref()` method and
the `link` function.

**Important:** by default, all URIs will be **relative**, even those which are
defined as **absolute** in their configuration.

```php
$linkHelper->getLinkHref($user, 'post');
// /api/posts/456

$linkHelper->getLinkHref($user, 'post', true);
// http://example.com/api/posts/456

$linkHelper->getLinkHref($user, 'relative');
// /api/posts/789

$linkHelper->getLinkHref($user, 'relative', true);
// http://example.com/api/posts/789
```

### Twig Extensions

Hateoas also provides a set of [Twig](http://twig.sensiolabs.org) extensions.

#### LinkExtension

The `LinkExtension` allows you to use the [LinkHelper](#linkhelper) into your
Twig templates, so that you can generate links in your HTML templates for
instance.

This extension exposes the `getLinkHref()` helper's method through the
`link_href` Twig function:

```html+jinja
{{ link_href(user, 'self') }}
{# will generate: /users/123 #}

{{ link_href(will, 'self', false) }}
{# will generate: /users/123 #}

{{ link_href(will, 'self', true) }}
{# will generate: http://example.com/users/123 #}
```

### Serializers & Formats

Hateoas provides a set of **serializers**. Each **serializer** allows you to
generate either XML or JSON content following a specific **format**, such as
[HAL](http://stateless.co/hal_specification.html), or [Atom
Links](http://tools.ietf.org/search/rfc4287#section-4.2.7) for instance.

#### The JsonHalSerializer

The `JsonHalSerializer` allows you to generate HAL compliant relations in JSON.
It is the default JSON serializer in Hateoas.

HAL provides its linking capability with a convention which says that a resource
object has a reserved property called `_links`. This property is an object that
contains links. These links are key'ed by their link relation.

HAL also describes another convention which says that a resource may have
another reserved property named `_embedded`. This property is similar to `_links`
in that embedded resources are key'ed by relation name. The main difference is
that rather than being links, the values are resource objects.

![](http://stateless.co/info-model.png)

```json
{
    "message": "Hello, World!",
    "_links": {
        "self": {
            "href": "/notes/0"
        }
    },
    "_embedded": {
        "associated_events": [
            {
                "name": "SymfonyCon",
                "date": "2013-12-12T00:00:00+0100"
            }
        ]
    }
}
```

#### The XmlSerializer

The `XmlSerializer` allows you to generate [Atom
Links](http://tools.ietf.org/search/rfc4287#section-4.2.7) into your XML
documents. It is the default XML serializer.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<note>
    <message><![CDATA[Hello, World!]]></message>
    <link rel="self" href="/notes/0" />
    <events rel="associated_events">
        <event>
            <name><![CDATA[SymfonyCon]]></name>
            <date><![CDATA[2013-12-12T00:00:00+0100]]></date>
        </event>
    </events>
</note>
```

#### The XmlHalSerializer

The `XmlHalSerializer` allows you to generate HAL compliant relations in XML.

HAL in XML is similar to [HAL in JSON](#the-jsonhalserializer) in the sense that
it describes `link` tags and `resource` tags.

**Note:** the `self` relation will actually become an attribute of the main
resource instead of being a `link` tag. Other links will be generated as `link`
tags.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<note href="/notes/0">
    <message><![CDATA[Hello, World!]]></message>

    <resource rel="associated_events">
        <name><![CDATA[SymfonyCon]]></name>
        <date><![CDATA[2013-12-12T00:00:00+0100]]></date>
    </resource>
</note>
```

#### Adding New Serializers

For JSON related formats, you must implement the `JsonSerializerInterface`
interface, and for XML related formats, you must implement the
`XmlSerializerInterface`. Both interfaces describe two methods to serialize
**links** and **embedded** relations.

### The HateoasBuilder

The `HateoasBuilder` class is used to easily configure Hateoas thanks to a
powerful and fluent API.

```php
use Hateoas\HateoasBuilder;

$hateoas = HateoasBuilder::create()
    ->setCacheDir('/path/to/cache/dir')
    ->setDebug($trueOrFalse)
    ->setDefaultXmlSerializer()
    ...
    ->build();
```

All the methods below return the current builder, so that you can chain them.

#### XML Serializer

* `setXmlSerializer(XmlSerializerInterface $xmlSerializer)`: sets the XML
  serializer to use. Default is: `XmlSerializer`;
* `setDefaultXmlSerializer()`: sets the default XML serializer
  (`XmlSerializer`).

#### JSON Serializer

* `setJsonSerializer(JsonSerializerInterface $jsonSerializer)`: sets the JSON
  serializer to use. Default is: `JsonHalSerializer`;
* `setDefaultJsonSerializer()`: sets the default JSON serializer
  (`JsonHalSerializer`).

#### URL Generator

* `setUrlGenerator($name = null, UrlGeneratorInterface $urlGenerator)`: adds a
  new named URL generator. If `$name` is `null`, the URL generator will be the
  default one.

#### Expression Evaluator/Expression Language

* `setExpressionContextVariable($name, $value)`: adds a new expression context
  variable;
* `setExpressionLanguage(ExpressionLanguage $expressionLanguage)`;
* `registerExpressionFunction(ExpressionFunctionInterface
   $expressionFunction)`: adds a new expression function.

#### Relation Provider

* `addRelationProviderResolver(RelationProviderResolverInterface $resolver)`:
  adds a new relation provider resolver.

#### (JMS) Serializer Specific

* `includeInterfaceMetadata($include)`: whether to include the metadata from the
  interfaces;
* `setMetadataDirs(array $namespacePrefixToDirMap)`: sets a map of namespace
  prefixes to directories. This method overrides any previously defined
  directories;
* `addMetadataDir($dir, $namespacePrefix = '')`: adds a directory where the
  serializer will look for class metadata;
* `addMetadataDirs(array $namespacePrefixToDirMap)`: adds a map of namespace
  prefixes to directories;
* `replaceMetadataDir($dir, $namespacePrefix = '')`: similar to
  `addMetadataDir()`, but overrides an existing entry.

Please read the official [Serializer
documentation](http://jmsyst.com/libs/serializer) for more details.

#### Others

* `setDebug($debug)`: enables or disables the debug mode;
* `setCacheDir($dir)`: sets the cache directory.

### Configuring a Cache Directory

Both the serializer and the Hateoas libraries collect metadata about your
objects from various sources such as YML, XML, or annotations. In order to make
this process as efficient as possible, it is recommended that you allow the
Hateoas library to cache this information. To do that, configure a cache
directory:

```php
$builder = \Hateoas\HateoasBuilder::create();

$hateoas = $builder
    ->setCacheDir($someWritableDir)
    ->build();
```

### Configuring Metadata Locations

Hateoas supports several metadata sources. By default, it uses Doctrine
annotations, but you may also store metadata in XML, or YAML files. For the
latter, it is necessary to configure a metadata directory where those files are
located:

```php
$hateoas = \Hateoas\HateoasBuilder::create()
    ->addMetadataDir($someDir)
    ->build();
```

Hateoas would expect the metadata files to be named like the fully qualified
class names where all `\` are replaced with `.`. If you class would be named
`Vendor\Package\Foo` the metadata file would need to be located at
`$someDir/Vendor.Package.Foo.(xml|yml)`.

### Extending The Library

Hateoas allows frameworks to dynamically add relations to classes by providing
an extension point at configuration level. This feature can be useful for those
who want to to create a new layer on top of Hateoas, or to add "global"
relations rather than copying the same configuration on each class.

In order to leverage this mechanism, the `ConfigurationExtensionInterface`
interface has to be implemented:

```php
use Hateoas\Configuration\Metadata\ConfigurationExtensionInterface;
use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use Hateoas\Configuration\Relation;

class AcmeFooConfigurationExtension implements ConfigurationExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function decorate(ClassMetadataInterface $classMetadata)
    {
        if (0 === strpos('Acme\Foo\Model', $classMetadata->getName())) {
            // Add a "root" relation to all classes in the `Acme\Foo\Model` namespace
            $classMetadata->addRelation(
                new Relation(
                    'root',
                    '/'
                )
            );
        }
    }
}
```

You can access the existing relations loaded from Annotations, XML, or YAML with
`$classMetadata->getRelations()`.

If the `$classMetadata` has relations, or if you add relations to it, its
relations will be cached. So if you read configuration files (Annotations, XML,
or YAML), make sure to reference them on the class metadata:

```php
$classMetadata->fileResources[] = $file;
```


Reference
---------

### XML

```xml
<?xml version="1.0" encoding="UTF-8"?>
<serializer>
<class name="Acme\Demo\Representation\User" h:providers="Class::getRelations, getRelations" xmlns:h="https://github.com/willdurand/Hateoas">
        <h:relation rel="self">
            <h:href uri="http://acme.com/foo/1" />
        </h:relation>
        <h:relation rel="expr(object.getFriendsDynamicRel())">
            <h:href route="user_friends" generator="my_custom_generator">
                <h:parameter name="id" value="expr(object.getId())" />
                <h:parameter name="page" value="1" />
            </h:ref>
            <h:embedded xml-element-name="users">
                <h:content>expr(object.getFriends())</h:content>
                <h:exclusion ... />
            </h:embedded>
            <h:exclusion groups="Default, user_full" since-version="1.0" until-version="2.2" exclude-if="expr(object.getFriends() === null)" />
        </h:relation>
    </class>
</serializer>
```
See the
[`hateoas.xsd`](https://github.com/willdurand/Hateoas/blob/master/hateoas.xsd)
file for more details.

### YAML

```yaml
Acme\Demo\Representation\User:
    relations:
        -
            rel: self
            href: http://acme.com/foo/1
        -
            rel: expr(object.getFriendsDynamicRel())
            href:
                route: user_friends
                parameters:
                    id: expr(object.getId())
                    page: 1
                generator: my_custom_generator
                absolute: false
            embedded:
                content: expr(object.getFriends())
                xmlElementName: users
                exclusion: ...
            exclusion:
                groups: [Default, user_full]
                since_version: 1.0
                until_version: 2.2
                exclude_if: expr(object.getFriends() === null)

    relation_providers: [ 'Class::getRelations', 'getRelations' ]
```

### Annotations

#### @Relation

This annotation can be defined on a class.

```php
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *     name = "self",
 *     href = "http://hello",
 *     embedded = "expr(object.getHello())",
 *     attributes = { "foo" = "bar" },
 *     exclusion = ...,
 * )
 */
```

| Property   | Required               | Content                         | Expression language   |
|------------|------------------------|---------------------------------|-----------------------|
| name       | Yes                    | string                          | Yes                   |
| href       | If embedded is not set | string / [@Route](#route)       | Yes                   |
| embedded   | If href is not set     | string / [@Embedded](#embedded) | Yes                   |
| attributes | No                     | array                           | Yes on key and values |
| exclusion  | No                     | [@Exclusion](#exclusion)        | N/A                   |

**Important:** `attributes` are only used on **link relations** (i.e. combined
with the `href` property, not with the `embedded` one).

#### @Route

```php
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *     name = "self",
 *     href = @Hateoas\Route(
 *         "user_get",
 *         parameters = { "id" = "expr(object.getId())" },
 *         absolute = true,
 *         generator = "my_custom_generator"
 *     )
 * )
 */
```

This annotation can be defined in the **href** property of the
[@Relation](#relation) annotation. This is allows you to your URL generator,
if you have configured one.

| Property   | Required            | Content          | Expression language             |
|------------|---------------------|------------------|---------------------------------|
| name       | Yes                 | string           | Yes                             |
| parameters | Defaults to array() | array / string   | Yes (string + array key/values) |
| absolute   | Defaults to false   | boolean / string | Yes                             |
| generator  | No                  | string / null    | No                              |

#### @Embedded

```php
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *     name = "friends",
 *     embedded = @Hateoas\Embedded(
 *         "expr(object.getFriends())",
 *         exclusion = ...,
 *         xmlElementName = "users"
 *     )
 * )
 */
```

This annotation can be defined in the **embedded** property of the
[@Relation](#relation) annotation. It is useful if you need configure the
`exclusion` or `xmlElementName` options for the embedded resource.

| Property       | Required            | Content                  | Expression language    |
|----------------|---------------------|--------------------------|------------------------|
| content        | Yes                 | string / array           | Yes (string)           |
| exclusion      | Defaults to array() | [@Exclusion](#exclusion) | N/A                    |
| xmlElementName | Defaults to array() | string                   | Yes                    |

#### @Exclusion

This annotation can be defined in the **exclusion** property of both the
[@Relation](#relation) and [@Embedded](#embedded) annotations.

| Property     | Required | Content          | Expression language    |
|--------------|----------|------------------|------------------------|
| groups       | No       | array            | No                     |
| sinceVersion | No       | float / integer  | No                     |
| untilVersion | No       | float / integer  | No                     |
| maxDepth     | No       | integer          | No                     |
| excludeIf    | No       | string / boolean | Yes                    |

All values except `excludeIf` act the same way as when they are used directly
on the regular properties with the serializer.

`excludeIf` expects a boolean and is helpful when another expression would fail
under some circumstances. In this example, if the `getManager` method is `null`,
you should exclude it to prevent the URL generation from failing:

```php
/**
 * @Hateoas\Relation(
 *     "manager",
 *     href = @Hateoas\Route(
 *         "user_get",
 *         parameters = { "id" = "expr(object.getManager().getId())" }
 *     ),
 *     exclusion = @Hateoas\Exclusion(excludeIf = "expr(null === object.getManager())")
 * )
 */
class User
{
    public function getId() {}

    /**
     * @return User|null
     */
    public function getManager() {}
}
```

#### @RelationProvider

This annotation can be defined on a class.
It is useful if you wish to serialize multiple-relations(links).
As an example:

```
{
  "_links": {
    "relation_name": [
      {"href": "link1"},
      {"href": "link2"},
      {"href": "link3"}
    ]
  }
}
```

| Property | Required | Content | Expression language |
|----------|----------|---------|---------------------|
| name     | Yes      | string  | No                  |

The property "name" should take the relations-returning method which you have defined in your class ("addRelations" in the following example).

The can be:

- A method: `addRelations`
- A static method: `Class::addRelations`
- A Symfony2 service method: `acme_foo.service:addRelations`

```php
use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use Hateoas\Configuration as Hateoas;

class MyRelationProvider
{
    public function addRelations($object, ClassMetadataInterface $classMetadata)
    {
        // You need to return the relations
        // Adding the relations to the $classMetadata won't work
        return array(
            new Hateoas\Relation(
                'self',
                new Hateoas\Route(
                    'foo_get',
                    array('id' => 'expr(object.getId())')
                )
            )
        );
    }
}
```


Internals
---------

This section refers to the Hateoas internals, providing documentation about
hidden parts of this library. This is not always relevant for end users, but
interesting for developers or people interested in learning how things work
under the hood.


Contributing
------------

See CONTRIBUTING file.


Running the Tests
-----------------

Install the [Composer](http://getcomposer.org/) `dev` dependencies:

    php composer.phar install --dev

Then, run the test suite using [PHPUnit](http://phpunit.de/):

    bin/phpunit


License
-------

Hateoas is released under the MIT License. See the bundled LICENSE file for
details.
