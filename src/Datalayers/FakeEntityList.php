<?php
namespace Apie\Faker\Datalayers;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Datalayers\Concerns\CreatePaginatedResultRuntime;
use Apie\Core\Datalayers\Lists\EntityListInterface;
use Apie\Core\Datalayers\Search\LazyLoadedListFilterer;
use Faker\Generator;
use Iterator;
use ReflectionClass;

final class FakeEntityList implements EntityListInterface
{
    use CreatePaginatedResultRuntime;

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
            return 100;
        }
        return reset($attributes)->newInstance()->count;
    }

    public function getIterator(): Iterator
    {
        $count = $this->getTotalCount();

        return new FakeIterator($count, $this->class, $this->faker);
    }
}
