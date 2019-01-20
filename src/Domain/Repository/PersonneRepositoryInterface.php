<?php

namespace App\Domain\Repository;

use App\Domain\Exception\PersonneNotFoundException;
use App\Domain\Personne;

/**
 * Le repository pour l'entité @see Personne.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
interface PersonneRepositoryInterface
{
    /**
     * Récupérer toutes les personnes.
     *
     * @return Personne[]
     */
    public function getAll(): array;

    /**
     * Récupérer une personne par son identifiant (email).
     *
     * @param string $email
     *
     * @return Personne
     *
     * @throws PersonneNotFoundException
     */
    public function get(string $email): Personne;

    /**
     * Sauvegarder une personne.
     *
     * @param Personne $personne
     */
    public function save(Personne $personne): void;

    /**
     * Vérifier si la personne avec cet email existe déjà.
     *
     * @param string $email
     *
     * @return bool
     */
    public function emailAlreadyExist(string $email): bool;

    /**
     * Vérifier s'il existe déjà une absence qui chevauche les dates fournies.
     *
     * @param Personne $personne
     * @param \DateTimeImmutable $debut
     * @param \DateTimeImmutable $fin
     *
     * @return bool
     */
    public function absenceAlreadyExist(Personne $personne, \DateTimeImmutable $debut, \DateTimeImmutable $fin): bool;
}