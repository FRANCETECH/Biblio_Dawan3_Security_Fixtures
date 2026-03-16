<?php

namespace App\Controller;

use App\Entity\Livre;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
//use App\Entity\User;

class HomeController extends AbstractController 
{

    #[Route('/', name: 'home')]
    public function index(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        
    /*  
    // Création d'un faut user
    
    $user = new User();
    $user->setEmail('admin@dawan.fr')
        ->setUsername('admin')
        ->setPassword($hasher->hashPassword($user, 'admin'))
        ->setRoles(["ROLE_ADMIN"]);
        $em->persist($user);
        $em->flush();
    */
        return $this->render('home/index.html.twig'); 
    }


    #[Route('/service')]
    public function service(ValidatorInterface $validator) 
    {
        $recipe = new Livre();
        $errors = $validator->validate($recipe);   
        dd((string) ($errors));  
    }


}
