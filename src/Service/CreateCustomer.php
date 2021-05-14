<?php

namespace App\Service;

use App\Entity\Balance;
use App\Entity\Customer;
use App\Entity\Branch;
use App\Form\Model\CustomerDto;
use App\Repository\BalanceRepository;
use App\Repository\CustomerRepository;

class CreateCustomer
{
    private CustomerRepository $customerRepository;
    private BalanceRepository $balanceRepository;

    public function __construct(
        CustomerRepository $customerRepository,
        BalanceRepository $balanceRepository
    )
    {
        $this->customerRepository = $customerRepository;
        $this->balanceRepository = $balanceRepository;
    }

    public function doAction(CustomerDto $customerDto, Branch $branch): Customer
    {
        $customer = $this->setCustomer($customerDto, $branch);
        $customer_saved = $this->saveCustomer($customer);
        $balance = $this->setBalance($customerDto, $customer_saved);
        $this->saveBalance($balance);
        return $customer_saved;
    }

    private function setCustomer(CustomerDto $customerDto, Branch $branch): Customer
    {
        $customer = new Customer();
        $customer->setName($customerDto->name);
        $customer->setBranch($branch);
        $now = date('Y-m-d H:i:s');
        $customer->setCreatedAt($now);
        return $customer;
    }

    private function saveCustomer(Customer $customer): Customer
    {
        return $this->customerRepository->save($customer);
    }

    private function setBalance(CustomerDto $customerDto, Customer $customer_saved): Balance
    {
        $balance = new Balance();
        $balance->setMove($customerDto->balance->move);
        $balance->setCoin($customerDto->balance->coin);
        $balance->setCustomer($customer_saved);
        $balance->setSecondaryCustomer($customerDto->balance->secondary_customer);
        $now = date('Y-m-d H:i:s');
        $balance->setCreatedAt($now);
        return $balance;
    }

    private function saveBalance(Balance $balance): Balance
    {
        return $this->balanceRepository->save($balance);
    }
}
