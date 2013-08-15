<?php

namespace tests\fixtures;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation("self", href = "http://hateoas.web/user/42", attributes = {"type" = "application/json"})
 * @Hateoas\Relation("foo", href = @Hateoas\Route("user_get", parameters = {"id" = "@this.id"}), embed = "@this.foo")
 * @Hateoas\Relation("bar", href = "foo", embed = @Hateoas\Embed("data", xmlElementName = "barTag"))
 * @Hateoas\Relation("baz", href = @Hateoas\Route("user_get", parameters = {"id" = "@this.id"}, absolute = true), embed = "@this.foo")
 * @Hateoas\Relation("boom", href = @Hateoas\Route("user_get", parameters = {"id" = "@this.id"}, absolute = false), embed = "@this.foo")
 */
class User
{
}
