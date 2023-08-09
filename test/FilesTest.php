<?php

namespace FlatFile\Tests;

use FlatFile\Files;
use PHPUnit\Framework\TestCase;

class FilesTest extends TestCase
{
    public function testFindAllIterates(): void
    {
        $cwd = getcwd();

        if (false === $cwd) {
            $this->assertTrue(false, 'Something bad has happened');
            return;
        }

        $files = $this->generatorToArray((new Files)->findAll($cwd));

        $this->assertNotEmpty($files);
        $this->assertNotContains('.', $files);
        $this->assertNotContains('..', $files);
    }

    /**
     * @param iterable<mixed> $gen
     *
     * @return array<mixed>
     */
    protected function generatorToArray(iterable $gen): array
    {
        $arr = [];
        foreach ($gen as $key => $value) {
            $arr[$key] = $value;
        }
        return $arr;
    }
}
