<?php
namespace Apie\Faker\Fakers;

use Apie\Core\Attributes\FakeFile;
use Apie\Core\FileStorage\StoredFile;
use Apie\Faker\FileFakerFactory;
use Apie\Faker\Interfaces\ApieClassFaker;
use Apie\Faker\Interfaces\ApieFileFaker;
use Apie\Faker\SeededFile;
use Faker\Generator;
use Psr\Http\Message\UploadedFileInterface;
use ReflectionAttribute;
use ReflectionClass;

/** @implements ApieClassFaker<StoredFile> */
class UploadedFileFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return UploadedFileInterface::class === $class->name
            || in_array(UploadedFileInterface::class, $class->getInterfaceNames());
    }

    /**
     * @param ReflectionClass<ApieFileFaker> $class
     */
    private function getApieFileFakers(Generator $generator, ReflectionClass $class): ApieFileFaker
    {
        $interfaces = array_map(
            function (ReflectionAttribute $attribute) {
                return $attribute->newInstance()->className;
            },
            $class->getAttributes(FakeFile::class)
        );
        $pickedInterface = empty($interfaces) ? ApieFileFaker::class : $generator->randomElement($interfaces);
        $supported = FileFakerFactory::getSupportedFileFakers($pickedInterface);
        if (empty($supported)) {
            throw new \LogicException('I have no file faker that implements ' . $pickedInterface);
        }
        return $generator->randomElement($supported);
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): UploadedFileInterface
    {
        $internal = SeededFile::create(
            $generator,
            $this->getApieFileFakers($generator, $class)
        );
        $className = $class->name === UploadedFileInterface::class ? StoredFile::class : $class->name;
        return $className::createFromUploadedFile($internal);
    }
}
