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
        if (in_array($class->name, [UploadedFileInterface::class, StoredFile::class])) {
            return true;
        }
        while ($class) {
            if ($class->name === StoredFile::class) {
                return true;
            }
            $class = $class->getParentClass();
        }
        return false;
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
