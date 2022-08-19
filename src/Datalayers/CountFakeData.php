<?php
namespace Apie\Faker\Datalayers;

use Apie\Core\Attributes\FakeCount;
use Apie\Core\Datalayers\Interfaces\CountItems;
use Apie\Core\Datalayers\Search\QuerySearch;
use ReflectionClass;

class CountFakeData implements CountItems
{
    /**
     * @param ReflectionClass<object> $class
     */
    public function __construct(private ReflectionClass $class)
    {
    }

    public function __invoke(QuerySearch $search): int
    {
        $attributes = $this->class->getAttributes(FakeCount::class);
        if (empty($attributes)) {
            return 100;
        }

        return reset($attributes)->newInstance()->count;
    }
}
