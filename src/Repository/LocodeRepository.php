<?php

namespace App\Repository;

use App\Entity\Locode;
use App\Entity\Token;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LocodeRepository extends ServiceEntityRepository
{
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
        parent::__construct($managerRegistry, Locode::class);


    }

    public function save(Locode $locode, bool $flush = false)
    {
        $entity = $this->getEntityManager();
        $entity->persist($locode);
        if ($flush) {
            $entity->flush();
        }
    }



}