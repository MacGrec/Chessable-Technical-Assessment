<?php

namespace App\Service;

use App\Entity\Location;
use App\Form\Model\BranchDto;
use App\Entity\Branch;
use App\Repository\BranchRepository;
use App\Repository\LocationRepository;

class CreateBranch
{
    private $branchRepository;
    private $locationRepository;

    public function __construct(
        BranchRepository $branchRepository,
        LocationRepository $locationRepository
    )
    {
        $this->branchRepository = $branchRepository;
        $this->locationRepository = $locationRepository;
    }

    public function doAction(BranchDto $branchDto): Branch
    {
        $location = $this->setLocation($branchDto);
        $location_saved = $this->saveLocation($location);
        $branch = $this->setBranch($branchDto, $location_saved);
        return $this->saveBranch($branch);
    }

    private function setLocation(BranchDto $branchDto): Location
    {
        $location = new Location();
        $location->setAddress($branchDto->location->address);
        $location->setPostalCode($branchDto->location->postal_code);
        $location->setProvince($branchDto->location->province);
        $location->setCountry($branchDto->location->country);
        return $location;
    }

    private function saveLocation(Location $location): Location
    {
        return $this->locationRepository->save($location);
    }

    private function setBranch(BranchDto $branchDto, Location $location_saved): Branch
    {
        $branch = new Branch();
        $branch->setName($branchDto->name);
        $branch->setLocation($location_saved);
        $now = date('Y-m-d H:i:s');
        $branch->setCreatedAt($now);
        return $branch;
    }

    private function saveBranch(Branch $branch): Branch
    {
        return $this->branchRepository->save($branch);
    }
}
