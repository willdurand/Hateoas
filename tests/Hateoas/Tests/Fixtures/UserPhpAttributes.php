<?php

declare(strict_types=1);

namespace Hateoas\Tests\Fixtures;

use Hateoas\Configuration\Annotation as Hateoas;

#[Hateoas\Relation(name: 'self', href: 'http://hateoas.web/user/42', attributes: ['type' => 'application/json'])]
#[Hateoas\Relation(
    name: 'foo',
    href: new Hateoas\Route(name: 'user_get', parameters: ['id' => 'expr(object.getId())']),
    embedded: 'expr(object.getFoo())'
)]
#[Hateoas\Relation(name: 'bar', href: 'foo', embedded: new Hateoas\Embedded(content: 'data', xmlElementName: 'barTag'))]
#[Hateoas\Relation(
    name: 'baz',
    href: new Hateoas\Route(name: 'user_get', parameters: ['id' => 'expr(object.getId())'], absolute: true),
    embedded: 'expr(object.getFoo())'
)]
#[Hateoas\Relation(
    name: 'boom',
    href: new Hateoas\Route(name: 'user_get', parameters: ['id' => 'expr(object.getId())'], absolute: false),
    embedded: 'expr(object.getFoo())'
)]
#[Hateoas\Relation(name: 'badaboom', embedded: 'expr(object.getFoo())')]
#[Hateoas\Relation(
    name: 'hello',
    href: '/hello',
    embedded: new Hateoas\Embedded(
        content: 'hello',
        type: 'string',
        xmlElementName: 'barTag',
        exclusion: new Hateoas\Exclusion(
            groups: ['group3', 'group4'],
            sinceVersion: '1.1',
            untilVersion: '2.3',
            maxDepth: 43,
            excludeIf: 'bar')
    ),
    exclusion: new Hateoas\Exclusion(
        groups: ['group1', 'group2'],
        sinceVersion: '1',
        untilVersion: '2.2',
        maxDepth: 42,
        excludeIf: 'foo'
    )
)]
#[Hateoas\Relation(name: 'attribute_with_expression', href: 'baz', attributes: ['baz' => 'expr(object.getId())'])]
#[Hateoas\RelationProvider(name: 'Hateoas\Tests\Fixtures\User::getRelations')]
class UserPhpAttributes
{
}
