<?php

namespace App\Entity;


class BranchHighestBalance
{
    private int $id;
    private float $highest_balance;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getHighestBalance(): float
    {
        return $this->highest_balance;
    }

    public function setHighestBalance(float $highest_balance): self
    {
        $this->highest_balance= $highest_balance;

        return $this;
    }
}
