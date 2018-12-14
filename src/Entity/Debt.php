<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Debt
 *
 * @ORM\Table(name="debt", indexes={@ORM\Index(name="fk_debt_person1_idx", columns={"from_id"}), @ORM\Index(name="fk_debt_person2_idx", columns={"to_id"})})
 * @ORM\Entity
 */
class Debt
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $amount;

    /**
     * @var bool
     *
     * @ORM\Column(name="paid", type="boolean", nullable=false)
     */
    private $paid = '0';

    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="from_id", referencedColumnName="id")
     * })
     */
    private $from;

    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="to_id", referencedColumnName="id")
     * })
     */
    private $to;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPaid(): ?bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;

        return $this;
    }

    public function getFrom(): ?Person
    {
        return $this->from;
    }

    public function setFrom(?Person $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function getTo(): ?Person
    {
        return $this->to;
    }

    public function setTo(?Person $to): self
    {
        $this->to = $to;

        return $this;
    }


}
