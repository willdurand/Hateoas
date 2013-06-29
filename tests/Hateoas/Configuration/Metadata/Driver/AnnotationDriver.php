<?php

namespace tests\Hateoas\Configuration\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Hateoas\Configuration\Metadata\Driver\AnnotationDriver as TestedAnnotationDriver;

class AnnotationDriver extends AbstractDriverTest
{
    public function createDriver()
    {
        return new TestedAnnotationDriver(new AnnotationReader());
    }
}
