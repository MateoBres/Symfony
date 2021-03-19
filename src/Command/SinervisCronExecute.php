<?php

namespace App\Command;

use App\Entity\CommercialFlock\Certificates\Certificate;
use App\Entity\CourseFlock\CourseEdition;
use App\Entity\QuestionnaireFlock\CompletedQuestionnaire\TeacherAssessmentCompletedQuestionnaire;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * USAGE:
 * Command: ./bin/console sinervis:cron --script=all
 * Command: ./bin/console sinervis:cron --script=updateEditions
 * Command: ./bin/console sinervis:cron --script=all --silent
 *
 * Class SinervisCronExecute
 * @package App\Command
 */
class SinervisCronExecute extends SinervisScriptManager
{
    protected function configure()
    {
        $this
            ->setName('sinervis:cron')
            ->setDescription('Sinervis Cron Execution Command');

        parent::configure();
    }

    /**
     * Command: ./bin/console sinervis:cron --script=updateEditions --silent
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function cronUpdateEditions(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->em->getRepository(CourseEdition::class);
        $entities = $repository->findAll();

        if (!SILENT) {
            $output->writeln([' Found ' . count($entities) . ' objects', '']);
        }

        foreach ($entities as $entity) {
            $entity->updateDuration();
            $entity->updateStartDate();
            $entity->updateState();
            $this->em->persist($entity);

            if (!SILENT) {
                $output->writeln(' > Updated Edition Id ' . $entity->getId() . ': ' . $entity);
            }
        }
    }

    /**
     * Command: ./bin/console sinervis:cron --script=updateCertificates --silent
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function cronUpdateCertificates(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->em->getRepository(Certificate::class);
        $entities = $repository->findAll();

        if (!SILENT) {
            $output->writeln([' Found ' . count($entities) . ' objects', '']);
        }

        foreach ($entities as $entity) {
            $entity->updateCertificateInformations();
            $this->em->persist($entity);

            if (!SILENT) {
                $output->writeln(' > Updated Certificate Id ' . $entity->getId() . ': ' . $entity);
            }
        }
    }

    /**
     * Command: ./bin/console sinervis:cron --script=updateTeacherAssessments --silent
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function cronUpdateTeacherAssessments(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->em->getRepository(TeacherAssessmentCompletedQuestionnaire::class);
        $entities = $repository->findAll();

        if (!SILENT) {
            $output->writeln([' Found ' . count($entities) . ' objects', '']);
        }

        foreach ($entities as $entity) {
            $entity->updateStatus();
            $this->em->persist($entity);

            if (!SILENT) {
                $output->writeln(' > Updated Teacher Assessments Id ' . $entity->getId() . ': ' . $entity);
            }
        }
    }
}