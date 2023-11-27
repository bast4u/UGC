<?php


namespace App\Controller;

use App\Document\Cinemas;
use App\Repository\CinemasRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;


class CinemasController extends AbstractController
{
    protected $cinemasRepository;

    public function __construct(CinemasRepository $cinemasRepository)
    {
        $this->cinemasRepository = $cinemasRepository;
    }

    #[Route('/cinemas/{search}', name: 'app_cinemas')]
    public function index(Request $request, string $search): Response
    {
        $search = trim(strtolower($request->get('search')));

        // traiter les données pour les préparer pour la réponse JSON
        $response = [];

        // Récupérer les cinémas à partir du repository
        // $cinemas = $this->cinemasRepository->findAll();
        $cinemas = $this->cinemasRepository->findByCriterias($search, 10);

        foreach ($cinemas as $cinema) {
            $response[] = [
                'id'        => $cinema->getId(),
                'name'      => $cinema->getName(),
                'address'   => $cinema->getAddress(),
                'zipcode'   => $cinema->getZipcode(),
                'city'      => $cinema->getCity(),
                'image'     => $cinema->getImage(),
            ];
        }

        return new JsonResponse($response);
    }
  
}
