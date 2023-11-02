<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Question;
use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(private readonly ManagerRegistry $registry) 
    {
        parent::__construct($registry, Question::class);
    }

    public function findNextQuestion(Quiz $quiz, Question $question): ?Question
    {
        $qb = $this->createQueryBuilder('q')
                    ->select('q')
                    ->where('q.id > :questionId')
                    ->setParameter(':questionId', $question->getId())
                    ->orderBy('q.id', 'DESC')
                    ->setFirstResult(0)
                    ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        if (null === $result) {
            return $quiz->getQuestions()->first();
        }

        return $result;
    }
}