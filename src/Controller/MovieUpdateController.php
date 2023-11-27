<?php


namespace App\Controller;

use App\Document\Movie;
use App\Repository\UserRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

const REGEX_CATCH_MOVIES_TITLES = '/<a[^>]*?class\s*=\s*["\']meta-title-link["\'][^>]*?href\s*=\s*["\']([^"\']*)["\'][^>]*?>([^<]*)<\/a>/i';
const REGEX_CATCH_MOVIE_YEAR = '/<span itemprop="datePublished" content="([0-9]{4}-[0-9]{2}-[0-9]{2})">/i';
const REGEX_CATCH_CINEMA_NAME = '/<div class="theater-cover-title">([^<]+)<\/div>/i';

#[Route('/movies')]
class MovieUpdateController extends AbstractController
{
    const TMDB_API_KEY = '79471b721cbdf1609bd7fae4469f0560';

    /**
     * Fonction permettant de chercher les titre et date de sortie des films UGC sur allociné
     *
     * @param DocumentManager $dm
     * @return Response
     */
    #[Route('/update', name: 'app_movies_update')]
    public function index(SessionInterface $session, UserRepository $userRepository, DocumentManager $dm): Response
    {

        $this->emptyMovies($dm);
        
        $moviesList = [];

        $route_json = __DIR__ . '/salles-ugc.full.json';
    
        $json = file_get_contents($route_json);

        $codes = json_decode ($json);

        // va chercher sur allocine les films à l'afiche dans les salles ugc
        foreach($codes as $code) {
            $matches = [];
            $html = file_get_contents("https://www.allocine.fr/seance/salle_gen_csalle=$code->code.html");
            $success = preg_match_all(REGEX_CATCH_MOVIES_TITLES, $html, $matches);
            if ($success) {
        
                for ($i=0; $i<count($matches[0]); $i++) {
                    $nom = $matches[2][$i];
                    $url = $matches[1][$i];
                    $nom = html_entity_decode($nom);
                    $nom = htmlspecialchars_decode($nom, ENT_QUOTES);

                    $cinemaNames = [];
                    preg_match_all(REGEX_CATCH_CINEMA_NAME, $html, $cinemaMatches);
                   
                    foreach ($cinemaMatches[1] as $cinemaName) {
                        // décodage de cin'hoche qui était cassé
                        $cinemaName = htmlspecialchars_decode($cinemaName, ENT_QUOTES);
                        $cinemaNames[] = $cinemaName;
                    }

                       // Vérifier si le film existe déjà dans $moviesList
                    if (array_key_exists($nom, $moviesList)) {
                        // Si oui, ajouter les cinémas associés
                        $moviesList[$nom]['cinemas'] = array_merge($moviesList[$nom]['cinemas'], $cinemaNames);
                    } else {
                        // Sinon, créer une nouvelle entrée pour le film
                        $moviesList[$nom] = [
                            'name' => $nom,
                            'url' => $url,
                            'cinemas'=> $cinemaNames,
                        ];
                    }
                }
            }
        }

        // va chercher pour chaque film la date de sortie
        foreach ($moviesList as $movie) {
            $html = file_get_contents('https://www.allocine.fr/' . $movie['url']);
            $success = preg_match_all(REGEX_CATCH_MOVIE_YEAR, $html, $matches);
            
            // APIRATEUR A SITE WEB
            // $file = './files/' . $movie['name'] . '.html';
            // $h = fopen($file, "w");
            // fwrite($h, $html);
            // fclose($h);
            
            $moviesList[$movie['name']]['date'] = $matches[1][0];
            $moviesList[$movie['name']]['date_fr'] = date('d/m/Y', strtotime($matches[1][0]));
        }

        // Persiste les datas en BDD
        $compteur = 0;
        foreach ($moviesList as $datasMovie){
            $mv = new Movie($datasMovie);
            $dm->persist($mv);
            $compteur++;
        }
        $dm->flush();
        dump($moviesList);

        return $this->render('movies_admin/index.html.twig', [
            'movieCount'    => $compteur,
            'controller_name' => 'MovieUpdateController',
            'controller_url'  => 'MovieUpdateController',
        ]);
    }

