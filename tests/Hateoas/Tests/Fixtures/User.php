<?php

declare(strict_types=1);

namespace Hateoas\Tests\Fixtures;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation("self", href = "http://hateoas.web/user/42", attributes = {"type" = "application/json"})
 * @Hateoas\Relation("foo", href = @Hateoas\Route("user_get", parameters = {"id" = "expr(object.getId())"}), embedded = "expr(object.getFoo())")
 * @Hateoas\Relation("bar", href = "foo", embedded = @Hateoas\Embedded("data", xmlElementName = "barTag"))
 * @Hateoas\Relation("baz", href = @Hateoas\Route("user_get", parameters = {"id" = "expr(object.getId())"}, absolute = true), embedded = "expr(object.getFoo())")
 * @Hateoas\Relation("boom", href = @Hateoas\Route("user_get", parameters = {"id" = "expr(object.getId())"}, absolute = false), embedded = "expr(object.getFoo())")
 * @Hateoas\Relation("badaboom", embedded = "expr(object.getFoo())")
 * @Hateoas\Relation(
 *      "hello",
 *      href = "/hello",
 *      exclusion = @Hateoas\Exclusion(
 *          groups = {"group1", "group2"},
 *          sinceVersion = "1",
 *          untilVersion = "2.2",
 *          maxDepth = 42,
 *          excludeIf = "foo"
 *      ),
 *      embedded = @Hateoas\Embedded(
 *          "hello",
 *          xmlElementName = "barTag",
 *          type="string",
 *          exclusion = @Hateoas\Exclusion(
 *              groups = {"group3", "group4"},
 *              sinceVersion = "1.1",
 *              untilVersion = "2.3",
 *              maxDepth = 43,
 *              excludeIf = "bar"
 *          )
 *      )
 * )
 * @Hateoas\Relation("attribute_with_expression", href = "baz", attributes = {"baz" = "expr(object.getId())"})
 * @Hateoas\RelationProvider("Hateoas\Tests\Fixtures\User::getRelations")
 */
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
