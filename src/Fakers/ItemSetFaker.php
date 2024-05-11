<?php

namespace Apie\Faker\Fakers;

use Apie\Core\Lists\ItemSet;
use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use ReflectionClass;

/** @implements ApieClassFaker<ItemSet> */
class ItemSetFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return $class->isSubclassOf(ItemSet::class) || $class->name === ItemSet::class;
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): object
    {
        $returnType = $class->getMethod('offsetGet')->getReturnType();
        $itemCount = $generator->numberBetween(1, 4);
        $arguments = [];
        for ($i = 0; $i < $itemCount; $i++) {
            $arguments[] = $generator->fakeFromType($returnType);
        }
        return $class->newInstance($arguments);
    }
}
