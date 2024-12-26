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

    #[\PHPUnit\Framework\Attributes\DataProvider('dateValueObjectsProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
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

    public static function dateValueObjectsProvider(): iterable
    {
        $path = dirname((new ReflectionClass(Time::class))->getFileName());
        foreach (Finder::create()->files()->name('*.php')->depth(0)->in($path) as $file) {
            $class = 'Apie\\DateValueObjects\\' . $file->getBasename('.php');
            yield $class => [$class];
        }
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('compositeValueObjectProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_fake_composite_value_objects(string $classToTest)
    {
        $faker = $this->givenAFakerWithApieObjectFaker();
        for ($i = 0; $i < 100; $i++) {
            $result = $faker->fakeClass($classToTest);
            $this->assertInstanceOf($classToTest, $result);
        }
    }

    public static function compositeValueObjectProvider(): iterable
    {
        yield 'Entity with autoincrement identifier' => [UserWithAutoincrementKey::class];
        yield 'Entity with uuid identifier' => [UserWithAddress::class];
        yield 'Polymorphic entity' => [Animal::class];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('enumProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_fake_an_enum(string $classToTest)
    {
        $faker = $this->givenAFakerWithApieObjectFaker();
        for ($i = 0; $i < 1000; $i++) {
            $result = $faker->fakeClass($classToTest);
            $this->assertInstanceOf(UnitEnum::class, $result);
        }
    }

    public static function enumProvider()
    {
        yield 'regular enum' => [Gender::class];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('passwordValueObjectsProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_fake_a_password_value_object(string $classToTest)
    {
        $faker = $this->givenAFakerWithApieObjectFaker();
        for ($i = 0; $i < 1000; $i++) {
            $result = $faker->fakeClass($classToTest);
            $this->assertStringMatchesFormat('%s', $result->toNative());
        }
    }

    public static function passwordValueObjectsProvider(): iterable
    {
        yield 'regular password' => [Password::class];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('primitiveProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
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

    public static function primitiveProvider(): iterable
    {
        $types = ['string', 'int', 'float', 'false', 'true', 'bool', 'mixed', 'array', stdClass::class];
        foreach ($types as $type) {
            yield $type => [$type];
        }
    }
}
