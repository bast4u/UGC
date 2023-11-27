<?php

namespace App\Repository;

use App\Document\Movie;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;


class MovieRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Movie::class);
    }

    public function findByGenres(array $genres): array
    {
        return $this->createQueryBuilder()
            ->field('tmdbGenre')->in($genres)
            ->getQuery()
            ->execute()->toArray();
    }

    public function findByActors(array $actors): array
    {
        return $this->createQueryBuilder()
            ->field('tmdbActor')->in($actors)
            ->getQuery()
            ->execute()->toArray();
    }

    public function findByDirectors(array $directors): array
    {
        return $this->createQueryBuilder()
            ->field('tmdbDirector')->in($directors)
            ->getQuery()
            ->execute()->toArray();
    }

    // public function findByCinema(array $cinemas):array
    // {
    //     return $this->createQueryBuilder()
    //         ->field('cinemas')->in($cinemas)
    //         ->getQuery()
    //         ->execute()->toArray();
    // }
 
    public function findByCinema(array $cinemas): array
    {
        $normalizedCinemas = array_map(function ($cinema) {
            // Convertir en minuscules et supprimer les espaces en début et en fin
            $normalized = trim(strtolower($cinema));
    
            // Supprimer les articles "le", "la", "les", "l'" au début du nom
            $normalized = preg_replace('/^(le|la|les|l\')\s+/i', '', $normalized);
    
            return $normalized;
        }, $cinemas);
    
        $query = $this->createQueryBuilder();
        $regexCinemas = array_map(fn ($cinema) => new \MongoDB\BSON\Regex('^' . preg_quote($cinema, '/') . '$', 'i'), $normalizedCinemas);
        $query->field('cinemas')->in($regexCinemas);
    
        return $query->getQuery()->execute()->toArray();
    }

    public function calculateStars($stars)
    {
        // initialiser un tableau vide
        $imgStar = [];

        // récupérer les bonnes images
        $imgStarEmpty = '/assets/icones/etoile-vide.svg';
        $imgStarHalf = '/assets/icones/etoile-demi.svg';
        $imgStarFull = '/assets/icones/etoile-pleine.svg';

        switch (round($stars)) {
            case 0:
                // '0 stars'
                $imgStar = [$imgStarEmpty, $imgStarEmpty, $imgStarEmpty, $imgStarEmpty, $imgStarEmpty];
                break;
            case 1:
                // '0.5 stars'
                $imgStar = [$imgStarHalf, $imgStarEmpty, $imgStarEmpty, $imgStarEmpty, $imgStarEmpty];
                break;
            case 2:
                // '1 stars'
                $imgStar = [$imgStarFull, $imgStarEmpty, $imgStarEmpty, $imgStarEmpty, $imgStarEmpty];
                break;
            case 3:
                // '1.5 stars'
                $imgStar = [$imgStarFull, $imgStarHalf, $imgStarEmpty, $imgStarEmpty, $imgStarEmpty];
                break;
            case 4:
                // '2 stars'
                $imgStar = [$imgStarFull, $imgStarFull, $imgStarEmpty, $imgStarEmpty, $imgStarEmpty];
                break;
            case 5:
                // '2.5 stars'
                $imgStar = [$imgStarFull, $imgStarFull, $imgStarHalf, $imgStarEmpty, $imgStarEmpty];
                break;
            case 6:
                // '3 stars'
                $imgStar = [$imgStarFull, $imgStarFull, $imgStarFull, $imgStarEmpty, $imgStarEmpty];
                break;
            case 7:
                // '3.5 stars'
                $imgStar = [$imgStarFull, $imgStarFull, $imgStarFull, $imgStarHalf, $imgStarEmpty];
                break;
            case 8:
                // '4 stars'
                $imgStar = [$imgStarFull, $imgStarFull, $imgStarFull, $imgStarFull, $imgStarEmpty];
                break;
            case 9:
                // '4.5 stars'
                $imgStar = [$imgStarFull, $imgStarFull, $imgStarFull, $imgStarFull, $imgStarHalf];
                break;
            case 10:
                // '5 stars'
                $imgStar = [$imgStarFull, $imgStarFull, $imgStarFull, $imgStarFull, $imgStarFull];
                break;
            default:
                // '0 stars'
                $imgStar = [$imgStarEmpty, $imgStarEmpty, $imgStarEmpty, $imgStarEmpty, $imgStarEmpty];
                break;
        }
        return $imgStar;
    }
}
