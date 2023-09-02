<?php
namespace Apie\Tests\Faker;

use Apie\Core\Datalayers\Search\LazyLoadedListFilterer;
use Apie\Core\Datalayers\Search\QuerySearch;
use Apie\Core\Indexing\Indexer;
use Apie\Faker\Datalayers\FakerDatalayer;
use Apie\Fixtures\Entities\UserWithAutoincrementKey;
use Apie\Fixtures\Identifiers\UserAutoincrementIdentifier;
use Apie\Fixtures\ValueObjects\AddressWithZipcodeCheck;
use Apie\Tests\Faker\Concerns\ItCreatesAFaker;
use Apie\TextValueObjects\DatabaseText;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class FakerDatalayerTest extends TestCase
{
    use ItCreatesAFaker;

    /**
     * @test
     */
    public function it_retrieves_random_data()
    {
        $faker = $this->givenAFakerWithApieObjectFaker();
        $testItem = new FakerDatalayer($faker, new LazyLoadedListFilterer(Indexer::create()));
        $list = $testItem->all(new ReflectionClass(UserWithAutoincrementKey::class));
        $this->assertEquals(100, $list->getTotalCount());
        $actual = $list->toPaginatedResult(QuerySearch::fromArray(['page' => 0, 'items_per_page' => 3]));
        $this->assertCount(3, $actual->list);
        $actual = $list->toPaginatedResult(QuerySearch::fromArray(['page' => 33, 'items_per_page' => 3]));
        $this->assertCount(1, $actual->list, 'Faker respects the total count when last page should not be complete');
    }

    /**
     * @test
     */
    public function it_finds_one_line_of_random_data()
    {
        $faker = $this->givenAFakerWithApieObjectFaker();
        $testItem = new FakerDatalayer($faker, new LazyLoadedListFilterer(Indexer::create()));
        $actual = $testItem->find(new UserAutoincrementIdentifier(12));
        $this->assertInstanceOf(UserWithAutoincrementKey::class, $actual);
        $this->assertEquals(12, $actual->getId()->toNative());
    }

    /**
     * @test
     */
    public function it_persists_only_an_autoincrement_integer()
    {
        $faker = $this->givenAFakerWithApieObjectFaker();
        $testItem = new FakerDatalayer($faker, new LazyLoadedListFilterer(Indexer::create()));
        $actual = $testItem->persistNew(new UserWithAutoincrementKey(new AddressWithZipcodeCheck(
            new DatabaseText('street'),
            new DatabaseText('42-A'),
            new DatabaseText('1234 AA'),
            new DatabaseText('Amsterdam')
        )));
        $this->assertInstanceOf(UserWithAutoincrementKey::class, $actual);
        $this->assertNotNull($actual->getId()->toNative());
    }
}
