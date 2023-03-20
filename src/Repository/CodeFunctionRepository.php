<?php

namespace App\Repository;

use App\Entity\CodeFunction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CodeFunctionRepository extends ServiceEntityRepository
{
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
        parent::__construct($managerRegistry, CodeFunction::class);


    }

    public function save(CodeFunction $codeFunction, bool $flush = false)
    {
        $entity = $this->getEntityManager();
        $entity->persist($codeFunction);
        if ($flush) {
            $entity->flush();
        }
    }




}