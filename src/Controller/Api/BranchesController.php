<?php
namespace App\Controller\Api;

use App\Form\Model\BranchDto;
use App\Form\Model\MinimumCustomerTotalBalanceDto;
use App\Form\Type\BranchFormType;
use App\Form\Type\MinimumCustomerTotalBalanceFormType;
use App\Service\CreateBranch;
use App\Service\GetAllBranchesWithQuantityCustomersWithMoreQuantityBalance;
use App\Service\GetAllBranchesWithSpecificNumberCustomersWithSpecificBalance;
use App\Service\GetBranch;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;

class BranchesController extends AbstractFOSRestController
{
    const BRANCH_NOT_EXIST = "Branch not exist";
    const NO_EXIST_DATA = "Not exist data to generate report";
    const FORM_NOT_SUBMITTED = "Form not submitted";
    const MESSAGE_KEY = "message";
    const CODE_KEY = "code";

    /**
     * @Rest\Post(path="/branches/add")
     * @Rest\View(serializerGroups={"branch"}, serializerEnableMaxDepthChecks=true)
     */
    public function add(
        CreateBranch $createBranch,
        Request $request
    ): View
    {
        $branchDto = new BranchDto();
        $form = $this->fillBranchDto($branchDto, $request);
        $response = $form;
        $response_code = Response::HTTP_BAD_REQUEST;
        if (!$form->isSubmitted()) {
            [$response_code, $response] = $this->mountFormNotSubmitted();
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
    public function getOne(
        int $id,
        GetBranch $getBranch
    ): View
    {
        $branchDto = new BranchDto();
        $branchDto->id = $id;
        $branch = $getBranch->doAction($branchDto);
        if(!isset($branch)) {
            return $this->mountBranchNotExist();
        }
        $response_code = Response::HTTP_OK;
        return View::create($branch, $response_code);
    }

    /**
     * @Rest\Get(path="/branches/report/balance/highest")
     * @Rest\View(serializerGroups={"branchHighest"}, serializerEnableMaxDepthChecks=true)
     */
    public function getReportBalanceHighest(
        GetAllBranchesWithQuantityCustomersWithMoreQuantityBalance $allBranchesWithQuantityCustomersWithMoreQuantityBalance
    )
    {
        $report_all_branches_highest_balance = $allBranchesWithQuantityCustomersWithMoreQuantityBalance->doAction();
        if(empty($report_all_branches_highest_balance)) {
            return $this->mountNotExistData();
        }

        $response_code = Response::HTTP_OK;
        return View::create($report_all_branches_highest_balance, $response_code);
    }


    /**
     * @Rest\Post(path="/branches/report/balance/morethan")
     * @Rest\View(serializerGroups={"branch"}, serializerEnableMaxDepthChecks=true)
     */
    public function getReportBalanceMoreThan(
        GetAllBranchesWithSpecificNumberCustomersWithSpecificBalance $allBranchesWithSpecificNumberCustomersWithSpecificBalance,
        Request $request
    )
    {
        $minimumCustomerTotalBalanceDto = new MinimumCustomerTotalBalanceDto();
        $form = $this->fillMinimumCustomerTotalBalanceDto($minimumCustomerTotalBalanceDto, $request);
        $response = $form;
        $response_code = Response::HTTP_BAD_REQUEST;
        if (!$form->isSubmitted()) {
            [$response_code, $response] = $this->mountFormNotSubmitted();
        }
        if ($form->isValid()) {
            $minimum_number_customer = $minimumCustomerTotalBalanceDto->minimum_number_customer;
            $minimum_total_balance = $minimumCustomerTotalBalanceDto->minimum_total_balance;
            $response = $allBranchesWithSpecificNumberCustomersWithSpecificBalance->doAction($minimum_number_customer, $minimum_total_balance);
            if(empty($response)) {
                return $this->mountNotExistData();
            }

            $response_code = Response::HTTP_OK;
        }

        return View::create($response, $response_code);
    }

    private function mountFormNotSubmitted(): array
    {
        $response_code = Response::HTTP_BAD_REQUEST;
        $response = [self::CODE_KEY => $response_code, self::MESSAGE_KEY => self::FORM_NOT_SUBMITTED];
        return array($response_code, $response);
    }

    private function fillBranchDto(
        BranchDto $branchDto,
        Request $request
    ): FormInterface
    {
        $form = $this->createForm(BranchFormType::class, $branchDto);
        $form->handleRequest($request);
        return $form;
    }

    private function mountBranchNotExist(): View
    {
        $response_code = Response::HTTP_BAD_REQUEST;
        $response = [self::CODE_KEY => $response_code, self::MESSAGE_KEY => self::BRANCH_NOT_EXIST];
        return View::create($response, $response_code);
    }

    private function mountNotExistData(): View
    {
        $response_code = Response::HTTP_BAD_REQUEST;
        $response = [self::CODE_KEY => $response_code, self::MESSAGE_KEY => self::NO_EXIST_DATA];
        return View::create($response, $response_code);
    }

    private function fillMinimumCustomerTotalBalanceDto(
        MinimumCustomerTotalBalanceDto $minimumCustomerTotalBalanceDto,
        Request $request
    ): FormInterface
    {
        $form = $this->createForm(MinimumCustomerTotalBalanceFormType::class, $minimumCustomerTotalBalanceDto);
        $form->handleRequest($request);
        return $form;
    }


}