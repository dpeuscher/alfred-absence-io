<?php

namespace Dpeuscher\AlfredAbsenceIo\Command;

use Dpeuscher\AbsenceIo\Service\TeamMapperService;
use Dpeuscher\Util\Date\DateHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @category  alfred-absence-io
 * @copyright Copyright (c) 2018 Dominik Peuscher
 */
class AbsenceIoCommand extends ContainerAwareCommand
{
    /**
     * @var DateHelper
     */
    protected $dateHelper;

    /**
     * @var TeamMapperService
     */
    protected $teamMapperService;

    protected function configure(): void
    {
        $this
            ->setName('absence')
            ->addArgument(
                'fromMonth',
                InputArgument::REQUIRED
            )->addArgument(
                'toMonth',
                InputArgument::OPTIONAL
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->dateHelper = $this->getContainer()->get(DateHelper::class);
        $this->teamMapperService = $this->getContainer()->get(TeamMapperService::class);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $fromMonth = $input->getArgument('fromMonth');
        $toMonth = $input->getArgument('toMonth');
        [$begin, $end] = $this->dateHelper->buildDateTimeRangeFromTwoInputs($fromMonth, $toMonth);

        $output->write($this->teamMapperService->checkTeamAvailability($begin, $end));
    }
}
