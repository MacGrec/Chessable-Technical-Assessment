<?php

namespace App\Repository;

use App\Entity\Branch;
use App\Entity\BranchHighestBalance;
use App\Form\Model\BranchDto;
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

    public function save(Branch $branch): Branch
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

    public function findOne(Branch $branch): ?Branch
    {
        $branch_id = $branch->getId();
        $sql = 'SELECT * FROM branch where id ='. $branch_id .';';
        $statement = $this->connection->prepare($sql);
        $statement->executeQuery();
        $database_returned_data = $statement->fetchAll();
        if(!isset($database_returned_data[0])) {
            return null;
        }
        $database_array_branch = $database_returned_data[0];
        $name = $database_array_branch["name"];
        $branch->setName($name);
        return $branch;
    }

    public function getReportBalanceHighest(): ?array
    {
        $sql = 'SELECT 
                       branch_id,
                       MAX(balance_total) AS highest_balance
                FROM (
                    SELECT                         
                           branch_id,                                                
                           customer_id,                         
                           balance_total                
                    FROM (                      
                        SELECT                             
                               branch.id branch_id,                               
                               customer.id customer_id,                             
                               SUM(balance.move) AS balance_total                      
                        FROM branch                           
                            INNER JOIN customer ON branch.id = customer.branch_id                           
                            INNER JOIN balance ON customer.id = balance.customer_id                     
                        GROUP BY customer_id                      
                        UNION ALL                      
                        SELECT                             
                               branch.id branch_id,                                                        
                               customer.id customer_id, 0 AS balance_total                    
                        FROM branch                           
                            LEFT JOIN customer ON branch.id = customer.branch_id                      
                        WHERE customer.branch_id IS NULL                      
                        ) as t
                    ) as r 
                GROUP BY branch_id; ';
        $statement = $this->connection->prepare($sql);
        $statement->executeQuery();
        $database_returned_data = $statement->fetchAll();
        if(!isset($database_returned_data[0])) {
            return null;
        }
        $branchHighestBalances = [];
        foreach ($database_returned_data as $branchHighestBalance_database_array){
            $branch_id = $branchHighestBalance_database_array["branch_id"];
            $highest_balance = $branchHighestBalance_database_array["highest_balance"];
            $branchHighestBalance = new BranchHighestBalance();
            $branchHighestBalance->setId($branch_id);
            $branchHighestBalance->setHighestBalance($highest_balance);
            $branchHighestBalances[] = $branchHighestBalance;
        }
        return $branchHighestBalances;
    }
}
