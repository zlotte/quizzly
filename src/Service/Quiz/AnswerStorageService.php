<?php

declare(strict_types=1);

namespace App\Service\Quiz;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Helper\Quiz as QuizHelper;
use Symfony\Component\HttpFoundation\RequestStack;

class AnswerStorageService
{
    public function __construct(private readonly RequestStack $requestStack)
    {

    }

    public function initiateStorage(Quiz $quiz, Question $question): void
    {
        $this->requestStack->getSession()->set('quiz', [
            [
                'quizId' => $quiz->getId(),
                'questionId' => $question->getId(),
                'answers' => $this->getAnswersFromSession(),
            ]
        ]); 
    }

    public function updateStorage(Quiz $quiz, Question $question): void
    {
        $answers = $this->requestStack->getSession()->get('quiz');

        if (is_array($answers) && !empty($answers)) {
            $answers = array_filter($answers, function($value) use ($question) {
                return $value['questionId'] !== $question->getId();
            });
    
            $answers[] = [
                'quizId' => $quiz->getId(),
                'questionId' => $question->getId(),
                'answers' => $this->getAnswersFromSession()
            ];
    
            $this->requestStack->getSession()->set('quiz', $answers);
        }
    }

    public function retrieveAnswersInProgress(Quiz $quiz): array
    {
        $answers = $this->requestStack->getSession()->get('quiz');

        if (!is_array($answers)) {
            $answers = [];
        }

        return QuizHelper::getQuizAnswers($quiz, $answers); 
    }

    public function retrieveFinishedQuizAnswers(Quiz $quiz): array
    {
        $finishedQuizes = $this->requestStack->getSession()->get('finishedQuizes');

        if (!is_array($finishedQuizes)) {
            return [];
        }

        $isCurrentQuizFinished = array_filter($finishedQuizes, function($value) use ($quiz) {
            return $value['quizId'] === $quiz->getId();
        });

        if (!$isCurrentQuizFinished) {
            return [];
        }

        $answers = $this->requestStack->getSession()->get('quiz');

        return QuizHelper::getQuizAnswers($quiz, $answers);
    }

    private function getAnswersFromSession(): array
    {
        return $this->requestStack
                ->getCurrentRequest()
                ->request
                ->all()['quiz_start']['answers'];
    }
}