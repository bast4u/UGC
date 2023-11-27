<?php

namespace App\Repository;

use App\Document\Cinemas;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Symfony\Component\HttpFoundation\Response;

class CinemasRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Cinemas::class);
    }

    public function save(Cinemas $users, bool $flush = false): void
    {
        $this->getDocumentManager()->persist($users);

        if ($flush) {
            $this->getDocumentManager()->flush();
        }
    }

    public function findByCriterias(string $searchData, int $limit = 0): array
    {

        $qb = $this->createQueryBuilder(Cinemas::class)
            ->field('$or')
            ->equals([
                ['name' => new \MongoDB\BSON\Regex($searchData, 'i')],
                ['zipcode' => new \MongoDB\BSON\Regex($searchData, 'i')],
                ['city' => new \MongoDB\BSON\Regex($searchData, 'i')],
            ]);
    
        if ($limit) {
            $qb->limit($limit);
        }
    
        return $qb->getQuery()->execute()->toArray();
    }

    // recherche un cinema par son nom
    // public function findByName(string $name): ?Cinemas
    // {
    //     return $this->findOneBy(['name' => $name]);
    // }
    


    public function findByLocations(array $locations): array
    {
        return $this->createQueryBuilder()
        ->field('name')->in($locations)
        ->getQuery()
        ->execute()->toArray();
    }
}
