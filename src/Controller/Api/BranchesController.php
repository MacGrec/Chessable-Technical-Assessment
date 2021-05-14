<?php
namespace App\Controller\Api;

use Doctrine\DBAL\Driver\Connection;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BranchesController extends AbstractFOSRestController
{

    /**
     * @Rest\Get(path="/branches/report/balance/highest")
     * @Rest\View(serializerGroups={"branch"}, serializerEnableMaxDepthChecks=true)
     */
    public function getReportBalanceHighest(Connection $connection)
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
        $statement = $connection->prepare($sql);
        $statement->executeQuery();
        $response = $statement->fetchAll();
        $response_code = Response::HTTP_OK;
        return View::create($response, $response_code);
    }


    /**
     * @Rest\Get(path="/branches/report/balance/morethan")
     * @Rest\View(serializerGroups={"branch"}, serializerEnableMaxDepthChecks=true)
     */
    public function getReportBalanceMoreThan(Connection $connection)
    {
        $sql = 'SELECT 
                        *
                FROM (
                    SELECT                         
                           branch_id,                          
                           branch_name,                          
                           location_id,                          
                           location_address, 
                           location_postal_code,
                           location_province,
                           location_country,
                           COUNT(customer_id) AS total_customers                                 
                    FROM ( 
                        SELECT                              
                               branch.id branch_id,                            
                               branch.name branch_name,                             
                               branch.location_id location_id,                            
                               customer.name customer_name,                             
                               customer.id customer_id,                              
                               SUM(balance.move) AS balance_total,                            
                               location.address location_address,
                               location.postal_code location_postal_code,
                               location.province location_province,
                               location.country location_country
                               
                        FROM branch                            
                            INNER JOIN customer ON branch.id = customer.branch_id                            
                            INNER JOIN balance ON customer.id = balance.customer_id                           
                            INNER JOIN location ON branch.location_id = location.id                     
                        GROUP BY customer_id                    
                        ) as q 
                    WHERE balance_total >= 20 
                    GROUP BY branch_id
                    ) as t 
                WHERE total_customers >= 2; ';
        $statement = $connection->prepare($sql);
        $statement->executeQuery();
        $response = $statement->fetchAll();
        $response_code = Response::HTTP_OK;
        return View::create($response, $response_code);
    }



}