<?php
namespace App\EventListener;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

class BookSavedListener
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $book = $args->getObject();
        if (!$book instanceof Book) {
            return;
        }
        $authors = $book->getAuthors();
        foreach ($authors as $author) {
            $bookCount = $this->entityManager->getRepository(Book::class)->countBooksByAuthor($author);
            $author->setBookCount($bookCount);
            $this->entityManager->flush($author);
        }
    }
}
