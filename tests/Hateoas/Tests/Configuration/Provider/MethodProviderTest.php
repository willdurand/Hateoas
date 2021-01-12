<?php

declare(strict_types=1);

namespace Hateoas\Tests\Configuration\Provider;

use Hateoas\Configuration\Provider\FunctionProvider;
use Hateoas\Configuration\Relation;
use Hateoas\Configuration\RelationProvider;
use Hateoas\Tests\TestCase;

class MethodProviderTest extends TestCase
{
    public function test()
    {
        $providerProvider = new FunctionProvider();

        $this->assertEquals(
            [new Relation('abcde')],
            $providerProvider->getRelations(new RelationProvider('func(Hateoas\Tests\Configuration\Provider\abc)'), \stdClass::class)
        );
        $this->assertEquals(
            [new Relation('abcdef')],
            $providerProvider->getRelations(new RelationProvider('func(' . self::class . '::abc)'), \stdClass::class)
        );
    }

    public static function abc()
    {
        return [new Relation('abcdef')];
    }
}

function abc()
{
    return [new Relation('abcde')];
}
