<?php

declare(strict_types=1);

namespace App\Tests\Service\Quiz;

use App\Entity\Quiz;
use App\Service\Quiz\AnswerStorageService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Question;

class AnswerStorageServiceTest extends TestCase
{
    public function testToCheckThatRetrieveFinishedQuizAnswersMethodReturnsEmptyArray(): void
    {
        $requestStackMock = $this->getMockBuilder(RequestStack::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();  

        $sessionMock = $this->getMockBuilder(SessionInterface::class)
                              ->disableOriginalConstructor()
                              ->getMock();

        $sessionMock->invalidate();

        $requestStackMock->expects($this->once())->method('getSession')->willReturn($sessionMock);

        $quiz = new Quiz();
        $question = new Question();
        $anotherQuestion = new Question();

        $quiz->addQuestion($question);
        $quiz->addQuestion($anotherQuestion);

        $sessionMock->expects($this->once())->method('get')->willReturn([]);

        $answerStorageService = new AnswerStorageService($requestStackMock);

        $result = $answerStorageService->retrieveFinishedQuizAnswers($quiz);

        self::assertEmpty($result);
        self::assertArrayNotHasKey('quizId', $result);
        self::assertArrayNotHasKey('questionId', $result);
        self::assertArrayNotHasKey('answers', $result);
    }

    public function testToCheckThatRetrieveFinishedQuizAnswersWorks(): void
    {
        $requestStackMock = $this->getMockBuilder(RequestStack::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();  

        $sessionMock = $this->getMockBuilder(SessionInterface::class)
                              ->disableOriginalConstructor()
                              ->getMock();

        $sessionMock->invalidate();

        $requestStackMock->expects($this->exactly(2))->method('getSession')->willReturn($sessionMock);

        $quiz = new Quiz();
        $question = new Question();
        $anotherQuestion = new Question();

        $quiz->addQuestion($question);
        $quiz->addQuestion($anotherQuestion);

        $sessionMock->expects($this->exactly(2))->method('get')->willReturn([
            [
                'quizId' => null,
            ]
        ]
        );

        $answerStorageService = new AnswerStorageService($requestStackMock);

        $result = $answerStorageService->retrieveFinishedQuizAnswers($quiz);

        self::assertNotEmpty($result);
        self::assertArrayHasKey('quizId', $result[0]);
        self::assertArrayNotHasKey('questionId', $result[0]);
        self::assertArrayNotHasKey('answers', $result[0]);
    }

    public function testToCheckThatRetrieveAnswersInProgressIsPossible(): void
    {
        $requestStackMock = $this->getMockBuilder(RequestStack::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();  

        $sessionMock = $this->getMockBuilder(SessionInterface::class)
                              ->disableOriginalConstructor()
                              ->getMock();

        $sessionMock->invalidate();

        $requestStackMock->expects($this->once())->method('getSession')->willReturn($sessionMock);

        $quiz = new Quiz();
        $question = new Question();
        $anotherQuestion = new Question();

        $quiz->addQuestion($question);
        $quiz->addQuestion($anotherQuestion);

        $sessionMock->expects($this->once())->method('get')->willReturn([
            [
                'quizId' => $quiz->getId(),
                'questionId' => $question->getId(),
                'answers' => [13, 14]
            ]
        ]
        );

        $answerStorageService = new AnswerStorageService($requestStackMock);

        $result = $answerStorageService->retrieveAnswersInProgress($quiz);

        self::assertNotEmpty($result);
        self::assertArrayHasKey('quizId', $result[0]);
        self::assertArrayHasKey('questionId', $result[0]);
        self::assertArrayHasKey('answers', $result[0]);
    }

    public function testToCheckThatStorageIsBeingUpdated(): void
    {
        $requestStackMock = $this->getMockBuilder(RequestStack::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();  

        $sessionMock = $this->getMockBuilder(SessionInterface::class)
                              ->disableOriginalConstructor()
                              ->getMock();
    
        $sessionMock->invalidate();

        $request = new Request([], ['quiz_start' => ['answers' => []]]);  

        $quiz = new Quiz();
        $question = new Question();
        $anotherQuestion = new Question();

        $quiz->addQuestion($question);
        $quiz->addQuestion($anotherQuestion);

        $answerStorageService = new AnswerStorageService($requestStackMock);

        $requestStackMock->expects($this->exactly(2))->method('getSession')->willReturn($sessionMock);

        $sessionMock->expects($this->exactly(2))->method('get')->willReturn([
            [
                'quizId' => $quiz->getId(),
                'questionId' => $question->getId(),
                'answers' => [13, 14]
            ]
        ]
        );

        $requestStackMock->expects($this->once())->method('getCurrentRequest')->willReturn($request);

        $result = $answerStorageService->updateStorage($quiz, $question);

        self::assertEquals(count($sessionMock->get('quiz')), 1);
        self::assertEmpty($result);
    }

    public function testToCheckThatStorageIsBeingInitiated(): void
    {
        $requestStackMock = $this->getMockBuilder(RequestStack::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();

        $request = new Request([], ['quiz_start' => ['answers' => []]]);  

        $quiz = new Quiz();
        $question = new Question();
        $anotherQuestion = new Question();

        $quiz->addQuestion($question);
        $quiz->addQuestion($anotherQuestion);

        $answerStorageService = new AnswerStorageService($requestStackMock);

        $requestStackMock->expects($this->once())->method('getCurrentRequest')->willReturn($request);

        $result = $answerStorageService->initiateStorage($quiz, $question);

        self::assertEmpty($result);
    }
}