<?php

namespace tests\fixtures;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation("self", href = "http://hateoas.web/user/42", attributes = {"type" = "application/json"})
 * @Hateoas\Relation("foo", href = @Hateoas\Route("user_get", parameters = {"id" = "@this.id"}), embed = "@this.foo")
 * @Hateoas\Relation("bar", href = "foo", embed = @Hateoas\Embed("data", xmlElementName = "barTag"))
 */
class User
{

}
