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
    public function findByAvailable(): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.isAvailable = true')
            ->orderBy('l.createAt')
            ->getQuery()
            ->getResult();
    }

    public function hideById(array $ids): void
    {
        $entityManager = $this->getEntityManager();

        foreach ($ids as $id) {
            $link = $this->find($id);
            var_dump($link);
            $link->setIsAvailable(false);
        }

        $entityManager->flush();
    }

    public function removeHiddenLinks(): void
    {

        $links = $this->createQueryBuilder('l')
            ->andWhere('l.isAvailable = false')
            ->getQuery()
            ->getResult();

        $entityManager = $this->getEntityManager();

        foreach ($links as $link) {
            $entityManager->remove($link);
        }

        $entityManager->flush();
    }
}
