<?php

declare(strict_types=1);

namespace App\Helper;

use App\Entity\Quiz as QuizEntity;

class Quiz
{
    public static function getQuizAnswers(QuizEntity $quizEntity, array $answers): array
    {
        return array_filter($answers, function($value) use ($quizEntity) {
            return $value['quizId'] === $quizEntity->getId();
        });
    }
}