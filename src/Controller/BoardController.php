<?php

namespace App\Controller;

use App\Document\Movie;
use App\Document\User;
use App\Repository\CinemasRepository;
use App\Repository\MovieRepository;
use App\Repository\UserRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/board')]
class BoardController extends AbstractController
{
    #[Route('/', name: 'app_board')]
    public function index(SessionInterface $session,
                         UserRepository $userRepository,
                         CinemasRepository $cinemasRepository): Response
    {


        return $this->render('board/index.html.twig', [
            'controller_name' => 'BoardController',

            
        ]);
    }

    #[Route('/home', name: 'app_board_home')]
    public function home(SessionInterface $session, 
                        UserRepository $userRepository,
                        CinemasRepository $cinemasRepository,
                        MovieRepository $movieRepository,): Response
    {
        // Récupérer l'utilisateur actuellement authentifié
        $user = $this->getUser();

        $preferredGenres = $user->getGenres();
        
        $preferredActors = $user->getActors();

        $preferredDirectors = $user->getDirectors();

        $preferredLocation = $user->getLocation();

        // Rechercher les films correspondant aux genres préférés de l'utilisateur
        $recommendedMoviesByGenres = $movieRepository->findByGenres($preferredGenres);

        // Rechercher les films correspondant aux acteurs préférés de l'utilisateur
        $recommendedMoviesByActors = $movieRepository->findByActors($preferredActors);

        // Rechercher les films correspondant aux acteurs préférés de l'utilisateur
        $recommendedMoviesByDirectors = $movieRepository->findByDirectors($preferredDirectors);

        // Rechercher le cinéma correspondant aux cinémas préférés de l'utilisateur
        $selectedLocation = $cinemasRepository->findByLocations($preferredLocation);

        // Rechercher les films correspondants au cinéma préférés de l'utilisateur
        $recommendedMoviesByCinema = $movieRepository->findByCinema($preferredLocation);

        // Initialiser $imgStar en tant que tableau vide
        $imgStar = [];

        // Accéder à la note de chaque film dans le tableau $recommendedMovies
        foreach ($recommendedMoviesByGenres as $movie) {
            // On va chercher la note du film dans la base de
            $stars = $movie->getTmdbVoteAvg();
            // Utiliser la fonction calculateStars pour obtenir le tableau d'images d'étoiles
            $starsImages  = $movieRepository->calculateStars($stars);
            // assigner $imgStar à chaque movie en fonction de son id
            $imgStar[$movie->getId()] = $starsImages;
        }

        // Accéder à la note de chaque film dans le tableau $recommendedMovies
        foreach ($recommendedMoviesByActors as $movie) {
            // On va chercher la note du film dans la base de
            $stars = $movie->getTmdbVoteAvg();
            // Utiliser la fonction calculateStars pour obtenir le tableau d'images d'étoiles
            $starsImages  = $movieRepository->calculateStars($stars);
            // assigner $imgStar à chaque movie en fonction de son id
            $imgStar[$movie->getId()] = $starsImages;
        }
        
        // Accéder à la note de chaque film dans le tableau $recommendedMovies
        foreach ($recommendedMoviesByDirectors as $movie) {
            // On va chercher la note du film dans la base de
            $stars = $movie->getTmdbVoteAvg();
            // Utiliser la fonction calculateStars pour obtenir le tableau d'images d'étoiles
            $starsImages  = $movieRepository->calculateStars($stars);
            // assigner $imgStar à chaque movie en fonction de son id
            $imgStar[$movie->getId()] = $starsImages;
        }

        // Accéder à la note de chaque film dans le tableau $recommendedMovies
        foreach ($recommendedMoviesByCinema as $movie) {
            // On va chercher la note du film dans la base de
            $stars = $movie->getTmdbVoteAvg();
            // Utiliser la fonction calculateStars pour obtenir le tableau d'images d'étoiles
            $starsImages  = $movieRepository->calculateStars($stars);
            // assigner $imgStar à chaque movie en fonction de son id
            $imgStar[$movie->getId()] = $starsImages;
        }

        return $this->render('board/home.html.twig', [
            'recommendedMoviesByGenres' => $recommendedMoviesByGenres,
            'recommendedMoviesByActors' => $recommendedMoviesByActors,
            'recommendedMoviesByDirectors' => $recommendedMoviesByDirectors,
            'recommendedMoviesByCinema' => $recommendedMoviesByCinema,
            'selectedLocation' => $selectedLocation,
            'imgStar' => $imgStar
        ]);
    }

