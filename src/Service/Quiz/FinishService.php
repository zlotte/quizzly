<?php

declare(strict_types=1);

namespace App\Service\Quiz;

use App\Entity\Quiz;
use App\Helper\Quiz as QuizHelper;
use Symfony\Component\HttpFoundation\RequestStack;

class FinishService
{
    public function __construct(private readonly RequestStack $requestStack)
    {

    }

    public function isFinishPossible(Quiz $quiz): bool
    {
        $answers = $this->requestStack->getSession()->get('quiz');

        if (!is_array($answers)) {
            return false;
        }

        $currentQuizAnswers = QuizHelper::getQuizAnswers($quiz, $answers);

        return count($currentQuizAnswers) === $quiz->getQuestions()->count();
    }

    public function finishQuiz(Quiz $quiz): void
    {
        if (!$this->isFinishPossible($quiz)) {
            return;
        }

        $finishedQuizes = $this->requestStack->getSession()->get('finishedQuizes');

        if (!$finishedQuizes) {
            $finishedQuizes = [];
            $finishedQuizes[] = ['quizId' => $quiz->getId()];

            $this->requestStack->getSession()->set('finishedQuizes', $finishedQuizes);

            return;
        }

        $finishedQuizes[] = ['quizId' => $quiz->getId()];

        $this->requestStack->getSession()->set('finishedQuizes', $finishedQuizes);
    }
}