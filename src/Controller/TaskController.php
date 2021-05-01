<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\CategoryRepository;
use App\Repository\TaskRepository;
use App\Service\ErrorsHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/tasks", name="tasks_")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/", name="show_all", methods={"GET"})
     */
    public function showAll(TaskRepository $taskRepository)
    {
        $tasks = ['result' => $taskRepository->findWithCategoryAndSubtasks()];

        return $this->json($tasks, Response::HTTP_OK, [], ['groups' => 'task_get']);
    }

    /**
     * @Route("/{id<\d+>}", name="show_one", methods={"GET"})
     */
    public function showOne(int $id, TaskRepository $taskRepository)
    {
        $task = $taskRepository->findOneWithCategoryAndSubtasks($id);

        if ($task === null) {
            $errorMessage = [
                'error' => [
                    'code' => '404',
                    'message' => 'Not Found'
                ]
            ];

            return $this->json($errorMessage, Response::HTTP_NOT_FOUND);
        }

        $resultArray = ['result' => $task];

        return $this->json($resultArray, Response::HTTP_OK, [], ['groups' => 'task_get']);
    }

    /**
     * @Route("/", name="add", methods={"POST"})
     */
    public function add(EntityManagerInterface $em, Request $request, ErrorsHandler $errorsHandler, ValidatorInterface $validator, CategoryRepository $categoryRepository)
    {
        $taskName = $request->request->get('name');
        $taskDeadline = new \DateTime($request->request->get('deadline'));
        $taskCategory = $categoryRepository->find($request->request->get('category'));

        $task = new Task();
        $task->setName($taskName);
        $task->setDeadline($taskDeadline);
        $task->setCategory($taskCategory);

        $errors = $validator->validate($task);

        if (count($errors)) {
            return $errorsHandler->setValidationErrorsResponse($errors);
        }

        $em->persist($task);
        $em->flush();

        return $this->json(['result' => $task], Response::HTTP_CREATED, ['Location' => $this->generateUrl('tasks_show_one', ['id' => $task->getId()])], ['groups' => 'task_get']);
    }

    /**
     * @Route("/{id<\d+>}", name="update", methods={"PUT", "PATCH"})
     */
    public function update(int $id, Request $request, ErrorsHandler $errorsHandler, ValidatorInterface $validator, EntityManagerInterface $em,CategoryRepository $categoryRepository, TaskRepository $taskRepository)
    {
        $task = $taskRepository->findOneWithCategoryAndSubtasks($id);

        if ($task === null) {
            $errorMessage = [
                'error' => [
                    'code' => '404',
                    'message' => 'Not found.'
                ]
            ];

            return $this->json($errorMessage, Response::HTTP_NOT_FOUND);
        }

        $newTaskName = $request->request->get('name');
        $newTaskDeadline = new \DateTime($request->request->get('deadline'));
        $newTaskCategory = $categoryRepository->find($request->request->get('category'));

        $task->setName($newTaskName);
        $task->setDeadline($newTaskDeadline);
        $task->setCategory($newTaskCategory);
        
        $errors = $validator->validate($task);

        if (count($errors)) {
            return $errorsHandler->setValidationErrorsResponse($errors);
        }

        $em->flush();

        return $this->json([], Response::HTTP_NO_CONTENT, ['Location' => $this->generateUrl('tasks_show_one', ['id' => $task->getId()])], ['groups' => 'task_get']);
    }

    /**
     * @Route("/{id<\d+>}", name="delete", methods={"DELETE"})
     */
    public function delete(int $id, EntityManagerInterface $em, TaskRepository $taskRepository)
    {
        $task = $taskRepository->findOneWithCategoryAndSubtasks($id);

        if ($task === null) {
            $errorMessage = [
                'error' => [
                    'code' => '404',
                    'message' => 'Not found.'
                ]
            ];

            return $this->json($errorMessage, Response::HTTP_NOT_FOUND);
        }

        $em->remove($task);
        $em->flush();

        return $this->json(Response::HTTP_OK);
    }
}
