<?php

declare(strict_types=1);

namespace App\Tests\Service\Quiz;

use App\Entity\Quiz;
use App\Service\Quiz\FinishService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Question;

class FinishServiceTest extends TestCase
{
    public function testToCheckThatQuizFinishes(): void
    {
        $quiz = new Quiz();
        $question = new Question();
        $anotherQuestion = new Question();

        $quiz->addQuestion($question);
        $quiz->addQuestion($anotherQuestion);

        $quizData = [
                [
                    'quizId' => null,
                    'questionId' => 1,
                    'answers' => [14, 13],
                ],
                [
                    'quizId' => null,
                    'questionId' => 2,
                    'answers' => [33, 25],
                ],
        ];


        $requestStackMock = $this->getMockBuilder(RequestStack::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();                    
        
        $sessionMock = $this->getMockBuilder(SessionInterface::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $sessionMock->invalidate();

        $requestStackMock->expects(self::exactly(3))->method('getSession')->willReturn($sessionMock);
        $sessionMock->expects(self::exactly(3))->method('get')->willReturn($quizData);

        $finishService = new FinishService($requestStackMock);

        $result = $finishService->finishQuiz($quiz);

        self::assertEquals(count($sessionMock->get('finishedQuizes')), 2);
        self::assertNull($result);
    }

    public function testToCheckThatQuizFinishIsNotPossible(): void
    {
        $quiz = new Quiz();
        $question = new Question();
        $anotherQuestion = new Question();

        $quiz->addQuestion($question);
        $quiz->addQuestion($anotherQuestion);

        $requestStackMock = $this->getMockBuilder(RequestStack::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();                    

        $finishService = new FinishService($requestStackMock);

        $result = $finishService->finishQuiz($quiz);

        self::assertNull($result);
    }

    public function testToCheckThatFinishingQuizIsNotPossibleWhenAllQuestionsAreNotAnswered(): void
    {
        $quiz = new Quiz();
        $question = new Question();
        $anotherQuestion = new Question();

        $quiz->addQuestion($question);
        $quiz->addQuestion($anotherQuestion);

        $quizData = [
                [
                    'quizId' => null,
                    'questionId' => 1,
                    'answers' => [14, 13],
                ],
        ];

        $requestStackMock = $this->getMockBuilder(RequestStack::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();                    
        
        $sessionMock = $this->getMockBuilder(SessionInterface::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $sessionMock->invalidate();

        $requestStackMock->expects(self::once())->method('getSession')->willReturn($sessionMock);
        $sessionMock->expects(self::once())->method('get')->willReturn($quizData);

        $finishService = new FinishService($requestStackMock);

        $result = $finishService->isFinishPossible($quiz);

        self::assertFalse($result);
    }

    public function testToCheckThatFinishingQuizIsPossibleWhenAllQuestionsAreAnswered(): void
    {
        $quiz = new Quiz();
        $question = new Question();
        $anotherQuestion = new Question();

        $quiz->addQuestion($question);
        $quiz->addQuestion($anotherQuestion);

        $quizData = [
                [
                    'quizId' => null,
                    'questionId' => 1,
                    'answers' => [14, 13],
                ],
                [
                    'quizId' => null,
                    'questionId' => 2,
                    'answers' => [8],
                ],
        ];

        $requestStackMock = $this->getMockBuilder(RequestStack::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();                    
        
        $sessionMock = $this->getMockBuilder(SessionInterface::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        $sessionMock->invalidate();

        $requestStackMock->expects(self::once())->method('getSession')->willReturn($sessionMock);
        $sessionMock->expects(self::once())->method('get')->willReturn($quizData);

        $finishService = new FinishService($requestStackMock);

        $result = $finishService->isFinishPossible($quiz);

        self::assertTrue($result);
    }
}