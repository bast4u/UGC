<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    /**
     * Route renvoyant le JSON d'un recherche d'acteur
     */
    #[Route('/actors/{name}/{limit}', name: 'app_api')]
    public function index(string $name, int $limit = 0): JsonResponse
    {
        // interroge l'api tmdb
        $srcDatas = json_decode(file_get_contents("https://api.themoviedb.org/3/search/person?query=$name&api_key=79471b721cbdf1609bd7fae4469f0560&language=fr-FR"));

        // parcours tous les résultats de l'api pour créer notre propre jeu de données
        $datas = [];
        foreach ($srcDatas->results as $actor) {

            // pour le moment, on ne récupère que le nom de l'acteur
            if (in_array($actor->name, $datas) || 
                in_array(strtolower($actor->name), $datas) || 
                in_array(strtoupper($actor->name), $datas)) 
                continue;

            // ajoute l'acteur actuel a la liste
            $datas[] = $actor->name;

            // on arrete si on atteind la limite max
            if ($limit && count($datas) == $limit) {
                break;
            }
        }

        // on renvoie nos datas en json
        return new JsonResponse($datas);
    }
}
