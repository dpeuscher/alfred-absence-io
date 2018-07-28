<?php

namespace Dpeuscher\AlfredAbsenceIo\Tests\Command;

use Dpeuscher\AbsenceIo\Service\AbsenceService;
use Dpeuscher\AlfredAbsenceIo\Command\AbsenceIoCommand;
use Dpeuscher\AlfredAbsenceIo\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @category  alfred-absence-io
 * @copyright Copyright (c) 2018 Dominik Peuscher
 * @covers \Dpeuscher\AlfredAbsenceIo\Command\AbsenceIoCommand
 */
class AbsenceIoCommandTest extends KernelTestCase
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

        $command = new AbsenceIoCommand();
        $application->add($command);

        $fixtureFolder = realpath(\dirname(__DIR__) . '/fixtures/');
        $activeMock = ['post_locations.json', 'post_absences.json'];

        $absenceServiceMock = $this->getMockBuilder(AbsenceService::class)->setConstructorArgs(['', '', ''])
            ->setMethods(['executeCall'])->getMock();
        $absenceServiceMock->expects($this->any())
            ->method('executeCall')
            ->withAnyParameters()
            ->will($this->returnCallback(
                function () use ($fixtureFolder, &$activeMock) {
                    $mockFile = array_shift($activeMock);
                    $data = json_decode(file_get_contents($fixtureFolder . '/' . $mockFile),
                        JSON_OBJECT_AS_ARRAY);
                    return [$data['response'], $data['headerSize']];
                }
            ));

        $kernel->getContainer()->set(AbsenceService::class, $absenceServiceMock);

        $command = $application->find('absence');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'   => $command->getName(),
            'fromMonth' => '1',
            'toMonth'   => '7',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertContains('01.01.-01.07.', $output);
    }
}
