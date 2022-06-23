<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="app_user")
     */
    public function index(): Response
    {
        if($this->isGranted('ROLE_USER'))
        {
            return $this->render('user/index.html.twig');
        }
        return $this->redirectToRoute('app_login');
    }
}
