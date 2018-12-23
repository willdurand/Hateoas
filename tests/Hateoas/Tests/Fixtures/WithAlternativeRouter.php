<?php

declare(strict_types=1);

namespace Hateoas\Tests\Fixtures;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *      "search",
 *      href = @Hateoas\Route(
 *          "/search",
 *          parameters = {
 *              "query" = "hello"
 *          },
 *          generator = "my_generator"
 *      )
 * )
 */
class WithAlternativeRouter
{
}
