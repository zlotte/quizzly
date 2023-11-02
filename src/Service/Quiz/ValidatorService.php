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

    public function validateQuizCode(Quiz $quiz, ?string $quizCode): void
    {
        $quizCodes = $quiz->getQuizCodes()->toArray();
        $quizCodeExist = array_filter($quizCodes, function($value) use ($quizCode) {
            return $quizCode === $value->getCode();
        });

        if (count($quizCodeExist) === 0) {
            throw new \Exception('Code is not valid');
        }

        return;
    }
}