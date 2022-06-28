<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Form\UserType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    //make password encoder
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    //see all options
    /**
     * @Route("/admin", name="admin_option")
     */
    public function viewOptions(): Response
    {
        return $this->render('admin/viewOption.html.twig');
    }
    //see all users
    /**
     * @Route ("/admin/user", name="admin_user")
     */
    public function viewUsers()
    {
        //find all users
        $users=$this->getDoctrine()->getManager()->getRepository(User::class)->findAll();
        return $this->render('Admin/viewUser.html.twig',['users'=>$users]);
    }
    //create user
    /**
     * @Route ("/admin/user/create", name="admin_create_user")
     */
    public  function createUser(Request $request)
    {
        $user=new User();
        $form=$this->createFormBuilder($user)
            ->add('fullName')
            ->add('email')
//            ->add('roles',CollectionType::class[
//                'entry'
//                ])
            ->add('password',RepeatedType::class,[
                'type'=>PasswordType::class,
                'required'=>true,
                'first_options'=>['label'=>'Password'],
                'second_options'=>['label'=>'Confirm Password']
            ])
            //is this admin
           ->add('roles', ChoiceType::class,
                ['label' => 'form.label.role',

                'choices' => [
                    'admin' => 'ROLE_ADMIN',
                    'user' => 'ROLE_USER'
                ],

                'multiple'  => true,
                'expanded' => true,
                'required' => true,
            ])

            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid())
        {
            //hash the plain password before writing to the db
            $user->setPassword(
                $this->passwordEncoder->encodePassword($user,$user->getPassword())
            );
            //save to the db
            $em=$this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('admin_user');

        }
        return $this->render('admin/createUser.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    //edit user
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
    //delete user
    /**
     * @Route ("/admin/user/delete/{id}", name="delete_user")
     */
    public function deleteUser($id)
    {
        $em=$this->getDoctrine()->getManager();
        $user=$em->getRepository(User::class)->find($id);
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('admin_option');
    }
    //view all products
    /**
     * @Route ("/admin/product", name="admin_product")
     */
    public function viewProduct()
    {
        $products=$this->getDoctrine()->getManager()->getRepository(Product::class)->findAll();
        return $this->render('Admin/viewProduct.html.twig',['products'=>$products]);
    }
    //create product
    /**
     * @Route ("/admin/product/create", name="create_product")
     */
    public function createProduct(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //get product id from current user
        $product = new Product();
        //create a form for edit product
        $form=$this->createFormBuilder($product)
            ->add('name')
            ->add('category')
            ->add('price')
            ->add('description')

            ->getForm();
        ;
        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid())
        {
            //save changes
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('admin_product');
        }
        return $this->render('admin/createProduct.html.twig', [
                'form' => $form->createView()]
        );
    }
    //edit product
    /**
     * @Route ("/admin/product/edit/{id}", name="edit_product")
     */
    public function editProduct($id,Request $request)
    {
        //find product in the database
        $em = $this->getDoctrine()->getManager();
        //get product id from current user
        $product = $em->getRepository(Product::class)->find($id);
        //create a form for edit product
        $form=$this->createFormBuilder($product)
            ->add('name')
            ->add('category')
            ->add('price')
            ->add('description')
            ->getForm();
        ;
        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid())
        {
            //save changes
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('admin_product');
        }
        return $this->render('admin/editProduct.html.twig', [
                'form' => $form->createView()]
        );
    }

    //delete product

    /**
     * @Route ("/admin/product/delete/{id}", name="delete_product")
     */
    public function deleteProduct($id)
    {
        $em=$this->getDoctrine()->getManager();
        $product=$em->getRepository(Product::class)->find($id);
        $em->remove($product);
        $em->flush();
        return $this->redirectToRoute('admin_product');
    }

    //view all categories
    /**
     * @Route ("/admin/category", name="admin_category")
     */
    public function viewCategory()
    {
        $categories=$this->getDoctrine()->getManager()->getRepository(Category::class)->findAll();
        return $this->render('Admin/viewCategory.html.twig',['categories'=>$categories]);
    }

    //create category
    /**
     * @Route ("/admin/category/create", name="create_category")
     */
    public function createCategory(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //get category id from current user
        $category = new Category();
        //create a form for edit category
        $form=$this->createFormBuilder($category)
            ->add('name')

            ->getForm();
        ;
        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid())
        {
            //save changes
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('admin_category');
        }
        return $this->render('admin/createCategory.html.twig', [
                'form' => $form->createView()]
        );
    }

    //edit category
    /**
     * @Route ("/admin/category/edit/{id}", name="edit_category")
     */
    public function editCategory($id,Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //get category id from current user
        $category = $this->getDoctrine()->getManager()->getRepository(Category::class)->find($id);
        //create a form for edit category
        $form=$this->createFormBuilder($category)
            ->add('name')
            ->getForm();
        ;
        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid())
        {
            //save changes
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('admin_category');
        }
        return $this->render('admin/editCategory.html.twig', [
                'form' => $form->createView()]
        );
    }

    //delete category
    /**
     * @Route ("/admin/category/delete/{id}", name="delete_category")
     */
    public function deleteCategory($id)
    {
        $em=$this->getDoctrine()->getManager();
        $category=$em->getRepository(Category::class)->find($id);
        $em->remove($category);
        $em->flush();
        return $this->redirectToRoute('admin_category');
    }
}
