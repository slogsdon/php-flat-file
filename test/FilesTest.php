<?php

namespace FlatFile\Tests;

use FlatFile\Files;
use PHPUnit\Framework\TestCase;

class FilesTest extends TestCase
{
    public function testFindAllIterates()
    {
        $files = iterator_to_array((new Files)->findAll(getcwd()));

        $this->assertNotEmpty($files);
        $this->assertNotContains('.', $files);
        $this->assertNotContains('..', $files);
    }
}
