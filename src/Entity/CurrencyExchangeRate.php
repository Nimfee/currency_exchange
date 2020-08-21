<?php

namespace App\Entity;

use App\Entity\Traits\TimeStampableTrait;
use App\Repository\CurrencyExchangeRateRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass=CurrencyExchangeRateRepository::class)
 * @ORM\Table(indexes={@ORM\Index(name="currency_from_to_rate", columns={"currency_from_id", "currency_to_id", "rate_date"})})
 */
class CurrencyExchangeRate
{
    use TimeStampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $rate;

    /**
     * @ORM\Column(type="date")
     */
    private $rateDate;

    /**
     * @ORM\ManyToOne(targetEntity=Currency::class, inversedBy="currencyExchangeRates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $currencyFrom;

    /**
     * @ORM\ManyToOne(targetEntity=Currency::class, inversedBy="currencyExchangeRates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $currencyTo;

    /**
     * @ORM\Column(type="integer")
     */
    private $currency_from_id = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $currency_to_id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getRateDate(): ?\DateTimeInterface
    {
        return $this->rateDate;
    }

    public function setRateDate(\DateTimeInterface $rateDate): self
    {
        $this->rateDate = $rateDate;

        return $this;
    }

    public function getCurrencyFrom(): ?Currency
    {
        return $this->currencyFrom;
    }

    public function setCurrencyFrom(?Currency $currencyFrom): self
    {
        $this->currencyFrom = $currencyFrom;

        return $this;
    }

    public function getCurrencyTo(): ?Currency
    {
        return $this->currencyTo;
    }

    public function setCurrencyTo(?Currency $currencyTo): self
    {
        $this->currencyTo = $currencyTo;

        return $this;
    }

    public function getCurrencyFromId(): ?int
    {
        return $this->currency_from_id;
    }
    
    public function setCurrencyFromId(int $currency_from_id): self
    {
        $this->currency_from_id = $currency_from_id;
        
        return $this;
    }
    
    public function getCurrencyToId(): ?int
    {
        return $this->currency_to_id;
    }
    
    public function setCurrencyToId(int $currency_to_id): self
    {
        $this->currency_to_id = $currency_to_id;
        
        return $this;
    }
}