    /**
     * Fonction de suppression qui appelle des fonction de suppression précise
     *
     * @param DocumentManager $dm
     * @return void
     */
    #[Route('/empty', name: 'app_movies_empty')]
    public function empty(DocumentManager $dm){

        $this->emptyMovies($dm);

        return $this->render('movies_admin/supp_film.html.twig', [
            'movieCount'    => 0,
            'controller_name' => 'MovieUpdateController',
            'controller_url'  => 'MovieUpdateController'
        ]);
    }

    /**
     * Fonction permettant de supprimer les films en base de données
     *
     * @param DocumentManager $dm
     * @return void
     */
    private function emptyMovies(DocumentManager $dm){

        

        $movies = $dm->getRepository(Movie::class)->findAll();
        foreach ($movies as $movie){
            $dm->remove($movie);
        }
        $dm->flush();
    }

    /**
     * Fonction qui récupère l'année de sortie de chaque film et fait une requête à l'api tmdb pour récupérer les informations dont on a besoin
     *
     * @param DocumentManager $dm
     * @return Response
     */
    #[Route('/match_tmdb', name: 'app_movies_match_tmdb')]
    public function matchTMDB(SessionInterface $session, UserRepository $userRepository, DocumentManager $dm): Response {

        $movies = [];

        // récupère les films en BDD mongo
        $moviesMongoDB = $dm->getRepository(Movie::class)->findAll();

        foreach ($moviesMongoDB as $movie) {

            // extraction de l'année
            $year = substr($movie->getDate(), 0, 4);
            
            // création de l'url d'appel à lapi TMDB
            $TMDBApiUrl = 'https://api.themoviedb.org/3/search/movie?api_key=' . self::TMDB_API_KEY . '&query='. urlencode($movie->getName()) .'&language=fr-FR&primary_release_year=' . $year;
            $response = file_get_contents($TMDBApiUrl);
            $data = json_decode($response, true);

            // au cas où aucun résultat, relancer sans filtrer sur l'année
            if (!$data['total_results']) {
                $TMDBApiUrl = 'https://api.themoviedb.org/3/search/movie?api_key=' . self::TMDB_API_KEY . '&query='. urlencode($movie->getName()) .'&language=fr-FR';
                $response = file_get_contents($TMDBApiUrl);
                $data = json_decode($response, true);                
            }

            // candidat vide
            $TMDBCandidate = [
                'id'                => '',
                'title'             => '',
                'original_title'    => '',
                'poster_path'       => '',
                'overview'          => '',
                'vote_average'      => '',
                'release_date'      => ''
            ];

            // récupération du candidat
            $TMDBCandidate = isset($data['results']['0']) ? $data['results']['0'] : $TMDBCandidate;

            // création des données du film à envoyer vers twig
            $movies[] = [
                'id'                    => $movie->getId(),
                'name'                  => $movie->getName(),
                'date_fr'               => $movie->getDateFr(),
                'TMDB_id'               => $TMDBCandidate['id'],
                'TMDB_name'             => $TMDBCandidate['title'],
                'TMDB_original_name'    => $TMDBCandidate['original_title'],
                'TMDB_poster'           => 'https://image.tmdb.org/t/p/w500' . $TMDBCandidate['poster_path'],
                'TMDB_overview'         => $TMDBCandidate['overview'],
                'TMDB_vote_avg'         => $TMDBCandidate['vote_average'],
                'TMDB_release_date'     => $TMDBCandidate['release_date'],
            ];
        }

        // affiche le template twig
        return $this->render('movies_admin/match_tmdb.html.twig', [
            'movies' => $movies,
        ]);
    }    

