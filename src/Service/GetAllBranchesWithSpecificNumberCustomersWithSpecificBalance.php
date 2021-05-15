<?php

namespace App\Service;

use App\Repository\BranchRepository;

class GetAllBranchesWithSpecificNumberCustomersWithSpecificBalance
{
    private BranchRepository $branchRepository;

    public function __construct(BranchRepository $branchRepository)
    {
        $this->branchRepository = $branchRepository;
    }

    public function doAction(
        int $minimum_number_customer,
        float $minimum_total_balance
    ): ?array
    {
        return $this->branchRepository->getReportBranchesWithSpecificNumberCustomersWithSpecificBalance($minimum_number_customer, $minimum_total_balance);
    }
}
