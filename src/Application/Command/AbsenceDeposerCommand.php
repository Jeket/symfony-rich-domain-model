<?php

namespace App\Application\Command;

use App\Domain\AbsenceType;
use App\Domain\DTO\AbsenceDeposerDTO;
use App\Domain\Exception\AbsenceDejaDeposeeException;
use App\Domain\Exception\PersonneNonTrouveeException;
use App\Domain\Repository\PersonneRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Déposer une absence.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class AbsenceDeposerCommand extends Command
{
    protected static $defaultName = 'app:absence:deposer';

    /**
     * @var PersonneRepositoryInterface
     */
    private $personneRepository;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param PersonneRepositoryInterface $personneRepository
     * @param ValidatorInterface          $validator
     */
    public function __construct(PersonneRepositoryInterface $personneRepository, ValidatorInterface $validator)
    {
        parent::__construct();

        $this->personneRepository = $personneRepository;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Déposer une absence.')
            ->setHelp("Il n'est pas possible de déposer une absence qui chevauche une absence déjà existante.")
            ->addArgument('email', InputArgument::REQUIRED, "L'email d'une personne")
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            // Récupérer une personne demandée.
            $personnne = $this->personneRepository->get($input->getArgument('email'));
        } catch (PersonneNonTrouveeException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return;
        }

        // Construire DTO.
        $absenceDeposerDTO = new AbsenceDeposerDTO();

        // Demander l'utilisateur à entrer les nouvelles valeurs.
        $helper = $this->getHelper('question');

        $question = new Question("Date de début d'absence :", $absenceDeposerDTO->debut->format('Y-m-d'));
        $absenceDeposerDTO->debut = new \DateTimeImmutable($helper->ask($input, $output, $question));

        $question = new Question("Date de fin d'absence :", $absenceDeposerDTO->fin->format('Y-m-d'));
        $absenceDeposerDTO->fin = new \DateTimeImmutable($helper->ask($input, $output, $question));

        $question = new ChoiceQuestion("Type d'absence :", [AbsenceType::MALADIE => AbsenceType::MALADIE, AbsenceType::CONGES_PAYES => AbsenceType::CONGES_PAYES]);
        $absenceDeposerDTO->type = $helper->ask($input, $output, $question);

        // Valider la saisie de l'utilisateur
        $constraintViolationList = $this->validator->validate($absenceDeposerDTO);
        if ($constraintViolationList->count() > 0) {
            foreach ($constraintViolationList as $violation) {
                /* @var $violation ConstraintViolationInterface */
                $output->writeln(sprintf('<error>%s: %s</error>', $violation->getPropertyPath(), $violation->getMessage()));
            }

            return;
        }

        try {
            // Déposer une absence.
            $personnne->deposerAbsence($absenceDeposerDTO);

            $output->writeln('<info>Absence a été déposée avec succès.</info>');
        } catch (AbsenceDejaDeposeeException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }
}
