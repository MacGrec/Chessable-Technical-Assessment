<?php

namespace App\Service;

use App\Repository\BranchRepository;

class GetAllBranchesWithQuantityCustomersWithMoreQuantityBalance
{
    private $branchRepository;

    public function __construct(BranchRepository $branchRepository)
    {
        $this->branchRepository = $branchRepository;
    }

    public function doAction(): ?array
    {
        return $this->branchRepository->getReportBalanceHighest();
    }
}
