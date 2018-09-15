<?php declare(strict_types=1);

namespace FlatFile\FileParser;

interface FileParserInterface
{
    public function canParse(\SplFileInfo $file): bool;
    public function parse(\SplFileInfo $file): \stdClass;
    public function supportedFileExtensions(): array;
}
