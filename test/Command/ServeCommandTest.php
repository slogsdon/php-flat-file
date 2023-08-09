<?php

namespace FlatFile\Tests\Command;

use FlatFile\Command\ServeCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ServeCommandTest extends TestCase
{
    public function testCommandRuns(): void
    {
        $application = new Application;

        $application->add(new ServeCommand());

        $command = $application->find('serve');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            '--dry-run' => true,
            '--quiet' => true,
        ));

        // the output of the command in the console
        $output = $commandTester->getDisplay();

        $this->assertStringNotContainsStringIgnoringCase('Exception', $output);
    }
}
