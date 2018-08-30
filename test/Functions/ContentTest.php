<?php

namespace FlatFile\Tests\Functions;

use function FlatFile\Functions\markdown;
use PHPUnit\Framework\TestCase;

class ContentTest extends TestCase
{
    public function testMarkdownSucceeds()
    {
        $expected = "<h1>hello</h1>\n";
        $actual = markdown('# hello')->content;
        $this->assertEquals($expected, $actual);
    }
}
