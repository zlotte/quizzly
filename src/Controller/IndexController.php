<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    public function __construct(private readonly QuizRepository $quizRepository)
    {

    }

    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $quizzes = $this->quizRepository->findAll();

        return $this->render('/home/index.html.twig', [
            'quizzes' => $quizzes,
        ]);
    }
}