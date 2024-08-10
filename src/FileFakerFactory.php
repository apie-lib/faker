<?php
namespace Apie\Faker;

use Apie\Common\ValueObjects\EntityNamespace;
use Apie\Faker\Interfaces\ApieFileFaker;
use ReflectionClass;

final class FileFakerFactory
{
    /** @var array<class-string<ApieFileFaker>, ApieFileFaker> $instantiated */
    private static array $instantiated = [];

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @param ReflectionClass<ApieFileFaker> $class
     */
    private static function getOrCreate(ReflectionClass $class): ApieFileFaker
    {
        if (!isset(self::$instantiated[$class->name])) {
            self::$instantiated[$class->name] = $class->newInstance();
        }
        return self::$instantiated[$class->name];
    }

    /**
     * @template T of ApieFileFaker
     * @param class-string<T> $interface
     * @return array<int, T>
     */
    public static function getSupportedFileFakers(string $interface = ApieFileFaker::class): array
    {
        $ns = new EntityNamespace('Apie\Faker\FileFakers');
        $supportedFileFakers = [];
        foreach ($ns->getClasses(__DIR__ . '/FileFakers') as $class) {
            if (in_array($interface, $class->getInterfaceNames())
                && $class->getMethod('isSupported')->invoke(null)) {
                $supportedFileFakers[] = self::getOrCreate($class);
            }
        }
        return $supportedFileFakers;
    }
}
