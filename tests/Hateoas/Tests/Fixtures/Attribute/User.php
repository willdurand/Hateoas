<?php

declare(strict_types=1);

namespace Hateoas\Tests\Fixtures\Attribute;

use Hateoas\Configuration\Annotation as Hateoas;

#[Hateoas\Relation('self', href: 'http://hateoas.web/user/42', attributes: ['type' => 'application/json'])]
#[Hateoas\Relation('foo', href: new Hateoas\Route(name: 'user_get', parameters: ['id' => 'expr(object.getId())']), embedded: 'expr(object.getFoo())')]
#[Hateoas\Relation('bar', href: 'foo', embedded: new Hateoas\Embedded(content: 'data', xmlElementName: 'barTag'))]
#[Hateoas\Relation('baz', href: new Hateoas\Route(name: 'user_get', parameters: ['id' => 'expr(object.getId())'], absolute: true), embedded: 'expr(object.getFoo())')]
#[Hateoas\Relation('boom', href: new Hateoas\Route(name: 'user_get', parameters: ['id' => 'expr(object.getId())'], absolute: false), embedded: 'expr(object.getFoo())')]
#[Hateoas\Relation('badaboom', embedded: 'expr(object.getFoo())')]
#[Hateoas\Relation(
    'hello',
    href: '/hello',
    exclusion: new Hateoas\Exclusion(
        groups: ['group1', 'group2'],
        sinceVersion: '1',
        untilVersion: '2.2',
        maxDepth: 42,
        excludeIf: 'foo',
    ),
    embedded: new Hateoas\Embedded(
        'hello',
        xmlElementName: 'barTag',
        type: 'string',
        exclusion: new Hateoas\Exclusion(
            groups: ['group3', 'group4'],
            sinceVersion: '1.1',
            untilVersion: '2.3',
            maxDepth: 43,
            excludeIf: 'bar',
        )
    )
)]
#[Hateoas\Relation(name: 'attribute_with_expression', href: 'baz', attributes: ['baz' => 'expr(object.getId())'])]
#[Hateoas\RelationProvider(name: 'Hateoas\Tests\Fixtures\Attribute\User::getRelations')]
class User
{
    /**
     * do not use for functional testing
     */
    public static function getRelations()
    {
        return [];
    }
}
