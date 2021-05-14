<?php
namespace App\Controller\Api;


use App\Form\Model\BranchDto;
use App\Form\Model\CustomerDto;
use App\Form\Model\CustomerTransferDto;
use App\Form\Type\BranchFormType;
use App\Form\Type\CustomerFormType;
use App\Form\Type\CustomerTransferFormType;
use App\Service\CreateBranch;
use App\Service\CreateCustomer;
use App\Service\GetBranch;
use App\Service\GetCustomer;
use App\Service\TransferBalance;
use Doctrine\DBAL\Driver\Connection;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;

class CustomersController extends AbstractFOSRestController
{
    const CUSTOMER_NOT_EXIST = "Customer not exist";
    const GIVER_CUSTOMER_NOT_EXIST = "Giver Customer not exist";
    const RECEIVER_CUSTOMER_NOT_EXIST = "Receiver Customer not exist";
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
            return $this->mountNotExistResponse(self::CUSTOMER_NOT_EXIST);
        }
        $customerDto = new CustomerDto();
        $form = $this->fillCustomerDto($customerDto, $request);
        [$response, $response_code] = $this->formNotSubmitted($form);
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
            return $this->mountNotExistResponse(self::CUSTOMER_NOT_EXIST);
        }
        $response_code = Response::HTTP_OK;
        return View::create($customer, $response_code);
    }

    /**
     * @Rest\Post(path="/customers/balance/transfer")
     * @Rest\View(serializerGroups={"customer"}, serializerEnableMaxDepthChecks=true)
     */
    public function transferBalance(GetCustomer $getCustomer, TransferBalance $transferBalance, Request $request): View
    {
        $customerTransferDto = new CustomerTransferDto();
        $form = $this->FillCustomerTransferDto($customerTransferDto, $request);
        [$response, $response_code] = $this->formNotSubmitted($form);
        if ($form->isValid()) {
            $response_code = Response::HTTP_OK;

            $giver_customerDto = new CustomerDto();
            $giver_customerDto->id = $customerTransferDto->giver_customer_id;
            $giver_customer = $getCustomer->doAction($giver_customerDto);
            if(!isset($giver_customer)) {
                return $this->mountNotExistResponse(self::GIVER_CUSTOMER_NOT_EXIST);
            }
            $receiver_customerDto = new CustomerDto();
            $receiver_customerDto->id = $customerTransferDto->receiver_customer_id;
            $receiver_customer = $getCustomer->doAction($receiver_customerDto);
            if(!isset($receiver_customer)) {
                return $this->mountNotExistResponse(self::RECEIVER_CUSTOMER_NOT_EXIST);
            }

            $amount = $customerTransferDto->amount;
            $coin = $customerTransferDto->coin;
            $response = $transferBalance->doAction($giver_customer, $receiver_customer, $amount, $coin);
        }
        return View::create($response, $response_code);
    }

    private function formNotSubmitted(FormInterface $form): array
    {
        $response = $form;
        $response_code = Response::HTTP_BAD_REQUEST;
        if (!$form->isSubmitted()) {
            $response = [self::CODE_KEY => $response_code, self::MESSAGE_KEY => self::FORM_NOT_SUBMITTED];
        }
        return array($response, $response_code);
    }

    private function FillCustomerTransferDto(CustomerTransferDto $customerTransferDto, Request $request): FormInterface
    {
        $form = $this->createForm(CustomerTransferFormType::class, $customerTransferDto);
        $form->handleRequest($request);
        return $form;
    }

    private function fillCustomerDto(CustomerDto $customerDto, Request $request): FormInterface
    {
        $form = $this->createForm(CustomerFormType::class, $customerDto);
        $form->handleRequest($request);
        return $form;
    }

    private function mountNotExistResponse(string $message): View
    {
        $response_code = Response::HTTP_BAD_REQUEST;
        $response = [self::CODE_KEY => $response_code, self::MESSAGE_KEY => $message];
        return View::create($response, $response_code);
    }
}