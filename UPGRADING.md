From 2.12.0 to 3.0.0
===================
- The PHP minimum version is 7.2
- Typehints and `strict_types` are used everywhere allowed by PHP 7.2, so most of the method signatures are different.
- XML Element name, rel name, attribute names can not use expression language anymore.
- `Hateoas\Configuration\Metadata\ClassMetadataInterface::addRelationProvider()` has been removed.  
Relations can not be added a runtime anymore.To add a relation please use the `@RelationProvider` annotation 
or (similar YAML/XML config) that will allow you to add relations at compile time, an example can be found 
[here](https://github.com/willdurand/Hateoas#relationprovider)
- The use of classes in `Hateoas\Representation\*` is discouraged and deprecations are expected in the next release. 
Suggested implementing own DTO/ValueObjects/Typed-collections to represent collections. 
- `Hateoas\Configuration\Provider\RelationProviderInterface::getRelations()` method has a new signature, 
previously had access to `$object` (the object being serialized), now relations can be added only depending on the class. 
An example can be found [here](https://github.com/willdurand/Hateoas#relationprovider).
- The expression function `link` and the `Hateoas\Helper\LinkHelper::getLinkHref` methods will throw an 
`RuntimeException` exception if the requested link is not available (the previous behaviour was to return `null`).  
- `Hateoas\Configuration\Provider\*` classes and interfaces have been moved one level up in the namespace (in `Hateoas\Configuration`)
<br>(_this change should affect only users who have implemented their own runtime relation providers_).
- `Hateoas\Configuration\Provider\Resolver\*` and `Hateoas\Configuration\RelationsRepository` classes have been removed, 
now `@RelationProvider` has full access to the expression language syntax. 
As example: `@Hateoas\RelationProvider("expr(service('foo').getExtraRelations())")`.
Because of this `Hateoas\HateoasBuilder::addRelationProviderResolver`  has been removed.
<br>(_this change should affect only users who have implemented their own runtime relation providers_).
- `Hateoas\Expression\ExpressionFunctionInterface` has been removed.
 This library uses now directly the jms/serializer provided expression language.
 Because of this `Hateoas\HateoasBuilder::registerExpressionFunction`  has been removed.
<br>(_this is a change of the hateoas internals and should affect only few users who have implemented their own expression language_).  
- `Hateoas\Representation\*` classes are deprecated, recommended user-land implementations.
- The interface `Hateoas\Serializer\JMSSerializerMetadataAwareInterface` has been removed, inline deferrers can access
the jms metadata factory via `$context` argument
<br>(_this is a change of the hateoas internals and should affect only few users who have implemented their own deferrer_).
- The interface `Hateoas\Serializer\XmlSerializerInterface` and `Hateoas\Serializer\JsonSerializerInterface` 
have been replaced by a single interface `Hateoas\Serializer\SerializerInterface` 
(the two interface had the same propose)
<br>(_this is a change of the hateoas internals and should affect only few users who have implemented their own serializer_).
- The event subscribers `Hateoas\Serializer\EventSubscriber\JsonEventSubscriber` and `Hateoas\Serializer\EventSubscriber\XmlEventSubscriber` 
have been replaced by the event listener `Hateoas\Serializer\AddRelationsListener`
(the two implementations had the same propose) 
<br>(_this is a change of the hateoas internals and should affect only few users who have implemented their own event subscriber_).
