<?php

namespace App\Service;

use App\Form\Model\BranchDto;
use App\Entity\Branch;
use App\Repository\BranchRepository;

class GetBranch
{
    private BranchRepository $branchRepository;

    public function __construct(BranchRepository $branchRepository)
    {
        $this->branchRepository = $branchRepository;
    }

    public function doAction(BranchDto $branchDto): ?Branch
    {
        return $this->branchRepository->findOne($branchDto->id);
    }
}
