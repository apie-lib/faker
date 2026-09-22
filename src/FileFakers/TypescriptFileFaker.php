<?php
namespace Apie\Faker\FileFakers;

use Apie\Faker\Interfaces\ApieFileFaker;
use Faker\Generator;
use Nyholm\Psr7\Stream;

final class TypescriptFileFaker implements ApieFileFaker
{
    public function createOriginalFilename(Generator $faker): string
    {
        return $faker->word() . '.ts';
    }

    public function createMimeType(): string
    {
        return 'text/typescript';
    }

    public static function isSupported(): bool
    {
        return class_exists(\Apie\TypescriptCodeBuilder\Dto\File::class);
    }

    /** @return resource */
    public function createResource(Generator $faker, string $originalFilename, string $mimeType): mixed
    {
        $file = $faker->fakeClass(\Apie\TypescriptCodeBuilder\Dto\File::class);
        return Stream::create($file->toTypescript())->detach();
    }
}
