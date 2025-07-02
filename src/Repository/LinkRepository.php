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
    public function findAll(): array
    {
        return $this->createQueryBuilder('l')
            ->getQuery()
            ->getResult();
    }

    public function removeById(array $ids): void
    {
        $this->createQueryBuilder('l')
            ->delete()
            ->where('l.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->execute();
    }
}
