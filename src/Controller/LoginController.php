<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_user_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils,SessionInterface $session): Response
    {
        // verifie si l'utilsateur est déjà connecté et si oui il est redirigé vers la page d'index du compte
        if ($this->getUser()) {
            return $this->redirectToRoute('app_my_account_index');
        }
        // récupère une éventuelle erreur de connexion stockée dans la session lors d'une tentative de connexion précédent
        $error = $authenticationUtils->getLastAuthenticationError();
        // récupère le dernier nom d'utilisateur  saisi par l'utilisateur lors d'une connexion précédente. 
        // Cela permet de pré-remplir le champ du nom d'utilisateur pour une nouvelle tentative de connexion.
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('login/success.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'app_user_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
