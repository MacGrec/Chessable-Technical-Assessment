<?php


namespace App\Tests\Unit;

use App\Entity\Branch;
use App\Entity\Location;
use App\Form\Model\BranchDto;
use App\Form\Model\LocationDto;
use App\Repository\BranchRepository;
use App\Repository\LocationRepository;
use App\Service\CreateBranch;
use PHPUnit\Framework\TestCase;

class CreateBranchTest extends TestCase
{
    private function setUpMockCreateBranch(): array
    {
        $branchRepository = $this->getMockBuilder(BranchRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $locationRepository = $this->getMockBuilder(LocationRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        return [$branchRepository, $locationRepository];
    }

    public function testSuccessCreateBranch()
    {
        [$branchRepository, $locationRepository] = $this->setUpMockCreateBranch();

        $branch = $this->buildBranch();
        $location = $this->buildLocation();
        $branchDto = $this->buildBranchDto();

        $locationRepository
            ->expects(self::exactly(1))
            ->method('save')
            ->with($location)
            ->willReturn($location);

        $branchRepository
            ->expects(self::exactly(1))
            ->method('save')
            ->with($branch)
            ->willReturn($branch);

        $createBranch = new CreateBranch($branchRepository, $locationRepository);
        $createBranch->doAction($branchDto);
    }

    private function buildBranch(): Branch
    {
        $branch = new Branch();
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