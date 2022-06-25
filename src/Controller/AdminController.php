<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="option_admin")
     */
    public function viewOptions(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route ("/admin/user", name="admin_user")
     */
    public function viewUsers()
    {
        //find all users
        $users=$this->getDoctrine()->getManager()->getRepository(User::class)->findAll();
        return $this->render('Admin/viewUser.html.twig',['users'=>$users]);
    }

    /**
     * @Route ("/admin/user/edit/{id}", name="edit_user")
     */
    public function editUser($id,Request $request)
    {
        //find user in the database
        $em = $this->getDoctrine()->getManager();
        //get user id from current user
        $user = $em->getRepository(User::class)->find($id);
        //create a form for edit profile
        $form=$this->createFormBuilder($user)
            ->add('fullName')
            ->add('email')
            ->add('save',SubmitType::class)
            ->getForm();
        ;
        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid())
        {
            //change email and full name
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('admin_user');
        }
        return $this->render('admin/editUser.html.twig', [
                'form' => $form->createView()]
        );
    }
}
