Hateoas
=======

[![Build
Status](https://secure.travis-ci.org/willdurand/Hateoas.png)](http://travis-ci.org/willdurand/Hateoas)
[![Scrutinizer Quality
Score](https://scrutinizer-ci.com/g/willdurand/Hateoas/badges/quality-score.png?s=45b5a825f99de4d29c98b5103f59e060139cf354)](https://scrutinizer-ci.com/g/willdurand/Hateoas/)

A PHP library to support implementing representations for HATEOAS REST web
services.


* [Installation](#installation)
  - [Working With Symfony2](#working-with-symfony2)
* [Usage](#usage)
  - [Introduction](#introduction)
  - [Configuring Links](#configuring-links)
  - [Embedding Resources](#embedding-resources)
  - [Dealing With Collections](#dealing-with-collections)
  - [The Expression Language](#the-expression-language)
    - [Context](#context)
    - [Adding Your Own Context Variables](#adding-your-own-context-variables)
  - [URL Generators](#url-generators)
  - [Helpers](#helpers)
    - [LinkHelper](#linkhelper)
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
* [Reference](#reference)
  - [XML](#xml)
  - [YAML](#yaml)
  - [Annotations](#annotations)
    - [@Relation](#relation)
    - [@Route](#route)
    - [@Embed](#embed)
    - [@Exclusion](#exclusion)
    - [@RelationProvider](#relationprovider)
* [Internals](#internals)
  - [Expression Functions](#expression-functions)


Installation
------------

The recommended way to install Hateoas is through
[Composer](http://getcomposer.org/). Require the `willdurand/hateoas` package
into your `composer.json` file:

```json
{
    "require": {
        "willdurand/hateoas": "2.0.*@dev"
    }
}
```

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
> following documentation has been written for Hateoas 2.0 and above.

### Introduction

**Hateoas** leverages the [Serializer](https://github.com/schmittjoh/serializer)
library to provide a nice way to build HATEOAS REST web services. HATEOAS stands
for **H**ypermedia **a**s **t**he **E**ngine **o**f **A**pplication **S**tate,
and basically adds **hypermedia links** to your **representations** (ie. your
API responses). [HATEOAS is about the discoverability of actions on a
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

A link is a relation which is identified by a `name` and that owns a `href`
link:

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
section.

In this section, [**annotations**](#annotations) are used to configure Hateoas.
However, [**XML**](#xml) and [**YAML**](#yaml) formats are also supported. If
you wish, you can use plain PHP too.

**Important:** you must configure both Serializer and Hateoas the same way. E.g.
if you use YAML for configuring Serializer, use YAML for configuring Hateoas.

The easiest way to give Hateoas a try is to use the `HateoasBuilder`. This
builder has numerous methods to configure the Hateoas serializer, but we won't
dig into it right now (see [The HateoasBuilder](#the-hateoasbuilder)).
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
serializer, it simply hooks into the JMS Serializer one.

By default, Hateoas uses the [Hypertext Application
Language](http://stateless.co/hal_specification.html) (HAL) for JSON
serialization:

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

And, [Atom Links](http://tools.ietf.org/search/rfc4287#section-4.2.7) are used
by default for XML serialization:

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
link to them, as it prevents clients from having to make extra round trips.

An **embedded resource** is a named **relation** that contains data, represented
by the `embed` parameter.

```php
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * ...
 *
 * @Hateoas\Relation(
 *     "manager",
 *     href = "expr('/api/users/' ~ object.getManager().getId())",
 *     embed = "expr(object.getManager())",
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

```php
$hateoas = HateoasBuilder::create()->build();

$user = new User(42, 'Adrien', 'Brault', new User(23, 'Will', 'Durand'));
$json = $hateoas->serialize($user, 'json');
$xml  = $hateoas->serialize($user, 'xml');
```

Serializing `embed` relations are also HAL compliant:

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
        },
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

In XML, serializing `embed` relations will create new elements:

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

The library provides the several classes in the `Hateoas\Representation\*`
namespace to help you with common tasks. These are simple classes configured
with the library's annotations.

The `PaginatedCollection` and `SimpleCollection` classes are probably the most
interesting ones:

```php
use Hateoas\Representation\PaginatedCollection;
use Hateoas\Representation\SimpleCollection;

$paginatedCollection = new PaginatedCollection(
    new SimpleCollection(
        array($user1, $user2, ...),
        'users', // embed rel
        'users' // xml element name
    ),
    'user_list', // route
    array(), // route parameters
    1, // page
    20, // limit
    4, // total pages
    'page', // page route parameter name, optional, defaults to 'page'
    'limit' // limit route parameter name, optional, defaults to 'limit'
);

$json = $hateoas->serialize($paginatedCollection, 'json');
$xml  = $hateoas->serialize($paginatedCollection, 'xml');
```

The `SimpleCollection` class allows you to dynamically configure the collection
resources rel, and the xml root element name.

The `PaginatedCollection` is designed to add `self`, `first`, and when possible
`last`, `next`, `previous` links.

The Hateoas library also provides a `PagerfantaFactory` to easily build
`PaginatedCollection` from a **Pagerfanta** instance:

```php
use Hateoas\Representation\Factory\PagerfantaFactory;

$pagerfantaFactory   = new PagerfantaFactory(); // you can pass the page and limit parameters name
$paginatedCollection = $pagerfantaFactory->create(
    $pager,
    'user_list',
    array() // route parameters
);

$json = $hateoas->serialize($paginatedCollection, 'json');
$xml  = $hateoas->serialize($paginatedCollection, 'xml');
```

You would get the following JSON content:

```json
{
    "users": [
        { "id": 123 },
        { "id": 456 }
    ],
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

### The Expression Language

Hateoas relies on the powerful Symfony
[ExpressionLanguage](http://symfony.com/doc/current/components/expression_language/introduction.html)
component to retrieve values such as links, ids or objects to embed.

Basically, each time you can fill in a value, you can either pass an
**hardcoded value** or an **expression**. In order to use the Expression
Language, you have to use the `expr()` notation:

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

Note that the library comes with a `SymfonyUrlGenerator`. For example to use it
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
relation name.  Basically, it is able to generate an URI (either absolute or
relative) from any **link** relation:

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

The `XmlHalSerializer` allows you to generate [Atom
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
* `setExpressionLanguage(ExpressionLanguage $expressionLanguage)`.

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

Both the serializer and the Hateoas library collects several metadata about your
objects from various sources such as YML, XML, or annotations. In order to make
this process as efficient as possible, it is encourage to let them cache that
information. For that, you can configure a cache directory:

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
class names where all `\` are replaced with `.`. So, if you class would be
named `Vendor\Package\Foo` the metadata file would need to be located at
`$someDir/Vendor.Package.Foo.(xml|yml)`.


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
            <h:href router="user_friends" generator="my_custom_generator">
                <h:parameter name="id" value="expr(object.getId())" />
                <h:parameter name="page" value="1" />
            </h:ref>
            <h:embed xml-element-name="users">
                <h:content>expr(object.getFriends())</h:content>
                <h:exclusion ... />
            </h:embed>
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
        -   rel: self
            href: http://acme.com/foo/1
        -   rel: expr(object.getFriendsDynamicRel())
            href:
                route: user_friends
                parameters:
                    id: expr(object.getId())
                    page: 1
                generator: my_custom_generator
            embed:
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
 *     embed = "expr(object.getHello())",
 *     attributes = { "foo" = "bar" },
 *     exclusion = ...,
 * )
 */
```

| Property   | Required            | Content                   | Expression language   |
|------------|---------------------|---------------------------|-----------------------|
| name       | Yes                 | string                    | Yes                   |
| href       | If embed is not set | string / [@Route](#route) | Yes                   |
| embed      | If href is not set  | string / [@Embed](#embed) | Yes                   |
| attributes | No                  | array                     | Yes on key and values |
| exclusion  | No                  | [@Exclusion](#exclusion)  | N/A                   |

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
[@Relation](#relation) annotation.

| Property   | Required            | Content        | Expression language             |
|------------|---------------------|----------------|---------------------------------|
| name       | Yes                 | string         | Yes                             |
| parameters | Defaults to array() | array / string | Yes (string + array key/values) |
| absolute   | Defaults to false   | boolean        | No                              |
| generator  | No                  | string / null  | No                              |

#### @Embed

```php
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *     name = "friends",
 *     embed = @Hateoas\Embed(
 *         "expr(object.getFriends())",
 *         exclusion = ...,
 *         xmlElementName = "users"
 *     )
 * )
 */
```

This annotation can be defined in the **embed** property of the
[@Relation](#relation) annotation.

| Property       | Required            | Content                  | Expression language    |
|----------------|---------------------|--------------------------|------------------------|
| content        | Yes                 | string / array           | Yes (string)           |
| exclusion      | Defaults to array() | [@Exclusion](#exclusion) | N/A                    |
| xmlElementName | Defaults to array() | string                   | Yes                    |

#### @Exclusion

This annotation can be defined in the **exclusion** property of both the
[@Relation](#relation) and [@Embed](#embed) annotations.

| Property     | Required | Content          | Expression language    |
|--------------|----------|------------------|------------------------|
| groups       | No       | array            | No                     |
| sinceVersion | No       | float / integer  | No                     |
| untilVersion | No       | float / integer  | No                     |
| maxDepth     | No       | integer          | No                     |
| excludeIf    | No       | string / boolean | Yes                    |

All values exception `excludeIf` as if it was defined on regular properties with
the serializer.

`excludeIf` expects a boolean; and is helpful when an other expression would fail
under some circumstances:

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

| Property | Required | Content | Expression language |
|----------|----------|---------|---------------------|
| name     | Yes      | string  | No                  |

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
hidden parts of this library, not always relevant for end users, but interesting
for developers or people interested in learning how things work under the hood.

### Expression Functions

**Expression Functions** are custom functions used to extend the [Expression
Language](#the-expression-language) as explained in chapter [Extending the
ExpressionLanguage](http://symfony.com/doc/current/components/expression_language/extending.html),
part of the Symfony documentation.

By now, Hateoas does not let users add their own custom functions. Only core
functions are registered, such as the `LinkExpressionFunction` described in
[LinkHelper - The `link` Function](#the-link-function).

The `ExpressionFunctionInterface` is designed to represent an expression
function. Adding a new expression function is a matter of implementing this
interface and registering it into the `ExpressionEvaluator` through the
`registerFunction()` method.


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
