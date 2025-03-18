<?php
namespace Apie\Tests\Faker\DataLayers;

use Apie\Faker\Datalayers\FakeIterator;
use Apie\Fixtures\Entities\UserWithAddress;
use Apie\Tests\Faker\Concerns\ItCreatesAFaker;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class FakeIteratorTest extends TestCase
{
    use ItCreatesAFaker;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_a_virtual_list_of_items()
    {
        $testItem = new FakeIterator(11, new ReflectionClass(UserWithAddress::class), $this->givenAFakerWithApieObjectFaker());
        $this->assertCount(11, iterator_to_array($testItem));
    }
}
