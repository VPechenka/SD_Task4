<?php

namespace App\Repository;

use App\Entity\Link;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Link>
 */
class LinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Link::class);
    }

    public function addLink(string $originalUrl): Link
    {
        $link = new Link();
        $link->setOriginalUrl($originalUrl);
        $link->setGenericShortUrl();
        $link->setNumberOfClicks(0);
        $link->setCreateNow();

        $entityManager = $this->getEntityManager();

        $entityManager->persist($link);
        $entityManager->flush();

        return $link;
    }

    public function addLinkClick(Link $link): void
    {
        $link->addOneClick();
        $link->setUseNow();

        $entityManager = $this->getEntityManager();

        $entityManager->flush();
    }

    /**
     * @return Link[] Returns an array of Link objects
     */
    public function findAllByIsDeleted(bool $isDelete): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.isDeleted = :isDelete')
            ->setParameter('isDelete', $isDelete)
            ->getQuery()
            ->getResult();
    }

    public function setDeletedById(array $ids, bool $isDelete): void
    {
        $links = $this->createQueryBuilder('l')
            ->where('l.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()->getResult();

        foreach ($links as $link) {
            $link->setIsDeleted($isDelete);
            if ($isDelete)
                $link->setDeleteNow();
            else
                $link->setDeleteAt(null);
        }

        $this->getEntityManager()->flush();
    }

    public function deleteOldLinks(): void
    {
        $dateThreshold = new \DateTimeImmutable('-1 month');

        $this->createQueryBuilder('l')
            ->delete()
            ->where('l.isDeleted = true')
            ->andWhere('l.deleteAt < :dateThreshold')
            ->setParameter('dateThreshold', $dateThreshold)
            ->getQuery()
            ->execute();
    }
}
