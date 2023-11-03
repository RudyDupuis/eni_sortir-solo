<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\Outing;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Outing>
 *
 * @method Outing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Outing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Outing[]    findAll()
 * @method Outing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Outing::class);
    }

    public function findNext20Outings(): array
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('o')
            ->where('o.outingDate >= :now')
            ->setParameter('now', $now)
            ->orderBy('o.outingDate', 'ASC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }

    public function findNext20OutingsByCampus(Campus $campus): array
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('o')
            ->join('o.campus', 'c')
            ->where('o.outingDate >= :now')
            ->andWhere('c = :userCampus')
            ->setParameter('now', $now)
            ->setParameter('userCampus', $campus)
            ->orderBy('o.outingDate', 'ASC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }

    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.name LIKE :searchName')
            ->setParameter('searchName', '%' . $name . '%')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }

    public function findOutingsByAuthor(User $user): array
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('o')
            ->where('o.author = :user')
            ->andWhere('o.outingDate > :now')
            ->setParameter('user', $user)
            ->setParameter('now', $now)
            ->orderBy('o.outingDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOutingsByRegistrant(User $user): array
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('o')
            ->join('o.registrants', 'u')
            ->where('u = :user')
            ->andWhere('o.outingDate > :now')
            ->setParameter('user', $user)
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();
    }
}
