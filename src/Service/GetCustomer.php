<?php

namespace App\Service;

use App\Entity\Customer;
use App\Form\Model\CustomerDto;
use App\Repository\CustomerRepository;

class GetCustomer
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function doAction(CustomerDto $customerDto): ?Customer
    {
        return $this->customerRepository->findOne($customerDto->id);
    }
}
