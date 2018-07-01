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

    /**
     * @var string[]
     */
    protected $dev;

    /**
     * @var string[]
     */
    protected $pm;

    /**
     * @var string[]
     */
    protected $tl;

    protected function configure()
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
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->dev = $this->getContainer()->getParameter('dev');
        $this->tl = $this->getContainer()->getParameter('tl');
        $this->pm = $this->getContainer()->getParameter('pm');
        $this->dateHelper = $this->getContainer()->get(DateHelper::class);
        $this->teamMapperService = $this->getContainer()->get(TeamMapperService::class);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fromMonth = $input->getArgument('fromMonth');
        $toMonth = $input->getArgument('toMonth');
        list($begin, $end) = $this->dateHelper->buildDateTimeRangeFromTwoInputs($fromMonth, $toMonth);

        $output->write($this->teamMapperService->checkTeamAvailability($begin, $end));
    }
}
