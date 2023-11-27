<?php

namespace App\Controller;

use App\Document\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/mon-compte')]
class AccountController extends AbstractController
{
    #[Route('/', name: 'app_my_account_show')]
    public function index(SessionInterface $session, UserRepository $userRepository): Response
    {
     
        // mémorise l'id du user en session
        $session->set('id', $this->getUser()->getId());
        $userId = $session->get('id');
        $user = $userRepository->findUserById($userId);
        
        $firstname = null;
        $firstname = $user->getFirstName();

        return $this->render('account/index.html.twig', [
            'controller_name' => 'ProfilController',
            
            'firsttime' => $this->getUser()->isFirstConnection(),
            'firstname' => $firstname,
        ]);
    }


    #[Route('/edit', name: 'app_my_account_edit')]
    public function edit( SessionInterface $session, Request $request, UserRepository $userRepository, DocumentManager $dm): Response
    {
      
        $user = $this->getUser();
        $userId = $session->get('id');
        $user = $userRepository->findUserById($userId);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $userRepository->save($user, true);
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('account/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/admin', name: 'app_my_account_admin')]
    public function admin(SessionInterface $session, UserRepository $userRepository): Response
    {
     
        // mémorise l'id du user en session
        $session->set('id', $this->getUser()->getId());
        $userId = $session->get('id');
        $user = $userRepository->findUserById($userId);
        
        return $this->render('account/admin.html.twig', [
            'controller_name' => 'ProfilController'
        ]);
    }


    #[Route('/edit_category', name: 'app_my_account_edit_category')]
    public function editCategory(Request $request,SessionInterface $session, UserRepository $userRepository): Response
    {

         // recuperer l'id en session
         $userId = $session->get('id');

         // faire un find pour retrouver le user
         $user = $userRepository->findUserById($userId);
        
        $genres = $user->setGenres([]);
        // traitement du formulaire
        $forname = $request->get('form-name');
        if ($forname == 'form_choose_categories') {
            $success = true;
            
            $genres = $request->get('genres');
            
            // if permettant de ne pas faire planter le programme si l'utilisateur ne sélectionne aucun genre
            if (is_array($genres)) {
                foreach( $genres as $genre){
                    $user->addGenre($genre);
                }
            }
            $userRepository->save($user, true);


            // si tout va bien passer à l'étape suivante
            return $this->redirectToRoute('app_my_account_edit_actor', [], Response::HTTP_SEE_OTHER);
            }
            return $this->render('account/edit_category.html.twig', [
                'formName' => 'form_choose_categories',
            ]);
    }


    #[Route('/edit_actor', name: 'app_my_account_edit_actor')]
    public function editActor(Request $request,SessionInterface $session, UserRepository $userRepository): Response
    {
         // recuperer l'id en session
         $userId = $session->get('id'); 

         // faire un find pour retrouver le user
         $user = $userRepository->findUserById($userId);
        
        $actors = $user->setActors([]);

        $directors = $user->setDirectors([]);


         // traitement du formulaire
         $forname = $request->get('form-name');
         if ($forname == 'form_choose_actors') {
         
         // set que si ce n'est pas vide
         $actors = $request->get('actors');
         $actors = explode('||', $actors);
         // if (!empty($actor)) {
         //     $user->setActor($actor);
         // } 
         if (is_array($actors)) {
             foreach( $actors as $actor){
                 $user->addActor($actor);
             }}
         

         // set que si ce n'est pas vide
         $directors = $request->get('directors');
         $directors = explode('||', $directors);
         if (is_array($directors)) {
             foreach( $directors as $director){
                 $user->addDirector($director);
             }}
     
         // set que si ce n'est pas vide
         $language = $request->get('language');
         if (!empty($language)) {
             $user->setLanguage($language);
         }

     $userRepository->save($user, true);

     // si tout va bien passer à l'étape suivante
     return $this->redirectToRoute('app_my_account_show', [], Response::HTTP_SEE_OTHER);
    }
    return $this->render('account/edit_actor.html.twig', [
        'formName' => 'form_choose_actors',
    ]);
    }
}
