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
    public function __construct(private ReflectionClass $class, private readonly Generator $faker)
    {
    }

    public function __invoke(int $index, int $count, QuerySearch $search): array
    {
        $array = [];
        for ($i = 0; $i < $count; $i++) {
            $array[$i] = $this->faker->fakeClass($this->class->name);
        }
        return $array;
    }
}
