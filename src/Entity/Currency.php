<?php

namespace App\Entity;

use App\Entity\Traits\TimeStampableTrait;
use App\Repository\CurrencyRepository;
use App\Entity\CurrencyExchangeRate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass=CurrencyRepository::class)
 */
class Currency
{
    use TimeStampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=3, unique=true)
     */
    private $ISO_code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=CurrencyExchangeRate::class, mappedBy="currencyFrom")
     */
    private $currencyExchangeRates;


    public function __construct()
    {
        $this->currencyExchangeRates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getISOCode(): ?string
    {
        return $this->ISO_code;
    }

    public function setISOCode(string $ISO_code): self
    {
        $this->ISO_code = $ISO_code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSource(): ?Source
    {
        return $this->source;
    }

    public function setSource(?Source $source): self
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return Collection|CurrencyExchangeRate[]
     */
    public function getCurrencyExchangeRates(): Collection
    {
        return $this->currencyExchangeRates;
    }

    public function addCurrencyExchangeRate(CurrencyExchangeRate $currencyExchangeRate): self
    {
        if (!$this->currencyExchangeRates->contains($currencyExchangeRate)) {
            $this->currencyExchangeRates[] = $currencyExchangeRate;
            $currencyExchangeRate->setCurrencyFrom($this);
        }

        return $this;
    }

    public function removeCurrencyExchangeRate(CurrencyExchangeRate $currencyExchangeRate): self
    {
        if ($this->currencyExchangeRates->contains($currencyExchangeRate)) {
            $this->currencyExchangeRates->removeElement($currencyExchangeRate);
            // set the owning side to null (unless already changed)
            if ($currencyExchangeRate->getCurrencyFrom() === $this) {
                $currencyExchangeRate->setCurrencyFrom(null);
            }
        }

        return $this;
    }
}
