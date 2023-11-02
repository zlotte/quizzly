<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Form\Type\QuizStartType;
use App\Form\Type\QuizType;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use App\Service\Quiz\AnswerStorageService;
use App\Service\Quiz\FinishService;
use App\Service\Quiz\ValidatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

class QuizController extends AbstractController
{
    public function __construct(
        private readonly QuizRepository $quizRepository,
        private readonly QuestionRepository $questionRepository,
        private readonly FinishService $finishService,
        private readonly AnswerStorageService $answersStorageService,
        private readonly ValidatorService $validatorService,
    )
    {

    }

    #[Route(path: '/create-quiz', name: 'create_quiz', methods: ['GET', 'POST'])]
    public function createQuiz(Request $request): Response
    {
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->quizRepository->save($quiz);
        }

        return $this->render('quiz/create/index.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route(path: '/quiz/{id}', name: 'show_quiz', methods: ['GET'])]
    public function showQuiz(Request $request, Quiz $quiz): Response
    {
        return $this->render('quiz/show/index.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    #[Route(
        path: '/quiz/{id}/question/{questionId}', 
        name: 'start_quiz', 
        methods: ['GET', 'POST']
        )
    ]
    public function startQuiz(
        Request $request, 
        Quiz $quiz, 
        #[MapEntity(expr: 'repository.find(questionId)')]
        Question $question
    ): Response
    {
        $this->validatorService->validateQuiz($quiz, $question);

        $form = $this->createForm(QuizStartType::class, $question, [
            'answersInProgress' => $this->answersStorageService->retrieveAnswersInProgress($quiz),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (!$request->getSession()->get('quiz')) {
                    $this->answersStorageService->initiateStorage($quiz, $question);
                } else {
                    $this->answersStorageService->updateStorage($quiz, $question);
                }
            } catch (\Exception $e) {
                $url = $request->headers->get('referer');

                return $this->redirect($url);
            }

            $nextQuestion = $this->questionRepository->findNextQuestion($quiz, $question);

            return $this->redirectToRoute('start_quiz', [
                'id' => $quiz->getId(),
                'questionId' => $nextQuestion->getId(),
            ]);
        }

        return $this->render('quiz/start.html.twig', [
            'form' => $form,
            'quizQuestionsCount' => count($quiz->getQuestions()),
            'finish' => $this->finishService->isFinishPossible($quiz),
            'quiz' => $quiz,
        ]);
    }

    #[Route(path: '/quiz/{id}/finish', name: 'finish_quiz', methods: ['GET'])]
    public function finishQuiz(Request $request, Quiz $quiz): Response
    {
        $this->finishService->finishQuiz($quiz);

        return $this->redirectToRoute('review_quiz', ['id' => $quiz->getId()]);
    }

    #[Route(path: '/quiz/{id}/review', name: 'review_quiz', methods: ['GET'])]
    public function reviewQuiz(Quiz $quiz): Response
    {
        $answers = $this->answersStorageService->retrieveFinishedQuizAnswers($quiz);

        if (empty($answers)) {
            throw new \Exception('Quiz is not finished');
        }

        return $this->render('quiz/review.html.twig', [
            'answers' => $answers,
            'quiz' => $quiz,
        ]);
    }
}