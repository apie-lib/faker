<?php
namespace Apie\Faker\Datalayers;

use Apie\Core\Datalayers\Interfaces\GetItem;
use Apie\Core\Datalayers\Search\QuerySearch;
use Apie\Core\Entities\EntityInterface;
use Faker\Generator;
use ReflectionClass;

/**
 * @template T of EntityInterface
 * @implements GetItem<T>
 */
class ProvideSingleFakeData implements GetItem
{
    /**
     * @param ReflectionClass<T> $class
     */
    public function __construct(private ReflectionClass $class, private readonly Generator $faker)
    {
    }

    public function __invoke(int $index, QuerySearch $search): EntityInterface
    {
        return $this->faker->fakeClass($this->class->name);
    }
}
