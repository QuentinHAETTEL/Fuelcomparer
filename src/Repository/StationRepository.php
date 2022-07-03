<?php

namespace App\Repository;

use App\Entity\Station;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Station>
 *
 * @method Station|null find($id, $lockMode = null, $lockVersion = null)
 * @method Station|null findOneBy(array $criteria, array $orderBy = null)
 * @method Station[]    findAll()
 * @method Station[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Station::class);
    }


    public function add(Station $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }


    public function remove(Station $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }


    public function clearTable(): void
    {
        $this->createQueryBuilder('station')
            ->delete()
            ->getQuery()
            ->execute();
    }


    /**
     * @return mixed[]
     */
    public function findStationsInRadius(float $latitude, float $longitude, int $distance): array
    {
//        https://ourcodeworld.com/articles/read/1019/how-to-find-nearest-locations-from-a-collection-of-coordinates-latitude-and-longitude-with-php-mysql
        $calculation = '
            (
                (
                    (
                        acos(
                            sin(( :latitude * pi() / 180))
                            *
                            sin(( station.latitude * pi() / 180)) + cos(( :latitude * pi() /180 ))
                            *
                            cos(( station.latitude * pi() / 180)) * cos((( :longitude - station.longitude) * pi()/180))
                        )
                    ) * 180/pi()
                ) * 60 * 1.1515 * 1.609344
            )
        ';

        return $this->createQueryBuilder('station')
            ->addSelect($calculation)
            ->andWhere($calculation . ' <= :distance')
            ->setParameter('latitude', $latitude)
            ->setParameter('longitude', $longitude)
            ->setParameter('distance', $distance)
            ->getQuery()
            ->getResult();
    }
}