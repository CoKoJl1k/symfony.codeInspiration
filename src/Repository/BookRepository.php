<?php

namespace App\Repository;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Book $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Book $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws \ReflectionException
     */
    public function findByFilters($search)
    {
        $entityClass = Book::class;
        $reflectionClass = new \ReflectionClass($entityClass);
        $properties = $reflectionClass->getProperties();
        $propertyNames = [];
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyNames[] = $propertyName;
        }
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('b')->from(Book::class, 'b');

        $whereExpression = $queryBuilder->expr()->orX();

        foreach ($propertyNames as $field) {
            if ($field != 'authors'){
                $whereExpression->add(
                    $queryBuilder->expr()->like('b.' . $field, $queryBuilder->expr()->literal('%' . $search . '%'))
                );
            }
        }
        $queryBuilder->where($whereExpression);
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

    public function countBooksByAuthor(Author $author): int
    {
        return $this->createQueryBuilder('b')
            ->select('COUNT(b)')
            ->where('b.author = :author')
            ->setParameter('author', $author)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // /**
    //  * @return Book[] Returns an array of Book objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
