<?php

namespace App\Repository;

use App\Entity\Branch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Branch|null find($id, $lockMode = null, $lockVersion = null)
 * @method Branch|null findOneBy(array $criteria, array $orderBy = null)
 * @method Branch[]    findAll()
 * @method Branch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BranchRepository extends ServiceEntityRepository
{
    private Connection $connection;

    public function __construct(ManagerRegistry $registry, Connection $connection)
    {
        $this->connection = $connection;
        parent::__construct($registry, Branch::class);
    }

    public function save(Branch $branch):Branch
    {
        $name = $branch->getName();
        $location = $branch->getLocation();
        $location_id = $location->getId();
        $created_at = $branch->getCreatedAt();
        $sql = 'INSERT INTO branch (name, created_at, location_id) VALUES ("'. $name .'", "'. $created_at .'", '. $location_id .');';
        $statement = $this->connection->prepare($sql);
        $statement->executeQuery();
        $branch->setId($this->connection->lastInsertId());
        return $branch;
    }
}
