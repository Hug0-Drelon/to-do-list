<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Service\ErrorsHandler;
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

        return $this->json($categories, Response::HTTP_OK, [], ['groups' => 'category_get']);
    }

    /**
     * @Route("/{id<\d+>}", name="show_one", methods={"GET"})
     */
    public function showOne(int $id, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->find($id);

        if ($category === null) {
            $errorMessage = [
                'error' => [
                    'code' => '404',
                    'message' => 'Not Found'
                ]
            ];

            return $this->json($errorMessage, Response::HTTP_NOT_FOUND);
        }

        $resultArray = ['result' => $category];

        return $this->json($resultArray, Response::HTTP_OK, [], ['groups' => 'category_get']);
    }

    /**
     * @Route("/", name="add", methods={"POST"})
     */
    public function add(Request $request,ErrorsHandler $errorsHandler, ValidatorInterface $validator, SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $categoryName = $request->request->get('name');

        $category = new Category();
        $category->setName($categoryName);

        $errors = $validator->validate($category);

        if (count($errors)) {
            return $errorsHandler->setValidationErrorsResponse($errors);
        }

        $em->persist($category);
        $em->flush();

        return $this->json(['result' => $category], Response::HTTP_CREATED, ['Location' => $this->generateUrl('categories_show_one', ['id' => $category->getId()])]);
    }

    /**
     * @Route("/{id<\d+>}", name="update", methods={"PUT", "PATCH"})
     */
    public function update(int $id, Request $request, ErrorsHandler $errorsHandler, SerializerInterface $serializer, CategoryRepository $categoryRepository, ValidatorInterface $validator, EntityManagerInterface $em)
    {
        $category = $categoryRepository->find($id);

        if ($category === null) {
            $errorMessage = [
                'error' => [
                    'code' => 404,
                    'message' => 'Not found.'
                ]
            ];

            return $this->json($errorMessage, Response::HTTP_NOT_FOUND);
        }

        $categoryName = $request->request->get('name');

        $category->setName($categoryName);
        
        $errors = $validator->validate($category);

        if (count($errors)) {
            return $errorsHandler->setValidationErrorsResponse($errors);
        }

        $em->flush();

        return $this->json([], Response::HTTP_NO_CONTENT, ['Location' => $this->generateUrl('categories_show_one', ['id' => $category->getId()])]);
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
