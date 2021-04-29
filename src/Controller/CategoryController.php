<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
