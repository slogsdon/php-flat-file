<?php declare(strict_types=1);

namespace FlatFile\FileParser;

use function FlatFile\Functions\markdown;

class MarkdownFileParser extends FileParserAbstract
{
    public function parse(\SplFileInfo $file, \League\Plates\Engine $plates = null): ParsedFile
    {
        $contents = file_get_contents($file->getPathName());
        if (false === $contents) {
            return new ParsedFile;
        }
        return markdown($contents);
    }

    public function supportedFileExtensions(): array
    {
        return ['md', 'markdown'];
    }
}
