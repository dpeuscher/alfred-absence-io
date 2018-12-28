<?php

namespace Dpeuscher\AlfredAbsenceIo\Command;

use Dpeuscher\AbsenceIo\Service\TeamMapperService;
use Dpeuscher\Util\Date\DateHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @category  alfred-absence-io
 * @copyright Copyright (c) 2018 Dominik Peuscher
 */
class AbsenceIoCommand extends Command
{
    /**
     * @var DateHelper
     */
    protected $dateHelper;

    /**
     * @var TeamMapperService
     */
    protected $teamMapperService;

    /**
     * AbsenceIoCommand constructor.
     *
     * @param DateHelper $dateHelper
     * @param TeamMapperService $teamMapperService
     */
    public function __construct(DateHelper $dateHelper, TeamMapperService $teamMapperService)
    {
        parent::__construct();
        $this->dateHelper = $dateHelper;
        $this->teamMapperService = $teamMapperService;
    }

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
