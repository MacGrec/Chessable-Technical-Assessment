<?php

namespace App\Repository;

use App\Entity\Branch;
use App\Entity\BranchHighestBalance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\Persistence\ManagerRegistry;
use \Doctrine\DBAL\Driver\Statement;

/**
 * @method Branch|null find($id, $lockMode = null, $lockVersion = null)
 * @method Branch|null findOneBy(array $criteria, array $orderBy = null)
 * @method Branch[]    findAll()
 * @method Branch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BranchRepository extends ServiceEntityRepository
{
    private Connection $connection;

    public function __construct(
        ManagerRegistry $registry,
        Connection $connection
    )
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
        $this->executeQuery($sql);
        $branch->setId($this->connection->lastInsertId());
        return $branch;
    }

    public function findOne(Branch $branch): ?Branch
    {
        $branch_id = $branch->getId();
        $sql = 'SELECT * FROM branch where id ='. $branch_id .';';
        $database_returned_data = $this->getDatabaseData($sql);
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
        $database_returned_data = $this->getDatabaseData($sql);
        if(!isset($database_returned_data[0])) {
            return null;
        }
        $branchesHighestBalance = [];
        foreach ($database_returned_data as $branchHighestBalance_database_array){
            $branchHighestBalance = $this->setBranchHighestBalance($branchHighestBalance_database_array);
            $branchesHighestBalance[] = $branchHighestBalance;
        }
        return $branchesHighestBalance;
    }

    public function getReportBranchesWithSpecificNumberCustomersWithSpecificBalance(
        int $minimum_number_customer,
        float $minimum_total_balance
    ): ?array
    {
        $sql = 'SELECT 
                        *
                FROM (
                    SELECT                         
                           branch_id,                          
                           branch_name,                          
                           COUNT(customer_id) AS total_customers                                 
                    FROM ( 
                        SELECT                              
                               branch.id branch_id,                            
                               branch.name branch_name,                                                        
                               customer.name customer_name,                             
                               customer.id customer_id,                              
                               SUM(balance.move) AS balance_total                       
                        FROM branch                            
                            INNER JOIN customer ON branch.id = customer.branch_id                            
                            INNER JOIN balance ON customer.id = balance.customer_id                    
                        GROUP BY customer_id                    
                        ) as q 
                    WHERE balance_total >= ' .$minimum_total_balance. ' 
                    GROUP BY branch_id
                    ) as t 
                WHERE total_customers >= ' .$minimum_number_customer. '; ';
        $database_returned_data = $this->getDatabaseData($sql);
        if(!isset($database_returned_data[0])) {
            return null;
        }
        $branchesBalanceCustomers = [];
        foreach ($database_returned_data as $branchHighestBalance_database_array){
            $branch = $this->setBranch($branchHighestBalance_database_array);
            $branchesBalanceCustomers[] = $branch;
        }
        return $branchesBalanceCustomers;
    }

    private function setBranch(mixed $branchHighestBalance_database_array): Branch
    {
        $branch_id = $branchHighestBalance_database_array["branch_id"];
        $branch_name = $branchHighestBalance_database_array["branch_name"];
        $branch = new Branch();
        $branch->setId($branch_id);
        $branch->setName($branch_name);
        return $branch;
    }

    private function setBranchHighestBalance(mixed $branchHighestBalance_database_array): BranchHighestBalance
    {
        $branch_id = $branchHighestBalance_database_array["branch_id"];
        $highest_balance = $branchHighestBalance_database_array["highest_balance"];
        $branchHighestBalance = new BranchHighestBalance();
        $branchHighestBalance->setId($branch_id);
        $branchHighestBalance->setHighestBalance($highest_balance);
        return $branchHighestBalance;
    }

    private function getDatabaseData(string $sql): array
    {
        $statement = $this->executeQuery($sql);
        $database_returned_data = $statement->fetchAll();
        return $database_returned_data;
    }

    private function executeQuery(string $sql): Statement
    {
        $statement = $this->connection->prepare($sql);
        $statement->executeQuery();
        return $statement;
    }
}
