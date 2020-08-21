<?php

namespace App\Repository;

use App\Entity\Currency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Currency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Currency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Currency[]    findAll()
 * @method Currency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Currency::class);
    }

    /**
     * @return mixed
     */
    public function getAllInArray()
    {
        $query = $this
            ->createQueryBuilder('c')
            ->getQuery();

        return $query->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * @return mixed
     */
    public function getByIsoCode(string $isoCode)
    {
        return $this->findOneBy(['ISO_code' => $isoCode]);
    }
}
