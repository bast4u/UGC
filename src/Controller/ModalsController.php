<?php

namespace App\Controller;

use App\Document\User;
use App\Form\UserType;
use App\Repository\CinemasRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;
use PhpParser\Node\Expr\Cast\String_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/modals')]
class ModalsController extends AbstractController
{
    /**
     * Formulaire d'inscription pour un nouvel utilisateur
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @param SessionInterface $session
     * @return Response
     */
    #[Route('/new_connection', name: 'app_modals_new_connection')]
    public function new_connection( Request $request, 
                                    UserRepository $userRepository,
                                    UserPasswordHasherInterface $userPasswordHasher,
                                    SessionInterface $session): Response
    {
        $user = new User();
        
        $message = '';
        $lastname = 'rex';
        $firstname = 'arty';
        $dateOfBirth = '12/07/1985';
        $email = '';
        $phone = '';
        $address = '';
        $postalCode = '';
        $city = '';
        $country ='';

        // traitement du formulaire
        $forname = $request->get('form-name');
        if ($forname == 'form_new_connection') {
            $success = true;
            
            // faire ici tous les tests et vérifications
            $user->setGender($request->get('gender'));
            
            $lastname = $request->get('lastname');
            
                if(empty($lastname) || !is_string($lastname)) {
                $success = false;
                $message .= 'Veuillez entrer votre nom.<br>';
                }
            $user->setLastName($request->get('lastname'));    

            $firstname = $request->get('firstname');
                if (empty($firstname) || !is_string($firstname)) {
                    $success = false;
                    $message .= 'Veuillez entrer votre prénom.<br>';
                }  
            $user->setFirstName($request->get('firstname'));

            $dateOfBirth = $request->get('dateOfBirth');            
            $user->setDateOfBirth(new DateTime($request->get('dateOfBirth')));
 
            // vérifier que l'email est valide
            $email = $request->request->get('email');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $success = false;
                $message .= 'Adresse email invalide.<br>';
            } else {
                // vérifier si l'email est déjà utilisé par un autre utilisateur
                $existingUser = $userRepository->findOneBy(['email' => $email]);
                if ($existingUser) {
                    $success = false;
                    $message .= 'Cet email est déjà utilisé.<br>';
                }
            }

            $password = $request->get('password');

            $checkPassword = $request->get('checkPassword');

            // vérifie que le pawword ne soit pas null et fasse au minimum 6 caractères
            if (empty($password) || strlen($password) < 6) {
                $success = false;
                $message .= 'Le mot de passe doit comporter au moins 6 caractères.<br>';
            }
        
            // vérifie si le password et le checkpassword sont identiques
            if ($password !== $checkPassword) {
                    $success = false;
                    $message .= 'La vérification du mot de passe est incorrecte.<br>';
                }

            // hashage du password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $password
                )
                );
        // $user->setRoles(['ROLE_ADMIN']);
            $user->setEmail($request->get('email'));

            $phone  = $request->get('phone');
            $user->setPhone($request->get('phone'));

            $address =$request->get('address');
            $user->setAddress($request->get('address'));

            $postalCode = $request->get('postalCode');
            // Protection contre un code postal vide (pour le moment, aux vues des besoin de notre site, nous n'avons pas besoin de le rendre obligatoire)
            if (!empty($postalCode)) {
                // Si le champ postalCode n'est pas vide, l'affecter directement à la propriété de l'utilisateur
                $user->setPostalCode($postalCode);
            } else {
                // Si le champ postalCode est vide, l'affecter à null sinon ça plante
                $user->setPostalCode(null);
            }

            $city = $request->get('city');
            $user->setCity($request->get('city'));

            $country = $request->get('country');
            $user->setCountry($request->get('country'));
              
            // si succès, enregistrement en bdd
             if ($success) {              
                    $userRepository->save($user, true);
                    // trouver l'id du user
                    $userId = $user->getId();
                    // memorise l'id en session
                    $session->set('id', $userId);
                }
      
            // si tout va bien passer à l'étape suivante
            if ($success) {
                return $this->accueil($request, 
                                      $userRepository, 
                                      $session);
            } 
        }
    
        // affichage du formulaire
        return $this->render('modals/modal_new_connection.html.twig', [
            'message' => $message,
            'formName' => 'form_new_connection',
            'step' => '/modals/new_connection',
            'previousStep' => '',
            'lastname' => $lastname,
            'firstname' => $firstname,
            'dateOfBirth' => $dateOfBirth,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'postalCode' => $postalCode,
            'city' => $city,
            'country' => $country

        ]);
    }

    /**
     * Undocumented function
     *
     * @param SessionInterface $session
     * @return Response
     */
    #[Route('/show_errors', name: 'app_show_errors')]
    public function show_errors(SessionInterface $session): Response
    {
        // Récupérer les erreurs depuis la variable de session
        $message = $session->get('message', '');

        // Supprimer les erreurs de la variable de session pour qu'elles n'apparaissent pas lors du prochain affichage
        $session->remove('message');

        // Afficher la vue Twig avec les erreurs
        return $this->render('modals/modal_errors.html.twig', [
            'message' => $message,
            'formName' => 'form_new_connection',
            'step' => '/modals/new_connection',
            'previousStep' => '',
        ]);
    }

    #[Route('/previous_step', name: 'app_modals_previous_step')]
    public function previous_step(): Response
    {
        return $this->redirectToRoute('app_modals_new_connection');
    }

    /**
     * Affichage de la page expliquant la fonctionnalité
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     */
    #[Route('/accueil', name: 'app_modals_accueil')]
    public function accueil(Request $request, 
                            UserRepository $userRepository,
                            SessionInterface $session): Response
    {

        // recuperer l'id en session
        $userId = $session->get('id') ? $session->get('id') : $this->getUser()->getId(); 

        // faire un find pour retrouver le user
        $user = $userRepository->findUserById($userId);

        $firstname = null;
        $message = null;
        if (!$user) {
           $message = 'Vous n\'êtes pas connecté(e).<br>';
        } else {
            $firstname = $user->getFirstName();
        }

        // traitement du formulaire
        $forname = $request->get('form-name');
        if ($forname == 'form_accueil') {

            // si tout va bien passer à l'étape suivante
            return $this->choose_cinema($request, 
                                        $userRepository,
                                        $session);
        }

        // affichage du formulaire        
        return $this->render('modals/modal_accueil.html.twig', [
            'message' => $message,
            'firstname' => $firstname,
            'formName' => 'form_accueil',
            'step' => '/modals/accueil',
            'previousStep' => '/modals/new_connection',
        ]);
    }

    /**
     * Formulaire de choix du cinema
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @param SessionInterface $session
     * @return Response
     */
    #[Route('/choose_cinema', name: 'app_modals_choose_cinema')]
    public function choose_cinema(Request $request,
                                UserRepository $userRepository,
                                SessionInterface $session): Response
    {
        $message = '';

         // recuperer l'id en session
         $userId = $session->get('id'); 

         // faire un find pour retrouver le user
        $user = $userRepository->findUserById($userId);

        // traitement du formulaire
        $forname = $request->get('form-name');
        if ($forname == 'form_choose_cinema') {
            
            // faire également les enregistrement en bdd
            $locations = $request->get('locations');

            if (is_array($locations)) {
            foreach( $locations as $location){
                $user->addLocation($location);
            }}
            $userRepository->save($user, true);
            // $user->setLocation($request->get('location'));

            // si tout va bien passer à l'étape suivante
            return $this->choose_seats($request, $userRepository, $session);
        }

        return $this->render('modals/modal_choose_cinema.html.twig', [
            'message' => $message,
            'formName' => 'form_choose_cinema',
            'step' => '/modals/choose_cinema',
            'previousStep' => '/modals/accueil',        
        ]);
    }

    /**
     * Formulaire de choix d'emplacement dans le cinema
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @param SessionInterface $session
     * @return Response
     */
    #[Route('/choose_seats', name: 'app_modals_choose_seats')]
    public function choose_seats(Request $request, 
                                UserRepository $userRepository, 
                                SessionInterface $session): Response
    {
        $message = '';
        // recuperer l'id en session
        $userId = $session->get('id'); 
        // faire un find pour retrouver le user
        $user = $userRepository->findUserById($userId);
        
        // traitement du formulaire
        $formName = $request->get('form-name');
        if ($formName === 'form_choose_seats') {
            
            // faire également les enregistrement en bdd
            $seats = $request->get('seats');
           
            if (is_array($seats)) {
                foreach ($seats as $seat) {
                    $user->addSeat($seat);
                }
            }

            $userRepository->save($user, true);

            // Si tout va bien, passer à l'étape suivante
            return $this->choose_categories($request, $userRepository, $session);
        }

        return $this->render('modals/modal_choose_seats.html.twig', [
            'message' => $message,
            'formName' => 'form_choose_seats',
            'step' => '/modals/choose_seats',
            'previousStep' => '/modals/choose_cinema',
        ]);
    }  
         
    /**
     * Formulaire de choix des catégories de films préférées
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     */
    #[Route('/choose_categories', name: 'app_modals_choose_categories')]
    public function choose_categories(Request $request,
                                      UserRepository $userRepository,
                                      SessionInterface $session
                                      ): Response
    {
        $message = '';

        // recuperer l'id en session
        $userId = $session->get('id'); 

        // faire un find pour retrouver le user
        $user = $userRepository->findUserById($userId);

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
            // faire ici tous les test et vérifications

            // faire également les enregistrement en bdd
            
            // si tout va bien passer à l'étape suivante
            return $this->choose_actors($request, $userRepository, $session);
        }

        return $this->render('modals/modal_choose_categories.html.twig', [
            'message' => $message,
            'formName' => 'form_choose_categories',
            'step' => '/modals/choose_categories',
            'previousStep' => '/modals/choose_seats',
            
        ]);
    }

    #[Route('/choose_actors', name: 'app_modals_choose_actors')]
    public function choose_actors(Request $request,
                                UserRepository $userRepository,
                                SessionInterface $session): Response
    {
            $message = '';

            // recuperer l'id en session
            $userId = $session->get('id'); 

            // faire un find pour retrouver le user
            $user = $userRepository->findUserById($userId);

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
             
            // enregistrement en bdd
            $userRepository->save($user, true);

            // si tout va bien passer à l'étape suivante
            return $this->fidelity($request, $userRepository, $session);
            }
            return $this->render('modals/modal_choose_actors.html.twig', [
                'message' => $message,
                'formName' => 'form_choose_actors',
                'step' => '/modals/choose_actors',
                'previousStep' => '/modals/choose_categories',
            ]);
    }

    #[Route('/fidelity', name: 'app_modals_fidelity')]
    public function fidelity(Request $request,
                            UserRepository $userRepository,
                            SessionInterface $session
                            ): Response
    {
        $message = '';

        // recuperer l'id en session
        $userId = $session->get('id'); 

        // faire un find pour retrouver le user
        $user = $userRepository->findUserById($userId);

        // traitement du formulaire
        $forname = $request->get('form-name');
        if ($forname == 'form_fidelity') {
           
            $gifts = $request->get('gifts');
            if (is_array($gifts)) {
            foreach($gifts as $gift){
                $user->addGift($gift);
                }
            }
            $user->setFirstConnection(false);
            $userRepository->save($user, true);
            // faire ici tous les test et vérifications

            // faire également les enregistrement en bdd


            // si tout va bien passer à l'étape suivante

            // si tout va bien passer à l'étape suivante
            return $this->final($request, $userRepository, $session);
        }

        return $this->render('modals/modal_fidelity.html.twig', [
            'message' => $message,
            'formName' => 'form_fidelity',
            'step' => '/modals/fidelity',
            'previousStep' => '/modals/choose_categories',
        ]);
    }


    #[Route('/final', name: 'app_modals_finale')]
    public function final(Request $request,
                            UserRepository $userRepository,
                            SessionInterface $session
                            ): Response
    {

        return $this->render('modals/final.html.twig', [
        ]);
    }
}
