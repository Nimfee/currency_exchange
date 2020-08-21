<?php
namespace App\EventSubscriber;

use App\Entity\CurrencyExchangeRate;
use App\Entity\Source;
use App\Repository\CurrencyRepository;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    /** @var CurrencyRepository  */
    private $currencyRepository;

    /**
     * EasyAdminSubscriber constructor.
     * @param CurrencyRepository $currencyRepository
     */
    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['setCurrencyToSource'],
        ];
    }

    public function setCurrencyToSource(BeforeEntityPersistedEvent $event)
    {
        /** @var Source $entity */
        $entity = $event->getEntityInstance();

        if (($entity instanceof Source)) {
            $currency = $this->currencyRepository->find($entity->getCurrencyId());
            $entity->setCurrency($currency);
        }
        if (($entity instanceof CurrencyExchangeRate)) {
            $currencyTo = $this->currencyRepository->find($entity->getCurrencyToId());
            $currencyFrom = $this->currencyRepository->find($entity->getCurrencyFromId());
            $entity->setCurrencyFrom($currencyFrom);
            $entity->setCurrencyTo($currencyTo);
        }
    }
}