    /**
     * Traitement du formulaire pour sauvegarder les données des films correspondants
     *
     * @param DocumentManager $dm
     * @return Response
     */
    #[Route('/save_matches', name: 'app_movies_save_matches')]
    public function saveMatches(DocumentManager $dm, Request $request): Response {

        // Récupération du nom du formulaire
        $request->request->get('form-name');

        // Récupération des données du formulaire
        $url = $request->getRequestUri();
        
        // je sépare les combinaisons d'id de tous les films
        $matchesArray = explode('&matches=', $url);

        // array_shift pour supprimer le premier élément car il contient un bout d'url dont on a pas besoin
        array_shift($matchesArray);

        // Boucle sur chaque film
        foreach ($matchesArray as $match){
            $ids = explode('_', $match);
            $id_mongo = $ids[1];
            $id_TMDB = $ids[2];

            // charge le document mongo par id
            $moviesMongoDB = $dm->getRepository(Movie::class)->find($id_mongo);
                
            // lance une nouvelle demande a l'api tmdb, en utilisant l'id_tmdb, pour obenir a nouveau toutes les datas de la fiche tmdb
            $TMDBApiUrl = 'https://api.themoviedb.org/3/movie/' . $id_TMDB . '?api_key=' . self::TMDB_API_KEY . '&language=fr-FR';
            $response = file_get_contents($TMDBApiUrl);
            $data = json_decode($response, true);

            // gestion des données du film TMDB pour récupérer celles dont nous avons besoin
            $TMDBCandidate = [
                'id'             => $data['id'],
                'title'          => $data['title'],
                'original_title' => $data['original_title'],
                'poster_path'    => 'https://image.tmdb.org/t/p/w500' . $data['poster_path'],
                'overview'       => $data['overview'],
                'vote_average'   => $data['vote_average'],
                'release_date'   => $data['release_date'],
                'genre'          => $data['genres'][0]['name'],
            ];

            $castTMDB = 'https://api.themoviedb.org/3/movie/' . $id_TMDB . '/credits?api_key=' . self::TMDB_API_KEY . '&language=fr-FR';
            $response = file_get_contents($castTMDB);
            $data = json_decode($response, true);

            // Foreach qui va chercher le réalisateur de chaque film
            foreach ($data['crew'] as $crewMember) {
                if ($crewMember['job'] == "Director") {
                    $director = $crewMember['name'];
                    break; // Sortir de la boucle dès que le réalisateur est trouvé
                }
            }

            $actors = [];
            // Foreach qui va chercher les 10 plus gros acteurs de chaque film
            $i = 0;
            foreach ($data['cast'] as $castMember) {
                if($i < 10){
                    if ($castMember['known_for_department'] == "Acting") {
                        $actors[] = $castMember['name'];
                        $i++;
                    }
                }
            }

            $TMDBCandidateCast = [
                'director' => $director,
                'actors'   => $actors,
            ];

            // problème sur le vote average qui est parfois à 0 (films français), dans ce cas de figure je leur attribue la valeur 6 en dur, cela est plus visuel
            if ($TMDBCandidate['vote_average'] == 0)
            $TMDBCandidate['vote_average'] = 6;

            // enregistre les données
            $moviesMongoDB->setTmdbOverview($TMDBCandidate['overview']);
            $moviesMongoDB->setTmdbPoster($TMDBCandidate['poster_path']);
            $moviesMongoDB->setTmdbVoteAvg($TMDBCandidate['vote_average']);
            $moviesMongoDB->setTmdbGenre($TMDBCandidate['genre']);
            $moviesMongoDB->setTmdbDirector($TMDBCandidateCast['director']);
            $moviesMongoDB->setTmdbActor($TMDBCandidateCast['actors']);
            $dm->persist($moviesMongoDB);
            $dm->flush();
        }
        // affiche le template twig
        return $this->render('movies_admin/save_matches.html.twig', [
        ]); 
    }              
}