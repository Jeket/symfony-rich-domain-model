<?php

namespace App\Application\Command;

use App\Domain\Repository\PersonneRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Lister toutes les personnes.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class PersonnesListerCommand extends Command
{
    protected static $defaultName = 'app:personne:lister';

    /**
     * @var PersonneRepositoryInterface
     */
    private $personneRepository;

    /**
     * @param PersonneRepositoryInterface $personneRepository
     */
    public function __construct(PersonneRepositoryInterface $personneRepository)
    {
        parent::__construct();

        $this->personneRepository = $personneRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Lister toutes les personnes.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $personnes = $this->personneRepository->getAllInfo();

        $table = new Table($output);
        $table->setHeaders(['Email', 'Nom']);

        foreach ($personnes as $personne) {
            $table->addRow([$personne['email'], $personne['nom']]);
        }

        $table->render();
    }
}
