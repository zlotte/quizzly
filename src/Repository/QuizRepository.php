<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuizRepository extends ServiceEntityRepository
{
    public function __construct(private readonly ManagerRegistry $registry) 
    {
        parent::__construct($registry, Quiz::class);
    }

    public function save(Quiz $quiz): void
    {
        $this->_em->persist($quiz);
        $this->_em->flush();
    }
}