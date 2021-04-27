<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tasks", name="task_")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/", name"show_all", methods={"GET"})
     */
    public function showAll(TaskRepository $taskRepository)
    {
        $resultArray = ['result' => $taskRepository->findAll()];

        return $this->json($resultArray);
    }

    /**
     * @Route("/{id<\d+>}", name="show", methods={"GET"})
     */
    public function show(Task $task)
    {
        return $this->json($task);
    }
}
