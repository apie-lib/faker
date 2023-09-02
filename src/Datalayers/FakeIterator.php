<?php
namespace Apie\Faker\Datalayers;

use Apie\Core\Entities\EntityInterface;
use Faker\Generator;
use Iterator;
use ReflectionClass;

/**
 * @template T of EntityInterface
 */
class FakeIterator implements Iterator
{
    private int $offset;

    private ?EntityInterface $currentEntity;

    /**
     * @param ReflectionClass<T> $class
     */
    public function __construct(
        private readonly int $count,
        private readonly ReflectionClass $class,
        private readonly Generator $faker
    ) {
        $this->rewind();
    }
    /**
     * @return T|null
     */
    public function current(): ?EntityInterface
    {
        return $this->currentEntity;
    }

    private function updateCurrent(): void
    {
        if ($this->offset >= $this->count) {
            $this->currentEntity = null;
            return;
        }
        $this->currentEntity = $this->faker->fakeClass($this->class->name);
    }
    public function key(): int
    {
        return $this->offset;
    }
    public function next(): void
    {
        $this->offset++;
        $this->updateCurrent();
    }
    public function rewind(): void
    {
        $this->offset = 0;
        $this->updateCurrent();
    }
    public function valid(): bool
    {
        return null !== $this->currentEntity;
    }
}
