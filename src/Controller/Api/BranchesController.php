<?php
namespace App\Controller\Api;

use App\Form\Model\BranchDto;
use App\Form\Type\BranchFormType;
use App\Service\CreateBranch;
use App\Service\CreateCustomer;
use App\Service\GetBranch;
use Doctrine\DBAL\Driver\Connection;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BranchesController extends AbstractFOSRestController
{
    const BRANCH_NOT_EXIST = "Branch not exist";
    const FORM_NOT_SUBMITTED = "Form not submitted";
    const MESSAGE_KEY = "message";
    const CODE_KEY = "code";

    /**
     * @Rest\Post(path="/branches/add")
     * @Rest\View(serializerGroups={"branch"}, serializerEnableMaxDepthChecks=true)
     */
    public function add(CreateBranch $createBranch, Request $request): View
    {
        $branchDto = new BranchDto();
        $form = $this->createForm(BranchFormType::class, $branchDto);
        $form->handleRequest($request);
        $response = $form;
        $response_code = Response::HTTP_BAD_REQUEST;
        if (!$form->isSubmitted()) {
            $response_code = Response::HTTP_BAD_REQUEST;
            $response = [self::CODE_KEY => $response_code, self::MESSAGE_KEY => self::FORM_NOT_SUBMITTED];
        }
        if ($form->isValid()) {
            $response_code = Response::HTTP_OK;
            $response = $createBranch->doAction($branchDto);;
        }
        return View::create($response, $response_code);
    }

    /**
     * @Rest\Get(path="/branches/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"branch"}, serializerEnableMaxDepthChecks=true)
     */
    public function getOne(int $id, GetBranch $getBranch): View
    {
        $branchDto = new BranchDto();
        $branchDto->id = $id;
        $branch = $getBranch->doAction($branchDto);
        if(!isset($branch)) {
            $response_code = Response::HTTP_BAD_REQUEST;
            $response = [self::CODE_KEY => $response_code, self::MESSAGE_KEY => self::BRANCH_NOT_EXIST];
            return View::create($response, $response_code);
        }
        $response_code = Response::HTTP_OK;
        return View::create($branch, $response_code);
    }

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