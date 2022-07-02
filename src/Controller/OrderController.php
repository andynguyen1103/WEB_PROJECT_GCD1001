<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Form\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderController extends AbstractController
{
    /**
     * @Route("/order",name="order_list")
     */
    public function listAction(UserInterface $user) {
        $currentUser=$this->getDoctrine()->getManager()->getRepository(User::class)->find($user->getId());
        $orders = $currentUser->getOrders();
        return $this->render('order/index.html.twig',['orders'=>$orders]);
    }

    /**
     * @Route("/order/{id}",name="order_details")
     */
    public function detailsAction(Order $id) {
        $orders = $this->getDoctrine()
            ->getManager()
            ->getRepository(Order::class)
            ->find($id);
        return $this->render('order/details.html.twig',['orders'=>$orders]);
    }

    /**
     * @Route("/order/delete/{id}",name="order_delete")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository(Order::class)->find($id);
        $em->remove($order);
        $em->flush();
        $this->addFlash('error','Order deleted');
        return $this->redirectToRoute('order_list');
    }

    /**
     * @Route("/order/product/{id}", name="order_create", methods={"GET","POST"})
     */
    public function createAction($id,Request $request,UserInterface $user)
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $product = $this->getDoctrine()->getManager()->getRepository(Product::class)->find($id);

        if ($this->saveChanges($form, $request, $order,$id,$user)) {
            $this->addFlash(
                'notice',
                'Order Added'
            );
            return $this->redirectToRoute('order_list');
        }

        return $this->render('order/create.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }
    public function saveChanges($form, $request,Order $order,$id,$user)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentUser=$this->getDoctrine()->getManager()->getRepository(User::class)->find($user->getId());
            $product=$this->getDoctrine()->getManager()->getRepository(Product::class)->find($id);
            $orderDate = new \DateTime();
            $order->setUser($currentUser)
                  ->setProduct($product)
                  ->setOrderDate($orderDate);
            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();
            return true;
        }
        return false;
    }
}