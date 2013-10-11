<?php

namespace tests\fixtures;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation("self", href = "http://hateoas.web/user/42", attributes = {"type" = "application/json"})
 * @Hateoas\Relation("foo", href = @Hateoas\Route("user_get", parameters = {"id" = "expr(object.getId())"}), embed = "expr(object.getFoo())")
 * @Hateoas\Relation("bar", href = "foo", embed = @Hateoas\Embed("data", xmlElementName = "barTag"))
 * @Hateoas\Relation("baz", href = @Hateoas\Route("user_get", parameters = {"id" = "expr(object.getId())"}, absolute = true), embed = "expr(object.getFoo())")
 * @Hateoas\Relation("boom", href = @Hateoas\Route("user_get", parameters = {"id" = "expr(object.getId())"}, absolute = false), embed = "expr(object.getFoo())")
 * @Hateoas\Relation("badaboom", embed = "expr(object.getFoo())")
 * @Hateoas\Relation(
 *      "hello",
 *      href = "/hello",
 *      exclusion = @Hateoas\Exclusion(
 *          groups = {"group1", "group2"},
 *          sinceVersion = 1,
 *          untilVersion = 2.2,
 *          maxDepth = 42,
 *          excludeIf = "foo"
 *      ),
 *      embed = @Hateoas\Embed(
 *          "hello",
 *          xmlElementName = "barTag",
 *          exclusion = @Hateoas\Exclusion(
 *              groups = {"group3", "group4"},
 *              sinceVersion = 1.1,
 *              untilVersion = 2.3,
 *              maxDepth = 43,
 *              excludeIf = "bar"
 *          )
 *      )
 * )
 *
 * @Hateoas\RelationProvider("getRelations")
 */
class User
{
}
