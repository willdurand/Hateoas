<?php

namespace tests\fixtures;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation("self", href = "http://hateoas.web/user/42", attributes = {"type" = "application/json"})
 * @Hateoas\Relation("foo", href = @Hateoas\Route("user_get", parameters = {"id" = "expr(object.getId())"}), embed = "expr(object.getFoo())")
 * @Hateoas\Relation("bar", href = "foo", embed = @Hateoas\Embed("data", xmlElementName = "barTag"))
 * @Hateoas\Relation("baz", href = @Hateoas\Route("user_get", parameters = {"id" = "expr(object.getId())"}, absolute = true), embed = "expr(object.getFoo())")
 * @Hateoas\Relation("boom", href = @Hateoas\Route("user_get", parameters = {"id" = "expr(object.getId())"}, absolute = false), embed = "expr(object.getFoo())")
 */
class User
{
}
