<?php

namespace App\Repository;

use App\Entity\Balance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Balance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Balance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Balance[]    findAll()
 * @method Balance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BalanceRepository extends ServiceEntityRepository
{
    private Connection $connection;

    public function __construct(ManagerRegistry $registry, Connection $connection)
    {
        $this->connection = $connection;
        parent::__construct($registry, Balance::class);
    }

    public function save(Balance $balance):Balance
    {
        $customer_id = $balance->getCustomer()->getId();
        $move = $balance->getMove();
        $coin = $balance->getCoin();
        $created_at = $balance->getCreatedAt();
        $sql = 'INSERT INTO balance (customer_id, move, coin, created_at) VALUES ('. $customer_id .',' . $move . ',"' . $coin . '","' . $created_at . '");';
        $statement = $this->connection->prepare($sql);
        $statement->executeQuery();
        $balance->setId($this->connection->lastInsertId());
        return $balance;
    }

    // /**
    //  * @return Balance[] Returns an array of Balance objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Balance
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
