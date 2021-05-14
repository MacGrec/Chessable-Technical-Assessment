<?php
namespace App\Controller\Api;


use App\Form\Model\BranchDto;
use App\Form\Model\CustomerDto;
use App\Form\Type\BranchFormType;
use App\Form\Type\CustomerFormType;
use App\Service\CreateBranch;
use App\Service\CreateCustomer;
use App\Service\GetBranch;
use App\Service\GetCustomer;
use Doctrine\DBAL\Driver\Connection;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomersController extends AbstractFOSRestController
{
    const CUSTOMER_NOT_EXIST = "Customer not exist";
    const FORM_NOT_SUBMITTED = "Form not submitted";
    const MESSAGE_KEY = "message";
    const CODE_KEY = "code";

    /**
     * @Rest\Post(path="/customers/add/branch/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"customer"}, serializerEnableMaxDepthChecks=true)
     */
    public function add(int $id, GetBranch $getBranch, CreateCustomer $createCustomer, Request $request): View
    {
        $branchDto = new BranchDto();
        $branchDto->id = $id;
        $branch = $getBranch->doAction($branchDto);
        if(!isset($branch)) {
            var_dump($branch);die;
            $response_code = Response::HTTP_BAD_REQUEST;
            $response = [self::CODE_KEY => $response_code, self::MESSAGE_KEY => self::CUSTOMER_NOT_EXIST];
            return View::create($response, $response_code);
        }
        $customerDto = new CustomerDto();
        $form = $this->createForm(CustomerFormType::class, $customerDto);
        $form->handleRequest($request);
        $response = $form;
        $response_code = Response::HTTP_BAD_REQUEST;
        if (!$form->isSubmitted()) {
            $response_code = Response::HTTP_BAD_REQUEST;
            $response = [self::CODE_KEY => $response_code, self::MESSAGE_KEY => self::FORM_NOT_SUBMITTED];
        }
        if ($form->isValid()) {
            $response_code = Response::HTTP_OK;
            $response = $createCustomer->doAction($customerDto, $branch);;
        }
        return View::create($response, $response_code);
    }

    /**
     * @Rest\Get(path="/customers/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"customer"}, serializerEnableMaxDepthChecks=true)
     */
    public function getOne(int $id, GetCustomer $getCustomer): View
    {
        $customerDto = new CustomerDto();
        $customerDto->id = $id;
        $customer = $getCustomer->doAction($customerDto);
        if(!isset($customer)) {
            $response_code = Response::HTTP_BAD_REQUEST;
            $response = [self::CODE_KEY => $response_code, self::MESSAGE_KEY => self::CUSTOMER_NOT_EXIST];
            return View::create($response, $response_code);
        }
        $response_code = Response::HTTP_OK;
        return View::create($customer, $response_code);
    }

    /**
     * @Rest\Post(path="/customers/balance/transfer")
     * @Rest\View(serializerGroups={"customer"}, serializerEnableMaxDepthChecks=true)
     */
    public function transferBalance(GetBranch $getBranch, CreateCustomer $createCustomer, Request $request): View
    {

    }
}