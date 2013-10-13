<?php

namespace Hateoas\Tests\Configuration\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Hateoas\Configuration\Metadata\Driver\AnnotationDriver;

class AnnotationDriverTest extends AbstractDriverTest
{
    public function createDriver()
    {
        return new AnnotationDriver(new AnnotationReader());
    }
}
