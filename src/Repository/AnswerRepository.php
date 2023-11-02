<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Answer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(private readonly ManagerRegistry $registry) 
    {
        parent::__construct($registry, Answer::class);
    }
}