<?php

namespace App\Controller;

use App\Entity\Subtask;
use App\Repository\SubtaskRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/subtasks", name="subtasks_")
 */
class SubtaskController extends AbstractController
{
     /**
      * @Route("/", name="show_all", methods={"GET"})
      */
    public function showAll(SubtaskRepository $subtaskRepository)
    {
        $subtasks = ['result' => $subtaskRepository->findAll()];

        return $this->json($subtasks, Response::HTTP_OK, [], ['groups' => 'subtask_get']);
    }

    /**
     * @Route("/{id<\d+>}", name="show_one", methods={"GET"})
     */
    public function showOne(int $id, SubtaskRepository $subtaskRepository)
    {
        $subtask = $subtaskRepository->find($id);

        if ($subtask === null) {
            $errorMessage = [
                'error' => [
                    'code' => '404',
                    'message' => 'Not Found'
                ]
            ];

            return $this->json($errorMessage, Response::HTTP_NOT_FOUND);
        }

        $resultArray = ['result' => $subtask];

        return $this->json($resultArray, Response::HTTP_OK, [], ['groups' => 'subtask_get']);
    }

    /**
     * @Route("/", name="add", methods={"POST"})
     */
    public function add(EntityManagerInterface $em, Request $request, TaskRepository $taskRepository, ValidatorInterface $validator)
    {
        $subtaskName = $request->request->get('name');
        $subtaskTask = $taskRepository->find($request->request->get('task'));

        $subtask = new Subtask();
        $subtask->setName($subtaskName);
        $subtask->setTask($subtaskTask);

        $errors = $validator->validate($subtask);

        if (count($errors)) {
            $errorsString = (string) $errors;

            return $this->json($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->persist($subtask);
        $em->flush();

        return $this->json(['result' => $subtask], Response::HTTP_CREATED, ['Location' => $this->generateUrl('subtasks_show_one', ['id' => $subtask->getId()])], ['groups' => 'subtask_get']);
    }

    /**
     * @Route("/{id<\d+>}", name="update", methods={"PUT", "PATCH"})
     */
    public function update(int $id, Request $request, TaskRepository $taskRepository, ValidatorInterface $validator, EntityManagerInterface $em, SubtaskRepository $subtaskRepository)
    {
        $subtask = $subtaskRepository->find($id);

        if ($subtask === null) {
            $errorMessage = [
                'error' => [
                    'code' => '404',
                    'message' => 'Not found.'
                ]
            ];

            return $this->json($errorMessage, Response::HTTP_NOT_FOUND);
        }

        $subtaskName = $request->request->get('name');
        $subtaskTask = $taskRepository->find($request->request->get('task'));

        $subtask->setName($subtaskName);
        $subtask->setTask($subtaskTask);

        $errors = $validator->validate($subtask);

        if (count($errors)) {
            $errorsString = (string) $errors;

            return $this->json($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->flush();

        return $this->json([], Response::HTTP_NO_CONTENT, ['Location' => $this->generateUrl('subtasks_show_one', ['id' => $subtask->getId()])], ['groups' => 'subtask_get']);
    }

    /**
     * @Route("/{id<\d+>}", name="delete", methods={"DELETE"})
     */
    public function delete(int $id, EntityManagerInterface $em, SubtaskRepository $subtaskRepository)
    {
        $subtask = $subtaskRepository->find($id);

        if ($subtask === null) {
            $errorMessage = [
                'error' => [
                    'code' => '404',
                    'message' => 'Not found.'
                ]
            ];

            return $this->json($errorMessage, Response::HTTP_NOT_FOUND);
        }

        $em->remove($subtask);
        $em->flush();

        return $this->json(Response::HTTP_OK);
    }
}
