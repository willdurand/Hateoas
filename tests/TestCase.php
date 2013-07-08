<?php

namespace tests;

use mageekguy\atoum;

abstract class TestCase extends atoum\test
{
    public function __construct(atoum\adapter $adapter = null, atoum\annotations\extractor $annotationExtractor = null, atoum\asserter\generator $asserterGenerator = null, atoum\test\assertion\manager $assertionManager = null, \closure $reflectionClassFactory = null)
    {
        $this->setTestNamespace('tests');

        parent::__construct($adapter, $annotationExtractor, $asserterGenerator, $assertionManager, $reflectionClassFactory);
    }

    public static function rootPath()
    {
        return __DIR__;
    }
}
