<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="app_user")
     */
    public function viewProfile(): Response
    {
        if($this->isGranted('ROLE_USER'))
        {
            return $this->render('user/index.html.twig');
        }
        return $this->redirectToRoute('app_login');
    }
    /**
     * @Route ("/profile/edit/{id}", name="edit_profile")
     */
    public function editProfile($id,Request $request)
    {
        //find user in the database
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        //create a form for edit profile
        $form=$this->createFormBuilder($user)
            ->add('fullName')
            ->add('email')
            ->getForm();
        ;
        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid())
        {
            //change email and full name
           $em->persist($user);
           $em->flush();
            return $this->redirectToRoute('app_user');
        }
        return $this->render('user/edit.html.twig', [
            'form' => $form->createView()]
        );
    }

}
