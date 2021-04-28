<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/tasks", name="task_")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/", name="show_all", methods={"GET"})
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

    /**
     * @Route("/", name="add", methods={"POST"})
     */
    public function add(EntityManagerInterface $em, Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $jsonResponse = $request->getContent();

        $task = $serializer->deserialize($jsonResponse, Task::class, 'json');

        $errors = $validator->validate($task);

        if (count($errors)) {
            $errorsString = (string) $errors;

            return $this->json($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->persist($task);
        $em->flush();

        return $this->json($task, Response::HTTP_CREATED, ['Location' => $this->generateUrl('task_show', ['id' => $task->getId()])]);
    }

    /**
     * @Route("/{id<\d+>}", name="update", methods={"PUT", "PATCH"})
     */
    public function update(Task $task, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em)
    {
        $jsonContent = $request->getContent();

        $serializer->deserialize($jsonContent, Task::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $task]);
        
        $errors = $validator->validate($task);

        if (count($errors)) {
            $errorsString = (string) $errors;

            return $this->json($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->flush();

        return $this->json($task, Response::HTTP_CREATED, ['Location' => $this->generateUrl('task_show', ['id' => $task->getId()])]);
    }

    /**
     * @Route("/{id<\d+>}", name="delete", methods={"DELETE"})
     */
    public function delete(Task $task, EntityManagerInterface $em)
    {
        $em->remove($task);
        $em->flush();

        return $this->json(Response::HTTP_OK);
    }
}
