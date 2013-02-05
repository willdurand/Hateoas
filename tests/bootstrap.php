<?php

if (!$loader = require_once __DIR__.'/../vendor/autoload.php') {
    die('You must set up the project dependencies, run the following commands:'.PHP_EOL.
        'curl -s http://getcomposer.org/installer | php'.PHP_EOL.
        'php composer.phar install'.PHP_EOL);
}

$loader->add('Hateoas\Tests', __DIR__);
Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
