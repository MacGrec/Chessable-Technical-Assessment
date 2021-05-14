<?php

namespace App\Repository;

use App\Entity\Branch;
use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    private Connection $connection;

    public function __construct(ManagerRegistry $registry, Connection $connection)
    {
        $this->connection = $connection;
        parent::__construct($registry, Customer::class);
    }

    public function save(Customer $customer):Customer
    {
        $branch_id = $customer->getBranch()->getId();
        $name = $customer->getName();
        $created_at = $customer->getCreatedAt();
        $sql = 'INSERT INTO customer (branch_id, name, created_at) VALUES ('. $branch_id . ',"' . $name .'","' . $created_at .'");';
        $statement = $this->connection->prepare($sql);
        $statement->executeQuery();
        $customer->setId($this->connection->lastInsertId());
        return $customer;
    }

    public function findOne(Customer $customer): ?Customer
    {
        $customer_id = $customer->getId();
        $sql = 'SELECT * FROM customer where id ='. $customer_id .';';
        $statement = $this->connection->prepare($sql);
        $statement->executeQuery();
        $database_returned = $statement->fetchAll();
        if(!isset($database_returned[0])) {
            return null;
        }
        $database_array_branch = $database_returned[0];
        $name = $database_array_branch["name"];
        $customer->setName($name);
        return $customer;
    }

    public function getTotalBalance(Customer $customer): ?float
    {
        $customer_id = $customer->getId();
        $sql = 'SELECT                             
                    customer.id customer_id,                              
                    SUM(balance.move) AS total_balance                      
                FROM customer                                                     
                INNER JOIN balance ON customer.id = balance.customer_id                     
                WHERE customer.id = '. $customer_id . ' 
                GROUP BY customer_id;';
        $statement = $this->connection->prepare($sql);
        $statement->executeQuery();
        $database_returned = $statement->fetchAll();
        if(!isset($database_returned[0])) {
            return null;
        }
        $database_array_branch = $database_returned[0];
        return floatval($database_array_branch["total_balance"]);
    }
}
