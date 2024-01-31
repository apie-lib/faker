<?php

namespace Apie\Faker\Fakers;

use Apie\Core\Lists\ItemHashmap;
use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionClass;

/** @implements ApieClassFaker<ItemHashmap> */
class ItemHashmapFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->isSubclassOf(ItemHashmap::class) || $class->name === ItemHashmap::class;
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): object
    {
        $returnType = $class->getMethod('offsetGet')->getReturnType();
        $itemCount = $generator->numberBetween(1, 4);
        $arguments = [];
        for ($i = 0; $i < $itemCount; $i++) {
            $arguments[$generator->word()] = $generator->fakeFromType($returnType);
        }
        return $class->newInstance($arguments);
    }
}
