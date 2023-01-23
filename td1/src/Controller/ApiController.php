<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    public function __construct(
        UserRepository $userRepository,
        RequestStack $requestStack
    ) {
        $apiKey = $requestStack->getCurrentRequest()->headers->get('X-API-KEY');
        $user = $userRepository->findOneBy([
            'apiKey' => $apiKey
        ]);

        if (!$user) {
            throw $this->createAccessDeniedException('Invalid API key');
        }
    }

    #[Route('/tasks', name: 'api_tasks')]
    public function index(TaskRepository $taskRepository): JsonResponse
    {
        $tasks = $taskRepository->findAll();

        return $this->json($tasks);
    }

    #[Route('/tasks/todo', name: 'api_todo')]
    public function todo(TaskRepository $taskRepository): JsonResponse
    {
        $tasks = $taskRepository->findBy([
            'done' => false
        ]);

        return $this->json($tasks);
    }

    #[Route('/tasks/done', name: 'api_done')]
    public function done(TaskRepository $taskRepository): JsonResponse
    {
        $tasks = $taskRepository->findBy([
            'done' => true
        ]);

        return $this->json($tasks);
    }
}
