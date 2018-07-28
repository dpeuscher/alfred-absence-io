<?php

namespace Dpeuscher\AlfredAbsenceIo\Tests\Command;

use Dpeuscher\AlfredAbsenceIo\Command\ConfigureCommand;
use Dpeuscher\AlfredAbsenceIo\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @category  alfred-absence-io
 * @copyright Copyright (c) 2018 Dominik Peuscher
 * @covers \Dpeuscher\AlfredAbsenceIo\Command\ConfigureCommand
 */
class ConfigureCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $_ENV['KERNEL_CLASS'] = Kernel::class;
        $_ENV['DEV'] = '[]';
        $_ENV['PM'] = '[]';
        $_ENV['TL'] = '[]';
        $_ENV['LOCATION'] = '';
        $_ENV['ABSENCEID'] = '';
        $_ENV['ABSENCEKEY'] = '';
        $_ENV['ABSENCEENDPOINT'] = 'https://app.absence.io/api/v2/';
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = new ConfigureCommand();
        $application->add($command);

        $command = $application->find('config');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'   => $command->getName(),
        ]);

        $output = $commandTester->getDisplay();
        $this->assertContains('"valid":false', $output);
    }
}
