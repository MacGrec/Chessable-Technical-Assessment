<?php


namespace App\Tests\Unit;

use App\Entity\Branch;
use App\Entity\Location;
use App\Form\Model\BranchDto;
use App\Form\Model\LocationDto;
use App\Repository\BranchRepository;
use App\Service\GetBranch;
use PHPUnit\Framework\TestCase;

class GetBranchTest extends TestCase
{

    public function testSuccessCreateBranch()
    {
        $branchRepository = $this->getMockBuilder(BranchRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $branch = $this->buildBranch();
        $branchDto = $this->buildBranchDto();

        $branchRepository
            ->expects(self::exactly(1))
            ->method('findOne')
            ->with($branch->getId())
            ->willReturn($branch);

        $createBranch = new GetBranch($branchRepository);
        $createBranch->doAction($branchDto);
    }

    private function buildBranch(): Branch
    {
        $branch = new Branch();
        $branch->setId(1);
        $branch->setName("Madrid Branch");
        $location = $this->buildLocation();
        $branch->setLocation($location);
        return $branch;
    }

    private function buildLocation(): Location
    {
        $location = new Location();
        $location->setAddress("Calla Alcala 33");
        $location->setPostalCode(28001);
        $location->setProvince("Madrid");
        $location->setCountry("Spain");
        return $location;
    }


    private function buildBranchDto(): BranchDto
    {
        $branchDto = new BranchDto();
        $branchDto->id = 1;
        $branchDto->name = "Madrid Branch";
        $locationDto = $this->buildLocationDto();
        $branchDto->location= $locationDto;
        return $branchDto;
    }

    private function buildLocationDto(): LocationDto
    {
        $locationDto = new LocationDto();
        $locationDto->address = "Calla Alcala 33" ;
        $locationDto->postal_code = 28001;
        $locationDto->province = "Madrid";
        $locationDto->country = "Spain";
        return $locationDto;
    }
}