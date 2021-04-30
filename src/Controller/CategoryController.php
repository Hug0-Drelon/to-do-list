<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/categories", name="categories_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="show_all", methods={"GET"})
     */
    public function showAll(CategoryRepository $categoryRepository)
    {
        $categories = ['result' => $categoryRepository->findAll()];

        return $this->json($categories, Response::HTTP_OK);
    }

    /**
     * @Route("/{id<\d+>}", name="show_one", methods={"GET"})
     */
    public function showOne(int $id, CategoryRepository $categoryRepository)
    {
        $category = ['result' => $categoryRepository->find($id)];

        if ($category === null) {
            $errorMessage = [
                'error' => [
                    'code' => '404',
                    'message' => 'Not Found'
                ]
            ];

            return $this->json($errorMessage, Response::HTTP_NOT_FOUND);
        }

        return $this->json($category, Response::HTTP_OK, [], ['groups' => 'category_get']);
    }

    /**
     * @Route("/", name="add", methods={"POST"})
     */
    public function add(Request $request, ValidatorInterface $validator, SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $jsonResponse = $request->getContent();

        $category = $serializer->deserialize($jsonResponse, Task::class, 'json');

        $errors = $validator->validate($category);

        if (count($errors)) {
            $errorsString = (string) $errors;

            return $this->json($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->persist($category);
        $em->flush();

        return $this->json($category, Response::HTTP_CREATED, ['Location' => $this->generateUrl('categories_show_one', ['id' => $category->getId()])]);
    }

    /**
     * @Route("/{id<\d+>}", name="update", methods={"PUT", "PATCH"})
     */
    public function update(int $id, Request $request, SerializerInterface $serializer, CategoryRepository $categoryRepository, ValidatorInterface $validator, EntityManagerInterface $em)
    {
        $category = $categoryRepository->find($id);

        if ($category === null) {
            $errorMessage = [
                'error' => [
                    'code' => '404',
                    'message' => 'Not found.'
                ]
            ];

            return $this->json($errorMessage, Response::HTTP_NOT_FOUND);
        }

        $jsonContent = $request->getContent();

        $serializer->deserialize($jsonContent, Task::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $category]);
        
        $errors = $validator->validate($category);

        if (count($errors)) {
            $errorsString = (string) $errors;

            return $this->json($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->flush();

        return $this->json($category, Response::HTTP_CREATED, ['Location' => $this->generateUrl('categories_show_one', ['id' => $category->getId()])]);
    }

    /**
     * @Route("/{id<\d+>}", name="delete", methods={"DELETE"})
     */
    public function delete(int $id, EntityManagerInterface $em, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->find($id);

        if ($category === null) {
            $errorMessage = [
                'error' => [
                    'code' => '404',
                    'message' => 'Not found.'
                ]
            ];

            return $this->json($errorMessage, Response::HTTP_NOT_FOUND);
        }

        $em->remove($category);
        $em->flush();

        return $this->json(Response::HTTP_OK);
    }
}
