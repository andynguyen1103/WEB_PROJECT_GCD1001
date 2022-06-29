<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/admin/product", name="product_list")
     */
    public function listAction() {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();
        return $this->render('product/index.html.twig',['products'=>$products]);
    }

    /**
     * @Route("/product/details/{id}",name="product_details")
     */
    public function detailsAction($id) {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);
        return $this->render('product/details.html.twig',['products'=>$products]);
    }

    /**
     * @Route("/product/{category}", name="find_product_by_category")
     */
    public function findProductByCategory($category)
    {
        $cat=$this->getDoctrine()
            ->getManager()
            ->getRepository(Category::class)
            ->findOneBy(['name'=>$category]);
        $products = $cat->getProducts();
        return $this->render('product/index.html.twig', array(
            'products' => $products,
        ));
    }
}
