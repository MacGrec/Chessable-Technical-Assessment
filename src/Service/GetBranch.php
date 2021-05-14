<?php

namespace App\Service;

use App\Entity\Location;
use App\Form\Model\BranchDto;
use App\Entity\Branch;
use App\Repository\BranchRepository;
use App\Repository\LocationRepository;

class GetBranch
{
    private $branchRepository;

    public function __construct(BranchRepository $branchRepository)
    {
        $this->branchRepository = $branchRepository;
    }

    public function doAction(BranchDto $branchDto): ?Branch
    {
        $branch = new Branch();
        $branch->setId($branchDto->id);
        return $this->branchRepository->findOne($branch);
    }
}
