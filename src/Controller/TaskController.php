<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\CategoryRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
    public function add(EntityManagerInterface $em, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, CategoryRepository $categoryRepository)
    {
        // $jsonResponse = $request->getContent();

        // $task = $serializer->deserialize($jsonResponse, Task::class, 'json');
        $taskName = $request->request->get('name');
        $taskDeadline = new \DateTime($request->request->get('deadline'));
        $taskCategory = $categoryRepository->find($request->request->get('category'));

        $task = new Task();
        $task->setName($taskName);
        $task->setDeadline($taskDeadline);
        $task->setCategory($taskCategory);
        dd($task);

        $errors = $validator->validate($task);

        if (count($errors)) {
            $errorsString = (string) $errors;

            return $this->json($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->persist($task);
        $em->flush();

        return $this->json($task, Response::HTTP_CREATED, ['Location' => $this->generateUrl('tasks_show_one', ['id' => $task->getId()])]);
    }

    /**
     * @Route("/{id<\d+>}", name="update", methods={"PUT", "PATCH"})
     */
    public function update(int $id, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em, TaskRepository $taskRepository)
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

        $jsonContent = $request->getContent();

        $serializer->deserialize($jsonContent, Task::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $task]);
        
        $errors = $validator->validate($task);

        if (count($errors)) {
            $errorsString = (string) $errors;

            return $this->json($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->flush();

        return $this->json($task, Response::HTTP_CREATED, ['Location' => $this->generateUrl('tasks_show_one', ['id' => $task->getId()])]);
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
