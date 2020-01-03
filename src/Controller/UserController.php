<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;





class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }


    /**
     * @Route("/user/add", name="user",methods={"POST"})
     */
    public function createUser(Request $request):Response
    {
        /** @var Serializer $serializer*/
        $serializer=$this->get('serializer');
        $userpost=$serializer->deserialize($request->getContent(), User::class,'json');
        $user=$this->getDoctrine()->getManager();
        $user->persist($userpost);
        $user->flush();
        return $this->json($userpost);


        /*$entityManager=$this->getDoctrine()->getManager();
        $user= new User();
        $user->setFio('Aleksandr Tihonyuk');
        $user->setPhoneNumber('0992445148');
        //creating date and post in db
        $date=date_create();
        date_date_set($date,2020,10,30);
        $user-> setBirthdaydate($date);
        $user->setEmail('tihonyuk1999@gmail.com');
        $user->setSex('null');

        $entityManager->persist($user);

        $entityManager->flush();

        return new Response('New user created with id'. $user->getId());*/
    }
    /**
     * @Route("/user/{id}", name="user_show")
     */
    public function show($id)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
        //return new Response('Name of this user: '.$user->getFio());
        $response = new Response(json_encode($user));
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }
    /**
     * @Route("/user_all", name="user_show_all")
     */
    public function show_all()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        if (!$user) {
            throw $this->createNotFoundException(
                'No users '
            );
        }

        $serializer = new Serializer($normalizers, $encoders);
        //return new Response($serializer->serialize($user, 'json'));
        $response = new Response($serializer->serialize($user, 'json'));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }
}
