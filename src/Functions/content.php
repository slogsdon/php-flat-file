<?php declare(strict_types=1);

namespace FlatFile\Functions;

use League\CommonMark\CommonMarkConverter;
use Spatie\YamlFrontMatter\YamlFrontMatter;

function markdown(string $markdown)
{
    static $converter;
    if (!$converter) {
        $converter = new CommonMarkConverter();
    }

    $content = YamlFrontMatter::parse($markdown);
    return $converter->convertToHtml($content->body());
}
