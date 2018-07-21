<?php

namespace FlatFile\Tests\Command;

use FlatFile\Command\BuildCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class BuildCommandTest extends TestCase
{
    public function testCommandRuns()
    {
        $application = new Application;

        $application->add(new BuildCommand());

        $command = $application->find('build');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
        ));

        // the output of the command in the console
        $output = $commandTester->getDisplay();

        $this->assertNotContains('Exception', $output);
    }
}
