<?php

namespace App\Repository;

use App\Document\User;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class UserRepository extends ServiceDocumentRepository  
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, User::class);
    }

    public function save(User $user, bool $flush = false): void
    {
        $this->getDocumentManager()->persist($user);

        if ($flush) {
            $this->getDocumentManager()->flush();
        }
    }

    /////test remove
    public function remove(User $user, bool $flush = false): void
    {
        $this->getDocumentManager()->remove($user);

        if ($flush) {
            $this->getDocumentManager()->flush();
        }
    }
    // Retrieve one document by id from the collection
    public function findUserById(string $id): ?User
    {
        return $this->findOneBy(['id' => $id]);
    }

}

