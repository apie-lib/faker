<?php
namespace Apie\Faker;

use Apie\Core\Enums\UploadedFileStatus;
use Apie\Core\FileStorage\StoredFile;
use Apie\Faker\Interfaces\ApieFileFaker;
use Faker\Generator;

final class SeededFile extends StoredFile
{
    public static function create(Generator $faker, ApieFileFaker $apieFileFaker): self
    {
        $filename = $apieFileFaker->createOriginalFilename($faker);
        $mimetype = $apieFileFaker->createMimeType();
        return new self(
            UploadedFileStatus::CreatedLocally,
            clientOriginalFile: $filename,
            clientMimeType: $mimetype,
            serverMimeType: $mimetype,
            resource: $apieFileFaker->createResource($faker, $filename, $mimetype)
        );
    }
}
