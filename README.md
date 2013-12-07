Hateoas
=======

[![Build
Status](https://secure.travis-ci.org/willdurand/Hateoas.png)](http://travis-ci.org/willdurand/Hateoas)
[![Scrutinizer Quality
Score](https://scrutinizer-ci.com/g/willdurand/Hateoas/badges/quality-score.png?s=45b5a825f99de4d29c98b5103f59e060139cf354)](https://scrutinizer-ci.com/g/willdurand/Hateoas/)

A PHP library to support implementing representations for HATEOAS REST web
services.


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
It is worth mentionning that **relations** also refer to **embedded resources**
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
part of the Serializer library. Hateoas does not come with its own serializer,
it simply hooks into the JMS Serializer one.

Hateoas uses the [Hypertext Application
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

And, [Atom links](http://tools.ietf.org/search/rfc4287#section-4.2.7) are used
for XML serialization:

```xml
<user id="42">
    <first_name><![CDATA[Adrien]]></first_name>
    <last_name><![CDATA[Brault]]></last_name>
    <link rel="self" href="/api/users/42"/>
</user>
```

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

$user = new User(42, 'Adrien', 'Brault', new User(23, 'MANAGER', 'MANAGER!!!'));
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
            "first_name": "MANAGER",
            "last_name": "MANAGER!!!",
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
        <first_name><![CDATA[MANAGER]]></first_name>
        <last_name><![CDATA[MANAGER!!!]]></last_name>
        <link rel="self" href="/api/users/23"/>
    </manager>
</user>
```

### Url Generators

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

### Collections Support

The library provides the several classes in the `Hateoas\Representation\*`
namespace to help you with common tasks. These are simple classes configured the
library's annotations.

The `PaginatedCollection` and `Collection` classes are probably the most
interesting one:

```php
use Hateoas\Representation\PaginatedCollection;
use Hateoas\Representation\Collection;

$paginatedCollection = new PaginatedCollection(
    new Collection(
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

`Collection` allow you to dynamically configure the collection resources rel,
and the xml root element name.
`PaginatedCollection` is configured to add `self`, `first`, and when possible
`last`, `next`, `previous` links.

We also provide a `PagerfantaFactory` to easily build PaginatedCollection
from a **Pagerfanta** instance:

```php
use Hateoas\Representation\Factory\PagerfantaFactory;

$pagerfantaFactory = new PagerfantaFactory(); // you can pass the page and limit parameters name
$paginatedCollection = $pagerfantaFactory->create(
    $pager,
    'user_list',
    array() // route parameters
);
$json = $hateoas->serialize($paginatedCollection, 'json');
$xml  = $hateoas->serialize($paginatedCollection, 'xml');
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

We call such a variable a **context value**.

You can add your own context values to the Expression Language context by adding
them to the `ExpressionEvaluator`. For instance, the
[BazingaHateoasBundle](https://github.com/willdurand/BazingaHateoasBundle) adds
the `request` and the `container` to the context: [BazingaHateoasBundle's
configuration](https://github.com/willdurand/BazingaHateoasBundle/blob/master/Resources/config/serializer.xml#L52-L59).

You would be able to write the following expressions:

```
expr(request.method !== 'GET')

expr(container.get('my-service').foo())
```

##### Adding Your Own Context Values

Using the `HateoasBuilder`, call the `setExpressionContextValue()` method to add
new context values:

```php
use Hateoas\HateoasBuilder;

$hateoas = HateoasBuilder::create()
    ->setExpressionContextValue('foo', new Foo())
    ->build();
```

The `foo` variables is now available:

```
expr(foo !== null)
```

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

* `setExpressionContextValue($name, $value)`: adds a new expression context
  value;
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
 *     )
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


Contributing
------------

See CONTRIBUTING file.


Running the Tests
-----------------

Install the [Composer](http://getcomposer.org/) `dev` dependencies:

    php composer.phar install --dev

Then, run the test suite using [atoum](http://www.atoum.org/):

    bin/phpunit


License
-------

Hateoas is released under the MIT License. See the bundled LICENSE file for
details.
