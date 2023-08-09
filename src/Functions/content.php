<?php declare(strict_types=1);

namespace FlatFile\Functions;

use League\CommonMark\GithubFlavoredMarkdownConverter;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use FlatFile\FileParser\ParsedFile;

function markdown(string $markdown): ParsedFile
{
    static $converter;
    if (!$converter) {
        $converter = new GithubFlavoredMarkdownConverter([]);
    }

    $content = YamlFrontMatter::parse($markdown);
    $result = new ParsedFile;
    $result->content = $converter->convert($content->body())?->getContent();
    $result->meta = $content->matter();
    return $result;
}
