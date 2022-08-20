<?php
namespace Apie\Faker\Datalayers;

use Apie\Core\Datalayers\Interfaces\TakeItem;
use Apie\Core\Datalayers\Search\QuerySearch;
use Apie\Core\Entities\EntityInterface;
use Faker\Generator;
use ReflectionClass;

/**
 * @template T of EntityInterface
 * @implements TakeItem<T>
 */
class ProvideMultipleFakeData implements TakeItem
{
    /**
     * @param ReflectionClass<T> $class
     */
    public function __construct(private ReflectionClass $class, private readonly Generator $faker, private readonly CountFakeData $counter)
    {
    }

    public function __invoke(int $index, int $count, QuerySearch $search): array
    {
        $max = ($this->counter)($search);
        if ($index + $count > $max) {
            $count = $max - $index;
        }
        $array = [];
        for ($i = 0; $i < $count; $i++) {
            $array[$i] = $this->faker->fakeClass($this->class->name);
        }
        return $array;
    }
}