    #[Route('/cinema', name: 'app_board_cinema')]
    public function cinema(SessionInterface $session, UserRepository $userRepository, CinemasRepository $cinemasRepository, MovieRepository $movieRepository): Response
    {
        // Récupérer l'utilisateur actuellement authentifié
        $user = $this->getUser();

        // Récupérer le cinéma préféré de l'utilisateur
        $preferredLocation = $user->getLocation();

        // Rechercher le cinéma correspondant aux cinémas préférés de l'utilisateur
        $selectedLocation = $cinemasRepository->findByLocations($preferredLocation);

        // Rechercher les films correspondants au cinéma préférés de l'utilisateur
        $recommendedMoviesByCinema = $movieRepository->findByCinema($preferredLocation);
        
        // dump($recommendedMoviesByCinema);

        // initialisation d'img star pour ne pas avoir d'erreur si un utilisateur n'a pas renseigné de cinéma préféré
        $imgStar = [];
        // Accéder à la note de chaque film dans le tableau $recommendedMovies
        foreach ($recommendedMoviesByCinema as $movie) {
            // On va chercher la note du film dans la base de
            $stars = $movie->getTmdbVoteAvg();
            // Utiliser la fonction calculateStars pour obtenir le tableau d'images d'étoiles
            $starsImages  = $movieRepository->calculateStars($stars);
            // assigner $imgStar à chaque movie en fonction de son id
            $imgStar[$movie->getId()] = $starsImages;
        }

        return $this->render('board/cinema.html.twig', [
            'selectedLocation' => $selectedLocation,
            'recommendedMoviesByCinema' => $recommendedMoviesByCinema,
            'imgStar' => $imgStar
        ]);
    }

    #[Route('/reservation', name: 'app_board_reservation')]
    public function reservation(SessionInterface $session, UserRepository $userRepository): Response
    {


        return $this->render('board/reservation.html.twig', [

        ]);
    }

    #[Route('/fidelite', name: 'app_board_fidelite')]
    public function fidelite(SessionInterface $session, UserRepository $userRepository): Response
    {


        return $this->render('board/fidelite.html.twig', [

        ]);
    }

    #[Route('/film', name: 'app_board_film')]
    public function filmsByPreferredGenres( SessionInterface $session, UserRepository $userRepository, MovieRepository $movieRepository): Response
    {
        // Récupérer l'utilisateur actuellement authentifié
        $user = $this->getUser();

        // Vérifier que l'utilisateur a des préférences
        // if ($user && !empty($user->getGenres())) {
        $preferredGenres = $user->getGenres();
        
        $preferredActors = $user->getActors();

        $preferredDirectors = $user->getDirectors();

        // Rechercher les films correspondant aux genres préférés de l'utilisateur
        $recommendedMoviesByGenres = $movieRepository->findByGenres($preferredGenres);

        // Rechercher les films correspondant aux acteurs préférés de l'utilisateur
        $recommendedMoviesByActors = $movieRepository->findByActors($preferredActors);

        // Rechercher les films correspondant aux acteurs préférés de l'utilisateur
        $recommendedMoviesByDirectors = $movieRepository->findByDirectors($preferredDirectors);

        // Initialiser $imgStar en tant que tableau vide
        $imgStar = [];

        // Accéder à la note de chaque film dans le tableau $recommendedMovies
        foreach ($recommendedMoviesByGenres as $movie) {
            // On va chercher la note du film dans la base de
            $stars = $movie->getTmdbVoteAvg();
            // Utiliser la fonction calculateStars pour obtenir le tableau d'images d'étoiles
            $starsImages  = $movieRepository->calculateStars($stars);
            // assigner $imgStar à chaque movie en fonction de son id
            $imgStar[$movie->getId()] = $starsImages;
        }

        // Accéder à la note de chaque film dans le tableau $recommendedMovies
        foreach ($recommendedMoviesByActors as $movie) {
            // On va chercher la note du film dans la base de
            $stars = $movie->getTmdbVoteAvg();
            // Utiliser la fonction calculateStars pour obtenir le tableau d'images d'étoiles
            $starsImages  = $movieRepository->calculateStars($stars);
            // assigner $imgStar à chaque movie en fonction de son id
            $imgStar[$movie->getId()] = $starsImages;
        }
        
        // Accéder à la note de chaque film dans le tableau $recommendedMovies
        foreach ($recommendedMoviesByDirectors as $movie) {
            // On va chercher la note du film dans la base de
            $stars = $movie->getTmdbVoteAvg();
            // Utiliser la fonction calculateStars pour obtenir le tableau d'images d'étoiles
            $starsImages  = $movieRepository->calculateStars($stars);
            // assigner $imgStar à chaque movie en fonction de son id
            $imgStar[$movie->getId()] = $starsImages;
        }

        return $this->render('board/film.html.twig', [
            'recommendedMoviesByGenres' => $recommendedMoviesByGenres,
            'recommendedMoviesByActors' => $recommendedMoviesByActors,
            'recommendedMoviesByDirectors' => $recommendedMoviesByDirectors,
            'imgStar' => $imgStar
        ]);
        //} else {
            // Si l'utilisateur n'a pas de préférences de genre,lui proposer des films populaires.
            // return $this->render('film/no_preferences.html.twig');
        }
    }
 //}
