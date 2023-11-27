<?php

namespace App\Controller;

use App\Document\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/connect/{email}', name: 'app_user_connect')]
    public function connect(string $email, SessionInterface $session){
        
        $session->set('email', $email);

        return $this->render('login/success.html.twig', [
            'message' => "Utilisateur $email connecté.",
        ]);
    }

    #[Route('/list', name: 'app_user_list')]
    public function index( SessionInterface $session, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $userId = $session->get('id');
        $user = $userRepository->findUserById($userId);
        $firstname = null;
        $firstname = $user->getFirstName();
        $users = $userRepository->findAll();
        return $this->render('user/usersList.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users,
            'firstname' => $firstname
        ]);
    }

    #[Route('/modalStart', name: 'app_user_index')]
    public function modalStart(UserRepository $userRepository): Response
    {
 
        $users = $userRepository->findAll();
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users
        ]);
    }
    // #[Route('/connect/{email}', name: 'app_user_connect')]
    // public function connect(string $email, SessionInterface $session): Response
    // {
    //     $session->set('email', $email);

    #[Route('/new', name: 'app_user_new')]
    public function createNew( Request $request, UserRepository $userRepository, DocumentManager $dm): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $userRepository->save($user, true);
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('user/new.html.twig', [
            'users' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit')]
    public function edit(string $id, Request $request, UserRepository $userRepository, DocumentManager $dm): Response
    {
        
        $user = $userRepository->find($id);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $userRepository->save($user, true);
            return $this->redirectToRoute('app_user_show', ['id'=> $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,

        ]);
    }

    #[Route('/firstTime', name: 'app_user_first_time')]
    public function isFirstTime(): JsonResponse{
        $datas = $this->getUser()->isFirstConnection();

        return new JsonResponse($datas);
    }

    #[Route('/{id}', name: 'app_user_show')]
    public function show(string $id, UserRepository $userRepository): Response
    {
 
        $user = $userRepository->find($id);
        return $this->render('user/show.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/{id}/delete', name: 'app_user_delete')]
    public function delete(string $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $userRepository->remove($user, true);

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }



}


