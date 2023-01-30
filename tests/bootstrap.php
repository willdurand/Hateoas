<?php

declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationRegistry;

if (!($loader = include __DIR__ . '/../vendor/autoload.php')) {
    die(<<<EOT
You need to install the project dependencies using Composer:
$ wget http://getcomposer.org/composer.phar
OR
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install --dev
$ phpunit
EOT
    );
}

$loader->add('Hateoas\Tests', __DIR__);

// Method has been removed in doctrine/annotations:2
if (method_exists(AnnotationRegistry::class, 'registerLoader')) {
    AnnotationRegistry::registerLoader('class_exists');
}
