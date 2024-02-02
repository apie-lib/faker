<?php
namespace Apie\Tests\Faker;

use Apie\DateValueObjects\Time;
use Apie\Fixtures\Entities\Polymorphic\Animal;
use Apie\Fixtures\Entities\UserWithAddress;
use Apie\Fixtures\Entities\UserWithAutoincrementKey;
use Apie\Fixtures\Enums\Gender;
use Apie\Fixtures\ValueObjects\Password;
use Apie\Tests\Faker\Concerns\ItCreatesAFaker;
use Apie\TypeConverter\ReflectionTypeFactory;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;
use Symfony\Component\Finder\Finder;
use UnitEnum;

class ApieObjectFakerTest extends TestCase
{
    use ItCreatesAFaker;

    /**
     * @test
     * @dataProvider dateValueObjectsProvider
     */
    public function it_can_fake_a_date_value_object(string $classToTest)
    {
        $faker = $this->givenAFakerWithApieObjectFaker();
        for ($i = 0; $i < 1000; $i++) {
            $result = $faker->fakeClass($classToTest);
            $this->assertInstanceOf(DateTimeImmutable::class, $result->toDate());

            $result = $faker->unique()->fakeClass($classToTest);
            $this->assertInstanceOf(DateTimeImmutable::class, $result->toDate());
        }
    }

    public function dateValueObjectsProvider(): iterable
    {
        $path = dirname((new ReflectionClass(Time::class))->getFileName());
        foreach (Finder::create()->files()->name('*.php')->depth(0)->in($path) as $file) {
            $class = 'Apie\\DateValueObjects\\' . $file->getBasename('.php');
            yield $class => [$class];
        }
    }

    /**
     * @test
     * @dataProvider compositeValueObjectProvider
     */
    public function it_can_fake_composite_value_objects(string $classToTest)
    {
        $faker = $this->givenAFakerWithApieObjectFaker();
        for ($i = 0; $i < 100; $i++) {
            $result = $faker->fakeClass($classToTest);
            $this->assertInstanceOf($classToTest, $result);
        }
    }

    public function compositeValueObjectProvider(): iterable
    {
        yield 'Entity with autoincrement identifier' => [UserWithAutoincrementKey::class];
        yield 'Entity with uuid identifier' => [UserWithAddress::class];
        yield 'Polymorphic entity' => [Animal::class];
    }

    /**
     * @test
     * @dataProvider enumProvider
     */
    public function it_can_fake_an_enum(string $classToTest)
    {
        $faker = $this->givenAFakerWithApieObjectFaker();
        for ($i = 0; $i < 1000; $i++) {
            $result = $faker->fakeClass($classToTest);
            $this->assertInstanceOf(UnitEnum::class, $result);
        }
    }

    public function enumProvider()
    {
        yield 'regular enum' => [Gender::class];
    }

    /**
     * @test
     * @dataProvider passwordValueObjectsProvider
     */
    public function it_can_fake_a_password_value_object(string $classToTest)
    {
        $faker = $this->givenAFakerWithApieObjectFaker();
        for ($i = 0; $i < 1000; $i++) {
            $result = $faker->fakeClass($classToTest);
            $this->assertStringMatchesFormat('%s', $result->toNative());
        }
    }

    public function passwordValueObjectsProvider(): iterable
    {
        yield 'regular password' => [Password::class];
    }

    /**
     * @test
     * @dataProvider primitiveProvider
     */
    public function it_can_fake_primitives(string $type)
    {
        $faker = $this->givenAFakerWithApieObjectFaker();
        for ($i = 0; $i < 100; $i++) {
            $result = $faker->fakeFromType(ReflectionTypeFactory::createReflectionType($type));
            switch ($type) {
                case 'true':
                    $this->assertTrue($result);
                    break;
                case 'false':
                    $this->assertFalse($result);
                    break;
                case 'mixed':
                    if ($result !== null && !is_array($result)) {
                        $this->assertIsScalar($result);
                    }
                    break;
                default:
                    $this->assertEquals($type, get_debug_type($result));
            }
            
        }
    }

    public function primitiveProvider(): iterable
    {
        $types = ['string', 'int', 'float', 'false', 'bool', 'mixed', 'array', stdClass::class];
        if (PHP_VERSION_ID >= 80200) {
            $types[] =  'true';
        }
        foreach ($types as $type) {
            yield $type => [$type];
        }
    }
}
