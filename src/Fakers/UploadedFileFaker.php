<?php
namespace Apie\Faker\Fakers;

use Apie\Core\FileStorage\StoredFile;
use Apie\Faker\Interfaces\ApieClassFaker;
use Faker\Generator;
use Psr\Http\Message\UploadedFileInterface;
use ReflectionClass;

/** @implements ApieClassFaker<StoredFile> */
class UploadedFileFaker implements ApieClassFaker
{
    public function supports(ReflectionClass $class): bool
    {
        return UploadedFileInterface::class === $class->name
            || in_array(UploadedFileInterface::class, $class->getInterfaceNames());
    }

    public function fakeFor(Generator $generator, ReflectionClass $class): UploadedFileInterface
    {
        $originalName = $generator->word() . '.txt';
        $mimeType = $generator->mimeType();
        $contents = $generator->text();
        $className = $class->name === UploadedFileInterface::class ? StoredFile::class : $class->name;
        return $className::createFromString($contents, $mimeType, $originalName);
    }
}
