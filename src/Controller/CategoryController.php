<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category")
 */
class CategoryController extends BaseController
{
    /**
     * @Route("/", name="category_list", methods="GET")
     */
    public function index()
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)->findAll();
        return $this->json($categories);
    }


}
