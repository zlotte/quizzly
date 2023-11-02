<?php

declare(strict_types=1);

namespace App\Service\Quiz;
use App\Entity\Question;
use App\Entity\Quiz;

class ValidatorService
{
    public function validateQuiz(Quiz $quiz, Question $question): void
    {
        if ($question->getQuiz() !== $quiz) {
            throw new \Exception('cheating');
        }
    }
}