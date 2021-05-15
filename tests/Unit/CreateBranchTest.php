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
    public function testSuccessCreateBranch()
    {
        $branchRepository = $this->getMockBuilder(BranchRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $locationRepository = $this->getMockBuilder(LocationRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $now = date('Y-m-d H:i:s');
        $branch = $this->buildBranch($now);
        $location = $this->buildLocation();
        $branchDto = $this->buildBranchDto($now);


        $locationRepository
            ->expects(self::exactly(1))
            ->method('save')
            ->with($location)
            ->willReturn($location);

        $branchRepository
            ->expects(self::exactly(1))
            ->method('save')
            ->with($branch)
            ->willReturn($branch->setCreatedAt($now));

        $createBranch = new CreateBranch($branchRepository, $locationRepository);
        $result = $createBranch->doAction($branchDto);
        $this->assertSame($branch->getName(), $result->getName());
        $this->assertSame($branch->getLocation()->getAddress(), $result->getLocation()->getAddress());
    }

    private function buildBranch(string $now): Branch
    {
        $branch = new Branch();
        $branch->setName("Madrid Branch");
        $branch->setCreatedAt($now);
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

    private function buildBranchDto(string $now): BranchDto
    {
        $branchDto = new BranchDto();
        $branchDto->name = "Madrid Branch";
        $branchDto->created_at = $now;
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