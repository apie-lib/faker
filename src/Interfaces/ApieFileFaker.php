<?php
namespace Apie\Faker\Interfaces;

use Faker\Generator;

interface ApieFileFaker
{
    public function createOriginalFilename(Generator $faker): string;
    public function createMimeType(): string;
    public static function isSupported(): bool;
    /** @return resource */
    public function createResource(Generator $faker, string $originalFilename, string $mimeType): mixed;
}
