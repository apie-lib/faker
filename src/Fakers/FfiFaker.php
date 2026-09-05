<?php

namespace Apie\Faker\Fakers;

use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use FFI;
use FFI\CData;
use FFI\CType;
use ReflectionClass;

/** @implements ApieClassFaker<CData|CType> */
class FfiFaker implements ApieClassFaker
{
    private static FFI $instance;

    private static function getFfi(): FFI
    {
        if (!isset(self::$instance)) {
            self::$instance = FFI::cdef();
        }

        return self::$instance;
    }

    public function supports(ReflectionClass $class): bool
    {
        return in_array(
            $class->name,
            [CData::class, CType::class],
            true
        );
    }

    /**
     * @return array<array-key, int>
     */
    private static function createRandomIntArray(Generator $faker): array
    {
        $numbers = [];

        for ($i = 0; $i < 10; $i++) {
            $numbers[] = $faker->numberBetween(1, 100);
        }

        return $numbers;
    }

    /**
     * @param array<array-key, int> $values
     */
    private static function fillIntArray(
        CData $data,
        array $values
    ): void {
        foreach ($values as $index => $value) {
            $data[$index] = $value;
        }
    }

    private static function fillCharArray(
        CData $data,
        string $value
    ): void {
        $length = min(FFI::sizeof($data), strlen($value));

        for ($i = 0; $i < $length; $i++) {
            $data[$i] = $value[$i];
        }
    }

    public function fakeFor(
        Generator $generator,
        ReflectionClass $class
    ): object {
        $typedef = $generator->randomElement([
            'int',
            'int[10]',
            'char[42]',
            'unsigned int',
            'char',
            'unsigned char',
            'bool',
            'float',
            'double',
        ]);

        $ffi = self::getFfi();
        $type = $ffi->type($typedef);

        // CType is already the requested object.
        if ($class->name === CType::class) {
            return $type;
        }

        $res = $ffi->new($type);

        switch ($typedef) {
            case 'int':
                // @phpstan-ignore-next-line property.notFound
                $res->cdata = $generator->numberBetween(
                    -2147483647,
                    2147483647
                );
                break;

            case 'int[10]':
                self::fillIntArray(
                    $res,
                    self::createRandomIntArray($generator)
                );
                break;

            case 'char[42]':
                self::fillCharArray(
                    $res,
                    str_shuffle(
                        'abcdefghijklmnopqrstuvwxyz'
                        . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                    )
                );
                break;

            case 'unsigned int':
                // @phpstan-ignore-next-line property.notFound
                $res->cdata = $generator->numberBetween(
                    0,
                    2147483647
                );
                break;

            case 'char':
                // FFI expects a one-character string for `char`.
                // @phpstan-ignore-next-line property.notFound
                $res->cdata = $generator->randomElement(
                    str_split(
                        'abcdefghijklmnopqrstuvwxyz'
                        . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                    )
                );
                break;

            case 'unsigned char':
                // FFI expects an integer for `unsigned char`.
                // @phpstan-ignore-next-line property.notFound
                $res->cdata = $generator->numberBetween(0, 255);
                break;

            case 'bool':
                // @phpstan-ignore-next-line property.notFound
                $res->cdata = $generator->boolean();
                break;

            case 'float':
            case 'double':
                // @phpstan-ignore-next-line property.notFound
                $res->cdata = $generator->randomFloat();
                break;
        }

        return $res;
    }
}
