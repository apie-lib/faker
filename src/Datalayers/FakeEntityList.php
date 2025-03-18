<?php
namespace Apie\Faker\Datalayers;

use Apie\Core\Attributes\FakeCount;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Datalayers\Concerns\CreatePaginatedResultRuntime;
use Apie\Core\Datalayers\Lists\EntityListInterface;
use Apie\Core\Datalayers\Search\LazyLoadedListFilterer;
use Apie\Core\Entities\EntityInterface;
use Faker\Generator;
use Iterator;
use ReflectionClass;

/**
 * @template T of EntityInterface
 * @implements EntityListInterface<T>
 */
final class FakeEntityList implements EntityListInterface
{
    use CreatePaginatedResultRuntime;

    /**
     * @param ReflectionClass<T> $class
     */
    public function __construct(
        private readonly ReflectionClass $class,
        private readonly BoundedContextId $boundedContextId,
        private readonly LazyLoadedListFilterer $filterer,
        private readonly Generator $faker
    ) {
    }

    public function getTotalCount(): int
    {
        $attributes = $this->class->getAttributes(FakeCount::class);
        if (empty($attributes)) {
            return 10;
        }
        return reset($attributes)->newInstance()->count;
    }

    public function getIterator(): Iterator
    {
        $count = $this->getTotalCount();

        return new FakeIterator($count, $this->class, $this->faker);
    }
}
