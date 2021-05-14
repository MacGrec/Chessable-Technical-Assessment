<?php

namespace App\Service;

use App\Entity\Balance;
use App\Entity\Customer;
use App\Repository\BalanceRepository;
use App\Repository\CustomerRepository;

class TransferBalance
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

    public function doAction(Customer $giver_customer, Customer $receiver_customer, float $amount, string $coin): ?bool
    {
        $total_balance_giver_customer = $this->customerRepository->getTotalBalance($giver_customer);
        if ($total_balance_giver_customer < $amount) {
            return null;
        }

        $now = date('Y-m-d H:i:s');
        $balance_giver_customer = new Balance();
        $balance_giver_customer->setCustomer($giver_customer);
        $balance_giver_customer->setMove(-1 * abs($amount));
        $balance_giver_customer->setCoin($coin);
        $balance_giver_customer->setCreatedAt($now);
        $this->balanceRepository->save($balance_giver_customer);
        $balance_receiver_customer = new Balance();
        $balance_receiver_customer->setCustomer($receiver_customer);
        $balance_receiver_customer->setMove($amount);
        $balance_receiver_customer->setCoin($coin);
        $balance_receiver_customer->setCreatedAt($now);
        $this->balanceRepository->save($balance_receiver_customer);
        return true;
    }
}
