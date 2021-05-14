<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 */
class Customer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Balance::class, mappedBy="customer", orphanRemoval=true)
     */
    private $balance;

    /**
     * @ORM\ManyToOne(targetEntity=Branch::class, inversedBy="customer")
     * @ORM\JoinColumn(nullable=false)
     */
    private $branch;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    public function __construct()
    {
        $this->balance = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }
    /**
     * @return Collection|Balance[]
     */
    public function getBalance(): Collection
    {
        return $this->balance;
    }

    public function addBalance(Balance $balance): self
    {
        if (!$this->balance->contains($balance)) {
            $this->balance[] = $balance;
            $balance->setCustomer($this);
        }

        return $this;
    }

    public function removeBalance(Balance $balance): self
    {
        if ($this->balance->removeElement($balance)) {
            // set the owning side to null (unless already changed)
            if ($balance->getCustomer() === $this) {
                $balance->setCustomer(null);
            }
        }

        return $this;
    }

    public function getBranch(): ?Branch
    {
        return $this->branch;
    }

    public function setBranch(?Branch $branch): self
    {
        $this->branch = $branch;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
