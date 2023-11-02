<?php

declare(strict_types=1);

namespace App\Tests\Service\Quiz;

use App\Entity\Question;
use App\Service\Quiz\ValidatorService;
use PHPUnit\Framework\TestCase;
use App\Entity\Quiz;

class ValidatorServiceTest extends TestCase
{
    public function testToCheckIfExceptionIsThrownWhenQuestionIsNotInQuiz(): void
    {
        $this->expectException(\Exception::class);

        $validatorService = new ValidatorService();
        $quiz = new Quiz();
        $quizOther = new Quiz();

        $question = new Question();
        $question->setQuiz($quiz);

        $validatorService->validateQuiz($quizOther, $question);
    }

    public function testToCheckIfExceptionIsNotThrownWhenQuestionIsInQuiz(): void
    {
        $validatorService = new ValidatorService();
        $quiz = new Quiz();

        $question = new Question();
        $question->setQuiz($quiz);

        $result = $validatorService->validateQuiz($quiz, $question);

        self::assertEmpty($result);
        self::assertEquals($quiz, $question->getQuiz());
    }
}