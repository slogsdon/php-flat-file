<?php declare(strict_types=1);

namespace FlatFile\Functions;

use League\CommonMark\CommonMarkConverter;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use FlatFile\FileParser\ParsedFile;

function markdown(string $markdown): ParsedFile
{
    static $converter;
    if (!$converter) {
        $converter = new CommonMarkConverter();
    }

    $content = YamlFrontMatter::parse($markdown);
    $result = new ParsedFile;
    $result->content = $converter->convertToHtml($content->body());
    $result->meta = $content->matter();
    return $result;
}
