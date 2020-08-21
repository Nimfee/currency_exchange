<?php

namespace App\Repository;

use App\Entity\Currency;
use App\Entity\CurrencyExchangeRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CurrencyExchangeRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurrencyExchangeRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurrencyExchangeRate[]    findAll()
 * @method CurrencyExchangeRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyExchangeRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrencyExchangeRate::class);
    }


    /**
     * @param Currency $currencyTo
     * @param Currency $currencyFrom
     * @param $rateDate
     * @return CurrencyExchangeRate|null
     */
    public function findOneByCurrenciesAndRateDate(
        Currency $currencyTo,
        Currency $currencyFrom,
        $rateDate
    )
    {
        return $this->findOneBy(
            [
                'currencyTo' => $currencyTo,
                'currencyFrom' => $currencyFrom,
                'rateDate' => $rateDate
            ]
        );
    }

    public function findCurrencyExchangeRate($from, $to)
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->where($qb->expr()->andX($qb->expr()->eq('c.currencyFrom', $from), $qb->expr()->eq('c.currencyTo', $to)))
            ->orWhere($qb->expr()->andX($qb->expr()->eq('c.currencyFrom', $to), $qb->expr()->eq('c.currencyTo', $from)))
            ->orderBy('c.rateDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param int $from
     * @param int $to
     * @return array
     */
    public function findComplexCurrencyExchangeRate(int $from, int $to)
    {
        $qb = $this->createQueryBuilder('c');

        $currenciesFrom =  $qb
            ->select('ct.id as ct_id, cf.id as cf_id ')
            ->leftJoin('c.currencyTo', 'ct', 'WITH', 'ct.id = c.currencyTo')
            ->leftJoin('c.currencyFrom', 'cf', 'WITH', 'cf.id = c.currencyFrom')
            ->where($qb->expr()->orX(
                $qb->expr()->eq('c.currencyFrom', $from),
                $qb->expr()->orX($qb->expr()->eq('c.currencyTo', $from)))
            )
            ->getQuery()
            ->getResult();

        $currenciesFromIds = $this->getIdsFromArray($currenciesFrom);

        $currenciesTo = $qb
            ->select('ct.id as ct_id, cf.id as cf_id')
            ->where($qb->expr()->orX(
                $qb->expr()->eq('c.currencyFrom', $to),
                $qb->expr()->orX($qb->expr()->eq('c.currencyTo', $to)))
            )
            ->getQuery()
            ->getResult();

        $currenciesToIds = $this->getIdsFromArray($currenciesTo);

        $result = array_intersect($currenciesFromIds, $currenciesToIds);
        if (count($result) > 0) {
            return [$from => array_shift($result)];
        }

        $currenciesFromIdsNext = [];
        foreach ($currenciesFromIds as $currenciesFromId) {

            $currenciesFrom =  $qb
                ->select('ct.id as ct_id, cf.id as cf_id ')
                ->where($qb->expr()->orX(
                    $qb->expr()->eq('c.currencyFrom', $currenciesFromId),
                    $qb->expr()->orX($qb->expr()->eq('c.currencyTo', $currenciesFromId)))
                )
                ->getQuery()
                ->getResult();

            $currenciesFromIdsNext[$currenciesFromId] = $this->getIdsFromArray($currenciesFrom);
            $result =  array_intersect($currenciesFromIdsNext[$currenciesFromId], $currenciesToIds);
            if (count($result) > 0) {

                return [$from => ['from' => $currenciesFromId, 'to' => array_shift($result)]];
            }
        }

        return [];
    }

    protected function getIdsFromArray($currencies)
    {
        $currenciesIds = [];
        array_map(function ($item) use (&$currencies, &$currenciesIds) {
            if (array_key_exists('ct_id', $item)) {
                $currenciesIds[] = $item['ct_id'];
            }
            if (array_key_exists('ct_id', $item)) {
                $currenciesIds[] = $item['cf_id'];
            }
        }, $currencies);

        return array_unique($currenciesIds);
    }

}
