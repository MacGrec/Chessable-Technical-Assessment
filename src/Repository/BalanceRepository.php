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

    public function save(Balance $balance): Balance
    {
        $customer_id = $balance->getCustomer()->getId();
        $move = $balance->getMove();
        $coin = $balance->getCoin();
        $secondary_customer = $balance->getSecondaryCustomer();
        $created_at = $balance->getCreatedAt();
        if(isset($secondary_customer)){
            $sql = 'INSERT INTO balance (customer_id, secondary_customer_id, move, coin, created_at) VALUES ('. $customer_id .','. $secondary_customer .',' . $move . ',"' . $coin . '","' . $created_at . '");';
        }
        else {
            $sql = 'INSERT INTO balance (customer_id, move, coin, created_at) VALUES ('. $customer_id .',' . $move . ',"' . $coin . '","' . $created_at . '");';
        }
        $statement = $this->connection->prepare($sql);
        $statement->executeQuery();
        $balance->setId($this->connection->lastInsertId());
        return $balance;
    }
}
