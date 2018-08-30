<?php declare(strict_types=1);

namespace FlatFile\FileParser;

abstract class FileParserAbstract implements FileParserInterface
{
    public function canParse(\SplFileInfo $file): bool
    {
        return in_array(
            strtolower($file->getExtension()),
            $this->supportedFileExtensions()
        );
    }
}
